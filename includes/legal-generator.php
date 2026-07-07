<?php
declare(strict_types=1);

/**
 * Rechts-Text-Generatoren (Stufe 1, Teil B).
 *
 * Erzeugt Impressum-Vorbelegung und eine Datenschutzerklärung aus den Angaben des
 * Kunden. Die Textbausteine sind den geprüften Vorlagen datenschutz.php / impressum.php
 * entnommen (aktuelle Gesetze: DDG, TDDDG, DSGVO). Kein Rechtsberatungsersatz — die
 * finale Fassung prüft Sartu. Das Impressum selbst wird im Renderer aus den Feldern
 * zusammengesetzt (siehe sc_body_impressum()).
 */

/** Startwerte fürs Impressum aus dem Kundenprofil (profiles-Row). */
function sartu_legal_prefill_impressum(array $profile): array
{
    $name = trim((string) ($profile['name'] ?? ''));
    return [
        'firmenname' => trim((string) ($profile['firma'] ?? '')),
        'inhaber' => $name,
        'telefon' => trim((string) ($profile['telefon'] ?? '')),
        'email' => trim((string) ($profile['email'] ?? '')),
        'verantwortlich' => $name,
    ];
}

/**
 * Datenschutzerklärung als Text (für das 'text'-Feld der Datenschutz-Seite) erzeugen.
 * $imp: Impressum-Felder · $opts: ['webanalyse'=>bool, 'google_maps'=>bool].
 * Modultitel als **fett**, Absätze durch Leerzeilen — passt zu sc_richtext().
 */
function sartu_generate_datenschutz(array $imp, array $opts = []): string
{
    $firma = trim((string) ($imp['firmenname'] ?? '')) ?: '[Firmenname]';
    $inhaber = trim((string) ($imp['inhaber'] ?? ''));
    $adresse = trim((string) ($imp['adresse'] ?? '')) ?: '[Anschrift]';
    $email = trim((string) ($imp['email'] ?? '')) ?: '[E-Mail]';
    $stand = date('m/Y');

    $verantwortlich = $firma . ($inhaber !== '' ? "\n" . $inhaber : '') . "\n" . $adresse . "\nE-Mail: " . $email;

    $b = [];
    $b[] = "**1. Verantwortlicher**\n\nVerantwortlich für die Datenverarbeitung auf dieser Website ist:\n{$verantwortlich}\n\nDie vollständigen Kontaktdaten finden Sie im Impressum.";
    $b[] = "**2. Allgemeines zur Datenverarbeitung**\n\nWir verarbeiten personenbezogene Daten nur, soweit dies zur Bereitstellung einer funktionsfähigen Website sowie zur Beantwortung Ihrer Anfragen erforderlich ist. Rechtsgrundlagen sind insbesondere Art. 6 Abs. 1 lit. a, b und f DSGVO.";
    $b[] = "**3. Hosting und Server-Logfiles**\n\nUnsere Website wird bei einem Anbieter mit Serverstandort in Deutschland gehostet. Beim Aufruf der Seite werden technisch notwendige Daten (z. B. IP-Adresse, Datum und Uhrzeit, abgerufene Seite) in sogenannten Server-Logfiles verarbeitet. Rechtsgrundlage ist Art. 6 Abs. 1 lit. f DSGVO (berechtigtes Interesse an einem sicheren, stabilen Betrieb). Mit dem Hosting-Dienstleister besteht ein Vertrag zur Auftragsverarbeitung.";
    $b[] = "**4. SSL-/TLS-Verschlüsselung**\n\nDiese Seite nutzt aus Sicherheitsgründen eine SSL-/TLS-Verschlüsselung. Eine verschlüsselte Verbindung erkennen Sie an „https://“ und dem Schloss-Symbol in Ihrer Browserzeile.";
    $b[] = "**5. Kontaktaufnahme**\n\nWenn Sie uns per E-Mail oder Telefon kontaktieren, verarbeiten wir die von Ihnen mitgeteilten Daten (z. B. Name, E-Mail-Adresse, Nachricht) zur Bearbeitung Ihrer Anfrage. Rechtsgrundlage ist Art. 6 Abs. 1 lit. b bzw. f DSGVO. Die Daten werden gelöscht, sobald sie nicht mehr erforderlich sind, sofern keine gesetzlichen Aufbewahrungspflichten entgegenstehen.";
    $b[] = "**6. Cookies und Einwilligung**\n\nDiese Website setzt standardmäßig nur technisch notwendige Cookies bzw. lokalen Speicher ein. Rechtsgrundlage ist Art. 6 Abs. 1 lit. f DSGVO in Verbindung mit § 25 Abs. 2 TDDDG. Optionale Dienste werden erst nach Ihrer ausdrücklichen Einwilligung geladen (Art. 6 Abs. 1 lit. a DSGVO, § 25 Abs. 1 TDDDG). Ihre Einwilligung ist freiwillig und jederzeit für die Zukunft widerrufbar.";

    if (!empty($opts['webanalyse'])) {
        $b[] = "**7. Webanalyse**\n\nZur Verbesserung unseres Angebots nutzen wir ein datenschutzkonformes, cookiefreies Analyse-Tool ohne Weitergabe an Dritte. Es werden keine personenbezogenen Profile gebildet. Rechtsgrundlage ist Art. 6 Abs. 1 lit. f DSGVO.";
    }
    if (!empty($opts['google_maps'])) {
        $b[] = "**8. Google Maps**\n\nZur Anfahrtsdarstellung binden wir Google Maps erst nach Ihrer Einwilligung ein (Art. 6 Abs. 1 lit. a DSGVO). Dabei kann Ihre IP-Adresse an Google übertragen werden. Anbieter ist Google Ireland Ltd., Dublin.";
    }

    $b[] = "**Ihre Rechte**\n\nSie haben nach der DSGVO das Recht auf Auskunft (Art. 15), Berichtigung (Art. 16), Löschung (Art. 17), Einschränkung der Verarbeitung (Art. 18), Datenübertragbarkeit (Art. 20), Widerspruch (Art. 21) sowie das Recht, eine erteilte Einwilligung jederzeit zu widerrufen (Art. 7 Abs. 3 DSGVO).";
    $b[] = "**Beschwerderecht**\n\nSie haben das Recht, sich bei einer Datenschutz-Aufsichtsbehörde über die Verarbeitung Ihrer personenbezogenen Daten zu beschweren.";
    $b[] = "**Aktualität**\n\nDiese Datenschutzerklärung hat den Stand {$stand} und wird angepasst, sobald sich die Datenverarbeitung ändert.";
    $b[] = "*Diese Datenschutzerklärung wurde automatisch aus Ihren Angaben erstellt und wird von Sartu geprüft. Sie ersetzt keine Rechtsberatung.*";

    return implode("\n\n", $b);
}
