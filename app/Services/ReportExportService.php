<?php

namespace App\Services;

use Barryvdh\DomPDF\Facade\Pdf;
use RuntimeException;
use ZipArchive;

class ReportExportService
{
    public function csv(array $headers, array $rows): string
    {
        $handle = fopen('php://temp', 'r+');
        fputcsv($handle, $headers);

        foreach ($rows as $row) {
            fputcsv($handle, $row);
        }

        rewind($handle);
        $csv = stream_get_contents($handle);
        fclose($handle);

        return $csv ?: '';
    }

    public function xlsx(string $sheetTitle, array $headers, array $rows): string
    {
        $tempFile = tempnam(sys_get_temp_dir(), 'report');

        if ($tempFile === false) {
            throw new RuntimeException('Unable to create temporary report file.');
        }

        $xlsxFile = $tempFile . '.xlsx';
        rename($tempFile, $xlsxFile);

        $zip = new ZipArchive();
        if ($zip->open($xlsxFile, ZipArchive::CREATE | ZipArchive::OVERWRITE) !== true) {
            throw new RuntimeException('Unable to create xlsx archive.');
        }

        $zip->addFromString('[Content_Types].xml', $this->contentTypesXml());
        $zip->addFromString('_rels/.rels', $this->relsXml());
        $zip->addFromString('docProps/app.xml', $this->appXml($sheetTitle));
        $zip->addFromString('docProps/core.xml', $this->coreXml());
        $zip->addFromString('xl/workbook.xml', $this->workbookXml($sheetTitle));
        $zip->addFromString('xl/_rels/workbook.xml.rels', $this->workbookRelsXml());
        $zip->addFromString('xl/worksheets/sheet1.xml', $this->sheetXml($headers, $rows, $sheetTitle));
        $zip->close();

        return $xlsxFile;
    }

    public function pdf(string $title, array $headers, array $rows, string $periodLabel = 'All Time'): string
    {
        $html = view('exports.report-pdf', [
            'title' => $title,
            'headers' => $headers,
            'rows' => $rows,
            'periodLabel' => $periodLabel,
            'generatedAt' => now()->format('Y-m-d H:i:s')
        ])->render();

        $pdf = Pdf::loadHTML($html)
            ->setPaper('a3', 'landscape')
            ->setOption('defaultFont', 'Arial')
            ->setOption('font-size', 6)
            ->setOption('margin-left', 8)
            ->setOption('margin-right', 8)
            ->setOption('margin-top', 10)
            ->setOption('margin-bottom', 10);
        return $pdf->output();
    }

    private function sheetXml(array $headers, array $rows, string $sheetTitle): string
    {
        $xml = '<?xml version="1.0" encoding="UTF-8" standalone="yes"?>';
        $xml .= '<worksheet xmlns="http://schemas.openxmlformats.org/spreadsheetml/2006/main">';
        $xml .= '<cols><col min="1" max="' . count($headers) . '" width="18" customWidth="1"/></cols>';
        $xml .= '<sheetData>';

        $xml .= $this->sheetRow(1, $headers);

        foreach ($rows as $index => $row) {
            $xml .= $this->sheetRow($index + 2, $row);
        }

        $xml .= '</sheetData></worksheet>';

        return $xml;
    }

    private function sheetRow(int $rowNumber, array $values): string
    {
        $xml = '<row r="' . $rowNumber . '">';

        foreach (array_values($values) as $columnIndex => $value) {
            $cellRef = $this->columnLetter($columnIndex + 1) . $rowNumber;
            $xml .= '<c r="' . $cellRef . '" t="inlineStr"><is><t xml:space="preserve">'
                . htmlspecialchars((string) $value, ENT_XML1 | ENT_COMPAT, 'UTF-8')
                . '</t></is></c>';
        }

        $xml .= '</row>';

        return $xml;
    }

    private function columnLetter(int $index): string
    {
        $letter = '';

        while ($index > 0) {
            $index--;
            $letter = chr(65 + ($index % 26)) . $letter;
            $index = intdiv($index, 26);
        }

        return $letter;
    }

    private function contentTypesXml(): string
    {
        return '<?xml version="1.0" encoding="UTF-8" standalone="yes"?>'
            . '<Types xmlns="http://schemas.openxmlformats.org/package/2006/content-types">'
            . '<Default Extension="rels" ContentType="application/vnd.openxmlformats-package.relationships+xml"/>'
            . '<Default Extension="xml" ContentType="application/xml"/>'
            . '<Override PartName="/xl/workbook.xml" ContentType="application/vnd.openxmlformats-officedocument.spreadsheetml.sheet.main+xml"/>'
            . '<Override PartName="/xl/worksheets/sheet1.xml" ContentType="application/vnd.openxmlformats-officedocument.spreadsheetml.worksheet+xml"/>'
            . '<Override PartName="/docProps/app.xml" ContentType="application/vnd.openxmlformats-officedocument.extended-properties+xml"/>'
            . '<Override PartName="/docProps/core.xml" ContentType="application/vnd.openxmlformats-package.core-properties+xml"/>'
            . '</Types>';
    }

    private function relsXml(): string
    {
        return '<?xml version="1.0" encoding="UTF-8" standalone="yes"?>'
            . '<Relationships xmlns="http://schemas.openxmlformats.org/package/2006/relationships">'
            . '<Relationship Id="rId1" Type="http://schemas.openxmlformats.org/officeDocument/2006/relationships/officeDocument" Target="xl/workbook.xml"/>'
            . '<Relationship Id="rId2" Type="http://schemas.openxmlformats.org/package/2006/relationships/metadata/core-properties" Target="docProps/core.xml"/>'
            . '<Relationship Id="rId3" Type="http://schemas.openxmlformats.org/officeDocument/2006/relationships/extended-properties" Target="docProps/app.xml"/>'
            . '</Relationships>';
    }

    private function appXml(string $sheetTitle): string
    {
        return '<?xml version="1.0" encoding="UTF-8" standalone="yes"?>'
            . '<Properties xmlns="http://schemas.openxmlformats.org/officeDocument/2006/extended-properties" '
            . 'xmlns:vt="http://schemas.openxmlformats.org/officeDocument/2006/docPropsVTypes">'
            . '<Application>Laravel</Application>'
            . '<DocSecurity>0</DocSecurity>'
            . '<ScaleCrop>false</ScaleCrop>'
            . '<HeadingPairs><vt:vector size="2" baseType="variant"><vt:variant><vt:lpstr>Worksheets</vt:lpstr></vt:variant><vt:variant><vt:i4>1</vt:i4></vt:variant></vt:vector></HeadingPairs>'
            . '<TitlesOfParts><vt:vector size="1" baseType="lpstr"><vt:lpstr>'
            . htmlspecialchars($sheetTitle, ENT_XML1 | ENT_COMPAT, 'UTF-8')
            . '</vt:lpstr></vt:vector></TitlesOfParts>'
            . '</Properties>';
    }

    private function coreXml(): string
    {
        $time = gmdate('Y-m-d\TH:i:s\Z');

        return '<?xml version="1.0" encoding="UTF-8" standalone="yes"?>'
            . '<cp:coreProperties xmlns:cp="http://schemas.openxmlformats.org/package/2006/metadata/core-properties" '
            . 'xmlns:dc="http://purl.org/dc/elements/1.1/" '
            . 'xmlns:dcterms="http://purl.org/dc/terms/" '
            . 'xmlns:dcmitype="http://purl.org/dc/dcmitype/" '
            . 'xmlns:xsi="http://www.w3.org/2001/XMLSchema-instance">'
            . '<dc:creator>Laravel</dc:creator>'
            . '<cp:lastModifiedBy>Laravel</cp:lastModifiedBy>'
            . '<dcterms:created xsi:type="dcterms:W3CDTF">' . $time . '</dcterms:created>'
            . '<dcterms:modified xsi:type="dcterms:W3CDTF">' . $time . '</dcterms:modified>'
            . '</cp:coreProperties>';
    }

    private function workbookXml(string $sheetTitle): string
    {
        return '<?xml version="1.0" encoding="UTF-8" standalone="yes"?>'
            . '<workbook xmlns="http://schemas.openxmlformats.org/spreadsheetml/2006/main" '
            . 'xmlns:r="http://schemas.openxmlformats.org/officeDocument/2006/relationships">'
            . '<sheets><sheet name="'
            . htmlspecialchars($sheetTitle, ENT_XML1 | ENT_COMPAT, 'UTF-8')
            . '" sheetId="1" r:id="rId1"/></sheets></workbook>';
    }

    private function workbookRelsXml(): string
    {
        return '<?xml version="1.0" encoding="UTF-8" standalone="yes"?>'
            . '<Relationships xmlns="http://schemas.openxmlformats.org/package/2006/relationships">'
            . '<Relationship Id="rId1" Type="http://schemas.openxmlformats.org/officeDocument/2006/relationships/worksheet" Target="worksheets/sheet1.xml"/>'
            . '</Relationships>';
    }
}
