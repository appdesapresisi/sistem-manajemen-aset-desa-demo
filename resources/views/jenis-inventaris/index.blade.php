<!DOCTYPE html>
<html lang="en">
    <head>
        @include('layouts.head-page-meta', ['title' => 'Jenis Inventaris - Sistem Manajemen Aset Desa Demo'])
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

            /* Jenis Cell */
            .jenis-cell {
                display: flex;
                align-items: center;
                gap: 14px;
            }
            .jenis-icon {
                width: 44px;
                height: 44px;
                border-radius: 12px;
                display: flex;
                align-items: center;
                justify-content: center;
                flex-shrink: 0;
            }
            .jenis-icon i { 
                width: 20px; 
                height: 20px; 
                color: #fff !important; 
                stroke: #fff !important; 
            }
            .jenis-icon svg {
                width: 20px;
                height: 20px;
                stroke: #fff !important;
                color: #fff !important;
            }
            .jenis-info h6 {
                font-size: 14px;
                font-weight: 600;
                color: #1f2937;
                margin: 0 0 2px 0;
            }
            .jenis-info span {
                font-size: 12px;
                color: #9ca3af;
            }

            /* Category Badge */
            .category-badge {
                display: inline-flex;
                align-items: center;
                gap: 6px;
                padding: 4px 10px;
                border-radius: 20px;
                font-size: 11px;
                font-weight: 600;
            }
            .category-badge.aset {
                background: linear-gradient(135deg, #dbeafe 0%, #bfdbfe 100%);
                color: #2563eb;
            }

            /* Count Badge */
            .count-badge {
                font-size: 14px;
                font-weight: 700;
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

            /* Description text */
            .desc-text {
                font-size: 13px;
                color: #6b7280;
                max-width: 100%;
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
        @php
            $canManageJenisInventaris = auth()->user()->isSuperAdmin();
        @endphp

        @include('layouts.notification')
        @include('layouts.modals')
        @include('layouts.layout-vertical')

        <div class="pc-container">
            <div class="pc-content">
                @include('layouts.breadcrumb', ['breadcrumbItemHome' => 'Master Data', 'breadcrumbItem' => 'Jenis Inventaris', 'breadcrumbItemActive' => 'List Jenis'])
 
                <!-- Page Header -->
                <div class="page-header-card">
                    {{-- <h1><i data-feather="layers" style="width: 32px; height: 32px; display: inline-block; vertical-align: middle; margin-right: 12px;"></i>Jenis Inventaris</h1> --}}
                    <h1>Jenis Inventaris</h1>
                    <p>Kelola kategori dan jenis inventaris untuk klasifikasi aset desa.</p>
                </div>

                <!-- Stats Cards -->
                <div class="stats-grid">
                    <div class="stat-card">
                        <div class="stat-card-label">TOTAL JENIS</div>
                        <div class="stat-card-value">{{ number_format($stats['totalJenis'] ?? 0, 0, ',', '.') }}</div>
                        <div class="kpi-trend-wrapper">
                            @php $tJenis = $stats['trends']['jenis']['percent'] ?? 0; @endphp
                            @if($tJenis > 0)
                                <span class="kpi-trend-pill up">
                                    <svg width="10" height="10" viewBox="0 0 24 24" fill="currentColor"><path d="M12 4l-8 8h16z"/></svg>
                                    +{{ $tJenis }}%
                                </span>
                            @elseif($tJenis < 0)
                                <span class="kpi-trend-pill down">
                                    <svg width="10" height="10" viewBox="0 0 24 24" fill="currentColor"><path d="M12 20l8-8H4z"/></svg>
                                    {{ $tJenis }}%
                                </span>
                            @else
                                <span class="kpi-trend-pill neutral">0%</span>
                            @endif
                            <span class="kpi-trend-label">vs. kemarin</span>
                        </div>
                    </div>

                    <div class="stat-card">
                        <div class="stat-card-label">TOTAL ASET</div>
                        <div class="stat-card-value">{{ number_format($stats['totalAssets'] ?? 0, 0, ',', '.') }}</div>
                        <div class="kpi-trend-wrapper">
                            @php $tAssets = $stats['trends']['assets']['percent'] ?? 0; @endphp
                            @if($tAssets > 0)
                                <span class="kpi-trend-pill up">
                                    <svg width="10" height="10" viewBox="0 0 24 24" fill="currentColor"><path d="M12 4l-8 8h16z"/></svg>
                                    +{{ $tAssets }}%
                                </span>
                            @elseif($tAssets < 0)
                                <span class="kpi-trend-pill down">
                                    <svg width="10" height="10" viewBox="0 0 24 24" fill="currentColor"><path d="M12 20l8-8H4z"/></svg>
                                    {{ $tAssets }}%
                                </span>
                            @else
                                <span class="kpi-trend-pill neutral">0%</span>
                            @endif
                            <span class="kpi-trend-label">vs. kemarin</span>
                        </div>
                    </div>

                    <div class="stat-card">
                        <div class="stat-card-label">JENIS ASET TERBANYAK</div>
                        <div class="stat-card-value" style="font-size: 18px; line-height: 1.3; font-weight: 700; white-space: nowrap; overflow: hidden; text-overflow: ellipsis;" title="{{ $stats['topJenisNama'] ?? '-' }}">{{ $stats['topJenisNama'] ?? '-' }}</div>
                        <div class="kpi-trend-wrapper">
                            <span class="kpi-trend-pill up" style="background:#e0e7ff; color:#3730a3;">
                                {{ number_format($stats['topJenisCount'] ?? 0, 0, ',', '.') }} aset
                            </span>
                            <span class="kpi-trend-label">dominan</span>
                        </div>
                    </div>

                    <div class="stat-card">
                        <div class="stat-card-label">RATA RATA ASET / JENIS</div>
                        <div class="stat-card-value">{{ number_format($stats['avgAssetsPerJenis'] ?? 0, 1, ',', '.') }}</div>
                        <div class="kpi-trend-wrapper">
                            <span class="kpi-trend-pill neutral">unit / jenis</span>
                            <span class="kpi-trend-label">rata-rata sebaran</span>
                        </div>
                    </div>
                </div>

                <!-- Data Card -->
                <div class="data-card">
                    <div class="data-card-header">
                        <div class="data-card-title">
                            <div>
                                <h5>Daftar Jenis Inventaris</h5>
                                <span>Total {{ $jenisInventaris->total() }} jenis inventaris</span>
                            </div>
                        </div>
                        <div class="controls-bar">
                            <div class="search-box">
                                <input type="text" id="searchInput" placeholder="Cari jenis..." oninput="onSearchInput(this)" onkeypress="if(event.key==='Enter'){doSearch();}" value="{{ request('search') }}" />
                                <button id="search-icon-btn" onclick="doSearch()" @if(request('search')) style="display:none" @endif><i data-feather="search"></i></button>
                                <button id="clear-search-btn" onclick="clearSearch()" style="color:#ef4444;{{ request('search') ? '' : 'display:none;' }}"><i data-feather="x"></i></button>
                            </div>
                            <div class="dropdown" style="position:relative;">
                                <button class="filter-btn" data-pc-toggle="dropdown" title="Filter">
                                    <i data-feather="sliders"></i>
                                </button>
                                <div class="dropdown-menu dropdown-menu-end py-3 px-4" onclick="event.stopPropagation();" style="min-width: 240px; z-index: 1050;">
                                    <div class="text-sm font-semibold mb-2" style="color: #064635;">Filter Status</div>
                                    <div class="space-y-2" style="margin-bottom: 12px;">
                                        <label class="flex items-center gap-2 text-sm" style="cursor: pointer;">
                                            <input type="radio" name="statusFilter" value="all" class="status-filter" checked style="accent-color: #064635; cursor: pointer;"> Semua Status
                                        </label>
                                        <label class="flex items-center gap-2 text-sm" style="cursor: pointer;">
                                            <input type="radio" name="statusFilter" value="dengan_aset" class="status-filter" style="accent-color: #064635; cursor: pointer;"> Memiliki Aset
                                        </label>
                                        <label class="flex items-center gap-2 text-sm" style="cursor: pointer;">
                                            <input type="radio" name="statusFilter" value="tanpa_aset" class="status-filter" style="accent-color: #064635; cursor: pointer;"> Tanpa Aset (Kosong)
                                        </label>
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
                                        <button onclick="applyFilters(); event.stopPropagation();" style="width: 100%; padding: 10px 16px; background: #064635; color: white; border: none; border-radius: 6px; font-size: 14px; font-weight: 500; cursor: pointer; transition: all 0.2s;">
                                            Terapkan Filter
                                        </button>
                                        <button onclick="resetFilters(); event.stopPropagation();" style="width: 100%; padding: 8px 16px; background: transparent; color: #6b7280; border: 1px solid #d1d5db; border-radius: 6px; font-size: 13px; margin-top: 8px; cursor: pointer; transition: all 0.2s;">
                                            Reset Filter
                                        </button>
                                    </div>
                                </div>
                            </div>
                            @if($canManageJenisInventaris)
                            <a href="{{ route('jenis-inventaris.create') }}" class="add-btn">
                                <i data-feather="plus"></i>
                                Tambah Jenis
                            </a>
                            @endif
                        </div>
                    </div>
                    @if($canManageJenisInventaris)
                    <!-- Bulk action bar -->
                    <div id="bulk-bar" style="display:none;align-items:center;gap:12px;background:#fef2f2;border:1px solid #fecaca;border-radius:8px;padding:10px 16px;margin:12px 16px 0;flex-wrap:wrap;">
                        <span style="font-size:13px;font-weight:600;color:#dc2626;"><span id="bulk-count">0</span> data terpilih</span>
                        <span id="bulk-warn" style="display:none;font-size:12px;color:#b45309;background:#fef3c7;border:1px solid #fcd34d;border-radius:5px;padding:3px 9px;">&#9888; Hanya 100 pertama yang akan dihapus, pilih ulang untuk sisanya</span>
                        <button type="button" onclick="clearSelection()" style="background:transparent;color:#6b7280;border:1px solid #d1d5db;padding:7px 12px;border-radius:6px;font-size:13px;cursor:pointer;display:inline-flex;align-items:center;gap:5px;"><i data-feather="x" style="width:14px;height:14px;"></i> Batal</button>
                        <button type="button" onclick="bulkDeleteConfirm()" style="background:#dc2626;color:#fff;border:none;padding:7px 14px;border-radius:6px;font-size:13px;font-weight:600;cursor:pointer;display:inline-flex;align-items:center;gap:5px;"><i data-feather="trash-2" style="width:14px;height:14px;"></i> Hapus Terpilih</button>
                    </div>
                    <form id="bulk-delete-form" method="POST" action="{{ route('jenis-inventaris.bulk-destroy') }}" style="display:none;">@csrf</form>
                    @endif
                    <div class="data-card-body table-loading-wrap">
                        <table class="modern-table">
                            <thead>
                                <tr>
                                    <th style="width:42px;">@if($canManageJenisInventaris)<input type="checkbox" id="check-all" onchange="toggleAll(this)" style="cursor:pointer;accent-color:#064635;">@endif</th>
                                    <th style="width: 60px; text-align: center;">No</th>
                                    <th style="width: 33%;">Jenis Inventaris</th>
                                    <th style="width: 50%;">Deskripsi</th>
                                    <th style="width: 17%; text-align: center;">Jumlah Aset</th>
                                    <th style="width: 140px;">Aksi</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($jenisInventaris as $index => $jenis)
                                <tr>
                                    <td>@if($canManageJenisInventaris && !$jenis->isVirtual())<input type="checkbox" class="row-check" value="{{ $jenis->id }}" onchange="updateBulkBar()" style="cursor:pointer;accent-color:#064635;">@endif</td>
                                    <td style="text-align: center;">{{ $jenisInventaris->firstItem() + $index }}</td>
                                    <td>
                                        <div class="jenis-cell">
                                            <div class="jenis-icon" style="background: {{ $jenis->getColorGradientCss() }};">
                                                <i data-feather="{{ $jenis->icon }}"></i>
                                            </div>
                                            <div class="jenis-info">
                                                <h6>{{ $jenis->nama }}</h6>
                                                <!-- Fields count badge removed as per user request -->
                                            </div>
                                        </div>
                                    </td>
                                    <td><span class="desc-text">{{ Str::limit($jenis->deskripsi ?? 'Belum ada deskripsi', 50) }}</span></td>
                                    <td style="text-align: center;"><span class="count-badge">{{ $jenis->isVirtual() ? ($virtualAsetCount ?? 0) : ($jenis->inventaris ? $jenis->inventaris->count() : 0) }}</span></td>
                                    <td>
                                        <div class="action-btns">
                                            <a href="{{ route('jenis-inventaris.show', $jenis->id) }}" class="action-btn view" title="Lihat">
                                                <i data-feather="eye"></i>
                                            </a>
                                            @if($canManageJenisInventaris)
                                            <a href="{{ route('jenis-inventaris.edit', $jenis->id) }}" class="action-btn edit" title="Edit">
                                                <i data-feather="edit-2"></i>
                                            </a>
                                            @if(!$jenis->isVirtual())
                                            <button data-nama="{{ $jenis->nama }}" onclick="confirmDeleteJenis({{ $jenis->id }}, this.getAttribute('data-nama'))" class="action-btn delete" title="Hapus">
                                                <i data-feather="trash-2"></i>
                                            </button>
                                            @endif
                                            @endif
                                        </div>
                                    </td>
                                </tr>
                                @empty
                                <tr>
                                    <td colspan="6" style="text-align:center;padding:40px;color:#9ca3af;">
                                        <div style="display:flex;flex-direction:column;align-items:center;justify-content:center;text-align:center;gap:8px;">
                                            <i data-feather="inbox" style="width:48px;height:48px;opacity:0.5;"></i>
                                            <p style="margin:0;">Belum ada data jenis inventaris</p>
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
                                    <option value="10" {{ ($jenisInventaris->perPage() == 10) ? 'selected' : '' }}>10</option>
                                    <option value="20" {{ ($jenisInventaris->perPage() == 20) ? 'selected' : '' }}>20</option>
                                    <option value="50" {{ ($jenisInventaris->perPage() == 50) ? 'selected' : '' }}>50</option>
                                    <option value="100" {{ ($jenisInventaris->perPage() == 100) ? 'selected' : '' }}>100</option>
                                </select>
                                <span>data</span>
                            </div>
                        </div>

                        <div class="pagination-center">
                            <nav class="pagination-nav" id="paginationNav">
                                @if ($jenisInventaris->onFirstPage())
                                    <span class="page-btn disabled" title="Previous"><i data-feather="chevron-left"></i></span>
                                @else
                                    <button type="button" class="page-btn" onclick="goToPage({{ $jenisInventaris->currentPage() - 1 }})" title="Previous"><i data-feather="chevron-left"></i></button>
                                @endif

                                @php
                                    $currentPage  = $jenisInventaris->currentPage();
                                    $lastPage     = $jenisInventaris->lastPage();
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

                                @if ($jenisInventaris->hasMorePages())
                                    <button type="button" class="page-btn" onclick="goToPage({{ $jenisInventaris->currentPage() + 1 }})" title="Next"><i data-feather="chevron-right"></i></button>
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
                                    max="{{ $jenisInventaris->lastPage() }}"
                                    value="{{ $jenisInventaris->currentPage() }}"
                                    placeholder="1–{{ $jenisInventaris->lastPage() }}"
                                    title="Masukkan nomor halaman (1–{{ $jenisInventaris->lastPage() }})"
                                >
                            </div>
                        </div>

                        <div class="pagination-right" id="paginationRangeInfo">
                            Menampilkan <strong>{{ $jenisInventaris->firstItem() ?? 0 }}-{{ $jenisInventaris->lastItem() ?? 0 }}</strong> dari <strong>{{ $jenisInventaris->total() }}</strong> data
                        </div>
                    </div>
                </div>
            </div>
        </div>
        @include('layouts.footer-block')
        @include('layouts.footer-js')
        <!-- Success Toast Notification -->
        <div id="success-toast" class="fixed top-4 right-4 z-50 transform translate-x-full transition-transform duration-300" style="display:none;">
            <div class="flex items-center gap-3 bg-green-500 text-white px-5 py-4 rounded-xl shadow-xl">
                <i data-feather="check-circle" style="width:22px;height:22px;"></i>
                <span id="success-message" class="font-medium">Jenis Inventaris berhasil ditambahkan!</span>
                <button onclick="closeToast()" class="ml-2 hover:opacity-80">
                    <i data-feather="x" style="width:18px;height:18px;"></i>
                </button>
            </div>
        </div>



        <script>
            const canManageJenisInventaris = @json($canManageJenisInventaris);

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

            // Check for success message from jenis-inventaris-form
            (function(){
                const success = sessionStorage.getItem('jenisFormSuccess');
                const message = sessionStorage.getItem('jenisFormMessage');
                
                if(success === 'true'){
                    const toast = document.getElementById('success-toast');
                    const msgEl = document.getElementById('success-message');
                    
                    if(message) msgEl.textContent = message;
                    
                    toast.style.display = 'block';
                    setTimeout(() => {
                        toast.classList.remove('translate-x-full');
                        toast.classList.add('translate-x-0');
                    }, 100);
                    
                    // Auto close after 5 seconds
                    setTimeout(() => closeToast(), 5000);
                    
                    // Clear sessionStorage
                    sessionStorage.removeItem('jenisFormSuccess');
                    sessionStorage.removeItem('jenisFormMessage');
                    
                    // Re-init feather icons for toast
                    if(typeof feather !== 'undefined') feather.replace();
                }
            })();

            function closeToast(){
                const toast = document.getElementById('success-toast');
                toast.classList.remove('translate-x-0');
                toast.classList.add('translate-x-full');
                setTimeout(() => { toast.style.display = 'none'; }, 300);
            }

            function toggleSearchClear(input) {
                document.getElementById('search-icon-btn').style.display = input.value ? 'none' : '';
                document.getElementById('clear-search-btn').style.display = input.value ? '' : 'none';
            }
            let _searchTimer = null;
            function onSearchInput(input) {
                toggleSearchClear(input);
                clearTimeout(_searchTimer);
                _searchTimer = setTimeout(function() { fetchJenis(); }, 400);
            }
            function doSearch() { fetchJenis(); }
            function clearSearch() {
                const input = document.getElementById('searchInput');
                input.value = '';
                toggleSearchClear(input);
                fetchJenis();
            }
            function changePerPage(perPage) {
                fetchJenis(false, 1);
            }

            function goToPage(page) {
                fetchJenis(false, page);
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

            let appliedStatus = 'all';
            let appliedSort   = 'nama_asc';

            function fetchJenis(isAutoRefresh = false, pageOverride = null) {
                const q            = (document.getElementById('searchInput')?.value || '').trim();
                const perPageSelect = document.getElementById('perPageSelect');
                const perPageValue  = perPageSelect ? perPageSelect.value : '10';

                const params       = new URLSearchParams();
                if (q) params.set('search', q);
                if (appliedStatus && appliedStatus !== 'all') params.set('status', appliedStatus);
                if (appliedSort && appliedSort !== 'nama_asc') params.set('sort', appliedSort);
                if (perPageValue && perPageValue !== '10') params.set('per_page', perPageValue);

                const urlParams = new URLSearchParams(window.location.search);
                let currentPage = pageOverride !== null ? pageOverride : (urlParams.get('page') || 1);
                if (currentPage > 1) {
                    params.set('page', currentPage);
                }

                history.replaceState(null, '', window.location.pathname + (params.toString() ? '?' + params.toString() : ''));
                const wrap = document.querySelector('.table-loading-wrap');
                if (!isAutoRefresh && wrap) wrap.classList.add('is-loading');
                fetch('{{ route('jenis-inventaris.index') }}?' + params.toString(), {
                    headers: { 'X-Requested-With': 'XMLHttpRequest', 'Accept': 'application/json' }
                })
                .then(r => r.json())
                .then(data => {
                    updateStatsJenis(data.stats);
                    updateTableJenis(data.jenisInventaris, data.from, data.to, data.total, data.pagination);
                    updatePaginationUI(data.pagination);
                    if (typeof feather !== 'undefined') feather.replace();
                })
                .catch(err => console.error(err))
                .finally(() => { if (wrap) wrap.classList.remove('is-loading'); });
            }

            function applyFilters() {
                appliedStatus = document.querySelector('.status-filter:checked')?.value || 'all';
                appliedSort   = document.querySelector('.sort-filter:checked')?.value || 'nama_asc';
                fetchJenis(false, 1);
                document.querySelectorAll('.dropdown-menu.show').forEach(el => el.classList.remove('show'));
                document.querySelectorAll('.dropdown.show').forEach(el => el.classList.remove('show'));
            }

            function resetFilters() {
                const searchInput = document.getElementById('searchInput');
                if (searchInput) searchInput.value = '';

                const defaultStatus = document.querySelector('.status-filter[value="all"]');
                if (defaultStatus) defaultStatus.checked = true;

                const defaultSort = document.querySelector('.sort-filter[value="nama_asc"]');
                if (defaultSort) defaultSort.checked = true;

                appliedStatus = 'all';
                appliedSort   = 'nama_asc';

                fetchJenis(false, 1);
                document.querySelectorAll('.dropdown-menu.show').forEach(el => el.classList.remove('show'));
                document.querySelectorAll('.dropdown.show').forEach(el => el.classList.remove('show'));
            }
            function updateStatsJenis(stats) {
                if (!stats) return;
                const cards = document.querySelectorAll('.stat-card');
                if (cards[0]) {
                    const valEl = cards[0].querySelector('.stat-card-value');
                    if (valEl) valEl.textContent = stats.totalJenis ?? 0;
                }
                if (cards[1]) {
                    const valEl = cards[1].querySelector('.stat-card-value');
                    if (valEl) valEl.textContent = (stats.totalAssets ?? 0).toLocaleString('id-ID');
                }
                if (cards[2]) {
                    const valEl = cards[2].querySelector('.stat-card-value');
                    if (valEl) {
                        valEl.textContent = stats.topJenisNama || '-';
                        valEl.title = stats.topJenisNama || '-';
                    }
                    const pill = cards[2].querySelector('.kpi-trend-pill');
                    if (pill) pill.textContent = (stats.topJenisCount ?? 0).toLocaleString('id-ID') + ' aset';
                }
                if (cards[3]) {
                    const valEl = cards[3].querySelector('.stat-card-value');
                    if (valEl) valEl.textContent = (stats.avgAssetsPerJenis ?? 0).toLocaleString('id-ID');
                }
                document.querySelectorAll('.data-card-title span').forEach(el => {
                    el.textContent = 'Total ' + (stats.totalJenis ?? 0) + ' jenis terdaftar';
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
            function updateTableJenis(items, from, to, total, pagination) {
                const tbody = document.querySelector('.modern-table tbody');
                if (!tbody) return;
                if (!items || items.length === 0) {
                    tbody.innerHTML = `<tr><td colspan="6" style="text-align:center;padding:40px;color:#9ca3af;">
                        <div style="display:flex;flex-direction:column;align-items:center;justify-content:center;text-align:center;gap:8px;">
                            <i data-feather="inbox" style="width:48px;height:48px;opacity:0.5;"></i>
                            <p style="margin:0;">Belum ada data jenis inventaris</p>
                        </div></td></tr>`;
                    return;
                }
                let html = '';
                items.forEach((jenis, idx) => {
                    const desc = jenis.deskripsi ? jenis.deskripsi.substring(0, 50) + (jenis.deskripsi.length > 50 ? '...' : '') : 'Belum ada deskripsi';
                    const checkboxCell = (canManageJenisInventaris && !jenis.is_virtual)
                        ? `<td><input type="checkbox" class="row-check" value="${jenis.id}" onchange="updateBulkBar()" style="cursor:pointer;accent-color:#064635;"></td>`
                        : '<td></td>';
                    const manageButtons = canManageJenisInventaris
                        ? `<a href="{{ url('jenis-inventaris') }}/${jenis.id}/edit" class="action-btn edit" title="Edit"><i data-feather="edit-2"></i></a>
                           ${jenis.is_virtual ? '' : `<button onclick="confirmDeleteJenis(${jenis.id}, this.getAttribute('data-nama'))" data-nama="${jenis.nama.replace(/&/g, '&amp;').replace(/"/g, '&quot;')}" class="action-btn delete" title="Hapus"><i data-feather="trash-2"></i></button>`}`
                        : '';
                    const rowNumber = (parseInt(from) || 1) + idx;
                    html += `<tr>
                        ${checkboxCell}
                        <td style="text-align: center;">${rowNumber}</td>
                        <td><div class="jenis-cell"><div class="jenis-icon" style="background:${jenis.color_css};"><i data-feather="${jenis.icon}"></i></div><div class="jenis-info"><h6>${jenis.nama}</h6></div></div></td>
                        <td><span class="desc-text">${desc}</span></td>
                        <td style="text-align: center;"><span class="count-badge">${jenis.aset_count}</span></td>
                        <td><div class="action-btns">
                            <a href="{{ url('jenis-inventaris') }}/${jenis.id}" class="action-btn view" title="Lihat"><i data-feather="eye"></i></a>
                            ${manageButtons}
                        </div></td>
                    </tr>`;
                });
                tbody.innerHTML = html;
            }
            function confirmDeleteJenis(id, nama) {
                if (!canManageJenisInventaris) return;
                AppModal.confirmDelete(nama, function() {
                    const form = document.createElement('form');
                    form.method = 'POST';
                    form.action = '/jenis-inventaris/' + id;
                    const csrfToken = document.querySelector('meta[name="csrf-token"]').getAttribute('content');
                    const csrfInput = document.createElement('input');
                    csrfInput.type = 'hidden'; csrfInput.name = '_token'; csrfInput.value = csrfToken;
                    form.appendChild(csrfInput);
                    const methodInput = document.createElement('input');
                    methodInput.type = 'hidden'; methodInput.name = '_method'; methodInput.value = 'DELETE';
                    form.appendChild(methodInput);
                    document.body.appendChild(form);
                    form.submit();
                });
            }

            function toggleAll(src) {
                if (!canManageJenisInventaris) return;
                document.querySelectorAll('.row-check').forEach(cb => { cb.checked = src.checked; });
                updateBulkBar();
            }

            function updateBulkBar() {
                if (!canManageJenisInventaris) return;
                const checked = document.querySelectorAll('.row-check:checked');
                const bar = document.getElementById('bulk-bar');
                if (!bar) return;
                bar.style.display = checked.length > 0 ? 'flex' : 'none';
                document.getElementById('bulk-count').textContent = checked.length;
                const warn = document.getElementById('bulk-warn');
                if (warn) warn.style.display = checked.length > 100 ? 'inline-block' : 'none';
                if (typeof feather !== 'undefined') feather.replace();
            }

            function clearSelection() {
                if (!canManageJenisInventaris) return;
                document.querySelectorAll('.row-check, #check-all').forEach(cb => { cb.checked = false; });
                const bar = document.getElementById('bulk-bar');
                if (bar) bar.style.display = 'none';
            }

            function bulkDeleteConfirm() {
                if (!canManageJenisInventaris) return;
                const checked = Array.from(document.querySelectorAll('.row-check:checked'));
                if (!checked.length) return;
                const toDelete = checked.slice(0, 100);
                const label = toDelete.length < checked.length
                    ? toDelete.length + ' jenis inventaris (dari ' + checked.length + ' terpilih, hanya 100 pertama)'
                    : checked.length + ' jenis inventaris terpilih';
                AppModal.confirmDelete(label, function() {
                    const form = document.getElementById('bulk-delete-form');
                    if (!form) return;
                    form.querySelectorAll('input[name="ids[]"]').forEach(el => el.remove());
                    toDelete.forEach(cb => {
                        const input = document.createElement('input');
                        input.type = 'hidden'; input.name = 'ids[]'; input.value = cb.value;
                        form.appendChild(input);
                    });
                    form.submit();
                });
            }

            document.addEventListener('DOMContentLoaded', function() {
                const urlParams = new URLSearchParams(window.location.search);
                appliedStatus = urlParams.get('status') || 'all';
                appliedSort   = urlParams.get('sort') || 'nama_asc';

                const stRadio = document.querySelector(`.status-filter[value="${appliedStatus}"]`);
                if (stRadio) stRadio.checked = true;

                const srRadio = document.querySelector(`.sort-filter[value="${appliedSort}"]`);
                if (srRadio) srRadio.checked = true;

                const q = urlParams.get('search') || '';
                const input = document.getElementById('searchInput');
                if (input && q) { input.value = q; toggleSearchClear(input); }

                const autoRefreshMs = 15000;
                setInterval(function () {
                    if (document.hidden) return;
                    fetchJenis(true);
                }, autoRefreshMs);
            });
        </script>
    </body>
</html>
