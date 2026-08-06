<!doctype html>
<html lang="id">
<head>
    <meta charset="utf-8">
    <title>{{ $title }}</title>
    <style>
        body {
            font-family: DejaVu Sans, sans-serif;
            font-size: 12px;
            color: #000;
            margin: 0;
            padding: 24px;
            background: #fff;
        }
        .header {
            border-bottom: 2px solid #000;
            padding-bottom: 10px;
            margin-bottom: 18px;
        }
        .header h1 {
            margin: 0;
            font-size: 18px;
            color: #000;
        }
        .header p {
            margin: 4px 0 0;
            color: #000;
            font-size: 12px;
        }
        .meta {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 14px;
        }
        .meta td {
            padding: 4px 0;
            vertical-align: top;
        }
        .meta td:first-child {
            width: 140px;
            color: #000;
        }
        .section-title {
            margin: 14px 0 8px;
            font-size: 13px;
            font-weight: 700;
            color: #000;
        }
        .filters {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 10px;
        }
        .filters td {
            border: 1px solid #000;
            padding: 6px 8px;
        }
        .filters td:first-child {
            width: 150px;
            font-weight: 600;
            background: #fff;
        }
        .table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 6px;
        }
        .table th,
        .table td {
            border: 1px solid #000;
            padding: 7px 8px;
            text-align: left;
        }
        .table th {
            background: #000;
            font-weight: 700;
            color: #fff;
        }
        .table td:last-child,
        .table th:last-child {
            text-align: right;
        }
        .totals {
            margin-top: 12px;
            width: 100%;
            border-collapse: collapse;
        }
        .totals td {
            border: 1px solid #000;
            padding: 6px 8px;
        }
        .totals td:first-child {
            width: 180px;
            background: #fff;
            font-weight: 600;
        }
    </style>
</head>
<body>
    <div class="header">
        <h1>{{ $title }}</h1>
        <p>{{ $subtitle }}</p>
    </div>

    <table class="meta">
        <tr>
            <td>Dibuat pada</td>
            <td>: {{ $generatedAt }}</td>
        </tr>
        <tr>
            <td>Dibuat oleh</td>
            <td>: {{ $generatedBy }}</td>
        </tr>
        <tr>
            <td>Jenis chart</td>
            <td>: {{ strtoupper($chartType) }}</td>
        </tr>
        <tr>
            <td>Metrik</td>
            <td>: {{ $metricLabel }}</td>
        </tr>
    </table>

    <div class="section-title">Filter Aktif</div>
    <table class="filters">
        @foreach($filters as $label => $value)
        <tr>
            <td>{{ $label }}</td>
            <td>{{ $value }}</td>
        </tr>
        @endforeach
    </table>

    <div class="section-title">Data Ringkas</div>
    <table class="table">
        <thead>
            <tr>
                <th>Label</th>
                <th>Nilai</th>
            </tr>
        </thead>
        <tbody>
            @forelse($rows as $row)
            <tr>
                <td>{{ $row['label'] ?? '-' }}</td>
                <td>{{ number_format((float) ($row['value'] ?? 0), 0, ',', '.') }}</td>
            </tr>
            @empty
            <tr>
                <td colspan="2">Tidak ada data</td>
            </tr>
            @endforelse
        </tbody>
    </table>

    <table class="totals">
        @foreach($totals as $label => $value)
        <tr>
            <td>{{ $label }}</td>
            <td>{{ number_format((float) $value, 0, ',', '.') }}</td>
        </tr>
        @endforeach
    </table>
</body>
</html>
