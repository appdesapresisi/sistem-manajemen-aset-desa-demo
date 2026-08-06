<!DOCTYPE html>
<html lang="en">
    <head>
        @include('layouts.head-page-meta', ['title' => 'Riwayat Export - Sistem Manajemen Aset Desa Demo'])
        @include('layouts.head-css')
        <style>
            /* Gradient Animation */
            @keyframes gradientShift {
                0% { background-position: 0% 50%; }
                50% { background-position: 100% 50%; }
                100% { background-position: 0% 50%; }
            }
            
            /* Page Header Card */
            .page-header-card {
                background: linear-gradient(135deg, #064635 0%, #1c5240 50%, #0b2d21 100%);
                background-size: 200% 200%;
                animation: gradientShift 8s ease infinite;
                border-radius: 20px;
                padding: 32px 40px;
                margin-bottom: 28px;
                position: relative;
                overflow: hidden;
            }
            .page-header-card::before {
                content: '';
                position: absolute;
                top: -50%;
                right: -20%;
                width: 400px;
                height: 400px;
                background: radial-gradient(circle, rgba(255,255,255,0.1) 0%, transparent 60%);
                pointer-events: none;
            }
            .page-header-card h1 {
                color: #fff;
                font-size: 28px;
                font-weight: 700;
                margin: 0 0 8px 0;
                position: relative;
                z-index: 1;
            }
            .page-header-card p {
                color: rgba(255,255,255,0.8);
                font-size: 15px;
                margin: 0;
                position: relative;
                z-index: 1;
            }

            /* Data Card */
            .data-card {
                background: #fff;
                border-radius: 20px;
                box-shadow: 0 4px 24px rgba(0,0,0,0.06);
                border: 1px solid rgba(0,0,0,0.04);
                overflow: visible;
            }
            .data-card-header {
                padding: 24px 28px;
                border-bottom: 1px solid #f3f4f6;
                display: flex;
                align-items: center;
                justify-content: space-between;
                flex-wrap: wrap;
                gap: 20px;
                position: relative;
                z-index: 1000;
            }
            .data-card-title {
                display: flex;
                align-items: center;
                gap: 16px;
            }
            .data-card-icon {
                width: 48px;
                height: 48px;
                background: #519259 !important;
                border-radius: 12px;
                display: flex;
                align-items: center;
                justify-content: center;
            }
            .data-card-icon svg,
            .data-card-icon i { 
                width: 22px; 
                height: 22px; 
                color: #fff !important; 
                stroke: #fff !important; 
            }
            .data-card-title h5 {
                font-size: 18px;
                font-weight: 600;
                color: #064635 !important;
                margin: 0;
            }
            .data-card-title span {
                font-size: 13px;
                color: #9ca3af;
            }
            .controls-bar {
                display: flex;
                align-items: center;
                gap: 12px;
                flex-wrap: wrap;
                position: relative;
                z-index: 1001;
            }
            .search-box {
                display: flex;
                align-items: center;
                background: #ffffff;
                border-radius: 12px;
                padding: 0 16px;
                border: 1px solid #d1d5db;
                transition: all 0.2s;
                flex: 1;
                min-width: 250px;
            }
            .search-box:focus-within {
                border-color: #064635;
                box-shadow: 0 0 0 3px rgba(6, 70, 53,0.1);
            }
            .search-box input {
                border: none;
                background: transparent;
                padding: 12px 0;
                font-size: 14px;
                width: 100%;
                outline: none;
            }
            .search-box button {
                border: none;
                background: transparent;
                padding: 0;
                cursor: pointer;
                color: #9ca3af;
                display: inline-flex;
                align-items: center;
                justify-content: center;
            }
            .search-box button i { width: 18px; height: 18px; }

            .filter-btn {
                width: 48px;
                height: 48px;
                border: 1px solid #d1d5db;
                background: #ffffff;
                border-radius: 12px;
                color: #4b5563;
                display: inline-flex;
                align-items: center;
                justify-content: center;
                cursor: pointer;
                transition: all 0.2s;
            }
            .filter-btn:hover {
                border-color: #064635;
                color: #064635;
                background: #f3f4f6;
            }
            .filter-btn i { width: 18px; height: 18px; }

            .history-delete-form {
                margin: 0;
            }
            .controls-wrapper {
                display: flex;
                align-items: center;
                gap: 12px;
                flex-wrap: wrap;
            }
            .btn-delete-history-all {
                height: 48px;
                padding: 0 16px;
                border: 1px solid #fca5a5;
                background: #fee2e2;
                color: #dc2626;
                border-radius: 12px;
                font-size: 12px;
                font-weight: 600;
                display: inline-flex;
                align-items: center;
                gap: 6px;
                cursor: pointer;
                white-space: nowrap;
                transition: all 0.2s;
                box-sizing: border-box;
            }
            .btn-delete-history-all:hover {
                background: #fecaca;
            }
            .btn-delete-history-row {
                padding: 4px;
                background: transparent;
                color: #dc2626;
                border: none;
                border-radius: 6px;
                display: inline-flex;
                align-items: center;
                justify-content: center;
                cursor: pointer;
                transition: color 0.15s ease;
            }
            .btn-delete-history-row:hover {
                background: transparent;
                color: #b91c1c;
            }
            .btn-delete-history-all i,
            .btn-delete-history-all svg {
                width: 14px;
                height: 14px;
            }
            .btn-delete-history-row i,
            .btn-delete-history-row svg {
                width: 16px;
                height: 16px;
            }

            .filter-menu {
                min-width: 320px;
                z-index: 9999;
            }

            .controls-bar .dropdown {
                position: relative;
                z-index: 9999;
            }

            @media (max-width: 768px) {
                .filter-menu {
                    min-width: min(320px, calc(100vw - 48px));
                }
            }
            .filter-group-title {
                font-size: 12px;
                font-weight: 700;
                color: #064635;
                margin-bottom: 8px;
            }
            .filter-options {
                display: grid;
                gap: 8px;
            }
            .filter-option {
                display: flex;
                align-items: center;
                gap: 8px;
                font-size: 14px;
                color: #6b7280;
                cursor: pointer;
            }
            .filter-option input {
                accent-color: #064635;
                cursor: pointer;
            }
            .filter-actions {
                padding-top: 12px;
                border-top: 1px solid #e5e7eb;
                margin-top: 12px;
            }
            .filter-actions .btn-apply,
            .filter-actions .btn-reset {
                display: block;
                width: 100%;
                border-radius: 8px;
                font-weight: 600;
                text-align: center;
                border: 1px solid transparent;
                text-decoration: none;
                cursor: pointer;
                transition: all 0.2s;
            }
            .filter-actions .btn-apply {
                padding: 10px 16px;
                background: #064635;
                color: #fff;
                border-color: #064635;
                font-size: 14px;
            }
            .filter-actions .btn-reset {
                margin-top: 8px;
                padding: 8px 16px;
                background: transparent;
                color: #6b7280;
                border-color: #d1d5db;
                font-size: 13px;
            }
            .filter-actions .btn-apply:hover {
                background: #2d3748;
                border-color: #2d3748;
            }
            .filter-actions .btn-reset:hover {
                background: #f3f4f6;
            }

            .back-button {
                display: inline-flex;
                align-items: center;
                gap: 8px;
                padding: 10px 18px;
                background: linear-gradient(135deg, #064635 0%, #1c5240 100%);
                color: #fff;
                border: none;
                border-radius: 10px;
                font-size: 13px;
                font-weight: 600;
                cursor: pointer;
                transition: all 0.2s;
                text-decoration: none;
            }
            .back-button:hover {
                transform: translateY(-2px);
                box-shadow: 0 4px 12px rgba(6, 70, 53,0.3);
                color: #fff;
            }
            .back-button i { width: 16px; height: 16px; }

            /* Modern Table */
            .data-card-body { padding: 0; }
            .modern-table {
                width: 100%;
                border-collapse: collapse;
            }
            .modern-table thead th {
                background: #f4faf5;
                padding: 16px 20px;
                text-align: left;
                font-size: 11px;
                font-weight: 700;
                color: #064635;
                text-transform: uppercase;
                letter-spacing: 0.5px;
                border-bottom: 1px solid #e6efe9;
            }
            .modern-table tbody tr {
                transition: all 0.2s;
                border-bottom: 1px solid #f3f4f6;
            }
            .modern-table tbody tr:nth-child(even) {
                background: #f4faf5;
            }
            .modern-table tbody tr:hover {
                background: #e6efe9;
            }
            .modern-table tbody td {
                padding: 16px 20px;
                font-size: 13px;
                color: #374151;
                vertical-align: middle;
            }
            .modern-table tbody tr:last-child td {
                border-bottom: none;
            }

            /* User Info */
            .user-info {
                display: flex;
                align-items: flex-start;
                gap: 12px;
                text-align: left;
            }
            /* .user-avatar removed */
            .user-name { 
                font-weight: 600; 
                color: #1f2937;
            }
            .user-date { 
                font-size: 11px; 
                color: #9ca3af; 
            }

            /* Badge Styles */
            .jenis-name { 
                font-weight: 600; 
                color: #1f2937; 
            }
            .jenis-name.deleted {
                color: #dc2626;
            }

            .format-badge {
                display: inline;
                padding: 0;
                border-radius: 0;
                font-size: 12px;
                font-weight: 600;
                text-transform: uppercase;
            }
            .format-badge.excel { color: #16a34a; }
            .format-badge.pdf { color: #dc2626; }

            .ttd-badge {
                display: inline;
                padding: 0;
                border-radius: 0;
                font-size: 12px;
                font-weight: 500;
                color: #111827;
                background: transparent;
            }
            .ttd-badge.ada,
            .ttd-badge.tidak {
                color: #111827;
                background: transparent;
            }

            /* Pagination */
            .pagination-bar {
                display: grid;
                grid-template-columns: 1fr auto auto 1fr;
                align-items: center;
                padding: 20px 24px;
                border-top: 1px solid #f3f4f6;
                gap: 16px;
            }
            .pagination-info { font-size: 14px; color: #6b7280; }
            .pagination-info strong { color: #064635; font-weight: 600; }
            .pagination-info:last-child { text-align: right; }
            .pagination-nav {
                display: flex;
                align-items: center;
                justify-content: center;
                gap: 6px;
            }
            .page-btn {
                min-width: 36px;
                height: 36px;
                padding: 0 10px;
                border: none;
                border-radius: 10px;
                background: #f3f4f6;
                color: #6b7280;
                font-size: 14px;
                font-weight: 500;
                display: flex;
                align-items: center;
                justify-content: center;
                cursor: pointer;
                transition: all 0.2s;
                text-decoration: none;
            }
            .page-btn:hover { background: #e5e7eb; color: #374151; }
            .page-btn.active {
                background: linear-gradient(135deg, #064635 0%, #1c5240 100%);
                color: #fff;
            }
            .page-btn i { width: 16px; height: 16px; }
            .page-jump-wrap { display: flex; align-items: center; gap: 6px; margin-left: 6px; }
            .page-jump-wrap label { font-size: 13px; color: #6b7280; white-space: nowrap; }
            .page-jump-input {
                width: 56px;
                height: 34px;
                border: 1px solid #d1d5db;
                border-radius: 6px;
                text-align: center;
                font-size: 13px;
                color: #374151;
                outline: none;
                transition: border-color .15s;
            }
            .page-jump-input:focus { border-color: #064635; box-shadow: 0 0 0 2px rgba(6, 70, 53,.15); }

            @media (max-width: 575px) {
                .pagination-bar {
                    grid-template-columns: 1fr;
                    align-items: center;
                    gap: 10px;
                    padding: 14px 16px;
                }
                .pagination-nav {
                    flex-wrap: wrap;
                    justify-content: center;
                }
                .page-jump-wrap {
                    margin-left: 0;
                    justify-content: center;
                }
                .pagination-info {
                    text-align: center;
                }
                .pagination-info:last-child { text-align: center; }
            }

            @media (max-width: 768px) {
                .controls-wrapper {
                    flex-direction: column;
                    align-items: stretch;
                    width: 100%;
                }
                .data-card-header { flex-direction: column; align-items: stretch; }
                .controls-bar {
                    flex-direction: column;
                    align-items: flex-start;
                    justify-content: flex-start;
                    width: 100%;
                }
                .history-delete-form {
                    width: 100%;
                }
                .btn-delete-history-all {
                    width: 100%;
                    justify-content: center;
                }
                .search-box {
                    min-width: unset;
                    flex: unset;
                    width: 100%;
                    max-width: 320px;
                }
                .controls-bar .dropdown {
                    align-self: flex-start;
                }
                .filter-select { width: 100%; }
                .modern-table { font-size: 12px; }
                .modern-table thead th { padding: 12px 16px; font-size: 10px; }
                .modern-table tbody td { padding: 12px 16px; font-size: 12px; }
            }
        </style>
        <style>
            /* Page-specific: hide topbar search icon/dropdown for this page only */
            .me-auto.pc-mob-drp i[data-feather="search"],
            .drp-search {
                display: none !important;
            }
        </style>
        <style>
            .me-auto.pc-mob-drp .pc-head-link.dropdown-toggle svg.feather,
            .me-auto.pc-mob-drp .pc-head-link.dropdown-toggle i[data-feather="search"] {
                display: none !important;
            }
        </style>
    </head>

    <body>
        @include('layouts.notification')
        @include('layouts.modals')
        @include('layouts.layout-vertical')

        <div class="pc-container">
            <div class="pc-content">
                @include('layouts.breadcrumb', ['breadcrumbItemHome' => 'Inventaris', 'breadcrumbItem' => 'Export Data', 'breadcrumbItemUrl' => route('export-data.index'), 'breadcrumbItemActive' => 'Riwayat Export'])
 
                <!-- Page Header -->
                <div class="page-header-card">
                    <h1>Riwayat Export Jenis Inventaris</h1>
                    <p>Daftar lengkap export yang telah dilakukan untuk setiap jenis inventaris desa.</p>
                </div>

                {{-- Back button removed — use browser back to return to origin (dashboard or export index) --}}

                <!-- Data Card -->
                <div class="data-card">
                    <div class="data-card-header">
                        <div class="data-card-title">
                            <div>
                                <h5>Riwayat Export</h5>
                                <span id="history-total">Total {{ $logs->total() }} export tercatat</span>
                            </div>
                        </div>
                        <div class="controls-wrapper">
                            <form method="GET" action="{{ route('export-data.history') }}" class="controls-bar" id="filter-form" style="margin:0;">
                                <div class="search-box">
                                <input type="text" name="search" id="searchInput" value="{{ request('search') }}" placeholder="Cari user, jenis, atau file..." />
                                <button type="submit" id="search-icon-btn" @if(request('search')) style="display:none" @endif><i data-feather="search"></i></button>
                                <button type="button" id="clear-search-btn" style="color:#ef4444;{{ request('search') ? '' : 'display:none;' }}"><i data-feather="x"></i></button>
                            </div>
                            <div class="dropdown" style="position:relative;">
                                <button class="filter-btn" type="button" data-pc-toggle="dropdown" title="Filter">
                                    <i data-feather="sliders"></i>
                                </button>
                                @php
                                    $selectedFormats = request()->has('format')
                                        ? array_values(array_filter((array) request('format')))
                                        : [];

                                    $selectedJenis = request()->has('jenis')
                                        ? array_values(array_filter((array) request('jenis')))
                                        : [];
                                @endphp
                                <div class="dropdown-menu dropdown-menu-end py-3 px-4 filter-menu" style="min-width: 240px;">
                                    <div class="filter-group-title">Format</div>
                                    <div class="filter-options">
                                        <label class="filter-option">
                                            <input type="checkbox" name="format[]" value="excel" {{ in_array('excel', $selectedFormats, true) ? 'checked' : '' }}>
                                            Excel
                                        </label>
                                        <label class="filter-option">
                                            <input type="checkbox" name="format[]" value="pdf" {{ in_array('pdf', $selectedFormats, true) ? 'checked' : '' }}>
                                            PDF
                                        </label>
                                    </div>

                                    <hr style="border-color:#f3f4f6; margin:12px 0;">

                                    <div class="filter-group-title">Jenis Inventaris</div>
                                    <div class="filter-options" style="max-height: 180px; overflow-y: auto; padding-right: 2px;">
                                        <label class="filter-option">
                                            <input type="checkbox" name="jenis[]" value="all" {{ in_array('all', array_map('strval', $selectedJenis), true) ? 'checked' : '' }}>
                                            Semua Jenis
                                        </label>
                                        @foreach($jenisList as $j)
                                            <label class="filter-option">
                                                <input type="checkbox" name="jenis[]" value="{{ $j->id }}" {{ in_array((string) $j->id, array_map('strval', $selectedJenis), true) ? 'checked' : '' }}>
                                                {{ $j->nama }}
                                            </label>
                                        @endforeach
                                    </div>

                                    <hr style="border-color:#f3f4f6; margin:12px 0;">

                                    <div class="filter-group-title">Rentang Tanggal</div>
                                    <div class="filter-options">
                                        <label class="filter-option">
                                            <input type="radio" name="date_range" value="1d" {{ request('date_range') === '1d' ? 'checked' : '' }}>
                                            1 Hari Terakhir
                                        </label>
                                        <label class="filter-option">
                                            <input type="radio" name="date_range" value="7d" {{ request('date_range') === '7d' ? 'checked' : '' }}>
                                            1 Minggu Terakhir
                                        </label>
                                        <label class="filter-option">
                                            <input type="radio" name="date_range" value="30d" {{ request('date_range') === '30d' ? 'checked' : '' }}>
                                            1 Bulan Terakhir
                                        </label>
                                    </div>

                                    <hr style="border-color:#f3f4f6; margin:12px 0;">

                                    <div class="filter-group-title">Urutkan</div>
                                    <div class="filter-options">
                                        @php $curSort = request('sort', 'terbaru'); @endphp
                                        <label class="filter-option">
                                            <input type="radio" name="sort" value="terbaru" {{ $curSort === 'terbaru' ? 'checked' : '' }}>
                                            Terbaru
                                        </label>
                                        <label class="filter-option">
                                            <input type="radio" name="sort" value="terlama" {{ $curSort === 'terlama' ? 'checked' : '' }}>
                                            Terlama
                                        </label>
                                        <label class="filter-option">
                                            <input type="radio" name="sort" value="user_asc" {{ $curSort === 'user_asc' ? 'checked' : '' }}>
                                            User A-Z
                                        </label>
                                        <label class="filter-option">
                                            <input type="radio" name="sort" value="user_desc" {{ $curSort === 'user_desc' ? 'checked' : '' }}>
                                            User Z-A
                                        </label>
                                    </div>

                                    <div class="filter-actions">
                                        <button type="submit" class="btn-apply" onclick="event.stopPropagation();">Terapkan Filter</button>
                                        <button type="button" id="reset-filter-btn" class="btn-reset">Reset Filter</button>
                                    </div>
                                </div>
                            </div>
                            </form>
 
                            @if(($canDelete ?? false) && $logs->total() > 0)
                            <form id="deleteAllExportHistoryForm" method="POST" action="{{ route('export-data.history.destroy-all') }}" class="history-delete-form" style="margin:0;">
                                @csrf
                                <button type="button" class="btn-delete-history-all" onclick="confirmDeleteAllExportHistory()">
                                    <i data-feather="trash-2"></i>
                                    Hapus semua data
                                </button>
                            </form>
                            @endif
                        </div>
                    </div>
                    <div class="data-card-body">
                        <table class="modern-table">
                            <thead>
                                <tr>
                                    <th style="width:60px; text-align:center;">No</th>
                                    <th style="width: 200px;">User & Waktu</th>
                                    <th style="width: 340px;">Jenis Inventaris</th>
                                    <th style="width: 240px; text-align:center;">Format</th>
                                    <th>Nama File (Audit)</th>
                                    <th style="width: 200px;">Pejabat</th>
                                    @if($canDelete ?? false)
                                    <th style="width: 90px; text-align:center;">Aksi</th>
                                    @endif
                                </tr>
                            </thead>
                            <tbody id="history-tbody">
                                @forelse($logs as $log)
                                <tr>
                                    <td style="text-align:center; font-weight:600;">{{ $logs->firstItem() + $loop->index }}</td>
                                    <td>
                                        <div class="user-info" style="display:flex;flex-direction:column;gap:4px;align-items:flex-start;text-align:left;">
                                            <div class="user-name">{{ optional($log->user)->nama ?? 'Unknown' }}</div>
                                            <div class="user-date">{{ $log->created_at ? $log->created_at->format('d/m/Y H:i') : '-' }}</div>
                                        </div>
                                    </td>
                                    <td>
                                        @if(is_null($log->jenis_inventaris_id))
                                            @php
                                                $logFilters = is_array($log->filters) ? $log->filters : [];
                                                $virtualJenis = $logFilters['virtual_export_as'] ?? 'Semua Jenis';
                                            @endphp
                                            <span class="jenis-name">{{ $virtualJenis }}</span>
                                        @elseif($log->jenisInventaris)
                                            <span class="jenis-name">{{ $log->jenisInventaris->nama }}</span>
                                        @else
                                            <span class="jenis-name deleted">jenis inventaris telah terhapus</span>
                                        @endif
                                    </td>
                                    <td style="text-align:center;"><span class="format-badge {{ strtolower($log->tipe_export ?? 'excel') }}">{{ strtoupper($log->tipe_export ?? 'EXCEL') }}</span></td>
                                    <td>
                                        @if($log->nama_file)
                                        <span style="color:#374151; font-size:12px; word-break:break-all;">{{ $log->nama_file }}</span>
                                        @else
                                        <span style="color: #9ca3af; font-style: italic;">File tidak tersedia</span>
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
                                            <span class="ttd-badge tidak">Tanpa Tanda Tangan</span>
                                        @elseif($overrideNames->count() > 0)
                                            @php
                                                $display = $overrideNames->take(2)->implode(', ');
                                                if ($overrideNames->count() > 2) {
                                                    $display .= ' +' . ($overrideNames->count() - 2) . ' lainnya';
                                                }
                                            @endphp
                                            <span class="ttd-badge ada">{{ $display }}</span>
                                        @elseif($log->pejabat)
                                            <span class="ttd-badge ada">{{ $log->pejabat->nama ?? 'Pejabat tidak diketahui' }}</span>
                                        @else
                                            <span class="ttd-badge tidak">Tidak ada TTD</span>
                                        @endif
                                    </td>
                                    @if($canDelete ?? false)
                                    <td style="text-align:center;">
                                        <form id="deleteExportHistoryForm{{ $log->id }}" method="POST" action="{{ route('export-data.history.destroy', $log->id) }}" style="margin:0;display:inline-flex;">
                                            @csrf
                                            @method('DELETE')
                                            <button type="button" class="btn-delete-history-row" title="Hapus riwayat export" onclick="confirmDeleteExportHistory({{ $log->id }})">
                                                <i data-feather="trash-2"></i>
                                            </button>
                                        </form>
                                    </td>
                                    @endif
                                </tr>
                                @empty
                                <tr>
                                    <td colspan="{{ ($canDelete ?? false) ? 7 : 6 }}" style="text-align: center; padding: 40px; color: #9ca3af;">
                                        <div style="display: flex; flex-direction: column; align-items: center; justify-content: center; gap: 8px;">
                                            <i data-feather="inbox" style="width: 48px; height: 48px; opacity: 0.5;"></i>
                                            <div style="font-weight: 500; margin: 0;">Belum ada riwayat export</div>
                                            <div style="font-size: 12px; color: #9ca3af;">Mulai export data untuk melihat riwayat</div>
                                        </div>
                                    </td>
                                </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                    <!-- Pagination -->
                    @if($logs->total() > 0)
                    <div class="pagination-bar">
                        <div class="pagination-info">
                            Menampilkan <strong>{{ $logs->firstItem() ?? 0 }}–{{ $logs->lastItem() ?? 0 }}</strong> dari <strong>{{ $logs->total() }}</strong> data
                        </div>
                        <nav class="pagination-nav">
                            @if ($logs->onFirstPage())
                                <span class="page-btn disabled" title="Previous"><i data-feather="chevron-left"></i></span>
                            @else
                                <a href="{{ $logs->previousPageUrl() }}" class="page-btn" title="Previous"><i data-feather="chevron-left"></i></a>
                            @endif

                            @php
                                $currentPage  = $logs->currentPage();
                                $lastPage     = $logs->lastPage();
                                $windowStart  = max(1, $currentPage - 2);
                                $windowEnd    = min($lastPage, $currentPage + 2);
                                // Pastikan window selalu 5 kotak (bila total halaman >= 5)
                                if (($windowEnd - $windowStart + 1) < 5) {
                                    if ($windowStart === 1) {
                                        $windowEnd = min($lastPage, 5);
                                    } else {
                                        $windowStart = max(1, $windowEnd - 4);
                                    }
                                }
                                $pageUrls = $logs->getUrlRange($windowStart, $windowEnd);
                            @endphp
                            @foreach ($pageUrls as $page => $url)
                                @if ($page == $currentPage)
                                    <span class="page-btn active">{{ $page }}</span>
                                @else
                                    <a href="{{ $url }}" class="page-btn">{{ $page }}</a>
                                @endif
                            @endforeach

                            @if ($logs->hasMorePages())
                                <a href="{{ $logs->nextPageUrl() }}" class="page-btn" title="Next"><i data-feather="chevron-right"></i></a>
                            @else
                                <span class="page-btn disabled" title="Next"><i data-feather="chevron-right"></i></span>
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
                                placeholder="1–{{ $logs->lastPage() }}"
                                title="Masukkan nomor halaman (1–{{ $logs->lastPage() }})"
                            >
                        </div>
                        <div class="pagination-info">Total <strong>{{ $logs->total() }}</strong> data</div>
                    </div>
                    @endif
                </div>
            </div>
        </div>

        @include('layouts.footer-block')
        @include('layouts.footer-js')

        <script>
            if(typeof feather !== 'undefined') feather.replace();

            // Jump-to-page input
            (function () {
                const jumpInput = document.getElementById('pageJumpInput');
                if (!jumpInput) return;

                const lastPage = parseInt(jumpInput.max, 10);

                // Cegah input angka di luar range secara real-time
                jumpInput.addEventListener('input', function () {
                    const raw = this.value;
                    if (raw === '' || raw === '0') return; // biarkan user hapus dulu
                    const val = parseInt(raw, 10);
                    if (!isNaN(val) && val > lastPage) {
                        this.value = ''; // hapus — angka tidak valid
                    }
                });

                jumpInput.addEventListener('keydown', function (e) {
                    if (e.key !== 'Enter') return;
                    const val = parseInt(this.value, 10);
                    if (isNaN(val) || val < 1 || val > lastPage) return; // abaikan Enter jika tidak valid

                    const url = new URL(window.location.href);
                    url.searchParams.set('page', val);
                    window.location.href = url.toString();
                });
            })();

            const searchInput = document.getElementById('searchInput');
            const searchBtn = document.getElementById('search-icon-btn');
            const clearBtn = document.getElementById('clear-search-btn');
            const resetFilterBtn = document.getElementById('reset-filter-btn');
            const filterForm = document.getElementById('filter-form');
            let searchDebounceTimer = null;

            function toggleSearchClear(input) {
                if (!input || !searchBtn || !clearBtn) return;
                searchBtn.style.display = input.value ? 'none' : '';
                clearBtn.style.display = input.value ? '' : 'none';
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

                const newTotal = doc.getElementById('history-total');
                const curTotal = document.getElementById('history-total');
                if (newTotal && curTotal) {
                    curTotal.textContent = newTotal.textContent;
                }

                const curCard = document.querySelector('.data-card');
                const curPagination = curCard ? curCard.querySelector('.pagination-bar') : null;
                const newCard = doc.querySelector('.data-card');
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

                if(typeof feather !== 'undefined') feather.replace();
            }

            function fetchHistoryDynamically(isAutoRefresh = false) {
                if (!filterForm) return;

                const params = buildParamsFromForm();
                if (isAutoRefresh) {
                    const urlParams = new URLSearchParams(window.location.search);
                    const page = urlParams.get('page');
                    if (page) params.set('page', page);
                }
                const actionUrl = filterForm.getAttribute('action') || window.location.pathname;
                const targetUrl = actionUrl + (params.toString() ? ('?' + params.toString()) : '');

                history.replaceState(null, '', targetUrl);

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

            if (resetFilterBtn && filterForm) {
                resetFilterBtn.addEventListener('click', function() {
                    if (searchInput) {
                        searchInput.value = '';
                        toggleSearchClear(searchInput);
                    }

                    filterForm.querySelectorAll('input[name="format[]"]').forEach(function(el) {
                        el.checked = false;
                    });

                    filterForm.querySelectorAll('input[name="jenis[]"]').forEach(function(el) {
                        el.checked = false;
                    });

                    filterForm.querySelectorAll('input[name="date_range"]').forEach(function(el) {
                        el.checked = false;
                    });

                    clearTimeout(searchDebounceTimer);
                    fetchHistoryDynamically();
                });
            }

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
                    AppModal.confirm(
                        'Hapus Semua Riwayat Export',
                        'Apakah Anda yakin ingin menghapus semua data riwayat export? Aksi ini tidak dapat dibatalkan.',
                        submitAction
                    );
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
                fetchHistoryDynamically(true);
            }, historyAutoRefreshMs);
        </script>
    </body>
</html>

