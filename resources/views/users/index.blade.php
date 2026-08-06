<!DOCTYPE html>
<html lang="en">
    <head>
        @include('layouts.head-page-meta', ['title' => 'Users - Sistem Manajemen Aset Desa Demo'])
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

            .users-helper-note {
                margin: 0 28px 14px;
                padding: 12px 14px;
                border-radius: 10px;
                border: 1px solid #bfdbfe;
                background: linear-gradient(135deg, #eff6ff 0%, #dbeafe 100%);
                display: flex;
                align-items: flex-start;
                gap: 10px;
                font-size: 13px;
                color: #1e3a8a;
                line-height: 1.5;
            }
            .users-helper-note i,
            .users-helper-note svg {
                width: 16px;
                height: 16px;
                margin-top: 1px;
                color: #1e3a8a;
                stroke: #1e3a8a;
                flex-shrink: 0;
            }
            .users-helper-note a {
                color: #1d4ed8;
                font-weight: 600;
                text-decoration: underline;
            }

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

            /* User Cell */
            .user-cell {
                display: flex;
                align-items: center;
                gap: 14px;
            }
            /* .user-avatar removed */
            .user-info h6 {
                font-size: 14px;
                font-weight: 600;
                color: #1f2937;
                margin: 0 0 2px 0;
            }
            .user-info span {
                font-size: 12px;
                color: #9ca3af;
            }

            /* Role Badge */
            .role-badge {
                display: inline-flex;
                align-items: center;
                gap: 6px;
                padding: 6px 12px;
                border-radius: 20px;
                font-size: 12px;
                font-weight: 600;
            }
            .role-badge.super-admin {
                background: linear-gradient(135deg, #f3e8ff 0%, #e9d5ff 100%);
                color: #7c3aed;
            }
            .role-badge.perbekel {
                background: linear-gradient(135deg, #f3e8ff 0%, #e9d5ff 100%);
                color: #7c3aed;
            }
            .role-badge.admin {
                background: linear-gradient(135deg, #dbeafe 0%, #bfdbfe 100%);
                color: #2563eb;
            }
            .role-badge.operator {
                background: linear-gradient(135deg, #d1fae5 0%, #a7f3d0 100%);
                color: #059669;
            }
            .role-badge.viewer {
                color: #6b7280;
            }

            /* Status Badge */
            .status-badge {
                display: inline-flex;
                align-items: center;
                gap: 6px;
                padding: 6px 12px;
                border-radius: 20px;
                font-size: 12px;
                font-weight: 600;
            }
            .status-badge.active {
                background: #ecfdf5;
                color: #059669;
            }
            .status-badge.inactive {
                background: #fef2f2;
                color: #dc2626;
            }
            .status-dot {
                width: 8px;
                height: 8px;
                border-radius: 50%;
            }
            .status-badge.active .status-dot { background: #10b981; }
            .status-badge.inactive .status-dot { background: #ef4444; }

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
            .action-btn.toggle-aktif { background: #d1fae5; color: #059669; }
            .action-btn.toggle-aktif:hover { background: #10b981; color: #fff; }
            .action-btn.toggle-nonaktif { background: #fef9c3; color: #b45309; }
            .action-btn.toggle-nonaktif:hover { background: #f59e0b; color: #fff; }
            .action-btn.delete { background: #fee2e2; color: #dc2626; }
            .action-btn.delete:hover { background: #ef4444; color: #fff; }

            /* Pagination & Dynamic Page Size Selector */
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
                gap: 16px;
                flex-wrap: wrap;
            }
            .page-size-wrap {
                display: flex;
                align-items: center;
                gap: 8px;
                font-size: 14px;
                color: #4b5563;
                font-weight: 500;
            }
            .per-page-select {
                height: 36px;
                padding: 0 28px 0 12px;
                border: 1px solid #d1d5db;
                border-radius: 8px;
                background-color: #ffffff;
                color: #1f2937;
                font-size: 14px;
                font-weight: 600;
                cursor: pointer;
                outline: none;
                transition: all 0.2s ease;
                appearance: none;
                -webkit-appearance: none;
                -moz-appearance: none;
                background-image: url("data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' width='16' height='16' viewBox='0 0 24 24' fill='none' stroke='%23374151' stroke-width='2' stroke-linecap='round' stroke-linejoin='round'%3E%3Cpolyline points='6 9 12 15 18 9'%3E%3C/polyline%3E%3C/svg%3E");
                background-repeat: no-repeat;
                background-position: right 8px center;
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

            .pagination-info { font-size: 14px; color: #6b7280; }
            .pagination-info strong { color: #064635; font-weight: 600; }
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
            .page-jump-wrap { display: flex; align-items: center; gap: 6px; }
            .page-jump-wrap label { font-size: 13px; color: #6b7280; white-space: nowrap; }
            .page-jump-input { width: 56px; height: 34px; border: 1px solid #d1d5db; border-radius: 6px; text-align: center; font-size: 13px; color: #374151; outline: none; transition: border-color .15s; }
            .page-jump-input:focus { border-color: #064635; box-shadow: 0 0 0 2px rgba(6, 70, 53,.15); }
            @media (max-width: 768px) {
                .pagination-bar { flex-direction: column; align-items: center; justify-content: center; gap: 12px; padding: 16px; }
                .pagination-left, .pagination-center, .pagination-right { justify-content: center; width: 100%; text-align: center; }
            }
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
                    'breadcrumbItem' => 'Users',
                    'breadcrumbItemActive' => 'List Users'
                ])
 
                <!-- Page Header -->
                <div class="page-header-card">
                    {{-- <h1><i data-feather="users" style="width: 32px; height: 32px; display: inline-block; vertical-align: middle; margin-right: 12px;"></i>Manajemen Users</h1> --}}
                    <h1>Manajemen Users</h1>
                    <p>Kelola data pengguna sistem, atur peran dan hak akses dengan mudah.</p>
                </div>

                <!-- Stats Cards -->
                <div class="stats-grid">
                    <div class="stat-card">
                        <div class="stat-card-label">TOTAL USERS</div>
                        <div class="stat-card-value">{{ number_format($stats['totalUsers'] ?? 0, 0, ',', '.') }}</div>
                        <div class="kpi-trend-wrapper">
                            <span class="kpi-trend-pill neutral">0%</span>
                            <span class="kpi-trend-label">vs. kemarin</span>
                        </div>
                    </div>
                    <div class="stat-card">
                        <div class="stat-card-label">USER AKTIF</div>
                        <div class="stat-card-value">{{ number_format($stats['userAktif'] ?? 0, 0, ',', '.') }}</div>
                        <div class="kpi-trend-wrapper">
                            <span class="kpi-trend-pill neutral">0%</span>
                            <span class="kpi-trend-label">vs. kemarin</span>
                        </div>
                    </div>
                    <div class="stat-card">
                        <div class="stat-card-label">USER TIDAK AKTIF</div>
                        <div class="stat-card-value">{{ number_format($stats['userTidakAktif'] ?? 0, 0, ',', '.') }}</div>
                        <div class="kpi-trend-wrapper">
                            <span class="kpi-trend-pill neutral">0%</span>
                            <span class="kpi-trend-label">vs. kemarin</span>
                        </div>
                    </div>
                    <div class="stat-card">
                        <div class="stat-card-label">ADMIN</div>
                        <div class="stat-card-value">{{ number_format($stats['totalAdmin'] ?? 0, 0, ',', '.') }}</div>
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
                                <h5>Daftar Users</h5>
                                <span>Total {{ $stats['totalUsers'] }} users terdaftar</span>
                            </div>
                        </div>
                        <div class="controls-bar">
                            <div class="search-box">
                                <input type="text" id="searchInput" placeholder="Cari user..." oninput="onSearchInput(this)" />
                                <button id="search-icon-btn" onclick="applyFilters()"><i data-feather="search"></i></button>
                                <button id="clear-search-btn" onclick="clearSearch()" style="color:#ef4444;display:none;"><i data-feather="x"></i></button>
                            </div>
                            <div class="dropdown" style="position:relative;">
                                <button class="filter-btn" data-pc-toggle="dropdown" title="Filter">
                                    <i data-feather="sliders"></i>
                                </button>
                                <div class="dropdown-menu dropdown-menu-end py-3 px-4" style="min-width: 240px; z-index: 1050;">
                                    <div class="text-sm font-semibold mb-2" style="color: #064635;">Filter Role</div>
                                    <div class="space-y-2" style="margin-bottom: 12px;">
                                        <label class="flex items-center gap-2 text-sm" style="cursor: pointer;">
                                            <input type="checkbox" class="role-filter" value="Super Admin" style="accent-color: #064635; cursor: pointer;"> Super Admin
                                        </label>
                                        <label class="flex items-center gap-2 text-sm" style="cursor: pointer;">
                                            <input type="checkbox" class="role-filter" value="Admin / SekDes" style="accent-color: #064635; cursor: pointer;"> Admin / SekDes
                                        </label>
                                        <label class="flex items-center gap-2 text-sm" style="cursor: pointer;">
                                            <input type="checkbox" class="role-filter" value="Admin / Kaur" style="accent-color: #064635; cursor: pointer;"> Admin / Kaur
                                        </label>
                                        <label class="flex items-center gap-2 text-sm" style="cursor: pointer;">
                                            <input type="checkbox" class="role-filter" value="Perbekel" style="accent-color: #064635; cursor: pointer;"> Perbekel
                                        </label>
                                    </div>
                                    <hr style="border-color: #e5e7eb; margin: 12px 0;" />
                                    <div class="text-sm font-semibold mb-2" style="color: #064635;">Status</div>
                                    <div class="space-y-2" style="margin-bottom: 12px;">
                                        <label class="flex items-center gap-2 text-sm" style="cursor: pointer;">
                                            <input type="radio" name="statusFilter" value="all" class="status-filter" checked style="accent-color: #064635; cursor: pointer;"> Semua
                                        </label>
                                        <label class="flex items-center gap-2 text-sm" style="cursor: pointer;">
                                            <input type="radio" name="statusFilter" value="active" class="status-filter" style="accent-color: #064635; cursor: pointer;"> Aktif
                                        </label>
                                        <label class="flex items-center gap-2 text-sm" style="cursor: pointer;">
                                            <input type="radio" name="statusFilter" value="inactive" class="status-filter" style="accent-color: #064635; cursor: pointer;"> Tidak Aktif
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
                            @if(auth()->user()->isSuperAdmin())
                            <a href="{{ route('users.create') }}" class="add-btn">
                                <i data-feather="plus"></i>
                                Tambah User
                            </a>
                            @endif
                        </div>
                    </div>
                    @if(!auth()->user()->isSuperAdmin())
                    <div class="users-helper-note">
                        <i data-feather="info"></i>
                        <div>
                            Untuk mengubah informasi pribadi, silakan gunakan fitur
                            <a href="{{ route('profile') }}">Informasi Saya</a>.
                        </div>
                    </div>
                    @endif
                    @if(auth()->user()->isSuperAdmin())
                    <!-- Bulk action bar -->
                    <div id="bulk-bar" style="display:none;align-items:center;gap:12px;background:#fef2f2;border:1px solid #fecaca;border-radius:8px;padding:10px 16px;margin:12px 16px 0;flex-wrap:wrap;">
                        <span style="font-size:13px;font-weight:600;color:#dc2626;"><span id="bulk-count">0</span> data terpilih</span>
                        <span id="bulk-warn" style="display:none;font-size:12px;color:#b45309;background:#fef3c7;border:1px solid #fcd34d;border-radius:5px;padding:3px 9px;">&#9888; Hanya 100 pertama yang akan dihapus, pilih ulang untuk sisanya</span>
                        <button type="button" onclick="clearSelection()" style="background:transparent;color:#6b7280;border:1px solid #d1d5db;padding:7px 12px;border-radius:6px;font-size:13px;cursor:pointer;display:inline-flex;align-items:center;gap:5px;"><i data-feather="x" style="width:14px;height:14px;"></i> Batal</button>
                        <button type="button" onclick="bulkDeleteConfirm()" style="background:#dc2626;color:#fff;border:none;padding:7px 14px;border-radius:6px;font-size:13px;font-weight:600;cursor:pointer;display:inline-flex;align-items:center;gap:5px;"><i data-feather="trash-2" style="width:14px;height:14px;"></i> Hapus Terpilih</button>
                    </div>
                    <form id="bulk-delete-form" method="POST" action="{{ route('users.bulk-destroy') }}" style="display:none;">@csrf</form>
                    @endif
                    <div class="data-card-body table-loading-wrap">
                        <table class="modern-table">
                            <thead>
                                <tr>
                                    @if(auth()->user()->isSuperAdmin())
                                    <th style="width:42px;"><input type="checkbox" id="check-all" onchange="toggleAll(this)" style="cursor:pointer;accent-color:#064635;"></th>
                                    @endif
                                    <th style="width: 60px; text-align: center;">No</th>
                                    <th>User</th>
                                    <th>Username</th>
                                    {{-- <th>Email</th> --}}
                                    <th>Role</th>
                                    <th>Status</th>
                                    <th style="width: 140px;">Aksi</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($users as $index => $user)
                                <tr>
                                    @if(auth()->user()->isSuperAdmin())
                                    <td><input type="checkbox" class="row-check" value="{{ $user->id }}" {{ $user->id === auth()->id() ? 'disabled' : '' }} onchange="updateBulkBar()" style="cursor:pointer;accent-color:#064635;"></td>
                                    @endif
                                    <td style="text-align: center;">{{ $users->firstItem() + $index }}</td>
                                    <td>
                                        <div class="user-cell">
                                            <div class="user-info">
                                                <h6>{{ $user->nama_display }}</h6>
                                                <span>{{ $user->role_name_display }}</span>
                                            </div>
                                        </div>
                                    </td>
                                    <td>{{ $user->username ?? '-' }}</td>
                                    {{-- <td>{{ $user->email }}</td> --}}
                                    <td>{{ $user->role_info['name'] }}</td>
                                    <td><span class="status-badge {{ $user->is_aktif ? 'active' : 'inactive' }}"><span class="status-dot"></span> {{ $user->is_aktif ? 'Aktif' : 'Tidak Aktif' }}</span></td>
                                    <td>
                                        <div class="action-btns">
                                            <button class="action-btn view" title="Lihat" onclick="window.location.href='{{ route('users.show', $user->id) }}'"><i data-feather="eye"></i></button>
                                            @if(auth()->user()->isSuperAdmin())
                                            <button class="action-btn edit" title="Edit" onclick="window.location.href='{{ route('users.edit', $user->id) }}'"><i data-feather="edit-2"></i></button>
                                            @if($user->id !== auth()->id())
                                            <form action="{{ route('users.destroy', $user->id) }}" method="POST" style="display:inline;" id="deleteForm{{ $user->id }}">
                                                @csrf
                                                @method('DELETE')
                                                <button type="button" class="action-btn delete" title="Hapus"
                                                data-nama="{{ $user->nama_display }}"
                                                data-pejabat-nama="{{ $user->pejabat_id ? ($user->pejabat->nama ?? '') : '' }}"
                                                onclick="confirmDeleteUserIndex(
                                                    this.getAttribute('data-nama'),
                                                    {{ $user->id }},
                                                    this.getAttribute('data-pejabat-nama'),
                                                    {{ $user->pejabat_id ?? 'null' }}
                                                )"><i data-feather="trash-2"></i></button>
                                            </form>
                                            @endif
                                            @endif
                                        </div>
                                    </td>
                                </tr>
                                @empty
                                <tr>
                                    <td colspan="6" style="text-align:center;padding:40px;color:#9ca3af;">
                                        <div style="display:flex;flex-direction:column;align-items:center;gap:8px;">
                                            <i data-feather="inbox" style="width:48px;height:48px;opacity:0.5;"></i>
                                            <p style="margin:0;">Tidak ada data user</p>
                                        </div>
                                    </td>
                                </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                    <!-- Pagination Bar -->
                    <div class="pagination-bar">
                        <div class="pagination-left">
                            <div class="page-size-wrap">
                                <span>Tampilkan</span>
                                <select id="perPageSelect" class="per-page-select" onchange="changePerPage(this.value)">
                                    <option value="10" {{ ($users->perPage() == 10) ? 'selected' : '' }}>10</option>
                                    <option value="20" {{ ($users->perPage() == 20) ? 'selected' : '' }}>20</option>
                                    <option value="50" {{ ($users->perPage() == 50) ? 'selected' : '' }}>50</option>
                                    <option value="100" {{ ($users->perPage() == 100) ? 'selected' : '' }}>100</option>
                                </select>
                                <span>data</span>
                            </div>
                            <!-- <div class="pagination-info" id="paginationRangeInfo">
                                Menampilkan <strong>{{ $users->firstItem() ?? 0 }}-{{ $users->lastItem() ?? 0 }}</strong> dari <strong>{{ $users->total() }}</strong> data
                            </div> -->
                        </div>

                        <div class="pagination-center">
                            <nav class="pagination-nav" id="paginationNav">
                                {{-- Previous Page Link --}}
                                @if ($users->onFirstPage())
                                    <span class="page-btn disabled" title="Previous"><i data-feather="chevron-left"></i></span>
                                @else
                                    <button type="button" class="page-btn" onclick="goToPage({{ $users->currentPage() - 1 }})" title="Previous"><i data-feather="chevron-left"></i></button>
                                @endif

                                {{-- Pagination Elements --}}
                                @php
                                    $currentPage  = $users->currentPage();
                                    $lastPage     = $users->lastPage();
                                    $windowStart  = max(1, $currentPage - 2);
                                    $windowEnd    = min($lastPage, $currentPage + 2);
                                    if (($windowEnd - $windowStart + 1) < 5) {
                                        if ($windowStart === 1) {
                                            $windowEnd = min($lastPage, 5);
                                        } else {
                                            $windowStart = max(1, $windowEnd - 4);
                                        }
                                    }
                                    $pageUrls = $users->getUrlRange($windowStart, $windowEnd);
                                @endphp
                                @foreach ($pageUrls as $page => $url)
                                    @if ($page == $currentPage)
                                        <span class="page-btn active">{{ $page }}</span>
                                    @else
                                        <button type="button" class="page-btn" onclick="goToPage({{ $page }})">{{ $page }}</button>
                                    @endif
                                @endforeach

                                {{-- Next Page Link --}}
                                @if ($users->hasMorePages())
                                    <button type="button" class="page-btn" onclick="goToPage({{ $users->currentPage() + 1 }})" title="Next"><i data-feather="chevron-right"></i></button>
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
                                    max="{{ $users->lastPage() }}"
                                    value="{{ $users->currentPage() }}"
                                    placeholder="1–{{ $users->lastPage() }}"
                                    title="Masukkan nomor halaman (1–{{ $users->lastPage() }})"
                                >
                            </div>
                        </div>

                        <div class="pagination-right" id="paginationRangeInfo">
                            Menampilkan <strong>{{ $users->firstItem() ?? 0 }}-{{ $users->lastItem() ?? 0 }}</strong> dari <strong>{{ $users->total() }}</strong> data
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
                <span id="success-message" class="font-medium">User berhasil ditambahkan!</span>
                <button onclick="closeToast()" class="ml-2 hover:opacity-80">
                    <i data-feather="x" style="width:18px;height:18px;"></i>
                </button>
            </div>
        </div>

        <script>
            function changePerPage(perPage) {
                applyFilters(false, 1);
            }

            function goToPage(page) {
                applyFilters(false, page);
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
                    goToPage(val);
                });
            })();

            // Search and Filter functionality
            function applyFilters(isAutoRefresh = false, pageOverride = null) {
                const searchValue = document.getElementById('searchInput').value;
                const selectedRoles = Array.from(document.querySelectorAll('.role-filter:checked')).map(cb => cb.value);
                const selectedStatus = document.querySelector('.status-filter:checked')?.value || 'all';
                const selectedSort = document.querySelector('.sort-filter:checked')?.value || 'nama_asc';
                const perPageSelect = document.getElementById('perPageSelect');
                const perPageValue = perPageSelect ? perPageSelect.value : '10';
                
                // Build query params
                const params = new URLSearchParams();
                if (searchValue) params.append('search', searchValue);
                if (selectedRoles.length > 0) {
                    selectedRoles.forEach(role => params.append('roles[]', role));
                }
                if (selectedStatus !== 'all') params.append('status', selectedStatus);
                if (selectedSort !== 'nama_asc') params.append('sort', selectedSort);
                if (perPageValue && perPageValue !== '10') params.append('per_page', perPageValue);

                const urlParams = new URLSearchParams(window.location.search);
                let currentPage = pageOverride !== null ? pageOverride : (urlParams.get('page') || 1);
                if (currentPage > 1) {
                    params.set('page', currentPage);
                }
                
                history.replaceState(null, '', window.location.pathname + (params.toString() ? '?' + params.toString() : ''));

                // Fetch filtered data
                const wrap = document.querySelector('.table-loading-wrap');
                if (!isAutoRefresh && wrap) wrap.classList.add('is-loading');
                fetch(`{{ route('users.index') }}?${params.toString()}`, {
                    headers: {
                        'X-Requested-With': 'XMLHttpRequest',
                        'Accept': 'application/json'
                    }
                })
                .then(response => response.json())
                .then(data => {
                    updateStats(data.stats);
                    updateTable(data.users, data.pagination, data.auth);
                    updatePaginationUI(data.pagination);
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
                
                // Uncheck all role filters
                document.querySelectorAll('.role-filter').forEach(cb => cb.checked = false);
                
                // Reset status to 'all'
                const allStatusRadio = document.querySelector('.status-filter[value="all"]');
                if (allStatusRadio) allStatusRadio.checked = true;
                
                // Reset sort to default
                const defaultSort = document.querySelector('.sort-filter[value="nama_asc"]');
                if (defaultSort) defaultSort.checked = true;
                
                // Apply filters (which will show all data)
                applyFilters();
            }
            
            function updateStats(stats) {
                // Update stats cards
                const statCards = document.querySelectorAll('.stat-card');
                if (statCards[0]) statCards[0].querySelector('h3').textContent = stats.totalUsers;
                if (statCards[1]) statCards[1].querySelector('h3').textContent = stats.userAktif;
                if (statCards[2]) statCards[2].querySelector('h3').textContent = stats.userTidakAktif;
                if (statCards[3]) statCards[3].querySelector('h3').textContent = stats.totalAdmin;
                
                // Update title
                const titleSpan = document.querySelector('.data-card-title span');
                if (titleSpan) titleSpan.textContent = `Total ${stats.totalUsers} users terdaftar`;
            }
            
            function updateTable(users, pagination, auth) {
                const tbody = document.querySelector('.modern-table tbody');
                if (!tbody) return;
                
                const isSuperAdmin = auth && auth.isSuperAdmin;
                const colspanCount = isSuperAdmin ? 7 : 6;
                
                const firstItem = pagination ? pagination.firstItem : 1;
                const lastItem = pagination ? (firstItem + users.length - 1) : users.length;
                const total = (pagination && pagination.total !== undefined) ? pagination.total : users.length;
                
                // Update pagination info DOM
                const paginationInfos = document.querySelectorAll('.pagination-info');
                if (paginationInfos[0]) paginationInfos[0].innerHTML = 'Menampilkan <strong>' + (firstItem||0) + '-' + (lastItem||0) + '</strong> dari <strong>' + total + '</strong> data';
                if (paginationInfos[1]) paginationInfos[1].innerHTML = 'Total <strong>' + total + '</strong> users';

                if (users.length === 0) {
                    tbody.innerHTML = `
                        <tr>
                            <td colspan="${colspanCount}" style="text-align:center;padding:40px;color:#9ca3af;">
                                <div style="display:flex;flex-direction:column;align-items:center;gap:8px;">
                                    <i data-feather="inbox" style="width:48px;height:48px;opacity:0.5;"></i>
                                    <p style="margin:0;">Tidak ada data user</p>
                                </div>
                            </td>
                        </tr>
                    `;
                    return;
                }
                
                let html = '';
                const currentUserId = auth && auth.currentUserId;
                
                users.forEach((user, index) => {
                    const rowNumber = firstItem + index;
                    
                    // Checkbox column hanya untuk Super Admin
                    const checkboxHtml = isSuperAdmin 
                        ? `<td><input type="checkbox" class="row-check" value="${user.id}" ${user.id === currentUserId ? 'disabled' : ''} onchange="updateBulkBar()" style="cursor:pointer;accent-color:#064635;"></td>`
                        : '';
                    
                    html += `
                        <tr>
                            ${checkboxHtml}
                            <td style="text-align:center;">${rowNumber}</td>
                            <td>
                                <div class="user-cell">
                                    <div class="user-info">
                                        <h6>${user.nama}</h6>
                                        <span>${user.role_name_display || user.role_name}</span>
                                    </div>
                                </div>
                            </td>
                            <td>${user.username ?? '-'}</td>
                            <td>${user.role_info.name}</td>
                            <td><span class="status-badge ${user.is_aktif ? 'active' : 'inactive'}"><span class="status-dot"></span> ${user.is_aktif ? 'Aktif' : 'Tidak Aktif'}</span></td>
                            <td>
                                <div class="action-btns">
                                    <button class="action-btn view" title="Lihat" onclick="window.location.href='{{ url('users') }}/${user.id}'"><i data-feather="eye"></i></button>
                                    <button class="action-btn edit" title="Edit" onclick="window.location.href='{{ url('users') }}/${user.id}/edit'"><i data-feather="edit-2"></i></button>
                                    <form action="{{ url('users') }}/${user.id}" method="POST" style="display:inline;" id="deleteForm${user.id}">
                                        @csrf
                                        @method('DELETE')
                                        <button type="button" class="action-btn delete" title="Hapus" 
                                            data-nama="${user.nama.replace(/&/g, '&amp;').replace(/"/g, '&quot;')}" 
                                            data-pejabat-nama="${user.pejabat ? user.pejabat.nama.replace(/&/g, '&amp;').replace(/"/g, '&quot;') : ''}" 
                                            onclick="confirmDeleteUserIndex(this.getAttribute('data-nama'), ${user.id}, this.getAttribute('data-pejabat-nama'), ${user.pejabat_id ?? 'null'})">
                                            <i data-feather="trash-2"></i>
                                        </button>
                                    </form>
                                </div>
                            </td>
                        </tr>
                    `;
                });
                tbody.innerHTML = html;
            }

            function updatePaginationUI(pagination) {
                if (!pagination) return;

                const firstItem = pagination.firstItem || 0;
                const lastItem = pagination.lastItem || 0;
                const total = (pagination.total !== undefined) ? pagination.total : 0;
                const currentPage = pagination.currentPage || 1;
                const lastPage = pagination.lastPage || 1;

                // Range info
                const rangeEl = document.getElementById('paginationRangeInfo');
                if (rangeEl) {
                    rangeEl.innerHTML = `Menampilkan <strong>${firstItem}-${lastItem}</strong> dari <strong>${total}</strong> data`;
                }

                // Total info
                const totalEl = document.getElementById('paginationTotalInfo');
                if (totalEl) {
                    totalEl.innerHTML = `Total <strong>${total}</strong> users`;
                }

                // Jump page input
                const jumpInput = document.getElementById('pageJumpInput');
                if (jumpInput) {
                    jumpInput.max = lastPage;
                    jumpInput.placeholder = `1–${lastPage}`;
                    jumpInput.title = `Masukkan nomor halaman (1–${lastPage})`;
                    jumpInput.value = currentPage;
                }

                // Navigation buttons
                const nav = document.getElementById('paginationNav');
                if (!nav) return;

                let html = '';

                // Prev
                if (currentPage <= 1) {
                    html += `<span class="page-btn disabled" title="Previous"><i data-feather="chevron-left"></i></span>`;
                } else {
                    html += `<button type="button" class="page-btn" onclick="goToPage(${currentPage - 1})" title="Previous"><i data-feather="chevron-left"></i></button>`;
                }

                // Page numbers
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

                // Next
                if (currentPage >= lastPage) {
                    html += `<span class="page-btn disabled" title="Next"><i data-feather="chevron-right"></i></span>`;
                } else {
                    html += `<button type="button" class="page-btn" onclick="goToPage(${currentPage + 1})" title="Next"><i data-feather="chevron-right"></i></button>`;
                }

                nav.innerHTML = html;
            }
            
            // Search on Enter key
            document.addEventListener('DOMContentLoaded', function() {
                // debounce live search already applied via onSearchInput
                // Auto-apply when role or status filter changes
                document.querySelectorAll('.role-filter, .status-filter').forEach(input => {
                    input.addEventListener('change', function() {
                        // Optional: auto-apply on change
                        // applyFilters();
                    });
                });

                const autoRefreshMs = 15000;
                setInterval(function () {
                    if (document.hidden) return;
                    applyFilters(true);
                }, autoRefreshMs);
            });
            
            // Check for success message from user-form
            (function(){
                const success = sessionStorage.getItem('userFormSuccess');
                const message = sessionStorage.getItem('userFormMessage');
                
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
                    sessionStorage.removeItem('userFormSuccess');
                    sessionStorage.removeItem('userFormMessage');
                    
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

            function confirmDeleteUserIndex(userName, userId, pejabatNama, pejabatId) {
                // Blokir jika user masih terhubung pejabat
                if (pejabatId) {
                    AppModal.show({
                        type: 'warning',
                        title: 'Hapus Pejabat Terlebih Dahulu',
                        message: `User <strong>${userName}</strong> masih terhubung dengan pejabat <strong>${pejabatNama}</strong>.<br><br>Hapus data pejabat tersebut terlebih dahulu, kemudian user ini dapat dihapus.`,
                        confirmText: 'Ke Halaman Pejabat',
                        cancelText: 'Tutup',
                        showCancel: true,
                        onConfirm: function() {
                            window.location.href = '{{ route('pejabat.index') }}';
                        }
                    });
                    return;
                }
                AppModal.confirmDelete(userName, function() {
                    document.getElementById('deleteForm' + userId).submit();
                });
            }

            function confirmToggleAktif(userName, userId, willBeActive) {
                const action = willBeActive ? 'mengaktifkan' : 'menonaktifkan';
                const title  = willBeActive ? 'Aktifkan User' : 'Nonaktifkan User';
                const msg    = `Apakah Anda yakin ingin ${action} user <strong>${userName}</strong>?`;
                AppModal.show({
                    type: willBeActive ? 'success' : 'warning',
                    title: title,
                    message: msg,
                    confirmText: willBeActive ? 'Aktifkan' : 'Nonaktifkan',
                    cancelText: 'Batal',
                    showCancel: true,
                    onConfirm: function() { document.getElementById('toggleForm' + userId).submit(); }
                });
            }

            function toggleAll(src) {
                document.querySelectorAll('.row-check:not(:disabled)').forEach(cb => { cb.checked = src.checked; });
                updateBulkBar();
            }
            function updateBulkBar() {
                const checked = document.querySelectorAll('.row-check:checked');
                const bar = document.getElementById('bulk-bar');
                if (bar) {
                    bar.style.display = checked.length > 0 ? 'flex' : 'none';
                    document.getElementById('bulk-count').textContent = checked.length;
                    const warn = document.getElementById('bulk-warn');
                    if (warn) warn.style.display = checked.length > 100 ? 'inline-block' : 'none';
                    if (typeof feather !== 'undefined') feather.replace();
                }
            }
            function clearSelection() {
                document.querySelectorAll('.row-check, #check-all').forEach(cb => { cb.checked = false; });
                const bar = document.getElementById('bulk-bar');
                if (bar) bar.style.display = 'none';
            }
            function bulkDeleteConfirm() {
                const checked = Array.from(document.querySelectorAll('.row-check:checked'));
                if (!checked.length) return;
                const toDelete = checked.slice(0, 100);
                const label = toDelete.length < checked.length
                    ? toDelete.length + ' user (dari ' + checked.length + ' terpilih, hanya 100 pertama)'
                    : checked.length + ' user terpilih';
                AppModal.confirmDelete(label, function() {
                    const form = document.getElementById('bulk-delete-form');
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
