<!DOCTYPE html>
<html lang="id">
<head>
    @include('layouts.head-page-meta', ['title' => 'Aktivitas Sistem - Sistem Manajemen Aset Desa'])
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

        /* Filter Popover Dropdown (Consistent with index standard) */
        .filter-dropdown-menu {
            min-width: 300px;
            max-width: 360px;
            max-height: min(520px, calc(100vh - 140px));
            overflow-y: auto;
            overscroll-behavior: contain;
            z-index: 1050;
            border-radius: var(--radius-lg);
            border: 1px solid var(--color-border);
            box-shadow: var(--shadow-float);
            background: var(--color-surface);
            padding: 18px 20px;
        }
        .filter-dropdown-menu::-webkit-scrollbar {
            width: 5px;
        }
        .filter-dropdown-menu::-webkit-scrollbar-track {
            background: transparent;
        }
        .filter-dropdown-menu::-webkit-scrollbar-thumb {
            background: var(--color-border);
            border-radius: 9999px;
        }
        .filter-dropdown-menu::-webkit-scrollbar-thumb:hover {
            background: var(--color-text-muted);
        }
        .filter-dropdown-menu[data-popper-placement^="top"],
        .dropdown-menu[data-popper-placement^="top"] {
            margin-bottom: 8px !important;
        }
        .filter-dropdown-menu[data-popper-placement^="bottom"],
        .dropdown-menu[data-popper-placement^="bottom"] {
            margin-top: 8px !important;
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
        <div class="pc-content" style="max-width: 1560px; margin: 0 auto;">

            <!-- PAGE HEADER & PRIMARY ACTIONS -->
            <div style="display: flex; justify-content: space-between; align-items: flex-start; margin-bottom: 24px; flex-wrap: wrap; gap: 16px;">
                <div>
                    <h1 style="font-size: 22px; font-weight: 700; margin: 0; color: var(--color-text); line-height: 1.2; letter-spacing: -0.02em;">
                        Aktivitas Sistem
                    </h1>
                </div>
                <div class="page-header-actions" style="display: flex; gap: 10px; align-items: center; flex-wrap: wrap;">
                    <div class="sima-dropdown-wrapper" style="position: relative; display: inline-block;">
                        <button type="button" id="btn-export-activities" class="btn-sima-secondary btn-sima-pill-white" onclick="toggleExportMenu(this, event)">
                            <i data-feather="download" style="width: 14px; height: 14px;"></i>
                            <span>Ekspor Data</span>
                            <i data-feather="chevron-down" style="width: 12px; height: 12px; opacity: 0.7;"></i>
                        </button>
                        <div class="export-dropdown-menu" style="display: none;">
                            <a class="export-menu-item" id="export-activities-excel" href="{{ route('dashboard.activities.export', ['format' => 'excel']) }}" onclick="if(window.showToast) window.showToast('Ekspor riwayat aktivitas (Excel) berhasil! Berkas sedang diunduh...', 'success'); this.closest('.export-dropdown-menu').style.display='none';">
                                <i data-feather="file-text" style="width: 14px; height: 14px; color: #10b981;"></i>
                                <span>Ekspor Excel (.xlsx)</span>
                            </a>
                            <hr class="dropdown-divider sima-divider" />
                            <a class="export-menu-item" id="export-activities-pdf" href="{{ route('dashboard.activities.export', ['format' => 'pdf']) }}" onclick="if(window.showToast) window.showToast('Ekspor riwayat aktivitas (PDF) berhasil! Berkas sedang diunduh...', 'success'); this.closest('.export-dropdown-menu').style.display='none';">
                                <i data-feather="file" style="width: 14px; height: 14px; color: #ef4444;"></i>
                                <span>Ekspor PDF (.pdf)</span>
                            </a>
                        </div>
                    </div>
                </div>
            </div>

            @php
                $totalLogCount = \App\Models\ActivityLog::excludeSuperDevelopers()->count();
                $todayLogCount = \App\Models\ActivityLog::excludeSuperDevelopers()->whereDate('created_at', today())->count();
                $monthLogCount = \App\Models\ActivityLog::excludeSuperDevelopers()->whereMonth('created_at', now()->month)->whereYear('created_at', now()->year)->count();
                $topActionRow  = \App\Models\ActivityLog::excludeSuperDevelopers()
                                    ->select('action', \Illuminate\Support\Facades\DB::raw('count(*) as total'))
                                    ->groupBy('action')
                                    ->orderByDesc('total')
                                    ->first();
                $topActionName = $topActionRow ? $topActionRow->action_label : '-';
                $topActionVal  = $topActionRow ? $topActionRow->total : 0;
            @endphp

            <!-- 1. STAT CARDS (Prodex 4-Card Summary Bar) -->
            <div class="stat-card-row">
                <!-- Card 1: Total Log -->
                <div class="stat-card-sima" id="card-total-log">
                    <div class="stat-header">
                        <span class="stat-label">TOTAL LOG AKTIVITAS</span>
                    </div>
                    <div class="stat-number">{{ number_format($totalLogCount ?? 0, 0, ',', '.') }}</div>
                    <div class="kpi-trend-wrapper stat-delta">
                        <span class="stat-delta-label">sepanjang waktu</span>
                    </div>
                </div>

                <!-- Card 2: Log Hari Ini -->
                <div class="stat-card-sima" id="card-log-today">
                    <div class="stat-header">
                        <span class="stat-label">AKTIVITAS HARI INI</span>
                    </div>
                    <div class="stat-number">{{ number_format($todayLogCount ?? 0, 0, ',', '.') }}</div>
                    <div class="kpi-trend-wrapper stat-delta">
                        <span class="stat-delta-pill is-up">
                            {{ date('d M Y') }}
                        </span>
                    </div>
                </div>

                <!-- Card 3: Log Bulan Ini -->
                <div class="stat-card-sima" id="card-log-month">
                    <div class="stat-header">
                        <span class="stat-label">AKTIVITAS BULAN INI</span>
                    </div>
                    <div class="stat-number">{{ number_format($monthLogCount ?? 0, 0, ',', '.') }}</div>
                    <div class="kpi-trend-wrapper stat-delta">
                        <span class="stat-delta-label">{{ now()->translatedFormat('F Y') }}</span>
                    </div>
                </div>

                <!-- Card 4: Aksi Dominan -->
                <div class="stat-card-sima" id="card-top-action">
                    <div class="stat-header">
                        <span class="stat-label">AKSI TERBANYAK</span>
                    </div>
                    <div class="stat-number" style="font-size: 18px; line-height: 1.3; white-space: nowrap; overflow: hidden; text-overflow: ellipsis;" title="{{ $topActionName }}">{{ $topActionName }}</div>
                    <div class="kpi-trend-wrapper stat-delta">
                        <span class="stat-delta-pill is-neutral">
                            {{ number_format($topActionVal ?? 0, 0, ',', '.') }} kali
                        </span>
                        <span class="stat-delta-label">tercatat</span>
                    </div>
                </div>
            </div>

            <!-- 2. MAIN TABLE CARD -->
            <div class="sima-card" style="padding: 0; margin-bottom: 32px; position: relative; overflow: visible;">
                
                <!-- Action Controls Bar -->
                <div style="display: flex; justify-content: space-between; align-items: center; padding: 20px 20px; border-bottom: 1px solid var(--color-border); flex-wrap: wrap; gap: 12px; border-top-left-radius: var(--radius-lg); border-top-right-radius: var(--radius-lg);">
                    <!-- Left: Search Box -->
                    <div class="topbar-search-box">
                        <i data-feather="search" style="width: 15px; height: 15px; color: var(--color-text-muted); flex-shrink: 0;"></i>
                        <input
                            type="text"
                            id="searchInput"
                            class="topbar-search-input"
                            placeholder="Cari aktivitas..."
                            value="{{ request('search', '') }}"
                            oninput="onActivitySearchInput(this)"
                            onkeypress="if(event.key==='Enter'){event.preventDefault();doActivitySearch();}"
                            autocomplete="off"
                        >
                        <button
                            type="button"
                            id="clear-search-btn"
                            class="topbar-search-shortcut"
                            onclick="clearActivitySearch()"
                            style="display: {{ request('search') ? 'block' : 'none' }}; border: none; background: none; cursor: pointer; color: var(--color-text-muted); font-size: 11px; padding: 0 4px;"
                            title="Hapus pencarian"
                        >Batal</button>
                    </div>

                    <!-- Right Controls: Reset Filter, Filter Dropdown, Delete All -->
                    <div style="display: flex; align-items: center; gap: 8px; flex-wrap: wrap;">
                        <button type="button" class="btn-sima-secondary btn-reset-filter" id="btn-reset-filter" onclick="resetActivityFilter()" title="Reset semua filter" style="padding: 7px 12px; font-size: 13px;">
                            <i data-feather="rotate-ccw" style="width: 13px; height: 13px;"></i>
                            <span>Reset Filter</span>
                        </button>

                        <div class="dropdown" id="filter-dropdown-container" style="position: relative;">
                            <button type="button" id="btn-filter-toggle" class="btn-sima-secondary" onclick="toggleFilterPopover(event)" title="Filter" style="padding: 7px 12px; font-size: 13px; display: inline-flex; align-items: center; gap: 6px;">
                                <i data-feather="sliders" style="width: 13px; height: 13px;"></i>
                                <span>Filter</span>
                            </button>
                            <div id="filter-dropdown-menu" class="sima-filter-popover" onclick="event.stopPropagation();" style="display: none; position: absolute; top: calc(100% + 4px); right: 0; min-width: 330px; padding: 20px 24px; max-height: 85vh; overflow-y: auto; z-index: 1050; border-radius: var(--radius-lg); border: 1px solid var(--color-border); box-shadow: var(--shadow-float); background: var(--color-surface); margin: 0;">
                                <div style="color: var(--color-primary); font-size: 12px; font-weight: 700; text-transform: uppercase; letter-spacing: 0.04em; margin-bottom: 8px;">Tipe Aksi</div>
                                <div style="display: flex; flex-direction: column; gap: 8px; margin-bottom: 14px;">
                                    @php $curAction = request('action', ''); @endphp
                                    @foreach([
                                        ''       => 'Semua Aksi',
                                        'tambah' => 'Tambah Data',
                                        'ubah'   => 'Update Data',
                                        'hapus'  => 'Hapus Data',
                                        'export' => 'Ekspor Data'
                                    ] as $val => $label)
                                    @php
                                        $isChecked = ($curAction === $val) || ($val === 'ubah' && $curAction === 'update');
                                    @endphp
                                    <label style="cursor: pointer; display: flex; align-items: center; gap: 8px; font-size: 13px; color: var(--color-text); margin: 0;">
                                        <input type="radio" name="rAction" value="{{ $val }}" {{ $isChecked ? 'checked' : '' }} style="accent-color: var(--color-primary); cursor: pointer;">
                                        <span>{{ $label }}</span>
                                    </label>
                                    @endforeach
                                </div>

                                <hr style="border: none; border-top: 1px dashed var(--color-border); margin: 12px 0;" />

                                <div style="color: var(--color-primary); font-size: 12px; font-weight: 700; text-transform: uppercase; letter-spacing: 0.04em; margin-bottom: 8px;">Modul / Fitur</div>
                                <div style="display: flex; flex-direction: column; gap: 8px; margin-bottom: 14px;">
                                    @php $curModule = request('module', ''); @endphp
                                    @foreach([
                                        ''                => 'Semua Modul',
                                        'Inventaris'      => 'Buku Inventaris',
                                        'PenghapusanAset' => 'Penghapusan Aset',
                                        'Export'          => 'Pusat Ekspor',
                                        'JenisInventaris' => 'Jenis Inventaris',
                                        'Ruangan'         => 'Ruangan',
                                        'Bidang'          => 'Manajemen Bidang',
                                        'Pejabat'         => 'Manajemen Pejabat',
                                        'User'            => 'Manajemen Pengguna',
                                        'Dropdown'        => 'Dropdown Dinamis',
                                    ] as $val => $label)
                                    <label style="cursor: pointer; display: flex; align-items: center; gap: 8px; font-size: 13px; color: var(--color-text); margin: 0;">
                                        <input type="radio" name="rModule" value="{{ $val }}" {{ $curModule === $val ? 'checked' : '' }} style="accent-color: var(--color-primary); cursor: pointer;">
                                        <span>{{ $label }}</span>
                                    </label>
                                    @endforeach
                                </div>

                                <hr style="border: none; border-top: 1px dashed var(--color-border); margin: 12px 0;" />

                                <div style="color: var(--color-primary); font-size: 12px; font-weight: 700; text-transform: uppercase; letter-spacing: 0.04em; margin-bottom: 8px;">Urutan Waktu</div>
                                <div style="display: flex; flex-direction: column; gap: 8px; margin-bottom: 16px;">
                                    @php $curSort = request('sort', 'terbaru'); @endphp
                                    <label style="cursor: pointer; display: flex; align-items: center; gap: 8px; font-size: 13px; color: var(--color-text); margin: 0;">
                                        <input type="radio" name="rSort" value="terbaru" {{ $curSort === 'terbaru' ? 'checked' : '' }} style="accent-color: var(--color-primary); cursor: pointer;"> Terbaru
                                    </label>
                                    <label style="cursor: pointer; display: flex; align-items: center; gap: 8px; font-size: 13px; color: var(--color-text); margin: 0;">
                                        <input type="radio" name="rSort" value="terlama" {{ $curSort === 'terlama' ? 'checked' : '' }} style="accent-color: var(--color-primary); cursor: pointer;"> Terlama
                                    </label>
                                </div>

                                <div style="padding-top: 12px; border-top: 1px dashed var(--color-border);">
                                    <button type="button" onclick="applyActivityFilter(); event.stopPropagation();" class="btn-sima-primary" style="width: 100%; justify-content: center; font-size: 13px;">
                                        Terapkan Filter
                                    </button>
                                </div>
                            </div>
                        </div>

                        <!-- Hapus Semua Data (Superadmin only) -->
                        @if(($canDelete ?? false) && $logs->total() > 0)
                        <form id="deleteAllActivitiesForm" method="POST" action="{{ route('dashboard.activities.destroy-all') }}" style="margin:0;">
                            @csrf
                            <button type="button" class="btn-sima-danger" onclick="confirmDeleteAllActivities()" title="Hapus seluruh riwayat log">
                                <i data-feather="trash-2"></i>
                                <span>Hapus Semua</span>
                            </button>
                        </form>
                        @endif
                    </div>
                </div>

                <!-- Activity List / Table -->
                <div class="table-sima-wrapper table-loading-wrap" id="activities-list-container" style="border: none; border-radius: 0;">
                    @if($logs->isEmpty())
                        <div style="text-align: center; padding: 48px 20px; color: var(--color-text-muted);">
                            <div style="display: flex; flex-direction: column; align-items: center; justify-content: center; gap: 8px;">
                                <i data-feather="inbox" style="width: 42px; height: 42px; stroke-width: 1.5; color: var(--color-border-dark);"></i>
                                <p style="margin: 0; font-size: 13.5px; font-weight: 500;">Tidak ada rekaman aktivitas yang sesuai</p>
                            </div>
                        </div>
                    @else
                        <table class="table-sima">
                            <thead>
                                <tr>
                                    <th style="width: 60px; text-align: center;">No</th>
                                    <th style="width: 220px;">Pengguna</th>
                                    <th style="width: 130px; text-align: center;">Aktivitas</th>
                                    <th style="width: 170px;">Modul</th>
                                    <th style="min-width: 280px;">Rincian Aktivitas</th>
                                    <th style="width: 160px;">Waktu Log</th>
                                    @if($canDelete ?? false)
                                    <th style="width: 70px; text-align: center;">Aksi</th>
                                    @endif
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($logs as $index => $log)
                                @php
                                    $userInitial = strtoupper(substr($log->user?->nama ?? 'S', 0, 1));
                                @endphp
                                <tr>
                                    <td style="text-align: center; font-variant-numeric: tabular-nums; color: var(--color-text-muted);">
                                        {{ $logs->firstItem() + $index }}
                                    </td>
                                    <td>
                                        <div style="display: flex; align-items: center; gap: 9px;">
                                            <div style="width: 30px; height: 30px; border-radius: 50%; background: var(--color-primary-tint); color: var(--color-primary); font-weight: 700; font-size: 11.5px; display: flex; align-items: center; justify-content: center; flex-shrink: 0;">
                                                {{ $userInitial }}
                                            </div>
                                            <div style="min-width: 0;">
                                                <div style="font-weight: 600; color: var(--color-text); font-size: 13px; line-height: 1.2; white-space: nowrap; overflow: hidden; text-overflow: ellipsis;">
                                                    {{ $log->user?->nama ?? 'Sistem' }}
                                                </div>
                                                <div style="font-size: 11px; color: var(--color-text-muted); margin-top: 2px;">
                                                    {{ $log->user?->role_name_display ?: ($log->user ? ($log->user->role?->nama ? ucwords(str_replace('_', ' ', $log->user->role->nama)) : '-') : 'Sistem') }}
                                                </div>
                                            </div>
                                        </div>
                                    </td>
                                    <td style="text-align: center; font-size: 13px; font-weight: 500; color: var(--color-text);">
                                        {{ $log->action_label }}
                                    </td>
                                    <td>
                                        @if($log->module)
                                        <span style="font-size: 13px; font-weight: 500; color: var(--color-text);">
                                            {{ $log->module_label }}
                                        </span>
                                        @else
                                        <span style="color: var(--color-text-muted); font-size: 12px;">-</span>
                                        @endif
                                    </td>
                                    <td>
                                        <div style="font-size: 13px; color: var(--color-text); line-height: 1.4;">
                                            {{ $log->detail }}
                                        </div>
                                    </td>
                                    <td>
                                        <div style="font-size: 12.5px; font-weight: 500; color: var(--color-text);">
                                            {{ $log->created_at?->translatedFormat('d M Y') }}
                                        </div>
                                        <div style="font-size: 11px; color: var(--color-text-muted); font-variant-numeric: tabular-nums;">
                                            {{ $log->created_at?->format('H:i') }} WITA
                                        </div>
                                    </td>
                                    @if($canDelete ?? false)
                                    <td style="text-align: center;">
                                        <form id="deleteActivityForm{{ $log->id }}" method="POST" action="{{ route('dashboard.activities.destroy', $log->id) }}" style="display: inline-block; margin: 0;">
                                            @csrf
                                            @method('DELETE')
                                            <button type="button" class="btn-sima-icon delete" onclick="confirmDeleteActivity({{ $log->id }})" title="Hapus Log">
                                                <i data-feather="trash-2"></i>
                                            </button>
                                        </form>
                                    </td>
                                    @endif
                                </tr>
                                @endforeach
                            </tbody>
                        </table>
                    @endif
                </div>

                {{-- Standard 3-Column Pagination Bar Footer --}}
                <div class="pagination-bar" id="paginationBar" style="padding: 32px 20px; border-top: 1px solid var(--color-border); background: var(--color-surface); flex-wrap: wrap; gap: 14px; border-bottom-left-radius: var(--radius-lg); border-bottom-right-radius: var(--radius-lg);">
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
                                <span class="page-btn disabled" title="Sebelumnya"><i data-feather="chevron-left"></i></span>
                            @else
                                <button type="button" class="page-btn" onclick="goToActivitiesPage({{ $logs->currentPage() - 1 }})" title="Sebelumnya"><i data-feather="chevron-left"></i></button>
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
                                <button type="button" class="page-btn" onclick="goToActivitiesPage({{ $logs->currentPage() + 1 }})" title="Selanjutnya"><i data-feather="chevron-right"></i></button>
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

    @include('layouts.footer-block')
    @include('layouts.footer-js')

    <script>
        let _activitySearchTimer = null;

        function toggleSearchClear(input) {
            const clearBtn = document.getElementById('clear-search-btn');
            if (clearBtn) clearBtn.style.display = input.value ? 'block' : 'none';
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
            try {
                if (query) {
                    sessionStorage.setItem('activities_filter_state', query);
                } else {
                    sessionStorage.removeItem('activities_filter_state');
                }
            } catch(e) {}

            const targetUrl = '{{ route('dashboard.activities') }}' + (query ? ('?' + query) : '');

            history.replaceState(null, '', targetUrl);

            if (_activitiesFetchController) {
                _activitiesFetchController.abort();
            }
            _activitiesFetchController = new AbortController();

            const listContainer = document.getElementById('activities-list-container');
            if (!isAutoRefresh && listContainer) listContainer.classList.add('is-loading');

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
            })
            .finally(() => {
                if (listContainer) listContainer.classList.remove('is-loading');
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

        function toggleFilterPopover(e) {
            if (e) {
                e.preventDefault();
                e.stopPropagation();
            }
            const menu = document.getElementById('filter-dropdown-menu');
            if (!menu) return;
            const isShown = menu.style.display === 'block';
            menu.style.display = isShown ? 'none' : 'block';
        }

        function closeFilterPopover() {
            const menu = document.getElementById('filter-dropdown-menu');
            if (menu) menu.style.display = 'none';
        }

        document.addEventListener('click', function(e) {
            const container = document.getElementById('filter-dropdown-container');
            if (container && !container.contains(e.target)) {
                closeFilterPopover();
            }
        });

        window.toggleFilterPopover = toggleFilterPopover;
        window.closeFilterPopover = closeFilterPopover;

        function applyActivityFilter() {
            fetchActivitiesData(false, 1);
            closeFilterPopover();
            document.querySelectorAll('.dropdown-menu.show').forEach(el => el.classList.remove('show'));
            document.querySelectorAll('.dropdown.show, .dropdown.drp-show').forEach(el => {
                el.classList.remove('show');
                el.classList.remove('drp-show');
            });
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

            try {
                sessionStorage.removeItem('activities_filter_state');
            } catch(e) {}

            toggleSearchClear(input || { value: '' });
            closeFilterPopover();
            fetchActivitiesData(false, 1);
            document.querySelectorAll('.dropdown-menu.show').forEach(el => el.classList.remove('show'));
            document.querySelectorAll('.dropdown.show, .dropdown.drp-show').forEach(el => {
                el.classList.remove('show');
                el.classList.remove('drp-show');
            });
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
                AppModal.show({
                    type: 'danger',
                    title: 'Hapus Semua Aktivitas',
                    message: 'Apakah Anda yakin ingin menghapus semua data aktivitas? Aksi ini tidak dapat dibatalkan.',
                    confirmText: 'Hapus Semua',
                    cancelText: 'Batal',
                    showCancel: true,
                    onConfirm: submitAction
                });
                return;
            }

            if (confirm('Apakah Anda yakin ingin menghapus semua data aktivitas?')) {
                submitAction();
            }
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
                sessionStorage.removeItem('activities_filter_state');
            } catch(e) {}
            @endif

            let isReturning = false;
            try {
                if (sessionStorage.getItem('activities_returning') === '1') {
                    isReturning = true;
                    sessionStorage.removeItem('activities_returning');
                }
            } catch(e) {}

            const ref = document.referrer || '';
            if (ref.includes('/activities') || ref.includes('/dashboard/activities')) {
                isReturning = true;
            }
            if (navType === 'back_forward') {
                isReturning = true;
            }

            if (navType === 'reload' && !isReturning) {
                try {
                    sessionStorage.removeItem('activities_filter_state');
                } catch(e) {}
                if (window.location.search) {
                    history.replaceState(null, '', window.location.pathname);
                }
                const input = document.getElementById('searchInput');
                if (input) input.value = '';
                toggleSearchClear(input || { value: '' });

                const actionDefault = document.querySelector('input[name="rAction"][value=""]');
                const moduleDefault = document.querySelector('input[name="rModule"][value=""]');
                const sortDefault   = document.querySelector('input[name="rSort"][value="terbaru"]');
                if (actionDefault) actionDefault.checked = true;
                if (moduleDefault) moduleDefault.checked = true;
                if (sortDefault) sortDefault.checked = true;

                const perPageSelect = document.getElementById('perPageSelect');
                if (perPageSelect) perPageSelect.value = '10';

                fetchActivitiesData(true, 1);
                return;
            }

            if (!isReturning && navType === 'navigate' && !window.location.search) {
                try {
                    sessionStorage.removeItem('activities_filter_state');
                } catch(e) {}
                return;
            }

            let currentSearch = window.location.search;
            let hasUrlQuery = (currentSearch && currentSearch.length > 1);

            if (!hasUrlQuery) {
                try {
                    const savedState = sessionStorage.getItem('activities_filter_state');
                    if (savedState && savedState.length > 0) {
                        currentSearch = '?' + savedState;
                        history.replaceState(null, '', window.location.pathname + currentSearch);
                        hasUrlQuery = true;
                    }
                } catch(e) {}
            }

            if (!hasUrlQuery) return;

            const params = new URLSearchParams(currentSearch);
            const search = params.get('search') || '';
            const input = document.getElementById('searchInput');
            if (input && search) {
                input.value = search;
                toggleSearchClear(input);
            }

            const actionVal = params.get('action') || '';
            const actionRadio = document.querySelector(`input[name="rAction"][value="${actionVal}"]`);
            if (actionRadio) actionRadio.checked = true;

            const moduleVal = params.get('module') || '';
            const moduleRadio = document.querySelector(`input[name="rModule"][value="${moduleVal}"]`);
            if (moduleRadio) moduleRadio.checked = true;

            const sortVal = params.get('sort') || 'terbaru';
            const sortRadio = document.querySelector(`input[name="rSort"][value="${sortVal}"]`);
            if (sortRadio) sortRadio.checked = true;

            const perPage = params.get('per_page') || '10';
            const perPageSelect = document.getElementById('perPageSelect');
            if (perPageSelect) perPageSelect.value = perPage;

            const page = parseInt(params.get('page') || '1', 10);
            fetchActivitiesData(true, page);
        }

        (function() {
            restoreFilterState();
            const input = document.getElementById('searchInput');
            if (input) toggleSearchClear(input);
            bindActivitiesPageJump();
            if (typeof feather !== 'undefined') feather.replace();
        })();

        const activitiesAutoRefreshMs = 15000;
        setInterval(function () {
            if (document.hidden) return;
            fetchActivitiesData(true);
        }, activitiesAutoRefreshMs);
    </script>
</body>
</html>
