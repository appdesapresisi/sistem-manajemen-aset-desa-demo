<!DOCTYPE html>
<html lang="en">
    <head>
        @include('layouts.head-page-meta', ['title' => 'Manajemen Aset - Sistem Manajemen Aset Desa Demo'])
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

            /* Stats Cards */
            .stats-grid {
                display: grid;
                grid-template-columns: repeat(4, 1fr);
                gap: 20px;
                margin-bottom: 28px;
            }
            @media (max-width: 1200px) { .stats-grid { grid-template-columns: repeat(2, 1fr); } }
            @media (max-width: 640px) { .stats-grid { grid-template-columns: 1fr; } }
            
            .stat-card {
                background: #fff;
                border-radius: 16px;
                padding: 18px 24px;
                display: flex;
                flex-direction: column;
                justify-content: center;
                box-shadow: 0 4px 20px rgba(0,0,0,0.04);
                border: 1px solid rgba(0,0,0,0.04);
                transition: all 0.3s ease;
                position: relative;
                overflow: hidden;
            }
            .stat-card:hover {
                transform: translateY(-4px);
                box-shadow: 0 12px 30px rgba(0,0,0,0.08);
            }
            .stat-card-label {
                font-size: 12px;
                font-weight: 700;
                color: #064635;
                text-transform: uppercase;
                letter-spacing: 0.5px;
                margin-bottom: 4px;
            }
            .stat-card-value {
                font-size: 24px;
                font-weight: 800;
                color: #1f2937;
                line-height: 1.2;
            }
            .kpi-trend-wrapper {
                display: flex;
                align-items: center;
                gap: 6px;
                margin-top: 8px;
            }
            .kpi-trend-pill {
                display: inline-flex;
                align-items: center;
                gap: 3px;
                padding: 2px 7px;
                border-radius: 12px;
                font-size: 11px;
                font-weight: 700;
                line-height: 1.3;
            }
            .kpi-trend-pill.up { background: #d1fae5; color: #047857; }
            .kpi-trend-pill.down { background: #fee2e2; color: #b91c1c; }
            .kpi-trend-pill.neutral { background: #f1f5f9; color: #64748b; }
            .kpi-trend-label { font-size: 11.5px; color: #94a3b8; font-weight: 500; }

            /* Data Card */
            .data-card {
                background: #fff;
                border-radius: 20px;
                box-shadow: 0 4px 24px rgba(0,0,0,0.06);
                border: 1px solid rgba(0,0,0,0.04);
            }
            .data-card-header {
                padding: 24px 28px;
                border-bottom: 1px solid #f3f4f6;
                display: flex;
                align-items: center;
                justify-content: space-between;
                flex-wrap: wrap;
                gap: 20px;
            }
            .data-card-title {
                display: flex;
                align-items: center;
                gap: 16px;
            }
            .data-card-icon {
                width: 48px;
                height: 48px;
                background: linear-gradient(135deg, #064635 0%, #1c5240 100%);
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
                color: #1f2937;
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
            }
            .search-box {
                display: flex;
                align-items: center;
                background: #ffffff;
                border-radius: 12px;
                padding: 0 16px;
                border: 1px solid #d1d5db;
                transition: all 0.2s;
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
                width: 200px;
                outline: none;
            }
            .search-box button {
                border: none;
                background: transparent;
                padding: 0;
                cursor: pointer;
                color: #9ca3af;
            }
            .search-box button i { width: 18px; height: 18px; }
            
            .filter-btn {
                width: 44px;
                height: 44px;
                border-radius: 12px;
                border: 1px solid #d1d5db;
                background: #ffffff;
                display: flex;
                align-items: center;
                justify-content: center;
                cursor: pointer;
                transition: all 0.2s;
                color: #4b5563;
            }
            .filter-btn:hover {
                background: #064635;
                border-color: #064635;
                color: #fff;
            }
            .filter-btn i { width: 18px; height: 18px; }
            
            .add-btn {
                display: flex;
                align-items: center;
                gap: 8px;
                padding: 12px 20px;
                background: linear-gradient(135deg, #064635 0%, #1c5240 100%);
                color: #fff;
                border-radius: 12px;
                font-size: 14px;
                font-weight: 600;
                text-decoration: none;
                transition: all 0.3s;
                box-shadow: 0 4px 12px rgba(6, 70, 53,0.3);
            }
            .add-btn:hover {
                transform: translateY(-2px);
                box-shadow: 0 6px 20px rgba(6, 70, 53,0.4);
                color: #fff;
            }
            .add-btn i { width: 18px; height: 18px; }

            /* Modern Table */
            .data-card-body { padding: 0; }
            .modern-table {
                width: 100%;
                border-collapse: collapse;
            }
            .table-loading-wrap { position: relative; }
            .table-loading-wrap::after {
                content: '';
                display: none;
                position: absolute;
                inset: 0;
                background: rgba(255,255,255,0.7);
                border-radius: 8px;
                z-index: 10;
            }
            .table-loading-wrap.is-loading::after { display: block; }
            .table-loading-wrap.is-loading::before {
                content: '';
                display: block;
                position: absolute;
                top: 50%; left: 50%;
                transform: translate(-50%,-50%);
                width: 36px; height: 36px;
                border: 3px solid #e5e7eb;
                border-top-color: #064635;
                border-radius: 50%;
                animation: tbl-spin 0.7s linear infinite;
                z-index: 11;
            }
            @keyframes tbl-spin { to { transform: translate(-50%,-50%) rotate(360deg); } }
            .modern-table thead th {
                background: #f4faf5;
                padding: 16px 20px;
                text-align: left;
                font-size: 12px;
                font-weight: 600;
                color: #064635;
                text-transform: uppercase;
                letter-spacing: 0.5px;
                border-bottom: 1px solid #e6efe9;
            }
            .modern-table tbody tr {
                transition: all 0.2s;
            }
            .modern-table tbody tr:nth-child(even) {
                background: #f4faf5;
            }
            .modern-table tbody tr:hover {
                background: #e6efe9;
            }
            .modern-table tbody td {
                padding: 16px 20px;
                border-bottom: 1px solid #f3f4f6;
                font-size: 14px;
                color: #374151;
                vertical-align: middle;
            }
            .modern-table tbody tr:last-child td {
                border-bottom: none;
            }

            /* Aset Cell */
            .aset-cell {
                display: flex;
                align-items: center;
                gap: 14px;
            }
            .aset-icon {
                width: 44px;
                height: 44px;
                border-radius: 12px;
                display: flex;
                align-items: center;
                justify-content: center;
                flex-shrink: 0;
            }
            .aset-icon i { width: 20px; height: 20px; color: #fff; }
            .aset-info h6 {
                font-size: 14px;
                font-weight: 600;
                color: #1f2937;
                margin: 0 0 2px 0;
            }
            .aset-info span {
                font-size: 12px;
                color: #9ca3af;
            }

            /* Jenis Cell & Icon - SAMA dengan jenis-inventaris */
            .jenis-cell {
                display: flex;
                align-items: center;
                gap: 12px;
            }
            .jenis-icon-inline {
                width: 40px;
                height: 40px;
                border-radius: 12px;
                display: flex;
                align-items: center;
                justify-content: center;
                flex-shrink: 0;
            }
            .jenis-icon-inline i { 
                width: 20px; 
                height: 20px; 
                color: #fff;
                stroke: #fff;
            }
            .jenis-icon-inline svg {
                width: 20px;
                height: 20px;
                color: #fff;
                stroke: #fff;
            }

            /* Jenis Badge (old - untuk fallback) */
            .jenis-badge {
                display: inline-flex;
                align-items: center;
                gap: 6px;
                padding: 6px 12px;
                border-radius: 20px;
                font-size: 12px;
                font-weight: 600;
            }
            .jenis-badge.kendaraan {
                background: linear-gradient(135deg, #d1fae5 0%, #a7f3d0 100%);
                color: #059669;
            }
            .jenis-badge.peralatan {
                background: linear-gradient(135deg, #fef3c7 0%, #fde68a 100%);
                color: #b45309;
            }
            .jenis-badge.bangunan {
                background: linear-gradient(135deg, #dbeafe 0%, #bfdbfe 100%);
                color: #2563eb;
            }
            .jenis-badge.tanah {
                background: linear-gradient(135deg, #fce7f3 0%, #fbcfe8 100%);
                color: #be185d;
            }
            .jenis-badge.infrastruktur {
                background: linear-gradient(135deg, #e9d5ff 0%, #d8b4fe 100%);
                color: #7c3aed;
            }

            /* Code Badge */
            .code-badge {
                font-size: 13px;
                font-weight: 600;
                color: #374151;
            }

            /* Action Buttons */
            .action-btns {
                display: flex;
                align-items: center;
                gap: 8px;
            }
            .action-btn {
                width: 36px;
                height: 36px;
                border-radius: 10px;
                border: none;
                display: flex;
                align-items: center;
                justify-content: center;
                cursor: pointer;
                transition: all 0.2s;
            }
            .action-btn i { width: 16px; height: 16px; }
            .action-btn.view { background: #dbeafe; color: #2563eb; }
            .action-btn.view:hover { background: #3b82f6; color: #fff; }
            .action-btn.edit { background: #fef3c7; color: #d97706; }
            .action-btn.edit:hover { background: #f59e0b; color: #fff; }
            .action-btn.copy { background: #f0fdf4; color: #059669; }
            .action-btn.copy:hover { background: #10b981; color: #fff; }
            .action-btn.delete { background: #fee2e2; color: #dc2626; }
            .action-btn.delete:hover { background: #ef4444; color: #fff; }

            /* Pagination */
            .pagination-bar {
                display: flex;
                align-items: center;
                justify-content: space-between;
                padding: 16px 24px;
                border-top: 1px solid #f3f4f6;
                gap: 16px;
                flex-wrap: wrap;
            }
            .pagination-left {
                display: flex;
                align-items: center;
                gap: 12px;
            }
            .page-size-wrap { display: flex; align-items: center; gap: 8px; font-size: 14px; color: #6b7280; }
            .per-page-select {
                padding: 6px 32px 6px 12px;
                font-size: 14px;
                font-weight: 500;
                color: #1f2937;
                background-color: #ffffff;
                border: 1px solid #d1d5db;
                border-radius: 8px;
                cursor: pointer;
                outline: none;
                transition: all 0.2s ease;
                appearance: none;
                background-image: url("data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' fill='none' viewBox='0 0 24 24' stroke='%236b7280'%3E%3Cpath stroke-linecap='round' stroke-linejoin='round' stroke-width='2' d='M19 9l-7 7-7-7'%3E%3C/path%3E%3C/svg%3E");
                background-repeat: no-repeat;
                background-position: right 10px center;
                background-size: 14px;
            }
            .per-page-select:hover { border-color: #9ca3af; }
            .per-page-select:focus { border-color: #064635; }
            .pagination-center {
                display: flex;
                align-items: center;
                gap: 12px;
                justify-content: center;
                flex-wrap: wrap;
            }
            .pagination-right {
                display: flex;
                align-items: center;
                gap: 5px;
                font-size: 14px;
                color: #6b7280;
                white-space: nowrap;
            }
            .pagination-right strong {
                color: #064635; 
                font-weight: 600;
                margin: 0 3px;
            }
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
            .page-jump-input { width: 56px; height: 34px; border: 1px solid #d1d5db; border-radius: 6px; text-align: center; font-size: 13px; color: #374151; outline: none; transition: border-color .15s; }
            .page-jump-input:focus { border-color: #064635; box-shadow: 0 0 0 2px rgba(6, 70, 53,.15); }
            @media (max-width: 575px) {
                .pagination-bar { grid-template-columns: 1fr; align-items: center; gap: 10px; padding: 14px 16px; }
                .pagination-nav { flex-wrap: wrap; justify-content: center; }
                .page-jump-wrap { margin-left: 0; justify-content: center; }
                .pagination-info { text-align: center; }
                .pagination-info:last-child { text-align: center; }
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
            /* Also hide Feather-inserted SVG for the same topbar search dropdown (post-replace)
               but do not hide in-page search buttons (.search-box) */
            .me-auto.pc-mob-drp .pc-head-link.dropdown-toggle svg.feather {
                display: none !important;
            }
            .me-auto.pc-mob-drp .pc-head-link.dropdown-toggle i[data-feather="search"] {
                display: none !important;
            }
        </style>
    </head>

    <body>
        @include('layouts.notification')
        @include('layouts.layout-vertical')

        <div class="pc-container">
            <div class="pc-content">
                @include('layouts.breadcrumb', [
                    'breadcrumbItemHome' => 'Inventaris',
                    'breadcrumbItem' => 'Manajemen Aset',
                    'breadcrumbItemActive' => 'List Aset'
                ])
 
                <!-- Page Header -->
                <div class="page-header-card">
                    {{-- <h1><i data-feather="package" style="width: 32px; height: 32px; display: inline-block; vertical-align: middle; margin-right: 12px;"></i>Manajemen Aset</h1> --}}
                    <h1>Manajemen Aset</h1>
                    <p>Kelola seluruh aset dan inventaris desa dengan sistem yang terintegrasi.</p>
                </div>

                <!-- Stats Cards -->
                <div class="stats-grid">
                    <div class="stat-card">
                        <div class="stat-card-label">TOTAL ASET</div>
                        <div class="stat-card-value">{{ number_format($stats['totalInventaris'] ?? 0, 0, ',', '.') }}</div>
                        <div class="kpi-trend-wrapper">
                            @php $tTot = $stats['trends']['total']['percent'] ?? 0; @endphp
                            @if($tTot > 0)
                                <span class="kpi-trend-pill up">
                                    <svg width="10" height="10" viewBox="0 0 24 24" fill="currentColor"><path d="M12 4l-8 8h16z"/></svg>
                                    +{{ $tTot }}%
                                </span>
                            @elseif($tTot < 0)
                                <span class="kpi-trend-pill down">
                                    <svg width="10" height="10" viewBox="0 0 24 24" fill="currentColor"><path d="M12 20l8-8H4z"/></svg>
                                    {{ $tTot }}%
                                </span>
                            @else
                                <span class="kpi-trend-pill neutral">0%</span>
                            @endif
                            <span class="kpi-trend-label">vs. kemarin</span>
                        </div>
                    </div>

                    <div class="stat-card">
                        <div class="stat-card-label">KONDISI BAIK</div>
                        <div class="stat-card-value">{{ number_format($stats['kondisiBaik'] ?? 0, 0, ',', '.') }}</div>
                        <div class="kpi-trend-wrapper">
                            @php $tBaik = $stats['trends']['baik']['percent'] ?? 0; @endphp
                            @if($tBaik > 0)
                                <span class="kpi-trend-pill up">
                                    <svg width="10" height="10" viewBox="0 0 24 24" fill="currentColor"><path d="M12 4l-8 8h16z"/></svg>
                                    +{{ $tBaik }}%
                                </span>
                            @elseif($tBaik < 0)
                                <span class="kpi-trend-pill down">
                                    <svg width="10" height="10" viewBox="0 0 24 24" fill="currentColor"><path d="M12 20l8-8H4z"/></svg>
                                    {{ $tBaik }}%
                                </span>
                            @else
                                <span class="kpi-trend-pill neutral">0%</span>
                            @endif
                            <span class="kpi-trend-label">vs. kemarin</span>
                        </div>
                    </div>

                    <div class="stat-card">
                        <div class="stat-card-label">RUSAK RINGAN</div>
                        <div class="stat-card-value">{{ number_format($stats['kondisiRusakRingan'] ?? 0, 0, ',', '.') }}</div>
                        <div class="kpi-trend-wrapper">
                            @php $tRr = $stats['trends']['rr']['percent'] ?? 0; @endphp
                            @if($tRr > 0)
                                <span class="kpi-trend-pill up">
                                    <svg width="10" height="10" viewBox="0 0 24 24" fill="currentColor"><path d="M12 4l-8 8h16z"/></svg>
                                    +{{ $tRr }}%
                                </span>
                            @elseif($tRr < 0)
                                <span class="kpi-trend-pill down">
                                    <svg width="10" height="10" viewBox="0 0 24 24" fill="currentColor"><path d="M12 20l8-8H4z"/></svg>
                                    {{ $tRr }}%
                                </span>
                            @else
                                <span class="kpi-trend-pill neutral">0%</span>
                            @endif
                            <span class="kpi-trend-label">vs. kemarin</span>
                        </div>
                    </div>

                    <div class="stat-card">
                        <div class="stat-card-label">RUSAK BERAT</div>
                        <div class="stat-card-value">{{ number_format($stats['kondisiRusakBerat'] ?? 0, 0, ',', '.') }}</div>
                        <div class="kpi-trend-wrapper">
                            @php $tRb = $stats['trends']['rb']['percent'] ?? 0; @endphp
                            @if($tRb > 0)
                                <span class="kpi-trend-pill up">
                                    <svg width="10" height="10" viewBox="0 0 24 24" fill="currentColor"><path d="M12 4l-8 8h16z"/></svg>
                                    +{{ $tRb }}%
                                </span>
                            @elseif($tRb < 0)
                                <span class="kpi-trend-pill down">
                                    <svg width="10" height="10" viewBox="0 0 24 24" fill="currentColor"><path d="M12 20l8-8H4z"/></svg>
                                    {{ $tRb }}%
                                </span>
                            @else
                                <span class="kpi-trend-pill neutral">0%</span>
                            @endif
                            <span class="kpi-trend-label">vs. kemarin</span>
                        </div>
                    </div>
                </div>

                <!-- Data Card -->
                <div class="data-card">
                    <div class="data-card-header">
                        <div class="data-card-title">
                            <div>
                                <h5>Daftar Aset Inventaris</h5>
                                <span>Total {{ $stats['totalInventaris'] }} aset terdaftar</span>
                            </div>
                        </div>
                        <div class="controls-bar">
                            <div class="search-box">
                                <input type="text" id="searchInput" placeholder="Cari aset..." oninput="onSearchInput(this)" onkeypress="if(event.key==='Enter'){doSearch();}" value="{{ request('search') }}" />
                                <button id="search-icon-btn" onclick="doSearch()" @if(request('search')) style="display:none" @endif><i data-feather="search"></i></button>
                                <button id="clear-search-btn" onclick="clearSearch()" style="color:#ef4444;{{ request('search') ? '' : 'display:none;' }}"><i data-feather="x"></i></button>
                            </div>
                            <div class="dropdown" style="position:relative;">
                                <button class="filter-btn" data-pc-toggle="dropdown" title="Filter">
                                    <i data-feather="sliders"></i>
                                </button>
                                <div class="dropdown-menu dropdown-menu-end py-3 px-4" style="min-width: 240px; z-index: 1050;">
                                    <div class="text-sm font-semibold mb-2" style="color: #064635;">Jenis Inventaris</div>
                                    <div class="space-y-2" id="jenisFilterList">
                                        @foreach($jenisList as $jenis)
                                        <label class="flex items-center gap-2 text-sm">
                                            <input type="checkbox" name="jenis[]" value="{{ $jenis->id }}" class="jenis-filter" style="accent-color: #064635;"> {{ $jenis->nama }}
                                        </label>
                                        @endforeach
                                    </div>
                                    <hr style="border-color: #e5e7eb; margin: 12px 0;" />
                                    <div class="text-sm font-semibold mb-2" style="color: #064635;">Urutkan</div>
                                    <div class="space-y-2" style="margin-bottom: 12px;">
                                        <label class="flex items-center gap-2 text-sm" style="cursor: pointer;">
                                            <input type="radio" name="sortFilter" value="nama_asc" class="sort-filter" checked style="accent-color: #064635; cursor: pointer;"> Nama A-Z
                                        </label>
                                        <label class="flex items-center gap-2 text-sm" style="cursor: pointer;">
                                            <input type="radio" name="sortFilter" value="nama_desc" class="sort-filter" style="accent-color: #064635; cursor: pointer;"> Nama Z-A
                                        </label>
                                        <label class="flex items-center gap-2 text-sm" style="cursor: pointer;">
                                            <input type="radio" name="sortFilter" value="terbaru" class="sort-filter" style="accent-color: #064635; cursor: pointer;"> Terbaru
                                        </label>
                                        <label class="flex items-center gap-2 text-sm" style="cursor: pointer;">
                                            <input type="radio" name="sortFilter" value="terlama" class="sort-filter" style="accent-color: #064635; cursor: pointer;"> Terlama
                                        </label>
                                    </div>
                                    <div style="padding-top: 12px; border-top: 1px solid #e5e7eb;">
                                        <button onclick="applyFilters(); event.stopPropagation();" style="width: 100%; padding: 10px 16px; background: #064635; color: white; border: none; border-radius: 6px; font-size: 14px; font-weight: 500; cursor: pointer; transition: all 0.2s;" onmouseover="this.style.background='#2d3748'" onmouseout="this.style.background='#064635'">
                                            Terapkan Filter
                                        </button>
                                        <button onclick="resetFilters(); event.stopPropagation();" style="width: 100%; padding: 8px 16px; background: transparent; color: #6b7280; border: 1px solid #d1d5db; border-radius: 6px; font-size: 13px; margin-top: 8px; cursor: pointer; transition: all 0.2s;" onmouseover="this.style.background='#f3f4f6'" onmouseout="this.style.background='transparent'">
                                            Reset Filter
                                        </button>
                                    </div>
                                </div>
                            </div>
                            <a href="{{ route('manajemen-aset.create') }}" class="add-btn">
                                <i data-feather="plus"></i>
                                Tambah Aset
                            </a>
                        </div>
                    </div>
                    <!-- Bulk action bar -->
                    <div id="bulk-bar" style="display:none;align-items:center;gap:8px;background:#f4faf5;border-bottom:1px solid #f3f4f6;padding:10px 20px;flex-wrap:wrap;">
                        <span style="font-size:13px;font-weight:600;color:#064635;margin-right:4px;"><span id="bulk-count">0</span> data</span>
                        <span id="bulk-warn" style="display:none;font-size:12px;color:#b45309;background:#fef3c7;border:1px solid #fcd34d;border-radius:5px;padding:3px 9px;">&#9888; Hanya 100 pertama yang akan diproses</span>
                        <button type="button" onclick="clearSelection()" style="background:#fff;color:#374151;border:1px solid #d1d5db;padding:6px 10px;border-radius:6px;font-size:13px;font-weight:500;cursor:pointer;display:inline-flex;align-items:center;gap:6px;"><i data-feather="x" style="width:13px;height:13px;"></i> Batal</button>
                        <button type="button" id="btn-salin-data" onclick="bulkDuplicateConfirm()" style="background:#fff;color:#064635;border:1px solid #064635;padding:6px 12px;border-radius:6px;font-size:13px;font-weight:500;cursor:pointer;display:inline-flex;align-items:center;gap:6px;"><i data-feather="copy" style="width:13px;height:13px;stroke:#064635;"></i> Salin Data</button>
                        <button type="button" onclick="bulkDeleteConfirm()" style="background:#fff;color:#dc2626;border:1px solid #d1d5db;padding:6px 12px;border-radius:6px;font-size:13px;font-weight:500;cursor:pointer;display:inline-flex;align-items:center;gap:6px;"><i data-feather="trash-2" style="width:13px;height:13px;stroke:#dc2626;"></i> Hapus Data</button>
                    </div>
                    <form id="bulk-delete-form" method="POST" action="{{ route('manajemen-aset.bulk-destroy') }}" style="display:none;">@csrf</form>
                    <div class="data-card-body table-loading-wrap">
                        <table class="modern-table">
                            <thead>
                                <tr>
                                    <th style="width:42px;"><input type="checkbox" id="check-all" onchange="toggleAll(this)" style="cursor:pointer;accent-color:#064635;"></th>
                                    <th style="width: 50px; text-align: center;">No</th>
                                    <th>Nama Barang</th>
                                    <th>Jenis Inventaris</th>
                                    <th>Kode Barang</th>
                                    <th style="width: 140px;">Aksi</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($inventaris as $index => $item)
                                <tr>
                                    <td><input type="checkbox" class="row-check" value="{{ $item->id }}" onchange="updateBulkBar()" style="cursor:pointer;accent-color:#064635;"></td>
                                    <td style="text-align: center;">{{ $inventaris->firstItem() + $index }}</td>
                                    <td>
                                        <div class="aset-cell">
                                            <div class="aset-info">
                                                <h6>{{ $item->display_name ?? '-' }}</h6>
                                            </div>
                                        </div>
                                    </td>
                                    <td>
                                        @php
                                            // Gunakan accessor dari Model JenisInventaris
                                            $jenis = $item->jenis;
                                            $icon = $jenis->icon ?? 'box';
                                            $colors = $jenis->color_gradient ?? ['#6b7280', '#4b5563'];
                                        @endphp
                                        <div class="jenis-cell">
                                            <div class="jenis-icon-inline" style="background: linear-gradient(135deg, {{ $colors[0] }} 0%, {{ $colors[1] }} 100%);">
                                                <i data-feather="{{ $icon }}"></i>
                                            </div>
                                            <span style="font-size: 13px; color: #374151;">{{ $jenis->nama ?? 'Unknown' }}</span>
                                        </div>
                                    </td>
                                    <td><span class="code-badge">{{ $item->kode_barang }}</span></td>
                                    <td>
                                        <div class="action-btns">
                                            <a href="{{ route('manajemen-aset.show', $item->id) }}" class="action-btn view" title="Lihat">
                                                <i data-feather="eye"></i>
                                            </a>
                                            <a href="{{ route('manajemen-aset.edit', $item->id) }}?from=index" class="action-btn edit" title="Edit">
                                                <i data-feather="edit-2"></i>
                                            </a>
                                            <form action="{{ route('manajemen-aset.destroy', $item->id) }}" method="POST" style="display:inline;" id="deleteForm{{ $item->id }}">
                                                @csrf
                                                @method('DELETE')
                                                <button type="button" class="action-btn delete" title="Hapus" data-nama="{{ $item->display_name ?? '' }}" onclick="confirmDeleteInventaris(this.getAttribute('data-nama'), {{ $item->id }})"><i data-feather="trash-2"></i></button>
                                            </form>
                                        </div>
                                    </td>
                                </tr>
                                @empty
                                <tr>
                                    <td colspan="6" style="text-align:center;padding:40px;color:#9ca3af;">
                                        <div style="display:flex;flex-direction:column;align-items:center;justify-content:center;text-align:center;gap:8px;">
                                            <i data-feather="inbox" style="width:48px;height:48px;opacity:0.5;"></i>
                                            <p style="margin:0;">Belum ada data inventaris</p>
                                        </div>
                                    </td>
                                </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                    <!-- Pagination -->
                    <div class="pagination-bar">
                        <div class="pagination-left">
                            <div class="page-size-wrap">
                                <span>Tampilkan</span>
                                <select id="perPageSelect" class="per-page-select" onchange="changePerPage(this.value)">
                                    <option value="10" {{ ($inventaris->perPage() == 10) ? 'selected' : '' }}>10</option>
                                    <option value="20" {{ ($inventaris->perPage() == 20) ? 'selected' : '' }}>20</option>
                                    <option value="50" {{ ($inventaris->perPage() == 50) ? 'selected' : '' }}>50</option>
                                    <option value="100" {{ ($inventaris->perPage() == 100) ? 'selected' : '' }}>100</option>
                                </select>
                                <span>data</span>
                            </div>
                        </div>

                        <div class="pagination-center">
                            <nav class="pagination-nav" id="paginationNav">
                                @if ($inventaris->onFirstPage())
                                    <span class="page-btn disabled" title="Previous"><i data-feather="chevron-left"></i></span>
                                @else
                                    <button type="button" class="page-btn" onclick="goToPage({{ $inventaris->currentPage() - 1 }})" title="Previous"><i data-feather="chevron-left"></i></button>
                                @endif

                                @php
                                    $currentPage  = $inventaris->currentPage();
                                    $lastPage     = $inventaris->lastPage();
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
                                        <button type="button" class="page-btn" onclick="goToPage({{ $p }})">{{ $p }}</button>
                                    @endif
                                @endfor

                                @if ($inventaris->hasMorePages())
                                    <button type="button" class="page-btn" onclick="goToPage({{ $inventaris->currentPage() + 1 }})" title="Next"><i data-feather="chevron-right"></i></button>
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
                                    max="{{ $inventaris->lastPage() }}"
                                    value="{{ $inventaris->currentPage() }}"
                                    placeholder="1–{{ $inventaris->lastPage() }}"
                                    title="Masukkan nomor halaman (1–{{ $inventaris->lastPage() }})"
                                >
                            </div>
                        </div>

                        <div class="pagination-right" id="paginationRangeInfo">
                            Menampilkan <strong>{{ $inventaris->firstItem() ?? 0 }}-{{ $inventaris->lastItem() ?? 0 }}</strong> dari <strong>{{ $inventaris->total() }}</strong> data
                        </div>
                    </div>
                </div>
            </div>
        </div>
        @include('layouts.footer-block')
        @include('layouts.footer-js')

        <!-- Modal Duplikasi Aset -->
        <div id="dup-modal" style="display:none;position:fixed;inset:0;background:rgba(0,0,0,.5);z-index:9999;align-items:center;justify-content:center;">
            <div style="background:#fff;border-radius:16px;width:90%;max-width:420px;box-shadow:0 20px 60px rgba(0,0,0,.25);overflow:hidden;">
                <div style="padding:20px 24px 16px;border-bottom:1px solid #f3f4f6;">
                    <div style="font-size:16px;font-weight:700;color:#1f2937;">Duplikasi Aset</div>
                    <div style="font-size:13px;color:#6b7280;margin-top:4px;" id="dup-modal-nama"></div>
                </div>
                <div style="padding:20px 24px;">
                    <label style="display:block;font-size:13px;font-weight:500;color:#374151;margin-bottom:6px;">
                        Jumlah salinan yang akan dibuat
                    </label>
                    <input type="text" inputmode="numeric" id="dup-jumlah" min="1" max="50" value="1"
                        placeholder="Masukkan jumlah (1–50)"
                        style="width:100%;padding:10px 14px;border:1px solid #d1d5db;border-radius:8px;font-size:14px;outline:none;box-sizing:border-box;"
                        onfocus="this.style.borderColor='#064635';this.style.boxShadow='0 0 0 3px rgba(6, 70, 53,.1)'"
                        onblur="this.style.borderColor='#d1d5db';this.style.boxShadow='none'"
                        oninput="this.value=this.value.replace(/[^0-9]/g,'')">
                    <p style="font-size:12px;color:#6b7280;margin-top:6px;">Foto dan semua data akan ikut ter-copy. Barcode baru akan digenerate otomatis.</p>
                </div>
                <div style="padding:0 24px 20px;display:flex;gap:10px;justify-content:flex-end;">
                    <button onclick="closeDuplicateModal()"
                        style="padding:9px 18px;border:1px solid #d1d5db;background:#fff;color:#374151;border-radius:8px;font-size:13px;font-weight:500;cursor:pointer;">
                        Batal
                    </button>
                    <button id="dup-btn-confirm" onclick="confirmDuplicate()"
                        style="padding:9px 20px;border:none;background:linear-gradient(135deg,#064635,#1c5240);color:#fff;border-radius:8px;font-size:13px;font-weight:600;cursor:pointer;">
                        Duplikasi
                    </button>
                </div>
            </div>
        </div>

        <script>
            // Jump-to-page input
            (function () {
                const jumpInput = document.getElementById('pageJumpInput');
                if (!jumpInput) return;
                const lastPage = parseInt(jumpInput.max, 10);
                jumpInput.addEventListener('input', function () {
                    const raw = this.value;
                    if (raw === '' || raw === '0') return;
                    const val = parseInt(raw, 10);
                    if (!isNaN(val) && val > lastPage) { this.value = ''; }
                });
                jumpInput.addEventListener('keydown', function (e) {
                    if (e.key !== 'Enter') return;
                    const val = parseInt(this.value, 10);
                    if (isNaN(val) || val < 1 || val > lastPage) return;
                    const url = new URL(window.location.href);
                    url.searchParams.set('page', val);
                    window.location.href = url.toString();
                });
            })();

            function confirmDeleteInventaris(nama, id) {
                AppModal.confirmDelete(nama, function() {
                    document.getElementById('deleteForm' + id).submit();
                });
            }

            // ── Duplikasi Aset ──────────────────────────────────────────────
            let _dupId   = null;
            let _dupNama = '';

            function openDuplicateModal(id, nama) {
                _dupId   = id;
                _dupNama = nama || 'Aset';
                document.getElementById('dup-modal-nama').textContent = _dupNama;
                document.getElementById('dup-jumlah').value = '1';
                document.getElementById('dup-modal').style.display = 'flex';
                setTimeout(() => document.getElementById('dup-jumlah').focus(), 80);
            }

            function closeDuplicateModal() {
                document.getElementById('dup-modal').style.display = 'none';
                _dupId = null;
            }

            function confirmDuplicate() {
                if (!_dupId) return;
                const jumlah = parseInt(document.getElementById('dup-jumlah').value) || 1;
                if (jumlah < 1 || jumlah > 50) {
                    alert('Jumlah salinan harus antara 1 dan 50.');
                    return;
                }
                const btn = document.getElementById('dup-btn-confirm');
                btn.disabled = true;
                btn.textContent = 'Memproses...';

                const formData = new FormData();
                formData.append('_token', document.querySelector('meta[name="csrf-token"]').getAttribute('content'));
                formData.append('jumlah', jumlah);

                if (typeof AppLoader !== 'undefined') AppLoader.show();
                fetch(`{{ url('manajemen-aset') }}/${_dupId}/duplicate`, {
                    method: 'POST',
                    credentials: 'same-origin',
                    body: formData
                })
                .then(r => r.json())
                .then(data => {
                    if (typeof AppLoader !== 'undefined') AppLoader.hide();
                    closeDuplicateModal();
                    if (data.success) {
                        AppModal.success ? AppModal.success('Berhasil', data.message) : alert(data.message);
                        setTimeout(() => window.location.reload(), 800);
                    } else {
                        alert(data.message || 'Gagal menduplikasi aset.');
                    }
                })
                .catch(() => {
                    if (typeof AppLoader !== 'undefined') AppLoader.hide();
                    closeDuplicateModal();
                    alert('Terjadi kesalahan. Silakan coba lagi.');
                })
                .finally(() => {
                    btn.disabled = false;
                    btn.textContent = 'Duplikasi';
                });
            }

            document.addEventListener('keydown', function(e) {
                if (e.key === 'Escape') closeDuplicateModal();
            });

            function toggleAll(src) {
                document.querySelectorAll('.row-check').forEach(cb => { cb.checked = src.checked; });
                updateBulkBar();
            }
            function updateBulkBar() {
                const checked = document.querySelectorAll('.row-check:checked');
                const bar = document.getElementById('bulk-bar');
                bar.style.display = checked.length > 0 ? 'flex' : 'none';
                document.getElementById('bulk-count').textContent = checked.length;
                const warn = document.getElementById('bulk-warn');
                if (warn) warn.style.display = checked.length > 100 ? 'inline-block' : 'none';
                // Salin Data hanya muncul saat tepat 1 data dipilih
                const btnSalin = document.getElementById('btn-salin-data');
                if (btnSalin) btnSalin.style.display = checked.length === 1 ? 'inline-flex' : 'none';
                if (typeof feather !== 'undefined') feather.replace();
            }
            function clearSelection() {
                document.querySelectorAll('.row-check, #check-all').forEach(cb => { cb.checked = false; });
                document.getElementById('bulk-bar').style.display = 'none';
            }
            function bulkDeleteConfirm() {
                const checked = Array.from(document.querySelectorAll('.row-check:checked'));
                if (!checked.length) return;
                const toDelete = checked.slice(0, 100);
                const label = toDelete.length < checked.length
                    ? toDelete.length + ' data inventaris (dari ' + checked.length + ' terpilih, hanya 100 pertama)'
                    : checked.length + ' data inventaris terpilih';
                AppModal.confirmDelete(label, function() {
                    const form = document.getElementById('bulk-delete-form');
                    form.querySelectorAll('input[name="ids[]"]').forEach(el => el.remove());
                    toDelete.forEach(cb => {
                        const input = document.createElement('input');
                        input.type = 'hidden';
                        input.name = 'ids[]';
                        input.value = cb.value;
                        form.appendChild(input);
                    });
                    form.submit();
                });
            }

            function bulkDuplicateConfirm() {
                const checked = Array.from(document.querySelectorAll('.row-check:checked'));
                if (!checked.length) return;
                const toProcess = checked.slice(0, 100);
                const jumlahAset = toProcess.length;
                const namaAset = toProcess[0]?.closest('tr')?.querySelector('.aset-info h6')?.textContent || 'aset terpilih';

                AppModal.show({
                    customHtml: `
                        <div class="modal-header" style="padding-bottom:8px;">
                            <div class="modal-icon success" style="background:linear-gradient(135deg,#f0f4f8,#e8ecf0);">
                                <i data-feather="copy" style="color:#064635;stroke:#064635;"></i>
                            </div>
                            <h3 class="modal-title">Salin Data</h3>
                            <p class="modal-message">${jumlahAset === 1 ? namaAset : jumlahAset + ' aset terpilih'}</p>
                        </div>
                        <div class="modal-body" style="padding:0 24px 16px;text-align:left;">
                            <label style="font-size:13px;font-weight:500;color:#374151;display:block;margin-bottom:6px;">Jumlah salinan</label>
                            <input type="text" inputmode="numeric" id="bulk-dup-jumlah" value="1" placeholder="1–50"
                                style="width:100%;padding:10px 14px;border:1px solid #d1d5db;border-radius:8px;font-size:14px;outline:none;box-sizing:border-box;"
                                onfocus="this.style.borderColor='#064635';this.style.boxShadow='0 0 0 3px rgba(6, 70, 53,.1)'"
                                onblur="this.style.borderColor='#d1d5db';this.style.boxShadow='none'"
                                oninput="this.value=this.value.replace(/[^0-9]/g,'');var n=parseInt(this.value)||1;document.getElementById('bulk-dup-hint').textContent='Akan membuat '+n+' salinan.'">
                            <p id="bulk-dup-hint" style="font-size:12px;color:#6b7280;margin:6px 0 0;text-align:left;">Akan membuat 1 salinan.</p>
                        </div>
                        <div class="modal-footer">
                            <button type="button" class="modal-btn modal-btn-secondary" onclick="AppModal.handleCancel()" style="display:inline-flex;align-items:center;gap:6px;">
                                <i data-feather="x" style="width:14px;height:14px;"></i> Batal
                            </button>
                            <button type="button" class="modal-btn modal-btn-primary" onclick="AppModal.handleConfirm()">
                                Ya, Lanjutkan
                            </button>
                        </div>`,
                    onConfirm: function() {
                        const jumlah = parseInt(document.getElementById('bulk-dup-jumlah')?.value) || 1;
                        const safe   = Math.max(1, Math.min(50, jumlah));
                        const ids    = toProcess.map(cb => cb.value);
                        const token  = document.querySelector('meta[name="csrf-token"]').getAttribute('content');

                        if (typeof AppLoader !== 'undefined') AppLoader.show();

                        // Gunakan URL relatif agar tidak ada CORS issue
                        const promises = ids.map(id => {
                            const fd = new FormData();
                            fd.append('_token', token);
                            fd.append('jumlah', safe);
                            return fetch('/manajemen-aset/' + id + '/duplicate', {
                                method: 'POST',
                                credentials: 'same-origin',
                                headers: { 'X-Requested-With': 'XMLHttpRequest', 'Accept': 'application/json' },
                                body: fd
                            }).then(r => r.json());
                        });

                        Promise.all(promises).then(results => {
                            const total = results.reduce((sum, r) => sum + (r.created || 0), 0);
                            const namaLabel = jumlahAset === 1 ? namaAset : jumlahAset + ' aset';
                            // Gunakan session flash dari server — redirect ke index dengan pesan sukses
                            // yang akan ditangani oleh layouts/notification.blade.php secara otomatis
                            const msg = total + ' salinan ' + namaLabel + ' berhasil dibuat.';
                            AppModal.close();
                            // Redirect ke index dengan flash message via query param
                            window.location.href = '/manajemen-aset?_flash_success=' + encodeURIComponent(msg);
                        }).catch(() => {
                            if (typeof AppLoader !== 'undefined') AppLoader.hide();
                            AppModal.error('Gagal', 'Terjadi kesalahan saat menyalin data. Silakan coba lagi.');
                        });
                    },
                    onCancel: function() { AppModal.close(); }
                });
            }
            function toggleSearchClear(input) {
                document.getElementById('search-icon-btn').style.display = input.value ? 'none' : '';
                document.getElementById('clear-search-btn').style.display = input.value ? '' : 'none';
            }
            // Debounce live search
            let _searchTimer = null;
            function onSearchInput(input) {
                toggleSearchClear(input);
                clearTimeout(_searchTimer);
                _searchTimer = setTimeout(function() {
                    fetchInventaris();
                }, 400);
            }
            function doSearch() { fetchInventaris(); }
            function clearSearch() {
                const input = document.getElementById('searchInput');
                input.value = '';
                toggleSearchClear(input);
                fetchInventaris();
            }
            function changePerPage(perPage) {
                fetchInventaris(false, 1);
            }

            function goToPage(page) {
                fetchInventaris(false, page);
            }

            (function () {
                const jumpInput = document.getElementById('pageJumpInput');
                if (!jumpInput) return;
                jumpInput.addEventListener('keydown', function (e) {
                    if (e.key !== 'Enter') return;
                    const lastPage = parseInt(this.max || 1, 10);
                    const val = parseInt(this.value, 10);
                    if (isNaN(val) || val < 1 || val > lastPage) return;
                    goToPage(val);
                });
            })();

            function buildParams() {
                const params = new URLSearchParams();
                const q = document.getElementById('searchInput').value.trim();
                if (q) params.set('search', q);
                document.querySelectorAll('.jenis-filter:checked').forEach(cb => params.append('jenis[]', cb.value));
                const sort = document.querySelector('.sort-filter:checked')?.value || 'nama_asc';
                if (sort !== 'nama_asc') params.set('sort', sort);
                const perPageSelect = document.getElementById('perPageSelect');
                const perPageValue = perPageSelect ? perPageSelect.value : '10';
                if (perPageValue && perPageValue !== '10') params.set('per_page', perPageValue);
                return params;
            }
            function fetchInventaris(isAutoRefresh = false, pageOverride = null) {
                const params = buildParams();
                const urlParams = new URLSearchParams(window.location.search);
                let currentPage = pageOverride !== null ? pageOverride : (urlParams.get('page') || 1);
                if (currentPage > 1) {
                    params.set('page', currentPage);
                }
                const newUrl = window.location.pathname + (params.toString() ? '?' + params.toString() : '');
                history.replaceState(null, '', newUrl);
                const wrap = document.querySelector('.table-loading-wrap');
                if (!isAutoRefresh && wrap) wrap.classList.add('is-loading');
                fetch('{{ route('manajemen-aset.index') }}?' + params.toString(), {
                    headers: { 'X-Requested-With': 'XMLHttpRequest', 'Accept': 'application/json' }
                })
                .then(r => r.json())
                .then(data => {
                    updateStats(data.stats);
                    updateTable(data.inventaris, data.pagination);
                    updatePaginationUI(data.pagination);
                    if (typeof feather !== 'undefined') feather.replace();
                })
                .catch(err => console.error(err))
                .finally(() => { if (wrap) wrap.classList.remove('is-loading'); });
            }
            function updateStats(stats) {
                const cards = document.querySelectorAll('.stat-card');
                if (cards[0]) cards[0].querySelector('h3').textContent = stats.totalInventaris;
                if (cards[1]) cards[1].querySelector('h3').textContent = stats.kondisiBaik;
                if (cards[2]) cards[2].querySelector('h3').textContent = stats.kondisiRusakRingan;
                if (cards[3]) cards[3].querySelector('h3').textContent = stats.kondisiRusakBerat;
                document.querySelectorAll('.data-card-title span').forEach(el => {
                    el.textContent = 'Total ' + stats.totalInventaris + ' aset terdaftar';
                });
            }
            function updatePaginationUI(pagination) {
                if (!pagination) return;
                const firstItem = pagination.firstItem || 0;
                const lastItem = pagination.lastItem || 0;
                const total = (pagination.total !== undefined) ? pagination.total : 0;
                const currentPage = pagination.currentPage || 1;
                const lastPage = pagination.lastPage || 1;

                const rangeEl = document.getElementById('paginationRangeInfo');
                if (rangeEl) {
                    rangeEl.innerHTML = `Menampilkan <strong>${firstItem}-${lastItem}</strong> dari <strong>${total}</strong> data`;
                }

                const jumpInput = document.getElementById('pageJumpInput');
                if (jumpInput) {
                    jumpInput.max = lastPage;
                    jumpInput.placeholder = `1–${lastPage}`;
                    jumpInput.title = `Masukkan nomor halaman (1–${lastPage})`;
                    jumpInput.value = currentPage;
                }

                const nav = document.getElementById('paginationNav');
                if (!nav) return;

                let html = '';
                if (currentPage <= 1) {
                    html += `<span class="page-btn disabled" title="Previous"><i data-feather="chevron-left"></i></span>`;
                } else {
                    html += `<button type="button" class="page-btn" onclick="goToPage(${currentPage - 1})" title="Previous"><i data-feather="chevron-left"></i></button>`;
                }

                let windowStart = Math.max(1, currentPage - 2);
                let windowEnd = Math.min(lastPage, currentPage + 2);
                if ((windowEnd - windowStart + 1) < 5) {
                    if (windowStart === 1) {
                        windowEnd = Math.min(lastPage, 5);
                    } else {
                        windowStart = Math.max(1, windowEnd - 4);
                    }
                }

                for (let p = windowStart; p <= windowEnd; p++) {
                    if (p === currentPage) {
                        html += `<span class="page-btn active">${p}</span>`;
                    } else {
                        html += `<button type="button" class="page-btn" onclick="goToPage(${p})">${p}</button>`;
                    }
                }

                if (currentPage >= lastPage) {
                    html += `<span class="page-btn disabled" title="Next"><i data-feather="chevron-right"></i></span>`;
                } else {
                    html += `<button type="button" class="page-btn" onclick="goToPage(${currentPage + 1})" title="Next"><i data-feather="chevron-right"></i></button>`;
                }

                nav.innerHTML = html;
            }
            function updateTable(items, pagination) {
                const tbody = document.querySelector('.modern-table tbody');
                if (!tbody) return;
                const firstItem = pagination ? pagination.firstItem : 1;
                const lastItem = pagination ? (firstItem + items.length - 1) : items.length;
                const total = pagination ? pagination.total : items.length;
                if (items.length === 0) {
                    tbody.innerHTML = `<tr><td colspan="6" style="text-align:center;padding:40px;color:#9ca3af;">
                        <div style="display:flex;flex-direction:column;align-items:center;justify-content:center;text-align:center;gap:8px;">
                            <i data-feather="inbox" style="width:48px;height:48px;opacity:0.5;"></i>
                            <p style="margin:0;">Belum ada data inventaris</p>
                        </div></td></tr>`;
                    return;
                }
                let html = '';
                items.forEach((item, idx) => {
                    const rowNumber = firstItem + idx;
                    html += `<tr>
                        <td><input type="checkbox" class="row-check" value="${item.id}" onchange="updateBulkBar()" style="cursor:pointer;accent-color:#064635;"></td>
                        <td style="text-align:center;">${rowNumber}</td>
                        <td><div class="aset-cell"><div class="aset-info"><h6>${item.display_name}</h6><span>${item.nup}</span></div></div></td>
                        <td><div class="jenis-cell"><div class="jenis-icon-inline" style="background:linear-gradient(135deg,${item.jenis_color0} 0%,${item.jenis_color1} 100%);"><i data-feather="${item.jenis_icon}"></i></div><span style="font-size:13px;color:#374151;">${item.jenis_nama}</span></div></td>
                        <td><span class="code-badge">${item.kode_barang}</span></td>
                        <td><div class="action-btns">
                            <a href="{{ url('manajemen-aset') }}/${item.id}" class="action-btn view" title="Lihat"><i data-feather="eye"></i></a>
                            <a href="{{ url('manajemen-aset') }}/${item.id}/edit?from=index" class="action-btn edit" title="Edit"><i data-feather="edit-2"></i></a>
                            <button type="button" class="action-btn delete" title="Hapus" data-nama="${item.display_name.replace(/&/g, '&amp;').replace(/"/g, '&quot;')}" onclick="confirmDeleteInventaris(this.getAttribute('data-nama'), ${item.id})"><i data-feather="trash-2"></i></button>
                        </div></td>
                    </tr>`;
                });
                tbody.innerHTML = html;
            }
            // Filter control functions
            function applyFilters() {
                fetchInventaris();
            }
            function resetFilters() {
                document.querySelectorAll('.jenis-filter').forEach(cb => cb.checked = false);
                document.querySelectorAll('.sort-filter').forEach(r => r.checked = false);
                document.querySelector('.sort-filter[value="nama_asc"]').checked = true;
                const searchInput = document.getElementById('searchInput');
                if (searchInput) searchInput.value = '';
                window.location.href = '{{ route('manajemen-aset.index') }}';
            }
            
            document.addEventListener('DOMContentLoaded', function() {
                // Mark filter checkboxes from URL (restore previous filter state)
                const urlParams = new URLSearchParams(window.location.search);
                urlParams.getAll('jenis[]').forEach(v => {
                    const cb = document.querySelector('.jenis-filter[value="'+v+'"]');
                    if (cb) cb.checked = true;
                });
                const sortParam = urlParams.get('sort') || 'nama_asc';
                const sr = document.querySelector('.sort-filter[value="'+sortParam+'"]');
                if (sr) sr.checked = true;

                const autoRefreshMs = 15000;
                setInterval(function () {
                    if (document.hidden) return;
                    // Jangan reload jika user sedang mencentang checkbox
                    if (document.querySelectorAll('.row-check:checked').length > 0) return;
                    fetchInventaris(true);
                }, autoRefreshMs);
            });
        </script>
        @include('layouts.modals')
    </body>
</html>
