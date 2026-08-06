<!DOCTYPE html>
<html lang="en">
<head>
    @include('layouts.head-page-meta', ['title' => 'Ruangan - Sistem Manajemen Aset Desa Demo'])
    @include('layouts.head-css')
    <style>
        @keyframes gradientShift {
            0% { background-position: 0% 50%; }
            50% { background-position: 100% 50%; }
            100% { background-position: 0% 50%; }
        }
        .page-header-card {
            background: linear-gradient(135deg, #064635 0%, #1c5240 50%, #0b2d21 100%);
            background-size: 200% 200%;
            animation: gradientShift 8s ease infinite;
            border-radius: 20px; padding: 32px 40px; margin-bottom: 28px;
            position: relative; overflow: hidden;
        }
        .page-header-card::before {
            content: ''; position: absolute; top: -50%; right: -20%;
            width: 400px; height: 400px;
            background: radial-gradient(circle, rgba(255,255,255,0.1) 0%, transparent 60%);
            pointer-events: none;
        }
        .page-header-card h1 { color:#fff; font-size:28px; font-weight:700; margin:0 0 8px; position:relative; z-index:1; }
        .page-header-card p  { color:rgba(255,255,255,0.8); font-size:15px; margin:0; position:relative; z-index:1; }
        .stats-grid { display:grid; grid-template-columns:repeat(4,1fr); gap:20px; margin-bottom:28px; }
        @media(max-width:1200px){ .stats-grid { grid-template-columns:repeat(2,1fr); } }
        @media(max-width:640px){ .stats-grid { grid-template-columns:1fr; } }
        .stat-card {
            background:#fff; border-radius:16px; padding:18px 24px;
            display:flex; flex-direction:column; justify-content:center;
            box-shadow:0 4px 20px rgba(0,0,0,0.04); border:1px solid rgba(0,0,0,0.04);
            transition:all 0.3s ease; position:relative; overflow:hidden;
        }
        .stat-card:hover { transform:translateY(-4px); box-shadow:0 12px 30px rgba(0,0,0,0.08); }
        .stat-card-label { font-size:12px; font-weight:700; color:#064635; text-transform:uppercase; letter-spacing:0.5px; margin-bottom:4px; }
        .stat-card-value { font-size:24px; font-weight:800; color:#1f2937; line-height:1.2; }
        .kpi-trend-wrapper { display:flex; align-items:center; gap:6px; margin-top:8px; }
        .kpi-trend-pill { display:inline-flex; align-items:center; gap:3px; padding:2px 7px; border-radius:12px; font-size:11px; font-weight:700; line-height:1.3; }
        .kpi-trend-pill.up { background:#d1fae5; color:#047857; }
        .kpi-trend-pill.down { background:#fee2e2; color:#b91c1c; }
        .kpi-trend-pill.neutral { background:#f1f5f9; color:#64748b; }
        .kpi-trend-label { font-size:11.5px; color:#94a3b8; font-weight:500; }
        .data-card { background:#fff; border-radius:20px; box-shadow:0 4px 24px rgba(0,0,0,0.06); border:1px solid rgba(0,0,0,0.04); }
        .data-card-header { padding:24px 28px; border-bottom:1px solid #f3f4f6; display:flex; align-items:center; justify-content:space-between; flex-wrap:wrap; gap:20px; }
        .data-card-title { display:flex; align-items:center; gap:16px; }
        .data-card-icon { width:48px; height:48px; background:linear-gradient(135deg,#064635 0%,#1c5240 100%); border-radius:12px; display:flex; align-items:center; justify-content:center; }
        .data-card-icon svg, .data-card-icon i { width:22px; height:22px; color:#fff !important; stroke:#fff !important; }
        .data-card-title h5 { font-size:18px; font-weight:600; color:#1f2937; margin:0; }
        .data-card-title span { font-size:13px; color:#9ca3af; }
        .controls-bar { display:flex; align-items:center; gap:12px; flex-wrap:wrap; }
        .search-box { display:flex; align-items:center; background:#ffffff; border-radius:12px; padding:0 16px; border:1px solid #d1d5db; transition:all 0.2s; }
        .search-box:focus-within { border-color:#064635; box-shadow:0 0 0 3px rgba(6, 70, 53,0.1); }
        .search-box input { border:none; background:transparent; padding:12px 0; font-size:14px; width:200px; outline:none; }
        .search-box button { border:none; background:transparent; padding:0; cursor:pointer; color:#9ca3af; }
        .search-box button i { width:18px; height:18px; }
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

        .add-btn { display:flex; align-items:center; gap:8px; padding:12px 20px; background:linear-gradient(135deg,#064635 0%,#1c5240 100%); color:#fff; border-radius:12px; font-size:14px; font-weight:600; text-decoration:none; transition:all 0.3s; box-shadow:0 4px 12px rgba(6, 70, 53,0.3); }
        .add-btn:hover { transform:translateY(-2px); box-shadow:0 6px 20px rgba(6, 70, 53,0.4); color:#fff; }
        .add-btn i { width:18px; height:18px; }
        .data-card-body { padding:0; }
        .modern-table { width:100%; border-collapse:collapse; }
        .table-loading-wrap { position:relative; }
        .table-loading-wrap::after { content:''; display:none; position:absolute; inset:0; background:rgba(255,255,255,0.7); border-radius:8px; z-index:10; }
        .table-loading-wrap.is-loading::after { display:block; }
        .table-loading-wrap.is-loading::before { content:''; display:block; position:absolute; top:50%; left:50%; transform:translate(-50%,-50%); width:36px; height:36px; border:3px solid #e5e7eb; border-top-color:#064635; border-radius:50%; animation:tbl-spin 0.7s linear infinite; z-index:11; }
        @keyframes tbl-spin { to { transform:translate(-50%,-50%) rotate(360deg); } }
        .modern-table thead th { background:#f4faf5; padding:16px 20px; text-align:left; font-size:12px; font-weight:600; color:#6b7280; text-transform:uppercase; letter-spacing:0.5px; border-bottom:1px solid #f3f4f6; }
        .modern-table tbody tr { transition:all 0.2s; }
        .modern-table tbody tr:hover { background:#f4faf5; }
        .modern-table tbody td { padding:16px 20px; border-bottom:1px solid #f3f4f6; font-size:14px; color:#374151; vertical-align:middle; }
        .modern-table tbody tr:last-child td { border-bottom:none; }
        .ruangan-cell { display:flex; align-items:center; }
        .ruangan-info h6 { font-size:14px; font-weight:600; color:#1f2937; margin:0 0 2px; }
        .ruangan-info span { font-size:12px; color:#9ca3af; }
        .code-badge { font-size:13px; font-weight:600; color:#374151; }
        .aset-count-badge { display:inline-flex; align-items:center; gap:6px; padding:5px 12px; background:#dbeafe; color:#1e40af; border-radius:20px; font-size:12px; font-weight:600; }
        .action-btns { display:flex; align-items:center; gap:8px; }
        .action-btn { width:36px; height:36px; border-radius:10px; border:none; display:flex; align-items:center; justify-content:center; cursor:pointer; transition:all 0.2s; }
        .action-btn i { width:16px; height:16px; }
        .action-btn.view   { background:#dbeafe; color:#2563eb; }
        .action-btn.view:hover   { background:#3b82f6; color:#fff; }
        .action-btn.edit   { background:#fef3c7; color:#d97706; }
        .action-btn.edit:hover   { background:#f59e0b; color:#fff; }
        .action-btn.delete { background:#fee2e2; color:#dc2626; }
        .action-btn.delete:hover { background:#ef4444; color:#fff; }
        .pagination-bar { display:flex; align-items:center; justify-content:space-between; padding:16px 24px; border-top:1px solid #f3f4f6; gap:16px; flex-wrap:wrap; }
        .pagination-left { display:flex; align-items:center; gap:12px; }
        .page-size-wrap { display:flex; align-items:center; gap:8px; font-size:14px; color:#6b7280; }
        .per-page-select { padding:6px 32px 6px 12px; font-size:14px; font-weight:500; color:#1f2937; background-color:#ffffff; border:1px solid #d1d5db; border-radius:8px; cursor:pointer; outline:none; transition:all 0.2s ease; appearance:none; background-image:url("data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' fill='none' viewBox='0 0 24 24' stroke='%236b7280'%3E%3Cpath stroke-linecap='round' stroke-linejoin='round' stroke-width='2' d='M19 9l-7 7-7-7'%3E%3C/path%3E%3C/svg%3E"); background-repeat:no-repeat; background-position:right 10px center; background-size:14px; }
        .per-page-select:hover { border-color:#9ca3af; }
        .per-page-select:focus { border-color:#064635; }
        .pagination-center { display:flex; align-items:center; gap:12px; justify-content:center; flex-wrap:wrap; }
        .pagination-right { display:flex; align-items:center; gap:5px; font-size:14px; color:#6b7280; white-space:nowrap; }
        .pagination-right strong { color:#064635; font-weight:600; margin:0 3px; }
        .pagination-nav { display:flex; align-items:center; justify-content:center; gap:6px; }
        .page-btn { min-width:36px; height:36px; padding:0 10px; border:none; border-radius:10px; background:#f3f4f6; color:#6b7280; font-size:14px; font-weight:500; display:flex; align-items:center; justify-content:center; cursor:pointer; transition:all 0.2s; text-decoration:none; }
        .page-btn:hover { background:#e5e7eb; color:#374151; }
        .page-btn.active { background:linear-gradient(135deg,#064635 0%,#1c5240 100%); color:#fff; }
        .page-btn i { width:16px; height:16px; }
        .page-jump-wrap { display:flex; align-items:center; gap:6px; margin-left:6px; }
        .page-jump-wrap label { font-size:13px; color:#6b7280; white-space:nowrap; }
        .page-jump-input { width:56px; height:34px; border:1px solid #d1d5db; border-radius:6px; text-align:center; font-size:13px; color:#374151; outline:none; transition:border-color .15s; }
        .page-jump-input:focus { border-color:#064635; box-shadow:0 0 0 2px rgba(6,70,53,.15); }
        @media(max-width:575px){ .pagination-bar { flex-direction:column; gap:10px; padding:14px 16px; } .pagination-nav { flex-wrap:wrap; justify-content:center; } }
    </style>
</head>
<body>
    @include('layouts.notification')
    @include('layouts.modals')
    @include('layouts.layout-vertical')
    <div class="pc-container">
        <div class="pc-content">
            @include('layouts.breadcrumb', ['breadcrumbItemHome'=>'Master Data','breadcrumbItem'=>'Ruangan','breadcrumbItemActive'=>'List Ruangan'])

            <div class="page-header-card">
                <h1>Ruangan</h1>
                <p>Kelola data ruangan dan pantau aset yang berada di setiap ruangan.</p>
            </div>

            <div class="stats-grid">
                <div class="stat-card">
                    <div class="stat-card-label">TOTAL RUANGAN</div>
                    <div class="stat-card-value">{{ number_format($stats['totalRuangan'] ?? 0, 0, ',', '.') }}</div>
                    <div class="kpi-trend-wrapper">
                        <span class="kpi-trend-pill neutral">0%</span>
                        <span class="kpi-trend-label">vs. kemarin</span>
                    </div>
                </div>
                <div class="stat-card">
                    <div class="stat-card-label">TOTAL ASET DI RUANGAN</div>
                    <div class="stat-card-value">{{ number_format($stats['totalAset'] ?? 0, 0, ',', '.') }}</div>
                    <div class="kpi-trend-wrapper">
                        <span class="kpi-trend-pill neutral">0%</span>
                        <span class="kpi-trend-label">vs. kemarin</span>
                    </div>
                </div>
                <div class="stat-card">
                    <div class="stat-card-label">RUANGAN TERISI</div>
                    <div class="stat-card-value">{{ number_format($stats['ruanganTerisi'] ?? 0, 0, ',', '.') }}</div>
                    <div class="kpi-trend-wrapper">
                        <span class="kpi-trend-pill neutral">0%</span>
                        <span class="kpi-trend-label">vs. kemarin</span>
                    </div>
                </div>
                <div class="stat-card">
                    <div class="stat-card-label">RUANGAN KOSONG</div>
                    <div class="stat-card-value">{{ number_format($stats['ruanganKosong'] ?? 0, 0, ',', '.') }}</div>
                    <div class="kpi-trend-wrapper">
                        <span class="kpi-trend-pill neutral">0%</span>
                        <span class="kpi-trend-label">vs. kemarin</span>
                    </div>
                </div>
            </div>

            <div class="data-card">
                <div class="data-card-header">
                    <div class="data-card-title">
                        <div>
                            <h5>Daftar Ruangan</h5>
                            <span>Total {{ $stats['totalRuangan'] }} ruangan terdaftar</span>
                        </div>
                    </div>
                    <div class="controls-bar">
                        <div class="search-box">
                            <input type="text" id="searchInput" placeholder="Cari ruangan..."
                                   oninput="onSearchInput(this)" value="{{ request('search') }}" />
                            <button id="search-icon-btn" onclick="doSearch()" @if(request('search')) style="display:none" @endif>
                                <i data-feather="search"></i>
                            </button>
                            <button id="clear-search-btn" onclick="clearSearch()" style="color:#ef4444;{{ request('search') ? '' : 'display:none;' }}">
                                <i data-feather="x"></i>
                            </button>
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
                                        <input type="radio" name="statusFilter" value="terisi" class="status-filter" style="accent-color: #064635; cursor: pointer;"> Ruangan Terisi
                                    </label>
                                    <label class="flex items-center gap-2 text-sm" style="cursor: pointer;">
                                        <input type="radio" name="statusFilter" value="kosong" class="status-filter" style="accent-color: #064635; cursor: pointer;"> Ruangan Kosong
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
                        @can('create', \App\Models\Ruangan::class)
                        <a href="{{ route('ruangan.create') }}" class="add-btn">
                            <i data-feather="plus"></i> Tambah Ruangan
                        </a>
                        @endcan
                    </div>
                </div>

                <div class="data-card-body table-loading-wrap">
                    @can('delete', \App\Models\Ruangan::class)
                    <div id="bulk-bar" style="display:none;align-items:center;gap:12px;background:#fef2f2;border:1px solid #fecaca;border-radius:8px;padding:10px 16px;margin:12px 16px 0;flex-wrap:wrap;">
                        <span style="font-size:13px;font-weight:600;color:#dc2626;"><span id="bulk-count">0</span> data terpilih</span>
                        <button type="button" onclick="clearSelection()" style="background:transparent;color:#6b7280;border:1px solid #d1d5db;padding:7px 12px;border-radius:6px;font-size:13px;cursor:pointer;display:inline-flex;align-items:center;gap:5px;"><i data-feather="x" style="width:14px;height:14px;"></i> Batal</button>
                        <button type="button" onclick="bulkDeleteConfirm()" style="background:#dc2626;color:#fff;border:none;padding:7px 14px;border-radius:6px;font-size:13px;font-weight:600;cursor:pointer;display:inline-flex;align-items:center;gap:5px;"><i data-feather="trash-2" style="width:14px;height:14px;"></i> Hapus Terpilih</button>
                    </div>
                    <form id="bulk-delete-form" method="POST" action="{{ route('ruangan.bulk-destroy') }}" style="display:none;">@csrf</form>
                    @endcan
                    <table class="modern-table">
                        <thead>
                            <tr>
                                <th style="width:42px;">@can('delete', \App\Models\Ruangan::class)<input type="checkbox" id="check-all" onchange="toggleAll(this)" style="cursor:pointer;accent-color:#064635;">@endcan</th>
                                <th style="width:50px;text-align:center;">No</th>
                                <th>Nama Ruangan</th>
                                <th>Penanggung Jawab</th>
                                <th style="text-align:center;">Jumlah Aset</th>
                                <th style="width:140px;">Aksi</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($ruangans as $index => $ruangan)
                            <tr>
                                <td>@can('delete', \App\Models\Ruangan::class)<input type="checkbox" class="row-check" value="{{ $ruangan->id }}" onchange="updateBulkBar()" style="cursor:pointer;accent-color:#064635;">@endcan</td>
                                <td style="text-align:center;">{{ $ruangans->firstItem() + $index }}</td>
                                <td>
                                    <div class="ruangan-cell">
                                        <div class="ruangan-info">
                                            <h6>{{ $ruangan->nama }}</h6>
                                            <span>{{ $ruangan->kode ?? '-' }}</span>
                                        </div>
                                    </div>
                                </td>
                                <td>
                                    @if($ruangan->penanggungJawab)
                                        <span style="font-size:13px;font-weight:500;color:#374151;">{{ $ruangan->penanggungJawab->nama }}</span>
                                    @else
                                        <span style="color:#9ca3af;font-size:13px;">-</span>
                                    @endif
                                </td>
                                <td style="text-align:center;">
                                    <span class="aset-count-badge">
                                        <i data-feather="package" style="width:12px;height:12px;"></i>
                                        {{ $ruangan->inventaris_count }} aset
                                    </span>
                                </td>
                                <td>
                                    <div class="action-btns">
                                        <a href="{{ route('ruangan.show', $ruangan->id) }}" class="action-btn view" title="Lihat">
                                            <i data-feather="eye"></i>
                                        </a>
                                        @can('update', $ruangan)
                                        <a href="{{ route('ruangan.edit', $ruangan->id) }}" class="action-btn edit" title="Edit">
                                            <i data-feather="edit-2"></i>
                                        </a>
                                        @endcan
                                        @can('delete', $ruangan)
                                        <form action="{{ route('ruangan.destroy', $ruangan->id) }}" method="POST" style="display:inline;" id="deleteForm{{ $ruangan->id }}">
                                            @csrf
                                            @method('DELETE')
                                            <button type="button" class="action-btn delete" title="Hapus"
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
                                <td colspan="{{ auth()->user()->can('delete', \App\Models\Ruangan::class) ? 6 : 5 }}" style="text-align:center;padding:40px;color:#9ca3af;">
                                    <div style="display:flex;flex-direction:column;align-items:center;justify-content:center;text-align:center;gap:8px;">
                                        <i data-feather="inbox" style="width:48px;height:48px;opacity:0.5;"></i>
                                        <p style="margin:0;">Belum ada data ruangan</p>
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
                                <option value="10" {{ ($ruangans->perPage() == 10) ? 'selected' : '' }}>10</option>
                                <option value="20" {{ ($ruangans->perPage() == 20) ? 'selected' : '' }}>20</option>
                                <option value="50" {{ ($ruangans->perPage() == 50) ? 'selected' : '' }}>50</option>
                                <option value="100" {{ ($ruangans->perPage() == 100) ? 'selected' : '' }}>100</option>
                            </select>
                            <span>data</span>
                        </div>
                    </div>

                    <div class="pagination-center">
                        <nav class="pagination-nav" id="paginationNav">
                            @if($ruangans->onFirstPage())
                                <span class="page-btn disabled" title="Previous"><i data-feather="chevron-left"></i></span>
                            @else
                                <button type="button" class="page-btn" onclick="goToPage({{ $ruangans->currentPage() - 1 }})" title="Previous"><i data-feather="chevron-left"></i></button>
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
                                    <span class="page-btn active">{{ $p }}</span>
                                @else
                                    <button type="button" class="page-btn" onclick="goToPage({{ $p }})">{{ $p }}</button>
                                @endif
                            @endfor
                            @if($ruangans->hasMorePages())
                                <button type="button" class="page-btn" onclick="goToPage({{ $ruangans->currentPage() + 1 }})" title="Next"><i data-feather="chevron-right"></i></button>
                            @else
                                <span class="page-btn disabled" title="Next"><i data-feather="chevron-right"></i></span>
                            @endif
                        </nav>
                        <div class="page-jump-wrap">
                            <label for="pageJumpInput">Ke hal.</label>
                            <input id="pageJumpInput" type="number" class="page-jump-input"
                                   min="1" max="{{ $ruangans->lastPage() }}"
                                   value="{{ $ruangans->currentPage() }}"
                                   placeholder="1–{{ $ruangans->lastPage() }}"
                                   title="Masukkan nomor halaman (1–{{ $ruangans->lastPage() }})">
                        </div>
                    </div>

                    <div class="pagination-right" id="paginationRangeInfo">
                        Menampilkan <strong>{{ $ruangans->firstItem() ?? 0 }}-{{ $ruangans->lastItem() ?? 0 }}</strong> dari <strong>{{ $ruangans->total() }}</strong> data
                    </div>
                </div></div>
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
            const lastPage = parseInt(jumpInput.max, 10);
            jumpInput.addEventListener('keydown', function(e) {
                if (e.key !== 'Enter') return;
                const val = parseInt(this.value, 10);
                if (isNaN(val) || val < 1 || val > lastPage) return;
                fetchRuangan(val);
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
            const checked = document.querySelectorAll('.row-check:checked');
            const bar = document.getElementById('bulk-bar');
            if (bar) bar.style.display = checked.length > 0 ? 'flex' : 'none';
            const cnt = document.getElementById('bulk-count');
            if (cnt) cnt.textContent = checked.length;
            if (typeof feather !== 'undefined') feather.replace();
        }
        function clearSelection() {
            document.querySelectorAll('.row-check, #check-all').forEach(cb => { cb.checked = false; });
            const bar = document.getElementById('bulk-bar');
            if (bar) bar.style.display = 'none';
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

        // ===== SEARCH — AJAX (tidak reload halaman) =====
        let _searchTimer = null;

        function toggleSearchClear(input) {
            const iconBtn  = document.getElementById('search-icon-btn');
            const clearBtn = document.getElementById('clear-search-btn');
            if (iconBtn)  iconBtn.style.display  = input.value ? 'none' : '';
            if (clearBtn) clearBtn.style.display = input.value ? '' : 'none';
        }

        function onSearchInput(input) {
            toggleSearchClear(input);
            clearTimeout(_searchTimer);
            _searchTimer = setTimeout(function() { fetchRuangan(); }, 400);
        }

        function clearSearch() {
            const input = document.getElementById('searchInput');
            input.value = '';
            toggleSearchClear(input);
            fetchRuangan();
        }

        function changePerPage(perPage) {
            fetchRuangan(false, 1);
        }

        function goToPage(page) {
            fetchRuangan(false, page);
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

        function fetchRuangan(isAutoRefresh = false, pageOverride = null) {
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

            const newUrl = window.location.pathname + (params.toString() ? '?' + params.toString() : '');
            history.replaceState(null, '', newUrl);

            const wrap = document.querySelector('.table-loading-wrap');
            if (!isAutoRefresh && wrap) wrap.classList.add('is-loading');

            fetch('{{ route('ruangan.index') }}?' + params.toString(), {
                headers: { 'X-Requested-With': 'XMLHttpRequest', 'Accept': 'application/json' }
            })
            .then(r => r.json())
            .then(data => {
                updateTable(data.ruangans, data.pagination);
                updatePaginationUI(data.pagination);
                if (typeof feather !== 'undefined') feather.replace();
            })
            .catch(err => console.error(err))
            .finally(() => { if (wrap) wrap.classList.remove('is-loading'); });
        }

        function applyFilters() {
            appliedStatus = document.querySelector('.status-filter:checked')?.value || 'all';
            appliedSort   = document.querySelector('.sort-filter:checked')?.value || 'nama_asc';
            fetchRuangan(false, 1);
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
            const total     = pagination ? pagination.total : items.length;

            // Update data-card subtitle
            document.querySelectorAll('.data-card-title span').forEach(el => {
                el.textContent = 'Total ' + total + ' ruangan terdaftar';
            });

            if (items.length === 0) {
                tbody.innerHTML = `<tr><td colspan="{{ auth()->user()->can('delete', \App\Models\Ruangan::class) ? 6 : 5 }}" style="text-align:center;padding:40px;color:#9ca3af;">
                    <div style="display:flex;flex-direction:column;align-items:center;justify-content:center;text-align:center;gap:8px;">
                        <i data-feather="inbox" style="width:48px;height:48px;opacity:0.5;"></i>
                        <p style="margin:0;">Belum ada data ruangan</p>
                    </div></td></tr>`;
                return;
            }

            let html = '';
            items.forEach((item, idx) => {
                const no      = firstItem + idx;
                const kode    = item.kode ?? '-';
                const pj = item.penanggung_jawab
                    ? `<span style="font-size:13px;font-weight:500;color:#374151;">${item.penanggung_jawab}</span>`
                    : `<span style="color:#9ca3af;font-size:13px;">-</span>`;
                html += `<tr>
                    <td><input type="checkbox" class="row-check" value="${item.id}" onchange="updateBulkBar()" style="cursor:pointer;accent-color:#064635;"></td>
                    <td style="text-align:center;">${no}</td>
                    <td><div class="ruangan-cell">
                        <div class="ruangan-info"><h6>${item.nama}</h6><span>${kode}</span></div>
                    </div></td>
                    <td>${pj}</td>
                    <td style="text-align:center;"><span class="aset-count-badge"><i data-feather="package" style="width:12px;height:12px;"></i> ${item.inventaris_count} aset</span></td>
                    <td><div class="action-btns">
                        <a href="{{ url('ruangan') }}/${item.id}" class="action-btn view" title="Lihat"><i data-feather="eye"></i></a>
                        <a href="{{ url('ruangan') }}/${item.id}/edit" class="action-btn edit" title="Edit"><i data-feather="edit-2"></i></a>
                        <button type="button" class="action-btn delete" title="Hapus" data-nama="${item.nama.replace(/&/g, '&amp;').replace(/"/g, '&quot;')}" onclick="confirmDelete(this.getAttribute('data-nama'), ${item.id})"><i data-feather="trash-2"></i></button>
                    </div></td>
                </tr>`;
            });
            tbody.innerHTML = html;
        }

        // Restore search value dari URL saat load + setup auto-refresh
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

            // Silent auto-refresh setiap 15 detik
            const autoRefreshMs = 15000;
            setInterval(function () {
                if (document.hidden) return;
                fetchRuangan(true);
            }, autoRefreshMs);
        });
    </script>
</body>
</html>
