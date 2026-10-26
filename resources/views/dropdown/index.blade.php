<!DOCTYPE html>
<html lang="id">
<head>
    @include('layouts.head-page-meta', ['title' => 'Master Dropdown - Sistem Manajemen Aset Desa'])
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
                        Master Dropdown Dinamis
                    </h1>
                </div>
                <div class="page-header-actions" style="display: flex; gap: 10px; align-items: center; flex-wrap: wrap;">
                    <div class="sima-dropdown-wrapper" style="position: relative; display: inline-block;">
                        <button type="button" id="btn-export-dropdown-menu" class="btn-sima-secondary btn-sima-pill-white" onclick="toggleExportMenu(this, event)">
                            <i data-feather="download" style="width: 14px; height: 14px;"></i>
                            <span>Ekspor Data</span>
                            <i data-feather="chevron-down" style="width: 12px; height: 12px; opacity: 0.7;"></i>
                        </button>
                        <div class="export-dropdown-menu" style="display: none;">
                            <a class="export-menu-item" id="export-dropdown-excel" href="{{ route('dropdown.export', ['format' => 'excel']) }}" onclick="if(window.showToast) window.showToast('Ekspor data pilihan dropdown (Excel) berhasil! Berkas sedang diunduh...', 'success'); this.closest('.export-dropdown-menu').style.display='none';">
                                <i data-feather="file-text" style="width: 14px; height: 14px; color: #10b981;"></i>
                                <span>Ekspor Excel (.xlsx)</span>
                            </a>
                            <hr class="dropdown-divider sima-divider" />
                            <a class="export-menu-item" id="export-dropdown-pdf" href="{{ route('dropdown.export', ['format' => 'pdf']) }}" onclick="if(window.showToast) window.showToast('Ekspor data pilihan dropdown (PDF) berhasil! Berkas sedang diunduh...', 'success'); this.closest('.export-dropdown-menu').style.display='none';">
                                <i data-feather="file" style="width: 14px; height: 14px; color: #ef4444;"></i>
                                <span>Ekspor PDF (.pdf)</span>
                            </a>
                        </div>
                    </div>
                    @can('create', \App\Models\Dropdown::class)
                    <button type="button" class="btn-sima-secondary btn-sima-pill-white" onclick="openImportModal()">
                        <i data-feather="upload-cloud" style="width: 14px; height: 14px;"></i>
                        <span>Import CSV</span>
                    </button>
                    <a href="{{ route('dropdown.create') }}" class="btn-sima-primary">
                        <i data-feather="plus" style="width: 14px; height: 14px;"></i>
                        <span>Tambah Dropdown</span>
                    </a>
                    @endcan
                </div>
            </div>

            @php
                $totalDropdown = $dropdowns->total();
                $totalItems    = \App\Models\DropdownItem::count();
                $totalDigunakan = \App\Models\JenisInventarisData::where('tipe','dropdown')->whereNotNull('dropdown_id')->distinct('dropdown_id')->count('dropdown_id');
            @endphp

            <!-- 1. STAT CARDS (Prodex 4-Card Summary Bar) -->
            <div class="stat-card-row">
                <!-- Card 1: Total Dropdown -->
                <div class="stat-card-sima" id="card-total-dropdown">
                    <div class="stat-header">
                        <span class="stat-label">TOTAL DROPDOWN</span>
                    </div>
                    <div class="stat-number">{{ number_format($totalDropdown ?? 0, 0, ',', '.') }}</div>
                    <div class="kpi-trend-wrapper stat-delta">
                        <span class="stat-delta-label">master dropdown aktif</span>
                    </div>
                </div>

                <!-- Card 2: Total Pilihan Opsi -->
                <div class="stat-card-sima" id="card-total-opsi">
                    <div class="stat-header">
                        <span class="stat-label">TOTAL PILIHAN OPSI</span>
                    </div>
                    <div class="stat-number">{{ number_format($totalItems ?? 0, 0, ',', '.') }}</div>
                    <div class="kpi-trend-wrapper stat-delta">
                        <span class="stat-delta-label">item pilihan tersedia</span>
                    </div>
                </div>

                <!-- Card 3: Digunakan di Jenis -->
                <div class="stat-card-sima" id="card-digunakan-jenis">
                    <div class="stat-header">
                        <span class="stat-label">DIGUNAKAN DI JENIS</span>
                    </div>
                    <div class="stat-number">{{ number_format($stats['usedInJenis'] ?? $totalDigunakan ?? 0, 0, ',', '.') }}</div>
                    <div class="kpi-trend-wrapper stat-delta">
                        <span class="stat-delta-label">terhubung ke inventaris</span>
                    </div>
                </div>

                <!-- Card 4: Pilihan Terbanyak -->
                <div class="stat-card-sima" id="card-top-dropdown">
                    <div class="stat-header">
                        <span class="stat-label">PILIHAN TERBANYAK</span>
                    </div>
                    <div class="stat-number" style="font-size: 18px; line-height: 1.3; white-space: nowrap; overflow: hidden; text-overflow: ellipsis;" title="{{ $stats['topDropdownNama'] ?? '-' }}">{{ $stats['topDropdownNama'] ?? '-' }}</div>
                    <div class="kpi-trend-wrapper stat-delta">
                        <span class="stat-delta-pill is-up">
                            {{ number_format($stats['topDropdownCount'] ?? 0, 0, ',', '.') }} pilihan
                        </span>
                        <span class="stat-delta-label">terbanyak</span>
                    </div>
                </div>
            </div>

            <!-- 2. MAIN TABLE CARD -->
            <div class="sima-card" style="padding: 0; overflow: visible; margin-bottom: 32px;">
                
                <!-- Action Controls Bar -->
                <div style="display: flex; justify-content: space-between; align-items: center; padding: 20px 20px; border-bottom: 1px solid var(--color-border); border-top-left-radius: var(--radius-lg); border-top-right-radius: var(--radius-lg); flex-wrap: wrap; gap: 12px;">
                    <!-- Left: Search Box -->
                    <div class="topbar-search-box">
                        <i data-feather="search" style="width: 15px; height: 15px; color: var(--color-text-muted); flex-shrink: 0;"></i>
                        <input
                            type="text"
                            id="searchInput"
                            class="topbar-search-input"
                            placeholder="Cari dropdown..."
                            value="{{ request('search') }}"
                            oninput="onSearchInput(this)"
                            onkeypress="if(event.key==='Enter'){event.preventDefault();fetchDropdowns(false, 1);}"
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
                                <div style="color: var(--color-primary); font-size: 12px; font-weight: 700; text-transform: uppercase; letter-spacing: 0.04em; margin-bottom: 8px;">Filter Penggunaan</div>
                                <div style="display: flex; flex-direction: column; gap: 8px; margin-bottom: 14px;">
                                    <label style="cursor: pointer; display: flex; align-items: center; gap: 8px; font-size: 13px; color: var(--color-text); margin: 0;">
                                        <input type="radio" name="statusFilter" value="all" class="status-filter" checked style="accent-color: var(--color-primary); cursor: pointer;">
                                        <span>Semua Dropdown</span>
                                    </label>
                                    <label style="cursor: pointer; display: flex; align-items: center; gap: 8px; font-size: 13px; color: var(--color-text); margin: 0;">
                                        <input type="radio" name="statusFilter" value="digunakan" class="status-filter" style="accent-color: var(--color-primary); cursor: pointer;">
                                        <span>Digunakan di Aset</span>
                                    </label>
                                    <label style="cursor: pointer; display: flex; align-items: center; gap: 8px; font-size: 13px; color: var(--color-text); margin: 0;">
                                        <input type="radio" name="statusFilter" value="tidak_digunakan" class="status-filter" style="accent-color: var(--color-primary); cursor: pointer;">
                                        <span>Belum Digunakan</span>
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
                                    <button type="button" onclick="applyFilters(); event.stopPropagation();" class="btn-sima-primary" style="width: 100%; justify-content: center; font-size: 13px;">
                                        Terapkan Filter
                                    </button>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Bulk Selection Action Bar -->
                @can('delete', \App\Models\Dropdown::class)
                <div id="bulk-bar" class="bulk-action-bar-sima">
                    <span class="bulk-count"><span id="bulk-count">0</span> dropdown terpilih</span>
                    <div class="bulk-divider"></div>
                    <button type="button" class="btn-sima-danger" onclick="bulkDeleteConfirm()" style="padding: 5px 12px; font-size: 12.5px;">
                        <i data-feather="trash-2" style="width: 13px; height: 13px;"></i>
                        <span>Hapus Terpilih</span>
                    </button>
                    <button type="button" class="btn-sima-icon" onclick="clearSelection()" style="width: 28px; height: 28px;" title="Batalkan Pilihan">
                        <i data-feather="x" style="width: 13px; height: 13px;"></i>
                    </button>
                </div>
                <form id="bulk-delete-form" method="POST" action="{{ route('dropdown.bulk-destroy') }}" style="display:none;">@csrf</form>
                @endcan

                <!-- Table Container -->
                <div class="table-sima-wrapper table-loading-wrap" id="table-wrap" style="border: none; border-radius: 0;">
                    <table class="table-sima">
                        <thead>
                            <tr>
                                @can('delete', \App\Models\Dropdown::class)
                                <th style="width: 44px; text-align: center; padding-left: 16px;">
                                    <input type="checkbox" id="check-all" onchange="toggleAll(this)" class="sima-checkbox">
                                </th>
                                @endcan
                                <th style="width: 60px; text-align: center;">No</th>
                                <th style="min-width: 240px;">Nama Dropdown</th>
                                <th style="min-width: 320px;">Deskripsi</th>
                                <th style="width: 150px; text-align: center;">Jumlah Pilihan</th>
                                <th style="width: 120px; text-align: center;">Aksi</th>
                            </tr>
                        </thead>
                        <tbody id="table-body">
                            @forelse($dropdowns as $i => $dropdown)
                            <tr>
                                @can('delete', \App\Models\Dropdown::class)
                                <td style="text-align: center; padding-left: 16px;">
                                    <input type="checkbox" class="row-check sima-checkbox" value="{{ $dropdown->id }}" onchange="updateBulkBar()">
                                </td>
                                @endcan
                                <td style="text-align: center; color: var(--color-text-muted); font-variant-numeric: tabular-nums;">
                                    {{ $dropdowns->firstItem() + $i }}
                                </td>
                                <td>
                                    <div style="font-weight: 600; color: var(--color-text); font-size: 13.5px;">
                                        {{ $dropdown->nama }}
                                    </div>
                                </td>
                                <td>
                                    <span style="font-size: 13px; color: var(--color-text-muted);">
                                        {{ $dropdown->deskripsi ? \Illuminate\Support\Str::limit($dropdown->deskripsi, 80) : '-' }}
                                    </span>
                                </td>
                                <td style="text-align: center;">
                                    <span style="display: inline-block; padding: 3px 9px; font-size: 12px; font-weight: 600; color: var(--color-primary); font-variant-numeric: tabular-nums;">
                                        {{ $dropdown->items_count }} pilihan
                                    </span>
                                </td>
                                <td>
                                    <div style="display: flex; align-items: center; justify-content: center; gap: 4px;">
                                        <a href="{{ route('dropdown.show', $dropdown->id) }}" class="btn-sima-icon view" title="Lihat Pilihan">
                                            <i data-feather="eye"></i>
                                        </a>
                                        @can('update', $dropdown)
                                        <a href="{{ route('dropdown.edit', $dropdown->id) }}" class="btn-sima-icon edit" title="Edit Dropdown">
                                            <i data-feather="edit-2"></i>
                                        </a>
                                        @endcan
                                        @can('delete', $dropdown)
                                        <button type="button" class="btn-sima-icon delete" title="Hapus Dropdown"
                                                onclick="AppModal.confirmDelete('{{ addslashes($dropdown->nama) }}', function(){ document.getElementById('delForm{{ $dropdown->id }}').submit(); })">
                                            <i data-feather="trash-2"></i>
                                        </button>
                                        <form action="{{ route('dropdown.destroy', $dropdown->id) }}" method="POST" style="display:none;" id="delForm{{ $dropdown->id }}">
                                            @csrf @method('DELETE')
                                        </form>
                                        @endcan
                                    </div>
                                </td>
                            </tr>
                            @empty
                            <tr>
                                <td @can('delete', \App\Models\Dropdown::class) colspan="6" @else colspan="5" @endcan style="text-align: center; padding: 48px 20px; color: var(--color-text-muted);">
                                    <div style="display: flex; flex-direction: column; align-items: center; justify-content: center; gap: 8px;">
                                        <i data-feather="inbox" style="width: 42px; height: 42px; stroke-width: 1.5; color: var(--color-border-dark);"></i>
                                        <p style="margin: 0; font-size: 13.5px; font-weight: 500;">Belum ada master dropdown yang sesuai</p>
                                    </div>
                                </td>
                            </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>

                <!-- 3-Column Pagination Bar Footer -->
                <div class="pagination-bar" id="paginationBar" style="padding: 32px 20px; border-top: 1px solid var(--color-border); border-bottom-left-radius: var(--radius-lg); border-bottom-right-radius: var(--radius-lg); background: var(--color-surface); flex-wrap: wrap; gap: 14px;">
                    <div class="pagination-left">
                        <div class="page-size-wrap">
                            <span>Tampilkan</span>
                            <select id="perPageSelect" class="per-page-select" onchange="changePerPage(this.value)">
                                <option value="10" {{ ($dropdowns->perPage() == 10) ? 'selected' : '' }}>10</option>
                                <option value="20" {{ ($dropdowns->perPage() == 20) ? 'selected' : '' }}>20</option>
                                <option value="50" {{ ($dropdowns->perPage() == 50) ? 'selected' : '' }}>50</option>
                                <option value="100" {{ ($dropdowns->perPage() == 100) ? 'selected' : '' }}>100</option>
                            </select>
                            <span>data</span>
                        </div>
                    </div>

                    <div class="pagination-center">
                        <nav class="pagination-nav" id="paginationNav">
                            @if($dropdowns->onFirstPage())
                                <span class="page-btn disabled" title="Sebelumnya"><i data-feather="chevron-left"></i></span>
                            @else
                                <button type="button" class="page-btn" onclick="goToPage({{ $dropdowns->currentPage() - 1 }})" title="Sebelumnya"><i data-feather="chevron-left"></i></button>
                            @endif
                            @php
                                $cp = $dropdowns->currentPage(); $lp = $dropdowns->lastPage();
                                $ws = max(1,$cp-2); $we = min($lp,$cp+2);
                                if(($we-$ws+1)<5){ if($ws===1){$we=min($lp,5);}else{$ws=max(1,$we-4);} }
                            @endphp
                            @foreach($dropdowns->getUrlRange($ws,$we) as $page => $url)
                                @if($page==$cp)
                                    <span class="page-btn active">{{ $page }}</span>
                                @else
                                    <button type="button" class="page-btn" onclick="goToPage({{ $page }})">{{ $page }}</button>
                                @endif
                            @endforeach
                            @if($dropdowns->hasMorePages())
                                <button type="button" class="page-btn" onclick="goToPage({{ $dropdowns->currentPage() + 1 }})" title="Selanjutnya"><i data-feather="chevron-right"></i></button>
                            @else
                                <span class="page-btn disabled" title="Selanjutnya"><i data-feather="chevron-right"></i></span>
                            @endif
                        </nav>
                        <div class="page-jump-wrap">
                            <label for="pageJumpInput">Ke hal.</label>
                            <input id="pageJumpInput" type="number" class="page-jump-input"
                                   min="1" max="{{ $dropdowns->lastPage() }}"
                                   value="{{ $dropdowns->currentPage() }}"
                                   placeholder="1–{{ $dropdowns->lastPage() }}"
                                   title="Masukkan nomor halaman (1–{{ $dropdowns->lastPage() }})">
                        </div>
                    </div>

                    <div class="pagination-right" id="paginationRangeInfo">
                        Menampilkan <strong>{{ $dropdowns->firstItem() ?? 0 }}-{{ $dropdowns->lastItem() ?? 0 }}</strong> dari <strong>{{ $dropdowns->total() }}</strong> data
                    </div>
                </div>
            </div>
        </div>
    </div>

    @include('layouts.footer-block')
    @include('layouts.footer-js')

    <script>
        function changePerPage(val) {
            fetchDropdowns(false, 1);
        }

        function goToPage(page) {
            fetchDropdowns(false, page);
        }

        (function() {
            const jumpInput = document.getElementById('pageJumpInput');
            if (!jumpInput) return;
            jumpInput.addEventListener('keydown', function(e) {
                if (e.key !== 'Enter') return;
                const lastPage = parseInt(this.max, 10);
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

            fetchDropdowns(false, 1);
        }

        function resetFilters() {
            const searchInput = document.getElementById('searchInput');
            if (searchInput) {
                searchInput.value = '';
                const clearBtn = document.getElementById('clear-search-btn');
                if (clearBtn) clearBtn.style.display = 'none';
            }

            appliedStatus = 'all';
            appliedSort   = 'terbaru';

            const defaultStatus = document.querySelector('.status-filter[value="all"]');
            if (defaultStatus) defaultStatus.checked = true;

            const defaultSort = document.querySelector('.sort-filter[value="terbaru"]');
            if (defaultSort) defaultSort.checked = true;

            const filterMenu = document.getElementById('filter-dropdown-menu');
            if (filterMenu) filterMenu.style.display = 'none';

            fetchDropdowns(false, 1);
        }

        let appliedStatus = 'all';
        let appliedSort   = 'terbaru';

        function fetchDropdowns(isAutoRefresh = false, pageOverride = null) {
            const q            = (document.getElementById('searchInput')?.value || '').trim();
            const perPageEl    = document.getElementById('perPageSelect');
            const perPageValue = perPageEl ? perPageEl.value : '10';

            const params       = new URLSearchParams();

            if (q) params.set('search', q);
            if (appliedStatus && appliedStatus !== 'all') params.set('status', appliedStatus);
            if (appliedSort && appliedSort !== 'terbaru') params.set('sort', appliedSort);
            if (perPageValue && perPageValue !== '10') params.set('per_page', perPageValue);

            const urlParams = new URLSearchParams(window.location.search);
            const currentPage = pageOverride !== null ? pageOverride : (urlParams.get('page') || 1);
            if (currentPage > 1) params.set('page', currentPage);

            history.replaceState(null, '', window.location.pathname + (params.toString() ? '?' + params.toString() : ''));

            const wrap = document.querySelector('.table-loading-wrap');
            if (!isAutoRefresh && wrap) wrap.classList.add('is-loading');

            fetch('{{ route('dropdown.index') }}?' + params.toString(), {
                headers: { 'X-Requested-With': 'XMLHttpRequest', 'Accept': 'application/json' }
            })
            .then(r => r.json())
            .then(data => {
                updateStats(data.stats);
                updateTable(data.dropdowns, data.pagination);
                updatePaginationUI(data.pagination);
                if (typeof feather !== 'undefined') feather.replace();
            })
            .catch(err => console.error('Gagal fetch dropdown:', err))
            .finally(() => {
                if (wrap) wrap.classList.remove('is-loading');
            });
        }

        let _searchTimer = null;
        function onSearchInput(input) {
            const clearBtn = document.getElementById('clear-search-btn');
            if (clearBtn) clearBtn.style.display = input.value.trim().length > 0 ? 'block' : 'none';
            clearTimeout(_searchTimer);
            _searchTimer = setTimeout(function() {
                fetchDropdowns(false, 1);
            }, 400);
        }

        function clearSearch() {
            const input = document.getElementById('searchInput');
            if (input) {
                input.value = '';
                const clearBtn = document.getElementById('clear-search-btn');
                if (clearBtn) clearBtn.style.display = 'none';
            }
            fetchDropdowns(false, 1);
        }

        function applyFilters() {
            const statusEl = document.querySelector('input[name="statusFilter"]:checked');
            const sortEl   = document.querySelector('input[name="sortFilter"]:checked');
            appliedStatus  = statusEl ? statusEl.value : 'all';
            appliedSort    = sortEl   ? sortEl.value   : 'terbaru';

            document.querySelectorAll('.dropdown-menu.show').forEach(el => el.classList.remove('show'));
            document.querySelectorAll('.dropdown.show').forEach(el => el.classList.remove('show'));

            fetchDropdowns(false, 1);
        }

        function resetFilters() {
            appliedStatus = 'all';
            appliedSort   = 'terbaru';

            const rStatusAll = document.querySelector('input[name="statusFilter"][value="all"]');
            const rSortAsc   = document.querySelector('input[name="sortFilter"][value="terbaru"]');
            if (rStatusAll) rStatusAll.checked = true;
            if (rSortAsc)   rSortAsc.checked   = true;

            const input = document.getElementById('searchInput');
            if (input) {
                input.value = '';
                const clearBtn = document.getElementById('clear-search-btn');
                if (clearBtn) clearBtn.style.display = 'none';
            }

            document.querySelectorAll('.dropdown-menu.show').forEach(el => el.classList.remove('show'));
            document.querySelectorAll('.dropdown.show').forEach(el => el.classList.remove('show'));

            fetchDropdowns(false, 1);
        }

        function updateStats(stats) {
            if (!stats) return;
            const cards = document.querySelectorAll('.stat-card-sima');
            if (cards[0]) {
                const el = cards[0].querySelector('.stat-number');
                if (el && stats.totalDropdowns !== undefined) el.textContent = (stats.totalDropdowns || 0).toLocaleString('id-ID');
            }
            if (cards[1]) {
                const el = cards[1].querySelector('.stat-number');
                if (el && stats.totalItems !== undefined) el.textContent = (stats.totalItems || 0).toLocaleString('id-ID');
            }
            if (cards[2]) {
                const el = cards[2].querySelector('.stat-number');
                if (el && stats.usedInJenis !== undefined) el.textContent = (stats.usedInJenis || 0).toLocaleString('id-ID');
            }
            if (cards[3]) {
                const el = cards[3].querySelector('.stat-number');
                if (el && stats.topDropdownNama !== undefined) {
                    el.textContent = stats.topDropdownNama || '-';
                    el.title = stats.topDropdownNama || '-';
                }
                const pill = cards[3].querySelector('.stat-delta-pill');
                if (pill && stats.topDropdownCount !== undefined) {
                    pill.textContent = (stats.topDropdownCount || 0).toLocaleString('id-ID') + ' pilihan';
                }
            }
        }

        function updateTable(dropdowns, pagination) {
            const tbody = document.getElementById('table-body');
            if (!tbody) return;
            const firstItem = pagination?.firstItem ?? 1;
            const canDelete = {{ auth()->user()->can('delete', \App\Models\Dropdown::class) ? 'true' : 'false' }};
            const colspan = canDelete ? 6 : 5;

            if (!dropdowns || dropdowns.length === 0) {
                tbody.innerHTML = `<tr><td colspan="${colspan}" style="text-align: center; padding: 48px 20px; color: var(--color-text-muted);">
                    <div style="display: flex; flex-direction: column; align-items: center; justify-content: center; gap: 8px;">
                        <i data-feather="inbox" style="width: 42px; height: 42px; stroke-width: 1.5; color: var(--color-border-dark);"></i>
                        <p style="margin: 0; font-size: 13.5px; font-weight: 500;">Belum ada master dropdown yang sesuai</p>
                    </div></td></tr>`;
                return;
            }

            let html = '';
            dropdowns.forEach((dd, idx) => {
                const no   = firstItem + idx;
                const desc = dd.deskripsi
                    ? dd.deskripsi.substring(0, 80) + (dd.deskripsi.length > 80 ? '…' : '')
                    : '-';
                const chk = canDelete
                    ? `<td style="text-align: center; padding-left: 16px;"><input type="checkbox" class="row-check sima-checkbox" value="${dd.id}" onchange="updateBulkBar()"></td>`
                    : '';
                html += `<tr>
                    ${chk}
                    <td style="text-align: center; color: var(--color-text-muted); font-variant-numeric: tabular-nums;">${no}</td>
                    <td><div style="font-weight: 600; color: var(--color-text); font-size: 13.5px;">${dd.nama}</div></td>
                    <td><span style="font-size: 13px; color: var(--color-text-muted);">${desc}</span></td>
                    <td style="text-align: center;"><span style="display: inline-block; padding: 3px 9px; font-size: 12px; font-weight: 600; border-radius: var(--radius-full); background: var(--color-primary-tint); color: var(--color-primary); font-variant-numeric: tabular-nums;">${dd.items_count} pilihan</span></td>
                    <td>
                        <div style="display: flex; align-items: center; justify-content: center; gap: 4px;">
                            <a href="{{ url('dropdown') }}/${dd.id}" class="btn-sima-icon view" title="Lihat Pilihan"><i data-feather="eye"></i></a>
                            ${canDelete ? `<a href="{{ url('dropdown') }}/${dd.id}/edit" class="btn-sima-icon edit" title="Edit Dropdown"><i data-feather="edit-2"></i></a>
                            <button type="button" class="btn-sima-icon delete" title="Hapus Dropdown" data-nama="${(dd.nama||'').replace(/"/g, '&quot;')}" onclick="confirmDeleteDropdown(this.getAttribute('data-nama'), ${dd.id})"><i data-feather="trash-2"></i></button>
                            <form id="delForm${dd.id}" action="{{ url('dropdown') }}/${dd.id}" method="POST" style="display:none;">@csrf @method('DELETE')</form>` : ''}
                        </div>
                    </td>
                </tr>`;
            });
            tbody.innerHTML = html;
        }

        function confirmDeleteDropdown(nama, id) {
            AppModal.confirmDelete(nama, function() {
                const f = document.getElementById('delForm' + id);
                if (f) f.submit();
            });
        }

        function updatePaginationUI(pagination) {
            if (!pagination) return;
            const firstItem   = pagination.firstItem   || 0;
            const lastItem    = pagination.lastItem    || 0;
            const total       = pagination.total       || 0;
            const currentPage = pagination.currentPage || 1;
            const lastPage    = pagination.lastPage    || 1;

            const rangeEl = document.getElementById('paginationRangeInfo');
            if (rangeEl) rangeEl.innerHTML = `Menampilkan <strong>${firstItem}-${lastItem}</strong> dari <strong>${total}</strong> data`;

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
            html += currentPage <= 1
                ? `<span class="page-btn disabled" title="Sebelumnya"><i data-feather="chevron-left"></i></span>`
                : `<button type="button" class="page-btn" onclick="goToPage(${currentPage - 1})" title="Sebelumnya"><i data-feather="chevron-left"></i></button>`;
            let ws = Math.max(1, currentPage - 2);
            let we = Math.min(lastPage, currentPage + 2);
            if ((we - ws + 1) < 5) {
                if (ws === 1) ws = Math.min(lastPage, 5);
                else ws = Math.max(1, we - 4);
            }
            for (let p = ws; p <= we; p++) {
                if (p === currentPage) {
                    html += `<span class="page-btn active">${p}</span>`;
                } else {
                    html += `<button type="button" class="page-btn" onclick="goToPage(${p})">${p}</button>`;
                }
            }
            html += currentPage >= lastPage
                ? `<span class="page-btn disabled" title="Selanjutnya"><i data-feather="chevron-right"></i></span>`
                : `<button type="button" class="page-btn" onclick="goToPage(${currentPage + 1})" title="Selanjutnya"><i data-feather="chevron-right"></i></button>`;
            nav.innerHTML = html;
        }

        function toggleAll(master) {
            document.querySelectorAll('.row-check').forEach(cb => cb.checked = master.checked);
            updateBulkBar();
        }

        function updateBulkBar() {
            const checked = document.querySelectorAll('.row-check:checked');
            const bar     = document.getElementById('bulk-bar');
            const countEl = document.getElementById('bulk-count');
            const allCb   = document.getElementById('check-all');

            if (countEl) countEl.textContent = checked.length;
            if (bar) {
                if (checked.length > 0) {
                    bar.classList.add('show');
                } else {
                    bar.classList.remove('show');
                }
            }

            const total = document.querySelectorAll('.row-check').length;
            if (allCb) allCb.checked = (total > 0 && checked.length === total);
            if (typeof feather !== 'undefined') feather.replace();
        }

        function clearSelection() {
            document.querySelectorAll('.row-check').forEach(cb => cb.checked = false);
            const allCb = document.getElementById('check-all');
            if (allCb) allCb.checked = false;
            const bar = document.getElementById('bulk-bar');
            if (bar) bar.classList.remove('show');
            updateBulkBar();
        }

        function bulkDeleteConfirm() {
            const checked = Array.from(document.querySelectorAll('.row-check:checked')).map(cb => cb.value);
            if (!checked.length) return;

            AppModal.confirmDelete(`${checked.length} data dropdown terpilih`, function() {
                const form = document.getElementById('bulk-delete-form');
                form.querySelectorAll('input[name="ids[]"]').forEach(el => el.remove());
                checked.forEach(id => {
                    const inp = document.createElement('input');
                    inp.type  = 'hidden';
                    inp.name  = 'ids[]';
                    inp.value = id;
                    form.appendChild(inp);
                });
                form.submit();
            });
        }
    </script>

    <!-- MODAL IMPORT EXCEL DROPDOWN (SIMA PREMIER THEME) -->
    <div id="import-modal" class="modal-sima" style="display:none; position:fixed; inset:0; background:rgba(31,58,42,0.45); z-index:9999; align-items:center; justify-content:center; backdrop-filter: blur(4px); padding: 16px;">
        <div class="modal-sima-box" style="width: 90%; max-width: 480px; display: flex; flex-direction: column; overflow: hidden;">
            <!-- Header -->
            <div class="modal-sima-header" style="position: relative; border-bottom: none !important; padding: 20px 24px 16px 24px; display: flex; align-items: center; justify-content: center;">
                <div style="font-size: 16px; font-weight: 700; color: var(--color-text); text-align: center; width: 100%;">Import Data Dropdown</div>
            </div>

            <!-- Body -->
            <div class="modal-sima-body" style="padding: 20px 24px;">
                <!-- Unduh Template Bar -->
                <div id="import-template-bar" style="display: flex; flex-direction: column; align-items: left; justify-content: left; text-align: left; gap: 10px; padding-bottom: 16px; border-bottom: 1px dashed var(--color-border); margin-bottom: 18px;">
                    <div style="text-align: left;">
                        <div style="font-size: 13.5px; font-weight: 600; color: var(--color-text);">Template Format CSV</div>
                        <div style="font-size: 12px; color: var(--color-text-muted); margin-top: 2px;">Sudah siap dengan baris contoh dan daftar referensi.</div>
                    </div>
                    <a href="{{ route('dropdown.template.download') }}" onclick="if(window.showToast) window.showToast('Template import dropdown (CSV) berhasil diunduh! Berkas sedang diproses...', 'success');" class="btn-sima-primary" style="align-self: flex-start;  border-radius: var(--radius-full); padding: 7px 18px; font-size: 12.5px; text-decoration: none; flex-shrink: 0; display: inline-flex; align-items: center; gap: 6px;">
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
                            <div id="import-dropzone" style="border: 1.5px dashed var(--color-border); border-radius: var(--radius-md); padding: 24px 16px; text-align: left; cursor: pointer; transition: border-color 0.15s ease, background-color 0.15s ease; background: var(--color-surface);" onclick="document.getElementById('import_file_input').click()">
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
                            Pilihan item yang sudah terdaftar pada kategori dropdown terkait akan otomatis dilewati (skip) agar data yang sudah ada tidak tertimpa.
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
                                    <span style="font-size: 11.5px; color: var(--color-text-muted); font-weight: 500;">item</span>
                                </div>
                                <div style="font-size: 11px; color: var(--color-text-muted); margin-top: 2px;">Data baru masuk</div>
                            </div>

                            <!-- Dilewati (Kuning) -->
                            <div style="text-align: center; padding: 0 8px; border-right: 1px dashed var(--color-border);">
                                <div style="font-size: 11px; font-weight: 600; color: var(--color-warning); text-transform: uppercase; letter-spacing: 0.5px;">Dilewati</div>
                                <div style="margin-top: 4px; display: flex; align-items: baseline; justify-content: center; gap: 4px;">
                                    <span id="res-count-skipped" style="font-size: 22px; font-weight: 700; color: var(--color-warning); font-variant-numeric: tabular-nums;">0</span>
                                    <span style="font-size: 11.5px; color: var(--color-text-muted); font-weight: 500;">item</span>
                                </div>
                                <div style="font-size: 11px; color: var(--color-text-muted); margin-top: 2px;">Item sudah ada</div>
                            </div>

                            <!-- Gagal (Merah) -->
                            <div style="text-align: center; padding: 0 8px;">
                                <div style="font-size: 11px; font-weight: 600; color: var(--color-danger); text-transform: uppercase; letter-spacing: 0.5px;">Gagal</div>
                                <div style="margin-top: 4px; display: flex; align-items: baseline; justify-content: center; gap: 4px;">
                                    <span id="res-count-failed" style="font-size: 22px; font-weight: 700; color: var(--color-danger); font-variant-numeric: tabular-nums;">0</span>
                                    <span style="font-size: 11.5px; color: var(--color-text-muted); font-weight: 500;">item</span>
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
                                            <th style="padding: 8px 12px; text-align: left; font-size: 11px; font-weight: 600; color: var(--color-text-muted); text-transform: uppercase; letter-spacing: 0.5px;">Identitas Dropdown</th>
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
        // FITUR IMPORT EXCEL MASTER DROPDOWN
        // ==========================================
        let importStageTimer = null;
        let importStatsAccumulator = { imported: 0, skipped: 0, failed: 0 };
        const importStages = [
            'Membaca dan memvalidasi baris data...',
            'Memeriksa kategori dropdown & item...',
            'Menyimpan master dropdown desa...'
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

            fetch('{{ route("dropdown.import") }}', {
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
                            ? `${importedCount} item baru berhasil disimpan, dan ${skippedCount} item yang sudah ada dilewati.`
                            : `${importedCount} item dropdown baru berhasil disimpan ke dalam sistem.`;
                    } else if (importedCount === 0 && skippedCount > 0 && failedCount === 0) {
                        titleEl.textContent = 'Data Sudah Terdaftar (Dilewati Otomatis)';
                        descEl.textContent = `Seluruh ${skippedCount} item pada berkas CSV sudah ada di sistem. Baris dilewati agar data eksisting tidak tertimpa.`;
                    } else if (failedCount > 0) {
                        titleEl.textContent = 'Impor Selesai dengan Catatan';
                        descEl.textContent = `${importedCount} item baru masuk, ${skippedCount} dilewati, dan ${failedCount} baris memerlukan koreksi format.`;
                    } else {
                        titleEl.textContent = 'Pemeriksaan Berkas Selesai';
                        descEl.textContent = 'Tidak ada pilihan dropdown baru yang ditambahkan ke sistem.';
                    }

                    const tbody = document.getElementById('res-details-body');
                    tbody.innerHTML = '';
                    const issues = [];

                    (data.failed_rows || []).forEach(r => {
                        issues.push({
                            row: r.row,
                            status: '<span class="status-pill status-pill-danger" style="font-size: 11px; padding: 2px 8px; font-weight: 600;">Gagal</span>',
                            dropdown: r.nama || 'Dropdown',
                            item: r.item || '-',
                            reason: r.reason || 'Format atau kelengkapan data tidak valid'
                        });
                    });

                    (data.skipped_rows || []).forEach(r => {
                        issues.push({
                            row: r.row,
                            status: '<span class="status-pill status-pill-warning" style="font-size: 11px; padding: 2px 8px; font-weight: 600;">Dilewati</span>',
                            dropdown: r.nama || 'Dropdown',
                            item: r.item || '-',
                            reason: r.reason || 'Pilihan item sudah terdaftar di dropdown ini'
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
                                    <div style="font-weight: 600; color: var(--color-text); font-size: 12.5px; line-height: 1.35;">${it.dropdown}</div>
                                    <div style="font-size: 11px; color: var(--color-text-muted); margin-top: 2px;">Item: <code>${it.item}</code></div>
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
                        msg = 'Import data pilihan dropdown berhasil! Seluruh data (' + imported + ' data) berhasil diimpor.';
                    } else if (imported > 0) {
                        msg = 'Import data pilihan dropdown selesai: ' + imported + ' data baru disimpan (' + skipped + ' dilewati, ' + failed + ' gagal).';
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
