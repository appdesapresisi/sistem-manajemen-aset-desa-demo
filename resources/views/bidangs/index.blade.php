<!DOCTYPE html>
<html lang="en">
    <head>
        @include('layouts.head-page-meta', ['title' => 'Bidang - Sistem Manajemen Aset Desa Demo'])
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
            .page-header-card::after {
                content: '';
                position: absolute;
                bottom: -30%;
                left: -10%;
                width: 300px;
                height: 300px;
                background: radial-gradient(circle, rgba(255,255,255,0.05) 0%, transparent 60%);
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

            /* Bidang Cell */
            .bidang-cell {
                display: flex;
                align-items: center;
                gap: 14px;
            }
            .bidang-avatar {
                width: 44px;
                height: 44px;
                border-radius: 12px;
                display: flex;
                align-items: center;
                justify-content: center;
                font-weight: 600;
                font-size: 15px;
                color: #fff;
                flex-shrink: 0;
            }
            .bidang-info h6 {
                font-size: 14px;
                font-weight: 600;
                color: #1f2937;
                margin: 0;
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
                @include('layouts.breadcrumb', [
                    'breadcrumbItemHome' => 'Master Data',
                    'breadcrumbItem' => 'Bidang',
                    'breadcrumbItemActive' => 'List Bidang'
                ])
 
                <!-- Page Header -->
                <div class="page-header-card">
                    <h1>Master Data Bidang</h1>
                    <p>Kelola data bidang di desa, atur struktur organisasi dengan mudah.</p>
                </div>

                <!-- Stats Cards -->
                <div class="stats-grid">
                    <div class="stat-card">
                        <div class="stat-card-label">TOTAL BIDANG</div>
                        <div class="stat-card-value">{{ number_format($stats['totalBidangs'] ?? 0, 0, ',', '.') }}</div>
                        <div class="kpi-trend-wrapper">
                            <span class="kpi-trend-pill neutral">0%</span>
                            <span class="kpi-trend-label">vs. kemarin</span>
                        </div>
                    </div>
                    <div class="stat-card">
                        <div class="stat-card-label">TOTAL USER</div>
                        <div class="stat-card-value">{{ number_format($stats['totalUsers'] ?? 0, 0, ',', '.') }}</div>
                        <div class="kpi-trend-wrapper">
                            <span class="kpi-trend-pill neutral">0%</span>
                            <span class="kpi-trend-label">vs. kemarin</span>
                        </div>
                    </div>
                    <div class="stat-card">
                        <div class="stat-card-label">BIDANG TERISI USER</div>
                        <div class="stat-card-value">{{ number_format($stats['bidangWithUsers'] ?? 0, 0, ',', '.') }}</div>
                        <div class="kpi-trend-wrapper">
                            <span class="kpi-trend-pill neutral">0%</span>
                            <span class="kpi-trend-label">vs. kemarin</span>
                        </div>
                    </div>
                    <div class="stat-card">
                        <div class="stat-card-label">BIDANG USER TERBANYAK</div>
                        <div class="stat-card-value" style="font-size: 18px; line-height: 1.3; font-weight: 700; white-space: nowrap; overflow: hidden; text-overflow: ellipsis;" title="{{ $stats['topBidangNama'] ?? '-' }}">{{ $stats['topBidangNama'] ?? '-' }}</div>
                        <div class="kpi-trend-wrapper">
                            <span class="kpi-trend-pill up" style="background:#e0e7ff; color:#3730a3;">
                                {{ number_format($stats['topBidangCount'] ?? 0, 0, ',', '.') }} user
                            </span>
                            <span class="kpi-trend-label">terbanyak</span>
                        </div>
                    </div>
                </div>

                <div class="data-card">
                    <div class="data-card-header">
                        <div class="data-card-title">
                            <div>
                                <h5>Daftar Bidang</h5>
                                <span>Total {{ $stats['totalBidangs'] }} bidang terdaftar</span>
                            </div>
                        </div>
                        <div class="controls-bar">
                            <form method="GET" action="{{ route('bidangs.index') }}" style="display: flex; align-items: center; gap: 8px;">
                                <div class="search-box">
                                    <input 
                                        type="text" 
                                        name="search" 
                                        id="searchInput" 
                                        placeholder="Cari bidang..." 
                                        value="{{ request('search') }}"
                                        oninput="toggleClearBtn(this)"
                                    />
                                    <button type="submit" id="search-icon-btn" {{ request('search') ? 'style=display:none' : '' }}>
                                        <i data-feather="search"></i>
                                    </button>
                                    <button type="button" id="clear-search-btn" onclick="clearSearch()" style="color: #ef4444; {{ request('search') ? '' : 'display:none;' }}">
                                        <i data-feather="x"></i>
                                    </button>
                                </div>
                            </form>

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
                                            <input type="radio" name="statusFilter" value="with_users" class="status-filter" style="accent-color: #064635; cursor: pointer;"> Memiliki User
                                        </label>
                                        <label class="flex items-center gap-2 text-sm" style="cursor: pointer;">
                                            <input type="radio" name="statusFilter" value="no_users" class="status-filter" style="accent-color: #064635; cursor: pointer;"> Tanpa User (Kosong)
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

                            @if(auth()->user()->isSuperAdmin())
                                <a href="{{ route('bidangs.create') }}" class="add-btn">
                                    <i data-feather="plus"></i> Tambah Bidang
                                </a>
                            @endif
                        </div>
                    </div>

                    <div class="data-card-body table-loading-wrap" id="table-wrap">
                        @if(auth()->user()->isSuperAdmin())
                            <div id="bulk-bar" style="display:none;align-items:center;gap:12px;background:#fef2f2;border:1px solid #fecaca;border-radius:8px;padding:10px 16px;margin:12px 16px 0;flex-wrap:wrap;">
                                <span style="font-size:13px;font-weight:600;color:#dc2626;"><span id="bulk-count">0</span> data terpilih</span>
                                <button type="button" onclick="clearSelection()" style="background:transparent;color:#6b7280;border:1px solid #d1d5db;padding:7px 12px;border-radius:6px;font-size:13px;cursor:pointer;display:inline-flex;align-items:center;gap:5px;"><i data-feather="x" style="width:14px;height:14px;"></i> Batal</button>
                                <button type="button" onclick="bulkDelete()" style="background:#dc2626;color:#fff;border:none;padding:7px 14px;border-radius:6px;font-size:13px;font-weight:600;cursor:pointer;display:inline-flex;align-items:center;gap:5px;"><i data-feather="trash-2" style="width:14px;height:14px;"></i> Hapus Terpilih</button>
                            </div>
                        @endif
                        
                        <div style="overflow-x: auto;">
                            <table class="modern-table">
                                <thead>
                                    <tr>
                                        @if(auth()->user()->isSuperAdmin())
                                            <th style="width: 60px;">
                                                <input type="checkbox" id="select-all" style="accent-color: #064635; cursor: pointer;">
                                            </th>
                                        @endif
                                        <th style="width: 60px; text-align: center;">No</th>
                                        <th style="width: 450px;">Nama Bidang</th>
                                        <th>Deskripsi</th>
                                        <th style="width: 150px; text-align: left;">Aksi</th>
                                    </tr>
                                </thead>
                                <tbody id="table-body">
                                    @forelse($bidangs as $index => $bidang)
                                        <tr>
                                            @if(auth()->user()->isSuperAdmin())
                                                <td>
                                                    <input type="checkbox" class="row-checkbox" value="{{ $bidang->id }}" style="accent-color: #064635; cursor: pointer;">
                                                </td>
                                            @endif
                                            <td style="text-align: center;">{{ $bidangs->firstItem() + $index }}</td>
                                            <td>
                                                <div class="bidang-cell">
                                                    <div class="bidang-info">
                                                        <h6>{{ $bidang->nama }}</h6>
                                                    </div>
                                                </div>
                                            </td>
                                            <td>
                                                @if($bidang->deskripsi)
                                                    <span style="font-size: 13px; color: #64748b; line-height: 1.5;">
                                                        {{ Str::limit($bidang->deskripsi, 80) }}
                                                    </span>
                                                @else
                                                    <span style="font-size: 13px; color: #cbd5e1; font-style: italic;">-</span>
                                                @endif
                                            </td>
                                            <td>
                                                <div class="action-btns" style="justify-content: center;">
                                                    <a href="{{ route('bidangs.show', $bidang->id) }}" class="action-btn view" title="Lihat Detail">
                                                        <i data-feather="eye"></i>
                                                    </a>
                                                    @if(auth()->user()->isSuperAdmin())
                                                        <a href="{{ route('bidangs.edit', $bidang->id) }}" class="action-btn edit" title="Edit">
                                                            <i data-feather="edit-2"></i>
                                                        </a>
                                                        <button class="action-btn delete" title="Hapus" onclick="confirmDelete({{ $bidang->id }}, '{{ $bidang->nama }}', {{ $bidang->users_count }})">
                                                            <i data-feather="trash-2"></i>
                                                        </button>
                                                    @endif
                                                </div>
                                            </td>
                                        </tr>
                                    @empty
                                        <tr>
                                            <td colspan="{{ auth()->user()->isSuperAdmin() ? '5' : '4' }}" style="text-align: center; padding: 40px 20px; color: #9ca3af;">
                                                <div style="display:flex;flex-direction:column;align-items:center;justify-content:center;text-align:center;gap:8px;">
                                                    <i data-feather="inbox" style="width: 48px; height: 48px; opacity: 0.5;"></i>
                                                    <p style="margin: 0; font-size: 14px;">Tidak ada data bidang</p>
                                                </div>
                                            </td>
                                        </tr>
                                    @endforelse
                                </tbody>
                            </table>
                        </div>
                    </div>
                    
                    <!-- Pagination Bar -->
                    <div class="pagination-bar">
                        <div class="pagination-left">
                            <div class="page-size-wrap">
                                <span>Tampilkan</span>
                                <select id="perPageSelect" class="per-page-select" onchange="changePerPage(this.value)">
                                    <option value="10" {{ ($bidangs->perPage() == 10) ? 'selected' : '' }}>10</option>
                                    <option value="20" {{ ($bidangs->perPage() == 20) ? 'selected' : '' }}>20</option>
                                    <option value="50" {{ ($bidangs->perPage() == 50) ? 'selected' : '' }}>50</option>
                                    <option value="100" {{ ($bidangs->perPage() == 100) ? 'selected' : '' }}>100</option>
                                </select>
                                <span>data</span>
                            </div>
                        </div>

                        <div class="pagination-center">
                            <nav class="pagination-nav">
                                {{-- Previous Page Link --}}
                                @if ($bidangs->onFirstPage())
                                    <span class="page-btn disabled" title="Previous"><i data-feather="chevron-left"></i></span>
                                @else
                                    <a href="{{ $bidangs->previousPageUrl() }}" class="page-btn" title="Previous"><i data-feather="chevron-left"></i></a>
                                @endif

                                {{-- Pagination Elements --}}
                                @php
                                    $currentPage  = $bidangs->currentPage();
                                    $lastPage     = $bidangs->lastPage();
                                    $windowStart  = max(1, $currentPage - 2);
                                    $windowEnd    = min($lastPage, $currentPage + 2);
                                    if (($windowEnd - $windowStart + 1) < 5) {
                                        if ($windowStart === 1) {
                                            $windowEnd = min($lastPage, 5);
                                        } else {
                                            $windowStart = max(1, $windowEnd - 4);
                                        }
                                    }
                                    $pageUrls = $bidangs->getUrlRange($windowStart, $windowEnd);
                                @endphp
                                @foreach ($pageUrls as $page => $url)
                                    @if ($page == $currentPage)
                                        <span class="page-btn active">{{ $page }}</span>
                                    @else
                                        <a href="{{ $url }}" class="page-btn">{{ $page }}</a>
                                    @endif
                                @endforeach

                                {{-- Next Page Link --}}
                                @if ($bidangs->hasMorePages())
                                    <a href="{{ $bidangs->nextPageUrl() }}" class="page-btn" title="Next"><i data-feather="chevron-right"></i></a>
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
                                    max="{{ $bidangs->lastPage() }}"
                                    value="{{ $bidangs->currentPage() }}"
                                    placeholder="1–{{ $bidangs->lastPage() }}"
                                    title="Masukkan nomor halaman (1–{{ $bidangs->lastPage() }})"
                                >
                            </div>
                        </div>

                        <div class="pagination-right" id="paginationRangeInfo">
                            Menampilkan <strong>{{ $bidangs->firstItem() ?? 0 }}-{{ $bidangs->lastItem() ?? 0 }}</strong> dari <strong>{{ $bidangs->total() }}</strong> data
                        </div>
                    </div>
                </div>

            </div>
        </div>

        @include('layouts.footer-block')
        @include('layouts.footer-js')
        
        <script>
            // ─── Search / Clear ───────────────────────────────────────────
            function toggleClearBtn(input) {
                const clearBtn  = document.getElementById('clear-search-btn');
                const searchBtn = document.getElementById('search-icon-btn');
                if (clearBtn)  clearBtn.style.display  = input.value ? 'block' : 'none';
                if (searchBtn) searchBtn.style.display = input.value ? 'none'  : 'block';
            }

            function onSearchInput(input) {
                toggleClearBtn(input);
                clearTimeout(window._searchTimer);
                window._searchTimer = setTimeout(function () { fetchBidangs(false, 1); }, 400);
            }

            function clearSearch() {
                const input = document.getElementById('searchInput');
                input.value = '';
                toggleClearBtn(input);
                fetchBidangs(false, 1);
            }

            // ─── Per-page & Page helpers ──────────────────────────────────
            function changePerPage(val) {
                fetchBidangs(false, 1);
            }

            function goToPage(page) {
                fetchBidangs(false, page);
            }

            // ─── Page jump (Enter key) ────────────────────────────────────
            (function () {
                const jumpInput = document.getElementById('pageJumpInput');
                if (!jumpInput) return;
                jumpInput.addEventListener('keydown', function (e) {
                    if (e.key !== 'Enter') return;
                    e.preventDefault();
                    const val     = parseInt(this.value, 10);
                    const minPage = parseInt(this.min || 1, 10);
                    const maxPage = parseInt(this.max || 1, 10);
                    if (isNaN(val) || val < minPage || val > maxPage) {
                        this.value = '';
                        return;
                    }
                    goToPage(val);
                });
            })();

            let appliedStatus = 'all';
            let appliedSort   = 'nama_asc';

            // ─── Core AJAX fetch ──────────────────────────────────────────
            function fetchBidangs(isAutoRefresh = false, pageOverride = null) {
                const q            = (document.getElementById('searchInput')?.value || '').trim();
                const perPageEl    = document.getElementById('perPageSelect');
                const perPageValue = perPageEl ? perPageEl.value : '10';

                const params       = new URLSearchParams();

                if (q) params.set('search', q);
                if (appliedStatus && appliedStatus !== 'all') params.set('status', appliedStatus);
                if (appliedSort && appliedSort !== 'nama_asc') params.set('sort', appliedSort);
                if (perPageValue && perPageValue !== '10') params.set('per_page', perPageValue);

                const urlParams  = new URLSearchParams(window.location.search);
                const currentPage = pageOverride !== null ? pageOverride : (urlParams.get('page') || 1);
                if (currentPage > 1) params.set('page', currentPage);

                history.replaceState(null, '', window.location.pathname + (params.toString() ? '?' + params.toString() : ''));

                const wrap = document.querySelector('.table-loading-wrap');
                if (!isAutoRefresh && wrap) wrap.classList.add('is-loading');

                fetch('{{ route('bidangs.index') }}?' + params.toString(), {
                    headers: { 'X-Requested-With': 'XMLHttpRequest', 'Accept': 'application/json' }
                })
                .then(r => r.json())
                .then(data => {
                    updateStats(data.stats);
                    updateTable(data.bidangs, data.pagination);
                    updatePaginationUI(data.pagination);
                    if (typeof feather !== 'undefined') feather.replace();
                })
                .catch(err => console.error('fetchBidangs error:', err))
                .finally(() => { if (wrap) wrap.classList.remove('is-loading'); });
            }

            function applyFilters() {
                appliedStatus = document.querySelector('.status-filter:checked')?.value || 'all';
                appliedSort   = document.querySelector('.sort-filter:checked')?.value || 'nama_asc';
                fetchBidangs(false, 1);
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

                fetchBidangs(false, 1);
                document.querySelectorAll('.dropdown-menu.show').forEach(el => el.classList.remove('show'));
                document.querySelectorAll('.dropdown.show').forEach(el => el.classList.remove('show'));
            }

            // ─── Update stats cards ───────────────────────────────────────
            function updateStats(stats) {
                if (!stats) return;
                const cards = document.querySelectorAll('.stat-card');
                if (cards[0]) {
                    const valEl = cards[0].querySelector('.stat-card-value');
                    if (valEl) valEl.textContent = (stats.totalBidangs ?? 0).toLocaleString('id-ID');
                }
                if (cards[1]) {
                    const valEl = cards[1].querySelector('.stat-card-value');
                    if (valEl) valEl.textContent = (stats.totalUsers ?? 0).toLocaleString('id-ID');
                }
                if (cards[2]) {
                    const valEl = cards[2].querySelector('.stat-card-value');
                    if (valEl) valEl.textContent = (stats.bidangWithUsers ?? 0).toLocaleString('id-ID');
                }
                if (cards[3]) {
                    const valEl = cards[3].querySelector('.stat-card-value');
                    if (valEl && stats.topBidangNama !== undefined) {
                        valEl.textContent = stats.topBidangNama;
                        valEl.title = stats.topBidangNama;
                    }
                    const pill = cards[3].querySelector('.kpi-trend-pill');
                    if (pill && stats.topBidangCount !== undefined) {
                        pill.textContent = (stats.topBidangCount || 0).toLocaleString('id-ID') + ' user';
                    }
                }
                const titleSpan = document.querySelector('.data-card-title span');
                if (titleSpan && stats.totalBidangs !== undefined) {
                    titleSpan.textContent = 'Total ' + stats.totalBidangs + ' bidang terdaftar';
                }
            }

            // ─── Update table rows ────────────────────────────────────────
            function updateTable(bidangs, pagination) {
                const tbody    = document.querySelector('#table-body');
                if (!tbody) return;
                const firstItem = pagination?.firstItem ?? 1;
                const isSuperAdmin = {{ auth()->user()->isSuperAdmin() ? 'true' : 'false' }};
                const colspan   = isSuperAdmin ? 5 : 4;

                if (!bidangs || bidangs.length === 0) {
                    tbody.innerHTML = `<tr><td colspan="${colspan}" style="text-align:center;padding:40px 20px;color:#9ca3af;">
                        <div style="display:flex;flex-direction:column;align-items:center;gap:8px;">
                            <i data-feather="inbox" style="width:48px;height:48px;opacity:0.5;"></i>
                            <p style="margin:0;font-size:14px;">Tidak ada data bidang</p>
                        </div></td></tr>`;
                    return;
                }

                let html = '';
                bidangs.forEach((bidang, idx) => {
                    const no   = firstItem + idx;
                    const desc = bidang.deskripsi
                        ? `<span style="font-size:13px;color:#64748b;line-height:1.5;">${bidang.deskripsi.substring(0, 80)}${bidang.deskripsi.length > 80 ? '…' : ''}</span>`
                        : `<span style="font-size:13px;color:#cbd5e1;font-style:italic;">-</span>`;

                    const chkHtml = isSuperAdmin
                        ? `<td><input type="checkbox" class="row-checkbox" value="${bidang.id}" style="accent-color:#064635;cursor:pointer;"></td>`
                        : '';

                    const actionsAdmin = isSuperAdmin
                        ? `<a href="{{ url('bidangs') }}/${bidang.id}/edit" class="action-btn edit" title="Edit"><i data-feather="edit-2"></i></a>
                           <button class="action-btn delete" title="Hapus" onclick="confirmDelete(${bidang.id}, '${(bidang.nama||'').replace(/'/g,"\\'")}', ${bidang.users_count ?? 0})"><i data-feather="trash-2"></i></button>`
                        : '';

                    html += `<tr>
                        ${chkHtml}
                        <td style="text-align:center;">${no}</td>
                        <td><div class="bidang-cell"><div class="bidang-info"><h6>${bidang.nama}</h6></div></div></td>
                        <td>${desc}</td>
                        <td><div class="action-btns" style="justify-content:center;">
                            <a href="{{ url('bidangs') }}/${bidang.id}" class="action-btn view" title="Lihat Detail"><i data-feather="eye"></i></a>
                            ${actionsAdmin}
                        </div></td>
                    </tr>`;
                });
                tbody.innerHTML = html;

                // Re-attach bulk checkbox listeners
                if (isSuperAdmin) attachBulkListeners();
            }

            // ─── Update pagination UI ─────────────────────────────────────
            function updatePaginationUI(pagination) {
                if (!pagination) return;
                const firstItem   = pagination.firstItem   || 0;
                const lastItem    = pagination.lastItem    || 0;
                const total       = pagination.total       || 0;
                const currentPage = pagination.currentPage || 1;
                const lastPage    = pagination.lastPage    || 1;

                // Range info
                const rangeEl = document.getElementById('paginationRangeInfo');
                if (rangeEl) {
                    rangeEl.innerHTML = `Menampilkan <strong>${firstItem}-${lastItem}</strong> dari <strong>${total}</strong> data`;
                }

                // Jump input
                const jumpInput = document.getElementById('pageJumpInput');
                if (jumpInput) {
                    jumpInput.max         = lastPage;
                    jumpInput.placeholder = `1–${lastPage}`;
                    jumpInput.title       = `Masukkan nomor halaman (1–${lastPage})`;
                    jumpInput.value       = currentPage;
                }

                // Nav buttons
                const nav = document.getElementById('paginationNav');
                if (!nav) return;
                let html = '';

                html += currentPage <= 1
                    ? `<span class="page-btn disabled" title="Previous"><i data-feather="chevron-left"></i></span>`
                    : `<button type="button" class="page-btn" onclick="goToPage(${currentPage - 1})" title="Previous"><i data-feather="chevron-left"></i></button>`;

                let ws = Math.max(1, currentPage - 2);
                let we = Math.min(lastPage, currentPage + 2);
                if ((we - ws + 1) < 5) { if (ws === 1) { we = Math.min(lastPage, 5); } else { ws = Math.max(1, we - 4); } }
                for (let p = ws; p <= we; p++) {
                    html += p === currentPage
                        ? `<span class="page-btn active">${p}</span>`
                        : `<button type="button" class="page-btn" onclick="goToPage(${p})">${p}</button>`;
                }

                html += currentPage >= lastPage
                    ? `<span class="page-btn disabled" title="Next"><i data-feather="chevron-right"></i></span>`
                    : `<button type="button" class="page-btn" onclick="goToPage(${currentPage + 1})" title="Next"><i data-feather="chevron-right"></i></button>`;

                nav.innerHTML = html;
            }

            // ─── Delete ───────────────────────────────────────────────────
            function confirmDelete(id, nama, usersCount) {
                if (usersCount > 0) {
                    AppModal.error('Tidak Dapat Dihapus',
                        `Bidang <strong>${nama}</strong> masih memiliki <strong>${usersCount} user</strong> yang terkait.<br><br>Silakan pindahkan atau hapus user tersebut terlebih dahulu.`);
                    return;
                }
                AppModal.confirmDelete(nama, function() {
                    const form = document.createElement('form');
                    form.method = 'POST';
                    form.action = `/bidangs/${id}`;
                    form.innerHTML = `@csrf @method('DELETE')`;
                    document.body.appendChild(form);
                    form.submit();
                });
            }

            // ─── Bulk selection ───────────────────────────────────────────
            @if(auth()->user()->isSuperAdmin())
            function attachBulkListeners() {
                const selectAll   = document.getElementById('select-all');
                const checkboxes  = document.querySelectorAll('.row-checkbox');
                const bulkBar     = document.getElementById('bulk-bar');
                const bulkCount   = document.getElementById('bulk-count');

                checkboxes.forEach(cb => {
                    cb.addEventListener('change', function () {
                        const allChecked  = Array.from(checkboxes).every(c => c.checked);
                        const someChecked = Array.from(checkboxes).some(c => c.checked);
                        if (selectAll) {
                            selectAll.checked       = allChecked;
                            selectAll.indeterminate = someChecked && !allChecked;
                        }
                        updateBulkBar();
                    });
                });

                if (selectAll) {
                    selectAll.addEventListener('change', function () {
                        checkboxes.forEach(cb => cb.checked = this.checked);
                        updateBulkBar();
                    });
                }
            }

            function updateBulkBar() {
                const checked   = document.querySelectorAll('.row-checkbox:checked').length;
                const bulkBar   = document.getElementById('bulk-bar');
                const bulkCount = document.getElementById('bulk-count');
                if (bulkBar)   bulkBar.style.display   = checked > 0 ? 'flex' : 'none';
                if (bulkCount) bulkCount.textContent    = checked;
            }

            function clearSelection() {
                document.querySelectorAll('.row-checkbox').forEach(cb => cb.checked = false);
                const selectAll = document.getElementById('select-all');
                if (selectAll) { selectAll.checked = false; selectAll.indeterminate = false; }
                updateBulkBar();
            }

            function bulkDelete() {
                const selectedIds = Array.from(document.querySelectorAll('.row-checkbox:checked')).map(cb => cb.value);
                if (!selectedIds.length) {
                    AppModal.error('Perhatian', 'Tidak ada bidang yang dipilih.');
                    return;
                }
                AppModal.confirmDelete(selectedIds.length + ' bidang terpilih', function() {
                    const form = document.createElement('form');
                    form.method = 'POST';
                    form.action = '{{ route("bidangs.bulk-destroy") }}';
                    let inputs = `@csrf`;
                    selectedIds.forEach(id => { inputs += `<input type="hidden" name="ids[]" value="${id}" />`; });
                    form.innerHTML = inputs;
                    document.body.appendChild(form);
                    form.submit();
                });
            }
            @endif

            // ─── Init + auto-refresh ──────────────────────────────────────
            document.addEventListener('DOMContentLoaded', function () {
                const urlParams = new URLSearchParams(window.location.search);
                appliedStatus = urlParams.get('status') || 'all';
                appliedSort   = urlParams.get('sort') || 'nama_asc';

                const stRadio = document.querySelector(`.status-filter[value="${appliedStatus}"]`);
                if (stRadio) stRadio.checked = true;

                const srRadio = document.querySelector(`.sort-filter[value="${appliedSort}"]`);
                if (srRadio) srRadio.checked = true;

                const q = urlParams.get('search') || '';
                const input = document.getElementById('searchInput');
                if (input && q) { input.value = q; toggleClearBtn(input); }

                @if(auth()->user()->isSuperAdmin())
                attachBulkListeners();
                @endif

                if (typeof feather !== 'undefined') feather.replace();

                // Silent auto-refresh setiap 15 detik
                const autoRefreshMs = 15000;
                setInterval(function () {
                    if (document.hidden) return;
                    fetchBidangs(true);
                }, autoRefreshMs);
            });
        </script>
    </body>
</html>
