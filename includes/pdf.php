<?php
declare(strict_types=1);

/**
 * Minimaler, abhängigkeitsfreier PDF-Generator (eine A4-Seite, Helvetica).
 * Reicht für Angebote und Rechnungen: Text (links/rechts), Linien, Umlaute
 * über WinAnsi/CP1252. Kein PDF/A — für ZUGFeRD/A-3 später erweiterbar.
 */
final class SartuPdf
{
    private float $w = 595.28;   // A4 Breite in pt
    private float $h = 841.89;   // A4 Höhe in pt
    private string $buf = '';
    /** Helvetica AFM-Zeichenbreiten (1/1000 em) für gängige Zeichen. */
    private array $cw;
    private ?string $attachXml = null;      // eingebettete E-Rechnung (Factur-X CII)
    private string $attachName = 'factur-x.xml';

    public function __construct()
    {
        $ascii = [32=>278,33=>278,34=>355,35=>556,36=>556,37=>889,38=>667,39=>191,40=>333,41=>333,42=>389,43=>584,44=>278,45=>333,46=>278,47=>278,48=>556,49=>556,50=>556,51=>556,52=>556,53=>556,54=>556,55=>556,56=>556,57=>556,58=>278,59=>278,60=>584,61=>584,62=>584,63=>556,64=>1015,65=>667,66=>667,67=>722,68=>722,69=>667,70=>611,71=>778,72=>722,73=>278,74=>500,75=>667,76=>556,77=>833,78=>722,79=>778,80=>667,81=>778,82=>722,83=>667,84=>611,85=>722,86=>667,87=>944,88=>667,89=>667,90=>611,91=>278,92=>278,93=>278,94=>469,95=>556,96=>333,97=>556,98=>556,99=>500,100=>556,101=>556,102=>278,103=>556,104=>556,105=>222,106=>222,107=>500,108=>222,109=>833,110=>556,111=>556,112=>556,113=>556,114=>333,115=>500,116=>278,117=>556,118=>500,119=>722,120=>500,121=>500,122=>500,123=>334,124=>260,125=>334,126=>584];
        // Umlaute / € / ß nach WinAnsi (gleiche Breiten wie Grundzeichen).
        $extra = ['ä'=>556,'ö'=>556,'ü'=>556,'Ä'=>667,'Ö'=>778,'Ü'=>722,'ß'=>556,'€'=>556,'–'=>556,'§'=>556,'’'=>222];
        $cw = [];
        foreach ($ascii as $code => $wd) { $cw[chr($code)] = $wd; }
        foreach ($extra as $ch => $wd) { $cw[$ch] = $wd; }
        $this->cw = $cw;
    }

    public function pageWidth(): float { return $this->w; }

    public function textWidth(string $s, float $size): float
    {
        $total = 0;
        // je UTF-8-Zeichen
        $chars = preg_split('//u', $s, -1, PREG_SPLIT_NO_EMPTY) ?: [];
        foreach ($chars as $ch) {
            $total += $this->cw[$ch] ?? 556;
        }
        return $total / 1000 * $size;
    }

    private function enc(string $s): string
    {
        $conv = @iconv('UTF-8', 'CP1252//TRANSLIT', $s);
        if ($conv === false) { $conv = $s; }
        return strtr($conv, ['\\' => '\\\\', '(' => '\\(', ')' => '\\)']);
    }

    /** Text an (x, yTop) — yTop von oben gemessen. align: 'L' oder 'R'. bold optional. */
    public function text(float $x, float $yTop, string $s, float $size = 10, bool $bold = false, string $align = 'L', ?array $rgb = null): void
    {
        if ($s === '') { return; }
        $y = $this->h - $yTop;
        if ($align === 'R') { $x -= $this->textWidth($s, $size); }
        $font = $bold ? 'F2' : 'F1';
        $color = $rgb ? sprintf('%.3F %.3F %.3F rg ', $rgb[0] / 255, $rgb[1] / 255, $rgb[2] / 255) : '';
        $reset = $rgb ? ' 0 0 0 rg' : '';
        $this->buf .= $color . sprintf("BT /%s %.2F Tf %.2F %.2F Td (%s) Tj ET", $font, $size, $x, $y, $this->enc($s)) . $reset . "\n";
    }

    /** Rechtsbündiger Text mit Durchstreichung (für regulären Preis bei Aktion). */
    public function strikeText(float $x, float $yTop, string $s, float $size = 10, ?array $rgb = null): void
    {
        $this->text($x, $yTop, $s, $size, false, 'R', $rgb);
        $w = $this->textWidth($s, $size);
        $this->line($x - $w, $yTop - $size * 0.3, $x, $yTop - $size * 0.3, 0.6, $rgb ?? [120, 120, 120]);
    }

    /** Zeilenumbruch-Text: gibt neue yTop zurück. */
    public function paragraph(float $x, float $yTop, string $s, float $size = 10, float $leading = 14): float
    {
        foreach (preg_split('/\r?\n/', $s) ?: [$s] as $line) {
            $this->text($x, $yTop, $line, $size);
            $yTop += $leading;
        }
        return $yTop;
    }

    public function line(float $x1, float $yTop1, float $x2, float $yTop2, float $w = 0.5, ?array $rgb = null): void
    {
        $y1 = $this->h - $yTop1;
        $y2 = $this->h - $yTop2;
        $color = $rgb ? sprintf('%.3F %.3F %.3F RG ', $rgb[0] / 255, $rgb[1] / 255, $rgb[2] / 255) : '';
        $reset = $rgb ? ' 0 0 0 RG' : '';
        $this->buf .= $color . sprintf('%.2F w %.2F %.2F m %.2F %.2F l S', $w, $x1, $y1, $x2, $y2) . $reset . "\n";
    }

    /** E-Rechnungs-XML (Factur-X/ZUGFeRD, CII) ins PDF einbetten. */
    public function attachFacturX(string $xml, string $name = 'factur-x.xml'): void
    {
        $this->attachXml = $xml;
        $this->attachName = $name;
    }

    private function xmp(): string
    {
        $now = date('Y-m-d\TH:i:sP');
        return '<?xpacket begin="' . "\xEF\xBB\xBF" . '" id="W5M0MpCehiHzreSzNTczkc9d"?>'
            . '<x:xmpmeta xmlns:x="adobe:ns:meta/"><rdf:RDF xmlns:rdf="http://www.w3.org/1999/02/22-rdf-syntax-ns#">'
            . '<rdf:Description rdf:about="" xmlns:pdfaid="http://www.aiim.org/pdfa/ns/id/"><pdfaid:part>3</pdfaid:part><pdfaid:conformance>B</pdfaid:conformance></rdf:Description>'
            . '<rdf:Description rdf:about="" xmlns:dc="http://purl.org/dc/elements/1.1/"><dc:title><rdf:Alt><rdf:li xml:lang="x-default">Rechnung</rdf:li></rdf:Alt></dc:title></rdf:Description>'
            . '<rdf:Description rdf:about="" xmlns:xmp="http://ns.adobe.com/xap/1.0/"><xmp:CreateDate>' . $now . '</xmp:CreateDate><xmp:CreatorTool>Sartu</xmp:CreatorTool></rdf:Description>'
            . '<rdf:Description rdf:about="" xmlns:fx="urn:factur-x:pdfa:CrossIndustryDocument:invoice:1p0#"><fx:DocumentType>INVOICE</fx:DocumentType><fx:DocumentFileName>' . $this->attachName . '</fx:DocumentFileName><fx:Version>1.0</fx:Version><fx:ConformanceLevel>EN 16931</fx:ConformanceLevel></rdf:Description>'
            . '</rdf:RDF></x:xmpmeta><?xpacket end="w"?>';
    }

    public function output(): string
    {
        $attach = $this->attachXml !== null;
        $objs = [];
        $objs[2] = '<< /Type /Pages /Kids [3 0 R] /Count 1 >>';
        $objs[3] = sprintf('<< /Type /Page /Parent 2 0 R /MediaBox [0 0 %.2F %.2F] /Resources << /Font << /F1 5 0 R /F2 6 0 R >> >> /Contents 4 0 R >>', $this->w, $this->h);
        $stream = $this->buf;
        $objs[4] = "<< /Length " . strlen($stream) . " >>\nstream\n" . $stream . "endstream";
        $objs[5] = '<< /Type /Font /Subtype /Type1 /BaseFont /Helvetica /Encoding /WinAnsiEncoding >>';
        $objs[6] = '<< /Type /Font /Subtype /Type1 /BaseFont /Helvetica-Bold /Encoding /WinAnsiEncoding >>';

        $catalogExtra = '';
        if ($attach) {
            $xml = (string) $this->attachXml;
            $mod = 'D:' . date('YmdHis') . "+00'00'";
            $objs[7] = "<< /Type /EmbeddedFile /Subtype /text#2Fxml /Params << /Size " . strlen($xml) . " /ModDate (" . $mod . ") >> /Length " . strlen($xml) . " >>\nstream\n" . $xml . "\nendstream";
            $objs[8] = "<< /Type /Filespec /F (" . $this->attachName . ") /UF (" . $this->attachName . ") /AFRelationship /Alternative /Desc (Rechnungsdaten Factur-X/ZUGFeRD) /EF << /F 7 0 R /UF 7 0 R >> >>";
            $xmp = $this->xmp();
            $objs[9] = "<< /Type /Metadata /Subtype /XML /Length " . strlen($xmp) . " >>\nstream\n" . $xmp . "\nendstream";
            $catalogExtra = ' /AF [8 0 R] /Names << /EmbeddedFiles << /Names [(' . $this->attachName . ') 8 0 R] >> >> /Metadata 9 0 R /MarkInfo << /Marked true >>';
        }
        $objs[1] = '<< /Type /Catalog /Pages 2 0 R' . $catalogExtra . ' >>';
        ksort($objs);

        $pdf = "%PDF-1.4\n%\xE2\xE3\xCF\xD3\n";
        $offsets = [];
        foreach ($objs as $n => $body) {
            $offsets[$n] = strlen($pdf);
            $pdf .= $n . " 0 obj\n" . $body . "\nendobj\n";
        }
        $xrefPos = strlen($pdf);
        $count = count($objs) + 1;
        $pdf .= "xref\n0 " . $count . "\n0000000000 65535 f \n";
        for ($i = 1; $i < $count; $i++) {
            $pdf .= sprintf("%010d 00000 n \n", $offsets[$i]);
        }
        $pdf .= "trailer\n<< /Size " . $count . " /Root 1 0 R >>\nstartxref\n" . $xrefPos . "\n%%EOF";
        return $pdf;
    }
}
