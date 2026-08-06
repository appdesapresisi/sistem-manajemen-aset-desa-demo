<!DOCTYPE html>
<html lang="en">
    <head>
        @include('layouts.head-page-meta', ['title' => 'Pejabat - Sistem Manajemen Aset Desa Demo'])
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

            /* Pejabat Cell */
            .pejabat-cell {
                display: flex;
                align-items: center;
                gap: 14px;
            }
            /* .pejabat-avatar removed */
            .pejabat-info h6 {
                font-size: 14px;
                font-weight: 600;
                color: #1f2937;
                margin: 0 0 2px 0;
            }
            .pejabat-info span {
                font-size: 12px;
                color: #9ca3af;
            }

            /* Jabatan Badge - Removed (jabatan now displayed as text) */
            }

            /* TTD Preview */
            .ttd-preview {
                display: flex;
                align-items: center;
                gap: 10px;
            }
            .ttd-thumb {
                width: 48px;
                height: 32px;
                border-radius: 6px;
                border: 2px solid #e5e7eb;
                background: #fff;
                display: flex;
                align-items: center;
                justify-content: center;
                overflow: hidden;
            }
            .ttd-thumb img {
                max-width: 100%;
                max-height: 100%;
                object-fit: contain;
            }
            .ttd-link {
                font-size: 12px;
                color: #3b82f6;
                text-decoration: underline;
                cursor: pointer;
            }
            .ttd-link:hover { color: #2563eb; }
            .ttd-empty {
                font-size: 12px;
                color: #9ca3af;
                text-decoration: none;
                cursor: default;
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
            .page-btn.disabled {
                opacity: 0.4;
                cursor: not-allowed;
                pointer-events: none;
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

            /* Tugas Column */
            .tugas-text {
                font-size: 13px;
                color: #4b5563;
                max-width: 100%;
                line-height: 1.5;
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
            $canManagePejabat = auth()->user()->isSuperAdmin();
            $currentUserPejabatId = auth()->user()->pejabat_id;
        @endphp

        @include('layouts.modals')
        @include('layouts.notification')
        @include('layouts.layout-vertical')

        <div class="pc-container">
            <div class="pc-content">
                @include('layouts.breadcrumb', ['breadcrumbItemHome' => 'Master Data', 'breadcrumbItem' => 'Pejabat', 'breadcrumbItemActive' => 'List Pejabat'])
 
                <!-- Page Header -->
                <div class="page-header-card">
                    {{-- <h1><i data-feather="award" style="width: 32px; height: 32px; display: inline-block; vertical-align: middle; margin-right: 12px;"></i>Manajemen Pejabat</h1> --}}
                    <h1>Manajemen Pejabat</h1>
                    <p>Kelola data pejabat desa: tugas, jabatan, dan tanda tangan digital.</p>
                </div>

                <!-- Stats Cards -->
                <div class="stats-grid">
                    <div class="stat-card">
                        <div class="stat-card-label">TOTAL PEJABAT</div>
                        <div class="stat-card-value">{{ number_format($stats['totalPejabat'] ?? 0, 0, ',', '.') }}</div>
                        <div class="kpi-trend-wrapper">
                            <span class="kpi-trend-pill neutral">0%</span>
                            <span class="kpi-trend-label">vs. kemarin</span>
                        </div>
                    </div>
                    <div class="stat-card">
                        <div class="stat-card-label">TTD LENGKAP</div>
                        <div class="stat-card-value">{{ number_format($stats['ttdLengkap'] ?? 0, 0, ',', '.') }}</div>
                        <div class="kpi-trend-wrapper">
                            <span class="kpi-trend-pill neutral">0%</span>
                            <span class="kpi-trend-label">vs. kemarin</span>
                        </div>
                    </div>
                    <div class="stat-card">
                        <div class="stat-card-label">JENIS JABATAN</div>
                        <div class="stat-card-value">{{ number_format($stats['totalJabatan'] ?? 0, 0, ',', '.') }}</div>
                        <div class="kpi-trend-wrapper">
                            <span class="kpi-trend-pill neutral">0%</span>
                            <span class="kpi-trend-label">vs. kemarin</span>
                        </div>
                    </div>
                    <div class="stat-card">
                        <div class="stat-card-label">TOTAL DATA</div>
                        <div class="stat-card-value">{{ number_format($pejabats->total() ?? 0, 0, ',', '.') }}</div>
                        <div class="kpi-trend-wrapper">
                            <span class="kpi-trend-pill neutral">0%</span>
                            <span class="kpi-trend-label">vs. kemarin</span>
                        </div>
                    </div>
                </div>

                <!-- Data Card -->
                <div class="data-card">
                    <div class="data-card-header">
                        <div class="data-card-title">
                            <div>
                                <h5>Daftar Pejabat</h5>
                                <span>Total {{ $stats['totalPejabat'] }} pejabat terdaftar</span>
                            </div>
                        </div>
                        <div class="controls-bar">
                            <div class="search-box">
                                <input type="text" id="searchInput" placeholder="Cari pejabat..." oninput="onSearchInput(this)" />
                                <button id="search-icon-btn" onclick="applyFilters()"><i data-feather="search"></i></button>
                                <button id="clear-search-btn" onclick="clearSearch()" style="color:#ef4444;display:none;"><i data-feather="x"></i></button>
                            </div>
                            <div class="dropdown" style="position:relative;">
                                <button class="filter-btn" data-pc-toggle="dropdown" title="Filter">
                                    <i data-feather="sliders"></i>
                                </button>
                                <div class="dropdown-menu dropdown-menu-end py-3 px-4" style="min-width: 240px; z-index: 1050;">
                                    <div class="text-sm font-semibold mb-2" style="color: #064635;">Filter Jabatan / Role</div>
                                    <div class="space-y-2" style="margin-bottom: 12px;">
                                        <label class="flex items-center gap-2 text-sm" style="cursor: pointer;">
                                            <input type="checkbox" class="jabatan-filter" value="Perbekel" style="accent-color: #064635; cursor: pointer;"> Perbekel
                                        </label>
                                        <label class="flex items-center gap-2 text-sm" style="cursor: pointer;">
                                            <input type="checkbox" class="jabatan-filter" value="Sekretaris Desa" style="accent-color: #064635; cursor: pointer;"> Sekretaris Desa
                                        </label>
                                        <label class="flex items-center gap-2 text-sm" style="cursor: pointer;">
                                            <input type="checkbox" class="jabatan-filter" value="Kaur" style="accent-color: #064635; cursor: pointer;"> Kaur
                                        </label>
                                        <label class="flex items-center gap-2 text-sm" style="cursor: pointer;">
                                            <input type="checkbox" class="jabatan-filter" value="Kasi" style="accent-color: #064635; cursor: pointer;"> Kasi
                                        </label>
                                        <label class="flex items-center gap-2 text-sm" style="cursor: pointer;">
                                            <input type="checkbox" class="jabatan-filter" value="Kelian Banjar" style="accent-color: #064635; cursor: pointer;"> Kelian Banjar
                                        </label>
                                        <label class="flex items-center gap-2 text-sm" style="cursor: pointer;">
                                            <input type="checkbox" class="jabatan-filter" value="Staf" style="accent-color: #064635; cursor: pointer;"> Staf
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
                                        <button onclick="applyFilters(); event.stopPropagation();" style="width: 100%; padding: 10px 16px; background: #064635; color: white; border: none; border-radius: 6px; font-size: 14px; font-weight: 500; cursor: pointer; transition: all 0.2s;" onmouseover="this.style.background='#2d3748'" onmouseout="this.style.background='#064635'">
                                            Terapkan Filter
                                        </button>
                                        <button onclick="resetFilters(); event.stopPropagation();" style="width: 100%; padding: 8px 16px; background: transparent; color: #6b7280; border: 1px solid #d1d5db; border-radius: 6px; font-size: 13px; margin-top: 8px; cursor: pointer; transition: all 0.2s;" onmouseover="this.style.background='#f3f4f6'" onmouseout="this.style.background='transparent'">
                                            Reset Filter
                                        </button>
                                    </div>
                                </div>
                            </div>
                            @if($canManagePejabat)
                            <a href="{{ route('pejabat.create') }}" class="add-btn">
                                <i data-feather="plus"></i>
                                Tambah Pejabat
                            </a>
                            @endif
                        </div>
                    </div>
                    @if($canManagePejabat)
                    <!-- Bulk action bar -->
                    <div id="bulk-bar" style="display:none;align-items:center;gap:12px;background:#fef2f2;border:1px solid #fecaca;border-radius:8px;padding:10px 16px;margin:12px 16px 0;flex-wrap:wrap;">
                        <span style="font-size:13px;font-weight:600;color:#dc2626;"><span id="bulk-count">0</span> data terpilih</span>
                        <span id="bulk-warn" style="display:none;font-size:12px;color:#b45309;background:#fef3c7;border:1px solid #fcd34d;border-radius:5px;padding:3px 9px;">&#9888; Hanya 100 pertama yang akan dihapus, pilih ulang untuk sisanya</span>
                        <button type="button" onclick="clearSelection()" style="background:transparent;color:#6b7280;border:1px solid #d1d5db;padding:7px 12px;border-radius:6px;font-size:13px;cursor:pointer;display:inline-flex;align-items:center;gap:5px;"><i data-feather="x" style="width:14px;height:14px;"></i> Batal</button>
                        <button type="button" onclick="bulkDeleteConfirm()" style="background:#dc2626;color:#fff;border:none;padding:7px 14px;border-radius:6px;font-size:13px;font-weight:600;cursor:pointer;display:inline-flex;align-items:center;gap:5px;"><i data-feather="trash-2" style="width:14px;height:14px;"></i> Hapus Terpilih</button>
                    </div>
                    <form id="bulk-delete-form" method="POST" action="{{ route('pejabat.bulk-destroy') }}" style="display:none;">@csrf</form>
                    @endif
                    <div class="data-card-body table-loading-wrap">
                        <table class="modern-table">
                            <thead>
                                <tr>
                                    <th style="width:42px;">@if($canManagePejabat)<input type="checkbox" id="check-all" onchange="toggleAll(this)" style="cursor:pointer;accent-color:#064635;">@endif</th>
                                    <th style="width: 60px; text-align: center;">No</th>
                                    <th style="width: 25%;">Pejabat</th>
                                    <th style="width: 25%;">Jabatan</th>
                                    <th style="width: 35%;">Tugas</th>
                                    <th style="width: 15%;">TTD</th>
                                    <th style="width: 140px;">Aksi</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($pejabats as $index => $pejabat)
                                <tr>
                                    <td>@if($canManagePejabat)<input type="checkbox" class="row-check" value="{{ $pejabat->id }}" onchange="updateBulkBar()" style="cursor:pointer;accent-color:#064635;">@endif</td>
                                    <td style="text-align: center;">{{ $pejabats->firstItem() + $index }}</td>
                                    <td>
                                        <div class="pejabat-cell">
                                            <div class="pejabat-info">
                                                <h6>{{ $pejabat->nama ?? 'Nama tidak tersedia' }}</h6>
                                            </div>
                                        </div>
                                    </td>
                                    <td>
                                        {{ $pejabat->jabatan ?? 'Belum ada jabatan' }}
                                    </td>
                                    <td><span class="tugas-text">{{ $pejabat->tugas ?? '' }}</span></td>
                                    <td>
                                        <div class="ttd-preview">
                                            @if($pejabat->ttd_path)
                                                {{-- <div class="ttd-thumb">
                                                    <i data-feather="file-text" style="width:16px;height:16px;color:#10b981;"></i>
                                                </div> --}}
                                                <a class="ttd-link" href="{{ route('pejabat.ttd.view', $pejabat->id) }}" target="_blank">Lihat TTD</a>
                                            @else
                                                {{-- <div class="ttd-thumb">
                                                    <i data-feather="edit-3" style="width:16px;height:16px;color:#9ca3af;"></i>
                                                </div> --}}
                                                <span class="ttd-empty">Belum ada</span>
                                            @endif
                                        </div>
                                    </td>
                                    <td>
                                        <div class="action-btns">
                                            <a href="{{ route('pejabat.show', $pejabat->id) }}" class="action-btn view" title="Lihat"><i data-feather="eye"></i></a>
                                            @can('update', $pejabat)
                                            <a href="{{ route('pejabat.edit', $pejabat->id) }}" class="action-btn edit" title="Edit"><i data-feather="edit-2"></i></a>
                                            @endcan
                                            @can('delete', $pejabat)
                                            <button class="action-btn delete" title="Hapus" data-nama="{{ $pejabat->nama }}" onclick="confirmDelete({{ $pejabat->id }}, this.getAttribute('data-nama'), {{ $pejabat->users_count ?? 0 }})"><i data-feather="trash-2"></i></button>
                                            @endcan
                                        </div>
                                    </td>
                                </tr>
                                @empty
                                <tr>
                                    <td colspan="7" style="text-align:center;padding:40px;color:#9ca3af;">
                                        <div style="display:flex;flex-direction:column;align-items:center;justify-content:center;text-align:center;gap:8px;">
                                            <i data-feather="inbox" style="width:48px;height:48px;opacity:0.5;"></i>
                                            <p style="margin:0;font-size:15px;">Belum ada data pejabat</p>
                                        </div>
                                    </td>
                                </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                    <!-- Pagination -->
                    <div class="pagination-bar">
                        <div class="pagination-info">
                            Menampilkan <strong>{{ $pejabats->firstItem() ?? 0 }}-{{ $pejabats->lastItem() ?? 0 }}</strong> dari <strong>{{ $pejabats->total() }}</strong> data
                        </div>
                        <nav class="pagination-nav">
                            @if ($pejabats->onFirstPage())
                                <span class="page-btn disabled" title="Previous"><i data-feather="chevron-left"></i></span>
                            @else
                                <a href="{{ $pejabats->previousPageUrl() }}" class="page-btn" title="Previous"><i data-feather="chevron-left"></i></a>
                            @endif

                            @php
                                $currentPage  = $pejabats->currentPage();
                                $lastPage     = $pejabats->lastPage();
                                $windowStart  = max(1, $currentPage - 2);
                                $windowEnd    = min($lastPage, $currentPage + 2);
                                if (($windowEnd - $windowStart + 1) < 5) {
                                    if ($windowStart === 1) {
                                        $windowEnd = min($lastPage, 5);
                                    } else {
                                        $windowStart = max(1, $windowEnd - 4);
                                    }
                                }
                                $pageUrls = $pejabats->getUrlRange($windowStart, $windowEnd);
                            @endphp
                            @foreach ($pageUrls as $page => $url)
                                @if ($page == $currentPage)
                                    <span class="page-btn active">{{ $page }}</span>
                                @else
                                    <a href="{{ $url }}" class="page-btn">{{ $page }}</a>
                                @endif
                            @endforeach

                            @if ($pejabats->hasMorePages())
                                <a href="{{ $pejabats->nextPageUrl() }}" class="page-btn" title="Next"><i data-feather="chevron-right"></i></a>
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
                                max="{{ $pejabats->lastPage() }}"
                                placeholder="1–{{ $pejabats->lastPage() }}"
                                title="Masukkan nomor halaman (1–{{ $pejabats->lastPage() }})"
                            >
                        </div>
                        <div class="pagination-info">
                            Total <strong>{{ $pejabats->total() }}</strong> pejabat
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
                <span id="success-message" class="font-medium">Pejabat berhasil ditambahkan!</span>
                <button onclick="closeToast()" class="ml-2 hover:opacity-80">
                    <i data-feather="x" style="width:18px;height:18px;"></i>
                </button>
            </div>
        </div>

        <script>
            const canManagePejabat = @json($canManagePejabat);
            const currentUserPejabatId = @json($currentUserPejabatId);

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
            })();            // Search and Filter functionality
            function applyFilters(isAutoRefresh = false) {
                const searchValue = document.getElementById('searchInput').value;
                const selectedJabatan = Array.from(document.querySelectorAll('.jabatan-filter:checked')).map(cb => cb.value);
                const selectedSort = document.querySelector('.sort-filter:checked')?.value || 'nama_asc';
                
                // Build query params
                const params = new URLSearchParams();
                if (searchValue) params.append('search', searchValue);
                if (selectedJabatan.length > 0) {
                    selectedJabatan.forEach(jab => params.append('jabatan[]', jab));
                }
                if (selectedSort !== 'nama_asc') params.append('sort', selectedSort);
                if (isAutoRefresh) {
                    const urlParams = new URLSearchParams(window.location.search);
                    const page = urlParams.get('page');
                    if (page) params.set('page', page);
                }
                
                history.replaceState(null, '', window.location.pathname + (params.toString() ? '?' + params.toString() : ''));

                // Fetch filtered data
                const wrap = document.querySelector('.table-loading-wrap');
                if (!isAutoRefresh && wrap) wrap.classList.add('is-loading');
                fetch(`{{ route('pejabat.index') }}?${params.toString()}`, {
                    headers: {
                        'X-Requested-With': 'XMLHttpRequest',
                        'Accept': 'application/json'
                    }
                })
                .then(response => response.json())
                .then(data => {
                    updateStats(data.stats);
                    updateTable(data.pejabats, data.pagination);
                    if(typeof feather !== 'undefined') feather.replace();
                })
                .catch(error => console.error('Error:', error))
                .finally(() => {
                    const wrap = document.querySelector('.table-loading-wrap');
                    if (wrap) wrap.classList.remove('is-loading');
                });
            }
            
            function toggleSearchClear(input) {
                document.getElementById('search-icon-btn').style.display = input.value ? 'none' : '';
                document.getElementById('clear-search-btn').style.display = input.value ? '' : 'none';
            }
            let _searchTimer = null;
            function onSearchInput(input) {
                toggleSearchClear(input);
                clearTimeout(_searchTimer);
                _searchTimer = setTimeout(function() { applyFilters(); }, 400);
            }
            function clearSearch() {
                const input = document.getElementById('searchInput');
                input.value = '';
                toggleSearchClear(input);
                applyFilters();
            }
            function resetFilters() {
                // Clear search input
                const searchInput = document.getElementById('searchInput');
                searchInput.value = '';
                toggleSearchClear(searchInput);

                // Uncheck all jabatan filters
                document.querySelectorAll('.jabatan-filter').forEach(cb => cb.checked = false);
                
                // Reset sort to 'nama_asc'
                const defaultSort = document.querySelector('.sort-filter[value="nama_asc"]');
                if (defaultSort) defaultSort.checked = true;
                
                // Apply filters (which will show all data)
                applyFilters();
            }
            
            function updateStats(stats) {
                if (!stats) return;
                const statCards = document.querySelectorAll('.stat-card');
                if (statCards[0]) {
                    const el = statCards[0].querySelector('.stat-card-value');
                    if (el && stats.totalPejabat !== undefined) el.textContent = (stats.totalPejabat || 0).toLocaleString('id-ID');
                }
                if (statCards[1]) {
                    const el = statCards[1].querySelector('.stat-card-value');
                    if (el && stats.ttdLengkap !== undefined) el.textContent = (stats.ttdLengkap || 0).toLocaleString('id-ID');
                }
                if (statCards[2]) {
                    const el = statCards[2].querySelector('.stat-card-value');
                    if (el && stats.totalJabatan !== undefined) el.textContent = (stats.totalJabatan || 0).toLocaleString('id-ID');
                }
                if (statCards[3]) {
                    const el = statCards[3].querySelector('.stat-card-value');
                    if (el && stats.totalPejabat !== undefined) el.textContent = (stats.totalPejabat || 0).toLocaleString('id-ID');
                }
                
                // Update title
                const titleSpan = document.querySelector('.data-card-title span');
                if (titleSpan && stats.totalPejabat !== undefined) titleSpan.textContent = `Total ${stats.totalPejabat} pejabat terdaftar`;
            }
            
            function updateTable(pejabats, pagination) {
                const tbody = document.querySelector('.modern-table tbody');
                if (!tbody) return;
                
                const firstItem = pagination ? pagination.firstItem : 1;
                const lastItem = pagination ? (firstItem + pejabats.length - 1) : pejabats.length;
                const total = (pagination && pagination.total !== undefined) ? pagination.total : pejabats.length;

                // Update pagination info DOM
                const paginationInfos = document.querySelectorAll('.pagination-info');
                if (paginationInfos[0]) paginationInfos[0].innerHTML = 'Menampilkan <strong>' + (firstItem||0) + '–' + (lastItem||0) + '</strong> dari <strong>' + total + '</strong> data';
                if (paginationInfos[1]) paginationInfos[1].innerHTML = 'Total <strong>' + total + '</strong> pejabat';

                if (pejabats.length === 0) {
                    tbody.innerHTML = `
                        <tr>
                            <td colspan="7" style="text-align:center;padding:40px;color:#9ca3af;">
                                <div style="display:flex;flex-direction:column;align-items:center;justify-content:center;text-align:center;gap:8px;">
                                    <i data-feather="inbox" style="width:48px;height:48px;opacity:0.5;"></i>
                                    <p style="margin:0;">Tidak ada data pejabat</p>
                                </div>
                            </td>
                        </tr>
                    `;
                    return;
                }
                
                let html = '';
                pejabats.forEach((pejabat, index) => {
                    const ttdHtml = pejabat.ttd_path 
                        ? `<a class="ttd-link" href="/pejabat/${pejabat.id}/ttd" target="_blank">Lihat TTD</a>`
                        : `<span class="ttd-empty">Belum ada</span>`;
                    
                    const rowNumber = firstItem + index;
                    const checkboxCell = canManagePejabat
                        ? `<td><input type="checkbox" class="row-check" value="${pejabat.id}" onchange="updateBulkBar()" style="cursor:pointer;accent-color:#064635;"></td>`
                        : '<td></td>';
                    const canEditPejabat = canManagePejabat || (currentUserPejabatId !== null && Number(currentUserPejabatId) === Number(pejabat.id));
                    const editButton = canEditPejabat
                        ? `<a href="/pejabat/${pejabat.id}/edit" class="action-btn edit" title="Edit"><i data-feather="edit-2"></i></a>`
                        : '';
                    const deleteButton = canManagePejabat
                        ? `<button class="action-btn delete" title="Hapus" data-nama="${pejabat.nama.replace(/&/g, '&amp;').replace(/"/g, '&quot;')}" onclick="confirmDelete(${pejabat.id}, this.getAttribute('data-nama'), ${pejabat.users_count || 0})"><i data-feather="trash-2"></i></button>`
                        : '';
                    
                    html += `
                        <tr>
                            ${checkboxCell}
                            <td style="text-align:center;">${rowNumber}</td>
                            <td>
                                <div class="pejabat-cell">
                                    <div class="pejabat-info">
                                        <h6>${pejabat.nama}</h6>
                                    </div>
                                </div>
                            </td>
                            <td>
                                ${pejabat.jabatan}
                            </td>
                            <td><span class="tugas-text">${pejabat.tugas || ''}</span></td>
                            <td>
                                <div class="ttd-preview">
                                    ${ttdHtml}
                                </div>
                            </td>
                            <td>
                                <div class="action-btns">
                                    <a href="/pejabat/${pejabat.id}" class="action-btn view" title="Lihat"><i data-feather="eye"></i></a>
                                    ${editButton}
                                    ${deleteButton}
                                </div>
                            </td>
                        </tr>
                    `;
                });
                tbody.innerHTML = html;
            }
            
            // Search on Enter key
            document.addEventListener('DOMContentLoaded', function() {
                // debounce live search already applied via onSearchInput
 
                const autoRefreshMs = 15000;
                setInterval(function () {
                    if (document.hidden) return;
                    applyFilters(true);
                }, autoRefreshMs);
            });
            
            // Check for success message from pejabat-form
            (function(){
                const success = sessionStorage.getItem('pejabatFormSuccess');
                const message = sessionStorage.getItem('pejabatFormMessage');
                
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
                    sessionStorage.removeItem('pejabatFormSuccess');
                    sessionStorage.removeItem('pejabatFormMessage');
                    
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

            // Delete confirmation
            function confirmDelete(id, nama, usersCount = 0) {
                if (!canManagePejabat) return;
                if (usersCount > 0) {
                    AppModal.confirm(
                        'Hapus Pejabat',
                        `Pejabat <strong>${nama}</strong> masih terhubung ke <strong>${usersCount} user</strong>. Menghapus pejabat ini akan memutus tautan tersebut, namun akun user tetap ada. Lanjutkan?`,
                        function() {
                            const form = document.createElement('form');
                            form.method = 'POST';
                            form.action = '/pejabat/' + id;
                            const csrfToken = document.querySelector('meta[name="csrf-token"]').getAttribute('content');
                            const csrfInput = document.createElement('input');
                            csrfInput.type = 'hidden'; csrfInput.name = '_token'; csrfInput.value = csrfToken;
                            form.appendChild(csrfInput);
                            const methodInput = document.createElement('input');
                            methodInput.type = 'hidden'; methodInput.name = '_method'; methodInput.value = 'DELETE';
                            form.appendChild(methodInput);
                            document.body.appendChild(form);
                            form.submit();
                        }
                    );
                } else {
                    AppModal.confirmDelete(nama, function() {
                        const form = document.createElement('form');
                        form.method = 'POST';
                        form.action = '/pejabat/' + id;
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
            }

            function toggleAll(src) {
                if (!canManagePejabat) return;
                document.querySelectorAll('.row-check').forEach(cb => { cb.checked = src.checked; });
                updateBulkBar();
            }
            function updateBulkBar() {
                if (!canManagePejabat) return;
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
                if (!canManagePejabat) return;
                document.querySelectorAll('.row-check, #check-all').forEach(cb => { cb.checked = false; });
                const bar = document.getElementById('bulk-bar');
                if (bar) bar.style.display = 'none';
            }
            function bulkDeleteConfirm() {
                if (!canManagePejabat) return;
                const checked = Array.from(document.querySelectorAll('.row-check:checked'));
                if (!checked.length) return;
                const toDelete = checked.slice(0, 100);
                const label = toDelete.length < checked.length
                    ? toDelete.length + ' data pejabat (dari ' + checked.length + ' terpilih, hanya 100 pertama)'
                    : checked.length + ' data pejabat terpilih';
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
        </script>
    </body>
</html>
