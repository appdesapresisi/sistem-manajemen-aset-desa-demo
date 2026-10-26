<!DOCTYPE html>
<html lang="id">
<head>
    @include('layouts.head-page-meta', ['title' => 'Buku Inventaris - Sistem Manajemen Aset Desa'])
    @include('layouts.head-css')
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/flatpickr/dist/flatpickr.min.css">
    <script src="https://cdn.jsdelivr.net/npm/feather-icons/dist/feather.min.js"></script>
    <style>
        /* Year Only Picker Styling (Prodex / Custora Theme) */
        .flatpickr-calendar.year-only-picker {
            width: 250px !important;
            background: var(--color-surface);
            border-radius: var(--radius-md);
            box-shadow: var(--shadow-float);
            border: 1px solid var(--color-border);
            overflow: hidden;
            z-index: 1060 !important;
        }
        .flatpickr-calendar.year-only-picker .flatpickr-innerContainer,
        .flatpickr-calendar.year-only-picker .flatpickr-weekdays,
        .flatpickr-calendar.year-only-picker .flatpickr-days,
        .flatpickr-calendar.year-only-picker .flatpickr-monthDropdown-months,
        .flatpickr-calendar.year-only-picker .flatpickr-prev-month,
        .flatpickr-calendar.year-only-picker .flatpickr-next-month,
        .flatpickr-calendar.year-only-picker .flatpickr-months {
            display: none !important;
        }
        .flatpickr-calendar.year-only-picker .year-picker-panel {
            padding: 12px;
        }
        .flatpickr-calendar.year-only-picker .year-picker-all-btn {
            width: 100%;
            padding: 7px 10px;
            border: 1px solid var(--color-primary);
            border-radius: var(--radius-full);
            background: var(--color-primary-tint);
            color: var(--color-primary);
            font-size: 12px;
            font-weight: 600;
            cursor: pointer;
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 6px;
            transition: all 0.15s;
            margin-bottom: 10px;
        }
        .flatpickr-calendar.year-only-picker .year-picker-all-btn:hover {
            background: #d4dfbf;
        }
        .flatpickr-calendar.year-only-picker .year-picker-header {
            display: flex;
            align-items: center;
            justify-content: space-between;
            margin-bottom: 10px;
        }
        .flatpickr-calendar.year-only-picker .year-picker-range {
            font-size: 13px;
            font-weight: 700;
            color: var(--color-primary);
        }
        .flatpickr-calendar.year-only-picker .year-picker-nav {
            width: 26px;
            height: 26px;
            border-radius: var(--radius-sm, 6px);
            border: none;
            background: transparent;
            color: var(--color-text);
            font-size: 16px;
            line-height: 1;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            cursor: pointer;
            transition: all 0.15s;
        }
        .flatpickr-calendar.year-only-picker .year-picker-nav:hover:not(:disabled) {
            background: var(--color-row-hover);
        }
        .flatpickr-calendar.year-only-picker .year-picker-nav:disabled {
            opacity: .45;
            cursor: not-allowed;
        }
        .flatpickr-calendar.year-only-picker .year-picker-grid {
            display: grid;
            grid-template-columns: repeat(3, minmax(0, 1fr));
            gap: 6px;
        }
        .flatpickr-calendar.year-only-picker .year-picker-option {
            border: 1px solid var(--color-border);
            border-radius: var(--radius-sm);
            background: var(--color-surface);
            color: var(--color-text);
            font-size: 12px;
            font-weight: 600;
            padding: 8px 0;
            cursor: pointer;
            transition: all 0.15s;
            text-align: center;
        }
        .flatpickr-calendar.year-only-picker .year-picker-option:hover {
            background: var(--color-row-hover);
        }
        .flatpickr-calendar.year-only-picker .year-picker-option.active {
            background: var(--color-primary);
            border-color: var(--color-primary);
            color: var(--color-text-on-primary);
        }

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

        /* SIMA Import Premium Loading & Entrance Animations */
        .sima-orbital-loader {
            position: relative;
            width: 68px;
            height: 68px;
            display: flex;
            align-items: center;
            justify-content: center;
        }
        .sima-orbital-ring {
            position: absolute;
            inset: 0;
            border-radius: 50%;
            border: 2.5px solid var(--color-border);
            border-top-color: var(--color-primary);
            border-right-color: var(--color-primary);
            animation: simaOrbitSpin 0.9s cubic-bezier(0.4, 0.1, 0.3, 0.9) infinite;
        }
        .sima-orbital-core {
            width: 48px;
            height: 48px;
            border-radius: 50%;
            background: var(--color-surface);
            display: flex;
            align-items: center;
            justify-content: center;
            box-shadow: 0 2px 8px rgba(0, 0, 0, 0.04);
            animation: simaCorePulse 1.8s ease-in-out infinite;
        }
        @keyframes simaOrbitSpin {
            0% { transform: rotate(0deg); }
            100% { transform: rotate(360deg); }
        }
        @keyframes simaCorePulse {
            0%, 100% { transform: scale(1); opacity: 0.9; }
            50% { transform: scale(1.06); opacity: 1; }
        }
        .sima-progress-track {
            width: 220px;
            height: 4px;
            border-radius: 9999px;
            background: var(--color-border);
            overflow: hidden;
            position: relative;
            margin-top: 14px;
        }
        .sima-progress-indicator {
            position: absolute;
            top: 0;
            left: 0;
            height: 100%;
            border-radius: 9999px;
            background: var(--color-primary);
            animation: simaIndeterminateBar 1.6s cubic-bezier(0.4, 0, 0.2, 1) infinite;
        }
        @keyframes simaIndeterminateBar {
            0% {
                left: -35%;
                width: 35%;
            }
            60% {
                left: 100%;
                width: 50%;
            }
            100% {
                left: 100%;
                width: 50%;
            }
        }

        /* Result State Entrance Animation */
        .sima-result-appear {
            animation: simaResultSlideUp 0.42s cubic-bezier(0.16, 1, 0.3, 1) forwards;
        }
        @keyframes simaResultSlideUp {
            0% {
                opacity: 0;
                transform: translateY(12px) scale(0.985);
            }
            100% {
                opacity: 1;
                transform: translateY(0) scale(1);
            }
        }
        .sima-fade-in {
            animation: simaFadeIn 0.35s cubic-bezier(0.16, 1, 0.3, 1) forwards;
        }
        @keyframes simaFadeIn {
            0% {
                opacity: 0;
                transform: translateY(6px);
            }
            100% {
                opacity: 1;
                transform: translateY(0);
            }
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
                        Buku Inventaris Aset Desa
                    </h1>
                </div>
                <div class="page-header-actions" style="display: flex; gap: 10px; align-items: center; flex-wrap: wrap;">    
                    @can('create', \App\Models\Inventaris::class)
                    <button type="button" class="btn-sima-secondary btn-sima-pill-white" onclick="openImportModal()">
                        <i data-feather="upload-cloud" style="width: 14px; height: 14px;"></i>
                        <span>Import CSV</span>
                    </button>
                    @endcan
                    <a href="{{ route('manajemen-aset.create') }}" class="btn-sima-primary">
                        <i data-feather="plus" style="width: 14px; height: 14px;"></i>
                        <span>Tambah Inventaris</span>
                    </a>
                </div>
            </div>

            <!-- 1. STAT CARDS (Prodex 4-Card Summary Bar) -->
            <div class="stat-card-row" id="section-stat-inventaris" data-search-title="Ringkasan Statistik Inventaris">
                <!-- Card 1: Total Aset -->
                <div class="stat-card-sima is-clickable is-active-filter" id="card-kondisi-total" onclick="filterByCardKondisi('all')" title="Klik untuk menampilkan semua aset">
                    <div class="stat-header">
                        <span style="font-weight: 600;">TOTAL INVENTARIS</span>
                    </div>
                    <div class="stat-number stat-card-value">{{ number_format($stats['totalInventaris'] ?? 0, 0, ',', '.') }}</div>
                    <div class="kpi-trend-wrapper stat-delta">
                        @php 
                            $tTot = $stats['trends']['total']['percent'] ?? 0;
                            $dTot = $stats['trends']['total']['diff'] ?? 0;
                        @endphp
                        @if($tTot > 0 || $dTot > 0)
                            <span class="stat-delta up">
                                <i data-feather="trending-up" style="width: 12px; height: 12px;"></i>
                                +{{ $tTot }}% (+{{ number_format($dTot, 0, ',', '.') }})
                            </span>
                        @elseif($tTot < 0 || $dTot < 0)
                            <span class="stat-delta down">
                                <i data-feather="trending-down" style="width: 12px; height: 12px;"></i>
                                {{ $tTot }}% ({{ number_format($dTot, 0, ',', '.') }})
                            </span>
                        @else
                            <span class="stat-delta" style="color: var(--color-text-muted);">0% (0)</span>
                        @endif
                        <span class="kpi-trend-label" style="color: var(--color-text-muted); font-size: 11.5px; margin-left: 4px;">vs. bulan lalu</span>
                    </div>
                </div>

                <!-- Card 2: Kondisi Baik -->
                <div class="stat-card-sima is-clickable" id="card-kondisi-baik" onclick="filterByCardKondisi('baik')" title="Klik untuk memfilter Kondisi Baik">
                    <div class="stat-header">
                        <span style="font-weight: 600;">KONDISI BAIK</span>
                    </div>
                    <div class="stat-number stat-card-value">{{ number_format($stats['kondisiBaik'] ?? 0, 0, ',', '.') }}</div>
                    <div class="kpi-trend-wrapper stat-delta">
                        @php 
                            $tBaik = $stats['trends']['baik']['percent'] ?? 0;
                            $dBaik = $stats['trends']['baik']['diff'] ?? 0;
                        @endphp
                        @if($tBaik > 0 || $dBaik > 0)
                            <span class="stat-delta up">
                                <i data-feather="trending-up" style="width: 12px; height: 12px;"></i>
                                +{{ $tBaik }}% (+{{ number_format($dBaik, 0, ',', '.') }})
                            </span>
                        @elseif($tBaik < 0 || $dBaik < 0)
                            <span class="stat-delta down">
                                <i data-feather="trending-down" style="width: 12px; height: 12px;"></i>
                                {{ $tBaik }}% ({{ number_format($dBaik, 0, ',', '.') }})
                            </span>
                        @else
                            <span class="stat-delta" style="color: var(--color-text-muted);">0% (0)</span>
                        @endif
                        <span class="kpi-trend-label" style="color: var(--color-text-muted); font-size: 11.5px; margin-left: 4px;">vs. bulan lalu</span>
                    </div>
                </div>

                <!-- Card 3: Rusak Ringan -->
                <div class="stat-card-sima is-clickable" id="card-kondisi-rusak_ringan" onclick="filterByCardKondisi('rusak_ringan')" title="Klik untuk memfilter Rusak Ringan">
                    <div class="stat-header">
                        <span style="font-weight: 600;">RUSAK RINGAN</span>
                    </div>
                    <div class="stat-number stat-card-value">{{ number_format($stats['kondisiRusakRingan'] ?? 0, 0, ',', '.') }}</div>
                    <div class="kpi-trend-wrapper stat-delta">
                        @php 
                            $tRr = $stats['trends']['rr']['percent'] ?? 0;
                            $dRr = $stats['trends']['rr']['diff'] ?? 0;
                        @endphp
                        @if($tRr > 0 || $dRr > 0)
                            <span class="stat-delta up">
                                <i data-feather="trending-up" style="width: 12px; height: 12px;"></i>
                                +{{ $tRr }}% (+{{ number_format($dRr, 0, ',', '.') }})
                            </span>
                        @elseif($tRr < 0 || $dRr < 0)
                            <span class="stat-delta down">
                                <i data-feather="trending-down" style="width: 12px; height: 12px;"></i>
                                {{ $tRr }}% ({{ number_format($dRr, 0, ',', '.') }})
                            </span>
                        @else
                            <span class="stat-delta" style="color: var(--color-text-muted);">0% (0)</span>
                        @endif
                        <span class="kpi-trend-label" style="color: var(--color-text-muted); font-size: 11.5px; margin-left: 4px;">vs. bulan lalu</span>
                    </div>
                </div>

                <!-- Card 4: Rusak Berat -->
                <div class="stat-card-sima is-clickable" id="card-kondisi-rusak_berat" onclick="filterByCardKondisi('rusak_berat')" title="Klik untuk memfilter Rusak Berat">
                    <div class="stat-header">
                        <span style="font-weight: 600;">RUSAK BERAT</span>
                    </div>
                    <div class="stat-number stat-card-value">{{ number_format($stats['kondisiRusakBerat'] ?? 0, 0, ',', '.') }}</div>
                    <div class="kpi-trend-wrapper stat-delta">
                        @php 
                            $tRb = $stats['trends']['rb']['percent'] ?? 0;
                            $dRb = $stats['trends']['rb']['diff'] ?? 0;
                        @endphp
                        @if($tRb > 0 || $dRb > 0)
                            <span class="stat-delta up">
                                <i data-feather="trending-up" style="width: 12px; height: 12px;"></i>
                                +{{ $tRb }}% (+{{ number_format($dRb, 0, ',', '.') }})
                            </span>
                        @elseif($tRb < 0 || $dRb < 0)
                            <span class="stat-delta down">
                                <i data-feather="trending-down" style="width: 12px; height: 12px;"></i>
                                {{ $tRb }}% ({{ number_format($dRb, 0, ',', '.') }})
                            </span>
                        @else
                            <span class="stat-delta" style="color: var(--color-text-muted);">0% (0)</span>
                        @endif
                        <span class="kpi-trend-label" style="color: var(--color-text-muted); font-size: 11.5px; margin-left: 4px;">vs. bulan lalu</span>
                    </div>
                </div>
            </div>

            <!-- 2. STATUS TABS (Underline Pattern) -->
            <div class="tabs-sima" id="section-tabs-kondisi" data-search-title="Tab Filter Kondisi Aset">
                <div class="tab-item-sima active" id="tab-status-all" onclick="filterByCardKondisi('all')">
                    <span>Semua Aset</span>
                    <span class="tab-badge-sima">{{ number_format($stats['totalInventaris'] ?? 0, 0, ',', '.') }}</span>
                </div>
                <div class="tab-item-sima" id="tab-status-baik" onclick="filterByCardKondisi('baik')">
                    <span>Kondisi Baik</span>
                    <span class="tab-badge-sima" style="background: var(--color-success-soft); color: var(--color-success);">{{ number_format($stats['kondisiBaik'] ?? 0, 0, ',', '.') }}</span>
                </div>
                <div class="tab-item-sima" id="tab-status-rusak_ringan" onclick="filterByCardKondisi('rusak_ringan')">
                    <span>Rusak Ringan</span>
                    <span class="tab-badge-sima" style="background: var(--color-warning-soft); color: var(--color-warning);">{{ number_format($stats['kondisiRusakRingan'] ?? 0, 0, ',', '.') }}</span>
                </div>
                <div class="tab-item-sima" id="tab-status-rusak_berat" onclick="filterByCardKondisi('rusak_berat')">
                    <span>Rusak Berat</span>
                    <span class="tab-badge-sima" style="background: var(--color-danger-soft); color: var(--color-danger);">{{ number_format($stats['kondisiRusakBerat'] ?? 0, 0, ',', '.') }}</span>
                </div>
            </div>

            <!-- 3. TABLE CONTAINER (Prodex Table Standard) -->
            <div class="sima-card" id="section-tabel-inventaris" data-search-title="Tabel Buku Inventaris Aset" style="padding: 0; overflow: visible; margin-bottom: 32px;">
                
                <!-- Action Controls Bar -->
                <div style="padding: 20px 20px; border-bottom: 1px solid var(--color-border); border-top-left-radius: var(--radius-lg); border-top-right-radius: var(--radius-lg); display: flex; justify-content: space-between; align-items: center; flex-wrap: wrap; gap: 12px;">
                    <div style="display: flex; align-items: center; gap: 10px; flex: 1; max-width: 480px;">
                        <div class="topbar-search-box" style="background: var(--color-surface);">
                            <i data-feather="search" style="width: 15px; height: 15px; color: var(--color-text-muted); flex-shrink: 0;"></i>
                            <input type="text" id="searchInput" class="topbar-search-input" placeholder="Cari nama barang, kode, atau NUP..." oninput="onSearchInput(this)" onkeypress="if(event.key==='Enter'){doSearch();}" value="{{ request('search') }}" />
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
                            <div id="filter-dropdown-menu" class="sima-filter-popover" onclick="event.stopPropagation();" style="display: none; position: absolute; top: calc(100% + 4px); right: 0; min-width: 330px; padding: 20px 24px; max-height: 85vh; overflow-y: auto; z-index: 1050; border-radius: var(--radius-lg); border: 1px solid var(--color-border); box-shadow: var(--shadow-float); background: var(--color-surface); margin: 0;">
                                <div style="font-size: 13px; font-weight: 700; color: var(--color-primary); margin-bottom: 8px;">Klasifikasi (KIB)</div>
                                <div style="display: flex; flex-direction: column; gap: 6px; margin-bottom: 12px;" id="jenisFilterList">
                                    @foreach($jenisList as $jenis)
                                    <label style="cursor: pointer; display: flex; align-items: center; gap: 8px; font-size: 13px; color: var(--color-text);">
                                        <input type="checkbox" name="jenis[]" value="{{ $jenis->id }}" class="jenis-filter sima-checkbox">
                                        <span>{{ $jenis->nama }}</span>
                                    </label>
                                    @endforeach
                                </div>
                                
                                <hr style="border: none; border-top: 1px solid var(--color-border); margin: 12px 0;" />
                                
                                <div style="font-size: 13px; font-weight: 700; color: var(--color-primary); margin-bottom: 8px;">Tahun Perolehan</div>
                                <div style="margin-bottom: 12px; position: relative;" id="tahunFilterContainer">
                                    <input type="text" id="tahunFilter" class="form-control-sima" placeholder="Semua Tahun" readonly
                                        style="font-size: 13px; padding-right: 32px; cursor: pointer;"
                                        value="{{ request('tahun') && request('tahun') !== 'all' ? request('tahun') : '' }}" />
                                    <button type="button" id="clearTahunFilterBtn" title="Reset ke Semua Tahun"
                                        style="position: absolute; right: 8px; top: 50%; transform: translateY(-50%); background: none; border: none; color: var(--color-text-muted); cursor: pointer; padding: 2px; display: {{ request('tahun') && request('tahun') !== 'all' ? 'inline-flex' : 'none' }}; align-items: center; justify-content: center;">
                                        <i data-feather="x" style="width: 14px; height: 14px;"></i>
                                    </button>
                                </div>

                                <div style="font-size: 13px; font-weight: 700; color: var(--color-primary); margin-bottom: 8px;">Ruangan / Lokasi</div>
                                <div style="margin-bottom: 12px;">
                                    <select id="ruanganFilter" class="form-select-sima" style="font-size: 13px; cursor: pointer;">
                                        <option value="all">Semua Ruangan</option>
                                        <option value="none">Tanpa Ruangan</option>
                                        @foreach($ruanganList as $rg)
                                            <option value="{{ $rg->id }}">{{ $rg->nama_with_kode }}</option>
                                        @endforeach
                                    </select>
                                </div>

                                <hr style="border: none; border-top: 1px solid var(--color-border); margin: 12px 0;" />

                                <div style="font-size: 13px; font-weight: 700; color: var(--color-primary); margin-bottom: 8px;">Kondisi Aset</div>
                                <div style="display: flex; flex-direction: column; gap: 6px; margin-bottom: 12px;" id="kondisiFilterList">
                                    <label style="cursor: pointer; display: flex; align-items: center; gap: 8px; font-size: 13px; color: var(--color-text);">
                                        <input type="checkbox" name="kondisi[]" value="baik" class="kondisi-filter sima-checkbox"> 
                                        <span>Baik</span>
                                    </label>
                                    <label style="cursor: pointer; display: flex; align-items: center; gap: 8px; font-size: 13px; color: var(--color-text);">
                                        <input type="checkbox" name="kondisi[]" value="rusak_ringan" class="kondisi-filter sima-checkbox"> 
                                        <span>Rusak Ringan</span>
                                    </label>
                                    <label style="cursor: pointer; display: flex; align-items: center; gap: 8px; font-size: 13px; color: var(--color-text);">
                                        <input type="checkbox" name="kondisi[]" value="rusak_berat" class="kondisi-filter sima-checkbox"> 
                                        <span>Rusak Berat</span>
                                    </label>
                                </div>

                                <hr style="border: none; border-top: 1px solid var(--color-border); margin: 12px 0;" />

                                <div style="font-size: 13px; font-weight: 700; color: var(--color-primary); margin-bottom: 8px;">Kelengkapan Data Aset</div>
                                <div style="display: flex; flex-direction: column; gap: 6px; margin-bottom: 12px;" id="kelengkapanFilterList">
                                    <label style="cursor: pointer; display: flex; align-items: center; gap: 8px; font-size: 13px; color: var(--color-text);">
                                        <input type="radio" name="kelengkapanFilter" value="all" class="kelengkapan-filter" {{ (!request('kelengkapan') || request('kelengkapan') === 'all') ? 'checked' : '' }} style="accent-color: var(--color-primary);"> 
                                        <span>Semua Status Kelengkapan</span>
                                    </label>
                                    <label style="cursor: pointer; display: flex; align-items: center; gap: 8px; font-size: 13px; color: var(--color-text);">
                                        <input type="radio" name="kelengkapanFilter" value="lengkap" class="kelengkapan-filter" {{ request('kelengkapan') === 'lengkap' ? 'checked' : '' }} style="accent-color: var(--color-primary);"> 
                                        <span>Data Lengkap</span>
                                    </label>
                                    <label style="cursor: pointer; display: flex; align-items: center; gap: 8px; font-size: 13px; color: var(--color-text);">
                                        <input type="radio" name="kelengkapanFilter" value="tidak_lengkap" class="kelengkapan-filter" {{ request('kelengkapan') === 'tidak_lengkap' ? 'checked' : '' }} style="accent-color: var(--color-primary);"> 
                                        <span>Perlu Dilengkapi (Tidak Lengkap)</span>
                                    </label>
                                </div>

                                <hr style="border: none; border-top: 1px dashed var(--color-border); margin: 12px 0;" />

                                <div style="font-size: 13px; font-weight: 700; color: var(--color-primary); margin-bottom: 8px;">Urutan Tampilan</div>
                                <div style="display: flex; flex-direction: column; gap: 6px; margin-bottom: 14px;">
                                    <label style="cursor: pointer; display: flex; align-items: center; gap: 8px; font-size: 13px; color: var(--color-text);">
                                        <input type="radio" name="sortFilter" value="nama_asc" class="sort-filter" {{ request('sort') === 'nama_asc' ? 'checked' : '' }} style="accent-color: var(--color-primary);"> 
                                        <span>Nama A-Z</span>
                                    </label>
                                    <label style="cursor: pointer; display: flex; align-items: center; gap: 8px; font-size: 13px; color: var(--color-text);">
                                        <input type="radio" name="sortFilter" value="nama_desc" class="sort-filter" {{ request('sort') === 'nama_desc' ? 'checked' : '' }} style="accent-color: var(--color-primary);"> 
                                        <span>Nama Z-A</span>
                                    </label>
                                    <label style="cursor: pointer; display: flex; align-items: center; gap: 8px; font-size: 13px; color: var(--color-text);">
                                        <input type="radio" name="sortFilter" value="terbaru" class="sort-filter" {{ (!request('sort') || request('sort') === 'terbaru') ? 'checked' : '' }} style="accent-color: var(--color-primary);"> 
                                        <span>Terbaru Terdaftar</span>
                                    </label>
                                    <label style="cursor: pointer; display: flex; align-items: center; gap: 8px; font-size: 13px; color: var(--color-text);">
                                        <input type="radio" name="sortFilter" value="terlama" class="sort-filter" {{ request('sort') === 'terlama' ? 'checked' : '' }} style="accent-color: var(--color-primary);"> 
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

                <!-- Floating Bulk Action Bar (Bawah Melayang) -->
                <div id="bulk-bar" class="bulk-action-bar-sima">
                    <span class="bulk-count"><span id="bulk-count">0</span> aset dipilih</span>
                    <span id="bulk-warn" style="display:none; font-size:11px; color: var(--color-warning); background: var(--color-warning-soft); border-radius: 4px; padding: 2px 6px;">&#9888; Maks 100 aset</span>
                    <div class="bulk-divider"></div>
                    <button type="button" id="btn-salin-data" onclick="bulkDuplicateConfirm()" class="btn-sima-primary" style="padding: 5px 12px; font-size: 12.5px;">
                        <i data-feather="copy" style="width: 13px; height: 13px;"></i>
                        <span>Salin Data</span>
                    </button>
                    <button type="button" id="btn-bulk-print-qr" onclick="bulkPrintQr()" class="btn-sima-secondary" style="padding: 5px 12px; font-size: 12.5px; background: var(--color-surface); border: 1px solid var(--color-border); color: var(--color-primary); font-weight: 600;">
                        <i data-feather="printer" style="width: 13px; height: 13px;"></i>
                        <span>Cetak Label QR</span>
                    </button>
                    @if(auth()->user()->canDeleteAset())
                    <button type="button" onclick="bulkDeleteConfirm()" class="btn-sima-danger" style="padding: 5px 12px; font-size: 12.5px;">
                        <i data-feather="trash-2" style="width: 13px; height: 13px;"></i>
                        <span>Hapus Aset</span>
                    </button>
                    @endif
                    <button type="button" onclick="clearSelection()" class="btn-sima-icon" style="width: 28px; height: 28px;" title="Batal Pilih">
                        <i data-feather="x" style="width: 13px; height: 13px;"></i>
                    </button>
                </div>
                <form id="bulk-delete-form" method="POST" action="{{ route('manajemen-aset.bulk-destroy') }}" style="display:none;">@csrf</form>

                <!-- Data Table (Clean, Maximum 6 Columns) -->
                <div class="table-sima-wrapper table-loading-wrap">
                    <table class="table-sima">
                        <thead>
                            <tr>
                                <th style="width: 44px; text-align: center;">
                                    <input type="checkbox" id="check-all" class="sima-checkbox" onchange="toggleAll(this)">
                                </th>
                                <th style="width: 50px; text-align: center;">No</th>
                                <th style="min-width: 260px;">Nama Barang & Kode (NUP)</th>
                                <th>Jenis Inventaris</th>
                                <th>Ruangan</th>
                                <th>Tahun</th>
                                <th class="num-col">Nilai Perolehan</th>
                                <th style="text-align: center;">Status Kondisi</th>
                                <th style="width: 120px; text-align: center;">Aksi</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($inventaris as $index => $item)
                            <tr>
                                <td style="text-align: center;">
                                    <input type="checkbox" class="row-check sima-checkbox" value="{{ $item->id }}" onchange="updateBulkBar()">
                                </td>
                                <td style="text-align: center; color: var(--color-text-muted); font-size: 13px; font-variant-numeric: tabular-nums;">
                                    {{ $inventaris->firstItem() + $index }}
                                </td>
                                <td>
                                    <div style="font-weight: 600; color: var(--color-text); font-size: 14px; line-height: 1.3;">
                                        {{ $item->display_name ?? $item->nama_barang ?? '-' }}
                                    </div>
                                    <div style="display: flex; align-items: center; gap: 6px; font-size: 12px; color: var(--color-text-muted); margin-top: 3px;">
                                        <code>{{ $item->kode_barang ?? '-' }}</code>
                                        <span>&bull;</span>
                                        <span>NUP: <strong style="color: var(--color-text);">{{ $item->nup ?? '-' }}</strong></span>
                                    </div>
                                    @if(!$item->isDataLengkap())
                                        @php
                                            $missing = $item->getMissingRequiredFields();
                                            $missingText = !empty($missing) ? implode(', ', $missing) : 'Data wajib belum lengkap';
                                        @endphp
                                        <div style="margin-top: 4px;">
                                            <span class="status-pill status-pill-warning" style="font-size: 11px; padding: 2px 7px; display: inline-flex; align-items: center; gap: 4px;" title="Belum lengkap: {{ $missingText }}">
                                                <i data-feather="alert-circle" style="width: 12px; height: 12px;"></i>
                                                Perlu Dilengkapi
                                            </span>
                                        </div>
                                    @endif
                                </td>
                                <td>
                                    @php
                                        $jenis = $item->jenis;
                                    @endphp
                                    <span style="font-size: 13px; color: var(--color-text); font-weight: 500;">
                                        {{ $jenis->nama ?? 'Inventaris' }}
                                    </span>
                                </td>
                                <td>
                                    @if($item->ruangan)
                                        <span style="font-size: 13px; color: var(--color-text); font-weight: 500;">{{ $item->ruangan->nama_with_kode }}</span>
                                    @else
                                        <span style="font-size: 13px; color: var(--color-text-muted);">-</span>
                                    @endif
                                </td>
                                <td style="font-size: 13px; color: var(--color-text-muted); font-variant-numeric: tabular-nums;">
                                    {{ $item->tahun_perolehan ?? $item->tahun_pengadaan ?? '-' }}
                                </td>
                                <td class="num-col" style="font-weight: 600; color: var(--color-text);">
                                    Rp {{ number_format($item->nilai_perolehan ?? 0, 0, ',', '.') }}
                                </td>
                                <td style="text-align: center;">
                                    @php
                                        $knd = strtolower($item->kondisi ?? 'baik');
                                    @endphp
                                    @if($knd == 'baik')
                                        <span class="status-pill status-pill-success">Baik</span>
                                    @elseif(str_contains($knd, 'ringan'))
                                        <span class="status-pill status-pill-warning">Rusak Ringan</span>
                                    @else
                                        <span class="status-pill status-pill-danger">Rusak Berat</span>
                                    @endif
                                </td>
                                <td style="text-align: center;">
                                    <div style="display: inline-flex; align-items: center; justify-content: center; gap: 4px;">
                                        <a href="{{ route('manajemen-aset.show', $item->id) }}" class="btn-sima-icon" title="Lihat Rincian">
                                            <i data-feather="eye" style="width: 18px; height: 18px;"></i>
                                        </a>
                                        <a href="{{ route('manajemen-aset.edit', $item->id) }}?from=index" class="btn-sima-icon" title="Ubah Aset">
                                            <i data-feather="edit-2" style="width: 18px; height: 18px;"></i>
                                        </a>
                                        @if(auth()->user()->canDeleteAset())
                                        <button type="button" class="btn-sima-icon" title="Hapus Aset" data-nama="{{ $item->display_name ?? $item->nama_barang ?? '' }}" onclick="confirmDeleteInventaris(this.getAttribute('data-nama'), {{ $item->id }})" style="color: var(--color-danger);">
                                            <i data-feather="trash-2" style="width: 18px; height: 18px;"></i>
                                        </button>
                                        @endif
                                    </div>
                                </td>
                            </tr>
                            @empty
                            <tr>
                                <td colspan="9" style="text-align: center; padding: 48px 20px; color: var(--color-text-muted);">
                                    <div style="display: flex; flex-direction: column; align-items: center; justify-content: center; gap: 8px;">
                                        <i data-feather="inbox" style="width: 42px; height: 42px; stroke-width: 1.5; color: var(--color-border-dark);"></i>
                                        <p style="margin: 0; font-size: 13.5px; font-weight: 500;">Belum ada data inventaris yang sesuai</p>
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
                                <option value="10" {{ ($inventaris->perPage() == 10) ? 'selected' : '' }}>10</option>
                                <option value="20" {{ ($inventaris->perPage() == 20) ? 'selected' : '' }}>20</option>
                                <option value="50" {{ ($inventaris->perPage() == 50) ? 'selected' : '' }}>50</option>
                                <option value="100" {{ ($inventaris->perPage() == 100) ? 'selected' : '' }}>100</option>
                            </select>
                            <span>data</span>
                        </div>
                    </div>

                    <!-- Page Navigation Numbers & Jump -->
                    <div class="pagination-center">
                        <nav class="pagination-sima pagination-nav" id="paginationNav">
                            @if ($inventaris->onFirstPage())
                                <span class="page-btn-sima page-btn disabled" style="opacity: 0.5; cursor: not-allowed;" title="Previous"><i data-feather="chevron-left" style="width: 14px; height: 14px;"></i></span>
                            @else
                                <button type="button" class="page-btn-sima page-btn" onclick="goToPage({{ $inventaris->currentPage() - 1 }})" title="Previous"><i data-feather="chevron-left" style="width: 14px; height: 14px;"></i></button>
                            @endif

                            @php
                                $currentPage  = $inventaris->currentPage();
                                $lastPage     = $inventaris->lastPage();
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
                                    <span class="page-btn-sima page-btn active">{{ $p }}</span>
                                @else
                                    <button type="button" class="page-btn-sima page-btn" onclick="goToPage({{ $p }})">{{ $p }}</button>
                                @endif
                            @endfor

                            @if ($inventaris->hasMorePages())
                                <button type="button" class="page-btn-sima page-btn" onclick="goToPage({{ $inventaris->currentPage() + 1 }})" title="Next"><i data-feather="chevron-right" style="width: 14px; height: 14px;"></i></button>
                            @else
                                <span class="page-btn-sima page-btn disabled" style="opacity: 0.5; cursor: not-allowed;" title="Next"><i data-feather="chevron-right" style="width: 14px; height: 14px;"></i></span>
                            @endif
                        </nav>

                        <!-- Jump to Page Input -->
                        <div class="page-jump-wrap">
                            <label for="pageJumpInput" style="color: var(--color-text-muted);">Ke hal.</label>
                            <input id="pageJumpInput" type="number" class="page-jump-input form-control-sima" min="1" max="{{ $inventaris->lastPage() }}" value="{{ $inventaris->currentPage() }}">
                        </div>
                    </div>

                    <!-- Range Info Text -->
                    <div class="pagination-right" id="paginationRangeInfo" style="font-size: 13px; color: var(--color-text-muted); font-variant-numeric: tabular-nums;">
                        Menampilkan <strong>{{ $inventaris->firstItem() ?? 0 }}-{{ $inventaris->lastItem() ?? 0 }}</strong> dari <strong>{{ $inventaris->total() }}</strong> aset
                    </div>
                </div>

            </div>

        </div>
    <div class="qr-modal-overlay" id="bulkQrModal" onclick="if(event.target===this) closeBulkQrModal()">
      <div class="qr-modal-card">
        <div class="qr-modal-header">
          <div>
            <h5 class="qr-modal-title">Cetak & Unduh Label QR Code Massal</h5>
            <div class="qr-modal-sub" id="bulk-qr-modal-count">0 aset dipilih</div>
          </div>
        </div>

        <div class="qr-modal-body">
          <!-- Format Selector -->
          <div style="font-size:11px;font-weight:700;color:var(--color-text-muted, #546958);text-transform:uppercase;letter-spacing:.5px;">
            PILIH FORMAT LABEL
          </div>
          <div class="format-options-grid">
            <!-- Option 1: Stiker Resmi Desa -->
            <button type="button" class="format-option-btn active" id="bulk-opt-btn-sticker" onclick="selectBulkLabelFormat('sticker')">
              <div class="format-opt-title">
                Label Stiker Resmi
              </div>
              <div class="format-opt-desc">
                Format stiker fisik resmi (Header instansi, Logo, Kode Lokasi, Kode Barang, NUP/Tahun, & QR Code)
              </div>
            </button>

            <!-- Option 2: Plain QR -->
            <button type="button" class="format-option-btn" id="bulk-opt-btn-plain" onclick="selectBulkLabelFormat('plain')">
              <div class="format-opt-title">
                QR Code & Barcode Saja
              </div>
              <div class="format-opt-desc">
                Hanya gambar QR Code tanpa frame stiker resmi
              </div>
            </button>
          </div>

          <!-- Live Preview Area -->
          <div>
            <div class="preview-heading">LIVE PREVIEW LABEL</div>
            <div class="preview-wrapper" style="display:flex; flex-direction:column; align-items:center; justify-content:center; text-align:center;">
              <!-- Preview Template 1: Sticker Official (Logo - Codes - QR) -->
              <div id="bulk-preview-sticker-container" class="sticker-preview-box" style="margin:0 auto;">
                <div class="sticker-preview-header">
                  {{ strtoupper(config('desa.upb') ?: ('PEMERINTAH DESA ' . (config('desa.desa_nama') ?: (config('desa.nama_lokasi') ?: 'DEMO')))) }}
                </div>
                <div class="sticker-preview-body">
                  <!-- Section Kiri: Logo Desa -->
                  <div class="sticker-preview-box-square left">
                    <img src="{{ asset('assets/images/logo/logo-sima-desa-demo.png') }}" class="sticker-preview-logo" alt="Logo Desa">
                  </div>
                  <!-- Section Tengah: Kode Lokasi & Kode Barang + NUP/Tahun -->
                  <div class="sticker-preview-box-center">
                    <div class="sticker-preview-cell top">
                      <span class="sticker-cell-value">{{ config('desa.kode_lokasi') ?: '51.03.02.2009' }}</span>
                    </div>
                    <div class="sticker-preview-cell">
                      <span class="sticker-cell-value" id="bulk-preview-kode-barang">04.14.01.01.033</span>
                      <span class="sticker-cell-value" id="bulk-preview-nup-tahun">NUP : 106 / 2022</span>
                    </div>
                  </div>
                  <!-- Section Kanan: QR Code -->
                  <div class="sticker-preview-box-square right">
                    <img src="data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg'/%3E" class="sticker-preview-qr" alt="QR Code" id="bulk-sticker-qr-img">
                  </div>
                </div>
              </div>

              <!-- Preview Template 2: Plain QR (Sesuai Acuan Halaman Show: Hanya QR Code Saja) -->
              <div id="bulk-preview-plain-container" class="plain-preview-box" style="display:none; margin:0 auto !important;">
                <img src="data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg'/%3E" alt="QR Code" id="bulk-plain-qr-img" style="width:140px;height:140px;border:none;border-radius:6px;display:block;margin:0 auto !important;">
              </div>
            </div>
          </div>
        </div>

        <div class="qr-modal-footer">
          <button type="button" class="btn-sima-secondary" onclick="closeBulkQrModal()" style="height: 38px; padding: 0 20px; font-size: 13px;">
            <span>Batal</span>
          </button>
          <button type="button" class="btn-sima-primary" onclick="submitBulkPrintQr()" style="height: 38px; padding: 0 22px; font-size: 13px;">
            <i data-feather="printer" style="width:14px;height:14px;"></i> <span>Cetak (Print / PDF)</span>
          </button>
        </div>
      </div>
    </div>

    <!-- Modal Duplikasi Aset -->
    <div id="dup-modal" style="display:none; position:fixed; inset:0; background:rgba(31,58,42,0.4); z-index:9999; align-items:center; justify-content:center; backdrop-filter: blur(4px);">
        <div class="modal-sima-box" style="width:90%; max-width:440px;">
            <div class="modal-sima-header" style="justify-content:center; text-align:center;">
                <div>
                    <div style="font-size:16px; font-weight:700; color:var(--color-text); text-align:center;">Duplikasi Data Aset</div>
                    <div style="font-size:12.5px; color:var(--color-text-muted); margin-top:2px; text-align:center;" id="dup-modal-nama"></div>
                </div>
            </div>
            <div class="modal-sima-body">
                <label class="form-label-sima">
                    Jumlah salinan aset yang akan dibuat:
                </label>
                <input type="text" inputmode="numeric" id="dup-jumlah" min="1" max="50" value="1"
                    placeholder="Masukkan jumlah (1–50)"
                    class="form-control-sima"
                    oninput="this.value=this.value.replace(/[^0-9]/g,'')">
                <p style="font-size:12px; color:var(--color-text-muted); margin-top:8px; line-height:1.4;">
                    Foto dan spesifikasi teknis KIB akan disalin secara otomatis. Nomor NUP baru akan digenerate otomatis secara berurutan.
                </p>
            </div>
            <div class="modal-sima-footer">
                <button type="button" onclick="closeDuplicateModal()" class="btn-sima-secondary">
                    Batal
                </button>
                <button type="button" id="dup-btn-confirm" onclick="confirmDuplicate()" class="btn-sima-primary">
                    Proses Duplikasi
                </button>
            </div>
        </div>
    </div>

    <!-- Modal Import Excel -->
    <div id="import-modal" class="modal-sima" style="display:none; position:fixed; inset:0; background:rgba(31,58,42,0.45); z-index:9999; align-items:center; justify-content:center; backdrop-filter: blur(4px); padding: 16px;">
        <div class="modal-sima-box" style="width:90%; max-width:480px; display:flex; flex-direction:column; overflow:hidden;">
            <!-- Header (Clean Plain Title, Centered) -->
            <div class="modal-sima-header" style="position: relative; border-bottom: none !important; padding: 20px 24px 16px 24px; display: flex; align-items: center; justify-content: center;">
                <div style="font-size: 16px; font-weight: 700; color: var(--color-text); text-align: center; width: 100%;">Import Data Inventaris</div>
            </div>

            <!-- Body -->
            <div class="modal-sima-body" style="padding: 20px 24px;">
                
                <!-- Unduh Template Bar (Clean Action Row with Dashed Divider) -->
                <div id="import-template-bar" style="display: flex; flex-direction: column; align-items: left; justify-content: left; text-align: left; gap: 10px; padding-bottom: 16px; border-bottom: 1px dashed var(--color-border); margin-bottom: 18px;">
                    <div style="text-align: left;">
                        <div style="font-size: 13.5px; font-weight: 600; color: var(--color-text);">Template Format CSV</div>
                        <div style="font-size: 12px; color: var(--color-text-muted); margin-top: 2px;">Sudah siap dengan baris contoh dan daftar referensi.</div>
                    </div>
                    <a href="{{ route('manajemen-aset.template.download') }}" onclick="if(window.showToast) window.showToast('Template import inventaris (CSV) berhasil diunduh! Berkas sedang diproses...', 'success');" class="btn-sima-primary" style="align-self:flex-start; border-radius: var(--radius-full); padding: 7px 18px; font-size: 12.5px; text-decoration: none; flex-shrink: 0; display: inline-flex; align-items: center; gap: 6px;">
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

                            <!-- Dropzone (Bare Icon, Surface Background, Zero Pure White) -->
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

                        <!-- Duplicate Policy Clean Note (No Alert Box, Plain Helper Text) -->
                        <p style="font-size: 12px; color: var(--color-text-muted); line-height: 1.5; margin: 0; text-align: center;">
                            Kombinasi <strong>Kode Barang</strong> dan <strong>NUP</strong> yang sudah terdaftar di sistem akan otomatis dilewati (skip) agar data yang sudah ada tidak tertimpa.
                        </p>
                    </div>

                    <!-- Loading state (Premium Organic SIMA Loader) -->
                    <div id="import-loading" style="display: none; padding: 36px 16px; text-align: center;">
                        <div style="display: flex; flex-direction: column; align-items: center; justify-content: center;">
                            <!-- Orbital Ring with Pulsing Inner Core -->
                            <div class="sima-orbital-loader" style="margin-bottom: 18px;">
                                <div class="sima-orbital-ring"></div>
                                <div class="sima-orbital-core">
                                    <i data-feather="file-text" style="width: 22px; height: 22px; color: var(--color-primary);"></i>
                                </div>
                            </div>

                            <!-- Typography -->
                            <div style="font-size: 15px; font-weight: 700; color: var(--color-text); letter-spacing: -0.01em; margin-bottom: 6px;">
                                Memproses Berkas CSV
                            </div>
                            <div id="import-dynamic-stage" style="font-size: 12.5px; color: var(--color-text-muted); min-height: 20px; transition: opacity 0.22s ease, transform 0.22s ease;">
                                Membaca dan memvalidasi baris data...
                            </div>

                            <!-- Slim Indeterminate Progress Track -->
                            <div class="sima-progress-track">
                                <div class="sima-progress-indicator"></div>
                            </div>
                        </div>
                    </div>

                    <!-- Result state (Clean, Professional & Human-Centered - Anti AI Slop) -->
                    <div id="import-result" style="display: none;">
                        <!-- Status Header & Message (No Icon, Direct Typography) -->
                        <div style="text-align: center; margin-bottom: 18px;">
                            <h4 id="res-headline-title" style="font-size: 15px; font-weight: 700; color: var(--color-text); margin: 0 0 6px 0;">Impor Selesai Diproses</h4>
                            <p id="res-headline-desc" style="font-size: 12.5px; color: var(--color-text-muted); margin: 0 auto; max-width: 440px; line-height: 1.45;"></p>
                        </div>

                        <!-- Unified Summary Strip (Warm SIMA Theme, Single Card, Dashed Dividers, Text Colors Only) -->
                        <div style="background: var(--color-surface); border: 1px solid var(--color-border); border-radius: var(--radius-md); padding: 14px 16px; margin-bottom: 16px; display: grid; grid-template-columns: repeat(3, 1fr); align-items: center;">
                            <!-- Berhasil (Hijau) -->
                            <div style="text-align: center; padding: 0 8px; border-right: 1px dashed var(--color-border);">
                                <div style="font-size: 11px; font-weight: 600; color: var(--color-success); text-transform: uppercase; letter-spacing: 0.5px;">Berhasil</div>
                                <div style="margin-top: 4px; display: flex; align-items: baseline; justify-content: center; gap: 4px;">
                                    <span id="res-count-imported" style="font-size: 22px; font-weight: 700; color: var(--color-success); font-variant-numeric: tabular-nums;">0</span>
                                    <span style="font-size: 11.5px; color: var(--color-text-muted); font-weight: 500;">aset</span>
                                </div>
                                <div style="font-size: 11px; color: var(--color-text-muted); margin-top: 2px;">Data baru masuk</div>
                            </div>

                            <!-- Dilewati (Kuning) -->
                            <div style="text-align: center; padding: 0 8px; border-right: 1px dashed var(--color-border);">
                                <div style="font-size: 11px; font-weight: 600; color: var(--color-warning); text-transform: uppercase; letter-spacing: 0.5px;">Dilewati</div>
                                <div style="margin-top: 4px; display: flex; align-items: baseline; justify-content: center; gap: 4px;">
                                    <span id="res-count-skipped" style="font-size: 22px; font-weight: 700; color: var(--color-warning); font-variant-numeric: tabular-nums;">0</span>
                                    <span style="font-size: 11.5px; color: var(--color-text-muted); font-weight: 500;">aset</span>
                                </div>
                                <div style="font-size: 11px; color: var(--color-text-muted); margin-top: 2px;">Kode & NUP sama</div>
                            </div>

                            <!-- Gagal (Merah) -->
                            <div style="text-align: center; padding: 0 8px;">
                                <div style="font-size: 11px; font-weight: 600; color: var(--color-danger); text-transform: uppercase; letter-spacing: 0.5px;">Gagal</div>
                                <div style="margin-top: 4px; display: flex; align-items: baseline; justify-content: center; gap: 4px;">
                                    <span id="res-count-failed" style="font-size: 22px; font-weight: 700; color: var(--color-danger); font-variant-numeric: tabular-nums;">0</span>
                                    <span style="font-size: 11.5px; color: var(--color-text-muted); font-weight: 500;">aset</span>
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
                                            <th style="padding: 8px 12px; text-align: left; font-size: 11px; font-weight: 600; color: var(--color-text-muted); text-transform: uppercase; letter-spacing: 0.5px;">Identitas Aset</th>
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

    <script src="https://cdn.jsdelivr.net/npm/flatpickr"></script>
    <script>
        const canDeleteAset = {{ auth()->user()->canDeleteAset() ? 'true' : 'false' }};

        function confirmDeleteInventaris(nama, id) {
            AppModal.show({
                customHtml: `
                    <div class="modal-header">
                        <div class="modal-icon danger">
                            <i data-feather="trash-2"></i>
                        </div>
                        <h3 class="modal-title">Hapus Aset Inventaris</h3>
                        <p class="modal-message">Apakah Anda yakin ingin menghapus data aset <strong style="color: var(--color-text, #16251b);">"${nama || 'Aset'}"</strong>?</p>
                    </div>
                    <div class="modal-body">
                        <div class="modal-opt-group">
                            <label class="modal-opt-card active" id="idx-opt-card-mutasi" onclick="selectIdxDeleteOpt('mutasi_berkurang')">
                                <input type="radio" name="delete_type_opt" value="mutasi_berkurang" checked>
                                <span class="modal-opt-indicator"></span>
                                <div>
                                    <div class="modal-opt-card-title">Penghapusan Resmi (Mutasi)</div>
                                    <div class="modal-opt-card-desc">Memerlukan Berita Acara / SK di modul Penghapusan</div>
                                </div>
                            </label>

                            <label class="modal-opt-card" id="idx-opt-card-salah" onclick="selectIdxDeleteOpt('salah_input')">
                                <input type="radio" name="delete_type_opt" value="salah_input">
                                <span class="modal-opt-indicator"></span>
                                <div>
                                    <div class="modal-opt-card-title">Salah Input / Percobaan</div>
                                    <div class="modal-opt-card-desc">Hapus data langsung secara permanen</div>
                                </div>
                            </label>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="modal-btn modal-btn-secondary" onclick="AppModal.handleCancel()">
                            Batal
                        </button>
                        <button type="button" id="btn-modal-delete-confirm" class="modal-btn modal-btn-primary" onclick="AppModal.handleConfirm()">
                            Lanjutkan
                        </button>
                    </div>
                `,
                onConfirm: function() {
                    const tipeHapus = document.querySelector('input[name="delete_type_opt"]:checked')?.value || 'mutasi_berkurang';
                    
                    if (tipeHapus === 'mutasi_berkurang') {
                        AppModal.close();
                        window.location.href = "{{ route('penghapusan-aset.index') }}?inventaris_id=" + id;
                        return;
                    }

                    const token = document.querySelector('meta[name="csrf-token"]').getAttribute('content');
                    if (typeof AppLoader !== 'undefined') AppLoader.show();

                    const fd = new FormData();
                    fd.append('_token', token);
                    fd.append('_method', 'DELETE');
                    fd.append('tipe_hapus', 'salah_input');

                    fetch('/manajemen-aset/' + id, {
                        method: 'POST',
                        credentials: 'same-origin',
                        headers: { 'X-Requested-With': 'XMLHttpRequest', 'Accept': 'application/json' },
                        body: fd
                    })
                    .then(r => r.json())
                    .then(data => {
                        if (typeof AppLoader !== 'undefined') AppLoader.hide();
                        AppModal.close();
                        if (data.success) {
                            AppModal.success('Berhasil', data.message || 'Data berhasil dihapus');
                            applyFilters(false);
                        } else {
                            AppModal.error('Gagal', data.message || 'Terjadi kesalahan saat menghapus data.');
                        }
                    })
                    .catch(() => {
                        if (typeof AppLoader !== 'undefined') AppLoader.hide();
                        AppModal.close();
                        AppModal.error('Gagal', 'Terjadi kesalahan jaringan.');
                    });
                },
                onCancel: function() { AppModal.close(); }
            });

            window.selectIdxDeleteOpt = function(val) {
                const mutasiCard = document.getElementById('idx-opt-card-mutasi');
                const salahCard = document.getElementById('idx-opt-card-salah');
                const btn = document.getElementById('btn-modal-delete-confirm');
                
                const radioMutasi = document.querySelector('input[name="delete_type_opt"][value="mutasi_berkurang"]');
                const radioSalah = document.querySelector('input[name="delete_type_opt"][value="salah_input"]');

                if (val === 'mutasi_berkurang') {
                    if (radioMutasi) radioMutasi.checked = true;
                    if (mutasiCard) mutasiCard.classList.add('active');
                    if (salahCard) salahCard.classList.remove('active');
                    if (btn) {
                        btn.className = 'modal-btn modal-btn-primary';
                        btn.textContent = 'Lanjutkan';
                    }
                } else {
                    if (radioSalah) radioSalah.checked = true;
                    if (mutasiCard) mutasiCard.classList.remove('active');
                    if (salahCard) salahCard.classList.add('active');
                    if (btn) {
                        btn.className = 'modal-btn modal-btn-danger';
                        btn.textContent = 'Hapus';
                    }
                }
            };
        }

        function bulkDeleteConfirm() {
            const checked = Array.from(document.querySelectorAll('.row-check:checked'));
            if (checked.length === 0) return;
            const toProcess = checked.slice(0, 100);
            const count = toProcess.length;

            AppModal.show({
                customHtml: `
                    <div class="modal-header">
                        <div class="modal-icon danger">
                            <i data-feather="trash-2"></i>
                        </div>
                        <h3 class="modal-title">Hapus ${count} Aset Sekaligus?</h3>
                        <p class="modal-message">Apakah Anda yakin ingin menghapus <strong style="color: var(--color-text, #16251b);">${count} data aset</strong> yang dipilih?</p>
                    </div>
                    <div class="modal-body">
                        <div class="modal-opt-group">
                            <label class="modal-opt-card active" id="bulk-card-mutasi" onclick="selectBulkDeleteOpt('mutasi_berkurang')">
                                <input type="radio" name="bulk_delete_type_opt" value="mutasi_berkurang" checked>
                                <span class="modal-opt-indicator"></span>
                                <div>
                                    <div class="modal-opt-card-title">Penghapusan Resmi (Mutasi)</div>
                                    <div class="modal-opt-card-desc">Tercatat dalam rekap mutasi keluar</div>
                                </div>
                            </label>
                            <label class="modal-opt-card" id="bulk-card-salah" onclick="selectBulkDeleteOpt('salah_input')">
                                <input type="radio" name="bulk_delete_type_opt" value="salah_input">
                                <span class="modal-opt-indicator"></span>
                                <div>
                                    <div class="modal-opt-card-title">Salah Input / Percobaan</div>
                                    <div class="modal-opt-card-desc">Hapus data langsung secara permanen</div>
                                </div>
                            </label>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="modal-btn modal-btn-secondary" onclick="AppModal.handleCancel()">
                            Batal
                        </button>
                        <button type="button" id="btn-bulk-delete-confirm" class="modal-btn modal-btn-primary" onclick="AppModal.handleConfirm()">
                            Lanjutkan
                        </button>
                    </div>
                `,
                onConfirm: function() {
                    const tipeHapus = document.querySelector('input[name="bulk_delete_type_opt"]:checked')?.value || 'mutasi_berkurang';
                    const ids = toProcess.map(cb => cb.value);

                    if (tipeHapus === 'mutasi_berkurang') {
                        AppModal.close();
                        window.location.href = "{{ route('penghapusan-aset.index') }}?inventaris_ids=" + ids.join(',');
                        return;
                    }

                    const token = document.querySelector('meta[name="csrf-token"]').getAttribute('content');
                    if (typeof AppLoader !== 'undefined') AppLoader.show();

                    const fd = new FormData();
                    fd.append('_token', token);
                    fd.append('tipe_hapus', 'salah_input');
                    ids.forEach(id => fd.append('ids[]', id));

                    fetch('{{ route("manajemen-aset.bulk-destroy") }}', {
                        method: 'POST',
                        credentials: 'same-origin',
                        headers: { 'X-Requested-With': 'XMLHttpRequest', 'Accept': 'application/json' },
                        body: fd
                    })
                    .then(r => r.json())
                    .then(data => {
                        if (typeof AppLoader !== 'undefined') AppLoader.hide();
                        AppModal.close();
                        if (data.success) {
                            AppModal.success('Berhasil', data.message || 'Data berhasil dihapus');
                            clearSelection();
                            applyFilters(false);
                        } else {
                            AppModal.error('Gagal', data.message || 'Terjadi kesalahan saat menghapus data.');
                        }
                    })
                    .catch(() => {
                        if (typeof AppLoader !== 'undefined') AppLoader.hide();
                        AppModal.close();
                        AppModal.error('Gagal', 'Terjadi kesalahan jaringan.');
                    });
                },
                onCancel: function() { AppModal.close(); }
            });

            window.selectBulkDeleteOpt = function(val) {
                const mutasiCard = document.getElementById('bulk-card-mutasi');
                const salahCard = document.getElementById('bulk-card-salah');
                const btn = document.getElementById('btn-bulk-delete-confirm');
                
                const radioMutasi = document.querySelector('input[name="bulk_delete_type_opt"][value="mutasi_berkurang"]');
                const radioSalah = document.querySelector('input[name="bulk_delete_type_opt"][value="salah_input"]');

                if (val === 'mutasi_berkurang') {
                    if (radioMutasi) radioMutasi.checked = true;
                    if (mutasiCard) mutasiCard.classList.add('active');
                    if (salahCard) salahCard.classList.remove('active');
                    if (btn) {
                        btn.className = 'modal-btn modal-btn-primary';
                        btn.textContent = 'Lanjutkan';
                    }
                } else {
                    if (radioSalah) radioSalah.checked = true;
                    if (mutasiCard) mutasiCard.classList.remove('active');
                    if (salahCard) salahCard.classList.add('active');
                    if (btn) {
                        btn.className = 'modal-btn modal-btn-danger';
                        btn.textContent = 'Hapus Semua';
                    }
                }
            };
        }

        let _dupId = null;
        function openDuplicateModal(id, nama) {
            _dupId = id;
            document.getElementById('dup-modal-nama').textContent = nama || 'Aset';
            document.getElementById('dup-jumlah').value = '1';
            document.getElementById('dup-modal').style.display = 'flex';
            setTimeout(() => document.getElementById('dup-jumlah').focus(), 80);
        }

        function closeDuplicateModal() {
            document.getElementById('dup-modal').style.display = 'none';
            _dupId = null;
        }

        let currentBulkLabelFormat = 'sticker';
        let selectedBulkAssetIds = [];

        function bulkPrintQr() {
            const checked = Array.from(document.querySelectorAll('.row-check:checked'));
            if (checked.length === 0) {
                if (typeof AppModal !== 'undefined') {
                    AppModal.alert('Peringatan', 'Pilih minimal satu aset untuk mencetak label QR.');
                } else {
                    alert('Pilih minimal satu aset untuk mencetak label QR.');
                }
                return;
            }
            if (checked.length > 100) {
                if (typeof AppModal !== 'undefined') {
                    AppModal.alert('Perhatian', 'Maksimal 100 label stiker aset per sekali cetak untuk menjaga performa berkas PDF.');
                } else {
                    alert('Maksimal 100 label stiker aset per sekali cetak.');
                }
                return;
            }

            selectedBulkAssetIds = checked.map(cb => cb.value);

            // Ambil sampel data dari baris pertama yang dipilih untuk live preview
            const firstRow = checked[0].closest('tr');
            let sampleKode = '04.14.01.01.033';
            let sampleNupThn = 'NUP : 106 / 2022';

            if (firstRow) {
                const textContent = firstRow.innerText || '';
                const matchKode = textContent.match(/(\d{2}\.\d{2}\.\d{2}\.\d{2}\.\d{3}|\d+\.\d+\.\d+\.\d+)/);
                if (matchKode) sampleKode = matchKode[1];
                const matchNup = textContent.match(/NUP\s*[:\-]?\s*(\d+)/i);
                const matchThn = textContent.match(/\b(20\d{2}|19\d{2})\b/);
                if (matchNup || matchThn) {
                    sampleNupThn = `NUP : ${matchNup ? matchNup[1] : '1'} / ${matchThn ? matchThn[1] : '2024'}`;
                }
            }

            const prevKodeEl = document.getElementById('bulk-preview-kode-barang');
            const prevNupEl = document.getElementById('bulk-preview-nup-tahun');

            if (prevKodeEl) prevKodeEl.textContent = sampleKode;
            if (prevNupEl) prevNupEl.textContent = sampleNupThn;

            // Load real QR Code dari endpoint resmi manajemen-aset/{id}/qr.png (sama persis dengan halaman Show)
            const firstId = selectedBulkAssetIds[0];
            if (firstId) {
                const dynamicQrUrl = `{{ url('manajemen-aset') }}/${firstId}/qr.png`;
                const stickerQrImg = document.getElementById('bulk-sticker-qr-img');
                const plainQrImg = document.getElementById('bulk-plain-qr-img');
                if (stickerQrImg) stickerQrImg.src = dynamicQrUrl;
                if (plainQrImg) plainQrImg.src = dynamicQrUrl;
            }

            const modalCountEl = document.getElementById('bulk-qr-modal-count');
            if (modalCountEl) modalCountEl.textContent = `${selectedBulkAssetIds.length} aset dipilih`;

            openBulkQrModal();
        }

        function openBulkQrModal() {
            const modal = document.getElementById('bulkQrModal');
            if (modal) {
                selectBulkLabelFormat('sticker');
                modal.classList.add('active');
                if (typeof feather !== 'undefined') feather.replace();
            }
        }

        function closeBulkQrModal() {
            const modal = document.getElementById('bulkQrModal');
            if (modal) modal.classList.remove('active');
        }

        function selectBulkLabelFormat(fmt) {
            currentBulkLabelFormat = fmt;
            const btnSticker = document.getElementById('bulk-opt-btn-sticker');
            const btnPlain   = document.getElementById('bulk-opt-btn-plain');
            const prevSticker = document.getElementById('bulk-preview-sticker-container');
            const prevPlain   = document.getElementById('bulk-preview-plain-container');

            if (fmt === 'sticker') {
                if (btnSticker) btnSticker.classList.add('active');
                if (btnPlain)   btnPlain.classList.remove('active');
                if (prevSticker) prevSticker.style.display = 'block';
                if (prevPlain)   prevPlain.style.display   = 'none';
            } else {
                if (btnPlain)   btnPlain.classList.add('active');
                if (btnSticker) btnSticker.classList.remove('active');
                if (prevSticker) prevSticker.style.display = 'none';
                if (prevPlain)   prevPlain.style.display   = 'flex';
            }
            if (typeof feather !== 'undefined') feather.replace();
        }

        function submitBulkPrintQr() {
            if (!selectedBulkAssetIds || selectedBulkAssetIds.length === 0) {
                closeBulkQrModal();
                return;
            }

            const token = document.querySelector('meta[name="csrf-token"]')?.getAttribute('content');
            const form = document.createElement('form');
            form.method = 'POST';
            form.action = '{{ route("manajemen-aset.bulk-print-qr") }}';
            form.target = '_blank';

            const csrfInput = document.createElement('input');
            csrfInput.type = 'hidden';
            csrfInput.name = '_token';
            csrfInput.value = token;
            form.appendChild(csrfInput);

            const fmtInput = document.createElement('input');
            fmtInput.type = 'hidden';
            fmtInput.name = 'format';
            fmtInput.value = currentBulkLabelFormat;
            form.appendChild(fmtInput);

            selectedBulkAssetIds.forEach(id => {
                const idInput = document.createElement('input');
                idInput.type = 'hidden';
                idInput.name = 'ids[]';
                idInput.value = id;
                form.appendChild(idInput);
            });

            document.body.appendChild(form);
            form.submit();
            document.body.removeChild(form);
            closeBulkQrModal();
        }

        function bulkDuplicateConfirm() {
            const checked = document.querySelectorAll('.row-check:checked');
            if (checked.length === 0) return;
            const firstChecked = checked[0];
            const id = firstChecked.value;
            const row = firstChecked.closest('tr');
            const nama = row ? (row.querySelector('td:nth-child(3) div:first-child')?.textContent?.trim() || 'Aset') : 'Aset';
            openDuplicateModal(id, nama);
        }

        function confirmDuplicate() {
            if (!_dupId) return;
            const jumlah = parseInt(document.getElementById('dup-jumlah').value) || 1;
            const btn = document.getElementById('dup-btn-confirm');
            btn.disabled = true;
            btn.textContent = 'Menyalin...';

            const fd = new FormData();
            fd.append('_token', document.querySelector('meta[name="csrf-token"]').getAttribute('content'));
            fd.append('jumlah', jumlah);

            fetch('/manajemen-aset/' + _dupId + '/duplicate', {
                method: 'POST',
                headers: { 'X-Requested-With': 'XMLHttpRequest', 'Accept': 'application/json' },
                body: fd
            })
            .then(r => r.json())
            .then(data => {
                closeDuplicateModal();
                if (data.success) {
                    AppModal.success('Berhasil', data.message || 'Data berhasil diduplikasi.');
                    clearSelection();
                    applyFilters(false);
                } else {
                    AppModal.error('Gagal', data.message || 'Gagal menduplikasi.');
                }
            })
            .catch(() => {
                if (typeof AppModal !== 'undefined') {
                    AppModal.error('Gagal', 'Terjadi kesalahan jaringan.');
                } else {
                    alert('Terjadi kesalahan jaringan.');
                }
            })
            .finally(() => {
                btn.disabled = false;
                btn.textContent = 'Proses Duplikasi';
            });
        }

        function toggleSearchClear(input) {
            const clearBtn = document.getElementById('clear-search-btn');
            if (clearBtn) clearBtn.style.display = input.value ? 'inline-block' : 'none';
        }

        let _searchTimer = null;
        function onSearchInput(input) {
            toggleSearchClear(input);
            clearTimeout(_searchTimer);
            _searchTimer = setTimeout(function() {
                applyFilters(false, 1);
            }, 400);
        }
        function doSearch() {
            clearTimeout(_searchTimer);
            applyFilters(false, 1);
        }
        function clearSearch() {
            const input = document.getElementById('searchInput');
            input.value = '';
            toggleSearchClear(input);
            applyFilters(false, 1);
        }

        function goToPage(page) {
            applyFilters(false, page);
        }

        function changePerPage(limit) {
            applyFilters(false, 1);
        }

        (function initPageJump() {
            const jumpInput = document.getElementById('pageJumpInput');
            if (!jumpInput) return;
            jumpInput.addEventListener('keydown', function (e) {
                if (e.key === 'Enter') {
                    e.preventDefault();
                    const lastPage = parseInt(this.max || 1, 10);
                    let val = parseInt(this.value, 10);
                    if (isNaN(val)) return;
                    if (val < 1) val = 1;
                    if (val > lastPage) val = lastPage;
                    this.value = val;
                    goToPage(val);
                }
            });
            jumpInput.addEventListener('blur', function () {
                const lastPage = parseInt(this.max || 1, 10);
                const val = parseInt(this.value, 10);
                if (isNaN(val) || val < 1 || val > lastPage) return;
                goToPage(val);
            });
        })();

        function toggleAll(src) {
            document.querySelectorAll('.row-check:not(:disabled)').forEach(cb => { cb.checked = src.checked; });
            updateBulkBar();
        }

        function clearSelection() {
            document.querySelectorAll('.row-check, #check-all').forEach(cb => { cb.checked = false; });
            const bar = document.getElementById('bulk-bar');
            if (bar) bar.classList.remove('show');
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
                const btnSalin = document.getElementById('btn-salin-data');
                if (btnSalin) btnSalin.style.display = checked.length === 1 ? 'inline-flex' : 'none';
                if (typeof feather !== 'undefined') feather.replace();
            }
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

        function applyFilters(isAutoRefresh = false, pageOverride = null) {
            const filterMenu = document.getElementById('filter-dropdown-menu');
            if (filterMenu && !isAutoRefresh) filterMenu.style.display = 'none';

            const searchValue = document.getElementById('searchInput').value.trim();
            const selectedJenis = Array.from(document.querySelectorAll('.jenis-filter:checked')).map(cb => cb.value);
            const selectedSort = document.querySelector('.sort-filter:checked')?.value || 'terbaru';
            const perPageSelect = document.getElementById('perPageSelect');
            const perPageValue = perPageSelect ? perPageSelect.value : '10';
            
            const rawTahun = document.getElementById('tahunFilter')?.value?.trim();
            const tahunValue = (rawTahun && rawTahun !== 'all' && rawTahun !== '-- Semua Tahun --' && rawTahun !== 'Semua Tahun') ? rawTahun : 'all';
            const ruanganValue = document.getElementById('ruanganFilter')?.value || 'all';
            const selectedKondisi = Array.from(document.querySelectorAll('.kondisi-filter:checked')).map(cb => cb.value);
            const selectedKelengkapan = document.querySelector('.kelengkapan-filter:checked')?.value || 'all';

            const params = new URLSearchParams();
            if (searchValue) params.append('search', searchValue);
            if (selectedJenis.length > 0) {
                selectedJenis.forEach(j => params.append('jenis[]', j));
            }
            if (selectedKondisi.length > 0) {
                selectedKondisi.forEach(k => params.append('kondisi[]', k));
            }
            if (tahunValue && tahunValue !== 'all') params.append('tahun', tahunValue);
            if (ruanganValue && ruanganValue !== 'all') params.append('ruangan', ruanganValue);
            if (selectedKelengkapan && selectedKelengkapan !== 'all') params.append('kelengkapan', selectedKelengkapan);
            if (selectedSort !== 'terbaru') params.append('sort', selectedSort);
            if (perPageValue && perPageValue !== '10') params.set('per_page', perPageValue);

            updateActiveCardStyles();

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
                        sessionStorage.setItem('manajemen_aset_filter_state', queryString);
                    } else {
                        sessionStorage.removeItem('manajemen_aset_filter_state');
                    }
                } catch(e) {}
            }

            const wrap = document.querySelector('.table-loading-wrap');
            if (!isAutoRefresh && wrap) wrap.classList.add('is-loading');
            fetch(`{{ route('manajemen-aset.index') }}?${queryString}`, {
                headers: {
                    'X-Requested-With': 'XMLHttpRequest',
                    'Accept': 'application/json'
                }
            })
            .then(response => response.json())
            .then(data => {
                updateStats(data.stats);
                updateTable(data.inventaris, data.pagination);
                updatePaginationUI(data.pagination);
                if (typeof feather !== 'undefined') feather.replace();
            })
            .catch(error => console.error('Error:', error))
            .finally(() => {
                if (wrap) wrap.classList.remove('is-loading');
            });
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
            if (typeof feather !== 'undefined') feather.replace();
        }

        function updateStats(stats) {
            if (!stats) return;
            const statCards = document.querySelectorAll('.stat-card-sima');
            if (statCards[0]) {
                const el = statCards[0].querySelector('.stat-card-value');
                if (el && stats.totalInventaris !== undefined) el.textContent = Number(stats.totalInventaris || 0).toLocaleString('id-ID');
                if (stats.trends && stats.trends.total) updateCardTrend(statCards[0], stats.trends.total);
            }
            if (statCards[1]) {
                const el = statCards[1].querySelector('.stat-card-value');
                if (el && stats.kondisiBaik !== undefined) el.textContent = Number(stats.kondisiBaik || 0).toLocaleString('id-ID');
                if (stats.trends && stats.trends.baik) updateCardTrend(statCards[1], stats.trends.baik);
            }
            if (statCards[2]) {
                const el = statCards[2].querySelector('.stat-card-value');
                if (el && stats.kondisiRusakRingan !== undefined) el.textContent = Number(stats.kondisiRusakRingan || 0).toLocaleString('id-ID');
                if (stats.trends && stats.trends.rr) updateCardTrend(statCards[2], stats.trends.rr);
            }
            if (statCards[3]) {
                const el = statCards[3].querySelector('.stat-card-value');
                if (el && stats.kondisiRusakBerat !== undefined) el.textContent = Number(stats.kondisiRusakBerat || 0).toLocaleString('id-ID');
                if (stats.trends && stats.trends.rb) updateCardTrend(statCards[3], stats.trends.rb);
            }
            
            const tabAll = document.querySelector('#tab-status-all .tab-badge-sima');
            if (tabAll && stats.totalInventaris !== undefined) tabAll.textContent = Number(stats.totalInventaris).toLocaleString('id-ID');
            const tabBaik = document.querySelector('#tab-status-baik .tab-badge-sima');
            if (tabBaik && stats.kondisiBaik !== undefined) tabBaik.textContent = Number(stats.kondisiBaik).toLocaleString('id-ID');
            const tabRr = document.querySelector('#tab-status-rusak_ringan .tab-badge-sima');
            if (tabRr && stats.kondisiRusakRingan !== undefined) tabRr.textContent = Number(stats.kondisiRusakRingan).toLocaleString('id-ID');
            const tabRb = document.querySelector('#tab-status-rusak_berat .tab-badge-sima');
            if (tabRb && stats.kondisiRusakBerat !== undefined) tabRb.textContent = Number(stats.kondisiRusakBerat).toLocaleString('id-ID');
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
                rangeEl.innerHTML = `Menampilkan <strong>${firstItem}-${lastItem}</strong> dari <strong>${total}</strong> aset`;
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
                html += `<span class="page-btn-sima" style="opacity: 0.5; cursor: not-allowed;" title="Previous"><i data-feather="chevron-left" style="width: 14px; height: 14px;"></i></span>`;
            } else {
                html += `<button type="button" class="page-btn-sima" onclick="goToPage(${currentPage - 1})" title="Previous"><i data-feather="chevron-left" style="width: 14px; height: 14px;"></i></button>`;
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
                html += `<span class="page-btn-sima" style="opacity: 0.5; cursor: not-allowed;" title="Next"><i data-feather="chevron-right" style="width: 14px; height: 14px;"></i></span>`;
            } else {
                html += `<button type="button" class="page-btn-sima" onclick="goToPage(${currentPage + 1})" title="Next"><i data-feather="chevron-right" style="width: 14px; height: 14px;"></i></button>`;
            }

            nav.innerHTML = html;
        }

        function updateTable(items, pagination) {
            clearSelection();
            const tbody = document.querySelector('.table-sima tbody');
            if (!tbody) return;
            const firstItem = pagination ? (pagination.firstItem || 1) : 1;
            if (!items || items.length === 0) {
                tbody.innerHTML = `<tr><td colspan="9" style="text-align: center; padding: 48px 20px; color: var(--color-text-muted);">
                    <div style="display: flex; flex-direction: column; align-items: center; justify-content: center; gap: 8px;">
                        <i data-feather="inbox" style="width: 42px; height: 42px; stroke-width: 1.5; color: var(--color-border-dark);"></i>
                        <p style="margin: 0; font-size: 13.5px; font-weight: 500;">Belum ada data inventaris yang sesuai</p>
                    </div></td></tr>`;
                if (typeof feather !== 'undefined') feather.replace();
                return;
            }
            let html = '';
            items.forEach((item, idx) => {
                const rowNumber = firstItem + idx;
                const escapedName = (item.display_name || '').replace(/&/g, '&amp;').replace(/"/g, '&quot;');
                const rawKondisi = String(item.kondisi || 'Baik').toLowerCase();
                let statusBadge = '';
                if (rawKondisi === 'baik') {
                    statusBadge = `<span class="status-pill status-pill-success">Baik</span>`;
                } else if (rawKondisi.includes('ringan')) {
                    statusBadge = `<span class="status-pill status-pill-warning">Rusak Ringan</span>`;
                } else {
                    const label = rawKondisi.includes('berat') ? 'Rusak Berat' : (item.kondisi ? item.kondisi.replace(/_/g, ' ').replace(/\b\w/g, l => l.toUpperCase()) : 'Rusak Berat');
                    statusBadge = `<span class="status-pill status-pill-danger">${label}</span>`;
                }

                let kelengkapanBadge = '';
                if (item.is_lengkap === false) {
                    const missingList = Array.isArray(item.missing_fields) && item.missing_fields.length > 0 ? item.missing_fields.join(', ') : 'Data wajib belum lengkap';
                    kelengkapanBadge = `
                        <div style="margin-top: 4px;">
                            <span class="status-pill status-pill-warning" style="font-size: 11px; padding: 2px 7px; display: inline-flex; align-items: center; gap: 4px;" title="Belum lengkap: ${missingList}">
                                <i data-feather="alert-circle" style="width: 12px; height: 12px;"></i>
                                Perlu Dilengkapi
                            </span>
                        </div>`;
                }

                html += `<tr>
                    <td style="text-align: center;">
                        <input type="checkbox" class="row-check sima-checkbox" value="${item.id}" onchange="updateBulkBar()">
                    </td>
                    <td style="text-align: center; color: var(--color-text-muted); font-size: 13px; font-variant-numeric: tabular-nums;">${rowNumber}</td>
                    <td>
                        <div style="font-weight: 600; color: var(--color-text); font-size: 14px; line-height: 1.3;">
                            ${item.display_name ?? item.nama_barang ?? '-'}
                        </div>
                        <div style="display: flex; align-items: center; gap: 6px; font-size: 12px; color: var(--color-text-muted); margin-top: 3px;">
                            <code>${item.kode_barang ?? '-'}</code>
                            <span>&bull;</span>
                            <span>NUP: <strong style="color: var(--color-text);">${item.nup ?? '-'}</strong></span>
                        </div>
                        ${kelengkapanBadge}
                    </td>
                    <td>
                        <span style="font-size: 13px; color: var(--color-text); font-weight: 500;">
                            ${item.jenis_nama ?? 'Inventaris'}
                        </span>
                    </td>
                    <td>
                        ${item.ruangan_nama && item.ruangan_nama !== '-' ? '<span style="font-size: 13px; color: var(--color-text); font-weight: 500;">' + item.ruangan_nama + '</span>' : '<span style="font-size: 13px; color: var(--color-text-muted);">-</span>'}
                    </td>
                    <td style="font-size: 13px; color: var(--color-text-muted); font-variant-numeric: tabular-nums;">
                        ${item.tahun_perolehan ?? item.tahun_pengadaan ?? '-'}
                    </td>
                    <td class="num-col" style="font-weight: 600; color: var(--color-text);">
                        Rp ${Number(item.nilai_perolehan || 0).toLocaleString('id-ID')}
                    </td>
                    <td style="text-align: center;">
                        ${statusBadge}
                    </td>
                    <td style="text-align: center;">
                        <div style="display: inline-flex; align-items: center; justify-content: center; gap: 4px;">
                            <a href="{{ url('manajemen-aset') }}/${item.id}" class="btn-sima-icon" title="Lihat Rincian"><i data-feather="eye" style="width: 18px; height: 18px;"></i></a>
                            <a href="{{ url('manajemen-aset') }}/${item.id}/edit?from=index" class="btn-sima-icon" title="Ubah Aset"><i data-feather="edit-2" style="width: 18px; height: 18px;"></i></a>
                            ${canDeleteAset ? `<button type="button" class="btn-sima-icon" title="Hapus Aset" data-nama="${escapedName}" onclick="confirmDeleteInventaris(this.getAttribute('data-nama'), ${item.id})" style="color: var(--color-danger);"><i data-feather="trash-2" style="width: 18px; height: 18px;"></i></button>` : ''}
                        </div>
                    </td>
                </tr>`;
            });
            tbody.innerHTML = html;
        }

        function filterByCardKondisi(kondisi) {
            const cbs = document.querySelectorAll('.kondisi-filter');
            if (kondisi === 'all') {
                cbs.forEach(cb => cb.checked = false);
            } else {
                const targetCb = document.querySelector(`.kondisi-filter[value="${kondisi}"]`);
                if (targetCb) {
                    const checkedCount = Array.from(cbs).filter(c => c.checked).length;
                    const alreadyOnlyOne = targetCb.checked && checkedCount === 1;
                    if (alreadyOnlyOne) {
                        targetCb.checked = false;
                    } else {
                        cbs.forEach(cb => cb.checked = (cb.value === kondisi));
                    }
                }
            }
            updateActiveCardStyles();
            applyFilters(false, 1);
        }

        function updateActiveCardStyles() {
            const checked = Array.from(document.querySelectorAll('.kondisi-filter:checked')).map(c => c.value);
            document.querySelectorAll('.stat-card-sima').forEach(c => c.classList.remove('is-active-filter'));
            document.querySelectorAll('.tab-item-sima').forEach(t => t.classList.remove('active'));

            if (checked.length === 1) {
                const activeCard = document.getElementById(`card-kondisi-${checked[0]}`);
                if (activeCard) activeCard.classList.add('is-active-filter');
                const activeTab = document.getElementById(`tab-status-${checked[0]}`);
                if (activeTab) activeTab.classList.add('active');
            } else if (checked.length === 0) {
                const totalCard = document.getElementById('card-kondisi-total');
                if (totalCard) totalCard.classList.add('is-active-filter');
                const totalTab = document.getElementById('tab-status-all');
                if (totalTab) totalTab.classList.add('active');
            }
        }

        function resetFilters() {
            try { sessionStorage.removeItem('manajemen_aset_filter_state'); } catch(e) {}
            document.querySelectorAll('.jenis-filter').forEach(cb => cb.checked = false);
            document.querySelectorAll('.kondisi-filter').forEach(cb => cb.checked = false);
            document.querySelectorAll('.sort-filter').forEach(r => r.checked = false);
            const defaultSort = document.querySelector('.sort-filter[value="terbaru"]');
            if (defaultSort) defaultSort.checked = true;
            document.querySelectorAll('.kelengkapan-filter').forEach(r => r.checked = false);
            const defaultKelengkapan = document.querySelector('.kelengkapan-filter[value="all"]');
            if (defaultKelengkapan) defaultKelengkapan.checked = true;
            const searchInput = document.getElementById('searchInput');
            if (searchInput) searchInput.value = '';
            const tahunFilter = document.getElementById('tahunFilter');
            if (tahunFilter) {
                tahunFilter.value = '';
                if (tahunFilter._flatpickr && tahunFilter._flatpickr.calendarContainer && tahunFilter._flatpickr.calendarContainer._yearPickerState) {
                    tahunFilter._flatpickr.calendarContainer._yearPickerState.selectedYear = null;
                }
            }
            const clearTahunBtn = document.getElementById('clearTahunFilterBtn');
            if (clearTahunBtn) clearTahunBtn.style.display = 'none';
            const ruanganFilter = document.getElementById('ruanganFilter');
            if (ruanganFilter) ruanganFilter.value = 'all';
            toggleSearchClear(searchInput || { value: '' });
            updateActiveCardStyles();
            applyFilters(false, 1);
        }

        // Year Picker Popover for filter tahun perolehan
        (function initFilterYearPicker() {
            const input = document.getElementById('tahunFilter');
            const clearBtn = document.getElementById('clearTahunFilterBtn');
            if (!input) return;

            const minYear = 1900;
            const maxYear = new Date().getFullYear() + 1;

            function ensureYearPanel(instance) {
                const cal = instance.calendarContainer;
                if (!cal) return;
                cal.classList.add('year-only-picker');
                cal.classList.add('dropdown-menu');
                cal.addEventListener('click', function(e) {
                    e.stopPropagation();
                });

                if (cal._yearPickerState) {
                    renderYearGrid(instance, cal._yearPickerState, minYear, maxYear);
                    return;
                }

                const panel = document.createElement('div');
                panel.className = 'year-picker-panel';
                panel.innerHTML = `
                    <button type="button" class="year-picker-all-btn" id="btn-year-picker-all">
                        <span>Semua Tahun (Seluruh Data)</span>
                    </button>
                    <div class="year-picker-header">
                        <button type="button" class="year-picker-nav" data-nav="prev" aria-label="Sebelumnya">&lsaquo;</button>
                        <span class="year-picker-range"></span>
                        <button type="button" class="year-picker-nav" data-nav="next" aria-label="Berikutnya">&rsaquo;</button>
                    </div>
                    <div class="year-picker-grid"></div>
                `;
                cal.appendChild(panel);

                const state = {
                    panel,
                    range: panel.querySelector('.year-picker-range'),
                    grid: panel.querySelector('.year-picker-grid'),
                    prev: panel.querySelector('[data-nav="prev"]'),
                    next: panel.querySelector('[data-nav="next"]'),
                    allBtn: panel.querySelector('#btn-year-picker-all'),
                    pageStart: Math.max(minYear, maxYear - 11),
                    selectedYear: parseInt(input.value) || null,
                };
                cal._yearPickerState = state;

                state.allBtn.addEventListener('click', function(ev) {
                    ev.preventDefault();
                    ev.stopPropagation();
                    input.value = '';
                    state.selectedYear = null;
                    if (clearBtn) clearBtn.style.display = 'none';
                    instance.close();
                });

                state.prev.addEventListener('click', function(ev) {
                    ev.preventDefault();
                    ev.stopPropagation();
                    state.pageStart = Math.max(minYear, state.pageStart - 12);
                    renderYearGrid(instance, state, minYear, maxYear);
                });

                state.next.addEventListener('click', function(ev) {
                    ev.preventDefault();
                    ev.stopPropagation();
                    const maxStart = Math.max(minYear, maxYear - 11);
                    state.pageStart = Math.min(maxStart, state.pageStart + 12);
                    renderYearGrid(instance, state, minYear, maxYear);
                });

                state.grid.addEventListener('click', function(ev) {
                    const btn = ev.target.closest('.year-picker-option');
                    if (!btn) return;
                    ev.preventDefault();
                    ev.stopPropagation();
                    const yr = parseInt(btn.dataset.year, 10);
                    if (!Number.isFinite(yr)) return;
                    input.value = String(yr);
                    state.selectedYear = yr;
                    if (clearBtn) clearBtn.style.display = 'inline-flex';
                    instance.close();
                });

                renderYearGrid(instance, state, minYear, maxYear);
            }

            function renderYearGrid(instance, state, minYear, maxYear) {
                const start = state.pageStart;
                const end = Math.min(start + 11, maxYear);

                state.range.textContent = `${start} - ${end}`;
                state.prev.disabled = start <= minYear;
                state.next.disabled = end >= maxYear;

                const curVal = parseInt(input.value) || null;
                state.selectedYear = curVal;

                let html = '';
                for (let yr = start; yr <= end; yr++) {
                    const isActive = yr === state.selectedYear ? ' active' : '';
                    html += `<button type="button" class="year-picker-option${isActive}" data-year="${yr}">${yr}</button>`;
                }
                state.grid.innerHTML = html;
            }

            if (typeof flatpickr !== 'undefined') {
                const fp = flatpickr(input, {
                    dateFormat: 'Y',
                    disableMobile: true,
                    allowInput: false,
                    minDate: `${minYear}-01-01`,
                    maxDate: `${maxYear}-12-31`,
                    position: 'auto left',
                    onReady: function(_, __, instance) {
                        ensureYearPanel(instance);
                    },
                    onOpen: function(_, __, instance) {
                        const val = parseInt(input.value);
                        if (val && Number.isFinite(val)) {
                            const maxStart = Math.max(minYear, maxYear - 11);
                            if (instance.calendarContainer && instance.calendarContainer._yearPickerState) {
                                instance.calendarContainer._yearPickerState.pageStart = Math.min(maxStart, Math.max(minYear, val - 5));
                            }
                        }
                        ensureYearPanel(instance);
                    }
                });

                if (clearBtn) {
                    clearBtn.addEventListener('click', function(e) {
                        e.preventDefault();
                        e.stopPropagation();
                        input.value = '';
                        clearBtn.style.display = 'none';
                        if (fp.calendarContainer && fp.calendarContainer._yearPickerState) {
                            fp.calendarContainer._yearPickerState.selectedYear = null;
                        }
                    });
                }
            }
        })();
        
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
            try { sessionStorage.removeItem('manajemen_aset_filter_state'); } catch(e) {}
            @endif

            let isReturning = false;
            try {
                if (sessionStorage.getItem('manajemen_aset_returning') === '1') {
                    isReturning = true;
                    sessionStorage.removeItem('manajemen_aset_returning');
                }
            } catch(e) {}

            const ref = document.referrer || '';
            if (ref.includes('/manajemen-aset/') || ref.includes('/create') || ref.includes('/edit')) {
                isReturning = true;
            }
            if (navType === 'back_forward') {
                isReturning = true;
            }

            if (navType === 'reload' && !isReturning && !window._simaReloadResetHandled) {
                window._simaReloadResetHandled = true;
                try { sessionStorage.removeItem('manajemen_aset_filter_state'); } catch(e) {}
                if (window.location.search) {
                    history.replaceState(null, '', window.location.pathname);
                }

                const searchInput = document.getElementById('searchInput');
                if (searchInput) {
                    searchInput.value = '';
                    toggleSearchClear(searchInput);
                }
                document.querySelectorAll('.jenis-filter').forEach(cb => cb.checked = false);
                document.querySelectorAll('.kondisi-filter').forEach(cb => cb.checked = false);
                document.querySelectorAll('.sort-filter').forEach(r => r.checked = false);
                const defaultSort = document.querySelector('.sort-filter[value="terbaru"]');
                if (defaultSort) defaultSort.checked = true;
                document.querySelectorAll('.kelengkapan-filter').forEach(r => r.checked = false);
                const defaultKelengkapan = document.querySelector('.kelengkapan-filter[value="all"]');
                if (defaultKelengkapan) defaultKelengkapan.checked = true;

                const tf = document.getElementById('tahunFilter');
                const clearTBtn = document.getElementById('clearTahunFilterBtn');
                if (tf) {
                    tf.value = '';
                    if (tf._flatpickr && tf._flatpickr.calendarContainer && tf._flatpickr.calendarContainer._yearPickerState) {
                        tf._flatpickr.calendarContainer._yearPickerState.selectedYear = null;
                    }
                }
                if (clearTBtn) clearTBtn.style.display = 'none';

                const rf = document.getElementById('ruanganFilter');
                if (rf) rf.value = 'all';

                const perPageSelect = document.getElementById('perPageSelect');
                if (perPageSelect) perPageSelect.value = '10';

                updateActiveCardStyles();
                applyFilters(true, 1);
                return;
            }

            if (!isReturning && navType === 'navigate' && !window.location.search) {
                try { sessionStorage.removeItem('manajemen_aset_filter_state'); } catch(e) {}
                updateActiveCardStyles();
                return;
            }

            let currentSearch = window.location.search;
            let hasUrlQuery = (currentSearch && currentSearch.length > 1);

            if (!hasUrlQuery) {
                try {
                    const saved = sessionStorage.getItem('manajemen_aset_filter_state');
                    if (saved && saved.length > 0) {
                        currentSearch = '?' + saved;
                        history.replaceState(null, '', window.location.pathname + currentSearch);
                        hasUrlQuery = true;
                    }
                } catch(e) {}
            }

            if (!hasUrlQuery) {
                updateActiveCardStyles();
                return;
            }

            const urlParams = new URLSearchParams(currentSearch);
            const q = urlParams.get('search') || '';
            const searchInput = document.getElementById('searchInput');
            if (searchInput) {
                searchInput.value = q;
                toggleSearchClear(searchInput);
            }

            const jenisParams = urlParams.getAll('jenis[]');
            document.querySelectorAll('.jenis-filter').forEach(cb => {
                cb.checked = jenisParams.includes(cb.value);
            });
            
            const thnParam = urlParams.get('tahun') || '';
            const tf = document.getElementById('tahunFilter');
            const clearTBtn = document.getElementById('clearTahunFilterBtn');
            if (tf) {
                if (thnParam && thnParam !== 'all') {
                    tf.value = thnParam;
                    if (clearTBtn) clearTBtn.style.display = 'inline-flex';
                } else {
                    tf.value = '';
                    if (clearTBtn) clearTBtn.style.display = 'none';
                }
            }

            const rgParam = urlParams.get('ruangan') || 'all';
            const rf = document.getElementById('ruanganFilter');
            if (rf) rf.value = rgParam;

            const sortParam = urlParams.get('sort') || 'terbaru';
            const sr = document.querySelector('.sort-filter[value="'+sortParam+'"]');
            if (sr) sr.checked = true;

            const kondisiParams = urlParams.getAll('kondisi[]');
            const singleKondisi = urlParams.get('kondisi');
            document.querySelectorAll('.kondisi-filter').forEach(cb => {
                cb.checked = kondisiParams.includes(cb.value) || (singleKondisi && singleKondisi === cb.value);
            });

            const kelengkapanParam = urlParams.get('kelengkapan') || 'all';
            const kr = document.querySelector('.kelengkapan-filter[value="'+kelengkapanParam+'"]');
            if (kr) {
                document.querySelectorAll('.kelengkapan-filter').forEach(r => r.checked = false);
                kr.checked = true;
            }

            const perPageParam = urlParams.get('per_page');
            const perPageSelect = document.getElementById('perPageSelect');
            if (perPageSelect && perPageParam) {
                perPageSelect.value = perPageParam;
            }

            updateActiveCardStyles();

            const page = parseInt(urlParams.get('page')) || 1;
            document.querySelectorAll('.table-loading-wrap').forEach(el => el.classList.remove('is-loading'));
            applyFilters(true, page);
        }

        // ==========================================
        // FITUR IMPORT EXCEL INVENTARIS ASET
        // ==========================================
        let importStageTimer = null;
        const importStages = [
            'Membaca dan memvalidasi baris data...',
            'Memeriksa duplikasi Kode Barang & NUP...',
            'Menyiapkan barcode & kalkulasi inventaris...'
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
                // easeOutCubic curve for natural, organic deceleration
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
        let importStatsAccumulator = { imported: 0, skipped: 0, failed: 0 };
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

            fetch('{{ route("manajemen-aset.import") }}', {
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

                    // Reset values before starting count-up animation
                    importedEl.textContent = '0';
                    skippedEl.textContent = '0';
                    failedEl.textContent = '0';

                    // Smooth fluid number count-up
                    animateNumberCount(importedEl, 0, importedCount, 600);
                    animateNumberCount(skippedEl, 0, skippedCount, 600);
                    animateNumberCount(failedEl, 0, failedCount, 600);

                    // Text colors as requested: Berhasil (Hijau), Dilewati (Kuning), Gagal (Merah)
                    importedEl.style.color = 'var(--color-success)';
                    skippedEl.style.color = 'var(--color-warning)';
                    failedEl.style.color = 'var(--color-danger)';

                    if (importedCount > 0 && failedCount === 0) {
                        titleEl.textContent = 'Impor Berhasil Ditambahkan';
                        descEl.textContent = skippedCount > 0
                            ? `${importedCount} aset baru berhasil disimpan, dan ${skippedCount} aset yang sudah terdaftar dilewati.`
                            : `${importedCount} data aset baru berhasil disimpan ke dalam inventaris desa.`;
                    } else if (importedCount === 0 && skippedCount > 0 && failedCount === 0) {
                        titleEl.textContent = 'Data Sudah Terdaftar (Dilewati Otomatis)';
                        descEl.textContent = `Seluruh ${skippedCount} aset pada berkas Excel memiliki Kode Barang dan NUP yang sudah ada di sistem. Baris dilewati agar data eksisting tidak tertimpa.`;
                    } else if (failedCount > 0) {
                        titleEl.textContent = 'Impor Selesai dengan Catatan';
                        descEl.textContent = `${importedCount} data baru masuk, ${skippedCount} dilewati, dan ${failedCount} baris memerlukan koreksi format.`;
                    } else {
                        titleEl.textContent = 'Pemeriksaan Berkas Selesai';
                        descEl.textContent = 'Tidak ada baris data inventaris baru yang ditambahkan ke sistem.';
                    }

                    const tbody = document.getElementById('res-details-body');
                    tbody.innerHTML = '';
                    const issues = [];

                    (data.failed_rows || []).forEach(r => {
                        issues.push({
                            row: r.row,
                            status: '<span class="status-pill status-pill-danger" style="font-size: 11px; padding: 2px 8px; font-weight: 600;">Gagal</span>',
                            nama: r.nama_barang || 'Aset',
                            kode_barang: r.kode_barang || '-',
                            nup: r.nup ?? '-',
                            reason: r.reason || 'Format atau kelengkapan data tidak valid'
                        });
                    });

                    (data.skipped_rows || []).forEach(r => {
                        issues.push({
                            row: r.row,
                            status: '<span class="status-pill status-pill-warning" style="font-size: 11px; padding: 2px 8px; font-weight: 600;">Dilewati</span>',
                            nama: r.nama_barang || 'Aset',
                            kode_barang: r.kode_barang || '-',
                            nup: r.nup ?? '-',
                            reason: r.reason || 'Kode Barang dan NUP sudah terdaftar'
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
                                    <div style="font-size: 11px; color: var(--color-text-muted); margin-top: 2px;"><code>${it.kode_barang}</code> <span style="opacity: 0.5; margin: 0 3px;">&bull;</span> NUP: <strong style="color: var(--color-text);">${it.nup}</strong></div>
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
                        msg = 'Import data inventaris aset berhasil! Seluruh data (' + imported + ' data) berhasil disimpan.';
                    } else if (imported > 0) {
                        msg = 'Import data inventaris aset selesai: ' + imported + ' data baru disimpan (' + skipped + ' dilewati, ' + failed + ' gagal).';
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
            if (typeof AppLoader !== 'undefined') {
                AppLoader.hide();
            }
            if (typeof feather !== 'undefined') {
                feather.replace();
            }

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
            document.querySelectorAll('.table-loading-wrap').forEach(el => el.classList.remove('is-loading'));
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

        window.addEventListener('popstate', function() {
            document.querySelectorAll('.table-loading-wrap').forEach(el => el.classList.remove('is-loading'));
            restoreFilterState();
        });

        window.addEventListener('pageshow', function(event) {
            document.querySelectorAll('.table-loading-wrap').forEach(el => el.classList.remove('is-loading'));
            if (event.persisted) {
                restoreFilterState();
            }
        });
    </script>
</body>
</html>
