<!DOCTYPE html>
<html lang="id">
<head>
    @include('layouts.head-page-meta', ['title' => 'Riwayat Ekspor - Sistem Manajemen Aset Desa'])
    @include('layouts.head-css')
    <style>
        /* Stat Cards */
        .stat-card-sima {
            user-select: none;
            transition: border-color 150ms ease, background-color 150ms ease;
        }

        /* Table Loading Overlay */
        .table-loading-wrap {
            position: relative;
        }
        .table-loading-wrap.is-loading .table-sima tbody {
            opacity: 0.4;
            pointer-events: none;
        }

        /* Hide topbar search dropdown icon on this page specifically */
        .me-auto.pc-mob-drp i[data-feather="search"],
        .drp-search,
        .me-auto.pc-mob-drp .pc-head-link.dropdown-toggle svg.feather,
        .me-auto.pc-mob-drp .pc-head-link.dropdown-toggle i[data-feather="search"] {
            display: none !important;
        }

        /* Pagination Bar Responsive */
        .pagination-bar {
            display: flex;
            align-items: center;
            justify-content: space-between;
            padding: 14px 20px;
            border-top: 1px solid var(--color-border);
            gap: 14px;
            flex-wrap: wrap;
        }
        .pagination-left { display: flex; align-items: center; gap: 10px; }
        .page-size-wrap { display: flex; align-items: center; gap: 6px; font-size: 13px; color: var(--color-text-muted); }
        .per-page-select {
            min-width: 68px;
            padding: 6px 30px 6px 12px;
            font-size: 13px;
            font-weight: 500;
            color: var(--color-text);
            background-color: var(--color-surface);
            border: 1px solid var(--color-border);
            border-radius: var(--radius-sm);
            cursor: pointer;
            outline: none;
            transition: all 0.2s ease;
            appearance: none;
            -webkit-appearance: none;
            background-image: url("data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' fill='none' viewBox='0 0 24 24' stroke='%235E6C5A'%3E%3Cpath stroke-linecap='round' stroke-linejoin='round' stroke-width='2' d='M19 9l-7 7-7-7'%3E%3C/path%3E%3C/svg%3E");
            background-repeat: no-repeat;
            background-position: right 8px center;
            background-size: 12px;
            box-sizing: border-box;
            text-align: left;
        }
        .per-page-select:hover, .per-page-select:focus { border-color: var(--color-primary); }
        .pagination-center { display: flex; align-items: center; gap: 10px; justify-content: center; flex-wrap: wrap; }
        .pagination-right { display: flex; align-items: center; gap: 5px; font-size: 13px; color: var(--color-text-muted); white-space: nowrap; }
        .pagination-right strong { color: var(--color-text); font-weight: 600; margin: 0 2px; }
        .pagination-nav { display: flex; align-items: center; justify-content: center; gap: 4px; }
        .page-btn {
            min-width: 32px;
            height: 32px;
            padding: 0 8px;
            border: 1px solid var(--color-border);
            border-radius: var(--radius-sm);
            background: var(--color-surface);
            color: var(--color-text-muted);
            font-size: 13px;
            font-weight: 500;
            display: flex;
            align-items: center;
            justify-content: center;
            cursor: pointer;
            transition: all 0.15s ease;
            text-decoration: none;
        }
        .page-btn:hover { background: var(--color-row-hover); color: var(--color-text); border-color: var(--color-primary); }
        .page-btn.active { background: var(--color-primary); color: #eaf1e7; border-color: var(--color-primary); }
        .page-btn.disabled { opacity: 0.4; cursor: not-allowed; pointer-events: none; }
        .page-btn i { width: 14px; height: 14px; }
        .page-jump-wrap { display: flex; align-items: center; gap: 6px; margin-left: 6px; }
        .page-jump-wrap label { font-size: 12.5px; color: var(--color-text-muted); white-space: nowrap; }
        .page-jump-input {
            width: 50px;
            height: 32px;
            border: 1px solid var(--color-border);
            border-radius: var(--radius-sm);
            text-align: center;
            font-size: 13px;
            color: var(--color-text);
            outline: none;
            background: var(--color-surface);
            transition: border-color .15s;
        }
        .page-jump-input:focus { border-color: var(--color-primary); box-shadow: none; }

        @media (max-width: 768px) {
            .pagination-bar {
                flex-direction: column;
                align-items: center;
                text-align: center;
                gap: 12px;
                padding: 16px 12px;
            }
            .pagination-nav { width: 100%; flex-wrap: wrap; }
            .pagination-right { justify-content: center; width: 100%; }
        }
    </style>
</head>

<body>
    @include('layouts.notification')
    @include('layouts.modals')
    @include('layouts.layout-vertical')

    <div class="pc-container">
        <div class="pc-content" style="max-width: 1560px; margin: 0 auto;">

            <!-- PAGE HEADER & PRIMARY ACTIONS -->
            <div style="display: flex; justify-content: space-between; align-items: flex-start; margin-bottom: 24px; flex-wrap: wrap; gap: 16px;">
                <div>
                    <h1 style="font-size: 22px; font-weight: 700; margin: 0; color: var(--color-text); line-height: 1.2; letter-spacing: -0.02em;">
                        Riwayat Ekspor & Laporan
                    </h1>
                </div>
            </div>

            @php
                $totalExportAll = \App\Models\ExportLog::excludeSuperDevelopers()->count();
                $exportMonth    = \App\Models\ExportLog::excludeSuperDevelopers()->whereMonth('created_at', now()->month)->whereYear('created_at', now()->year)->count();
                $excelCount     = \App\Models\ExportLog::excludeSuperDevelopers()->where('tipe_export', 'like', '%excel%')->count();
                $pdfCount       = \App\Models\ExportLog::excludeSuperDevelopers()->where('tipe_export', 'like', '%pdf%')->count();
            @endphp

            <!-- 1. STAT CARDS (Prodex 4-Card Summary Bar) -->
            <div class="stat-card-row">
                <!-- Card 1: Total Ekspor -->
                <div class="stat-card-sima" id="card-total-export">
                    <div class="stat-header">
                        <span class="stat-label">TOTAL EKSPOR</span>
                    </div>
                    <div class="stat-number">{{ number_format($totalExportAll ?? 0, 0, ',', '.') }}</div>
                    <div class="kpi-trend-wrapper stat-delta">
                        <span class="stat-delta-label">semua berkas</span>
                    </div>
                </div>

                <!-- Card 2: Ekspor Bulan Ini -->
                <div class="stat-card-sima" id="card-export-month">
                    <div class="stat-header">
                        <span class="stat-label">EKSPOR BULAN INI</span>
                    </div>
                    <div class="stat-number">{{ number_format($exportMonth ?? 0, 0, ',', '.') }}</div>
                    <div class="kpi-trend-wrapper stat-delta">
                        <span class="stat-delta-label">{{ now()->translatedFormat('F Y') }}</span>
                    </div>
                </div>

                <!-- Card 3: Berkas Excel -->
                <div class="stat-card-sima" id="card-export-excel">
                    <div class="stat-header">
                        <span class="stat-label">DOKUMEN EXCEL</span>
                    </div>
                    <div class="stat-number">{{ number_format($excelCount ?? 0, 0, ',', '.') }}</div>
                    <div class="kpi-trend-wrapper stat-delta">
                        <span class="stat-delta-pill is-up">
                            .xlsx
                        </span>
                    </div>
                </div>

                <!-- Card 4: Berkas PDF -->
                <div class="stat-card-sima" id="card-export-pdf">
                    <div class="stat-header">
                        <span class="stat-label">DOKUMEN PDF</span>
                    </div>
                    <div class="stat-number">{{ number_format($pdfCount ?? 0, 0, ',', '.') }}</div>
                    <div class="kpi-trend-wrapper stat-delta">
                        <span class="stat-delta-pill is-neutral">
                            .pdf
                        </span>
                    </div>
                </div>
            </div>

            <!-- 2. MAIN TABLE CARD -->
            <div class="sima-card" style="padding: 0; overflow: visible; margin-bottom: 32px;">
                
                <!-- Action Controls Bar -->
                <form method="GET" action="{{ route('export-data.history') }}" id="filter-form" style="margin: 0; padding: 20px 20px; border-bottom: 1px solid var(--color-border); display: flex; justify-content: space-between; align-items: center; flex-wrap: wrap; gap: 12px; border-top-left-radius: var(--radius-lg, 12px); border-top-right-radius: var(--radius-lg, 12px);">
                    <!-- Left: Search Box -->
                    <div class="topbar-search-box">
                        <i data-feather="search" style="width: 15px; height: 15px; color: var(--color-text-muted); flex-shrink: 0;"></i>
                        <input
                            type="text"
                            id="searchInput"
                            name="search"
                            class="topbar-search-input"
                            placeholder="Cari user, jenis, atau file..."
                            value="{{ request('search') }}"
                            autocomplete="off"
                        >
                        <span class="topbar-search-shortcut" id="clear-search-btn" style="cursor: pointer; display: {{ request('search') ? 'inline-block' : 'none' }}; color: var(--color-text-muted); font-size: 11px;">Batal</span>
                    </div>

                    <!-- Right Controls: Reset Filter & Filter Dropdown & Delete All -->
                    <div style="display: flex; align-items: center; gap: 8px; flex-wrap: wrap;">
                        <button type="button" class="btn-sima-secondary btn-reset-filter" id="btn-reset-filter" onclick="resetFilters()" title="Reset semua filter" style="padding: 7px 12px; font-size: 13px;">
                            <i data-feather="rotate-ccw" style="width: 13px; height: 13px;"></i>
                            <span>Reset Filter</span>
                        </button>

                        <div class="dropdown" id="filter-dropdown-container" style="position: relative;">
                            <button class="btn-sima-secondary" id="btn-filter-toggle" type="button" onclick="toggleFilterPopover(event)" title="Filter" style="padding: 7px 12px; font-size: 13px; display: inline-flex; align-items: center; gap: 6px;">
                                <i data-feather="sliders" style="width: 13px; height: 13px;"></i>
                                <span>Filter</span>
                            </button>
                            @php
                                $selectedFormats = request()->has('format')
                                    ? array_values(array_filter((array) request('format')))
                                    : [];

                                $selectedJenis = request()->has('jenis')
                                    ? array_values(array_filter((array) request('jenis')))
                                    : [];
                            @endphp
                            <div id="filter-dropdown-menu" class="sima-filter-popover" onclick="event.stopPropagation();" style="display: none; position: absolute; top: calc(100% + 4px); right: 0; min-width: 320px; padding: 20px 24px; z-index: 1050; background: var(--color-surface); border: 1px solid var(--color-border); border-radius: var(--radius-lg); box-shadow: var(--shadow-float); margin: 0;">
                                <div style="color: var(--color-primary); font-size: 12px; font-weight: 700; text-transform: uppercase; letter-spacing: 0.04em; margin-bottom: 8px;">Format Berkas</div>
                                <div style="display: flex; flex-direction: column; gap: 8px; margin-bottom: 14px;">
                                    <label style="cursor: pointer; display: flex; align-items: center; gap: 8px; font-size: 13px; color: var(--color-text); margin: 0;">
                                        <input type="checkbox" name="format[]" value="excel" {{ in_array('excel', $selectedFormats, true) ? 'checked' : '' }} class="sima-checkbox">
                                        Excel (.xlsx)
                                    </label>
                                    <label style="cursor: pointer; display: flex; align-items: center; gap: 8px; font-size: 13px; color: var(--color-text); margin: 0;">
                                        <input type="checkbox" name="format[]" value="pdf" {{ in_array('pdf', $selectedFormats, true) ? 'checked' : '' }} class="sima-checkbox">
                                        PDF (.pdf)
                                    </label>
                                </div>

                                <hr style="border: none; border-top: 1px dashed var(--color-border); margin: 12px 0;">

                                <div style="color: var(--color-primary); font-size: 12px; font-weight: 700; text-transform: uppercase; letter-spacing: 0.04em; margin-bottom: 8px;">Jenis Inventaris</div>
                                <div style="max-height: 160px; overflow-y: auto; display: flex; flex-direction: column; gap: 8px; margin-bottom: 14px; padding-right: 4px;">
                                    <label style="cursor: pointer; display: flex; align-items: center; gap: 8px; font-size: 13px; color: var(--color-text); margin: 0;">
                                        <input type="checkbox" name="jenis[]" value="all" {{ in_array('all', array_map('strval', $selectedJenis), true) ? 'checked' : '' }} class="sima-checkbox">
                                        Semua Jenis
                                    </label>
                                    @foreach($jenisList as $j)
                                        <label style="cursor: pointer; display: flex; align-items: center; gap: 8px; font-size: 13px; color: var(--color-text); margin: 0;">
                                            <input type="checkbox" name="jenis[]" value="{{ $j->id }}" {{ in_array((string) $j->id, array_map('strval', $selectedJenis), true) ? 'checked' : '' }} class="sima-checkbox">
                                            {{ $j->nama }}
                                        </label>
                                    @endforeach
                                </div>

                                <hr style="border: none; border-top: 1px dashed var(--color-border); margin: 12px 0;">

                                <div style="color: var(--color-primary); font-size: 12px; font-weight: 700; text-transform: uppercase; letter-spacing: 0.04em; margin-bottom: 8px;">Rentang Waktu</div>
                                <div style="display: flex; flex-direction: column; gap: 8px; margin-bottom: 14px;">
                                    <label style="cursor: pointer; display: flex; align-items: center; gap: 8px; font-size: 13px; color: var(--color-text); margin: 0;">
                                        <input type="radio" name="date_range" value="1d" {{ request('date_range') === '1d' ? 'checked' : '' }} style="accent-color: var(--color-primary); cursor: pointer;">
                                        1 Hari Terakhir
                                    </label>
                                    <label style="cursor: pointer; display: flex; align-items: center; gap: 8px; font-size: 13px; color: var(--color-text); margin: 0;">
                                        <input type="radio" name="date_range" value="7d" {{ request('date_range') === '7d' ? 'checked' : '' }} style="accent-color: var(--color-primary); cursor: pointer;">
                                        1 Minggu Terakhir
                                    </label>
                                    <label style="cursor: pointer; display: flex; align-items: center; gap: 8px; font-size: 13px; color: var(--color-text); margin: 0;">
                                        <input type="radio" name="date_range" value="30d" {{ request('date_range') === '30d' ? 'checked' : '' }} style="accent-color: var(--color-primary); cursor: pointer;">
                                        1 Bulan Terakhir
                                    </label>
                                </div>

                                <hr style="border: none; border-top: 1px dashed var(--color-border); margin: 12px 0;">

                                <div style="color: var(--color-primary); font-size: 12px; font-weight: 700; text-transform: uppercase; letter-spacing: 0.04em; margin-bottom: 8px;">Urutkan</div>
                                <div style="display: flex; flex-direction: column; gap: 8px; margin-bottom: 16px;">
                                    @php $curSort = request('sort', 'terbaru'); @endphp
                                    <label style="cursor: pointer; display: flex; align-items: center; gap: 8px; font-size: 13px; color: var(--color-text); margin: 0;">
                                        <input type="radio" name="sort" value="terbaru" {{ $curSort === 'terbaru' ? 'checked' : '' }} style="accent-color: var(--color-primary); cursor: pointer;">
                                        Terbaru
                                    </label>
                                    <label style="cursor: pointer; display: flex; align-items: center; gap: 8px; font-size: 13px; color: var(--color-text); margin: 0;">
                                        <input type="radio" name="sort" value="terlama" {{ $curSort === 'terlama' ? 'checked' : '' }} style="accent-color: var(--color-primary); cursor: pointer;">
                                        Terlama
                                    </label>
                                    <label style="cursor: pointer; display: flex; align-items: center; gap: 8px; font-size: 13px; color: var(--color-text); margin: 0;">
                                        <input type="radio" name="sort" value="user_asc" {{ $curSort === 'user_asc' ? 'checked' : '' }} style="accent-color: var(--color-primary); cursor: pointer;">
                                        User A-Z
                                    </label>
                                    <label style="cursor: pointer; display: flex; align-items: center; gap: 8px; font-size: 13px; color: var(--color-text); margin: 0;">
                                        <input type="radio" name="sort" value="user_desc" {{ $curSort === 'user_desc' ? 'checked' : '' }} style="accent-color: var(--color-primary); cursor: pointer;">
                                        User Z-A
                                    </label>
                                </div>

                                <div style="padding-top: 12px; border-top: 1px dashed var(--color-border);">
                                    <button type="submit" class="btn-sima-primary" onclick="closeFilterPopover();" style="width: 100%; justify-content: center; font-size: 13px;">
                                        Terapkan Filter
                                    </button>
                                </div>
                            </div>
                        </div>

                        @if(($canDelete ?? false) && $logs->total() > 0)
                        <button type="button" class="btn-sima-danger" onclick="confirmDeleteAllExportHistory()" title="Hapus semua riwayat ekspor">
                            <i data-feather="trash-2"></i>
                            <span>Hapus Semua</span>
                        </button>
                        @endif
                    </div>
                </form>

                @if(($canDelete ?? false) && $logs->total() > 0)
                <form id="deleteAllExportHistoryForm" method="POST" action="{{ route('export-data.history.destroy-all') }}" style="display:none;">
                    @csrf
                </form>
                @endif

                <!-- Table Viewport -->
                <div class="table-sima-wrapper table-loading-wrap" style="border: none; border-radius: 0;">
                    <table class="table-sima">
                        <thead>
                            <tr>
                                <th style="width: 60px; text-align: center;">No</th>
                                <th style="width: 220px;">User & Waktu</th>
                                <th style="min-width: 260px;">Jenis Inventaris</th>
                                <th style="width: 130px; text-align: center;">Format</th>
                                <th style="min-width: 280px;">Nama File (Audit)</th>
                                <th style="width: 200px;">Pejabat TTD</th>
                                @if($canDelete ?? false)
                                <th style="width: 70px; text-align: center;">Aksi</th>
                                @endif
                            </tr>
                        </thead>
                        <tbody id="history-tbody">
                            @forelse($logs as $log)
                            <tr>
                                <td style="text-align: center; font-variant-numeric: tabular-nums; color: var(--color-text-muted);">
                                    {{ $logs->firstItem() + $loop->index }}
                                </td>
                                <td>
                                    <div style="display: flex; flex-direction: column; gap: 2px;">
                                        <div style="font-weight: 600; color: var(--color-text); font-size: 13px;">
                                            {{ optional($log->user)->nama ?? 'Unknown' }}
                                        </div>
                                        <div style="font-size: 11px; color: var(--color-text-muted); font-variant-numeric: tabular-nums;">
                                            {{ $log->created_at ? $log->created_at->format('d/m/Y H:i') : '-' }} WITA
                                        </div>
                                    </div>
                                </td>
                                <td>
                                    @if(is_null($log->jenis_inventaris_id))
                                        @php
                                            $logFilters = is_array($log->filters) ? $log->filters : [];
                                            $virtualJenis = $logFilters['virtual_export_as'] ?? 'Semua Jenis';
                                        @endphp
                                        <span style="font-weight: 600; color: var(--color-text); font-size: 13.5px;">{{ $virtualJenis }}</span>
                                    @elseif($log->jenisInventaris)
                                        <span style="font-weight: 600; color: var(--color-text); font-size: 13.5px;">{{ $log->jenisInventaris->nama }}</span>
                                    @else
                                        <span style="font-size: 13px; color: var(--color-text-muted); font-style: italic;">jenis inventaris telah terhapus</span>
                                    @endif
                                </td>
                                <td style="text-align: center;">
                                    @php
                                        $fmt = strtolower($log->tipe_export ?? 'excel');
                                        $pillClass = ($fmt === 'excel' || str_contains($fmt, 'excel')) ? 'status-pill-success' : 'status-pill-danger';
                                    @endphp
                                    <span class="status-pill {{ $pillClass }}" style="font-size: 11px; font-weight: 600;">
                                        {{ strtoupper($log->tipe_export ?? 'EXCEL') }}
                                    </span>
                                </td>
                                <td>
                                    @if($log->nama_file)
                                    <span style="color: var(--color-text); font-size: 12.5px; word-break: break-all; font-family: monospace;">
                                        {{ $log->nama_file }}
                                    </span>
                                    @else
                                    <span style="color: var(--color-text-muted); font-style: italic; font-size: 12px;">File tidak tersedia</span>
                                    @endif
                                </td>
                                <td>
                                    @php
                                        $filters = is_array($log->filters) ? $log->filters : [];
                                        $sigOpt = strtolower((string)($filters['signature_option'] ?? ''));
                                        $override = is_array($log->pejabat_override) ? $log->pejabat_override : [];
                                        $overrideNames = collect($override)
                                            ->map(function ($p) {
                                                if (!is_array($p)) return null;
                                                return $p['nama'] ?? ($p['nama_pejabat'] ?? null);
                                            })
                                            ->filter()
                                            ->values();
                                    @endphp

                                    @if($sigOpt === 'without')
                                        <span style="font-size: 13px; color: var(--color-text-muted);">Tanpa TTD</span>
                                    @elseif($overrideNames->count() > 0)
                                        @php
                                            $display = $overrideNames->take(2)->implode(', ');
                                            if ($overrideNames->count() > 2) {
                                                $display .= ' +' . ($overrideNames->count() - 2) . ' lainnya';
                                            }
                                        @endphp
                                        <span style="font-size: 13px; color: var(--color-text);">{{ $display }}</span>
                                    @elseif($log->pejabat)
                                        <span style="font-size: 13px; color: var(--color-text);">{{ $log->pejabat->nama ?? 'Pejabat tidak diketahui' }}</span>
                                    @else
                                        <span style="font-size: 13px; color: var(--color-text-muted);">Tanpa TTD</span>
                                    @endif
                                </td>
                                @if($canDelete ?? false)
                                <td style="text-align: center;">
                                    <form id="deleteExportHistoryForm{{ $log->id }}" method="POST" action="{{ route('export-data.history.destroy', $log->id) }}" style="margin: 0; display: inline-flex;">
                                        @csrf
                                        @method('DELETE')
                                        <button type="button" class="btn-sima-icon delete" title="Hapus riwayat ekspor" onclick="confirmDeleteExportHistory({{ $log->id }})">
                                            <i data-feather="trash-2"></i>
                                        </button>
                                    </form>
                                </td>
                                @endif
                            </tr>
                            @empty
                            <tr>
                                <td colspan="{{ ($canDelete ?? false) ? 7 : 6 }}" style="text-align: center; padding: 48px 20px; color: var(--color-text-muted);">
                                    <div style="display: flex; flex-direction: column; align-items: center; justify-content: center; gap: 8px;">
                                        <i data-feather="inbox" style="width: 42px; height: 42px; stroke-width: 1.5; color: var(--color-border-dark);"></i>
                                        <p style="margin: 0; font-size: 13.5px; font-weight: 500;">Belum ada riwayat ekspor yang sesuai</p>
                                    </div>
                                </td>
                            </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>

                <!-- Pagination Bar -->
                @if($logs->total() > 0)
                <div class="pagination-bar" style="padding: 32px 20px; border-top: 1px solid var(--color-border); background: var(--color-surface); flex-wrap: wrap; gap: 14px; border-bottom-left-radius: var(--radius-lg, 12px); border-bottom-right-radius: var(--radius-lg, 12px);">
                    <div class="pagination-left">
                        <div class="page-size-wrap">
                            <span>Tampilkan</span>
                            <select id="perPageSelect" class="per-page-select" onchange="changePerPage(this.value)">
                                <option value="10" {{ ($logs->perPage() == 10) ? 'selected' : '' }}>10</option>
                                <option value="20" {{ ($logs->perPage() == 20) ? 'selected' : '' }}>20</option>
                                <option value="50" {{ ($logs->perPage() == 50) ? 'selected' : '' }}>50</option>
                                <option value="100" {{ ($logs->perPage() == 100) ? 'selected' : '' }}>100</option>
                            </select>
                            <span>data</span>
                        </div>
                    </div>

                    <div class="pagination-center">
                        <nav class="pagination-nav" id="paginationNav">
                            @if ($logs->onFirstPage())
                                <span class="page-btn disabled" title="Sebelumnya"><i data-feather="chevron-left"></i></span>
                            @else
                                <button type="button" class="page-btn" onclick="goToHistoryPage({{ $logs->currentPage() - 1 }})" title="Sebelumnya"><i data-feather="chevron-left"></i></button>
                            @endif

                            @php
                                $currentPage  = $logs->currentPage();
                                $lastPage     = $logs->lastPage();
                                $windowStart  = max(1, $currentPage - 2);
                                $windowEnd    = min($lastPage, $currentPage + 2);
                                if (($windowEnd - $windowStart + 1) < 5) {
                                    if ($windowStart === 1) {
                                        $windowEnd = min($lastPage, 5);
                                    } else {
                                        $windowStart = max(1, $windowEnd - 4);
                                    }
                                }
                            @endphp
                            @for ($p = $windowStart; $p <= $windowEnd; $p++)
                                @if ($p == $currentPage)
                                    <span class="page-btn active">{{ $p }}</span>
                                @else
                                    <button type="button" class="page-btn" onclick="goToHistoryPage({{ $p }})">{{ $p }}</button>
                                @endif
                            @endfor

                            @if ($logs->hasMorePages())
                                <button type="button" class="page-btn" onclick="goToHistoryPage({{ $logs->currentPage() + 1 }})" title="Selanjutnya"><i data-feather="chevron-right"></i></button>
                            @else
                                <span class="page-btn disabled" title="Selanjutnya"><i data-feather="chevron-right"></i></span>
                            @endif
                        </nav>
                        <div class="page-jump-wrap">
                            <label for="pageJumpInput">Ke hal.</label>
                            <input
                                id="pageJumpInput"
                                type="number"
                                class="page-jump-input"
                                min="1"
                                max="{{ $logs->lastPage() }}"
                                value="{{ $logs->currentPage() }}"
                                placeholder="1–{{ $logs->lastPage() }}"
                                title="Masukkan nomor halaman (1–{{ $logs->lastPage() }})"
                            >
                        </div>
                    </div>

                    <div class="pagination-right" id="paginationRangeInfo">
                        Menampilkan <strong>{{ $logs->firstItem() ?? 0 }}–{{ $logs->lastItem() ?? 0 }}</strong> dari <strong>{{ $logs->total() }}</strong> data
                    </div>
                </div>
                @endif
            </div>

        </div>
    </div>

    @include('layouts.footer-block')
    @include('layouts.footer-js')

    <script>
        if(typeof feather !== 'undefined') feather.replace();

        function changePerPage(val) {
            fetchHistoryDynamically(false, 1);
        }

        function goToHistoryPage(page) {
            fetchHistoryDynamically(false, page);
        }

        // Jump-to-page input
        (function () {
            const jumpInput = document.getElementById('pageJumpInput');
            if (!jumpInput) return;
            jumpInput.addEventListener('keydown', function (e) {
                if (e.key !== 'Enter') return;
                const lastPage = parseInt(this.max || 1, 10);
                const val = parseInt(this.value, 10);
                if (isNaN(val) || val < 1 || val > lastPage) return;
                goToHistoryPage(val);
            });
        })();

        const searchInput = document.getElementById('searchInput');
        const clearBtn = document.getElementById('clear-search-btn');
        const resetFilterBtn = document.getElementById('btn-reset-filter');
        const filterForm = document.getElementById('filter-form');
        let searchDebounceTimer = null;

        function toggleSearchClear(input) {
            if (!input || !clearBtn) return;
            clearBtn.style.display = input.value ? 'inline-block' : 'none';
        }

        function buildParamsFromForm() {
            const params = new URLSearchParams();
            if (!filterForm) return params;

            const formData = new FormData(filterForm);
            formData.forEach(function(value, key) {
                const strValue = String(value).trim();
                if (strValue !== '') {
                    params.append(key, strValue);
                }
            });

            const perPageSelect = document.getElementById('perPageSelect');
            const perPageValue = perPageSelect ? perPageSelect.value : '10';
            if (perPageValue && perPageValue !== '10') {
                params.set('per_page', perPageValue);
            }

            return params;
        }

        function replaceHistoryDomFromHtml(htmlText) {
            const parser = new DOMParser();
            const doc = parser.parseFromString(htmlText, 'text/html');

            const newTbody = doc.getElementById('history-tbody');
            const curTbody = document.getElementById('history-tbody');
            if (newTbody && curTbody) {
                curTbody.innerHTML = newTbody.innerHTML;
            }

            const curCard = document.querySelector('.sima-card');
            const curPagination = curCard ? curCard.querySelector('.pagination-bar') : null;
            const newCard = doc.querySelector('.sima-card');
            const newPagination = newCard ? newCard.querySelector('.pagination-bar') : null;

            if (curCard) {
                if (curPagination && newPagination) {
                    curPagination.outerHTML = newPagination.outerHTML;
                } else if (curPagination && !newPagination) {
                    curPagination.remove();
                } else if (!curPagination && newPagination) {
                    curCard.appendChild(newPagination.cloneNode(true));
                }
            }

            const jumpInput = document.getElementById('pageJumpInput');
            if (jumpInput) {
                jumpInput.addEventListener('keydown', function (e) {
                    if (e.key !== 'Enter') return;
                    const lastPage = parseInt(this.max || 1, 10);
                    const val = parseInt(this.value, 10);
                    if (isNaN(val) || val < 1 || val > lastPage) return;
                    goToHistoryPage(val);
                });
            }

            if(typeof feather !== 'undefined') feather.replace();
        }

        function fetchHistoryDynamically(isAutoRefresh = false, pageOverride = null) {
            if (!filterForm) return;

            const params = buildParamsFromForm();
            const urlParams = new URLSearchParams(window.location.search);
            let page = pageOverride !== null ? pageOverride : (urlParams.get('page') || 1);
            if (page > 1) {
                params.set('page', page);
            }

            const filterQuery = params.toString();
            try {
                if (filterQuery) {
                    sessionStorage.setItem('export_history_filter_state', filterQuery);
                } else {
                    sessionStorage.removeItem('export_history_filter_state');
                }
            } catch(e) {}

            const actionUrl = filterForm.getAttribute('action') || window.location.pathname;
            const targetUrl = actionUrl + (filterQuery ? ('?' + filterQuery) : '');

            history.replaceState(null, '', targetUrl);

            const wrap = document.querySelector('.table-loading-wrap');
            if (!isAutoRefresh && wrap) wrap.classList.add('is-loading');

            fetch(targetUrl, {
                headers: {
                    'X-Requested-With': 'XMLHttpRequest',
                    'Accept': 'text/html'
                }
            })
            .then(function(res) { return res.text(); })
            .then(function(htmlText) {
                replaceHistoryDomFromHtml(htmlText);
            })
            .catch(function(err) {
                console.error('Gagal memuat data riwayat secara dinamis:', err);
            })
            .finally(function() {
                if (wrap) wrap.classList.remove('is-loading');
            });
        }

        if (searchInput) {
            searchInput.addEventListener('input', function() {
                toggleSearchClear(this);

                clearTimeout(searchDebounceTimer);
                searchDebounceTimer = setTimeout(function() {
                    fetchHistoryDynamically();
                }, 400);
            });

            searchInput.addEventListener('keydown', function(e) {
                if (e.key === 'Enter') {
                    e.preventDefault();
                    clearTimeout(searchDebounceTimer);
                    fetchHistoryDynamically();
                }
            });
        }

        if (filterForm) {
            filterForm.addEventListener('submit', function(e) {
                e.preventDefault();
                clearTimeout(searchDebounceTimer);
                fetchHistoryDynamically();
            });
        }

        if (clearBtn) {
            clearBtn.addEventListener('click', function() {
                if (!searchInput) return;
                searchInput.value = '';
                toggleSearchClear(searchInput);
                clearTimeout(searchDebounceTimer);
                fetchHistoryDynamically();
            });
        }

        function resetFilters() {
            if (searchInput) {
                searchInput.value = '';
                toggleSearchClear(searchInput);
            }

            if (filterForm) {
                filterForm.querySelectorAll('input[name="format[]"]').forEach(function(el) {
                    el.checked = false;
                });

                filterForm.querySelectorAll('input[name="jenis[]"]').forEach(function(el) {
                    el.checked = false;
                });

                filterForm.querySelectorAll('input[name="date_range"]').forEach(function(el) {
                    el.checked = false;
                });

                const defaultSort = filterForm.querySelector('input[name="sort"][value="terbaru"]');
                if (defaultSort) defaultSort.checked = true;
            }

            try {
                sessionStorage.removeItem('export_history_filter_state');
            } catch(e) {}

            clearTimeout(searchDebounceTimer);
            closeFilterPopover();
            fetchHistoryDynamically();
        }

        function toggleFilterPopover(e) {
            if (e) {
                e.preventDefault();
                e.stopPropagation();
            }
            const menu = document.getElementById('filter-dropdown-menu');
            if (!menu) return;
            const isShown = menu.style.display === 'block';
            menu.style.display = isShown ? 'none' : 'block';
        }

        function closeFilterPopover() {
            const menu = document.getElementById('filter-dropdown-menu');
            if (menu) menu.style.display = 'none';
        }

        document.addEventListener('click', function(e) {
            const container = document.getElementById('filter-dropdown-container');
            if (container && !container.contains(e.target)) {
                closeFilterPopover();
            }
        });

        window.toggleFilterPopover = toggleFilterPopover;
        window.closeFilterPopover = closeFilterPopover;

        if (resetFilterBtn) {
            resetFilterBtn.addEventListener('click', resetFilters);
        }

        function getNavigationType() {
            try {
                const entries = performance.getEntriesByType('navigation');
                if (entries && entries.length > 0) {
                    return entries[0].type;
                }
                if (window.performance && window.performance.navigation) {
                    const t = window.performance.navigation.type;
                    if (t === 1) return 'reload';
                    if (t === 2) return 'back_forward';
                }
            } catch (e) {}
            return 'navigate';
        }

        function restoreFilterState() {
            const navType = getNavigationType();

            @if(session('success'))
            try {
                sessionStorage.removeItem('export_history_filter_state');
            } catch(e) {}
            @endif

            let isReturning = false;
            try {
                if (sessionStorage.getItem('export_history_returning') === '1') {
                    isReturning = true;
                    sessionStorage.removeItem('export_history_returning');
                }
            } catch(e) {}

            const ref = document.referrer || '';
            if (ref.includes('/export-data/history') || ref.includes('/export-data')) {
                isReturning = true;
            }
            if (navType === 'back_forward') {
                isReturning = true;
            }

            if (navType === 'reload' && !isReturning) {
                try {
                    sessionStorage.removeItem('export_history_filter_state');
                } catch(e) {}
                if (window.location.search) {
                    history.replaceState(null, '', window.location.pathname);
                }
                if (searchInput) {
                    searchInput.value = '';
                    toggleSearchClear(searchInput);
                }
                if (filterForm) {
                    filterForm.querySelectorAll('input[name="format[]"]').forEach(el => el.checked = false);
                    filterForm.querySelectorAll('input[name="jenis[]"]').forEach(el => el.checked = false);
                    filterForm.querySelectorAll('input[name="date_range"]').forEach(el => el.checked = false);
                    const defaultSort = filterForm.querySelector('input[name="sort"][value="terbaru"]');
                    if (defaultSort) defaultSort.checked = true;
                }
                const perPageSelect = document.getElementById('perPageSelect');
                if (perPageSelect) perPageSelect.value = '10';

                fetchHistoryDynamically(true, 1);
                return;
            }

            if (!isReturning && navType === 'navigate' && !window.location.search) {
                try {
                    sessionStorage.removeItem('export_history_filter_state');
                } catch(e) {}
                return;
            }

            let currentSearch = window.location.search;
            let hasUrlQuery = (currentSearch && currentSearch.length > 1);

            if (!hasUrlQuery) {
                try {
                    const savedState = sessionStorage.getItem('export_history_filter_state');
                    if (savedState && savedState.length > 0) {
                        currentSearch = '?' + savedState;
                        history.replaceState(null, '', window.location.pathname + currentSearch);
                        hasUrlQuery = true;
                    }
                } catch(e) {}
            }

            if (!hasUrlQuery) return;

            const params = new URLSearchParams(currentSearch);
            const q = params.get('search') || '';
            if (searchInput && q) {
                searchInput.value = q;
                toggleSearchClear(searchInput);
            }

            if (filterForm) {
                filterForm.querySelectorAll('input[name="format[]"]').forEach(el => el.checked = false);
                params.getAll('format[]').forEach(v => {
                    const cb = filterForm.querySelector(`input[name="format[]"][value="${v}"]`);
                    if (cb) cb.checked = true;
                });

                filterForm.querySelectorAll('input[name="jenis[]"]').forEach(el => el.checked = false);
                params.getAll('jenis[]').forEach(v => {
                    const cb = filterForm.querySelector(`input[name="jenis[]"][value="${v}"]`);
                    if (cb) cb.checked = true;
                });

                const dateRange = params.get('date_range');
                filterForm.querySelectorAll('input[name="date_range"]').forEach(el => el.checked = false);
                if (dateRange) {
                    const rb = filterForm.querySelector(`input[name="date_range"][value="${dateRange}"]`);
                    if (rb) rb.checked = true;
                }

                const sortVal = params.get('sort') || 'terbaru';
                const sb = filterForm.querySelector(`input[name="sort"][value="${sortVal}"]`);
                if (sb) sb.checked = true;
            }

            const perPage = params.get('per_page') || '10';
            const perPageSelect = document.getElementById('perPageSelect');
            if (perPageSelect) perPageSelect.value = perPage;

            const page = parseInt(params.get('page') || '1', 10);
            fetchHistoryDynamically(true, page);
        }

        (function() {
            restoreFilterState();
        })();

        function confirmDeleteExportHistory(logId) {
            const submitAction = function () {
                const form = document.getElementById('deleteExportHistoryForm' + logId);
                if (form) form.submit();
            };

            if (typeof AppModal !== 'undefined') {
                AppModal.confirmDelete('riwayat export ini', submitAction);
                return;
            }

            if (confirm('Apakah Anda yakin ingin menghapus riwayat export ini?')) {
                submitAction();
            }
        }

        function confirmDeleteAllExportHistory() {
            const submitAction = function () {
                const form = document.getElementById('deleteAllExportHistoryForm');
                if (form) form.submit();
            };

            if (typeof AppModal !== 'undefined') {
                AppModal.show({
                    type: 'danger',
                    title: 'Hapus Semua Riwayat Export',
                    message: 'Apakah Anda yakin ingin menghapus semua data riwayat export? Aksi ini tidak dapat dibatalkan.',
                    confirmText: 'Hapus Semua',
                    cancelText: 'Batal',
                    showCancel: true,
                    onConfirm: submitAction
                });
                return;
            }

            if (confirm('Apakah Anda yakin ingin menghapus semua data riwayat export?')) {
                submitAction();
            }
        }

        window.confirmDeleteExportHistory = confirmDeleteExportHistory;
        window.confirmDeleteAllExportHistory = confirmDeleteAllExportHistory;

        const historyAutoRefreshMs = 15000;
        setInterval(function () {
            if (document.hidden) return;
            if (document.querySelector('.modal.show, [id*="modal"][style*="display: flex"], [id*="modal"][style*="display: block"]')) return;
            fetchHistoryDynamically(true);
        }, historyAutoRefreshMs);
    </script>
</body>
</html>
