<!doctype html>
<html lang="en" data-pc-preset="{{ config('theme.pc_preset_theme', 'preset-1') }}" data-pc-theme="{{ config('theme.pc_dark_layout', 'light') }}">
<head>
    @include('layouts.head-page-meta', ['title' => 'Semua Aktivitas Saya'])
    @include('layouts.head-css')
    <style>
        .activity-card {
            background: #fff;
            border-radius: 20px;
            border: 1px solid rgba(0,0,0,0.04);
            box-shadow: 0 4px 24px rgba(0,0,0,0.06);
        }
        .log-header {
            padding: 24px 28px;
            border-bottom: 1px solid #f3f4f6;
            display: flex;
            align-items: center;
            justify-content: space-between;
            flex-wrap: wrap;
            gap: 20px;
        }
        .log-title {
            display: flex;
            align-items: center;
            gap: 12px;
        }
        .log-icon {
            width: 42px; height: 42px;
            border-radius: 12px;
            background: #519259 !important;
            display: flex; align-items: center; justify-content: center;
            flex-shrink: 0;
        }
        .log-icon i, .log-icon svg { width: 18px; height: 18px; color: #fff !important; stroke: #fff !important; }
        .log-title h5 { margin: 0; font-size: 18px; font-weight: 600; color: #064635 !important; }
        .log-title span { font-size: 13px; color: #9ca3af; }

        /* Controls bar */
        .controls-bar {
            display: flex;
            align-items: center;
            gap: 10px;
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
            box-shadow: 0 0 0 3px rgba(6, 70, 53, 0.1);
        }
        .search-box input {
            border: none;
            background: transparent;
            padding: 10px 0;
            font-size: 13.5px;
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
        .search-box button i { width: 16px; height: 16px; }

        .filter-icon-btn {
            width: 42px;
            height: 42px;
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
        .filter-icon-btn:hover {
            background: #064635;
            border-color: #064635;
            color: #fff;
        }
        .filter-icon-btn i { width: 18px; height: 18px; }

        .btn-hapus-semua {
            display: inline-flex;
            align-items: center;
            gap: 8px;
            padding: 0 18px;
            height: 42px;
            border-radius: 12px;
            background: #fff5f5;
            color: #dc2626;
            border: 1px solid #fecaca;
            font-size: 13.5px;
            font-weight: 600;
            cursor: pointer;
            transition: all 0.2s;
            white-space: nowrap;
        }
        .btn-hapus-semua:hover {
            background: #dc2626;
            color: #fff;
            border-color: #dc2626;
        }
        .btn-hapus-semua i,
        .btn-hapus-semua svg {
            width: 16px;
            height: 16px;
        }

        /* List Items */
        .activity-row-wrap {
            border-bottom: 1px solid #f3f4f6;
        }
        .activity-row-wrap:last-child {
            border-bottom: none;
        }
        .activity-row {
            display: grid;
            grid-template-columns: 12px minmax(0, 1fr) 114px;
            align-items: center;
            gap: 14px;
            padding: 14px 28px;
            transition: background 0.2s;
            background: #ffffff;
        }
        .activity-row-wrap:hover .activity-row {
            background: #f9fafb;
        }

        /* Indicator Dot */
        .activity-dot {
            width: 8px;
            height: 8px;
            border-radius: 50%;
            flex-shrink: 0;
            display: block;
        }

        /* Main Info Column */
        .activity-main {
            min-width: 0;
        }
        .activity-main-top {
            display: flex;
            align-items: center;
            gap: 8px;
            flex-wrap: wrap;
            margin-bottom: 3px;
        }
        .activity-user-name {
            font-size: 13.5px;
            font-weight: 600;
            color: #064635;
        }
        .action-badge {
            display: inline-flex;
            align-items: center;
            padding: 2px 9px;
            border-radius: 20px;
            font-size: 10.5px;
            font-weight: 600;
            color: #fff;
            text-transform: capitalize;
            line-height: 1.4;
        }
        .module-badge {
            display: inline-flex;
            align-items: center;
            padding: 2px 9px;
            border-radius: 20px;
            font-size: 10.5px;
            font-weight: 500;
            background: #e8f3ec;
            color: #064635;
            border: 1px solid #c7e2d2;
            line-height: 1.4;
        }

        .activity-text {
            font-size: 13.5px;
            color: #374151;
            margin: 0;
            line-height: 1.4;
            word-break: break-word;
        }

        /* Time & Delete Action Column */
        .activity-side {
            display: flex;
            flex-direction: column;
            align-items: flex-end;
            justify-content: center;
            gap: 6px;
            min-width: 0;
        }
        .activity-time {
            font-size: 11px;
            color: #9ca3af;
            text-align: right;
            line-height: 1.35;
            white-space: nowrap;
        }
        .btn-del-row {
            width: 32px;
            height: 32px;
            border-radius: 8px;
            border: 1px solid #fecaca;
            background: #fff5f5;
            color: #dc2626;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            cursor: pointer;
            transition: all 0.15s;
            padding: 0;
            line-height: 1;
        }
        .btn-del-row:hover {
            background: #dc2626;
            color: #fff;
            border-color: #dc2626;
        }
        .btn-del-row i,
        .btn-del-row svg {
            width: 13px;
            height: 13px;
        }

        .empty-state {
            padding: 60px 24px;
            display: flex;
            flex-direction: column;
            align-items: center;
            justify-content: center;
            text-align: center;
            color: #9ca3af;
        }
        .empty-state i { width: 48px; height: 48px; margin-bottom: 12px; opacity: 0.5; }

        /* Standard 3-Column Pagination Bar matching Index Views */
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
        .page-btn.disabled { opacity: 0.5; cursor: not-allowed; pointer-events: none; }
        .page-btn i, .page-btn svg { width: 16px; height: 16px; }
        .page-jump-wrap { display: flex; align-items: center; gap: 6px; margin-left: 6px; }
        .page-jump-wrap label { font-size: 13px; color: #6b7280; white-space: nowrap; }
        .page-jump-input { width: 56px; height: 34px; border: 1px solid #d1d5db; border-radius: 6px; text-align: center; font-size: 13px; color: #374151; outline: none; transition: border-color .15s; }
        .page-jump-input:focus { border-color: #064635; box-shadow: 0 0 0 2px rgba(6, 70, 53,.15); }

        @media (max-width: 575px) {
            .pagination-bar { grid-template-columns: 1fr; align-items: center; gap: 10px; padding: 14px 16px; }
            .pagination-nav { flex-wrap: wrap; justify-content: center; }
            .page-jump-wrap { margin-left: 0; justify-content: center; }
            .pagination-right { text-align: center; justify-content: center; }
        }

        @media (max-width: 768px) {
            .log-header { align-items: stretch; padding: 16px; }
            .log-title { width: 100%; }
            .controls-bar { width: 100%; flex-wrap: nowrap; justify-content: flex-start; gap: 8px; }
            #activitySearchForm { flex: 1; min-width: 0; }
            .search-box { width: 100%; padding: 0 12px; }
            .search-box input { width: 100%; }
            .activity-row { grid-template-columns: 10px minmax(0, 1fr) 104px; padding: 11px 14px; gap: 10px; }
        }
    </style>
</head>
<body>
    @include('layouts.notification')
    @php
        $disableModalSessionFeedback = true;
    @endphp
    @include('layouts.modals')
    @include('layouts.layout-vertical')

    <div class="pc-container">
        <div class="pc-content">
            @include('layouts.breadcrumb', [
                'breadcrumbItemHome' => 'Navigasi',
                'breadcrumbItem' => 'Aktivitas',
                'breadcrumbItemActive' => 'Semua Aktivitas Saya'
            ])

            <div class="activity-card">
                {{-- Header --}}
                <div class="log-header">
                    <div class="log-title">
                        <div class="log-icon">
                            <i data-feather="activity"></i>
                        </div>
                        <div>
                            <h5 style="margin:0;font-size:18px;font-weight:600;color:#064635;">Aktivitas Pengguna</h5>
                            <span id="activities-total-text">Total {{ $logs->total() }} aktivitas tercatat</span>
                        </div>
                    </div>

                    <div class="controls-bar">
                        {{-- Search input --}}
                        <form method="GET" action="{{ route('dashboard.activities') }}" id="activitySearchForm" style="margin:0;">
                            <input type="hidden" name="action" value="{{ request('action', '') }}">
                            <input type="hidden" name="module" value="{{ request('module', '') }}">
                            <input type="hidden" name="sort"   value="{{ request('sort', 'terbaru') }}">
                            <div class="search-box">
                                <input type="text" id="searchInput" name="search" value="{{ request('search', '') }}"
                                    placeholder="Cari aktivitas..."
                                    oninput="onActivitySearchInput(this)"
                                    onkeypress="if(event.key==='Enter'){event.preventDefault();doActivitySearch();}">
                                <button type="button" id="search-icon-btn" onclick="doActivitySearch()">
                                    <i data-feather="search"></i>
                                </button>
                                <button type="button" id="clear-search-btn" onclick="clearActivitySearch()" style="color:#ef4444;{{ request('search') ? '' : 'display:none;' }}">
                                    <i data-feather="x"></i>
                                </button>
                            </div>
                        </form>

                        {{-- Filter dropdown --}}
                        <div class="dropdown" style="position:relative;">
                            <button class="filter-icon-btn {{ request('action') || request('module') || request('sort','terbaru') !== 'terbaru' ? 'has-filter' : '' }}"
                                data-pc-toggle="dropdown" title="Filter" type="button">
                                <i data-feather="sliders"></i>
                            </button>
                            <div class="dropdown-menu dropdown-menu-end py-3 px-4" onclick="event.stopPropagation();" style="min-width:260px;max-height:440px;overflow-y:auto;z-index:1050;">
                                <div style="font-size:12px;font-weight:700;color:#064635;margin-bottom:8px;">Aksi</div>
                                <div style="margin-bottom:4px;">
                                    @php $curAction = request('action', ''); @endphp
                                    @foreach([
                                        ''       => 'Semua Aksi',
                                        'tambah' => 'Tambah Data',
                                        'ubah'   => 'Update Data',
                                        'hapus'  => 'Hapus Data',
                                    ] as $val => $label)
                                    <label style="display:flex;align-items:center;gap:8px;font-size:13px;cursor:pointer;margin-bottom:6px;">
                                        <input type="radio" name="rAction" value="{{ $val }}"
                                            {{ $curAction === $val ? 'checked' : '' }}
                                            style="accent-color:#064635;cursor:pointer;">
                                        {{ $label }}
                                    </label>
                                    @endforeach
                                </div>
                                <hr style="border-color:#e5e7eb;margin:12px 0;">
                                <div style="font-size:12px;font-weight:700;color:#064635;margin-bottom:8px;">Modul</div>
                                <div style="margin-bottom:4px;">
                                    @php $curModule = request('module', ''); @endphp
                                    @foreach([
                                        ''               => 'Semua Modul',
                                        'JenisInventaris'=> 'Jenis Inventaris',
                                        'User'           => 'User',
                                        'Pejabat'        => 'Pejabat',
                                        'Inventaris'     => 'Manajemen Aset',
                                    ] as $val => $label)
                                    <label style="display:flex;align-items:center;gap:8px;font-size:13px;cursor:pointer;margin-bottom:6px;">
                                        <input type="radio" name="rModule" value="{{ $val }}"
                                            {{ $curModule === $val ? 'checked' : '' }}
                                            style="accent-color:#064635;cursor:pointer;">
                                        {{ $label }}
                                    </label>
                                    @endforeach
                                </div>
                                <hr style="border-color:#e5e7eb;margin:12px 0;">
                                <div style="font-size:12px;font-weight:700;color:#064635;margin-bottom:8px;">Urutkan</div>
                                <div style="margin-bottom:4px;">
                                    @php $curSort = request('sort', 'terbaru'); @endphp
                                    <label style="display:flex;align-items:center;gap:8px;font-size:13px;cursor:pointer;margin-bottom:6px;">
                                        <input type="radio" name="rSort" value="terbaru" {{ $curSort === 'terbaru' ? 'checked' : '' }} style="accent-color:#064635;cursor:pointer;"> Terbaru
                                    </label>
                                    <label style="display:flex;align-items:center;gap:8px;font-size:13px;cursor:pointer;margin-bottom:6px;">
                                        <input type="radio" name="rSort" value="terlama" {{ $curSort === 'terlama' ? 'checked' : '' }} style="accent-color:#064635;cursor:pointer;"> Terlama
                                    </label>
                                </div>
                                <div style="padding-top:12px;border-top:1px solid #e5e7eb;">
                                    <button type="button"
                                        onclick="applyActivityFilter(); event.stopPropagation();"
                                        style="width:100%;padding:10px 16px;background:linear-gradient(135deg, #064635 0%, #2e7d5c 100%);color:#fff;border:none;border-radius:8px;font-size:14px;font-weight:600;cursor:pointer;box-shadow: 0 3px 8px rgba(6, 70, 53, 0.25);">
                                        Terapkan Filter
                                    </button>
                                    <button type="button"
                                        onclick="resetActivityFilter(); event.stopPropagation();"
                                        style="width:100%;padding:8px 16px;background:transparent;color:#6b7280;border:1px solid #d1d5db;border-radius:8px;font-size:13px;margin-top:8px;cursor:pointer;">
                                        Reset Filter
                                    </button>
                                </div>
                            </div>
                        </div>

                        @if(($canDelete ?? false) && $logs->total() > 0)
                        <form id="deleteAllActivitiesForm" method="POST" action="{{ route('dashboard.activities.destroy-all') }}" class="bulk-delete-form" style="margin:0;">
                            @csrf
                            <button type="button" class="btn-hapus-semua" onclick="confirmDeleteAllActivities()">
                                <i data-feather="trash-2"></i>
                                Hapus semua data
                            </button>
                        </form>
                        @endif
                    </div>
                </div>

                {{-- Activity list container --}}
                <div id="activities-list-container">
                    @if($logs->isEmpty())
                        <div class="empty-state">
                            <i data-feather="inbox"></i>
                            <div style="font-size:14px; font-weight:600; color:#6b7280; margin-bottom:4px;">Tidak ada aktivitas</div>
                            <div style="font-size:12px;">Belum ada aktivitas yang cocok dengan filter yang dipilih.</div>
                        </div>
                    @else
                        @foreach($logs as $log)
                        @php
                            $dotBg = $colors[$log->action] ?? '#6b7280';
                        @endphp
                        <div class="activity-row-wrap">
                            <div class="activity-row">
                                <span class="activity-dot" style="background-color: {{ $dotBg }};"></span>

                                <div class="activity-main">
                                    <div class="activity-main-top">
                                        <span class="activity-user-name">{{ $log->user?->nama ?? 'Sistem' }}</span>
                                        <span class="action-badge" style="background-color: {{ $dotBg }};">
                                            {{ ucfirst($log->action) }}
                                        </span>
                                        @if($log->module)
                                        <span class="module-badge">
                                            {{ $log->module }}
                                        </span>
                                        @endif
                                    </div>
                                    <p class="activity-text">{{ $log->detail }}</p>
                                </div>

                                <div class="activity-side">
                                    <div class="activity-time">
                                        {{ $log->created_at?->translatedFormat('d M Y') }}
                                        <div style="color:#b0b7c3;">{{ $log->created_at?->format('H:i') }}</div>
                                    </div>

                                    @if($canDelete ?? false)
                                    <form id="deleteActivityForm{{ $log->id }}" method="POST" action="{{ route('dashboard.activities.destroy', $log->id) }}" style="margin:0;">
                                        @csrf
                                        @method('DELETE')
                                        <button type="button" class="btn-del-row" title="Hapus aktivitas" onclick="confirmDeleteActivity({{ $log->id }})">
                                            <i data-feather="trash-2"></i>
                                        </button>
                                    </form>
                                    @endif
                                </div>
                            </div>
                        </div>
                        @endforeach
                    @endif
                </div>

                {{-- Standard 3-Column Pagination Bar Footer --}}
                <div class="pagination-bar" id="paginationBar">
                    <div class="pagination-left">
                        <div class="page-size-wrap">
                            <span>Tampilkan</span>
                            <select id="perPageSelect" class="per-page-select" onchange="changePerPage(this.value)">
                                <option value="10" {{ ($logs->perPage() == 10) ? 'selected' : '' }}>10</option>
                                <option value="20" {{ ($logs->perPage() == 20) ? 'selected' : '' }}>20</option>
                                <option value="50" {{ ($logs->perPage() == 50) ? 'selected' : '' }}>50</option>
                                <option value="100" {{ ($logs->perPage() == 100) ? 'selected' : '' }}>100</option>
                            </select>
                            <span>data</span>
                        </div>
                    </div>

                    <div class="pagination-center">
                        <nav class="pagination-nav" id="paginationNav">
                            @if ($logs->onFirstPage())
                                <span class="page-btn disabled" title="Previous"><i data-feather="chevron-left"></i></span>
                            @else
                                <button type="button" class="page-btn" onclick="goToActivitiesPage({{ $logs->currentPage() - 1 }})" title="Previous"><i data-feather="chevron-left"></i></button>
                            @endif

                            @php
                                $currentPage  = $logs->currentPage();
                                $lastPage     = $logs->lastPage();
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
                                    <button type="button" class="page-btn" onclick="goToActivitiesPage({{ $p }})">{{ $p }}</button>
                                @endif
                            @endfor

                            @if ($logs->hasMorePages())
                                <button type="button" class="page-btn" onclick="goToActivitiesPage({{ $logs->currentPage() + 1 }})" title="Next"><i data-feather="chevron-right"></i></button>
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
                                max="{{ $logs->lastPage() }}"
                                value="{{ $logs->currentPage() }}"
                                placeholder="1–{{ $logs->lastPage() }}"
                                title="Masukkan nomor halaman (1–{{ $logs->lastPage() }})"
                            >
                        </div>
                    </div>

                    <div class="pagination-right" id="paginationRangeInfo">
                        Menampilkan <strong>{{ $logs->firstItem() ?? 0 }}-{{ $logs->lastItem() ?? 0 }}</strong> dari <strong>{{ $logs->total() }}</strong> data
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script>
        let _activitySearchTimer = null;

        function toggleSearchClear(input) {
            const searchBtn = document.getElementById('search-icon-btn');
            const clearBtn = document.getElementById('clear-search-btn');
            if (searchBtn) searchBtn.style.display = input.value ? 'none' : '';
            if (clearBtn) clearBtn.style.display = input.value ? '' : 'none';
        }

        function onActivitySearchInput(input) {
            toggleSearchClear(input);
            clearTimeout(_activitySearchTimer);
            _activitySearchTimer = setTimeout(() => {
                fetchActivitiesData(false, 1);
            }, 400);
        }

        function buildActivitiesParams(page = 1) {
            const params = new URLSearchParams();
            const action = document.querySelector('input[name="rAction"]:checked')?.value || '';
            const moduleName = document.querySelector('input[name="rModule"]:checked')?.value || '';
            const sort = document.querySelector('input[name="rSort"]:checked')?.value || 'terbaru';
            const search = document.getElementById('searchInput')?.value.trim() || '';
            const perPage = document.getElementById('perPageSelect')?.value || '10';

            if (action) params.set('action', action);
            if (moduleName) params.set('module', moduleName);
            if (sort && sort !== 'terbaru') params.set('sort', sort);
            if (search) params.set('search', search);
            if (perPage && perPage !== '10') params.set('per_page', perPage);
            if (page > 1) params.set('page', page);

            return params;
        }

        function changePerPage(val) {
            fetchActivitiesData(false, 1);
        }

        function goToActivitiesPage(page) {
            fetchActivitiesData(false, page);
        }

        function bindActivitiesPageJump() {
            const jumpInput = document.getElementById('pageJumpInput');
            if (!jumpInput) return;
            const lastPage = parseInt(jumpInput.max, 10) || 1;

            jumpInput.addEventListener('keydown', function (e) {
                if (e.key === 'Enter') {
                    e.preventDefault();
                    let val = parseInt(this.value, 10);
                    if (isNaN(val) || val < 1) val = 1;
                    if (val > lastPage) val = lastPage;
                    goToActivitiesPage(val);
                }
            });
            jumpInput.addEventListener('change', function () {
                let val = parseInt(this.value, 10);
                if (isNaN(val) || val < 1) val = 1;
                if (val > lastPage) val = lastPage;
                goToActivitiesPage(val);
            });
        }

        let _activitiesFetchController = null;
        function fetchActivitiesData(isAutoRefresh = false, pageOverride = null) {
            const urlParams = new URLSearchParams(window.location.search);
            const page = pageOverride !== null ? pageOverride : (urlParams.get('page') || 1);
            const params = buildActivitiesParams(page);

            const query = params.toString();
            const targetUrl = '{{ route('dashboard.activities') }}' + (query ? ('?' + query) : '');

            history.replaceState(null, '', targetUrl);

            if (_activitiesFetchController) {
                _activitiesFetchController.abort();
            }
            _activitiesFetchController = new AbortController();

            fetch(targetUrl, {
                headers: { 'X-Requested-With': 'XMLHttpRequest' },
                signal: _activitiesFetchController.signal
            })
            .then(response => response.text())
            .then(html => {
                const parser = new DOMParser();
                const doc = parser.parseFromString(html, 'text/html');

                const nextList = doc.getElementById('activities-list-container');
                const currentList = document.getElementById('activities-list-container');
                if (nextList && currentList) {
                    currentList.innerHTML = nextList.innerHTML;
                }

                const nextTotalText = doc.getElementById('activities-total-text');
                const currentTotalText = document.getElementById('activities-total-text');
                if (nextTotalText && currentTotalText) {
                    currentTotalText.textContent = nextTotalText.textContent;
                }

                const nextPagination = doc.getElementById('paginationBar');
                const currentPagination = document.getElementById('paginationBar');
                if (nextPagination && currentPagination) {
                    currentPagination.innerHTML = nextPagination.innerHTML;
                }

                bindActivitiesPageJump();
                if (typeof feather !== 'undefined') feather.replace();
            })
            .catch(error => {
                if (error.name !== 'AbortError') {
                    console.error('Gagal memuat data aktivitas:', error);
                }
            });
        }

        function doActivitySearch() {
            fetchActivitiesData(false, 1);
        }

        function clearActivitySearch() {
            const input = document.getElementById('searchInput');
            if (!input) return;

            input.value = '';
            toggleSearchClear(input);
            fetchActivitiesData(false, 1);
        }

        (function() {
            const input = document.getElementById('searchInput');
            if (input) toggleSearchClear(input);
            bindActivitiesPageJump();
            if (typeof feather !== 'undefined') feather.replace();
        })();

        function applyActivityFilter() {
            fetchActivitiesData(false, 1);
            document.querySelectorAll('.dropdown-menu.show').forEach(el => el.classList.remove('show'));
            document.querySelectorAll('.dropdown.show').forEach(el => el.classList.remove('show'));
        }

        function resetActivityFilter() {
            const actionDefault = document.querySelector('input[name="rAction"][value=""]');
            const moduleDefault = document.querySelector('input[name="rModule"][value=""]');
            const sortDefault   = document.querySelector('input[name="rSort"][value="terbaru"]');
            const input         = document.getElementById('searchInput');

            if (actionDefault) actionDefault.checked = true;
            if (moduleDefault) moduleDefault.checked = true;
            if (sortDefault) sortDefault.checked = true;
            if (input) input.value = '';

            toggleSearchClear(input || { value: '' });
            fetchActivitiesData(false, 1);
            document.querySelectorAll('.dropdown-menu.show').forEach(el => el.classList.remove('show'));
            document.querySelectorAll('.dropdown.show').forEach(el => el.classList.remove('show'));
        }

        function confirmDeleteActivity(logId) {
            const submitAction = function() {
                const form = document.getElementById('deleteActivityForm' + logId);
                if (form) form.submit();
            };

            if (typeof AppModal !== 'undefined') {
                AppModal.confirmDelete('data aktivitas ini', submitAction);
                return;
            }

            if (confirm('Apakah Anda yakin ingin menghapus data aktivitas ini?')) {
                submitAction();
            }
        }

        function confirmDeleteAllActivities() {
            const submitAction = function() {
                const form = document.getElementById('deleteAllActivitiesForm');
                if (form) form.submit();
            };

            if (typeof AppModal !== 'undefined') {
                AppModal.confirm(
                    'Hapus Semua Aktivitas',
                    'Apakah Anda yakin ingin menghapus semua data aktivitas? Aksi ini tidak dapat dibatalkan.',
                    submitAction
                );
                return;
            }

            if (confirm('Apakah Anda yakin ingin menghapus semua data aktivitas?')) {
                submitAction();
            }
        }

        const activitiesAutoRefreshMs = 15000;
        setInterval(function () {
            if (document.hidden) return;
            fetchActivitiesData(true);
        }, activitiesAutoRefreshMs);
    </script>

    @include('layouts.footer-block')
    @include('layouts.footer-js')
</body>
</html>
