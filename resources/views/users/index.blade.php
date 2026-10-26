<!DOCTYPE html>
<html lang="id">
<head>
    @include('layouts.head-page-meta', ['title' => 'Manajemen Pengguna - Sistem Manajemen Aset Desa'])
    @include('layouts.head-css')
    <style>
        /* Stat Cards is-clickable & active filter */
        .stat-card-sima.is-clickable {
            cursor: pointer;
            user-select: none;
            transition: border-color 150ms ease, background-color 150ms ease;
        }
        .stat-card-sima.is-clickable:hover {
            border-color: #b8c9b3;
        }
        .stat-card-sima.is-active-filter {
            border-color: var(--color-primary) !important;
            background-color: var(--color-surface) !important;
            box-shadow: 0 0 0 1px var(--color-primary);
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

        /* Helper note */
        .users-helper-note {
            margin: 16px 20px 0;
            padding: 10px 14px;
            border-radius: var(--radius-sm);
            border: 1px dashed var(--color-border);
            background: var(--color-surface);
            display: flex;
            align-items: center;
            gap: 10px;
            font-size: 13px;
            color: var(--color-text);
            line-height: 1.5;
        }
        .users-helper-note i,
        .users-helper-note svg {
            width: 16px;
            height: 16px;
            color: var(--color-primary);
            stroke: var(--color-primary);
            flex-shrink: 0;
        }
        .users-helper-note a {
            color: var(--color-primary);
            font-weight: 600;
            text-decoration: underline;
            text-underline-offset: 3px;
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
                        Manajemen Pengguna
                    </h1>
                </div>
                <div class="page-header-actions" style="display: flex; gap: 10px; align-items: center; flex-wrap: wrap;">
                    <div class="sima-dropdown-wrapper" style="position: relative; display: inline-block;">
                        <button type="button" id="btn-export-users" class="btn-sima-secondary btn-sima-pill-white" onclick="toggleExportMenu(this, event)">
                            <i data-feather="download" style="width: 14px; height: 14px;"></i>
                            <span>Ekspor Data</span>
                            <i data-feather="chevron-down" style="width: 12px; height: 12px; opacity: 0.7;"></i>
                        </button>
                        <div class="export-dropdown-menu" style="display: none;">
                            <a class="export-menu-item" id="export-users-excel" href="{{ route('users.export', ['format' => 'excel']) }}" onclick="if(window.showToast) window.showToast('Ekspor data pengguna (Excel) berhasil! Berkas sedang diunduh...', 'success'); this.closest('.export-dropdown-menu').style.display='none';">
                                <i data-feather="file-text" style="width: 14px; height: 14px; color: #10b981;"></i>
                                <span>Ekspor Excel (.xlsx)</span>
                            </a>
                            <hr class="dropdown-divider sima-divider" />
                            <a class="export-menu-item" id="export-users-pdf" href="{{ route('users.export', ['format' => 'pdf']) }}" onclick="if(window.showToast) window.showToast('Ekspor data pengguna (PDF) berhasil! Berkas sedang diunduh...', 'success'); this.closest('.export-dropdown-menu').style.display='none';">
                                <i data-feather="file" style="width: 14px; height: 14px; color: #ef4444;"></i>
                                <span>Ekspor PDF (.pdf)</span>
                            </a>
                        </div>
                    </div>
                    @if(auth()->user()->isSuperAdmin())
                    <button type="button" class="btn-sima-secondary btn-sima-pill-white" onclick="openImportModal()">
                        <i data-feather="upload-cloud" style="width: 14px; height: 14px;"></i>
                        <span>Import CSV</span>
                    </button>
                    <a href="{{ route('users.create') }}" class="btn-sima-primary">
                        <i data-feather="plus" style="width: 14px; height: 14px;"></i>
                        <span>Tambah Pengguna</span>
                    </a>
                    @endif
                </div>
            </div>

            <!-- 1. STAT CARDS (Prodex 4-Card Summary Bar) -->
            <div class="stat-card-row">
                <!-- Card 1: Total Users -->
                <div class="stat-card-sima is-clickable is-active-filter" id="card-status-all" onclick="filterByCardStatus('all')" title="Klik untuk menampilkan semua pengguna">
                    <div class="stat-header">
                        <span style="font-weight: 600;">TOTAL PENGGUNA</span>
                    </div>
                    <div class="stat-number stat-card-value">{{ number_format($stats['totalUsers'] ?? 0, 0, ',', '.') }}</div>
                    <div class="kpi-trend-wrapper stat-delta">
                        @php 
                            $tUsers = $stats['trends']['total']['percent'] ?? 0;
                            $dUsers = $stats['trends']['total']['diff'] ?? 0;
                        @endphp
                        @if($tUsers > 0 || $dUsers > 0)
                            <span class="stat-delta up">
                                <i data-feather="trending-up" style="width: 12px; height: 12px;"></i>
                                +{{ $tUsers }}% (+{{ number_format($dUsers, 0, ',', '.') }})
                            </span>
                        @elseif($tUsers < 0 || $dUsers < 0)
                            <span class="stat-delta down">
                                <i data-feather="trending-down" style="width: 12px; height: 12px;"></i>
                                {{ $tUsers }}% ({{ number_format($dUsers, 0, ',', '.') }})
                            </span>
                        @else
                            <span class="stat-delta" style="color: var(--color-text-muted);">0% (0)</span>
                        @endif
                        <span class="kpi-trend-label" style="color: var(--color-text-muted); font-size: 11.5px; margin-left: 4px;">vs. bulan lalu</span>
                    </div>
                </div>

                <!-- Card 2: User Aktif -->
                <div class="stat-card-sima is-clickable" id="card-status-active" onclick="filterByCardStatus('active')" title="Klik untuk memfilter Pengguna Aktif">
                    <div class="stat-header">
                        <span style="font-weight: 600;">PENGGUNA AKTIF</span>
                    </div>
                    <div class="stat-number stat-card-value">{{ number_format($stats['userAktif'] ?? 0, 0, ',', '.') }}</div>
                    <div class="kpi-trend-wrapper stat-delta">
                        @php 
                            $tAktif = $stats['trends']['aktif']['percent'] ?? 0;
                            $dAktif = $stats['trends']['aktif']['diff'] ?? 0;
                        @endphp
                        @if($tAktif > 0 || $dAktif > 0)
                            <span class="stat-delta up">
                                <i data-feather="trending-up" style="width: 12px; height: 12px;"></i>
                                +{{ $tAktif }}% (+{{ number_format($dAktif, 0, ',', '.') }})
                            </span>
                        @elseif($tAktif < 0 || $dAktif < 0)
                            <span class="stat-delta down">
                                <i data-feather="trending-down" style="width: 12px; height: 12px;"></i>
                                {{ $tAktif }}% ({{ number_format($dAktif, 0, ',', '.') }})
                            </span>
                        @else
                            <span class="stat-delta" style="color: var(--color-text-muted);">0% (0)</span>
                        @endif
                        <span class="kpi-trend-label" style="color: var(--color-text-muted); font-size: 11.5px; margin-left: 4px;">vs. bulan lalu</span>
                    </div>
                </div>

                <!-- Card 3: User Tidak Aktif -->
                <div class="stat-card-sima is-clickable" id="card-status-inactive" onclick="filterByCardStatus('inactive')" title="Klik untuk memfilter Pengguna Tidak Aktif">
                    <div class="stat-header">
                        <span style="font-weight: 600;">PENGGUNA TIDAK AKTIF</span>
                    </div>
                    <div class="stat-number stat-card-value">{{ number_format($stats['userTidakAktif'] ?? 0, 0, ',', '.') }}</div>
                    <div class="kpi-trend-wrapper stat-delta">
                        @php 
                            $tNonAktif = $stats['trends']['tidakAktif']['percent'] ?? 0;
                            $dNonAktif = $stats['trends']['tidakAktif']['diff'] ?? 0;
                        @endphp
                        @if($tNonAktif > 0 || $dNonAktif > 0)
                            <span class="stat-delta up">
                                <i data-feather="trending-up" style="width: 12px; height: 12px;"></i>
                                +{{ $tNonAktif }}% (+{{ number_format($dNonAktif, 0, ',', '.') }})
                            </span>
                        @elseif($tNonAktif < 0 || $dNonAktif < 0)
                            <span class="stat-delta down">
                                <i data-feather="trending-down" style="width: 12px; height: 12px;"></i>
                                {{ $tNonAktif }}% ({{ number_format($dNonAktif, 0, ',', '.') }})
                            </span>
                        @else
                            <span class="stat-delta" style="color: var(--color-text-muted);">0% (0)</span>
                        @endif
                        <span class="kpi-trend-label" style="color: var(--color-text-muted); font-size: 11.5px; margin-left: 4px;">vs. bulan lalu</span>
                    </div>
                </div>

                <!-- Card 4: Total Admin -->
                <div class="stat-card-sima" id="card-total-admin">
                    <div class="stat-header">
                        <span style="font-weight: 600;">ADMIN & SUPER ADMIN</span>
                    </div>
                    <div class="stat-number stat-card-value">{{ number_format($stats['totalAdmin'] ?? 0, 0, ',', '.') }}</div>
                    <div class="kpi-trend-wrapper stat-delta">
                        @php 
                            $tAdm = $stats['trends']['admin']['percent'] ?? 0;
                            $dAdm = $stats['trends']['admin']['diff'] ?? 0;
                        @endphp
                        @if($tAdm > 0 || $dAdm > 0)
                            <span class="stat-delta up">
                                <i data-feather="trending-up" style="width: 12px; height: 12px;"></i>
                                +{{ $tAdm }}% (+{{ number_format($dAdm, 0, ',', '.') }})
                            </span>
                        @elseif($tAdm < 0 || $dAdm < 0)
                            <span class="stat-delta down">
                                <i data-feather="trending-down" style="width: 12px; height: 12px;"></i>
                                {{ $tAdm }}% ({{ number_format($dAdm, 0, ',', '.') }})
                            </span>
                        @else
                            <span class="stat-delta" style="color: var(--color-text-muted);">0% (0)</span>
                        @endif
                        <span class="kpi-trend-label" style="color: var(--color-text-muted); font-size: 11.5px; margin-left: 4px;">vs. bulan lalu</span>
                    </div>
                </div>
            </div>

            <!-- 2. STATUS TABS (Underline Pattern) -->
            <div class="tabs-sima">
                <div class="tab-item-sima active" id="tab-status-all" onclick="filterByCardStatus('all')">
                    <span>Semua Pengguna</span>
                    <span class="tab-badge-sima">{{ number_format($stats['totalUsers'] ?? 0, 0, ',', '.') }}</span>
                </div>
                <div class="tab-item-sima" id="tab-status-active" onclick="filterByCardStatus('active')">
                    <span>Pengguna Aktif</span>
                    <span class="tab-badge-sima" style="background: var(--color-success-soft); color: var(--color-success);">{{ number_format($stats['userAktif'] ?? 0, 0, ',', '.') }}</span>
                </div>
                <div class="tab-item-sima" id="tab-status-inactive" onclick="filterByCardStatus('inactive')">
                    <span>Pengguna Tidak Aktif</span>
                    <span class="tab-badge-sima" style="background: var(--color-danger-soft); color: var(--color-danger);">{{ number_format($stats['userTidakAktif'] ?? 0, 0, ',', '.') }}</span>
                </div>
            </div>

            <!-- 3. TABLE CONTAINER (Prodex Table Standard) -->
            <div class="sima-card" style="padding: 0; overflow: visible; margin-bottom: 32px;">
                
                <!-- Action Controls Bar -->
                <div style="padding: 20px 20px; border-bottom: 1px solid var(--color-border); border-top-left-radius: var(--radius-lg); border-top-right-radius: var(--radius-lg); display: flex; justify-content: space-between; align-items: center; flex-wrap: wrap; gap: 12px;">
                    <div style="display: flex; align-items: center; gap: 10px; flex: 1; max-width: 480px;">
                        <!-- Search Box with Shortcut Badge -->
                        <div class="topbar-search-box" style="background: var(--color-surface);">
                            <i data-feather="search" style="width: 15px; height: 15px; color: var(--color-text-muted); flex-shrink: 0;"></i>
                            <input type="text" id="searchInput" class="topbar-search-input" placeholder="Cari nama atau username..." oninput="onSearchInput(this)" onkeypress="if(event.key==='Enter'){applyFilters(false, 1);}" value="{{ request('search') }}" />
                            <span class="topbar-search-shortcut" id="clear-search-btn" onclick="clearSearch()" style="cursor: pointer; display: {{ request('search') ? 'inline-block' : 'none' }}; color: var(--color-danger);">Batal</span>
                        </div>
                    </div>

                    <!-- Right Controls -->
                    <div style="display: flex; align-items: center; gap: 8px;">
                        <!-- Reset Filter Button -->
                        <button type="button" class="btn-sima-secondary btn-reset-filter" id="btn-reset-filter" onclick="resetFilters()" title="Reset Filter" style="padding: 7px 12px; font-size: 13px;">
                            <i data-feather="rotate-ccw" style="width: 13px; height: 13px;"></i>
                            <span>Reset Filter</span>
                        </button>

                        <!-- Filter Popover Dropdown -->
                        <div class="dropdown" id="filter-dropdown-container" style="position: relative;">
                            <button type="button" id="btn-filter-toggle" class="btn-sima-secondary" onclick="toggleFilterPopover(event)" title="Filter" style="padding: 7px 12px; font-size: 13px; display: inline-flex; align-items: center; gap: 6px;">
                                <i data-feather="sliders" style="width: 13px; height: 13px;"></i>
                                <span>Filter</span>
                            </button>
                            <div id="filter-dropdown-menu" class="sima-filter-popover" onclick="event.stopPropagation();" style="display: none; position: absolute; top: calc(100% + 4px); right: 0; min-width: 290px; padding: 20px 24px; z-index: 1050; background: var(--color-surface); border: 1px solid var(--color-border); border-radius: var(--radius-lg); box-shadow: var(--shadow-float); margin: 0;">
                                <div style="font-size: 13px; font-weight: 700; color: var(--color-primary); margin-bottom: 8px;">Filter Role</div>
                                <div style="display: flex; flex-direction: column; gap: 6px; margin-bottom: 12px;">
                                    <label style="cursor: pointer; display: flex; align-items: center; gap: 8px; font-size: 13px; color: var(--color-text);">
                                        <input type="checkbox" class="role-filter sima-checkbox" value="Super Admin"> Super Admin
                                    </label>
                                    <label style="cursor: pointer; display: flex; align-items: center; gap: 8px; font-size: 13px; color: var(--color-text);">
                                         <input type="checkbox" class="role-filter sima-checkbox" value="Admin / SekDes"> Admin / SekDes
                                    </label>
                                    <label style="cursor: pointer; display: flex; align-items: center; gap: 8px; font-size: 13px; color: var(--color-text);">
                                         <input type="checkbox" class="role-filter sima-checkbox" value="Admin / Kaur"> Admin / Kaur
                                    </label>
                                    <label style="cursor: pointer; display: flex; align-items: center; gap: 8px; font-size: 13px; color: var(--color-text);">
                                         <input type="checkbox" class="role-filter sima-checkbox" value="Perbekel"> Perbekel
                                    </label>
                                    <label style="cursor: pointer; display: flex; align-items: center; gap: 8px; font-size: 13px; color: var(--color-text);">
                                         <input type="checkbox" class="role-filter sima-checkbox" value="Kasi"> Kasi
                                    </label>
                                    <label style="cursor: pointer; display: flex; align-items: center; gap: 8px; font-size: 13px; color: var(--color-text);">
                                         <input type="checkbox" class="role-filter sima-checkbox" value="Kelian Banjar"> Kelian Banjar
                                    </label>
                                    <label style="cursor: pointer; display: flex; align-items: center; gap: 8px; font-size: 13px; color: var(--color-text);">
                                         <input type="checkbox" class="role-filter sima-checkbox" value="Staf"> Staf
                                    </label>
                                </div>
                                <hr style="border: none; border-top: 1px dashed var(--color-border); margin: 12px 0;" />
                                 <div style="font-size: 13px; font-weight: 700; color: var(--color-primary); margin-bottom: 8px;">Status</div>
                                <div style="display: flex; flex-direction: column; gap: 6px; margin-bottom: 12px;">
                                    <label style="cursor: pointer; display: flex; align-items: center; gap: 8px; font-size: 13px; color: var(--color-text);">
                                        <input type="radio" name="statusFilter" value="all" class="status-filter" {{ (!request('status') || request('status') === 'all') ? 'checked' : '' }} style="accent-color: var(--color-primary); cursor: pointer;"> 
                                        <span>Semua Status</span>
                                    </label>
                                    <label style="cursor: pointer; display: flex; align-items: center; gap: 8px; font-size: 13px; color: var(--color-text);">
                                        <input type="radio" name="statusFilter" value="active" class="status-filter" {{ request('status') === 'active' ? 'checked' : '' }} style="accent-color: var(--color-primary); cursor: pointer;"> 
                                        <span>Pengguna Aktif</span>
                                    </label>
                                    <label style="cursor: pointer; display: flex; align-items: center; gap: 8px; font-size: 13px; color: var(--color-text);">
                                        <input type="radio" name="statusFilter" value="inactive" class="status-filter" {{ request('status') === 'inactive' ? 'checked' : '' }} style="accent-color: var(--color-primary); cursor: pointer;"> 
                                        <span>Pengguna Tidak Aktif</span>
                                    </label>
                                </div>
                                <hr style="border: none; border-top: 1px dashed var(--color-border); margin: 12px 0;" />
                                <div style="font-size: 13px; font-weight: 700; color: var(--color-primary); margin-bottom: 8px;">Urutan Tampilan</div>
                                <div style="display: flex; flex-direction: column; gap: 6px; margin-bottom: 14px;">
                                    <label style="cursor: pointer; display: flex; align-items: center; gap: 8px; font-size: 13px; color: var(--color-text);">
                                        <input type="radio" name="sortFilter" value="nama_asc" class="sort-filter" {{ request('sort') === 'nama_asc' ? 'checked' : '' }} style="accent-color: var(--color-primary); cursor: pointer;"> 
                                        <span>Nama A-Z</span>
                                    </label>
                                    <label style="cursor: pointer; display: flex; align-items: center; gap: 8px; font-size: 13px; color: var(--color-text);">
                                        <input type="radio" name="sortFilter" value="nama_desc" class="sort-filter" {{ request('sort') === 'nama_desc' ? 'checked' : '' }} style="accent-color: var(--color-primary); cursor: pointer;"> 
                                        <span>Nama Z-A</span>
                                    </label>
                                    <label style="cursor: pointer; display: flex; align-items: center; gap: 8px; font-size: 13px; color: var(--color-text);">
                                        <input type="radio" name="sortFilter" value="terbaru" class="sort-filter" {{ (!request('sort') || request('sort') === 'terbaru') ? 'checked' : '' }} style="accent-color: var(--color-primary); cursor: pointer;"> 
                                        <span>Terbaru Terdaftar</span>
                                    </label>
                                    <label style="cursor: pointer; display: flex; align-items: center; gap: 8px; font-size: 13px; color: var(--color-text);">
                                        <input type="radio" name="sortFilter" value="terlama" class="sort-filter" {{ request('sort') === 'terlama' ? 'checked' : '' }} style="accent-color: var(--color-primary); cursor: pointer;"> 
                                        <span>Terlama Terdaftar</span>
                                    </label>
                                </div>
                                <button onclick="applyFilters(); event.stopPropagation();" class="btn-sima-primary" style="width: 100%; font-size: 13px; padding: 8px 14px;">
                                    Terapkan Filter
                                </button>
                            </div>
                        </div>
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

                <!-- Floating Bulk Action Bar -->
                @if(auth()->user()->isSuperAdmin())
                <div id="bulk-bar" class="bulk-action-bar-sima">
                    <span class="bulk-count"><span id="bulk-count">0</span> user dipilih</span>
                    <span id="bulk-warn" style="display:none; font-size:11px; color: var(--color-warning); background: var(--color-warning-soft); border-radius: 4px; padding: 2px 6px;">&#9888; Maks 100 user</span>
                    <div class="bulk-divider"></div>
                    <button type="button" onclick="bulkDeleteConfirm()" class="btn-sima-danger" style="padding: 5px 12px; font-size: 12.5px;">
                        <i data-feather="trash-2" style="width: 13px; height: 13px;"></i>
                        <span>Hapus User</span>
                    </button>
                    <button type="button" onclick="clearSelection()" class="btn-sima-icon" style="width: 28px; height: 28px;" title="Batal Pilih">
                        <i data-feather="x" style="width: 13px; height: 13px;"></i>
                    </button>
                </div>
                <form id="bulk-delete-form" method="POST" action="{{ route('users.bulk-destroy') }}" style="display:none;">@csrf</form>
                @endif

                <!-- Data Table (Clean, Maximum 6 Columns) -->
                <div class="table-sima-wrapper table-loading-wrap">
                    <table class="table-sima">
                        <thead>
                            <tr>
                                @if(auth()->user()->isSuperAdmin())
                                <th style="width: 44px; text-align: center;">
                                    <input type="checkbox" id="check-all" onchange="toggleAll(this)" class="sima-checkbox">
                                </th>
                                @endif
                                <th style="width: 50px; text-align: center;">No</th>
                                <th>Nama Pengguna</th>
                                <th>Username</th>
                                <th>Role</th>
                                <th style="text-align: center;">Status</th>
                                <th style="width: 120px; text-align: center;">Aksi</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($users as $index => $user)
                            <tr>
                                @if(auth()->user()->isSuperAdmin())
                                <td style="text-align: center;">
                                    <input type="checkbox" class="row-check sima-checkbox" value="{{ $user->id }}" {{ $user->id === auth()->id() ? 'disabled' : '' }} onchange="updateBulkBar()">
                                </td>
                                @endif
                                <td style="text-align: center; color: var(--color-text-muted);">{{ $users->firstItem() + $index }}</td>
                                <td>
                                    <div>
                                        <div style="font-weight: 600; color: var(--color-text);">{{ $user->nama_display }}</div>
                                        <div style="font-size: 12px; color: var(--color-text-muted);">{{ $user->role_name_display }}</div>
                                    </div>
                                </td>
                                <td>
                                    <span style="font-family: monospace; font-size: 13px; color: var(--color-text);">{{ $user->username ?? '-' }}</span>
                                </td>
                                <td>
                                    <span style="font-weight: 500; color: var(--color-text);">{{ $user->role_info['name'] }}</span>
                                </td>
                                <td style="text-align: center;">
                                    @if($user->is_aktif)
                                        <span class="status-pill status-pill-success">Aktif</span>
                                    @else
                                        <span class="status-pill status-pill-danger">Tidak Aktif</span>
                                    @endif
                                </td>
                                <td style="text-align: center;">
                                    <div style="display: inline-flex; align-items: center; justify-content: center; gap: 4px;">
                                        <a href="{{ route('users.show', $user->id) }}" class="btn-sima-icon view" title="Lihat Rincian">
                                            <i data-feather="eye"></i>
                                        </a>
                                        @if(auth()->user()->isSuperAdmin())
                                        <a href="{{ route('users.edit', $user->id) }}" class="btn-sima-icon edit" title="Edit User">
                                            <i data-feather="edit-2"></i>
                                        </a>
                                        @if($user->id !== auth()->id())
                                        <form action="{{ route('users.destroy', $user->id) }}" method="POST" style="display:inline;" id="deleteForm{{ $user->id }}">
                                            @csrf
                                            @method('DELETE')
                                            <button type="button" class="btn-sima-icon delete" title="Hapus User"
                                                data-nama="{{ $user->nama_display }}"
                                                data-pejabat-nama="{{ $user->pejabat_id ? ($user->pejabat->nama ?? '') : '' }}"
                                                onclick="confirmDeleteUserIndex(
                                                    this.getAttribute('data-nama'),
                                                    {{ $user->id }},
                                                    this.getAttribute('data-pejabat-nama'),
                                                    {{ $user->pejabat_id ?? 'null' }}
                                                )">
                                                <i data-feather="trash-2"></i>
                                            </button>
                                        </form>
                                        @endif
                                        @endif
                                    </div>
                                </td>
                            </tr>
                            @empty
                            <tr>
                                <td colspan="{{ auth()->user()->isSuperAdmin() ? 7 : 6 }}" style="text-align: center; padding: 48px 20px; color: var(--color-text-muted);">
                                    <div style="display: flex; flex-direction: column; align-items: center; justify-content: center; gap: 8px;">
                                        <i data-feather="inbox" style="width: 42px; height: 42px; stroke-width: 1.5; color: var(--color-border-dark);"></i>
                                        <p style="margin: 0; font-size: 13.5px; font-weight: 500;">Belum ada data user yang sesuai</p>
                                    </div>
                                </td>
                            </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>

                <!-- Pagination Bar -->
                <div class="pagination-bar" style="padding: 32px 20px; border-top: 1px solid var(--color-border); border-bottom-left-radius: var(--radius-lg); border-bottom-right-radius: var(--radius-lg); background: var(--color-surface); flex-wrap: wrap; gap: 14px;">
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
                    </div>

                    <div class="pagination-center">
                        <nav class="pagination-nav" id="paginationNav">
                            @if ($users->onFirstPage())
                                <span class="page-btn disabled" title="Sebelumnya"><i data-feather="chevron-left"></i></span>
                            @else
                                <button type="button" class="page-btn" onclick="goToPage({{ $users->currentPage() - 1 }})" title="Sebelumnya"><i data-feather="chevron-left"></i></button>
                            @endif

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
                            @endphp
                            @for ($p = $windowStart; $p <= $windowEnd; $p++)
                                @if ($p == $currentPage)
                                    <span class="page-btn active">{{ $p }}</span>
                                @else
                                    <button type="button" class="page-btn" onclick="goToPage({{ $p }})">{{ $p }}</button>
                                @endif
                            @endfor

                            @if ($users->hasMorePages())
                                <button type="button" class="page-btn" onclick="goToPage({{ $users->currentPage() + 1 }})" title="Berikutnya"><i data-feather="chevron-right"></i></button>
                            @else
                                <span class="page-btn disabled" title="Berikutnya"><i data-feather="chevron-right"></i></span>
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

        let appliedStatus = 'all';

        function filterByCardStatus(status) {
            appliedStatus = status;

            // Sync radio filter
            const stRadio = document.querySelector(`.status-filter[value="${status}"]`);
            if (stRadio) stRadio.checked = true;

            // Sync visual active state on cards
            document.querySelectorAll('.stat-card-sima.is-clickable').forEach(card => card.classList.remove('is-active-filter'));
            const targetCard = document.getElementById('card-status-' + status);
            if (targetCard) targetCard.classList.add('is-active-filter');

            // Sync visual active state on tabs
            document.querySelectorAll('.tabs-sima .tab-item-sima').forEach(tab => tab.classList.remove('active'));
            const targetTab = document.getElementById('tab-status-' + status);
            if (targetTab) targetTab.classList.add('active');

            applyFilters(false, 1);
        }

        window.toggleFilterPopover = function(e) {
            if (e) {
                e.preventDefault();
                e.stopPropagation();
            }
            const menu = document.getElementById('filter-dropdown-menu');
            if (!menu) return;
            const isVisible = menu.style.display === 'block';
            menu.style.display = isVisible ? 'none' : 'block';
            if (!isVisible && typeof feather !== 'undefined') {
                feather.replace();
            }
        };

        window.closeFilterPopover = function(e) {
            if (e) {
                e.preventDefault();
                e.stopPropagation();
            }
            const menu = document.getElementById('filter-dropdown-menu');
            if (menu) menu.style.display = 'none';
        };

        document.addEventListener('click', function(e) {
            const container = document.getElementById('filter-dropdown-container');
            const menu = document.getElementById('filter-dropdown-menu');
            if (container && menu && !container.contains(e.target)) {
                menu.style.display = 'none';
            }
        });

        // Search and Filter functionality
        function applyFilters(isAutoRefresh = false, pageOverride = null) {
            const filterMenu = document.getElementById('filter-dropdown-menu');
            if (filterMenu && !isAutoRefresh) filterMenu.style.display = 'none';

            const searchValue = (document.getElementById('searchInput')?.value || '').trim();
            const selectedRoles = Array.from(document.querySelectorAll('.role-filter:checked')).map(cb => cb.value);
            const selectedStatus = document.querySelector('.status-filter:checked')?.value || appliedStatus || 'all';
            const selectedSort = document.querySelector('.sort-filter:checked')?.value || 'terbaru';
            const perPageSelect = document.getElementById('perPageSelect');
            const perPageValue = perPageSelect ? perPageSelect.value : '10';
            
            // Build query params
            const params = new URLSearchParams();
            if (searchValue) params.append('search', searchValue);
            if (selectedRoles.length > 0) {
                selectedRoles.forEach(role => params.append('roles[]', role));
            }
            if (selectedStatus !== 'all') params.append('status', selectedStatus);
            if (selectedSort !== 'terbaru') params.append('sort', selectedSort);
            if (perPageValue && perPageValue !== '10') params.set('per_page', perPageValue);

            const urlParams = new URLSearchParams(window.location.search);
            let currentPage = pageOverride !== null ? pageOverride : (urlParams.get('page') || 1);
            if (currentPage > 1) {
                params.set('page', currentPage);
            }
            
            const filterQuery = params.toString();
            if (!isAutoRefresh) {
                try {
                    if (filterQuery) {
                        sessionStorage.setItem('users_filter_state', filterQuery);
                    } else {
                        sessionStorage.removeItem('users_filter_state');
                    }
                } catch(e) {}

                history.replaceState(null, '', window.location.pathname + (filterQuery ? '?' + filterQuery : ''));
            }

            // Fetch filtered data
            const wrap = document.querySelector('.table-loading-wrap');
            if (!isAutoRefresh && wrap) wrap.classList.add('is-loading');
            fetch(`{{ route('users.index') }}?${filterQuery}`, {
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

            if (!isAutoRefresh) {
                document.querySelectorAll('.dropdown-menu.show').forEach(el => el.classList.remove('show'));
                document.querySelectorAll('.dropdown.show').forEach(el => el.classList.remove('show'));
            }
        }
        
        function toggleSearchClear(input) {
            const clearBtn = document.getElementById('clear-search-btn');
            if (clearBtn) clearBtn.style.display = input.value ? 'inline-block' : 'none';
        }
        let _searchTimer = null;
        function onSearchInput(input) {
            toggleSearchClear(input);
            clearTimeout(_searchTimer);
            _searchTimer = setTimeout(function() { applyFilters(false, 1); }, 400);
        }
        function clearSearch() {
            const input = document.getElementById('searchInput');
            if (input) input.value = '';
            toggleSearchClear(input || { value: '' });
            applyFilters(false, 1);
        }
        function resetFilters() {
            const searchInput = document.getElementById('searchInput');
            if (searchInput) searchInput.value = '';
            toggleSearchClear(searchInput || { value: '' });
            
            document.querySelectorAll('.role-filter').forEach(cb => cb.checked = false);
            
            const allStatusRadio = document.querySelector('.status-filter[value="all"]');
            if (allStatusRadio) allStatusRadio.checked = true;
            
            const defaultSort = document.querySelector('.sort-filter[value="terbaru"]');
            if (defaultSort) defaultSort.checked = true;

            appliedStatus = 'all';

            document.querySelectorAll('.stat-card-sima.is-clickable').forEach(card => card.classList.remove('is-active-filter'));
            const targetCard = document.getElementById('card-status-all');
            if (targetCard) targetCard.classList.add('is-active-filter');

            document.querySelectorAll('.tabs-sima .tab-item-sima').forEach(tab => tab.classList.remove('active'));
            const targetTab = document.getElementById('tab-status-all');
            if (targetTab) targetTab.classList.add('active');
            
            try {
                sessionStorage.removeItem('users_filter_state');
            } catch(e) {}

            applyFilters(false, 1);
        }
        
        function renderKpiTrend(trendData) {
            if (!trendData) return '<span class="stat-delta" style="color: var(--color-text-muted);">0% (0)</span>';
            const percent = trendData.percent ?? 0;
            const diff = trendData.diff ?? 0;
            const formattedDiff = Number(Math.abs(diff)).toLocaleString('id-ID');

            if (percent > 0 || diff > 0) {
                return `<span class="stat-delta up">
                    <i data-feather="trending-up" style="width: 12px; height: 12px;"></i>
                    +${percent}% (+${formattedDiff})
                </span>`;
            } else if (percent < 0 || diff < 0) {
                return `<span class="stat-delta down">
                    <i data-feather="trending-down" style="width: 12px; height: 12px;"></i>
                    ${percent}% (${diff < 0 ? '-' : ''}${formattedDiff})
                </span>`;
            } else {
                return '<span class="stat-delta" style="color: var(--color-text-muted);">0% (0)</span>';
            }
        }

        function updateCardTrend(cardEl, trendData) {
            if (!cardEl) return;
            const wrapper = cardEl.querySelector('.kpi-trend-wrapper');
            if (!wrapper) return;
            const label = wrapper.querySelector('.kpi-trend-label');
            const labelHtml = label ? label.outerHTML : '<span class="kpi-trend-label" style="color: var(--color-text-muted); font-size: 11.5px; margin-left: 4px;">vs. bulan lalu</span>';
            wrapper.innerHTML = renderKpiTrend(trendData) + labelHtml;
        }
        
        function updateStats(stats) {
            if (!stats) return;

            const cardAll = document.getElementById('card-status-all');
            if (cardAll) {
                const el = cardAll.querySelector('.stat-number');
                if (el && stats.totalUsers !== undefined) el.textContent = Number(stats.totalUsers || 0).toLocaleString('id-ID');
                if (stats.trends && stats.trends.total) updateCardTrend(cardAll, stats.trends.total);
            }
            const cardAktif = document.getElementById('card-status-active');
            if (cardAktif) {
                const el = cardAktif.querySelector('.stat-number');
                if (el && stats.userAktif !== undefined) el.textContent = Number(stats.userAktif || 0).toLocaleString('id-ID');
                if (stats.trends && stats.trends.aktif) updateCardTrend(cardAktif, stats.trends.aktif);
            }
            const cardNonaktif = document.getElementById('card-status-inactive');
            if (cardNonaktif) {
                const el = cardNonaktif.querySelector('.stat-number');
                if (el && stats.userTidakAktif !== undefined) el.textContent = Number(stats.userTidakAktif || 0).toLocaleString('id-ID');
                if (stats.trends && stats.trends.tidakAktif) updateCardTrend(cardNonaktif, stats.trends.tidakAktif);
            }
            const cardAdmin = document.getElementById('card-total-admin');
            if (cardAdmin) {
                const el = cardAdmin.querySelector('.stat-number');
                if (el && stats.totalAdmin !== undefined) el.textContent = Number(stats.totalAdmin || 0).toLocaleString('id-ID');
                if (stats.trends && stats.trends.admin) updateCardTrend(cardAdmin, stats.trends.admin);
            }

            // Tab Badges
            const tabAllBadge = document.querySelector('#tab-status-all .tab-badge-sima');
            if (tabAllBadge && stats.totalUsers !== undefined) tabAllBadge.textContent = Number(stats.totalUsers || 0).toLocaleString('id-ID');

            const tabAktifBadge = document.querySelector('#tab-status-active .tab-badge-sima');
            if (tabAktifBadge && stats.userAktif !== undefined) tabAktifBadge.textContent = Number(stats.userAktif || 0).toLocaleString('id-ID');

            const tabNonaktifBadge = document.querySelector('#tab-status-inactive .tab-badge-sima');
            if (tabNonaktifBadge && stats.userTidakAktif !== undefined) tabNonaktifBadge.textContent = Number(stats.userTidakAktif || 0).toLocaleString('id-ID');
        }
        
        function updateTable(users, pagination, auth) {
            clearSelection();
            const tbody = document.querySelector('.table-sima tbody');
            if (!tbody) return;
            
            const isSuperAdmin = auth && auth.isSuperAdmin;
            const colspanCount = isSuperAdmin ? 7 : 6;
            const firstItem = pagination ? (pagination.firstItem || 1) : 1;
            
            if (!users || users.length === 0) {
                tbody.innerHTML = `
                    <tr>
                        <td colspan="${colspanCount}" style="text-align:center;padding:48px 20px;color:var(--color-text-muted);">
                            <div style="display:flex;flex-direction:column;align-items:center;justify-content:center;gap:8px;">
                                <i data-feather="inbox" style="width:40px;height:40px;color:var(--color-text-muted);stroke:var(--color-text-muted);"></i>
                                <span style="font-size:13.5px;">Belum ada data user terdaftar</span>
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
                
                const checkboxHtml = isSuperAdmin 
                    ? `<td style="text-align: center;"><input type="checkbox" class="row-check sima-checkbox" value="${user.id}" ${user.id === currentUserId ? 'disabled' : ''} onchange="updateBulkBar()"></td>`
                    : '';

                const statusPill = user.is_aktif 
                    ? `<span class="status-pill status-pill-success">Aktif</span>`
                    : `<span class="status-pill status-pill-danger">Tidak Aktif</span>`;

                const actionEdit = isSuperAdmin
                    ? `<a href="{{ url('users') }}/${user.id}/edit" class="btn-sima-icon edit" title="Edit User"><i data-feather="edit-2"></i></a>`
                    : '';

                const actionDelete = (isSuperAdmin && user.id !== currentUserId)
                    ? `<form action="{{ url('users') }}/${user.id}" method="POST" style="display:inline;" id="deleteForm${user.id}">
                            @csrf
                            @method('DELETE')
                            <button type="button" class="btn-sima-icon delete" title="Hapus User" 
                                data-nama="${user.nama.replace(/&/g, '&amp;').replace(/"/g, '&quot;')}" 
                                data-pejabat-nama="${user.pejabat ? user.pejabat.nama.replace(/&/g, '&amp;').replace(/"/g, '&quot;') : ''}" 
                                onclick="confirmDeleteUserIndex(this.getAttribute('data-nama'), ${user.id}, this.getAttribute('data-pejabat-nama'), ${user.pejabat_id ?? 'null'})">
                                <i data-feather="trash-2"></i>
                            </button>
                        </form>`
                    : '';
                
                html += `
                    <tr>
                        ${checkboxHtml}
                        <td style="text-align:center; color: var(--color-text-muted);">${rowNumber}</td>
                        <td>
                            <div>
                                <div style="font-weight: 600; color: var(--color-text);">${user.nama}</div>
                                <div style="font-size: 12px; color: var(--color-text-muted);">${user.role_name_display || user.role_name}</div>
                            </div>
                        </td>
                        <td><span style="font-family: monospace; font-size: 13px; color: var(--color-text);">${user.username ?? '-'}</span></td>
                        <td><span style="font-weight: 500; color: var(--color-text);">${user.role_info.name}</span></td>
                        <td style="text-align: center;">${statusPill}</td>
                        <td style="text-align: center;">
                            <div style="display: inline-flex; align-items: center; justify-content: center; gap: 4px;">
                                <a href="{{ url('users') }}/${user.id}" class="btn-sima-icon view" title="Lihat Rincian"><i data-feather="eye"></i></a>
                                ${actionEdit}
                                ${actionDelete}
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
                html += `<span class="page-btn disabled" title="Sebelumnya"><i data-feather="chevron-left"></i></span>`;
            } else {
                html += `<button type="button" class="page-btn" onclick="goToPage(${currentPage - 1})" title="Sebelumnya"><i data-feather="chevron-left"></i></button>`;
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
                html += `<span class="page-btn disabled" title="Berikutnya"><i data-feather="chevron-right"></i></span>`;
            } else {
                html += `<button type="button" class="page-btn" onclick="goToPage(${currentPage + 1})" title="Berikutnya"><i data-feather="chevron-right"></i></button>`;
            }

            nav.innerHTML = html;
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
                sessionStorage.removeItem('users_filter_state');
            } catch(e) {}
            @endif

            let isReturning = false;
            try {
                if (sessionStorage.getItem('users_returning') === '1') {
                    isReturning = true;
                    sessionStorage.removeItem('users_returning');
                }
            } catch(e) {}

            const ref = document.referrer || '';
            if (ref.includes('/users/') || ref.includes('/users/create') || ref.includes('/users/edit')) {
                isReturning = true;
            }
            if (navType === 'back_forward') {
                isReturning = true;
            }

            if (navType === 'reload' && !isReturning && !window._simaReloadResetHandled) {
                window._simaReloadResetHandled = true;
                try {
                    sessionStorage.removeItem('users_filter_state');
                } catch(e) {}
                if (window.location.search) {
                    history.replaceState(null, '', window.location.pathname);
                }
                const searchInput = document.getElementById('searchInput');
                if (searchInput) {
                    searchInput.value = '';
                    toggleSearchClear(searchInput);
                }
                document.querySelectorAll('.role-filter').forEach(cb => cb.checked = false);
                const allStatusRadio = document.querySelector('.status-filter[value="all"]');
                if (allStatusRadio) allStatusRadio.checked = true;
                const defaultSort = document.querySelector('.sort-filter[value="terbaru"]');
                if (defaultSort) defaultSort.checked = true;

                appliedStatus = 'all';
                applyFilters(true, 1);
                return;
            }

            if (!isReturning && navType === 'navigate' && !window.location.search) {
                try {
                    sessionStorage.removeItem('users_filter_state');
                } catch(e) {}
                return;
            }

            let currentSearch = window.location.search;
            let hasUrlQuery = (currentSearch && currentSearch.length > 1);

            if (!hasUrlQuery) {
                try {
                    const savedState = sessionStorage.getItem('users_filter_state');
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
            const searchInput = document.getElementById('searchInput');
            if (searchInput && q) {
                searchInput.value = q;
                toggleSearchClear(searchInput);
            }

            document.querySelectorAll('.role-filter').forEach(cb => cb.checked = false);
            params.getAll('roles[]').forEach(v => {
                const cb = document.querySelector('.role-filter[value="'+v+'"]');
                if (cb) cb.checked = true;
            });

            const statusParam = params.get('status') || 'all';
            appliedStatus = statusParam;
            const st = document.querySelector('.status-filter[value="'+statusParam+'"]');
            if (st) st.checked = true;

            document.querySelectorAll('.stat-card-sima.is-clickable').forEach(card => card.classList.remove('is-active-filter'));
            const targetCard = document.getElementById('card-status-' + statusParam);
            if (targetCard) targetCard.classList.add('is-active-filter');

            document.querySelectorAll('.tabs-sima .tab-item-sima').forEach(tab => tab.classList.remove('active'));
            const targetTab = document.getElementById('tab-status-' + statusParam);
            if (targetTab) targetTab.classList.add('active');

            const sortParam = params.get('sort') || 'terbaru';
            const sr = document.querySelector('.sort-filter[value="'+sortParam+'"]');
            if (sr) sr.checked = true;

            const perPage = params.get('per_page') || '10';
            const perPageSelect = document.getElementById('perPageSelect');
            if (perPageSelect) perPageSelect.value = perPage;

            const page = parseInt(params.get('page') || '1', 10);
            applyFilters(true, page);
        }

        document.addEventListener('DOMContentLoaded', function() {
            restoreFilterState();

            const autoRefreshMs = 15000;
            setInterval(function () {
                if (document.hidden) return;
                if (document.querySelectorAll('.row-check:checked, #check-all:checked').length > 0) return;
                if (document.querySelector('.modal.show, [id*="modal"][style*="display: flex"], [id*="modal"][style*="display: block"]')) return;
                if (document.querySelector('.dropdown-menu.show, .dropdown.show')) return;
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
                
                setTimeout(() => closeToast(), 5000);
                
                sessionStorage.removeItem('userFormSuccess');
                sessionStorage.removeItem('userFormMessage');
                
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

        function toggleAll(src) {
            document.querySelectorAll('.row-check:not(:disabled)').forEach(cb => { cb.checked = src.checked; });
            updateBulkBar();
        }
        function updateBulkBar() {
            const allRows = document.querySelectorAll('.row-check:not(:disabled)');
            const checked = document.querySelectorAll('.row-check:checked');
            const checkAll = document.getElementById('check-all');
            if (checkAll) {
                checkAll.checked = allRows.length > 0 && checked.length === allRows.length;
            }
            const bar = document.getElementById('bulk-bar');
            if (bar) {
                if (checked.length > 0) {
                    bar.classList.add('show');
                } else {
                    bar.classList.remove('show');
                }
                const countEl = document.getElementById('bulk-count');
                if (countEl) countEl.textContent = checked.length;
                const warn = document.getElementById('bulk-warn');
                if (warn) warn.style.display = checked.length > 100 ? 'inline-block' : 'none';
                if (typeof feather !== 'undefined') feather.replace();
            }
        }
        function clearSelection() {
            document.querySelectorAll('.row-check, #check-all').forEach(cb => { cb.checked = false; });
            const bar = document.getElementById('bulk-bar');
            if (bar) bar.classList.remove('show');
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

    <!-- MODAL IMPORT EXCEL PENGGUNA (SIMA PREMIER THEME) -->
    <div id="import-modal" class="modal-sima" style="display:none; position:fixed; inset:0; background:rgba(31,58,42,0.45); z-index:9999; align-items:center; justify-content:center; backdrop-filter: blur(4px); padding: 16px;">
        <div class="modal-sima-box" style="width: 90%; max-width: 480px; display: flex; flex-direction: column; overflow: hidden;">
            <!-- Header -->
            <div class="modal-sima-header" style="position: relative; border-bottom: none !important; padding: 20px 24px 16px 24px; display: flex; align-items: center; justify-content: center;">
                <div style="font-size: 16px; font-weight: 700; color: var(--color-text); text-align: center; width: 100%;">Import Data Pengguna</div>
            </div>

            <!-- Body -->
            <div class="modal-sima-body" style="padding: 20px 24px;">
                <!-- Unduh Template Bar -->
                <div id="import-template-bar" style="display: flex; flex-direction: column; align-items: left; justify-content: left; text-align: left; gap: 10px; padding-bottom: 16px; border-bottom: 1px dashed var(--color-border); margin-bottom: 18px;">
                    <div style="text-align: left;">
                        <div style="font-size: 13.5px; font-weight: 600; color: var(--color-text);">Template Format CSV</div>
                        <div style="font-size: 12px; color: var(--color-text-muted); margin-top: 2px;">Sudah siap dengan baris contoh dan format kolom baku.</div>
                    </div>
                    <a href="{{ route('users.template.download') }}" onclick="if(window.showToast) window.showToast('Template import pengguna (CSV) berhasil diunduh! Berkas sedang diproses...', 'success');" class="btn-sima-primary" style="align-self: flex-start; border-radius: var(--radius-full); padding: 7px 18px; font-size: 12.5px; text-decoration: none; flex-shrink: 0; display: inline-flex; align-items: center; gap: 6px;">
                        <i data-feather="download" style="width: 13px; height: 13px;"></i>
                        <span>Unduh Template CSV</span>
                    </a>
                </div>

                <!-- Form -->
                <form id="form-import-excel" enctype="multipart/form-data" onsubmit="submitImportExcel(event)">
                    @csrf
                    
                    <!-- Inline Error Alert inside Modal (SIMA Theme - Centered) -->
                    <div id="import-error-box" style="display: none; position: relative; background: var(--color-danger-soft, #FDE8E8); border: 1px solid var(--color-danger, #E02424); border-radius: var(--radius-md); padding: 18px 20px; margin-bottom: 16px; flex-direction: column; align-items: center; justify-content: center; text-align: center;">
                        <button type="button" onclick="hideImportError()" style="position: absolute; top: 10px; right: 10px; background: transparent; border: none; cursor: pointer; color: var(--color-danger, #991B1B); padding: 4px; line-height: 1; border-radius: var(--radius-sm); display: flex; align-items: center; justify-content: center;" title="Tutup">
                            <i data-feather="x" style="width: 14px; height: 14px;"></i>
                        </button>
                        <div style="width: 32px; height: 32px; border-radius: 50%; background: var(--color-danger, #E02424); display: flex; align-items: center; justify-content: center; flex-shrink: 0; margin-bottom: 8px;">
                            <i data-feather="alert-circle" style="width: 18px; height: 18px; color: #ffffff;"></i>
                        </div>
                        <div class="import-error-title" style="font-size: 13.5px; font-weight: 700; color: var(--color-danger, #991B1B); margin-bottom: 4px; text-align: center;">Format Berkas Tidak Sesuai</div>
                        <div id="import-error-message" style="font-size: 12px; color: var(--color-text, #374151); line-height: 1.5; word-break: break-word; text-align: center; max-width: 420px;"></div>
                    </div>

                    <div id="import-form-inputs">
                        <div style="margin-bottom: 14px;">
                            <label class="form-label-sima" style="display: block; margin-bottom: 8px; text-align: left;">
                                Pilih Berkas CSV (.csv)
                            </label>

                            <!-- Dropzone -->
                            <div id="import-dropzone" style="border: 1.5px dashed var(--color-border); border-radius: var(--radius-md); padding: 24px 16px; text-align: center; cursor: pointer; transition: border-color 0.15s ease, background-color 0.15s ease; background: var(--color-surface);" onclick="document.getElementById('import_file_input').click()">
                                <input type="file" id="import_file_input" name="file_excel" accept=".csv, text/csv" style="display: none;" onchange="handleImportFileSelected(this)">
                                
                                <div id="dropzone-unselected" style="display: flex; flex-direction: column; align-items: center; justify-content: center; text-align: center;">
                                    <div style="display: flex; justify-content: center; align-items: center; width: 100%; margin-bottom: 8px;">
                                        <i data-feather="upload-cloud" style="width: 28px; height: 28px; stroke-width: 1.5; color: var(--color-text-muted); display: block; margin: 0 auto;"></i>
                                    </div>
                                    <div style="font-size: 13.5px; font-weight: 600; color: var(--color-text); text-align: center;">Pilih berkas CSV atau seret ke area ini</div>
                                    <div style="font-size: 12px; color: var(--color-text-muted); margin-top: 3px; text-align: center;">Maksimal ukuran berkas 10 MB</div>
                                </div>

                                <div id="dropzone-selected" style="display: none; align-items: center; justify-content: space-between; background: var(--color-row-hover); padding: 10px 14px; border-radius: var(--radius-sm); text-align: left;">
                                    <div style="display: flex; align-items: center; gap: 10px; overflow: hidden;">
                                        <i data-feather="file-text" style="width: 18px; height: 18px; color: var(--color-primary); flex-shrink: 0;"></i>
                                        <div style="overflow: hidden;">
                                            <div id="import-file-name" style="font-size: 13px; font-weight: 600; color: var(--color-text); white-space: nowrap; overflow: hidden; text-overflow: ellipsis;"></div>
                                            <div id="import-file-size" style="font-size: 11.5px; color: var(--color-text-muted); margin-top: 1px;"></div>
                                        </div>
                                    </div>
                                    <button type="button" onclick="changeImportFile(event)" style="background: transparent; border: none; cursor: pointer; color: var(--color-primary); font-size: 12.5px; font-weight: 600; padding: 4px 8px; border-radius: var(--radius-sm); display: inline-flex; align-items: center; text-decoration: underline; text-underline-offset: 3px;">
                                        <span>Ganti</span>
                                    </button>
                                </div>
                            </div>
                        </div>

                        <!-- Duplicate Policy Helper Text -->
                        <p style="font-size: 12px; color: var(--color-text-muted); line-height: 1.5; margin: 0; text-align: center;">
                            Pengguna dengan <strong>Username</strong> yang sudah terdaftar di sistem akan otomatis dilewati (skip) agar data yang sudah ada tidak tertimpa.
                        </p>
                    </div>

                    <!-- Loading state (Premium Organic SIMA Loader) -->
                    <div id="import-loading" style="display: none; padding: 36px 16px; text-align: center;">
                        <div style="display: flex; flex-direction: column; align-items: center; justify-content: center;">
                            <div class="sima-orbital-loader" style="margin-bottom: 18px;">
                                <div class="sima-orbital-ring"></div>
                                <div class="sima-orbital-core">
                                    <i data-feather="file-text" style="width: 22px; height: 22px; color: var(--color-primary);"></i>
                                </div>
                            </div>
                            <div style="font-size: 15px; font-weight: 700; color: var(--color-text); letter-spacing: -0.01em; margin-bottom: 6px;">
                                Memproses Berkas CSV
                            </div>
                            <div id="import-dynamic-stage" style="font-size: 12.5px; color: var(--color-text-muted); min-height: 20px; transition: opacity 0.22s ease, transform 0.22s ease;">
                                Membaca dan memvalidasi baris data...
                            </div>
                            <div class="sima-progress-track">
                                <div class="sima-progress-indicator"></div>
                            </div>
                        </div>
                    </div>

                    <!-- Result state (Clean, Professional & Human-Centered) -->
                    <div id="import-result" style="display: none;">
                        <div style="text-align: center; margin-bottom: 18px;">
                            <h4 id="res-headline-title" style="font-size: 15px; font-weight: 700; color: var(--color-text); margin: 0 0 6px 0;">Impor Selesai Diproses</h4>
                            <p id="res-headline-desc" style="font-size: 12.5px; color: var(--color-text-muted); margin: 0 auto; max-width: 440px; line-height: 1.45;"></p>
                        </div>

                        <!-- Unified Summary Strip -->
                        <div style="background: var(--color-surface); border: 1px solid var(--color-border); border-radius: var(--radius-md); padding: 14px 16px; margin-bottom: 16px; display: grid; grid-template-columns: repeat(3, 1fr); align-items: center;">
                            <!-- Berhasil (Hijau) -->
                            <div style="text-align: center; padding: 0 8px; border-right: 1px dashed var(--color-border);">
                                <div style="font-size: 11px; font-weight: 600; color: var(--color-success); text-transform: uppercase; letter-spacing: 0.5px;">Berhasil</div>
                                <div style="margin-top: 4px; display: flex; align-items: baseline; justify-content: center; gap: 4px;">
                                    <span id="res-count-imported" style="font-size: 22px; font-weight: 700; color: var(--color-success); font-variant-numeric: tabular-nums;">0</span>
                                    <span style="font-size: 11.5px; color: var(--color-text-muted); font-weight: 500;">pengguna</span>
                                </div>
                                <div style="font-size: 11px; color: var(--color-text-muted); margin-top: 2px;">Data baru masuk</div>
                            </div>

                            <!-- Dilewati (Kuning) -->
                            <div style="text-align: center; padding: 0 8px; border-right: 1px dashed var(--color-border);">
                                <div style="font-size: 11px; font-weight: 600; color: var(--color-warning); text-transform: uppercase; letter-spacing: 0.5px;">Dilewati</div>
                                <div style="margin-top: 4px; display: flex; align-items: baseline; justify-content: center; gap: 4px;">
                                    <span id="res-count-skipped" style="font-size: 22px; font-weight: 700; color: var(--color-warning); font-variant-numeric: tabular-nums;">0</span>
                                    <span style="font-size: 11.5px; color: var(--color-text-muted); font-weight: 500;">pengguna</span>
                                </div>
                                <div style="font-size: 11px; color: var(--color-text-muted); margin-top: 2px;">Username sama</div>
                            </div>

                            <!-- Gagal (Merah) -->
                            <div style="text-align: center; padding: 0 8px;">
                                <div style="font-size: 11px; font-weight: 600; color: var(--color-danger); text-transform: uppercase; letter-spacing: 0.5px;">Gagal</div>
                                <div style="margin-top: 4px; display: flex; align-items: baseline; justify-content: center; gap: 4px;">
                                    <span id="res-count-failed" style="font-size: 22px; font-weight: 700; color: var(--color-danger); font-variant-numeric: tabular-nums;">0</span>
                                    <span style="font-size: 11.5px; color: var(--color-text-muted); font-weight: 500;">pengguna</span>
                                </div>
                                <div style="font-size: 11px; color: var(--color-text-muted); margin-top: 2px;">Format tidak valid</div>
                            </div>
                        </div>

                        <!-- Report list of skipped / failed rows -->
                        <div id="res-details-container" style="display: none; margin-bottom: 8px;">
                            <div style="display: flex; align-items: center; justify-content: space-between; margin-bottom: 8px; font-size: 12px;">
                                <span style="font-weight: 600; color: var(--color-text);">Catatan Pemeriksaan Data</span>
                                <span id="res-details-subtitle" style="color: var(--color-text-muted); font-size: 11.5px;"></span>
                            </div>
                            <div style="border: 1px solid var(--color-border); border-radius: var(--radius-md); overflow: hidden; max-height: 220px; overflow-y: auto; background: var(--color-surface);">
                                <table style="width: 100%; border-collapse: collapse; font-size: 12px;">
                                    <thead style="background: var(--color-row-hover); position: sticky; top: 0; z-index: 1;">
                                        <tr style="border-bottom: 1px dashed var(--color-border);">
                                            <th style="padding: 8px 12px; text-align: center; font-size: 11px; font-weight: 600; color: var(--color-text-muted); text-transform: uppercase; letter-spacing: 0.5px; width: 64px;">Baris</th>
                                            <th style="padding: 8px 12px; text-align: left; font-size: 11px; font-weight: 600; color: var(--color-text-muted); text-transform: uppercase; letter-spacing: 0.5px; width: 92px;">Status</th>
                                            <th style="padding: 8px 12px; text-align: left; font-size: 11px; font-weight: 600; color: var(--color-text-muted); text-transform: uppercase; letter-spacing: 0.5px;">Identitas Pengguna</th>
                                            <th style="padding: 8px 12px; text-align: left; font-size: 11px; font-weight: 600; color: var(--color-text-muted); text-transform: uppercase; letter-spacing: 0.5px;">Keterangan</th>
                                        </tr>
                                    </thead>
                                    <tbody id="res-details-body"></tbody>
                                </table>
                            </div>
                        </div>
                    </div>

                    <!-- Footer Default -->
                    <div class="modal-sima-footer" id="import-footer-default" style="padding-top: 18px; margin-top: 20px; border-top: none !important; justify-content: center !important;">
                        <button type="button" onclick="closeImportModal()" class="btn-sima-secondary">
                            Batal
                        </button>
                        <button type="submit" id="btn-submit-import" class="btn-sima-primary" disabled>
                            Mulai Impor
                        </button>
                    </div>

                    <!-- Footer Completed -->
                    <div class="modal-sima-footer" id="import-footer-done" style="display: none; padding-top: 18px; margin-top: 20px; border-top: none !important; justify-content: center !important; gap: 10px;">
                        <button type="button" onclick="backToImportUploadState()" class="btn-sima-secondary" style="flex: 1; justify-content: center;">
                            <span>Impor Berkas Lain</span>
                        </button>
                        <button type="button" onclick="finishImportAndRefresh()" class="btn-sima-primary" style="flex: 1; justify-content: center;">
                            <span>Selesai & Muat Data</span>
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <script>
        // ==========================================
        // FITUR IMPORT EXCEL MASTER PENGGUNA
        // ==========================================
        let importStageTimer = null;
        let importStatsAccumulator = { imported: 0, skipped: 0, failed: 0 };
        const importStages = [
            'Membaca dan memvalidasi baris data...',
            'Memeriksa duplikasi Username...',
            'Mendaftarkan akun pengguna ke sistem...'
        ];

        function startImportStageAnimation() {
            stopImportStageAnimation();
            const stageEl = document.getElementById('import-dynamic-stage');
            if (!stageEl) return;
            stageEl.textContent = importStages[0];
            stageEl.style.opacity = '1';
            stageEl.style.transform = 'translateY(0)';
            let stageIndex = 0;
            importStageTimer = setInterval(() => {
                stageIndex = (stageIndex + 1) % importStages.length;
                stageEl.style.opacity = '0';
                stageEl.style.transform = 'translateY(-4px)';
                setTimeout(() => {
                    stageEl.textContent = importStages[stageIndex];
                    stageEl.style.transform = 'translateY(4px)';
                    setTimeout(() => {
                        stageEl.style.opacity = '1';
                        stageEl.style.transform = 'translateY(0)';
                    }, 40);
                }, 220);
            }, 1400);
        }

        function stopImportStageAnimation() {
            if (importStageTimer) {
                clearInterval(importStageTimer);
                importStageTimer = null;
            }
        }

        function animateNumberCount(el, start, end, duration = 650) {
            if (!el) return;
            const target = parseInt(end) || 0;
            if (target === 0) {
                el.textContent = '0';
                return;
            }
            const startTime = performance.now();
            function step(currentTime) {
                const elapsed = currentTime - startTime;
                const progress = Math.min(elapsed / duration, 1);
                const ease = 1 - Math.pow(1 - progress, 3);
                const current = Math.round(start + (target - start) * ease);
                el.textContent = current;
                if (progress < 1) {
                    requestAnimationFrame(step);
                } else {
                    el.textContent = target;
                }
            }
            requestAnimationFrame(step);
        }

        let hasImportedAny = false;
        let importErrorHideTimeout = null;

        function showImportError(msg, title = 'Format Berkas Tidak Sesuai') {
            const errorBox = document.getElementById('import-error-box');
            const errorMsg = document.getElementById('import-error-message');
            if (!errorBox || !errorMsg) return;

            if (importErrorHideTimeout) {
                clearTimeout(importErrorHideTimeout);
                importErrorHideTimeout = null;
            }

            const titleEl = errorBox.querySelector('.import-error-title');
            if (titleEl) titleEl.textContent = title;

            errorMsg.textContent = msg;

            errorBox.style.display = 'flex';
            errorBox.style.transition = 'opacity 0.25s cubic-bezier(0.16, 1, 0.3, 1), transform 0.25s cubic-bezier(0.16, 1, 0.3, 1)';
            errorBox.style.opacity = '0';
            errorBox.style.transform = 'translateY(-6px)';

            void errorBox.offsetWidth; // Force DOM reflow

            errorBox.style.opacity = '1';
            errorBox.style.transform = 'translateY(0)';
            if (typeof feather !== 'undefined') feather.replace();
        }

        function hideImportError(immediate = false) {
            const errorBox = document.getElementById('import-error-box');
            if (!errorBox || errorBox.style.display === 'none') return;

            if (importErrorHideTimeout) {
                clearTimeout(importErrorHideTimeout);
                importErrorHideTimeout = null;
            }

            if (immediate) {
                errorBox.style.display = 'none';
                errorBox.style.opacity = '0';
                return;
            }

            errorBox.style.transition = 'opacity 0.22s cubic-bezier(0.4, 0, 0.2, 1), transform 0.22s cubic-bezier(0.4, 0, 0.2, 1)';
            errorBox.style.opacity = '0';
            errorBox.style.transform = 'translateY(-6px)';

            importErrorHideTimeout = setTimeout(() => {
                errorBox.style.display = 'none';
                importErrorHideTimeout = null;
            }, 220);
        }

        function backToImportUploadState() {
            stopImportStageAnimation();
            hideImportError(true);

            const resultEl = document.getElementById('import-result');
            if (resultEl) {
                resultEl.classList.remove('sima-result-appear');
                resultEl.style.display = 'none';
            }
            const footerDone = document.getElementById('import-footer-done');
            if (footerDone) {
                footerDone.classList.remove('sima-fade-in');
                footerDone.style.display = 'none';
            }
            const detailsContainer = document.getElementById('res-details-container');
            if (detailsContainer) {
                detailsContainer.classList.remove('sima-fade-in');
                detailsContainer.style.display = 'none';
            }

            const templateBar = document.getElementById('import-template-bar');
            if (templateBar) templateBar.style.display = 'flex';
            const formInputs = document.getElementById('import-form-inputs');
            if (formInputs) formInputs.style.display = 'block';
            const footerDefault = document.getElementById('import-footer-default');
            if (footerDefault) footerDefault.style.display = 'flex';

            const fileInput = document.getElementById('import_file_input');
            if (fileInput) fileInput.value = '';
            const dropzoneUnselected = document.getElementById('dropzone-unselected');
            if (dropzoneUnselected) dropzoneUnselected.style.display = 'flex';
            const dropzoneSelected = document.getElementById('dropzone-selected');
            if (dropzoneSelected) dropzoneSelected.style.display = 'none';
            const btnSubmit = document.getElementById('btn-submit-import');
            if (btnSubmit) btnSubmit.disabled = true;

            if (typeof feather !== 'undefined') feather.replace();
        }

        function openImportModal() {
            const modal = document.getElementById('import-modal');
            if (!modal) return;
            hasImportedAny = false;
            importStatsAccumulator = { imported: 0, skipped: 0, failed: 0 };
            backToImportUploadState();
            modal.style.display = 'flex';
            if (typeof feather !== 'undefined') feather.replace();
        }

        function closeImportModal() {
            stopImportStageAnimation();
            hideImportError(true);
            const modal = document.getElementById('import-modal');
            if (modal) modal.style.display = 'none';
            if (hasImportedAny) {
                window.location.reload();
            }
        }

        function handleImportFileSelected(input) {
            hideImportError();
            if (!input.files || input.files.length === 0) return;
            const file = input.files[0];

            if (!file.name.toLowerCase().endsWith('.csv')) {
                showImportError('Hanya menerima format berkas CSV (.csv). Berkas Excel (.xlsx/.xls) tidak didukung.', 'Format Tidak Sesuai');
                cancelImportFile();
                return;
            }

            document.getElementById('import-file-name').textContent = file.name;
            const sizeKb = (file.size / 1024).toFixed(1);
            const sizeStr = file.size > 1024 * 1024 ? (file.size / (1024 * 1024)).toFixed(2) + ' MB' : sizeKb + ' KB';
            document.getElementById('import-file-size').textContent = sizeStr;

            document.getElementById('dropzone-unselected').style.display = 'none';
            document.getElementById('dropzone-selected').style.display = 'flex';
            document.getElementById('btn-submit-import').disabled = false;
            if (typeof feather !== 'undefined') feather.replace();
        }

        function changeImportFile(e) {
            if (e) {
                e.preventDefault();
                e.stopPropagation();
            }
            hideImportError();
            const fileInput = document.getElementById('import_file_input');
            if (fileInput) fileInput.click();
        }

        function cancelImportFile(e) {
            if (e) e.stopPropagation();
            hideImportError();
            document.getElementById('import_file_input').value = '';
            document.getElementById('dropzone-unselected').style.display = 'flex';
            document.getElementById('dropzone-selected').style.display = 'none';
            document.getElementById('btn-submit-import').disabled = true;
            if (typeof feather !== 'undefined') feather.replace();
        }

        function submitImportExcel(e) {
            e.preventDefault();
            hideImportError(true);
            const fileInput = document.getElementById('import_file_input');
            if (!fileInput.files || fileInput.files.length === 0) {
                showImportError('Silakan pilih file CSV terlebih dahulu.', 'Berkas Belum Dipilih');
                return;
            }

            const formData = new FormData(document.getElementById('form-import-excel'));

            const templateBar = document.getElementById('import-template-bar');
            if (templateBar) templateBar.style.display = 'none';
            document.getElementById('import-form-inputs').style.display = 'none';
            document.getElementById('import-footer-default').style.display = 'none';
            document.getElementById('import-loading').style.display = 'block';
            if (typeof feather !== 'undefined') feather.replace();
            startImportStageAnimation();

            fetch('{{ route("users.import") }}', {
                method: 'POST',
                headers: {
                    'X-Requested-With': 'XMLHttpRequest',
                    'Accept': 'application/json'
                },
                body: formData
            })
            .then(res => res.json())
            .then(data => {
                stopImportStageAnimation();
                document.getElementById('import-loading').style.display = 'none';

                if (data.success) {
                    const resultEl = document.getElementById('import-result');
                    resultEl.classList.remove('sima-result-appear');
                    void resultEl.offsetWidth;
                    resultEl.classList.add('sima-result-appear');
                    resultEl.style.display = 'block';

                    const footerDone = document.getElementById('import-footer-done');
                    footerDone.classList.remove('sima-fade-in');
                    void footerDone.offsetWidth;
                    footerDone.classList.add('sima-fade-in');
                    footerDone.style.display = 'flex';

                    const stats = data.stats || {};
                    const importedCount = stats.imported_count || 0;
                    const skippedCount = stats.skipped_count || 0;
                    const failedCount = stats.failed_count || 0;

                    importStatsAccumulator.imported += importedCount;
                    importStatsAccumulator.skipped += skippedCount;
                    importStatsAccumulator.failed += failedCount;

                    const importedEl = document.getElementById('res-count-imported');
                    const skippedEl = document.getElementById('res-count-skipped');
                    const failedEl = document.getElementById('res-count-failed');
                    const titleEl = document.getElementById('res-headline-title');
                    const descEl = document.getElementById('res-headline-desc');

                    importedEl.textContent = '0';
                    skippedEl.textContent = '0';
                    failedEl.textContent = '0';

                    animateNumberCount(importedEl, 0, importedCount, 600);
                    animateNumberCount(skippedEl, 0, skippedCount, 600);
                    animateNumberCount(failedEl, 0, failedCount, 600);

                    importedEl.style.color = 'var(--color-success)';
                    skippedEl.style.color = 'var(--color-warning)';
                    failedEl.style.color = 'var(--color-danger)';

                    if (importedCount > 0 && failedCount === 0) {
                        titleEl.textContent = 'Impor Berhasil Ditambahkan';
                        descEl.textContent = skippedCount > 0
                            ? `${importedCount} akun baru berhasil disimpan, dan ${skippedCount} akun yang sudah terdaftar dilewati.`
                            : `${importedCount} data akun baru berhasil disimpan ke dalam sistem.`;
                    } else if (importedCount === 0 && skippedCount > 0 && failedCount === 0) {
                        titleEl.textContent = 'Data Sudah Terdaftar (Dilewati Otomatis)';
                        descEl.textContent = `Seluruh ${skippedCount} akun pada berkas Excel sudah ada di sistem. Baris dilewati agar data eksisting tidak tertimpa.`;
                    } else if (failedCount > 0) {
                        titleEl.textContent = 'Impor Selesai dengan Catatan';
                        descEl.textContent = `${importedCount} data baru masuk, ${skippedCount} dilewati, dan ${failedCount} baris memerlukan koreksi format.`;
                    } else {
                        titleEl.textContent = 'Pemeriksaan Berkas Selesai';
                        descEl.textContent = 'Tidak ada baris data pengguna baru yang ditambahkan ke sistem.';
                    }

                    const tbody = document.getElementById('res-details-body');
                    tbody.innerHTML = '';
                    const issues = [];

                    (data.failed_rows || []).forEach(r => {
                        issues.push({
                            row: r.row,
                            status: '<span class="status-pill status-pill-danger" style="font-size: 11px; padding: 2px 8px; font-weight: 600;">Gagal</span>',
                            nama: r.nama || 'Pengguna',
                            username: r.username || '-',
                            reason: r.reason || 'Format atau kelengkapan data tidak valid'
                        });
                    });

                    (data.skipped_rows || []).forEach(r => {
                        issues.push({
                            row: r.row,
                            status: '<span class="status-pill status-pill-warning" style="font-size: 11px; padding: 2px 8px; font-weight: 600;">Dilewati</span>',
                            nama: r.nama || 'Pengguna',
                            username: r.username || '-',
                            reason: r.reason || 'Username sudah terdaftar di sistem'
                        });
                    });

                    if (issues.length > 0) {
                        document.getElementById('res-details-subtitle').textContent = `${issues.length} baris tidak ditambahkan`;
                        issues.forEach((it, idx) => {
                            const tr = document.createElement('tr');
                            if (idx < issues.length - 1) {
                                tr.style.borderBottom = '1px dashed var(--color-border)';
                            }
                            tr.innerHTML = `
                                <td style="padding: 10px 12px; text-align: center; font-weight: 600; color: var(--color-text-muted); font-size: 12px; font-variant-numeric: tabular-nums;">#${it.row}</td>
                                <td style="padding: 10px 12px;">${it.status}</td>
                                <td style="padding: 10px 12px;">
                                    <div style="font-weight: 600; color: var(--color-text); font-size: 12.5px; line-height: 1.35;">${it.nama}</div>
                                    <div style="font-size: 11px; color: var(--color-text-muted); margin-top: 2px;">Username: <code>${it.username}</code></div>
                                </td>
                                <td style="padding: 10px 12px; color: var(--color-text-muted); font-size: 12px; line-height: 1.4;">${it.reason}</td>
                            `;
                            tbody.appendChild(tr);
                        });
                        const detailsContainer = document.getElementById('res-details-container');
                        detailsContainer.classList.remove('sima-fade-in');
                        void detailsContainer.offsetWidth;
                        detailsContainer.classList.add('sima-fade-in');
                        detailsContainer.style.display = 'block';
                    } else {
                        document.getElementById('res-details-container').style.display = 'none';
                    }

                    if (importedCount > 0) {
                        hasImportedAny = true;
                    }

                    if (typeof feather !== 'undefined') feather.replace();
                } else {
                    const templateBar = document.getElementById('import-template-bar');
                    if (templateBar) templateBar.style.display = 'flex';
                    document.getElementById('import-form-inputs').style.display = 'block';
                    document.getElementById('import-footer-default').style.display = 'flex';
                    document.getElementById('import-result').style.display = 'none';
                    document.getElementById('import-footer-done').style.display = 'none';
                    showImportError(data.message || 'Gagal memproses file Excel.');
                }
            })
            .catch(err => {
                stopImportStageAnimation();
                document.getElementById('import-loading').style.display = 'none';
                const templateBar = document.getElementById('import-template-bar');
                if (templateBar) templateBar.style.display = 'flex';
                document.getElementById('import-form-inputs').style.display = 'block';
                document.getElementById('import-footer-default').style.display = 'flex';
                showImportError('Terjadi kesalahan jaringan atau server saat mengunggah file: ' + (err.message || err), 'Kesalahan Jaringan');
            });
        }

        function finishImportAndRefresh() {
            if (importStatsAccumulator) {
                const imported = importStatsAccumulator.imported || 0;
                const failed = importStatsAccumulator.failed || 0;
                const skipped = importStatsAccumulator.skipped || 0;
                const total = imported + failed + skipped;

                if (total > 0) {
                    let msg = '';
                    let type = 'success';
                    let title = 'Berhasil';

                    if (imported > 0 && failed === 0 && skipped === 0) {
                        msg = 'Import data pengguna berhasil! Seluruh data (' + imported + ' data) berhasil diimpor.';
                    } else if (imported > 0) {
                        msg = 'Import data pengguna selesai: ' + imported + ' data baru disimpan (' + skipped + ' dilewati, ' + failed + ' gagal).';
                    } else if (skipped > 0 && failed === 0) {
                        msg = 'Pemeriksaan selesai: Seluruh ' + skipped + ' data dilewati otomatis karena sudah terdaftar di sistem.';
                        type = 'info';
                        title = 'Data Sudah Terdaftar';
                    } else if (failed > 0) {
                        msg = 'Import selesai dengan catatan: ' + failed + ' baris memerlukan perbaikan format.';
                        type = 'warning';
                        title = 'Perhatian';
                    }

                    if (msg) {
                        try {
                            sessionStorage.setItem('pending_toast', JSON.stringify({
                                message: msg,
                                type: type,
                                title: title
                            }));
                        } catch (e) {
                            console.error('Error saving pending toast:', e);
                        }
                    }
                }
            }
            closeImportModal();
            window.location.reload();
        }

        document.addEventListener('DOMContentLoaded', function() {
            const dropzone = document.getElementById('import-dropzone');
            if (dropzone) {
                ['dragenter', 'dragover'].forEach(eventName => {
                    dropzone.addEventListener(eventName, (e) => {
                        e.preventDefault();
                        e.stopPropagation();
                        dropzone.style.borderColor = 'var(--color-primary, #1f3a2a)';
                        dropzone.style.background = 'var(--color-primary-tint, #d5e4d7)';
                    }, false);
                });

                ['dragleave', 'drop'].forEach(eventName => {
                    dropzone.addEventListener(eventName, (e) => {
                        e.preventDefault();
                        e.stopPropagation();
                        dropzone.style.borderColor = 'var(--color-border, #cfdcc8)';
                        dropzone.style.background = 'var(--color-surface, #f8faf7)';
                    }, false);
                });

                dropzone.addEventListener('drop', (e) => {
                    const dt = e.dataTransfer;
                    const files = dt.files;
                    if (files.length > 0) {
                        const fileInput = document.getElementById('import_file_input');
                        fileInput.files = files;
                        handleImportFileSelected(fileInput);
                    }
                }, false);
            }
        });
    </script>
</body>
</html>
