<?php
declare(strict_types=1);

require_once __DIR__ . '/db.php';
require_once __DIR__ . '/pdf.php';

/* ---------------------------------------------------------------------------
 * Konfiguration / Flags
 * ------------------------------------------------------------------------- */

/** Ist Mollie eingerichtet? Steuert, ob die Bezahlfunktion sichtbar ist. */
function mollie_enabled(): bool
{
    $k = app_config()['mollie']['api_key'] ?? '';
    return is_string($k) && $k !== '';
}

/** Zahlungen aktiv (aktuell an Mollie gekoppelt). */
function billing_payments_enabled(): bool
{
    return mollie_enabled();
}

function billing_seller(): array
{
    return app_config()['billing']['verkaeufer'] ?? [];
}

function billing_is_kleinunternehmer(): bool
{
    return !empty(app_config()['billing']['kleinunternehmer']);
}

function billing_ust_satz(): int
{
    return (int) (app_config()['billing']['ust_satz'] ?? 19);
}

function money_de(int $cent): string
{
    return number_format($cent / 100, 2, ',', '.') . ' €';
}

/** Aus einem Brutto-Betrag Netto/USt/Brutto ableiten (Preis = Endpreis). */
function billing_amounts(int $bruttoCent, ?bool $klein = null, ?int $satz = null): array
{
    $klein = $klein ?? billing_is_kleinunternehmer();
    $satz = $satz ?? billing_ust_satz();
    if ($klein || $satz <= 0) {
        return ['netto' => $bruttoCent, 'ust' => 0, 'brutto' => $bruttoCent, 'satz' => 0];
    }
    $netto = (int) round($bruttoCent * 100 / (100 + $satz));
    return ['netto' => $netto, 'ust' => $bruttoCent - $netto, 'brutto' => $bruttoCent, 'satz' => $satz];
}

/** Speicherort für Rechnungs-/Angebots-PDFs (nicht öffentlich). */
function billing_storage_dir(): string
{
    $base = getenv('SARTU_STORAGE_PATH') ?: (dirname(__DIR__) . '/storage');
    $dir = rtrim($base, '/\\') . '/invoices';
    if (!is_dir($dir)) {
        @mkdir($dir, 0770, true);
    }
    return $dir;
}

/* ---------------------------------------------------------------------------
 * Empfänger / Rechnungsnummer
 * ------------------------------------------------------------------------- */

function billing_recipient_lines(array $c): array
{
    $lines = [];
    if (!empty($c['firma'])) { $lines[] = (string) $c['firma']; }
    if (!empty($c['name'])) { $lines[] = (string) $c['name']; }
    if (!empty($c['email'])) { $lines[] = (string) $c['email']; }
    return $lines ?: ['Kunde'];
}

/** Nächste fortlaufende Rechnungsnummer für ein Jahr (atomar). */
function billing_next_number(PDO $pdo, int $jahr): string
{
    $pdo->prepare('insert into rechnung_counter (jahr, letzte_nummer) values (?, 1) on duplicate key update letzte_nummer = letzte_nummer + 1')->execute([$jahr]);
    $stmt = $pdo->prepare('select letzte_nummer from rechnung_counter where jahr = ?');
    $stmt->execute([$jahr]);
    $n = (int) ($stmt->fetch()['letzte_nummer'] ?? 1);
    return sprintf('%d-%04d', $jahr, $n);
}

/* ---------------------------------------------------------------------------
 * Rechnung erstellen / festschreiben
 * ------------------------------------------------------------------------- */

/** Rechnungsentwurf aus einem angenommenen Angebot erzeugen. Gibt invoice_id zurück. */
function billing_create_invoice_for_offer(PDO $pdo, array $offer, array $customer): string
{
    $klein = billing_is_kleinunternehmer();
    $satz = billing_ust_satz();
    $brutto = (int) round(((int) ($offer['preis_einmalig'] ?? 0)) * 100);
    $a = billing_amounts($brutto, $klein, $satz);

    $id = uuidv4();
    $empf = json_encode(billing_recipient_lines($customer), JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);
    $pdo->prepare(
        'insert into invoices (id, customer_id, project_id, angebot_id, status, netto_cent, ust_satz, ust_cent, brutto_cent, kleinunternehmer, empfaenger, hinweis)
         values (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)'
    )->execute([
        $id, $customer['id'], $offer['project_id'] ?? null, $offer['id'] ?? null, 'entwurf',
        $a['netto'], $a['satz'], $a['ust'], $a['brutto'], $klein ? 1 : 0, $empf,
        'Rechnung zum Angebot „' . ($offer['titel'] ?? 'Website-Projekt') . '“.',
    ]);
    $pos = ($offer['titel'] ?? 'Website-Projekt') . ' — Paket ' . ucfirst((string) ($offer['paket'] ?? ''));
    $pdo->prepare('insert into invoice_items (id, invoice_id, pos, bezeichnung, menge, einzelpreis_cent, betrag_cent) values (?, ?, 1, ?, 1, ?, ?)')
        ->execute([uuidv4(), $id, $pos, $brutto, $brutto]);
    return $id;
}

/** Rechnung festschreiben: Nummer vergeben, PDF + XML erzeugen, Status offen. */
function billing_issue_invoice(PDO $pdo, string $invoiceId): ?array
{
    $inv = billing_load_invoice($pdo, $invoiceId);
    if (!$inv) { return null; }
    if (!empty($inv['nummer'])) { return $inv; } // schon festgeschrieben

    $jahr = (int) date('Y');
    $nummer = billing_next_number($pdo, $jahr);
    $ausgestellt = date('Y-m-d');
    $faellig = date('Y-m-d', strtotime('+14 days'));

    $pdo->prepare('update invoices set nummer = ?, status = ?, ausgestellt_am = ?, faellig_am = ? where id = ?')
        ->execute([$nummer, 'offen', $ausgestellt, $faellig, $invoiceId]);

    $inv = billing_load_invoice($pdo, $invoiceId);
    // Dateien erzeugen
    $pdf = billing_render_invoice_pdf($inv);
    $xml = billing_render_xrechnung($inv);
    $dir = billing_storage_dir();
    $pdfPath = $dir . '/' . $invoiceId . '.pdf';
    $xmlPath = $dir . '/' . $invoiceId . '.xml';
    @file_put_contents($pdfPath, $pdf);
    @file_put_contents($xmlPath, $xml);
    $pdo->prepare('update invoices set pdf_path = ?, xml_path = ? where id = ?')->execute([$pdfPath, $xmlPath, $invoiceId]);
    $inv['pdf_path'] = $pdfPath;
    $inv['xml_path'] = $xmlPath;
    return $inv;
}

function billing_load_invoice(PDO $pdo, string $id): ?array
{
    $stmt = $pdo->prepare('select * from invoices where id = ? limit 1');
    $stmt->execute([$id]);
    $inv = $stmt->fetch();
    if (!$inv) { return null; }
    $items = $pdo->prepare('select * from invoice_items where invoice_id = ? order by pos asc');
    $items->execute([$id]);
    $inv['items'] = $items->fetchAll();
    $inv['empfaenger_lines'] = $inv['empfaenger'] ? (json_decode((string) $inv['empfaenger'], true) ?: []) : [];
    return $inv;
}

function billing_customer_invoices(PDO $pdo, string $customerId): array
{
    $stmt = $pdo->prepare('select id, nummer, status, ausgestellt_am, faellig_am, brutto_cent from invoices where customer_id = ? and status <> ? order by created_at desc');
    $stmt->execute([$customerId, 'entwurf']);
    return $stmt->fetchAll();
}

/* ---------------------------------------------------------------------------
 * PDF-Ausgabe
 * ------------------------------------------------------------------------- */

/** Gemeinsames Layout für Angebot und Rechnung. */
function billing_pdf_document(array $doc): string
{
    $pdf = new SartuPdf();
    $seller = $doc['seller'];
    $M = 56;
    $right = $pdf->pageWidth() - $M;

    // Kopf
    $pdf->text($M, 54, (string) ($seller['name'] ?? 'Sartu'), 15, true);
    $y = 74;
    foreach (['inhaber', 'strasse', 'plz_ort', 'land'] as $k) {
        if (!empty($seller[$k])) { $pdf->text($M, $y, (string) $seller[$k], 9, false, 'L', [90, 90, 90]); $y += 12; }
    }
    $ry = 54;
    foreach ([$seller['email'] ?? '', $seller['telefon'] ?? '', !empty($seller['ust_id']) ? 'USt-IdNr.: ' . $seller['ust_id'] : ''] as $line) {
        if ($line !== '') { $pdf->text($right, $ry, (string) $line, 9, false, 'R', [90, 90, 90]); $ry += 12; }
    }

    // Empfänger
    $ey = 168;
    $pdf->text($M, $ey - 13, 'An', 8, false, 'L', [130, 130, 130]);
    foreach ($doc['empfaenger'] as $line) { $pdf->text($M, $ey, (string) $line, 10); $ey += 13; }

    // Titel + Meta
    $pdf->text($M, 250, $doc['typ'] . (!empty($doc['nummer']) ? ' ' . $doc['nummer'] : ''), 18, true);
    $pdf->text($right, 168, 'Datum: ' . $doc['datum'], 9, false, 'R');
    if (!empty($doc['faellig'])) { $pdf->text($right, 181, $doc['faellig'], 9, false, 'R'); }

    // Tabelle
    $ty = 290;
    $colBez = $M; $colEinzel = 400; $colBetrag = $right;
    $pdf->text($colBez, $ty, 'Beschreibung', 9, true);
    $pdf->text($colEinzel, $ty, 'Einzelpreis', 9, true, 'R');
    $pdf->text($colBetrag, $ty, 'Betrag', 9, true, 'R');
    $ty += 6; $pdf->line($M, $ty, $colBetrag, $ty, 0.6, [180, 180, 180]); $ty += 17;

    foreach ($doc['items'] as $it) {
        $bez = (string) $it[0];
        // lange Bezeichnung auf zwei Zeilen umbrechen
        $pdf->text($colBez, $ty, mb_substr($bez, 0, 52), 10);
        $pdf->text($colEinzel, $ty, money_de((int) $it[1]), 10, false, 'R');
        $pdf->text($colBetrag, $ty, money_de((int) $it[2]), 10, false, 'R');
        if (mb_strlen($bez) > 52) { $ty += 13; $pdf->text($colBez, $ty, mb_substr($bez, 52, 80), 9, false, 'L', [110, 110, 110]); }
        $ty += 20;
    }

    // Summen
    $sx = 340;
    $ty += 2; $pdf->line($sx, $ty, $colBetrag, $ty, 0.5, [180, 180, 180]); $ty += 17;
    // Aktion: regulärer Preis (durchgestrichen) + Rabattzeile vor der Endsumme
    if (!empty($doc['regulaer_cent']) && (int) $doc['regulaer_cent'] > (int) $doc['brutto']) {
        $rabatt = (int) $doc['regulaer_cent'] - (int) $doc['brutto'];
        $pdf->text($sx, $ty, 'Regulärer Festpreis', 10, false, 'L', [120, 120, 120]);
        $pdf->strikeText($colBetrag, $ty, money_de((int) $doc['regulaer_cent']), 10, [120, 120, 120]); $ty += 15;
        $pdf->text($sx, $ty, (string) ($doc['aktion_label'] ?? 'Aktion'), 10, false, 'L', [15, 118, 110]);
        $pdf->text($colBetrag, $ty, '- ' . money_de($rabatt), 10, false, 'R', [15, 118, 110]); $ty += 15;
    }
    if (empty($doc['klein'])) {
        $pdf->text($sx, $ty, 'Nettobetrag', 10); $pdf->text($colBetrag, $ty, money_de((int) $doc['netto']), 10, false, 'R'); $ty += 15;
        $pdf->text($sx, $ty, 'zzgl. USt ' . $doc['satz'] . ' %', 10); $pdf->text($colBetrag, $ty, money_de((int) $doc['ust']), 10, false, 'R'); $ty += 15;
    }
    $gesamtLabel = !empty($doc['regulaer_cent']) && (int) $doc['regulaer_cent'] > (int) $doc['brutto'] ? 'Aktionspreis' : 'Gesamtbetrag';
    $pdf->text($sx, $ty, $gesamtLabel, 11, true); $pdf->text($colBetrag, $ty, money_de((int) $doc['brutto']), 11, true, 'R');
    $ty += 6; $pdf->line($sx, $ty, $colBetrag, $ty, 0.8); $ty += 26;

    if (!empty($doc['klein'])) {
        $pdf->text($M, $ty, 'Gemäß § 19 UStG wird keine Umsatzsteuer berechnet.', 9, false, 'L', [90, 90, 90]); $ty += 18;
    }
    foreach ($doc['texte'] ?? [] as $t) {
        $ty = $pdf->paragraph($M, $ty, (string) $t, 9, 12) + 8;
    }

    // Fußzeile mit Bankdaten
    $fy = 800;
    $foot = trim(($seller['name'] ?? '') . ($seller['iban'] ? ' · IBAN: ' . $seller['iban'] : '') . ($seller['bic'] ? ' · BIC: ' . $seller['bic'] : ''));
    if ($foot !== '') { $pdf->line($M, $fy - 12, $right, $fy - 12, 0.4, [200, 200, 200]); $pdf->text($M, $fy, $foot, 8, false, 'L', [120, 120, 120]); }

    // ZUGFeRD / Factur-X: E-Rechnungs-XML ins PDF einbetten (macht es zur hybriden E-Rechnung).
    if (!empty($doc['facturx'])) {
        $pdf->attachFacturX((string) $doc['facturx']);
    }
    return $pdf->output();
}

/** PDF eines Angebots (aus der angebote-Row). */
function billing_render_offer_pdf(array $offer, array $customer): string
{
    $seller = billing_seller();
    $klein = billing_is_kleinunternehmer();
    $satz = billing_ust_satz();
    $brutto = (int) round(((int) ($offer['preis_einmalig'] ?? 0)) * 100);
    $a = billing_amounts($brutto, $klein, $satz);
    $regulaerCent = !empty($offer['preis_regulaer']) ? (int) round(((int) $offer['preis_regulaer']) * 100) : 0;

    $items = [[($offer['titel'] ?? 'Website-Projekt') . ' — Paket ' . ucfirst((string) ($offer['paket'] ?? '')), $brutto, $brutto]];
    $texte = [];
    if (!empty($offer['umfang'])) { $texte[] = "Leistungsumfang:\n" . $offer['umfang']; }
    if (!empty($offer['care_stufe'])) {
        $texte[] = 'Laufender Rundum-Schutz: ' . $offer['care_stufe'] . (isset($offer['care_preis']) ? ' · ' . money_de(((int) $offer['care_preis']) * 100) . ' pro Monat' : '') . ' (separat).';
    }
    if (!empty($offer['korrekturrunden'])) { $texte[] = 'Enthaltene Korrekturrunden: ' . $offer['korrekturrunden'] . ' (je ein gesammeltes Feedback).'; }
    $texte[] = 'Dieses Angebot ist unverbindlich' . (!empty($offer['gueltig_bis']) ? ' und gültig bis ' . $offer['gueltig_bis'] : '') . '. Die verbindliche Beauftragung erfolgt im Kundenportal.';

    return billing_pdf_document([
        'typ' => 'Angebot', 'nummer' => '', 'datum' => date('d.m.Y'),
        'faellig' => !empty($offer['gueltig_bis']) ? 'Gültig bis: ' . $offer['gueltig_bis'] : '',
        'empfaenger' => billing_recipient_lines($customer), 'items' => $items,
        'netto' => $a['netto'], 'ust' => $a['ust'], 'brutto' => $a['brutto'], 'satz' => $a['satz'], 'klein' => $klein,
        'texte' => $texte, 'seller' => $seller,
        'regulaer_cent' => $regulaerCent, 'aktion_label' => (string) ($offer['aktion_label'] ?? 'Aktion'),
    ]);
}

/** PDF einer Rechnung (aus billing_load_invoice). */
function billing_render_invoice_pdf(array $inv): string
{
    $seller = billing_seller();
    $items = [];
    foreach ($inv['items'] as $it) {
        $items[] = [(string) $it['bezeichnung'], (int) $it['einzelpreis_cent'], (int) $it['betrag_cent']];
    }
    $texte = [];
    if (!empty($inv['hinweis'])) { $texte[] = (string) $inv['hinweis']; }
    $texte[] = 'Bitte überweisen Sie den Betrag bis zum ' . billing_fmt_date($inv['faellig_am']) . ' oder zahlen Sie bequem online im Kundenportal.';
    if (!empty($seller['iban'])) { $texte[] = 'Bankverbindung: ' . ($seller['bank'] ?? '') . ' · IBAN ' . $seller['iban'] . ($seller['bic'] ? ' · BIC ' . $seller['bic'] : ''); }

    return billing_pdf_document([
        'typ' => 'Rechnung', 'nummer' => (string) $inv['nummer'], 'datum' => billing_fmt_date($inv['ausgestellt_am']),
        'faellig' => 'Fällig: ' . billing_fmt_date($inv['faellig_am']),
        'empfaenger' => $inv['empfaenger_lines'], 'items' => $items,
        'netto' => (int) $inv['netto_cent'], 'ust' => (int) $inv['ust_cent'], 'brutto' => (int) $inv['brutto_cent'],
        'satz' => (int) $inv['ust_satz'], 'klein' => (int) $inv['kleinunternehmer'] === 1,
        'texte' => $texte, 'seller' => $seller,
        'facturx' => billing_render_cii($inv),
    ]);
}

function billing_fmt_date(?string $d): string
{
    if (!$d) { return date('d.m.Y'); }
    $ts = strtotime($d);
    return $ts ? date('d.m.Y', $ts) : (string) $d;
}

/* ---------------------------------------------------------------------------
 * E-Rechnung (XRechnung / EN16931 UBL)
 * ------------------------------------------------------------------------- */

function billing_render_xrechnung(array $inv): string
{
    $s = billing_seller();
    $e = function ($v) { return htmlspecialchars((string) $v, ENT_XML1 | ENT_QUOTES, 'UTF-8'); };
    $c = static function (int $cent): string { return number_format($cent / 100, 2, '.', ''); };
    $buyer = $inv['empfaenger_lines'][0] ?? 'Kunde';
    $nummer = (string) ($inv['nummer'] ?? '');
    $issue = date('Y-m-d', strtotime((string) ($inv['ausgestellt_am'] ?: 'now')));
    $due = date('Y-m-d', strtotime((string) ($inv['faellig_am'] ?: 'now')));
    $klein = (int) $inv['kleinunternehmer'] === 1;
    $satz = (int) $inv['ust_satz'];
    $taxCat = $klein ? 'E' : 'S'; // E = befreit, S = Standard
    $netto = $c((int) $inv['netto_cent']);
    $ust = $c((int) $inv['ust_cent']);
    $brutto = $c((int) $inv['brutto_cent']);

    $lines = '';
    $i = 0;
    foreach ($inv['items'] as $it) {
        $i++;
        $lines .= '<cac:InvoiceLine><cbc:ID>' . $i . '</cbc:ID>'
            . '<cbc:InvoicedQuantity unitCode="C62">' . (int) $it['menge'] . '</cbc:InvoicedQuantity>'
            . '<cbc:LineExtensionAmount currencyID="EUR">' . $c((int) $it['betrag_cent']) . '</cbc:LineExtensionAmount>'
            . '<cac:Item><cbc:Name>' . $e($it['bezeichnung']) . '</cbc:Name>'
            . '<cac:ClassifiedTaxCategory><cbc:ID>' . $taxCat . '</cbc:ID><cbc:Percent>' . ($klein ? 0 : $satz) . '</cbc:Percent><cac:TaxScheme><cbc:ID>VAT</cbc:ID></cac:TaxScheme></cac:ClassifiedTaxCategory></cac:Item>'
            . '<cac:Price><cbc:PriceAmount currencyID="EUR">' . $c((int) $it['einzelpreis_cent']) . '</cbc:PriceAmount></cac:Price>'
            . '</cac:InvoiceLine>';
    }

    return '<?xml version="1.0" encoding="UTF-8"?>' . "\n"
        . '<Invoice xmlns="urn:oasis:names:specification:ubl:schema:xsd:Invoice-2" xmlns:cac="urn:oasis:names:specification:ubl:schema:xsd:CommonAggregateComponents" xmlns:cbc="urn:oasis:names:specification:ubl:schema:xsd:CommonBasicComponents">'
        . '<cbc:CustomizationID>urn:cen.eu:en16931:2017#compliant#urn:xoev-de:kosit:standard:xrechnung_3.0</cbc:CustomizationID>'
        . '<cbc:ProfileID>urn:fdc:peppol.eu:2017:poacc:billing:01:1.0</cbc:ProfileID>'
        . '<cbc:ID>' . $e($nummer) . '</cbc:ID>'
        . '<cbc:IssueDate>' . $issue . '</cbc:IssueDate>'
        . '<cbc:DueDate>' . $due . '</cbc:DueDate>'
        . '<cbc:InvoiceTypeCode>380</cbc:InvoiceTypeCode>'
        . '<cbc:DocumentCurrencyCode>EUR</cbc:DocumentCurrencyCode>'
        . '<cac:AccountingSupplierParty><cac:Party>'
        . '<cac:PartyName><cbc:Name>' . $e($s['name'] ?? '') . '</cbc:Name></cac:PartyName>'
        . '<cac:PostalAddress><cbc:StreetName>' . $e($s['strasse'] ?? '') . '</cbc:StreetName><cbc:CityName>' . $e($s['plz_ort'] ?? '') . '</cbc:CityName><cac:Country><cbc:IdentificationCode>DE</cbc:IdentificationCode></cac:Country></cac:PostalAddress>'
        . ($s['ust_id'] ? '<cac:PartyTaxScheme><cbc:CompanyID>' . $e($s['ust_id']) . '</cbc:CompanyID><cac:TaxScheme><cbc:ID>VAT</cbc:ID></cac:TaxScheme></cac:PartyTaxScheme>' : '')
        . '<cac:Contact><cbc:ElectronicMail>' . $e($s['email'] ?? '') . '</cbc:ElectronicMail></cac:Contact>'
        . '</cac:Party></cac:AccountingSupplierParty>'
        . '<cac:AccountingCustomerParty><cac:Party><cac:PartyName><cbc:Name>' . $e($buyer) . '</cbc:Name></cac:PartyName></cac:Party></cac:AccountingCustomerParty>'
        . '<cac:TaxTotal><cbc:TaxAmount currencyID="EUR">' . $ust . '</cbc:TaxAmount>'
        . '<cac:TaxSubtotal><cbc:TaxableAmount currencyID="EUR">' . $netto . '</cbc:TaxableAmount><cbc:TaxAmount currencyID="EUR">' . $ust . '</cbc:TaxAmount>'
        . '<cac:TaxCategory><cbc:ID>' . $taxCat . '</cbc:ID><cbc:Percent>' . ($klein ? 0 : $satz) . '</cbc:Percent>' . ($klein ? '<cbc:TaxExemptionReason>Kleinunternehmer gemäß § 19 UStG</cbc:TaxExemptionReason>' : '') . '<cac:TaxScheme><cbc:ID>VAT</cbc:ID></cac:TaxScheme></cac:TaxCategory></cac:TaxSubtotal></cac:TaxTotal>'
        . '<cac:LegalMonetaryTotal><cbc:LineExtensionAmount currencyID="EUR">' . $netto . '</cbc:LineExtensionAmount><cbc:TaxExclusiveAmount currencyID="EUR">' . $netto . '</cbc:TaxExclusiveAmount><cbc:TaxInclusiveAmount currencyID="EUR">' . $brutto . '</cbc:TaxInclusiveAmount><cbc:PayableAmount currencyID="EUR">' . $brutto . '</cbc:PayableAmount></cac:LegalMonetaryTotal>'
        . $lines
        . '</Invoice>';
}

/* ---------------------------------------------------------------------------
 * E-Rechnung (ZUGFeRD / Factur-X — CII, ins PDF eingebettet)
 * ------------------------------------------------------------------------- */

/** Cross-Industry-Invoice (EN16931-Profil) für die Einbettung ins Rechnungs-PDF. */
function billing_render_cii(array $inv): string
{
    $s = billing_seller();
    $e = static function ($v): string { return htmlspecialchars((string) $v, ENT_XML1 | ENT_QUOTES, 'UTF-8'); };
    $c = static function (int $cent): string { return number_format($cent / 100, 2, '.', ''); };
    $buyer = $inv['empfaenger_lines'][0] ?? 'Kunde';
    $nummer = (string) ($inv['nummer'] ?? '');
    $issue = date('Ymd', strtotime((string) ($inv['ausgestellt_am'] ?: 'now')));
    $due = date('Ymd', strtotime((string) ($inv['faellig_am'] ?: 'now')));
    $klein = (int) $inv['kleinunternehmer'] === 1;
    $satz = (int) $inv['ust_satz'];
    $taxCat = $klein ? 'E' : 'S'; // E = befreit, S = Standard
    $prozent = $klein ? 0 : $satz;
    $netto = $c((int) $inv['netto_cent']);
    $ust = $c((int) $inv['ust_cent']);
    $brutto = $c((int) $inv['brutto_cent']);

    $lines = '';
    $i = 0;
    foreach ($inv['items'] as $it) {
        $i++;
        $lines .= '<ram:IncludedSupplyChainTradeLineItem>'
            . '<ram:AssociatedDocumentLineDocument><ram:LineID>' . $i . '</ram:LineID></ram:AssociatedDocumentLineDocument>'
            . '<ram:SpecifiedTradeProduct><ram:Name>' . $e($it['bezeichnung']) . '</ram:Name></ram:SpecifiedTradeProduct>'
            . '<ram:SpecifiedLineTradeAgreement><ram:NetPriceProductTradePrice><ram:ChargeAmount>' . $c((int) $it['einzelpreis_cent']) . '</ram:ChargeAmount></ram:NetPriceProductTradePrice></ram:SpecifiedLineTradeAgreement>'
            . '<ram:SpecifiedLineTradeDelivery><ram:BilledQuantity unitCode="C62">' . (int) $it['menge'] . '</ram:BilledQuantity></ram:SpecifiedLineTradeDelivery>'
            . '<ram:SpecifiedLineTradeSettlement>'
            . '<ram:ApplicableTradeTax><ram:TypeCode>VAT</ram:TypeCode><ram:CategoryCode>' . $taxCat . '</ram:CategoryCode><ram:RateApplicablePercent>' . $prozent . '</ram:RateApplicablePercent></ram:ApplicableTradeTax>'
            . '<ram:SpecifiedTradeSettlementLineMonetarySummation><ram:LineTotalAmount>' . $c((int) $it['betrag_cent']) . '</ram:LineTotalAmount></ram:SpecifiedTradeSettlementLineMonetarySummation>'
            . '</ram:SpecifiedLineTradeSettlement>'
            . '</ram:IncludedSupplyChainTradeLineItem>';
    }

    $exemptReason = $klein ? '<ram:ExemptionReason>Kleinunternehmer gemäß § 19 UStG</ram:ExemptionReason>' : '';

    return '<?xml version="1.0" encoding="UTF-8"?>' . "\n"
        . '<rsm:CrossIndustryInvoice xmlns:rsm="urn:un:unece:uncefact:data:standard:CrossIndustryInvoice:100" xmlns:ram="urn:un:unece:uncefact:data:standard:ReusableAggregateBusinessInformationEntity:100" xmlns:udt="urn:un:unece:uncefact:data:standard:UnqualifiedDataType:100">'
        . '<rsm:ExchangedDocumentContext><ram:GuidelineSpecifiedDocumentContextParameter><ram:ID>urn:cen.eu:en16931:2017</ram:ID></ram:GuidelineSpecifiedDocumentContextParameter></rsm:ExchangedDocumentContext>'
        . '<rsm:ExchangedDocument><ram:ID>' . $e($nummer) . '</ram:ID><ram:TypeCode>380</ram:TypeCode>'
        . '<ram:IssueDateTime><udt:DateTimeString format="102">' . $issue . '</udt:DateTimeString></ram:IssueDateTime></rsm:ExchangedDocument>'
        . '<rsm:SupplyChainTradeTransaction>'
        . $lines
        . '<ram:ApplicableHeaderTradeAgreement>'
        . '<ram:SellerTradeParty><ram:Name>' . $e($s['name'] ?? '') . '</ram:Name>'
        . '<ram:PostalTradeAddress><ram:LineOne>' . $e($s['strasse'] ?? '') . '</ram:LineOne><ram:CityName>' . $e($s['plz_ort'] ?? '') . '</ram:CityName><ram:CountryID>DE</ram:CountryID></ram:PostalTradeAddress>'
        . ($s['email'] ? '<ram:URIUniversalCommunication><ram:URIID schemeID="EM">' . $e($s['email']) . '</ram:URIID></ram:URIUniversalCommunication>' : '')
        . ($s['ust_id'] ? '<ram:SpecifiedTaxRegistration><ram:ID schemeID="VA">' . $e($s['ust_id']) . '</ram:ID></ram:SpecifiedTaxRegistration>' : '')
        . '</ram:SellerTradeParty>'
        . '<ram:BuyerTradeParty><ram:Name>' . $e($buyer) . '</ram:Name></ram:BuyerTradeParty>'
        . '</ram:ApplicableHeaderTradeAgreement>'
        . '<ram:ApplicableHeaderTradeDelivery/>'
        . '<ram:ApplicableHeaderTradeSettlement>'
        . '<ram:InvoiceCurrencyCode>EUR</ram:InvoiceCurrencyCode>'
        . '<ram:ApplicableTradeTax><ram:CalculatedAmount>' . $ust . '</ram:CalculatedAmount><ram:TypeCode>VAT</ram:TypeCode>' . $exemptReason . '<ram:BasisAmount>' . $netto . '</ram:BasisAmount><ram:CategoryCode>' . $taxCat . '</ram:CategoryCode><ram:RateApplicablePercent>' . $prozent . '</ram:RateApplicablePercent></ram:ApplicableTradeTax>'
        . '<ram:SpecifiedTradePaymentTerms><ram:DueDateDateTime><udt:DateTimeString format="102">' . $due . '</udt:DateTimeString></ram:DueDateDateTime></ram:SpecifiedTradePaymentTerms>'
        . '<ram:SpecifiedTradeSettlementHeaderMonetarySummation>'
        . '<ram:LineTotalAmount>' . $netto . '</ram:LineTotalAmount>'
        . '<ram:TaxBasisTotalAmount>' . $netto . '</ram:TaxBasisTotalAmount>'
        . '<ram:TaxTotalAmount currencyID="EUR">' . $ust . '</ram:TaxTotalAmount>'
        . '<ram:GrandTotalAmount>' . $brutto . '</ram:GrandTotalAmount>'
        . '<ram:DuePayableAmount>' . $brutto . '</ram:DuePayableAmount>'
        . '</ram:SpecifiedTradeSettlementHeaderMonetarySummation>'
        . '</ram:ApplicableHeaderTradeSettlement>'
        . '</rsm:SupplyChainTradeTransaction>'
        . '</rsm:CrossIndustryInvoice>';
}
