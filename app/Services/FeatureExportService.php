<?php

namespace App\Services;

use Barryvdh\DomPDF\Facade\Pdf;
use Symfony\Component\HttpFoundation\Response;

class FeatureExportService
{
    /**
     * Render dan download PDF laporan fitur dengan standar resmi pusat ekspor (tanpa garis divider kop surat, info-table rapi, footer halaman resmi).
     *
     * @param string $title Judul laporan (misal: "LAPORAN REKAPITULASI PENGHAPUSAN ASET DESA")
     * @param string $subtitle Subjudul laporan atau periode
     * @param array $headers Header kolom tabel (contoh: ['NO', 'KODE BARANG', ...])
     * @param array $rows Baris data dalam bentuk array of array
     * @param string $filename Nama file yang didownload
     * @param string $orientation 'portrait' atau 'landscape'
     * @param array $summaryInfo Informasi ringkasan di bawah header (misal: ['Total Data' => '12', ...])
     * @return Response
     */
    public static function downloadPdf(
        string $title,
        string $subtitle,
        array $headers,
        array $rows,
        string $filename,
        string $orientation = 'portrait',
        array $summaryInfo = []
    ): Response {
        @ini_set('memory_limit', '2048M');
        @set_time_limit(300);

        $desaConfig = config('desa', []);
        $kodeLokasi = $desaConfig['kode_lokasi'] ?? '111.3.2.2009';
        $namaDesa   = strtoupper($desaConfig['nama_lokasi'] ?? 'DEMO');
        $kabupaten  = strtoupper($desaConfig['kabupaten_nama'] ?? 'KABUPATEN DEMO');
        $kecamatan  = strtoupper($desaConfig['kecamatan_nama'] ?? 'KECAMATAN DEMO');
        $provinsi   = strtoupper($desaConfig['provinsi_nama'] ?? 'BALI');
        $tanggalCetak = now()->translatedFormat('d F Y, H:i');

        // Normalisasi pasangan summary info
        $summaryPairs = [];
        foreach ($summaryInfo as $lbl => $val) {
            $summaryPairs[] = ['label' => $lbl, 'value' => $val];
        }

        $html = '<!DOCTYPE html>
<html>
<head>
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8"/>
    <title>' . htmlspecialchars($title) . '</title>
    <style>
        @page {
            margin: 12mm 10mm 20mm 10mm;
        }
        body {
            font-family: Arial, sans-serif;
            font-size: 8.5pt;
            color: #000000;
            background-color: #ffffff;
            margin: 0;
            padding: 0;
            line-height: 1.35;
        }
        .doc-title {
            text-align: center;
            font-size: 13pt;
            font-weight: bold;
            text-transform: uppercase;
            margin: 0 0 4px 0;
            color: #000000;
            letter-spacing: 0.5px;
        }
        .doc-subtitle {
            text-align: center;
            font-size: 9.5pt;
            margin: 0 0 16px 0;
            color: #000000;
        }
        .info-table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 12px;
            font-size: 8.5pt;
        }
        .info-table td {
            padding: 1.5px 3px;
            border: none;
            vertical-align: top;
        }
        .info-label {
            width: 95px;
            font-weight: bold;
        }
        .info-sep {
            width: 10px;
            text-align: center;
            font-weight: bold;
        }
        .info-val {
            font-weight: bold;
        }
        .summary-label {
            text-align: right;
            font-weight: normal;
        }
        .summary-val {
            text-align: left;
            font-weight: bold;
        }
        table.data-table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 4px;
            font-size: 8pt;
            border: 1px solid #000000;
            background-color: #ffffff;
        }
        table.data-table thead {
            display: table-header-group;
        }
        table.data-table tr {
            page-break-inside: auto;
        }
        table.data-table th {
            background-color: #ffffff;
            color: #000000;
            font-size: 8pt;
            font-weight: bold;
            text-transform: uppercase;
            padding: 5px 4px;
            border: 1px solid #000000;
            text-align: center;
            vertical-align: middle;
            white-space: nowrap;
        }
        table.data-table td {
            font-size: 8pt;
            padding: 4px 5px;
            border: 1px solid #000000;
            color: #000000;
            vertical-align: middle;
            background-color: #ffffff;
        }
        .text-center { text-align: center; }
        .text-right { text-align: right; }
        .text-left { text-align: left; }
    </style>
</head>
<body>
    <div class="doc-title">' . htmlspecialchars($title) . '</div>
    ' . (!empty($subtitle) ? '<div class="doc-subtitle">' . htmlspecialchars($subtitle) . '</div>' : '<div style="margin-bottom: 16px;"></div>') . '

    <table class="info-table">
        <tr>
            <td class="info-label">Provinsi</td>
            <td class="info-sep">:</td>
            <td class="info-val">' . htmlspecialchars($provinsi) . '</td>
            <td class="summary-label" style="width: 140px;">Waktu Cetak</td>
            <td class="info-sep">:</td>
            <td class="summary-val" style="width: 160px;">' . htmlspecialchars($tanggalCetak) . '</td>
        </tr>
        <tr>
            <td class="info-label">Kab./Kota</td>
            <td class="info-sep">:</td>
            <td class="info-val">' . htmlspecialchars($kabupaten) . '</td>
            ' . (isset($summaryPairs[0]) ? '<td class="summary-label">' . htmlspecialchars($summaryPairs[0]['label']) . '</td><td class="info-sep">:</td><td class="summary-val">' . htmlspecialchars($summaryPairs[0]['value']) . '</td>' : '<td></td><td></td><td></td>') . '
        </tr>
        <tr>
            <td class="info-label">Kecamatan</td>
            <td class="info-sep">:</td>
            <td class="info-val">' . htmlspecialchars($kecamatan) . '</td>
            ' . (isset($summaryPairs[1]) ? '<td class="summary-label">' . htmlspecialchars($summaryPairs[1]['label']) . '</td><td class="info-sep">:</td><td class="summary-val">' . htmlspecialchars($summaryPairs[1]['value']) . '</td>' : '<td></td><td></td><td></td>') . '
        </tr>
        <tr>
            <td class="info-label">Desa</td>
            <td class="info-sep">:</td>
            <td class="info-val">' . htmlspecialchars($namaDesa) . '</td>
            ' . (isset($summaryPairs[2]) ? '<td class="summary-label">' . htmlspecialchars($summaryPairs[2]['label']) . '</td><td class="info-sep">:</td><td class="summary-val">' . htmlspecialchars($summaryPairs[2]['value']) . '</td>' : '<td></td><td></td><td></td>') . '
        </tr>
        <tr>
            <td class="info-label">No. Kode Lokasi</td>
            <td class="info-sep">:</td>
            <td class="info-val">' . htmlspecialchars($kodeLokasi) . '</td>
            <td></td><td></td><td></td>
        </tr>
    </table>

    <table class="data-table">
        <thead>
            <tr>';

        foreach ($headers as $h) {
            $h = is_array($h) ? $h : ['text' => (string)$h];
            $align = isset($h['align']) ? 'text-align:' . $h['align'] . ';' : '';
            $customStyle = isset($h['style']) ? rtrim($h['style'], ';') . ';' : '';
            $styleAttr = ' style="' . $align . $customStyle . 'white-space:nowrap;"';
            $width = isset($h['width']) ? ' width="' . $h['width'] . '"' : '';
            $html .= '<th' . $styleAttr . $width . '>' . htmlspecialchars($h['text'] ?? '') . '</th>';
        }
        $html .= '</tr></thead><tbody>';

        if (empty($rows)) {
            $html .= '<tr><td colspan="' . count($headers) . '" class="text-center" style="padding: 12px; color: #000000; border: 1px solid #000000; font-style: italic;">Tidak ada data yang tersedia untuk dicetak.</td></tr>';
        } else {
            foreach ($rows as $row) {
                $html .= '<tr>';
                foreach ($row as $cell) {
                    $cell = is_array($cell) ? $cell : ['text' => (string)$cell];
                    $class = isset($cell['class']) ? ' class="' . $cell['class'] . '"' : '';
                    $style = isset($cell['style']) ? ' style="' . $cell['style'] . '"' : '';
                    $colspan = !empty($cell['colspan']) ? ' colspan="' . (int)$cell['colspan'] . '"' : '';
                    $html .= '<td' . $colspan . $class . $style . '>' . ($cell['raw'] ?? htmlspecialchars((string)($cell['text'] ?? ''))) . '</td>';
                }
                $html .= '</tr>';
            }
        }

        $html .= '</tbody></table>';

        // Gunakan footer standar resmi aplikasi (garis pembatas bawah halaman + nomor halaman)
        $html .= \App\Services\ExportService::buildFooterHtml($title);

        $html .= '</body></html>';

        $fontDir = storage_path('fonts');
        if (!is_dir($fontDir)) {
            @mkdir($fontDir, 0775, true);
        }
        if (!is_dir($fontDir) || !is_writable($fontDir)) {
            $fontDir = sys_get_temp_dir();
        }

        $dompdfOptions = [
            'isHtml5ParserEnabled'    => true,
            'isRemoteEnabled'         => false,
            'isPhpEnabled'            => true,
            'isFontSubsettingEnabled' => false,
            'defaultFont'             => 'Arial',
            'fontDir'                 => $fontDir,
            'fontCache'               => $fontDir,
            'tempDir'                 => sys_get_temp_dir(),
            'chroot'                  => [base_path(), storage_path(), sys_get_temp_dir()],
        ];

        $pdf = Pdf::loadHTML($html);
        $pdf->setOptions($dompdfOptions);
        $pdf->setPaper('a4', $orientation);

        return $pdf->download($filename);
    }
}
