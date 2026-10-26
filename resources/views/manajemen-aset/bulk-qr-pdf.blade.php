<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>{{ ($format ?? 'sticker') === 'plain' ? 'QR Code Polos Aset Desa' : 'Label Stiker Resmi QR Code Aset Desa' }}</title>
    <style>
        @page {
            size: a4 portrait;
            margin: 10mm 12mm;
        }
        * {
            box-sizing: border-box;
            -webkit-print-color-adjust: exact;
            print-color-adjust: exact;
        }
        body {
            font-family: Arial, Helvetica, sans-serif;
            margin: 0;
            padding: 0;
            color: #000000;
            background: #FFFFFF;
        }
        .page-sheet {
            width: 100%;
            page-break-after: always;
        }
        .page-sheet:last-child {
            page-break-after: avoid;
        }
        table.grid-table {
            width: 100%;
            border-collapse: separate;
            border-spacing: 0 6mm;
            table-layout: fixed;
        }
        td.label-cell {
            width: 100%;
            vertical-align: top;
            padding: 0;
        }

        /* ── Template 1: Label Stiker Resmi (Full Width 1 Kolom Proporsional) ── */
        table.official-sticker {
            width: 100%;
            border: 2.5px solid #000000;
            border-collapse: collapse;
            background: #FFFFFF;
            table-layout: fixed;
        }
        td.sticker-red-header {
            background-color: #c81e1e;
            color: #FFFFFF;
            text-align: center;
            padding: 6px 4px;
            font-size: 11pt;
            font-weight: bold;
            letter-spacing: 1px;
            text-transform: uppercase;
            border-bottom: 2.5px solid #000000;
            line-height: 1.2;
        }
        tr.sticker-body-row {
            height: 48mm;
        }
        td.sticker-col-logo {
            width: 48mm;
            text-align: center;
            vertical-align: middle;
            border-right: 2.5px solid #000000;
            padding: 4px;
            background: #FFFFFF;
        }
        td.sticker-col-center {
            vertical-align: middle;
            padding: 0;
            text-align: center;
            background: #FFFFFF;
        }
        td.sticker-col-qr {
            width: 48mm;
            text-align: center;
            vertical-align: middle;
            border-left: 2.5px solid #000000;
            padding: 4px;
            background: #FFFFFF;
        }
        .sticker-lokasi {
            font-family: 'Courier New', Courier, monospace;
            font-size: 13.5pt;
            font-weight: bold;
            color: #000000;
            text-align: center;
            padding: 8px 4px;
            border-bottom: 2.5px solid #000000;
            letter-spacing: 0.5px;
        }
        .sticker-barang-box {
            font-family: 'Courier New', Courier, monospace;
            font-weight: bold;
            color: #000000;
            text-align: center;
            padding: 7px 4px;
        }
        .sticker-kode-barang {
            font-size: 12.5pt;
            margin-bottom: 3px;
            letter-spacing: 0.5px;
        }
        .sticker-nup-tahun {
            font-size: 11pt;
            letter-spacing: 0.3px;
        }
        .logo-img {
            max-width: 40mm;
            max-height: 40mm;
            display: inline-block;
        }
        .qr-img {
            width: 42mm;
            height: 42mm;
            display: inline-block;
        }

        /* ── Template 2: Plain QR Code Saja (Full Width 1 Kolom Proporsional) ── */
        table.plain-sticker {
            width: 100%;
            border: 1.5px dashed #A0A590;
            border-collapse: collapse;
            background: #FFFFFF;
            text-align: center;
            table-layout: fixed;
            height: 56mm;
        }
        td.plain-col {
            text-align: center;
            vertical-align: middle;
            padding: 8px;
        }
        .plain-qr-img {
            width: 46mm;
            height: 46mm;
            display: inline-block;
        }
    </style>
</head>
<body>
    @php
        $desaUpb = strtoupper(config('desa.upb') ?: ('PEMERINTAH DESA ' . (config('desa.desa_nama') ?: (config('desa.nama_lokasi') ?: 'DEMO'))));
        $kodeLokasi = config('desa.kode_lokasi') ?: '51.03.02.2009';
        $currentFormat = $format ?? 'sticker';
        
        // 4 label proporsional lebar penuh per halaman A4
        $pages = array_chunk($items, 4);
    @endphp

    @foreach($pages as $pageIndex => $pageItems)
    <div class="page-sheet">
        <table class="grid-table" cellspacing="0" cellpadding="0">
            @foreach($pageItems as $entry)
            @php
                $inv = $entry['item'];
                $qr = $entry['qrBase64'];
            @endphp
            <tr>
                <td class="label-cell">
                    @if($currentFormat === 'sticker')
                        <!-- Template 1: Label Stiker Resmi (Lebar Penuh Proporsional) -->
                        <table class="official-sticker" cellspacing="0" cellpadding="0">
                            <tr>
                                <td colspan="3" class="sticker-red-header">
                                    {{ $desaUpb }}
                                </td>
                            </tr>
                            <tr class="sticker-body-row">
                                <td class="sticker-col-logo">
                                    @if(!empty($logoBase64))
                                        <img src="{{ $logoBase64 }}" class="logo-img" alt="Logo">
                                    @else
                                        <div style="font-size: 8pt; font-weight: bold; color: #000;">SIMA</div>
                                    @endif
                                </td>
                                <td class="sticker-col-center">
                                    <div class="sticker-lokasi">
                                        {{ $kodeLokasi }}
                                    </div>
                                    <div class="sticker-barang-box">
                                        <div class="sticker-kode-barang">{{ $inv->kode_barang ?? '-' }}</div>
                                        <div class="sticker-nup-tahun">NUP : {{ $inv->nup ?? '-' }} / {{ $inv->tahun_perolehan ?? '-' }}</div>
                                    </div>
                                </td>
                                <td class="sticker-col-qr">
                                    <img src="{{ $qr }}" class="qr-img" alt="QR Code">
                                </td>
                            </tr>
                        </table>
                    @else
                        <!-- Template 2: Plain QR Code Saja (Lebar Penuh Proporsional) -->
                        <table class="plain-sticker" cellspacing="0" cellpadding="0">
                            <tr>
                                <td class="plain-col">
                                    <img src="{{ $qr }}" class="plain-qr-img" alt="QR Code">
                                </td>
                            </tr>
                        </table>
                    @endif
                </td>
            </tr>
            @endforeach
        </table>
    </div>
    @endforeach
</body>
</html>
