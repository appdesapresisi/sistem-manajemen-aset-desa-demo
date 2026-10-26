<!DOCTYPE html>
<html lang="id">
<head>
    @include('layouts.head-page-meta', ['title' => 'Ruangan - Sistem Manajemen Aset Desa'])
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
                        Ruangan
                    </h1>
                </div>
                <div class="page-header-actions" style="display: flex; gap: 10px; align-items: center; flex-wrap: wrap;">
                    <div class="sima-dropdown-wrapper" style="position: relative; display: inline-block;">
                        <button type="button" id="btn-export-ruangan" class="btn-sima-secondary btn-sima-pill-white" onclick="toggleExportMenu(this, event)">
                            <i data-feather="download" style="width: 14px; height: 14px;"></i>
                            <span>Ekspor Data</span>
                            <i data-feather="chevron-down" style="width: 12px; height: 12px; opacity: 0.7;"></i>
                        </button>
                        <div class="export-dropdown-menu" style="display: none;">
                            <a class="export-menu-item" id="export-ruangan-excel" href="{{ route('ruangan.export', ['format' => 'excel']) }}" onclick="if(window.showToast) window.showToast('Ekspor data ruangan (Excel) berhasil! Berkas sedang diunduh...', 'success'); this.closest('.export-dropdown-menu').style.display='none';">
                                <i data-feather="file-text" style="width: 14px; height: 14px; color: #10b981;"></i>
                                <span>Ekspor Excel (.xlsx)</span>
                            </a>
                            <hr class="dropdown-divider sima-divider" />
                            <a class="export-menu-item" id="export-ruangan-pdf" href="{{ route('ruangan.export', ['format' => 'pdf']) }}" onclick="if(window.showToast) window.showToast('Ekspor data ruangan (PDF) berhasil! Berkas sedang diunduh...', 'success'); this.closest('.export-dropdown-menu').style.display='none';">
                                <i data-feather="file" style="width: 14px; height: 14px; color: #ef4444;"></i>
                                <span>Ekspor PDF (.pdf)</span>
                            </a>
                        </div>
                    </div>
                    @can('create', \App\Models\Ruangan::class)
                    <button type="button" class="btn-sima-secondary btn-sima-pill-white" onclick="openImportModal()">
                        <i data-feather="upload-cloud" style="width: 14px; height: 14px;"></i>
                        <span>Import CSV</span>
                    </button>
                    <a href="{{ route('ruangan.create') }}" class="btn-sima-primary">
                        <i data-feather="plus" style="width: 14px; height: 14px;"></i>
                        <span>Tambah Ruangan</span>
                    </a>
                    @endcan
                </div>
            </div>

            <!-- 1. STAT CARDS (Prodex 4-Card Summary Bar) -->
            <div class="stat-card-row">
                <!-- Card 1: Total Ruangan -->
                <div class="stat-card-sima is-clickable is-active-filter" id="card-status-all" onclick="filterByCardStatus('all')" title="Klik untuk menampilkan semua ruangan">
                    <div class="stat-header">
                        <span style="font-weight: 600;">TOTAL RUANGAN</span>
                    </div>
                    <div class="stat-number stat-card-value">{{ number_format($stats['totalRuangan'] ?? 0, 0, ',', '.') }}</div>
                    <div class="kpi-trend-wrapper stat-delta">
                        @php 
                            $tRuang = $stats['trends']['total']['percent'] ?? 0;
                            $dRuang = $stats['trends']['total']['diff'] ?? 0;
                        @endphp
                        @if($tRuang > 0 || $dRuang > 0)
                            <span class="stat-delta up">
                                <i data-feather="trending-up" style="width: 12px; height: 12px;"></i>
                                +{{ $tRuang }}% (+{{ number_format($dRuang, 0, ',', '.') }})
                            </span>
                        @elseif($tRuang < 0 || $dRuang < 0)
                            <span class="stat-delta down">
                                <i data-feather="trending-down" style="width: 12px; height: 12px;"></i>
                                {{ $tRuang }}% ({{ number_format($dRuang, 0, ',', '.') }})
                            </span>
                        @else
                            <span class="stat-delta" style="color: var(--color-text-muted);">0% (0)</span>
                        @endif
                        <span class="kpi-trend-label" style="color: var(--color-text-muted); font-size: 11.5px; margin-left: 4px;">vs. bulan lalu</span>
                    </div>
                </div>

                <!-- Card 2: Ruangan Terisi -->
                <div class="stat-card-sima is-clickable" id="card-status-terisi" onclick="filterByCardStatus('terisi')" title="Klik untuk memfilter Ruangan Terisi">
                    <div class="stat-header">
                        <span style="font-weight: 600;">RUANGAN TERISI</span>
                    </div>
                    <div class="stat-number stat-card-value">{{ number_format($stats['ruanganTerisi'] ?? 0, 0, ',', '.') }}</div>
                    <div class="kpi-trend-wrapper stat-delta">
                        @php 
                            $tTerisi = $stats['trends']['ruanganTerisi']['percent'] ?? 0;
                            $dTerisi = $stats['trends']['ruanganTerisi']['diff'] ?? 0;
                        @endphp
                        @if($tTerisi > 0 || $dTerisi > 0)
                            <span class="stat-delta up">
                                <i data-feather="trending-up" style="width: 12px; height: 12px;"></i>
                                +{{ $tTerisi }}% (+{{ number_format($dTerisi, 0, ',', '.') }})
                            </span>
                        @elseif($tTerisi < 0 || $dTerisi < 0)
                            <span class="stat-delta down">
                                <i data-feather="trending-down" style="width: 12px; height: 12px;"></i>
                                {{ $tTerisi }}% ({{ number_format($dTerisi, 0, ',', '.') }})
                            </span>
                        @else
                            <span class="stat-delta" style="color: var(--color-text-muted);">0% (0)</span>
                        @endif
                        <span class="kpi-trend-label" style="color: var(--color-text-muted); font-size: 11.5px; margin-left: 4px;">vs. bulan lalu</span>
                    </div>
                </div>

                <!-- Card 3: Ruangan Kosong -->
                <div class="stat-card-sima is-clickable" id="card-status-kosong" onclick="filterByCardStatus('kosong')" title="Klik untuk memfilter Ruangan Kosong">
                    <div class="stat-header">
                        <span style="font-weight: 600;">RUANGAN KOSONG</span>
                    </div>
                    <div class="stat-number stat-card-value">{{ number_format($stats['ruanganKosong'] ?? 0, 0, ',', '.') }}</div>
                    <div class="kpi-trend-wrapper stat-delta">
                        @php 
                            $tKosong = $stats['trends']['ruanganKosong']['percent'] ?? 0;
                            $dKosong = $stats['trends']['ruanganKosong']['diff'] ?? 0;
                        @endphp
                        @if($tKosong > 0 || $dKosong > 0)
                            <span class="stat-delta up">
                                <i data-feather="trending-up" style="width: 12px; height: 12px;"></i>
                                +{{ $tKosong }}% (+{{ number_format($dKosong, 0, ',', '.') }})
                            </span>
                        @elseif($tKosong < 0 || $dKosong < 0)
                            <span class="stat-delta down">
                                <i data-feather="trending-down" style="width: 12px; height: 12px;"></i>
                                {{ $tKosong }}% ({{ number_format($dKosong, 0, ',', '.') }})
                            </span>
                        @else
                            <span class="stat-delta" style="color: var(--color-text-muted);">0% (0)</span>
                        @endif
                        <span class="kpi-trend-label" style="color: var(--color-text-muted); font-size: 11.5px; margin-left: 4px;">vs. bulan lalu</span>
                    </div>
                </div>

                <!-- Card 4: Total Aset di Ruangan -->
                <div class="stat-card-sima" id="card-total-aset">
                    <div class="stat-header">
                        <span style="font-weight: 600;">TOTAL ASET DI RUANGAN</span>
                    </div>
                    <div class="stat-number stat-card-value">{{ number_format($stats['totalAset'] ?? 0, 0, ',', '.') }}</div>
                    <div class="kpi-trend-wrapper stat-delta">
                        @php 
                            $tTotAset = $stats['trends']['totalAset']['percent'] ?? 0;
                            $dTotAset = $stats['trends']['totalAset']['diff'] ?? 0;
                        @endphp
                        @if($tTotAset > 0 || $dTotAset > 0)
                            <span class="stat-delta up">
                                <i data-feather="trending-up" style="width: 12px; height: 12px;"></i>
                                +{{ $tTotAset }}% (+{{ number_format($dTotAset, 0, ',', '.') }})
                            </span>
                        @elseif($tTotAset < 0 || $dTotAset < 0)
                            <span class="stat-delta down">
                                <i data-feather="trending-down" style="width: 12px; height: 12px;"></i>
                                {{ $tTotAset }}% ({{ number_format($dTotAset, 0, ',', '.') }})
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
                    <span>Semua Ruangan</span>
                    <span class="tab-badge-sima">{{ number_format($stats['totalRuangan'] ?? 0, 0, ',', '.') }}</span>
                </div>
                <div class="tab-item-sima" id="tab-status-terisi" onclick="filterByCardStatus('terisi')">
                    <span>Ruangan Terisi</span>
                    <span class="tab-badge-sima" style="background: var(--color-success-soft); color: var(--color-success);">{{ number_format($stats['ruanganTerisi'] ?? 0, 0, ',', '.') }}</span>
                </div>
                <div class="tab-item-sima" id="tab-status-kosong" onclick="filterByCardStatus('kosong')">
                    <span>Ruangan Kosong</span>
                    <span class="tab-badge-sima" style="background: var(--color-warning-soft); color: var(--color-warning);">{{ number_format($stats['ruanganKosong'] ?? 0, 0, ',', '.') }}</span>
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
                            <input type="text" id="searchInput" class="topbar-search-input" placeholder="Cari nama atau kode ruangan..." oninput="onSearchInput(this)" onkeypress="if(event.key==='Enter'){doSearch();}" value="{{ request('search') }}" />
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
                            <div id="filter-dropdown-menu" class="sima-filter-popover" onclick="event.stopPropagation();" style="display: none; position: absolute; top: calc(100% + 4px); right: 0; min-width: 280px; padding: 20px 24px; z-index: 1050; border-radius: var(--radius-lg); border: 1px solid var(--color-border); box-shadow: var(--shadow-float); background: var(--color-surface); margin: 0;">
                                <div style="font-size: 13px; font-weight: 700; color: var(--color-primary); margin-bottom: 8px;">Filter Status</div>
                                <div style="display: flex; flex-direction: column; gap: 6px; margin-bottom: 12px;">
                                    <label style="cursor: pointer; display: flex; align-items: center; gap: 8px; font-size: 13px; color: var(--color-text);">
                                        <input type="radio" name="statusFilter" value="all" class="status-filter" {{ (!request('status') || request('status') === 'all') ? 'checked' : '' }} style="accent-color: var(--color-primary); cursor: pointer;"> 
                                        <span>Semua Status</span>
                                    </label>
                                    <label style="cursor: pointer; display: flex; align-items: center; gap: 8px; font-size: 13px; color: var(--color-text);">
                                        <input type="radio" name="statusFilter" value="terisi" class="status-filter" {{ request('status') === 'terisi' ? 'checked' : '' }} style="accent-color: var(--color-primary); cursor: pointer;"> 
                                        <span>Ruangan Terisi</span>
                                    </label>
                                    <label style="cursor: pointer; display: flex; align-items: center; gap: 8px; font-size: 13px; color: var(--color-text);">
                                        <input type="radio" name="statusFilter" value="kosong" class="status-filter" {{ request('status') === 'kosong' ? 'checked' : '' }} style="accent-color: var(--color-primary); cursor: pointer;"> 
                                        <span>Ruangan Kosong</span>
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

                <!-- Floating Bulk Action Bar -->
                @can('delete', \App\Models\Ruangan::class)
                <div id="bulk-bar" class="bulk-action-bar-sima">
                    <span class="bulk-count"><span id="bulk-count">0</span> ruangan dipilih</span>
                    <div class="bulk-divider"></div>
                    <button type="button" onclick="bulkDeleteConfirm()" class="btn-sima-danger" style="padding: 5px 12px; font-size: 12.5px;">
                        <i data-feather="trash-2" style="width: 13px; height: 13px;"></i>
                        <span>Hapus Ruangan</span>
                    </button>
                    <button type="button" onclick="clearSelection()" class="btn-sima-icon" style="width: 28px; height: 28px;" title="Batal Pilih">
                        <i data-feather="x" style="width: 13px; height: 13px;"></i>
                    </button>
                </div>
                <form id="bulk-delete-form" method="POST" action="{{ route('ruangan.bulk-destroy') }}" style="display:none;">@csrf</form>
                @endcan

                <!-- Data Table (Clean, Maximum 6 Columns) -->
                <div class="table-sima-wrapper table-loading-wrap">
                    <table class="table-sima">
                        <thead>
                            <tr>
                                <th style="width: 44px; text-align: center;">
                                    @can('delete', \App\Models\Ruangan::class)
                                    <input type="checkbox" id="check-all" onchange="toggleAll(this)" class="sima-checkbox">
                                    @endcan
                                </th>
                                <th style="width: 50px; text-align: center;">No</th>
                                <th>Nama Ruangan</th>
                                <th>Penanggung Jawab</th>
                                <th style="text-align: center;">Jumlah Aset</th>
                                <th style="width: 120px; text-align: center;">Aksi</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($ruangans as $index => $ruangan)
                            <tr>
                                <td style="text-align: center;">
                                    @can('delete', \App\Models\Ruangan::class)
                                    <input type="checkbox" class="row-check sima-checkbox" value="{{ $ruangan->id }}" onchange="updateBulkBar()">
                                    @endcan
                                </td>
                                <td style="text-align: center; color: var(--color-text-muted);">{{ $ruangans->firstItem() + $index }}</td>
                                <td>
                                    <div>
                                        <div style="font-weight: 600; color: var(--color-text);">{{ $ruangan->nama }}</div>
                                        <div style="font-size: 12px; color: var(--color-text-muted); font-variant-numeric: tabular-nums;">
                                            Kode: {{ $ruangan->kode ?? '-' }}
                                        </div>
                                    </div>
                                </td>
                                <td>
                                    @if($ruangan->penanggungJawab)
                                        <span style="font-weight: 500; color: var(--color-text);">{{ $ruangan->penanggungJawab->nama }}</span>
                                    @else
                                        <span style="color: var(--color-text-muted); font-size: 13px;">-</span>
                                    @endif
                                </td>
                                <td style="text-align: center;">
                                    <span style="font-weight: 500; color: var(--color-text); font-variant-numeric: tabular-nums;">
                                        {{ $ruangan->inventaris_count }} aset
                                    </span>
                                </td>
                                <td style="text-align: center;">
                                    <div style="display: inline-flex; align-items: center; justify-content: center; gap: 4px;">
                                        <a href="{{ route('ruangan.show', $ruangan->id) }}" class="btn-sima-icon view" title="Lihat Rincian">
                                            <i data-feather="eye"></i>
                                        </a>
                                        @can('update', $ruangan)
                                        <a href="{{ route('ruangan.edit', $ruangan->id) }}" class="btn-sima-icon edit" title="Edit Ruangan">
                                            <i data-feather="edit-2"></i>
                                        </a>
                                        @endcan
                                        @can('delete', $ruangan)
                                        <form action="{{ route('ruangan.destroy', $ruangan->id) }}" method="POST" style="display:inline;" id="deleteForm{{ $ruangan->id }}">
                                            @csrf
                                            @method('DELETE')
                                            <button type="button" class="btn-sima-icon delete" title="Hapus Ruangan"
                                                    onclick="confirmDelete('{{ addslashes($ruangan->nama) }}', {{ $ruangan->id }})">
                                                <i data-feather="trash-2"></i>
                                            </button>
                                        </form>
                                        @endcan
                                    </div>
                                </td>
                            </tr>
                            @empty
                            <tr>
                                <td colspan="{{ auth()->user()->can('delete', \App\Models\Ruangan::class) ? 6 : 5 }}" style="text-align: center; padding: 48px 20px; color: var(--color-text-muted);">
                                    <div style="display: flex; flex-direction: column; align-items: center; justify-content: center; gap: 8px;">
                                        <i data-feather="inbox" style="width: 42px; height: 42px; stroke-width: 1.5; color: var(--color-border-dark);"></i>
                                        <p style="margin: 0; font-size: 13.5px; font-weight: 500;">Belum ada data ruangan yang sesuai</p>
                                    </div>
                                </td>
                            </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>

                <!-- 4. PAGINATION FOOTER BAR -->
                <div class="pagination-bar pagination-sima-container" style="padding: 32px 20px; border-top: 1px solid var(--color-border); border-bottom-left-radius: var(--radius-lg); border-bottom-right-radius: var(--radius-lg); background: var(--color-surface); flex-wrap: wrap; gap: 14px;">
                    <!-- Per Page Select -->
                    <div class="pagination-left">
                        <div class="page-size-wrap">
                            <span>Tampilkan</span>
                            <select id="perPageSelect" class="per-page-select" onchange="changePerPage(this.value)">
                                <option value="10" {{ ($ruangans->perPage() == 10) ? 'selected' : '' }}>10</option>
                                <option value="20" {{ ($ruangans->perPage() == 20) ? 'selected' : '' }}>20</option>
                                <option value="50" {{ ($ruangans->perPage() == 50) ? 'selected' : '' }}>50</option>
                                <option value="100" {{ ($ruangans->perPage() == 100) ? 'selected' : '' }}>100</option>
                            </select>
                            <span>data</span>
                        </div>
                    </div>

                    <!-- Page Navigation Numbers & Jump -->
                    <div class="pagination-center">
                        <nav class="pagination-sima pagination-nav" id="paginationNav">
                            @if($ruangans->onFirstPage())
                                <span class="page-btn-sima page-btn disabled" style="opacity: 0.5; cursor: not-allowed;" title="Sebelumnya"><i data-feather="chevron-left" style="width: 14px; height: 14px;"></i></span>
                            @else
                                <button type="button" class="page-btn-sima page-btn" onclick="goToPage({{ $ruangans->currentPage() - 1 }})" title="Sebelumnya"><i data-feather="chevron-left" style="width: 14px; height: 14px;"></i></button>
                            @endif
                            @php
                                $currentPage = $ruangans->currentPage();
                                $lastPage    = $ruangans->lastPage();
                                $wStart = max(1, $currentPage - 2);
                                $wEnd   = min($lastPage, $currentPage + 2);
                                if (($wEnd - $wStart + 1) < 5) {
                                    if ($wStart === 1) { $wEnd = min($lastPage, 5); }
                                    else { $wStart = max(1, $wEnd - 4); }
                                }
                            @endphp
                            @for ($p = $wStart; $p <= $wEnd; $p++)
                                @if ($p == $currentPage)
                                    <span class="page-btn-sima page-btn active">{{ $p }}</span>
                                @else
                                    <button type="button" class="page-btn-sima page-btn" onclick="goToPage({{ $p }})">{{ $p }}</button>
                                @endif
                            @endfor
                            @if($ruangans->hasMorePages())
                                <button type="button" class="page-btn-sima page-btn" onclick="goToPage({{ $ruangans->currentPage() + 1 }})" title="Berikutnya"><i data-feather="chevron-right" style="width: 14px; height: 14px;"></i></button>
                            @else
                                <span class="page-btn-sima page-btn disabled" style="opacity: 0.5; cursor: not-allowed;" title="Berikutnya"><i data-feather="chevron-right" style="width: 14px; height: 14px;"></i></span>
                            @endif
                        </nav>
                        <div class="page-jump-wrap">
                            <label for="pageJumpInput" style="color: var(--color-text-muted);">Ke hal.</label>
                            <input id="pageJumpInput" type="number" class="page-jump-input form-control-sima" min="1" max="{{ $ruangans->lastPage() }}" value="{{ $ruangans->currentPage() }}">
                        </div>
                    </div>

                    <div class="pagination-right" id="paginationRangeInfo" style="font-size: 13px; color: var(--color-text-muted); font-variant-numeric: tabular-nums;">
                        Menampilkan <strong>{{ $ruangans->firstItem() ?? 0 }}-{{ $ruangans->lastItem() ?? 0 }}</strong> dari <strong>{{ $ruangans->total() }}</strong> data
                    </div>
                </div>

            </div>
        </div>
    </div>

    @include('layouts.footer-block')
    @include('layouts.footer-js')

    <script>
        // Page jump
        (function() {
            const jumpInput = document.getElementById('pageJumpInput');
            if (!jumpInput) return;
            jumpInput.addEventListener('keydown', function(e) {
                if (e.key !== 'Enter') return;
                const lastPage = parseInt(this.max || 1, 10);
                const val = parseInt(this.value, 10);
                if (isNaN(val) || val < 1 || val > lastPage) return;
                goToPage(val);
            });
            jumpInput.addEventListener('blur', function() {
                const lastPage = parseInt(this.max || 1, 10);
                const val = parseInt(this.value, 10);
                if (isNaN(val) || val < 1 || val > lastPage) return;
                goToPage(val);
            });
        })();

        function confirmDelete(nama, id) {
            AppModal.confirmDelete(nama, function() {
                document.getElementById('deleteForm' + id).submit();
            });
        }

        // Bulk select
        function toggleAll(src) {
            document.querySelectorAll('.row-check').forEach(cb => { cb.checked = src.checked; });
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
            }
            const cnt = document.getElementById('bulk-count');
            if (cnt) cnt.textContent = checked.length;
            if (typeof feather !== 'undefined') feather.replace();
        }
        function clearSelection() {
            document.querySelectorAll('.row-check, #check-all').forEach(cb => { cb.checked = false; });
            const bar = document.getElementById('bulk-bar');
            if (bar) bar.classList.remove('show');
        }
        function bulkDeleteConfirm() {
            const checked = Array.from(document.querySelectorAll('.row-check:checked'));
            if (!checked.length) return;
            const label = checked.length + ' ruangan terpilih';
            AppModal.confirmDelete(label, function() {
                const form = document.getElementById('bulk-delete-form');
                form.querySelectorAll('input[name="ids[]"]').forEach(el => el.remove());
                checked.forEach(cb => {
                    const input = document.createElement('input');
                    input.type = 'hidden'; input.name = 'ids[]'; input.value = cb.value;
                    form.appendChild(input);
                });
                form.submit();
            });
        }

        // ===== SEARCH & FILTER =====
        let _searchTimer = null;
        let appliedStatus = 'all';
        let appliedSort   = 'terbaru';

        function toggleSearchClear(input) {
            const clearBtn = document.getElementById('clear-search-btn');
            if (clearBtn) clearBtn.style.display = input.value ? 'inline-block' : 'none';
        }

        function onSearchInput(input) {
            toggleSearchClear(input);
            clearTimeout(_searchTimer);
            _searchTimer = setTimeout(function() { fetchRuangan(false, 1); }, 400);
        }

        function doSearch() {
            fetchRuangan(false, 1);
        }

        function clearSearch() {
            const input = document.getElementById('searchInput');
            if (input) input.value = '';
            toggleSearchClear(input || { value: '' });
            fetchRuangan(false, 1);
        }

        function changePerPage(perPage) {
            fetchRuangan(false, 1);
        }

        function goToPage(page) {
            fetchRuangan(false, page);
        }

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

            fetchRuangan(false, 1);
        }

        function fetchRuangan(isAutoRefresh = false, pageOverride = null) {
            const q             = (document.getElementById('searchInput')?.value || '').trim();
            const perPageSelect = document.getElementById('perPageSelect');
            const perPageValue  = perPageSelect ? perPageSelect.value : '10';

            const params = new URLSearchParams();
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
                const newUrl = window.location.pathname + (queryString ? '?' + queryString : '');
                history.replaceState(null, '', newUrl);

                try {
                    if (queryString) {
                        sessionStorage.setItem('ruangan_filter_state', queryString);
                    } else {
                        sessionStorage.removeItem('ruangan_filter_state');
                    }
                } catch(e) {}
            }

            const wrap = document.querySelector('.table-loading-wrap');
            if (!isAutoRefresh && wrap) wrap.classList.add('is-loading');

            fetch('{{ route('ruangan.index') }}?' + params.toString(), {
                headers: { 'X-Requested-With': 'XMLHttpRequest', 'Accept': 'application/json' }
            })
            .then(r => r.json())
            .then(data => {
                if (data.stats) updateStats(data.stats);
                updateTable(data.ruangans, data.pagination);
                updatePaginationUI(data.pagination);
                if (typeof feather !== 'undefined') feather.replace();
            })
            .catch(err => console.error(err))
            .finally(() => { if (wrap) wrap.classList.remove('is-loading'); });
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

            // Stat Cards
            const cardAll = document.getElementById('card-status-all');
            if (cardAll) {
                const el = cardAll.querySelector('.stat-number');
                if (el && stats.totalRuangan !== undefined) el.textContent = Number(stats.totalRuangan || 0).toLocaleString('id-ID');
                if (stats.trends && stats.trends.total) updateCardTrend(cardAll, stats.trends.total);
            }
            const cardTerisi = document.getElementById('card-status-terisi');
            if (cardTerisi) {
                const el = cardTerisi.querySelector('.stat-number');
                if (el && stats.ruanganTerisi !== undefined) el.textContent = Number(stats.ruanganTerisi || 0).toLocaleString('id-ID');
                if (stats.trends && stats.trends.ruanganTerisi) updateCardTrend(cardTerisi, stats.trends.ruanganTerisi);
            }
            const cardKosong = document.getElementById('card-status-kosong');
            if (cardKosong) {
                const el = cardKosong.querySelector('.stat-number');
                if (el && stats.ruanganKosong !== undefined) el.textContent = Number(stats.ruanganKosong || 0).toLocaleString('id-ID');
                if (stats.trends && stats.trends.ruanganKosong) updateCardTrend(cardKosong, stats.trends.ruanganKosong);
            }
            const cardTotalAset = document.getElementById('card-total-aset');
            if (cardTotalAset) {
                const el = cardTotalAset.querySelector('.stat-number');
                if (el && stats.totalAset !== undefined) el.textContent = Number(stats.totalAset || 0).toLocaleString('id-ID');
                if (stats.trends && stats.trends.totalAset) updateCardTrend(cardTotalAset, stats.trends.totalAset);
            }

            // Tab Badges
            const tabAllBadge = document.querySelector('#tab-status-all .tab-badge-sima');
            if (tabAllBadge && stats.totalRuangan !== undefined) tabAllBadge.textContent = Number(stats.totalRuangan || 0).toLocaleString('id-ID');

            const tabTerisiBadge = document.querySelector('#tab-status-terisi .tab-badge-sima');
            if (tabTerisiBadge && stats.ruanganTerisi !== undefined) tabTerisiBadge.textContent = Number(stats.ruanganTerisi || 0).toLocaleString('id-ID');

            const tabKosongBadge = document.querySelector('#tab-status-kosong .tab-badge-sima');
            if (tabKosongBadge && stats.ruanganKosong !== undefined) tabKosongBadge.textContent = Number(stats.ruanganKosong || 0).toLocaleString('id-ID');
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

            // Sync visual active state on cards & tabs
            document.querySelectorAll('.stat-card-sima.is-clickable').forEach(card => card.classList.remove('is-active-filter'));
            const targetCard = document.getElementById('card-status-' + appliedStatus);
            if (targetCard) targetCard.classList.add('is-active-filter');

            document.querySelectorAll('.tabs-sima .tab-item-sima').forEach(tab => tab.classList.remove('active'));
            const targetTab = document.getElementById('tab-status-' + appliedStatus);
            if (targetTab) targetTab.classList.add('active');

            fetchRuangan(false, 1);
            document.querySelectorAll('.dropdown-menu.show').forEach(el => el.classList.remove('show'));
            document.querySelectorAll('.dropdown.show').forEach(el => el.classList.remove('show'));
        }

        function resetFilters() {
            const searchInput = document.getElementById('searchInput');
            if (searchInput) searchInput.value = '';
            toggleSearchClear(searchInput || { value: '' });

            const defaultStatus = document.querySelector('.status-filter[value="all"]');
            if (defaultStatus) defaultStatus.checked = true;

            const defaultSort = document.querySelector('.sort-filter[value="terbaru"]');
            if (defaultSort) defaultSort.checked = true;

            appliedStatus = 'all';
            appliedSort   = 'terbaru';

            // Reset visual states
            document.querySelectorAll('.stat-card-sima.is-clickable').forEach(card => card.classList.remove('is-active-filter'));
            const targetCard = document.getElementById('card-status-all');
            if (targetCard) targetCard.classList.add('is-active-filter');

            document.querySelectorAll('.tabs-sima .tab-item-sima').forEach(tab => tab.classList.remove('active'));
            const targetTab = document.getElementById('tab-status-all');
            if (targetTab) targetTab.classList.add('active');

            fetchRuangan(false, 1);
            document.querySelectorAll('.dropdown-menu.show').forEach(el => el.classList.remove('show'));
            document.querySelectorAll('.dropdown.show').forEach(el => el.classList.remove('show'));
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
                html += `<span class="page-btn-sima" style="opacity: 0.5; cursor: not-allowed;" title="Sebelumnya"><i data-feather="chevron-left" style="width: 14px; height: 14px;"></i></span>`;
            } else {
                html += `<button type="button" class="page-btn-sima" onclick="goToPage(${currentPage - 1})" title="Sebelumnya"><i data-feather="chevron-left" style="width: 14px; height: 14px;"></i></button>`;
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
                    html += `<span class="page-btn-sima active">${p}</span>`;
                } else {
                    html += `<button type="button" class="page-btn-sima" onclick="goToPage(${p})">${p}</button>`;
                }
            }

            if (currentPage >= lastPage) {
                html += `<span class="page-btn-sima" style="opacity: 0.5; cursor: not-allowed;" title="Berikutnya"><i data-feather="chevron-right" style="width: 14px; height: 14px;"></i></span>`;
            } else {
                html += `<button type="button" class="page-btn-sima" onclick="goToPage(${currentPage + 1})" title="Berikutnya"><i data-feather="chevron-right" style="width: 14px; height: 14px;"></i></button>`;
            }

            nav.innerHTML = html;
        }

        function updateTable(items, pagination) {
            clearSelection();
            const tbody = document.querySelector('.table-sima tbody');
            if (!tbody) return;
            const firstItem = pagination ? pagination.firstItem : 1;

            if (!items || items.length === 0) {
                tbody.innerHTML = `<tr><td colspan="{{ auth()->user()->can('delete', \App\Models\Ruangan::class) ? 6 : 5 }}" style="text-align:center;padding:48px 20px;color:var(--color-text-muted);">
                    <div style="display:flex;flex-direction:column;align-items:center;justify-content:center;gap:8px;">
                        <i data-feather="inbox" style="width:40px;height:40px;color:var(--color-text-muted);stroke:var(--color-text-muted);"></i>
                        <span style="font-size:13.5px;">Belum ada data ruangan terdaftar</span>
                    </div></td></tr>`;
                return;
            }

            let html = '';
            items.forEach((item, idx) => {
                const no   = firstItem + idx;
                const kode = item.kode ?? '-';
                const pj   = item.penanggung_jawab
                    ? `<span style="font-weight:500;color:var(--color-text);">${item.penanggung_jawab}</span>`
                    : `<span style="color:var(--color-text-muted);font-size:13px;">-</span>`;
                html += `<tr>
                    <td style="text-align: center;">
                        @can('delete', \App\Models\Ruangan::class)
                        <input type="checkbox" class="row-check sima-checkbox" value="${item.id}" onchange="updateBulkBar()">
                        @endcan
                    </td>
                    <td style="text-align: center; color: var(--color-text-muted);">${no}</td>
                    <td>
                        <div>
                            <div style="font-weight: 600; color: var(--color-text);">${item.nama}</div>
                            <div style="font-size: 12px; color: var(--color-text-muted); font-variant-numeric: tabular-nums;">Kode: ${kode}</div>
                        </div>
                    </td>
                    <td>${pj}</td>
                    <td style="text-align: center;">
                        <span style="font-weight: 500; color: var(--color-text); font-variant-numeric: tabular-nums;">${item.inventaris_count} aset</span>
                    </td>
                    <td style="text-align: center;">
                        <div style="display: inline-flex; align-items: center; justify-content: center; gap: 4px;">
                            <a href="{{ url('ruangan') }}/${item.id}" class="btn-sima-icon view" title="Lihat Rincian"><i data-feather="eye"></i></a>
                            @can('update', \App\Models\Ruangan::class)
                            <a href="{{ url('ruangan') }}/${item.id}/edit" class="btn-sima-icon edit" title="Edit Ruangan"><i data-feather="edit-2"></i></a>
                            @endcan
                            @can('delete', \App\Models\Ruangan::class)
                            <button type="button" class="btn-sima-icon delete" title="Hapus Ruangan" data-nama="${item.nama.replace(/&/g, '&amp;').replace(/"/g, '&quot;')}" onclick="confirmDelete(this.getAttribute('data-nama'), ${item.id})"><i data-feather="trash-2"></i></button>
                            @endcan
                        </div>
                    </td>
                </tr>`;
            });
            tbody.innerHTML = html;
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
            try { sessionStorage.removeItem('ruangan_filter_state'); } catch(e) {}
            @endif

            let isReturning = false;
            try {
                if (sessionStorage.getItem('ruangan_returning') === '1') {
                    isReturning = true;
                    sessionStorage.removeItem('ruangan_returning');
                }
            } catch(e) {}

            const ref = document.referrer || '';
            if (ref.includes('/ruangan/') || ref.includes('/create') || ref.includes('/edit')) {
                isReturning = true;
            }
            if (navType === 'back_forward') {
                isReturning = true;
            }

            // KASUS 1: Manual Refresh (F5 / Reload) saat di index
            if (navType === 'reload' && !isReturning && !window._simaReloadResetHandled) {
                window._simaReloadResetHandled = true;
                try { sessionStorage.removeItem('ruangan_filter_state'); } catch(e) {}
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
                fetchRuangan(true, 1);
                return;
            }

            // KASUS 2: Navigasi biasa tanpa query
            if (!isReturning && navType === 'navigate' && !window.location.search) {
                try { sessionStorage.removeItem('ruangan_filter_state'); } catch(e) {}
                return;
            }

            // KASUS 3: Restorasi query
            let currentSearch = window.location.search;
            let hasUrlQuery = (currentSearch && currentSearch.length > 1);

            if (!hasUrlQuery) {
                try {
                    const saved = sessionStorage.getItem('ruangan_filter_state');
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

            // Sync visual states on card and tabs
            document.querySelectorAll('.stat-card-sima.is-clickable').forEach(card => card.classList.remove('is-active-filter'));
            const targetCard = document.getElementById('card-status-' + appliedStatus);
            if (targetCard) targetCard.classList.add('is-active-filter');

            document.querySelectorAll('.tabs-sima .tab-item-sima').forEach(tab => tab.classList.remove('active'));
            const targetTab = document.getElementById('tab-status-' + appliedStatus);
            if (targetTab) targetTab.classList.add('active');

            const q = urlParams.get('search') || '';
            const input = document.getElementById('searchInput');
            if (input) { input.value = q; toggleSearchClear(input); }

            const perPageParam = urlParams.get('per_page');
            const perPageSelect = document.getElementById('perPageSelect');
            if (perPageSelect && perPageParam) {
                perPageSelect.value = perPageParam;
            }

            const page = parseInt(urlParams.get('page')) || 1;
            fetchRuangan(true, page);
        }

        document.addEventListener('DOMContentLoaded', function() {
            restoreFilterState();

            const autoRefreshMs = 15000;
            setInterval(function () {
                if (document.hidden) return;
                if (document.querySelectorAll('.row-check:checked, #check-all:checked').length > 0) return;
                if (document.querySelector('.modal.show, [id*="modal"][style*="display: flex"], [id*="modal"][style*="display: block"]')) return;
                if (document.querySelector('.dropdown-menu.show, .dropdown.show')) return;
                fetchRuangan(true);
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

    <!-- MODAL IMPORT EXCEL RUANGAN (SIMA PREMIER THEME) -->
    <div id="import-modal" class="modal-sima" style="display:none; position:fixed; inset:0; background:rgba(31,58,42,0.45); z-index:9999; align-items:center; justify-content:center; backdrop-filter: blur(4px); padding: 16px;">
        <div class="modal-sima-box" style="width: 90%; max-width: 480px; display: flex; flex-direction: column; overflow: hidden;">
            <!-- Header -->
            <div class="modal-sima-header" style="position: relative; border-bottom: none !important; padding: 20px 24px 16px 24px; display: flex; align-items: center; justify-content: center;">
                <div style="font-size: 16px; font-weight: 700; color: var(--color-text); text-align: center; width: 100%;">Import Data Ruangan</div>
            </div>

            <!-- Body -->
            <div class="modal-sima-body" style="padding: 20px 24px;">
                <!-- Unduh Template Bar -->
                <div id="import-template-bar" style="display: flex; flex-direction: column; align-items: left; justify-content: left; text-align: left; gap: 10px; padding-bottom: 16px; border-bottom: 1px dashed var(--color-border); margin-bottom: 18px;">
                    <div style="text-align: left;">
                        <div style="font-size: 13.5px; font-weight: 600; color: var(--color-text);">Template Format CSV</div>
                        <div style="font-size: 12px; color: var(--color-text-muted); margin-top: 2px;">Sudah siap dengan baris contoh dan format kolom baku.</div>
                    </div>
                    <a href="{{ route('ruangan.template.download') }}" onclick="if(window.showToast) window.showToast('Template import ruangan (CSV) berhasil diunduh! Berkas sedang diproses...', 'success');" class="btn-sima-primary" style="align-self: flex-start; border-radius: var(--radius-full); padding: 7px 18px; font-size: 12.5px; text-decoration: none; flex-shrink: 0; display: inline-flex; align-items: center; gap: 6px;">
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
                            Ruangan dengan <strong>Nama Ruangan</strong> atau <strong>Kode Ruangan</strong> yang sudah terdaftar di sistem akan otomatis dilewati (skip) agar data yang sudah ada tidak tertimpa.
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
                                    <span style="font-size: 11.5px; color: var(--color-text-muted); font-weight: 500;">ruangan</span>
                                </div>
                                <div style="font-size: 11px; color: var(--color-text-muted); margin-top: 2px;">Data baru masuk</div>
                            </div>

                            <!-- Dilewati (Kuning) -->
                            <div style="text-align: center; padding: 0 8px; border-right: 1px dashed var(--color-border);">
                                <div style="font-size: 11px; font-weight: 600; color: var(--color-warning); text-transform: uppercase; letter-spacing: 0.5px;">Dilewati</div>
                                <div style="margin-top: 4px; display: flex; align-items: baseline; justify-content: center; gap: 4px;">
                                    <span id="res-count-skipped" style="font-size: 22px; font-weight: 700; color: var(--color-warning); font-variant-numeric: tabular-nums;">0</span>
                                    <span style="font-size: 11.5px; color: var(--color-text-muted); font-weight: 500;">ruangan</span>
                                </div>
                                <div style="font-size: 11px; color: var(--color-text-muted); margin-top: 2px;">Nama/Kode sama</div>
                            </div>

                            <!-- Gagal (Merah) -->
                            <div style="text-align: center; padding: 0 8px;">
                                <div style="font-size: 11px; font-weight: 600; color: var(--color-danger); text-transform: uppercase; letter-spacing: 0.5px;">Gagal</div>
                                <div style="margin-top: 4px; display: flex; align-items: baseline; justify-content: center; gap: 4px;">
                                    <span id="res-count-failed" style="font-size: 22px; font-weight: 700; color: var(--color-danger); font-variant-numeric: tabular-nums;">0</span>
                                    <span style="font-size: 11.5px; color: var(--color-text-muted); font-weight: 500;">ruangan</span>
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
                                            <th style="padding: 8px 12px; text-align: left; font-size: 11px; font-weight: 600; color: var(--color-text-muted); text-transform: uppercase; letter-spacing: 0.5px;">Identitas Ruangan</th>
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
        // FITUR IMPORT EXCEL MASTER RUANGAN
        // ==========================================
        let importStageTimer = null;
        let importStatsAccumulator = { imported: 0, skipped: 0, failed: 0 };
        const importStages = [
            'Membaca dan memvalidasi baris data...',
            'Memeriksa duplikasi Nama & Kode Ruangan...',
            'Menyimpan master data ruangan desa...'
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

            fetch('{{ route("ruangan.import") }}', {
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
                            ? `${importedCount} ruangan baru berhasil disimpan, dan ${skippedCount} ruangan yang sudah terdaftar dilewati.`
                            : `${importedCount} data ruangan baru berhasil disimpan ke dalam sistem.`;
                    } else if (importedCount === 0 && skippedCount > 0 && failedCount === 0) {
                        titleEl.textContent = 'Data Sudah Terdaftar (Dilewati Otomatis)';
                        descEl.textContent = `Seluruh ${skippedCount} ruangan pada berkas Excel sudah ada di sistem. Baris dilewati agar data eksisting tidak tertimpa.`;
                    } else if (failedCount > 0) {
                        titleEl.textContent = 'Impor Selesai dengan Catatan';
                        descEl.textContent = `${importedCount} data baru masuk, ${skippedCount} dilewati, dan ${failedCount} baris memerlukan koreksi format.`;
                    } else {
                        titleEl.textContent = 'Pemeriksaan Berkas Selesai';
                        descEl.textContent = 'Tidak ada baris data ruangan baru yang ditambahkan ke sistem.';
                    }

                    const tbody = document.getElementById('res-details-body');
                    tbody.innerHTML = '';
                    const issues = [];

                    (data.failed_rows || []).forEach(r => {
                        issues.push({
                            row: r.row,
                            status: '<span class="status-pill status-pill-danger" style="font-size: 11px; padding: 2px 8px; font-weight: 600;">Gagal</span>',
                            nama: r.nama || 'Ruangan',
                            kode: r.kode || '-',
                            reason: r.reason || 'Format atau kelengkapan data tidak valid'
                        });
                    });

                    (data.skipped_rows || []).forEach(r => {
                        issues.push({
                            row: r.row,
                            status: '<span class="status-pill status-pill-warning" style="font-size: 11px; padding: 2px 8px; font-weight: 600;">Dilewati</span>',
                            nama: r.nama || 'Ruangan',
                            kode: r.kode || '-',
                            reason: r.reason || 'Nama atau Kode Ruangan sudah terdaftar'
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
                                    <div style="font-size: 11px; color: var(--color-text-muted); margin-top: 2px;">Kode: <code>${it.kode}</code></div>
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
                        msg = 'Import data ruangan berhasil! Seluruh data (' + imported + ' data) berhasil diimpor.';
                    } else if (imported > 0) {
                        msg = 'Import data ruangan selesai: ' + imported + ' data baru disimpan (' + skipped + ' dilewati, ' + failed + ' gagal).';
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
