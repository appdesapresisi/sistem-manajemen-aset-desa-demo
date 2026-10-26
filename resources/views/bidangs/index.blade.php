<!DOCTYPE html>
<html lang="id">
<head>
    @include('layouts.head-page-meta', ['title' => 'Bidang - Sistem Manajemen Aset Desa'])
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
                        Master Bidang
                    </h1>
                </div>
                <div class="page-header-actions" style="display: flex; gap: 10px; align-items: center; flex-wrap: wrap;">
                    <div class="sima-dropdown-wrapper" style="position: relative; display: inline-block;">
                        <button type="button" id="btn-export-bidangs" class="btn-sima-secondary btn-sima-pill-white" onclick="toggleExportMenu(this, event)">
                            <i data-feather="download" style="width: 14px; height: 14px;"></i>
                            <span>Ekspor Data</span>
                            <i data-feather="chevron-down" style="width: 12px; height: 12px; opacity: 0.7;"></i>
                        </button>
                        <div class="export-dropdown-menu" style="display: none;">
                            <a class="export-menu-item" id="export-bidangs-excel" href="{{ route('bidangs.export', ['format' => 'excel']) }}" onclick="if(window.showToast) window.showToast('Ekspor data bidang (Excel) berhasil! Berkas sedang diunduh...', 'success'); this.closest('.export-dropdown-menu').style.display='none';">
                                <i data-feather="file-text" style="width: 14px; height: 14px; color: #10b981;"></i>
                                <span>Ekspor Excel (.xlsx)</span>
                            </a>
                            <hr class="dropdown-divider sima-divider" />
                            <a class="export-menu-item" id="export-bidangs-pdf" href="{{ route('bidangs.export', ['format' => 'pdf']) }}" onclick="if(window.showToast) window.showToast('Ekspor data bidang (PDF) berhasil! Berkas sedang diunduh...', 'success'); this.closest('.export-dropdown-menu').style.display='none';">
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
                    <a href="{{ route('bidangs.create') }}" class="btn-sima-primary">
                        <i data-feather="plus" style="width: 14px; height: 14px;"></i>
                        <span>Tambah Bidang</span>
                    </a>
                    @endif
                </div>
            </div>

            <!-- 1. STAT CARDS (Prodex 4-Card Summary Bar) -->
            <div class="stat-card-row">
                <!-- Card 1: Total Bidang -->
                <div class="stat-card-sima" id="card-total-bidang">
                    <div class="stat-header">
                        <span class="stat-label">TOTAL BIDANG</span>
                    </div>
                    <div class="stat-number">{{ number_format($stats['totalBidangs'] ?? 0, 0, ',', '.') }}</div>
                    <div class="kpi-trend-wrapper stat-delta">
                        @php 
                            $tBid = $stats['trends']['totalBidangs']['percent'] ?? 0;
                            $dBid = $stats['trends']['totalBidangs']['diff'] ?? 0;
                        @endphp
                        @if($tBid > 0 || $dBid > 0)
                            <span class="stat-delta-pill is-up">
                                <i data-feather="trending-up" style="width: 11px; height: 11px;"></i>
                                +{{ $tBid }}% (+{{ number_format($dBid, 0, ',', '.') }})
                            </span>
                        @elseif($tBid < 0 || $dBid < 0)
                            <span class="stat-delta-pill is-down">
                                <i data-feather="trending-down" style="width: 11px; height: 11px;"></i>
                                {{ $tBid }}% ({{ number_format($dBid, 0, ',', '.') }})
                            </span>
                        @else
                            <span class="stat-delta-pill is-neutral">0% (0)</span>
                        @endif
                        <span class="stat-delta-label">vs. bulan lalu</span>
                    </div>
                </div>

                <!-- Card 2: Total User -->
                <div class="stat-card-sima" id="card-total-user">
                    <div class="stat-header">
                        <span class="stat-label">TOTAL USER</span>
                    </div>
                    <div class="stat-number">{{ number_format($stats['totalUsers'] ?? 0, 0, ',', '.') }}</div>
                    <div class="kpi-trend-wrapper stat-delta">
                        @php 
                            $tUsers = $stats['trends']['totalUsers']['percent'] ?? 0;
                            $dUsers = $stats['trends']['totalUsers']['diff'] ?? 0;
                        @endphp
                        @if($tUsers > 0 || $dUsers > 0)
                            <span class="stat-delta-pill is-up">
                                <i data-feather="trending-up" style="width: 11px; height: 11px;"></i>
                                +{{ $tUsers }}% (+{{ number_format($dUsers, 0, ',', '.') }})
                            </span>
                        @elseif($tUsers < 0 || $dUsers < 0)
                            <span class="stat-delta-pill is-down">
                                <i data-feather="trending-down" style="width: 11px; height: 11px;"></i>
                                {{ $tUsers }}% ({{ number_format($dUsers, 0, ',', '.') }})
                            </span>
                        @else
                            <span class="stat-delta-pill is-neutral">0% (0)</span>
                        @endif
                        <span class="stat-delta-label">vs. bulan lalu</span>
                    </div>
                </div>

                <!-- Card 3: Bidang Terisi User -->
                <div class="stat-card-sima" id="card-terisi-user">
                    <div class="stat-header">
                        <span class="stat-label">BIDANG TERISI USER</span>
                    </div>
                    <div class="stat-number">{{ number_format($stats['bidangWithUsers'] ?? 0, 0, ',', '.') }}</div>
                    <div class="kpi-trend-wrapper stat-delta">
                        @php 
                            $tTerisi = $stats['trends']['bidangWithUsers']['percent'] ?? 0;
                            $dTerisi = $stats['trends']['bidangWithUsers']['diff'] ?? 0;
                        @endphp
                        @if($tTerisi > 0 || $dTerisi > 0)
                            <span class="stat-delta-pill is-up">
                                <i data-feather="trending-up" style="width: 11px; height: 11px;"></i>
                                +{{ $tTerisi }}% (+{{ number_format($dTerisi, 0, ',', '.') }})
                            </span>
                        @elseif($tTerisi < 0 || $dTerisi < 0)
                            <span class="stat-delta-pill is-down">
                                <i data-feather="trending-down" style="width: 11px; height: 11px;"></i>
                                {{ $tTerisi }}% ({{ number_format($dTerisi, 0, ',', '.') }})
                            </span>
                        @else
                            <span class="stat-delta-pill is-neutral">0% (0)</span>
                        @endif
                        <span class="stat-delta-label">vs. bulan lalu</span>
                    </div>
                </div>

                <!-- Card 4: Bidang User Terbanyak -->
                <div class="stat-card-sima" id="card-top-bidang">
                    <div class="stat-header">
                        <span class="stat-label">BIDANG USER TERBANYAK</span>
                    </div>
                    <div class="stat-number" style="font-size: 18px; line-height: 1.3; white-space: nowrap; overflow: hidden; text-overflow: ellipsis;" title="{{ $stats['topBidangNama'] ?? '-' }}">{{ $stats['topBidangNama'] ?? '-' }}</div>
                    <div class="kpi-trend-wrapper stat-delta">
                        <span class="stat-delta-pill is-up">
                            {{ number_format($stats['topBidangCount'] ?? 0, 0, ',', '.') }} user
                        </span>
                        <span class="stat-delta-label">terbanyak</span>
                    </div>
                </div>
            </div>

            <!-- 2. DATA CARD CONTAINER -->
            <div class="sima-card" style="padding: 0; overflow: visible; margin-bottom: 32px;">
                <!-- Header Controls Bar -->
                <div style="display: flex; justify-content: space-between; align-items: center; padding: 20px 20px; border-bottom: 1px solid var(--color-border); border-top-left-radius: var(--radius-lg); border-top-right-radius: var(--radius-lg); flex-wrap: wrap; gap: 12px;">
                    <!-- Left: Search Box -->
                    <div class="topbar-search-box">
                        <i data-feather="search" style="width: 15px; height: 15px; color: var(--color-text-muted); flex-shrink: 0;"></i>
                        <input
                            type="text"
                            id="searchInput"
                            class="topbar-search-input"
                            placeholder="Cari bidang..."
                            value="{{ request('search') }}"
                            oninput="onSearchInput(this)"
                            onkeypress="if(event.key==='Enter'){fetchBidangs(false, 1);}"
                            autocomplete="off"
                        >
                        <button
                            type="button"
                            id="clear-search-btn"
                            class="topbar-search-shortcut"
                            onclick="clearSearch()"
                            style="display: {{ request('search') ? 'block' : 'none' }}; border: none; background: none; cursor: pointer; color: var(--color-text-muted); font-size: 11px; padding: 0 4px;"
                            title="Hapus pencarian"
                        >Batal</button>
                    </div>

                    <!-- Right Controls: Reset Filter & Filter Dropdown -->
                    <div style="display: flex; align-items: center; gap: 8px;">
                        <button type="button" class="btn-sima-secondary btn-reset-filter" id="btn-reset-filter" onclick="resetFilters()" title="Reset semua filter" style="padding: 7px 12px; font-size: 13px;">
                            <i data-feather="rotate-ccw" style="width: 13px; height: 13px;"></i>
                            <span>Reset Filter</span>
                        </button>

                        <div class="dropdown" id="filter-dropdown-container" style="position: relative;">
                            <button type="button" id="btn-filter-toggle" class="btn-sima-secondary" onclick="toggleFilterPopover(event)" title="Filter" style="padding: 7px 12px; font-size: 13px; display: inline-flex; align-items: center; gap: 6px;">
                                <i data-feather="sliders" style="width: 13px; height: 13px;"></i>
                                <span>Filter</span>
                            </button>
                            <div id="filter-dropdown-menu" class="sima-filter-popover" onclick="event.stopPropagation();" style="display: none; position: absolute; top: calc(100% + 4px); right: 0; min-width: 300px; padding: 20px 24px; z-index: 1050; background: var(--color-surface); border: 1px solid var(--color-border); border-radius: var(--radius-lg); box-shadow: var(--shadow-float); margin: 0;">
                                <div style="color: var(--color-primary); font-size: 12px; font-weight: 700; text-transform: uppercase; letter-spacing: 0.04em; margin-bottom: 8px;">Filter Status</div>
                                <div style="display: flex; flex-direction: column; gap: 8px; margin-bottom: 14px;">
                                    <label style="cursor: pointer; display: flex; align-items: center; gap: 8px; font-size: 13px; color: var(--color-text); margin: 0;">
                                        <input type="radio" name="statusFilter" value="all" class="status-filter" checked style="accent-color: var(--color-primary); cursor: pointer;"> Semua Status
                                    </label>
                                    <label style="cursor: pointer; display: flex; align-items: center; gap: 8px; font-size: 13px; color: var(--color-text); margin: 0;">
                                        <input type="radio" name="statusFilter" value="with_users" class="status-filter" style="accent-color: var(--color-primary); cursor: pointer;"> Memiliki User
                                    </label>
                                    <label style="cursor: pointer; display: flex; align-items: center; gap: 8px; font-size: 13px; color: var(--color-text); margin: 0;">
                                        <input type="radio" name="statusFilter" value="no_users" class="status-filter" style="accent-color: var(--color-primary); cursor: pointer;"> Tanpa User (Kosong)
                                    </label>
                                </div>
                                <hr style="border: none; border-top: 1px dashed var(--color-border); margin: 12px 0;" />
                                <div style="color: var(--color-primary); font-size: 12px; font-weight: 700; text-transform: uppercase; letter-spacing: 0.04em; margin-bottom: 8px;">Urutkan</div>
                                <div style="display: flex; flex-direction: column; gap: 8px; margin-bottom: 16px;">
                                    <label style="cursor: pointer; display: flex; align-items: center; gap: 8px; font-size: 13px; color: var(--color-text); margin: 0;">
                                        <input type="radio" name="sortFilter" value="nama_asc" class="sort-filter" {{ request('sort') === 'nama_asc' ? 'checked' : '' }} style="accent-color: var(--color-primary); cursor: pointer;"> Nama A-Z
                                    </label>
                                    <label style="cursor: pointer; display: flex; align-items: center; gap: 8px; font-size: 13px; color: var(--color-text); margin: 0;">
                                        <input type="radio" name="sortFilter" value="nama_desc" class="sort-filter" {{ request('sort') === 'nama_desc' ? 'checked' : '' }} style="accent-color: var(--color-primary); cursor: pointer;"> Nama Z-A
                                    </label>
                                    <label style="cursor: pointer; display: flex; align-items: center; gap: 8px; font-size: 13px; color: var(--color-text); margin: 0;">
                                        <input type="radio" name="sortFilter" value="terbaru" class="sort-filter" {{ (!request('sort') || request('sort') === 'terbaru') ? 'checked' : '' }} style="accent-color: var(--color-primary); cursor: pointer;"> Terbaru
                                    </label>
                                    <label style="cursor: pointer; display: flex; align-items: center; gap: 8px; font-size: 13px; color: var(--color-text); margin: 0;">
                                        <input type="radio" name="sortFilter" value="terlama" class="sort-filter" {{ request('sort') === 'terlama' ? 'checked' : '' }} style="accent-color: var(--color-primary); cursor: pointer;"> Terlama
                                    </label>
                                </div>
                                <div style="padding-top: 12px; border-top: 1px dashed var(--color-border);">
                                    <button onclick="applyFilters(); event.stopPropagation();" class="btn-sima-primary" style="width: 100%; justify-content: center; font-size: 13px;">
                                        Terapkan Filter
                                    </button>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                @if(auth()->user()->isSuperAdmin())
                <div id="bulk-bar" class="bulk-action-bar-sima">
                    <span class="bulk-count"><span id="bulk-count">0</span> data terpilih</span>
                    <div class="bulk-divider"></div>
                    <button type="button" class="btn-sima-danger" onclick="bulkDelete()" style="padding: 5px 12px; font-size: 12.5px;">
                        <i data-feather="trash-2" style="width: 13px; height: 13px;"></i>
                        <span>Hapus Terpilih</span>
                    </button>
                    <button type="button" class="btn-sima-icon" onclick="clearSelection()" style="width: 28px; height: 28px;" title="Batalkan Pilihan">
                        <i data-feather="x" style="width: 13px; height: 13px;"></i>
                    </button>
                </div>
                @endif

                <!-- 4. TABLE VIEWPORT -->
                <div class="table-sima-wrapper table-loading-wrap" style="border: none; border-radius: 0;">
                    <table class="table-sima">
                        <thead>
                            <tr>
                                @if(auth()->user()->isSuperAdmin())
                                <th style="width: 44px; text-align: center; padding-left: 16px;">
                                    <input type="checkbox" id="select-all" class="sima-checkbox">
                                </th>
                                @endif
                                <th style="width: 60px; text-align: center;">No</th>
                                <th style="min-width: 240px;">Nama Bidang</th>
                                <th style="min-width: 320px;">Deskripsi</th>
                                <th style="width: 120px; text-align: center;">Aksi</th>
                            </tr>
                        </thead>
                        <tbody id="table-body">
                            @forelse($bidangs as $index => $bidang)
                            <tr>
                                @if(auth()->user()->isSuperAdmin())
                                <td style="text-align: center; padding-left: 16px;">
                                    <input type="checkbox" class="row-checkbox sima-checkbox" value="{{ $bidang->id }}">
                                </td>
                                @endif
                                <td style="text-align: center; font-variant-numeric: tabular-nums; color: var(--color-text-muted);">
                                    {{ $bidangs->firstItem() + $index }}
                                </td>
                                <td>
                                    <div style="font-weight: 600; color: var(--color-text); font-size: 13.5px;">
                                        {{ $bidang->nama }}
                                    </div>
                                </td>
                                <td>
                                    @if($bidang->deskripsi)
                                    <span style="font-size: 13px; color: var(--color-text-muted); line-height: 1.5;">
                                        {{ Str::limit($bidang->deskripsi, 80) }}
                                    </span>
                                    @else
                                    <span style="font-size: 13px; color: var(--color-border-dark); font-style: italic;">-</span>
                                    @endif
                                </td>
                                <td>
                                    <div style="display: flex; align-items: center; justify-content: center; gap: 4px;">
                                        <a href="{{ route('bidangs.show', $bidang->id) }}" class="btn-sima-icon view" title="Lihat Detail">
                                            <i data-feather="eye"></i>
                                        </a>
                                        @if(auth()->user()->isSuperAdmin())
                                        <a href="{{ route('bidangs.edit', $bidang->id) }}" class="btn-sima-icon edit" title="Edit Bidang">
                                            <i data-feather="edit-2"></i>
                                        </a>
                                        <button type="button" class="btn-sima-icon delete" title="Hapus Bidang" onclick="confirmDelete({{ $bidang->id }}, '{{ addslashes($bidang->nama) }}', {{ $bidang->users_count ?? 0 }})">
                                            <i data-feather="trash-2"></i>
                                        </button>
                                        @endif
                                    </div>
                                </td>
                            </tr>
                            @empty
                            <tr>
                                <td colspan="{{ auth()->user()->isSuperAdmin() ? '5' : '4' }}" style="text-align: center; padding: 48px 20px; color: var(--color-text-muted);">
                                    <div style="display: flex; flex-direction: column; align-items: center; justify-content: center; gap: 8px;">
                                        <i data-feather="inbox" style="width: 42px; height: 42px; stroke-width: 1.5; color: var(--color-border-dark);"></i>
                                        <p style="margin: 0; font-size: 13.5px; font-weight: 500;">Tidak ada data bidang yang sesuai</p>
                                    </div>
                                </td>
                            </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>

                <!-- 5. PAGINATION BAR -->
                <div class="pagination-bar" style="padding: 32px 20px; border-top: 1px solid var(--color-border); border-bottom-left-radius: var(--radius-lg); border-bottom-right-radius: var(--radius-lg); background: var(--color-surface); flex-wrap: wrap; gap: 14px;">
                    <div class="pagination-left">
                        <div class="page-size-wrap">
                            <span>Tampilkan</span>
                            <select id="perPageSelect" class="per-page-select" onchange="changePerPage(this.value)">
                                <option value="10" {{ ($bidangs->perPage() == 10) ? 'selected' : '' }}>10</option>
                                <option value="20" {{ ($bidangs->perPage() == 20) ? 'selected' : '' }}>20</option>
                                <option value="50" {{ ($bidangs->perPage() == 50) ? 'selected' : '' }}>50</option>
                                <option value="100" {{ ($bidangs->perPage() == 100) ? 'selected' : '' }}>100</option>
                            </select>
                            <span>data per halaman</span>
                        </div>
                    </div>

                    <div class="pagination-center">
                        <nav class="pagination-nav" id="paginationNav">
                            @if ($bidangs->onFirstPage())
                                <span class="page-btn disabled" title="Sebelumnya"><i data-feather="chevron-left"></i></span>
                            @else
                                <button type="button" class="page-btn" onclick="goToPage({{ $bidangs->currentPage() - 1 }})" title="Sebelumnya"><i data-feather="chevron-left"></i></button>
                            @endif

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
                            @endphp
                            @for ($p = $windowStart; $p <= $windowEnd; $p++)
                                @if ($p == $currentPage)
                                    <span class="page-btn active">{{ $p }}</span>
                                @else
                                    <button type="button" class="page-btn" onclick="goToPage({{ $p }})">{{ $p }}</button>
                                @endif
                            @endfor

                            @if ($bidangs->hasMorePages())
                                <button type="button" class="page-btn" onclick="goToPage({{ $bidangs->currentPage() + 1 }})" title="Selanjutnya"><i data-feather="chevron-right"></i></button>
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

    <!-- MODAL IMPORT EXCEL BIDANG (SIMA PREMIER THEME) -->
    <div id="import-modal" class="modal-sima" style="display:none; position:fixed; inset:0; background:rgba(31,58,42,0.45); z-index:9999; align-items:center; justify-content:center; backdrop-filter: blur(4px); padding: 16px;">
        <div class="modal-sima-box" style="width: 90%; max-width: 480px; display: flex; flex-direction: column; overflow: hidden;">
            <!-- Header -->
            <div class="modal-sima-header" style="position: relative; border-bottom: none !important; padding: 20px 24px 16px 24px; display: flex; align-items: center; justify-content: center;">
                <div style="font-size: 16px; font-weight: 700; color: var(--color-text); text-align: center; width: 100%;">Import Data Bidang</div>
            </div>

            <!-- Body -->
            <div class="modal-sima-body" style="padding: 20px 24px;">
                <!-- Unduh Template Bar -->
                <div id="import-template-bar" style="display: flex; flex-direction: column; align-items: left; justify-content: left; text-align: left; gap: 10px; padding-bottom: 16px; border-bottom: 1px dashed var(--color-border); margin-bottom: 18px;">
                    <div style="text-align: left;">
                        <div style="font-size: 13.5px; font-weight: 600; color: var(--color-text);">Template Format CSV</div>
                        <div style="font-size: 12px; color: var(--color-text-muted); margin-top: 2px;">Sudah siap dengan baris contoh dan daftar referensi.</div>
                    </div>
                    <a href="{{ route('bidangs.template.download') }}" onclick="if(window.showToast) window.showToast('Template import bidang (CSV) berhasil diunduh! Berkas sedang diproses...', 'success');" class="btn-sima-primary" style="align-self:flex-start; border-radius: var(--radius-full); padding: 7px 18px; font-size: 12.5px; text-decoration: none; flex-shrink: 0; display: inline-flex; align-items: center; gap: 6px;">
                        <i data-feather="download" style="width: 13px; height: 13px;"></i>
                        <span>Unduh Template</span>
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
                                    <div style="font-size: 13.5px; font-weight: 600; color: var(--color-text); text-align: center;">Pilih berkas atau seret ke area ini</div>
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
                            Bidang dengan <strong>Nama Bidang</strong> yang sudah terdaftar di sistem akan otomatis dilewati (skip) agar data yang sudah ada tidak tertimpa.
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
                                    <span style="font-size: 11.5px; color: var(--color-text-muted); font-weight: 500;">bidang</span>
                                </div>
                                <div style="font-size: 11px; color: var(--color-text-muted); margin-top: 2px;">Data baru masuk</div>
                            </div>

                            <!-- Dilewati (Kuning) -->
                            <div style="text-align: center; padding: 0 8px; border-right: 1px dashed var(--color-border);">
                                <div style="font-size: 11px; font-weight: 600; color: var(--color-warning); text-transform: uppercase; letter-spacing: 0.5px;">Dilewati</div>
                                <div style="margin-top: 4px; display: flex; align-items: baseline; justify-content: center; gap: 4px;">
                                    <span id="res-count-skipped" style="font-size: 22px; font-weight: 700; color: var(--color-warning); font-variant-numeric: tabular-nums;">0</span>
                                    <span style="font-size: 11.5px; color: var(--color-text-muted); font-weight: 500;">bidang</span>
                                </div>
                                <div style="font-size: 11px; color: var(--color-text-muted); margin-top: 2px;">Nama sudah ada</div>
                            </div>

                            <!-- Gagal (Merah) -->
                            <div style="text-align: center; padding: 0 8px;">
                                <div style="font-size: 11px; font-weight: 600; color: var(--color-danger); text-transform: uppercase; letter-spacing: 0.5px;">Gagal</div>
                                <div style="margin-top: 4px; display: flex; align-items: baseline; justify-content: center; gap: 4px;">
                                    <span id="res-count-failed" style="font-size: 22px; font-weight: 700; color: var(--color-danger); font-variant-numeric: tabular-nums;">0</span>
                                    <span style="font-size: 11.5px; color: var(--color-text-muted); font-weight: 500;">bidang</span>
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
                                            <th style="padding: 8px 12px; text-align: left; font-size: 11px; font-weight: 600; color: var(--color-text-muted); text-transform: uppercase; letter-spacing: 0.5px;">Identitas Bidang</th>
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

    @include('layouts.footer-block')
    @include('layouts.footer-js')

    <script>
        function toggleClearBtn(input) {
            const clearBtn = document.getElementById('clear-search-btn');
            if (clearBtn) clearBtn.style.display = input.value ? 'block' : 'none';
        }

        function onSearchInput(input) {
            toggleClearBtn(input);
            clearTimeout(window._searchTimer);
            window._searchTimer = setTimeout(function () { fetchBidangs(false, 1); }, 400);
        }

        function clearSearch() {
            const input = document.getElementById('searchInput');
            if (input) {
                input.value = '';
                toggleClearBtn(input);
            }
            fetchBidangs(false, 1);
        }

        function changePerPage(val) {
            fetchBidangs(false, 1);
        }

        function goToPage(page) {
            fetchBidangs(false, page);
        }

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
        let appliedSort   = 'terbaru';

        function fetchBidangs(isAutoRefresh = false, pageOverride = null) {
            const q            = (document.getElementById('searchInput')?.value || '').trim();
            const perPageEl    = document.getElementById('perPageSelect');
            const perPageValue = perPageEl ? perPageEl.value : '10';

            const params       = new URLSearchParams();

            if (q) params.set('search', q);
            if (appliedStatus && appliedStatus !== 'all') params.set('status', appliedStatus);
            if (appliedSort && appliedSort !== 'terbaru') params.set('sort', appliedSort);
            if (perPageValue && perPageValue !== '10') params.set('per_page', perPageValue);

            const urlParams  = new URLSearchParams(window.location.search);
            const currentPage = pageOverride !== null ? pageOverride : (urlParams.get('page') || 1);
            if (currentPage > 1) params.set('page', currentPage);

            const filterQuery = params.toString();
            try {
                if (filterQuery) {
                    sessionStorage.setItem('bidang_filter_state', filterQuery);
                } else {
                    sessionStorage.removeItem('bidang_filter_state');
                }
            } catch(e) {}

            history.replaceState(null, '', window.location.pathname + (filterQuery ? '?' + filterQuery : ''));

            const wrap = document.querySelector('.table-loading-wrap');
            if (!isAutoRefresh && wrap) wrap.classList.add('is-loading');

            fetch('{{ route('bidangs.index') }}?' + filterQuery, {
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

        function applyFilters() {
            const filterMenu = document.getElementById('filter-dropdown-menu');
            if (filterMenu) filterMenu.style.display = 'none';

            appliedStatus = document.querySelector('.status-filter:checked')?.value || 'all';
            appliedSort   = document.querySelector('.sort-filter:checked')?.value || 'terbaru';
            fetchBidangs(false, 1);
            document.querySelectorAll('.dropdown-menu.show').forEach(el => el.classList.remove('show'));
            document.querySelectorAll('.dropdown.show').forEach(el => el.classList.remove('show'));
        }

        function resetFilters() {
            const searchInput = document.getElementById('searchInput');
            if (searchInput) {
                searchInput.value = '';
                toggleClearBtn(searchInput);
            }

            const defaultStatus = document.querySelector('.status-filter[value="all"]');
            if (defaultStatus) defaultStatus.checked = true;

            const defaultSort = document.querySelector('.sort-filter[value="terbaru"]');
            if (defaultSort) defaultSort.checked = true;

            appliedStatus = 'all';
            appliedSort   = 'terbaru';

            try {
                sessionStorage.removeItem('bidang_filter_state');
            } catch(e) {}

            fetchBidangs(false, 1);
            document.querySelectorAll('.dropdown-menu.show').forEach(el => el.classList.remove('show'));
            document.querySelectorAll('.dropdown.show').forEach(el => el.classList.remove('show'));
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
                sessionStorage.removeItem('bidang_filter_state');
            } catch(e) {}
            @endif

            let isReturning = false;
            try {
                if (sessionStorage.getItem('bidang_returning') === '1') {
                    isReturning = true;
                    sessionStorage.removeItem('bidang_returning');
                }
            } catch(e) {}

            const ref = document.referrer || '';
            if (ref.includes('/bidangs/') || ref.includes('/bidangs/create') || ref.includes('/bidangs/edit')) {
                isReturning = true;
            }
            if (navType === 'back_forward') {
                isReturning = true;
            }

            if (navType === 'reload' && !isReturning) {
                try {
                    sessionStorage.removeItem('bidang_filter_state');
                } catch(e) {}
                if (window.location.search) {
                    history.replaceState(null, '', window.location.pathname);
                }
                const searchInput = document.getElementById('searchInput');
                if (searchInput) {
                    searchInput.value = '';
                    toggleClearBtn(searchInput);
                }
                const defaultStatus = document.querySelector('.status-filter[value="all"]');
                if (defaultStatus) defaultStatus.checked = true;
                const defaultSort = document.querySelector('.sort-filter[value="terbaru"]');
                if (defaultSort) defaultSort.checked = true;
                appliedStatus = 'all';
                appliedSort   = 'terbaru';

                fetchBidangs(true, 1);
                return;
            }

            if (!isReturning && navType === 'navigate' && !window.location.search) {
                try {
                    sessionStorage.removeItem('bidang_filter_state');
                } catch(e) {}
                return;
            }

            let currentSearch = window.location.search;
            let hasUrlQuery = (currentSearch && currentSearch.length > 1);

            if (!hasUrlQuery) {
                try {
                    const savedState = sessionStorage.getItem('bidang_filter_state');
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
                toggleClearBtn(searchInput);
            }

            appliedStatus = params.get('status') || 'all';
            const stRadio = document.querySelector(`.status-filter[value="${appliedStatus}"]`);
            if (stRadio) stRadio.checked = true;

            appliedSort = params.get('sort') || 'terbaru';
            const srRadio = document.querySelector(`.sort-filter[value="${appliedSort}"]`);
            if (srRadio) srRadio.checked = true;

            const perPage = params.get('per_page') || '10';
            const perPageEl = document.getElementById('perPageSelect');
            if (perPageEl) perPageEl.value = perPage;

            const page = parseInt(params.get('page') || '1', 10);
            fetchBidangs(true, page);
        }

        function renderKpiTrend(trendData) {
            if (!trendData) return '<span class="stat-delta-pill is-neutral">0% (0)</span>';
            const percent = trendData.percent ?? 0;
            const diff = trendData.diff ?? 0;
            const formattedDiff = Number(Math.abs(diff)).toLocaleString('id-ID');

            if (percent > 0 || diff > 0) {
                return `<span class="stat-delta-pill is-up">
                    <i data-feather="trending-up" style="width: 11px; height: 11px;"></i>
                    +${percent}% (+${formattedDiff})
                </span>`;
            } else if (percent < 0 || diff < 0) {
                return `<span class="stat-delta-pill is-down">
                    <i data-feather="trending-down" style="width: 11px; height: 11px;"></i>
                    ${percent}% (${diff < 0 ? '-' : ''}${formattedDiff})
                </span>`;
            } else {
                return '<span class="stat-delta-pill is-neutral">0% (0)</span>';
            }
        }

        function updateCardTrend(cardEl, trendData) {
            if (!cardEl) return;
            const wrapper = cardEl.querySelector('.kpi-trend-wrapper');
            if (!wrapper) return;
            const label = wrapper.querySelector('.stat-delta-label');
            const labelHtml = label ? label.outerHTML : '<span class="stat-delta-label">vs. bulan lalu</span>';
            wrapper.innerHTML = renderKpiTrend(trendData) + labelHtml;
        }

        function updateStats(stats) {
            if (!stats) return;
            const cards = document.querySelectorAll('.stat-card-sima');
            if (cards[0]) {
                const valEl = cards[0].querySelector('.stat-number');
                if (valEl) valEl.textContent = (stats.totalBidangs ?? 0).toLocaleString('id-ID');
                if (stats.trends && stats.trends.bidang) updateCardTrend(cards[0], stats.trends.bidang);
            }
            if (cards[1]) {
                const valEl = cards[1].querySelector('.stat-number');
                if (valEl) valEl.textContent = (stats.totalUsers ?? 0).toLocaleString('id-ID');
                if (stats.trends && stats.trends.user) updateCardTrend(cards[1], stats.trends.user);
            }
            if (cards[2]) {
                const valEl = cards[2].querySelector('.stat-number');
                if (valEl) valEl.textContent = (stats.bidangWithUsers ?? 0).toLocaleString('id-ID');
                if (stats.trends && stats.trends.assigned) updateCardTrend(cards[2], stats.trends.assigned);
            }
            if (cards[3]) {
                const valEl = cards[3].querySelector('.stat-number');
                if (valEl && stats.topBidangNama !== undefined) {
                    valEl.textContent = stats.topBidangNama || '-';
                    valEl.title = stats.topBidangNama || '-';
                }
                const pill = cards[3].querySelector('.stat-delta-pill');
                if (pill && stats.topBidangCount !== undefined) {
                    pill.textContent = (stats.topBidangCount || 0).toLocaleString('id-ID') + ' user';
                }
            }
        }

        function updateTable(bidangs, pagination) {
            if (typeof clearSelection === 'function') clearSelection();
            const tbody    = document.querySelector('#table-body');
            if (!tbody) return;
            const firstItem = pagination?.firstItem ?? 1;
            const isSuperAdmin = {{ auth()->user()->isSuperAdmin() ? 'true' : 'false' }};
            const colspan   = isSuperAdmin ? 5 : 4;

            if (!bidangs || bidangs.length === 0) {
                tbody.innerHTML = `<tr><td colspan="${colspan}" style="text-align:center;padding:48px 20px;color:var(--color-text-muted);">
                    <div style="display:flex;flex-direction:column;align-items:center;justify-content:center;gap:8px;">
                        <i data-feather="inbox" style="width:42px;height:42px;stroke-width:1.5;color:var(--color-border-dark);"></i>
                        <p style="margin:0;font-size:13.5px;font-weight:500;">Tidak ada data bidang yang sesuai</p>
                    </div></td></tr>`;
                return;
            }

            let html = '';
            bidangs.forEach((bidang, idx) => {
                const no   = firstItem + idx;
                const desc = bidang.deskripsi
                    ? `<span style="font-size:13px;color:var(--color-text-muted);line-height:1.5;">${bidang.deskripsi.substring(0, 80)}${bidang.deskripsi.length > 80 ? '…' : ''}</span>`
                    : `<span style="font-size:13px;color:var(--color-border-dark);font-style:italic;">-</span>`;

                const chkHtml = isSuperAdmin
                    ? `<td style="text-align: center; padding-left: 16px;"><input type="checkbox" class="row-checkbox sima-checkbox" value="${bidang.id}"></td>`
                    : '';

                const actionsAdmin = isSuperAdmin
                    ? `<a href="{{ url('bidangs') }}/${bidang.id}/edit" class="btn-sima-icon edit" title="Edit Bidang"><i data-feather="edit-2"></i></a>
                       <button type="button" class="btn-sima-icon delete" title="Hapus Bidang" onclick="confirmDelete(${bidang.id}, '${(bidang.nama||'').replace(/'/g,"\\'")}', ${bidang.users_count ?? 0})"><i data-feather="trash-2"></i></button>`
                    : '';

                html += `<tr>
                    ${chkHtml}
                    <td style="text-align:center; font-variant-numeric: tabular-nums; color: var(--color-text-muted);">${no}</td>
                    <td><div style="font-weight:600; color:var(--color-text); font-size:13.5px;">${bidang.nama}</div></td>
                    <td>${desc}</td>
                    <td><div style="display:flex; align-items:center; justify-content:center; gap:4px;">
                        <a href="{{ url('bidangs') }}/${bidang.id}" class="btn-sima-icon view" title="Lihat Detail"><i data-feather="eye"></i></a>
                        ${actionsAdmin}
                    </div></td>
                </tr>`;
            });
            tbody.innerHTML = html;

            if (isSuperAdmin) attachBulkListeners();
        }

        function updatePaginationUI(pagination) {
            if (!pagination) return;
            const firstItem   = pagination.firstItem   || 0;
            const lastItem    = pagination.lastItem    || 0;
            const total       = pagination.total       || 0;
            const currentPage = pagination.currentPage || 1;
            const lastPage    = pagination.lastPage    || 1;

            const rangeEl = document.getElementById('paginationRangeInfo');
            if (rangeEl) {
                rangeEl.innerHTML = `Menampilkan <strong>${firstItem}-${lastItem}</strong> dari <strong>${total}</strong> data`;
            }

            const jumpInput = document.getElementById('pageJumpInput');
            if (jumpInput) {
                jumpInput.max         = lastPage;
                jumpInput.placeholder = `1–${lastPage}`;
                jumpInput.title       = `Masukkan nomor halaman (1–${lastPage})`;
                jumpInput.value       = currentPage;
            }

            const nav = document.getElementById('paginationNav');
            if (!nav) return;
            let html = '';

            html += currentPage <= 1
                ? `<span class="page-btn disabled" title="Sebelumnya"><i data-feather="chevron-left"></i></span>`
                : `<button type="button" class="page-btn" onclick="goToPage(${currentPage - 1})" title="Sebelumnya"><i data-feather="chevron-left"></i></button>`;

            let ws = Math.max(1, currentPage - 2);
            let we = Math.min(lastPage, currentPage + 2);
            if ((we - ws + 1) < 5) { if (ws === 1) { we = Math.min(lastPage, 5); } else { ws = Math.max(1, we - 4); } }
            for (let p = ws; p <= we; p++) {
                html += p === currentPage
                    ? `<span class="page-btn active">${p}</span>`
                    : `<button type="button" class="page-btn" onclick="goToPage(${p})">${p}</button>`;
            }

            html += currentPage >= lastPage
                ? `<span class="page-btn disabled" title="Selanjutnya"><i data-feather="chevron-right"></i></span>`
                : `<button type="button" class="page-btn" onclick="goToPage(${currentPage + 1})" title="Selanjutnya"><i data-feather="chevron-right"></i></button>`;

            nav.innerHTML = html;
        }

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

        @if(auth()->user()->isSuperAdmin())
        function attachBulkListeners() {
            const selectAll   = document.getElementById('select-all');
            const checkboxes  = document.querySelectorAll('.row-checkbox');

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
            if (bulkBar) {
                if (checked > 0) {
                    bulkBar.classList.add('show');
                } else {
                    bulkBar.classList.remove('show');
                }
            }
            if (bulkCount) bulkCount.textContent    = checked;
            if (typeof feather !== 'undefined') feather.replace();
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

        document.addEventListener('DOMContentLoaded', function () {
            restoreFilterState();

            @if(auth()->user()->isSuperAdmin())
            attachBulkListeners();
            @endif

            if (typeof feather !== 'undefined') feather.replace();

            const autoRefreshMs = 15000;
            setInterval(function () {
                if (document.hidden) return;
                if (document.querySelectorAll('.row-checkbox:checked, #select-all:checked').length > 0) return;
                if (document.querySelector('.modal.show, [id*="modal"][style*="display: flex"], [id*="modal"][style*="display: block"]')) return;
                fetchBidangs(true);
            }, autoRefreshMs);
        });

        // ==========================================
        // FITUR IMPORT EXCEL MASTER BIDANG
        // ==========================================
        let importStageTimer = null;
        let importStatsAccumulator = { imported: 0, skipped: 0, failed: 0 };
        const importStages = [
            'Membaca dan memvalidasi baris data...',
            'Memeriksa keunikan nama bidang...',
            'Menyimpan master bidang desa...'
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
            const ext = file.name.split('.').pop().toLowerCase();
            if (ext !== 'csv') {
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
            e.stopPropagation();
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

            fetch('{{ route("bidangs.import") }}', {
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
                            ? `${importedCount} bidang baru berhasil disimpan, dan ${skippedCount} bidang yang sudah ada dilewati.`
                            : `${importedCount} bidang baru berhasil disimpan ke dalam sistem.`;
                    } else if (importedCount === 0 && skippedCount > 0 && failedCount === 0) {
                        titleEl.textContent = 'Data Sudah Terdaftar (Dilewati Otomatis)';
                        descEl.textContent = `Seluruh ${skippedCount} bidang pada berkas CSV sudah terdaftar di sistem. Baris dilewati agar data eksisting tidak tertimpa.`;
                    } else if (failedCount > 0) {
                        titleEl.textContent = 'Impor Selesai dengan Catatan';
                        descEl.textContent = `${importedCount} bidang baru masuk, ${skippedCount} dilewati, dan ${failedCount} baris memerlukan koreksi format.`;
                    } else {
                        titleEl.textContent = 'Pemeriksaan Berkas Selesai';
                        descEl.textContent = 'Tidak ada bidang baru yang ditambahkan ke sistem.';
                    }

                    const tbody = document.getElementById('res-details-body');
                    tbody.innerHTML = '';
                    const issues = [];

                    (data.failed_rows || []).forEach(r => {
                        issues.push({
                            row: r.row,
                            status: '<span class="status-pill status-pill-danger" style="font-size: 11px; padding: 2px 8px; font-weight: 600;">Gagal</span>',
                            nama: r.nama || '-',
                            reason: r.reason || 'Format atau kelengkapan data tidak valid'
                        });
                    });

                    (data.skipped_rows || []).forEach(r => {
                        issues.push({
                            row: r.row,
                            status: '<span class="status-pill status-pill-warning" style="font-size: 11px; padding: 2px 8px; font-weight: 600;">Dilewati</span>',
                            nama: r.nama || '-',
                            reason: r.reason || 'Nama bidang sudah terdaftar'
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
                                <td style="padding: 10px 12px; font-weight: 600; color: var(--color-text); font-size: 12.5px;">${it.nama}</td>
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
                    showImportError(data.message || 'Gagal memproses file CSV.');
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
                        msg = 'Import data bidang berhasil! Seluruh data (' + imported + ' data) berhasil disimpan.';
                    } else if (imported > 0) {
                        msg = 'Import data bidang selesai: ' + imported + ' data baru disimpan (' + skipped + ' dilewati, ' + failed + ' gagal).';
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
                        dropzone.style.borderColor = 'var(--color-primary)';
                        dropzone.style.backgroundColor = 'var(--color-row-hover)';
                    }, false);
                });

                ['dragleave', 'drop'].forEach(eventName => {
                    dropzone.addEventListener(eventName, (e) => {
                        e.preventDefault();
                        e.stopPropagation();
                        dropzone.style.borderColor = 'var(--color-border)';
                        dropzone.style.backgroundColor = 'var(--color-surface)';
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

        window.addEventListener('popstate', function() {
            restoreFilterState();
        });

        window.addEventListener('pageshow', function(event) {
            if (event.persisted) {
                restoreFilterState();
            }
        });
    </script>
</body>
</html>
