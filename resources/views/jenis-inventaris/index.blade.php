<!DOCTYPE html>
<html lang="id">
<head>
    @include('layouts.head-page-meta', ['title' => 'Jenis Inventaris - Sistem Manajemen Aset Desa'])
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
    @php
        $canManageJenisInventaris = auth()->user()->isSuperAdmin();
    @endphp

    @include('layouts.notification')
    @include('layouts.modals')
    @include('layouts.layout-vertical')

    <div class="pc-container">
        <div class="pc-content" style="max-width: 1560px; margin: 0 auto;">

            <!-- PAGE HEADER & PRIMARY ACTIONS -->
            <div style="display: flex; justify-content: space-between; align-items: flex-start; margin-bottom: 24px; flex-wrap: wrap; gap: 16px;">
                <div>
                    <h1 style="font-size: 22px; font-weight: 700; margin: 0; color: var(--color-text); line-height: 1.2; letter-spacing: -0.02em;">
                        Jenis Inventaris
                    </h1>
                </div>
                <div style="display: flex; gap: 10px; align-items: center; flex-wrap: wrap;">
                    @if($canManageJenisInventaris)
                    <a href="{{ route('jenis-inventaris.create') }}" class="btn-sima-primary">
                        <i data-feather="plus" style="width: 14px; height: 14px;"></i>
                        <span>Tambah Jenis Inventaris</span>
                    </a>
                    @endif
                </div>
            </div>

            <!-- 1. STAT CARDS (Prodex 4-Card Summary Bar) -->
            <div class="stat-card-row">
                <!-- Card 1: Total Jenis -->
                <div class="stat-card-sima" id="card-total-jenis">
                    <div class="stat-header">
                        <span class="stat-label">TOTAL JENIS</span>
                    </div>
                    <div class="stat-number">{{ number_format($stats['totalJenis'] ?? 0, 0, ',', '.') }}</div>
                    <div class="kpi-trend-wrapper stat-delta">
                        @php 
                            $tJenis = $stats['trends']['jenis']['percent'] ?? 0;
                            $dJenis = $stats['trends']['jenis']['diff'] ?? 0;
                        @endphp
                        @if($tJenis > 0 || $dJenis > 0)
                            <span class="stat-delta-pill is-up">
                                <i data-feather="trending-up" style="width: 11px; height: 11px;"></i>
                                +{{ $tJenis }}% (+{{ number_format($dJenis, 0, ',', '.') }})
                            </span>
                        @elseif($tJenis < 0 || $dJenis < 0)
                            <span class="stat-delta-pill is-down">
                                <i data-feather="trending-down" style="width: 11px; height: 11px;"></i>
                                {{ $tJenis }}% ({{ number_format($dJenis, 0, ',', '.') }})
                            </span>
                        @else
                            <span class="stat-delta-pill is-neutral">0% (0)</span>
                        @endif
                        <span class="stat-delta-label">vs. bulan lalu</span>
                    </div>
                </div>

                <!-- Card 2: Total Aset -->
                <div class="stat-card-sima" id="card-total-aset">
                    <div class="stat-header">
                        <span class="stat-label">TOTAL ASET</span>
                    </div>
                    <div class="stat-number">{{ number_format($stats['totalAssets'] ?? 0, 0, ',', '.') }}</div>
                    <div class="kpi-trend-wrapper stat-delta">
                        @php 
                            $tAssets = $stats['trends']['assets']['percent'] ?? 0;
                            $dAssets = $stats['trends']['assets']['diff'] ?? 0;
                        @endphp
                        @if($tAssets > 0 || $dAssets > 0)
                            <span class="stat-delta-pill is-up">
                                <i data-feather="trending-up" style="width: 11px; height: 11px;"></i>
                                +{{ $tAssets }}% (+{{ number_format($dAssets, 0, ',', '.') }})
                            </span>
                        @elseif($tAssets < 0 || $dAssets < 0)
                            <span class="stat-delta-pill is-down">
                                <i data-feather="trending-down" style="width: 11px; height: 11px;"></i>
                                {{ $tAssets }}% ({{ number_format($dAssets, 0, ',', '.') }})
                            </span>
                        @else
                            <span class="stat-delta-pill is-neutral">0% (0)</span>
                        @endif
                        <span class="stat-delta-label">vs. bulan lalu</span>
                    </div>
                </div>

                <!-- Card 3: Jenis Terbanyak -->
                <div class="stat-card-sima" id="card-top-jenis">
                    <div class="stat-header">
                        <span class="stat-label">JENIS ASET TERBANYAK</span>
                    </div>
                    <div class="stat-number" style="font-size: 18px; line-height: 1.3; white-space: nowrap; overflow: hidden; text-overflow: ellipsis;" title="{{ $stats['topJenisNama'] ?? '-' }}">{{ $stats['topJenisNama'] ?? '-' }}</div>
                    <div class="kpi-trend-wrapper stat-delta">
                        <span class="stat-delta-pill is-up">
                            {{ number_format($stats['topJenisCount'] ?? 0, 0, ',', '.') }} aset
                        </span>
                        <span class="stat-delta-label">dominan</span>
                    </div>
                </div>

                <!-- Card 4: Jenis Tersedikit -->
                <div class="stat-card-sima" id="card-lowest-jenis">
                    <div class="stat-header">
                        <span class="stat-label">JENIS ASET TERSEDIKIT</span>
                    </div>
                    <div class="stat-number" style="font-size: 18px; line-height: 1.3; white-space: nowrap; overflow: hidden; text-overflow: ellipsis;" title="{{ $stats['lowestJenisNama'] ?? '-' }}">{{ $stats['lowestJenisNama'] ?? '-' }}</div>
                    <div class="kpi-trend-wrapper stat-delta">
                        <span class="stat-delta-pill is-neutral">
                            {{ number_format($stats['lowestJenisCount'] ?? 0, 0, ',', '.') }} aset
                        </span>
                        <span class="stat-delta-label">kategori minim</span>
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
                            placeholder="Cari jenis inventaris..."
                            value="{{ request('search') }}"
                            oninput="onSearchInput(this)"
                            onkeypress="if(event.key==='Enter'){doSearch();}"
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
                                <div style="display: flex; flex-direction: column; gap: 8px; margin-bottom: 12px;">
                                    <label style="cursor: pointer; display: flex; align-items: center; gap: 8px; font-size: 13px; color: var(--color-text); margin: 0;">
                                        <input type="radio" name="statusFilter" value="all" class="status-filter" {{ (!request('status') || request('status') === 'all') ? 'checked' : '' }} style="accent-color: var(--color-primary); cursor: pointer;"> 
                                        <span>Semua Status</span>
                                    </label>
                                    <label style="cursor: pointer; display: flex; align-items: center; gap: 8px; font-size: 13px; color: var(--color-text); margin: 0;">
                                        <input type="radio" name="statusFilter" value="dengan_aset" class="status-filter" {{ request('status') === 'dengan_aset' ? 'checked' : '' }} style="accent-color: var(--color-primary); cursor: pointer;"> 
                                        <span>Memiliki Aset</span>
                                    </label>
                                    <label style="cursor: pointer; display: flex; align-items: center; gap: 8px; font-size: 13px; color: var(--color-text); margin: 0;">
                                        <input type="radio" name="statusFilter" value="tanpa_aset" class="status-filter" {{ request('status') === 'tanpa_aset' ? 'checked' : '' }} style="accent-color: var(--color-primary); cursor: pointer;"> 
                                        <span>Tanpa Aset (Kosong)</span>
                                    </label>
                                </div>
                                <hr style="border: none; border-top: 1px dashed var(--color-border); margin: 12px 0;" />
                                <div style="color: var(--color-primary); font-size: 12px; font-weight: 700; text-transform: uppercase; letter-spacing: 0.04em; margin-bottom: 8px;">Urutkan</div>
                                <div style="display: flex; flex-direction: column; gap: 8px; margin-bottom: 16px;">
                                    <label style="cursor: pointer; display: flex; align-items: center; gap: 8px; font-size: 13px; color: var(--color-text); margin: 0;">
                                        <input type="radio" name="sortFilter" value="nama_asc" class="sort-filter" {{ request('sort') === 'nama_asc' ? 'checked' : '' }} style="accent-color: var(--color-primary); cursor: pointer;"> 
                                        <span>Nama A-Z</span>
                                    </label>
                                    <label style="cursor: pointer; display: flex; align-items: center; gap: 8px; font-size: 13px; color: var(--color-text); margin: 0;">
                                        <input type="radio" name="sortFilter" value="nama_desc" class="sort-filter" {{ request('sort') === 'nama_desc' ? 'checked' : '' }} style="accent-color: var(--color-primary); cursor: pointer;"> 
                                        <span>Nama Z-A</span>
                                    </label>
                                    <label style="cursor: pointer; display: flex; align-items: center; gap: 8px; font-size: 13px; color: var(--color-text); margin: 0;">
                                        <input type="radio" name="sortFilter" value="terbaru" class="sort-filter" {{ (!request('sort') || request('sort') === 'terbaru') ? 'checked' : '' }} style="accent-color: var(--color-primary); cursor: pointer;"> 
                                        <span>Terbaru</span>
                                    </label>
                                    <label style="cursor: pointer; display: flex; align-items: center; gap: 8px; font-size: 13px; color: var(--color-text); margin: 0;">
                                        <input type="radio" name="sortFilter" value="terlama" class="sort-filter" {{ request('sort') === 'terlama' ? 'checked' : '' }} style="accent-color: var(--color-primary); cursor: pointer;"> 
                                        <span>Terlama</span>
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

                @if($canManageJenisInventaris)
                <div id="bulk-bar" class="bulk-action-bar-sima">
                    <span class="bulk-count"><span id="bulk-count">0</span> data terpilih</span>
                    <span id="bulk-warn" style="display:none; font-size:11px; color: var(--color-warning); background: var(--color-warning-soft); border-radius: 4px; padding: 2px 6px;">&#9888; Maks 100 data</span>
                    <div class="bulk-divider"></div>
                    <button type="button" class="btn-sima-danger" onclick="bulkDeleteConfirm()" style="padding: 5px 12px; font-size: 12.5px;">
                        <i data-feather="trash-2" style="width: 13px; height: 13px;"></i>
                        <span>Hapus Terpilih</span>
                    </button>
                    <button type="button" class="btn-sima-icon" onclick="clearSelection()" style="width: 28px; height: 28px;" title="Batalkan Pilihan">
                        <i data-feather="x" style="width: 13px; height: 13px;"></i>
                    </button>
                </div>
                <form id="bulk-delete-form" method="POST" action="{{ route('jenis-inventaris.bulk-destroy') }}" style="display:none;">@csrf</form>
                @endif

                <!-- 4. TABLE VIEWPORT -->
                <div class="table-sima-wrapper table-loading-wrap" style="border: none; border-radius: 0;">
                    <table class="table-sima">
                        <thead>
                            <tr>
                                <th style="width: 44px; text-align: center; padding-left: 16px;">
                                    @if($canManageJenisInventaris)
                                    <input type="checkbox" id="check-all" class="sima-checkbox" onchange="toggleAll(this)">
                                    @endif
                                </th>
                                <th style="width: 60px; text-align: center;">No</th>
                                <th style="min-width: 220px;">Jenis Inventaris</th>
                                <th style="min-width: 300px;">Deskripsi</th>
                                <th style="width: 140px; text-align: center;">Jumlah Aset</th>
                                <th style="width: 120px; text-align: center;">Aksi</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($jenisInventaris as $index => $jenis)
                            <tr>
                                <td style="text-align: center; padding-left: 16px;">
                                    @if($canManageJenisInventaris && !$jenis->isVirtual())
                                    <input type="checkbox" class="row-check sima-checkbox" value="{{ $jenis->id }}" onchange="updateBulkBar()">
                                    @endif
                                </td>
                                <td style="text-align: center; font-variant-numeric: tabular-nums; color: var(--color-text-muted);">
                                    {{ $jenisInventaris->firstItem() + $index }}
                                </td>
                                <td>
                                    <div style="font-weight: 600; color: var(--color-text); font-size: 13.5px;">
                                        {{ $jenis->nama }}
                                    </div>
                                </td>
                                <td>
                                    <span style="font-size: 13px; color: var(--color-text-muted);">
                                        {{ Str::limit($jenis->deskripsi ?? 'Belum ada deskripsi', 60) }}
                                    </span>
                                </td>
                                <td style="text-align: center;">
                                    <span style="display: inline-block; padding: 3px 9px; font-size: 12px; font-weight: 600; border-radius: var(--radius-full); background: var(--color-primary-tint); color: var(--color-primary); font-variant-numeric: tabular-nums;">
                                        {{ $jenis->isVirtual() ? ($virtualAsetCount ?? 0) : ($jenis->inventaris ? $jenis->inventaris->count() : 0) }}
                                    </span>
                                </td>
                                <td>
                                    <div style="display: flex; align-items: center; justify-content: center; gap: 4px;">
                                        <a href="{{ route('jenis-inventaris.show', $jenis->id) }}" class="btn-sima-icon view" title="Lihat Detail">
                                            <i data-feather="eye"></i>
                                        </a>
                                        @if($canManageJenisInventaris)
                                        <a href="{{ route('jenis-inventaris.edit', $jenis->id) }}" class="btn-sima-icon edit" title="Edit Format">
                                            <i data-feather="edit-2"></i>
                                        </a>
                                        @if(!$jenis->isVirtual())
                                        <button type="button" data-nama="{{ $jenis->nama }}" onclick="confirmDeleteJenis({{ $jenis->id }}, this.getAttribute('data-nama'))" class="btn-sima-icon delete" title="Hapus Format">
                                            <i data-feather="trash-2"></i>
                                        </button>
                                        @endif
                                        @endif
                                    </div>
                                </td>
                            </tr>
                            @empty
                            <tr>
                                <td colspan="6" style="text-align: center; padding: 48px 20px; color: var(--color-text-muted);">
                                    <div style="display: flex; flex-direction: column; align-items: center; justify-content: center; gap: 8px;">
                                        <i data-feather="inbox" style="width: 42px; height: 42px; stroke-width: 1.5; color: var(--color-border-dark);"></i>
                                        <p style="margin: 0; font-size: 13.5px; font-weight: 500;">Belum ada data jenis inventaris yang sesuai</p>
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
                                <span class="page-btn disabled" title="Sebelumnya"><i data-feather="chevron-left"></i></span>
                            @else
                                <button type="button" class="page-btn" onclick="goToPage({{ $jenisInventaris->currentPage() - 1 }})" title="Sebelumnya"><i data-feather="chevron-left"></i></button>
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
                                <button type="button" class="page-btn" onclick="goToPage({{ $jenisInventaris->currentPage() + 1 }})" title="Selanjutnya"><i data-feather="chevron-right"></i></button>
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
                
                setTimeout(() => closeToast(), 5000);
                
                sessionStorage.removeItem('jenisFormSuccess');
                sessionStorage.removeItem('jenisFormMessage');
                
                if(typeof feather !== 'undefined') feather.replace();
            }
        })();

        function closeToast(){
            const toast = document.getElementById('success-toast');
            if (!toast) return;
            toast.classList.remove('translate-x-0');
            toast.classList.add('translate-x-full');
            setTimeout(() => { toast.style.display = 'none'; }, 300);
        }

        function toggleSearchClear(input) {
            const clearBtn = document.getElementById('clear-search-btn');
            if (clearBtn) clearBtn.style.display = input.value ? 'block' : 'none';
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
            if (input) {
                input.value = '';
                toggleSearchClear(input);
            }
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

            fetchJenis(false, 1);
        }

        function resetFilters() {
            const searchInput = document.getElementById('searchInput');
            if (searchInput) searchInput.value = '';
            toggleSearchClear(searchInput);

            appliedStatus = 'all';
            appliedSort   = 'terbaru';

            const defaultStatus = document.querySelector('.status-filter[value="all"]');
            if (defaultStatus) defaultStatus.checked = true;

            const defaultSort = document.querySelector('.sort-filter[value="terbaru"]');
            if (defaultSort) defaultSort.checked = true;

            const filterMenu = document.getElementById('filter-dropdown-menu');
            if (filterMenu) filterMenu.style.display = 'none';

            fetchJenis(false, 1);
        }

        let appliedStatus = 'all';
        let appliedSort   = 'terbaru';

        function fetchJenis(isAutoRefresh = false, pageOverride = null) {
            const q            = (document.getElementById('searchInput')?.value || '').trim();
            const perPageSelect = document.getElementById('perPageSelect');
            const perPageValue  = perPageSelect ? perPageSelect.value : '10';

            const params       = new URLSearchParams();
            if (q) params.set('search', q);
            if (appliedStatus && appliedStatus !== 'all') params.set('status', appliedStatus);
            if (appliedSort && appliedSort !== 'terbaru') params.set('sort', appliedSort);
            if (perPageValue && perPageValue !== '10') params.set('per_page', perPageValue);

            const urlParams = new URLSearchParams(window.location.search);
            let currentPage = pageOverride !== null ? pageOverride : (urlParams.get('page') || 1);
            if (currentPage > 1) {
                params.set('page', currentPage);
            }

            const queryString = params.toString();
            if (!isAutoRefresh) {
                history.replaceState(null, '', window.location.pathname + (queryString ? '?' + queryString : ''));
                try {
                    if (queryString) {
                        sessionStorage.setItem('jenis_inventaris_filter_state', queryString);
                    } else {
                        sessionStorage.removeItem('jenis_inventaris_filter_state');
                    }
                } catch(e) {}
            }
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
            appliedSort   = document.querySelector('.sort-filter:checked')?.value || 'terbaru';
            fetchJenis(false, 1);
            document.querySelectorAll('.dropdown-menu.show').forEach(el => el.classList.remove('show'));
            document.querySelectorAll('.dropdown.show').forEach(el => el.classList.remove('show'));
        }

        function resetFilters() {
            const searchInput = document.getElementById('searchInput');
            if (searchInput) {
                searchInput.value = '';
                toggleSearchClear(searchInput);
            }

            const defaultStatus = document.querySelector('.status-filter[value="all"]');
            if (defaultStatus) defaultStatus.checked = true;

            const defaultSort = document.querySelector('.sort-filter[value="terbaru"]');
            if (defaultSort) defaultSort.checked = true;

            appliedStatus = 'all';
            appliedSort   = 'terbaru';

            fetchJenis(false, 1);
            document.querySelectorAll('.dropdown-menu.show').forEach(el => el.classList.remove('show'));
            document.querySelectorAll('.dropdown.show').forEach(el => el.classList.remove('show'));
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

        function updateStatsJenis(stats) {
            if (!stats) return;
            const cards = document.querySelectorAll('.stat-card-sima');
            if (cards[0]) {
                const valEl = cards[0].querySelector('.stat-number');
                if (valEl) valEl.textContent = (stats.totalJenis ?? 0).toLocaleString('id-ID');
                if (stats.trends && stats.trends.jenis) updateCardTrend(cards[0], stats.trends.jenis);
            }
            if (cards[1]) {
                const valEl = cards[1].querySelector('.stat-number');
                if (valEl) valEl.textContent = (stats.totalAssets ?? 0).toLocaleString('id-ID');
                if (stats.trends && stats.trends.aset) updateCardTrend(cards[1], stats.trends.aset);
            }
            if (cards[2]) {
                const valEl = cards[2].querySelector('.stat-number');
                if (valEl) {
                    valEl.textContent = stats.topJenisNama || '-';
                    valEl.title = stats.topJenisNama || '-';
                }
                const pill = cards[2].querySelector('.stat-delta-pill');
                if (pill) pill.textContent = (stats.topJenisCount ?? 0).toLocaleString('id-ID') + ' aset';
            }
            if (cards[3]) {
                const valEl = cards[3].querySelector('.stat-number');
                if (valEl) {
                    valEl.textContent = stats.lowestJenisNama || '-';
                    valEl.title = stats.lowestJenisNama || '-';
                }
                const pill = cards[3].querySelector('.stat-delta-pill');
                if (pill) pill.textContent = (stats.lowestJenisCount ?? 0).toLocaleString('id-ID') + ' aset';
            }
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
                html += `<span class="page-btn disabled" title="Selanjutnya"><i data-feather="chevron-right"></i></span>`;
            } else {
                html += `<button type="button" class="page-btn" onclick="goToPage(${currentPage + 1})" title="Selanjutnya"><i data-feather="chevron-right"></i></button>`;
            }

            nav.innerHTML = html;
        }

        function updateTableJenis(items, from, to, total, pagination) {
            clearSelection();
            const tbody = document.querySelector('.table-sima tbody');
            if (!tbody) return;
            if (!items || items.length === 0) {
                tbody.innerHTML = `<tr><td colspan="6" style="text-align:center;padding:48px 20px;color:var(--color-text-muted);">
                    <div style="display:flex;flex-direction:column;align-items:center;justify-content:center;gap:8px;">
                        <i data-feather="inbox" style="width:42px;height:42px;stroke-width:1.5;color:var(--color-border-dark);"></i>
                        <p style="margin:0;font-size:13.5px;font-weight:500;">Belum ada data jenis inventaris yang sesuai</p>
                    </div></td></tr>`;
                return;
            }
            let html = '';
            items.forEach((jenis, idx) => {
                const desc = jenis.deskripsi ? jenis.deskripsi.substring(0, 60) + (jenis.deskripsi.length > 60 ? '...' : '') : 'Belum ada deskripsi';
                const checkboxCell = (canManageJenisInventaris && !jenis.is_virtual)
                    ? `<td style="text-align: center; padding-left: 16px;"><input type="checkbox" class="row-check sima-checkbox" value="${jenis.id}" onchange="updateBulkBar()"></td>`
                    : `<td style="text-align: center; padding-left: 16px;"></td>`;
                const manageButtons = canManageJenisInventaris
                    ? `<a href="{{ url('jenis-inventaris') }}/${jenis.id}/edit" class="btn-sima-icon edit" title="Edit Format"><i data-feather="edit-2"></i></a>
                       ${jenis.is_virtual ? '' : `<button type="button" onclick="confirmDeleteJenis(${jenis.id}, this.getAttribute('data-nama'))" data-nama="${jenis.nama.replace(/&/g, '&amp;').replace(/"/g, '&quot;')}" class="btn-sima-icon delete" title="Hapus Format"><i data-feather="trash-2"></i></button>`}`
                    : '';
                const rowNumber = (parseInt(from) || 1) + idx;
                html += `<tr>
                    ${checkboxCell}
                    <td style="text-align: center; font-variant-numeric: tabular-nums; color: var(--color-text-muted);">${rowNumber}</td>
                    <td><div style="font-weight: 600; color: var(--color-text); font-size: 13.5px;">${jenis.nama}</div></td>
                    <td><span style="font-size: 13px; color: var(--color-text-muted);">${desc}</span></td>
                    <td style="text-align: center;"><span style="display: inline-block; padding: 3px 9px; font-size: 12px; font-weight: 600; border-radius: var(--radius-full); background: var(--color-primary-tint); color: var(--color-primary); font-variant-numeric: tabular-nums;">${jenis.aset_count}</span></td>
                    <td><div style="display: flex; align-items: center; justify-content: center; gap: 4px;">
                        <a href="{{ url('jenis-inventaris') }}/${jenis.id}" class="btn-sima-icon view" title="Lihat Detail"><i data-feather="eye"></i></a>
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
            const allRows = document.querySelectorAll('.row-check:not(:disabled)');
            const checked = document.querySelectorAll('.row-check:checked');
            const checkAll = document.getElementById('check-all');
            if (checkAll) {
                checkAll.checked = allRows.length > 0 && checked.length === allRows.length;
            }
            const bar = document.getElementById('bulk-bar');
            if (!bar) return;
            if (checked.length > 0) {
                bar.classList.add('show');
            } else {
                bar.classList.remove('show');
            }
            document.getElementById('bulk-count').textContent = checked.length;
            const warn = document.getElementById('bulk-warn');
            if (warn) warn.style.display = checked.length > 100 ? 'inline-block' : 'none';
            if (typeof feather !== 'undefined') feather.replace();
        }

        function clearSelection() {
            if (!canManageJenisInventaris) return;
            document.querySelectorAll('.row-check, #check-all').forEach(cb => { cb.checked = false; });
            const bar = document.getElementById('bulk-bar');
            if (bar) bar.classList.remove('show');
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

        function getNavigationType() {
            try {
                const entries = performance.getEntriesByType('navigation');
                if (entries && entries.length > 0) {
                    return entries[0].type;
                }
                if (window.performance && window.performance.navigation) {
                    const type = window.performance.navigation.type;
                    if (type === 1) return 'reload';
                    if (type === 2) return 'back_forward';
                    return 'navigate';
                }
            } catch(e) {}
            return 'navigate';
        }

        function restoreFilterState() {
            const navType = getNavigationType();

            @if(session('success'))
            try { sessionStorage.removeItem('jenis_inventaris_filter_state'); } catch(e) {}
            @endif

            let isReturning = false;
            try {
                if (sessionStorage.getItem('jenis_inventaris_returning') === '1') {
                    isReturning = true;
                    sessionStorage.removeItem('jenis_inventaris_returning');
                }
            } catch(e) {}

            const ref = document.referrer || '';
            if (ref.includes('/jenis-inventaris/') || ref.includes('/create') || ref.includes('/edit')) {
                isReturning = true;
            }
            if (navType === 'back_forward') {
                isReturning = true;
            }

            // Manual Reload
            if (navType === 'reload' && !isReturning && !window._simaReloadResetHandled) {
                window._simaReloadResetHandled = true;
                try { sessionStorage.removeItem('jenis_inventaris_filter_state'); } catch(e) {}
                if (window.location.search) {
                    history.replaceState(null, '', window.location.pathname);
                }
                const searchInput = document.getElementById('searchInput');
                if (searchInput) {
                    searchInput.value = '';
                    toggleSearchClear(searchInput);
                }
                const defaultStatus = document.querySelector('.status-filter[value="all"]');
                if (defaultStatus) defaultStatus.checked = true;
                const defaultSort = document.querySelector('.sort-filter[value="terbaru"]');
                if (defaultSort) defaultSort.checked = true;
                appliedStatus = 'all';
                appliedSort   = 'terbaru';
                const perPageSelect = document.getElementById('perPageSelect');
                if (perPageSelect) perPageSelect.value = '10';
                fetchJenis(true, 1);
                return;
            }

            // Normal Navigate
            if (!isReturning && navType === 'navigate' && !window.location.search) {
                try { sessionStorage.removeItem('jenis_inventaris_filter_state'); } catch(e) {}
                return;
            }

            // Restore from state
            let currentSearch = window.location.search;
            let hasUrlQuery = (currentSearch && currentSearch.length > 1);

            if (!hasUrlQuery) {
                try {
                    const saved = sessionStorage.getItem('jenis_inventaris_filter_state');
                    if (saved && saved.length > 0) {
                        currentSearch = '?' + saved;
                        history.replaceState(null, '', window.location.pathname + currentSearch);
                        hasUrlQuery = true;
                    }
                } catch(e) {}
            }

            if (!hasUrlQuery) return;

            const urlParams = new URLSearchParams(currentSearch);
            appliedStatus = urlParams.get('status') || 'all';
            appliedSort   = urlParams.get('sort') || 'terbaru';

            const stRadio = document.querySelector(`.status-filter[value="${appliedStatus}"]`);
            if (stRadio) stRadio.checked = true;

            const srRadio = document.querySelector(`.sort-filter[value="${appliedSort}"]`);
            if (srRadio) srRadio.checked = true;

            const q = urlParams.get('search') || '';
            const input = document.getElementById('searchInput');
            if (input) { input.value = q; toggleSearchClear(input); }

            const perPageParam = urlParams.get('per_page');
            const perPageSelect = document.getElementById('perPageSelect');
            if (perPageSelect && perPageParam) {
                perPageSelect.value = perPageParam;
            }

            const page = parseInt(urlParams.get('page')) || 1;
            fetchJenis(true, page);
        }

        document.addEventListener('DOMContentLoaded', function() {
            restoreFilterState();

            const autoRefreshMs = 15000;
            setInterval(function () {
                if (document.hidden) return;
                if (document.querySelectorAll('.row-check:checked, #check-all:checked').length > 0) return;
                if (document.querySelector('.modal.show, [id*="modal"][style*="display: flex"], [id*="modal"][style*="display: block"]')) return;
                if (document.querySelector('.dropdown-menu.show, .dropdown.show')) return;
                fetchJenis(true);
            }, autoRefreshMs);
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
