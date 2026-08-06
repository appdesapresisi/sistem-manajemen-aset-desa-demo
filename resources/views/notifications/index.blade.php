<!doctype html>
<html lang="en" data-pc-preset="{{ config('theme.pc_preset_theme', 'preset-1') }}" data-pc-theme="{{ config('theme.pc_dark_layout', 'light') }}">
<head>
    @include('layouts.head-page-meta', ['title' => 'Notifikasi'])
    @include('layouts.head-css')
    <style>
        .notif-card {
            background: #fff;
            border-radius: 20px;
            border: 1px solid rgba(0,0,0,0.04);
            box-shadow: 0 4px 24px rgba(0,0,0,0.06);
        }
        .notif-header {
            padding: 24px 28px;
            border-bottom: 1px solid #f3f4f6;
            display: flex;
            align-items: center;
            justify-content: space-between;
            flex-wrap: wrap;
            gap: 20px;
        }
        .notif-actions {
            display: flex;
            align-items: center;
            gap: 12px;
            flex-wrap: wrap;
            justify-content: flex-end;
        }
        .notif-title {
            display: flex;
            align-items: center;
            gap: 12px;
        }
        .notif-icon {
            width: 42px; height: 42px;
            border-radius: 12px;
            background: #519259 !important;
            display: flex; align-items: center; justify-content: center;
            flex-shrink: 0;
        }
        .notif-icon i, .notif-icon svg { width: 18px; height: 18px; color: #fff !important; stroke: #fff !important; }
        .notif-title h5 { margin: 0; font-size: 18px; font-weight: 600; color: #064635 !important; }
        .notif-title span { font-size: 13px; color: #9ca3af; }

        /* Search Box & Controls */
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

        /* Time Range Segmented Pills */
        .range-filter-group {
            display: flex;
            align-items: center;
            gap: 4px;
            background: #f3f4f6;
            padding: 4px;
            border-radius: 20px;
        }
        .range-pill-label {
            position: relative;
            cursor: pointer;
            margin: 0;
            flex: 1;
            text-align: center;
        }
        .range-pill-label input {
            position: absolute;
            opacity: 0;
            cursor: pointer;
            width: 0;
            height: 0;
        }
        .range-pill-label span {
            display: block;
            padding: 6px 12px;
            border-radius: 16px;
            font-size: 12px;
            font-weight: 600;
            color: #6b7280;
            transition: all 0.2s;
            user-select: none;
        }
        .range-pill-label input:checked + span {
            background: linear-gradient(135deg, #064635 0%, #2e7d5c 100%);
            color: #ffffff;
            box-shadow: 0 2px 8px rgba(6, 70, 53, 0.3);
        }

        .date-group-label {
            padding: 12px 28px;
            font-size: 11px;
            font-weight: 700;
            color: #064635;
            text-transform: uppercase;
            letter-spacing: 0.6px;
            background: #f4faf5;
            border-bottom: 1px solid #f3f4f6;
        }
        .notif-row-wrap {
            border-bottom: 1px solid #f3f4f6;
        }
        .notif-row-wrap:last-child {
            border-bottom: none;
        }
        .notif-row {
            display: grid;
            grid-template-columns: minmax(0, 1fr) 110px;
            align-items: center;
            gap: 16px;
            padding: 16px 28px;
            transition: background 0.2s;
            background: #ffffff;
        }
        .notif-row-wrap:hover .notif-row {
            background: #f9fafb;
        }
        .notif-row.unread {
            background: #ffffff;
        }
        .notif-row-wrap:hover .notif-row.unread {
            background: #f9fafb;
        }

        .notif-main-link {
            display: block;
            min-width: 0;
            color: inherit;
            text-decoration: none;
        }
        .notif-main-link:hover,
        .notif-main-link:focus {
            color: inherit;
            text-decoration: none;
        }
        .notif-main-top {
            margin-bottom: 3px;
        }
        .notif-row-title {
            font-size: 14px; font-weight: 500; color: #374151;
            margin: 0;
            line-height: 1.35;
        }
        .notif-row.unread .notif-row-title { font-weight: 700; color: #064635; }
        .notif-row-body-text {
            font-size: 13px; color: #6b7280; overflow: hidden;
            text-overflow: ellipsis; white-space: nowrap;
        }
        .notif-row-time {
            font-size: 11px;
            color: #9ca3af;
            text-align: right;
            line-height: 1.4;
        }
        .notif-right-col {
            display: flex;
            flex-direction: column;
            align-items: flex-end;
            justify-content: center;
            gap: 6px;
            min-width: 0;
        }
        .notif-delete-inline-form {
            margin: 0;
            display: flex;
            align-items: center;
            justify-content: center;
        }
        .notif-delete-inline-btn {
            width: 34px;
            height: 34px;
            border-radius: 8px;
            border: 1px solid #fecaca;
            background: #fff5f5;
            color: #dc2626;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            cursor: pointer;
            transition: all 0.15s;
        }
        .notif-delete-inline-btn:hover {
            background: #dc2626;
            color: #fff;
            border-color: #dc2626;
        }
        .notif-delete-inline-btn i,
        .notif-delete-inline-btn svg {
            width: 14px;
            height: 14px;
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
        .empty-state i, .empty-state svg { width: 48px; height: 48px; margin-bottom: 12px; opacity: 0.5; }

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
        .page-btn.disabled { opacity: 0.5; cursor: not-allowed; }
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

        /* Matched to Pasted Image 1 (.add-btn style) */
        .btn-mark-all {
            display: flex;
            align-items: center;
            gap: 8px;
            padding: 12px 20px;
            background: linear-gradient(135deg, #064635 0%, #2e7d5c 100%);
            color: #fff;
            border: none;
            border-radius: 12px;
            font-size: 14px;
            font-weight: 600;
            text-decoration: none;
            cursor: pointer;
            transition: all 0.3s;
            box-shadow: 0 4px 12px rgba(6, 70, 53, 0.3);
        }
        .btn-mark-all:hover {
            transform: translateY(-2px);
            box-shadow: 0 6px 20px rgba(6, 70, 53, 0.4);
            color: #fff;
        }
        .btn-mark-all i, .btn-mark-all svg { width: 18px; height: 18px; }

        .btn-delete-read {
            display: flex;
            align-items: center;
            gap: 8px;
            padding: 12px 20px;
            background: #fff5f5;
            color: #dc2626;
            border: 1px solid #fecaca;
            border-radius: 12px;
            font-size: 14px;
            font-weight: 600;
            cursor: pointer;
            transition: all 0.2s;
        }
        .btn-delete-read:hover {
            background: #dc2626;
            color: #fff;
            border-color: #dc2626;
        }
        .btn-delete-read i,
        .btn-delete-read svg {
            width: 18px;
            height: 18px;
        }

        @media (max-width: 768px) {
            .notif-row {
                grid-template-columns: minmax(0, 1fr) 94px;
                gap: 10px;
                padding: 12px 16px;
            }
            .date-group-label,
            .notif-header {
                padding-left: 16px;
                padding-right: 16px;
            }
            .notif-header {
                align-items: stretch;
            }
            .notif-title {
                width: 100%;
            }
            .notif-actions {
                width: 100%;
                justify-content: flex-start;
            }
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
                'breadcrumbItem' => 'Akun',
                'breadcrumbItemActive' => 'Notifikasi'
            ])

            <div class="notif-card">
                {{-- Header --}}
                <div class="notif-header">
                    <div class="notif-title">
                        <div class="notif-icon">
                            <i data-feather="bell"></i>
                        </div>
                        <div>
                            <h5>Notifikasi</h5>
                            <span id="notif-total-text">Total {{ $notifications->total() }} notifikasi</span>
                        </div>
                    </div>

                    <div class="notif-actions">
                        <div class="search-box">
                            <input type="text" id="searchInput" placeholder="Cari notifikasi..." value="{{ request('search') }}" oninput="onSearchInput(this)" />
                            <button id="search-icon-btn" onclick="applyFilters()"><i data-feather="search"></i></button>
                            <button id="clear-search-btn" onclick="clearSearch()" style="color:#ef4444; {{ request('search') ? '' : 'display:none;' }}"><i data-feather="x"></i></button>
                        </div>

                        <div class="dropdown" style="position:relative;">
                            <button class="filter-btn" data-pc-toggle="dropdown" title="Filter">
                                <i data-feather="sliders"></i>
                            </button>
                            <div class="dropdown-menu dropdown-menu-end py-3 px-4" onclick="event.stopPropagation();" style="min-width: 280px; z-index: 1050;">
                                <div class="text-sm font-semibold mb-2" style="color: #064635;">Rentang Waktu</div>
                                <div class="range-filter-group mb-3">
                                    <label class="range-pill-label">
                                        <input type="radio" name="rangeFilter" value="all" class="range-filter" {{ request('range', 'all') === 'all' ? 'checked' : '' }}>
                                        <span>Semua</span>
                                    </label>
                                    <label class="range-pill-label">
                                        <input type="radio" name="rangeFilter" value="7d" class="range-filter" {{ request('range') === '7d' ? 'checked' : '' }}>
                                        <span>7d</span>
                                    </label>
                                    <label class="range-pill-label">
                                        <input type="radio" name="rangeFilter" value="30d" class="range-filter" {{ request('range') === '30d' ? 'checked' : '' }}>
                                        <span>30d</span>
                                    </label>
                                    <label class="range-pill-label">
                                        <input type="radio" name="rangeFilter" value="90d" class="range-filter" {{ request('range') === '90d' ? 'checked' : '' }}>
                                        <span>90d</span>
                                    </label>
                                    <label class="range-pill-label">
                                        <input type="radio" name="rangeFilter" value="1y" class="range-filter" {{ request('range') === '1y' ? 'checked' : '' }}>
                                        <span>1y</span>
                                    </label>
                                </div>

                                <hr style="border-color: #e5e7eb; margin: 12px 0;" />

                                <div class="text-sm font-semibold mb-2" style="color: #064635;">Filter Status</div>
                                <div class="space-y-2" style="margin-bottom: 12px;">
                                    <label class="flex items-center gap-2 text-sm" style="cursor: pointer;">
                                        <input type="radio" name="statusFilter" value="all" class="status-filter" {{ request('status', 'all') === 'all' ? 'checked' : '' }} style="accent-color: #064635; cursor: pointer;"> Semua Notifikasi
                                    </label>
                                    <label class="flex items-center gap-2 text-sm" style="cursor: pointer;">
                                        <input type="radio" name="statusFilter" value="unread" class="status-filter" {{ request('status') === 'unread' ? 'checked' : '' }} style="accent-color: #064635; cursor: pointer;"> Belum Dibaca
                                    </label>
                                    <label class="flex items-center gap-2 text-sm" style="cursor: pointer;">
                                        <input type="radio" name="statusFilter" value="read" class="status-filter" {{ request('status') === 'read' ? 'checked' : '' }} style="accent-color: #064635; cursor: pointer;"> Sudah Dibaca
                                    </label>
                                </div>

                                <hr style="border-color: #e5e7eb; margin: 12px 0;" />

                                <div class="text-sm font-semibold mb-2" style="color: #064635;">Urutkan</div>
                                <div class="space-y-2" style="margin-bottom: 12px;">
                                    <label class="flex items-center gap-2 text-sm" style="cursor: pointer;">
                                        <input type="radio" name="sortFilter" value="terbaru" class="sort-filter" {{ request('sort', 'terbaru') === 'terbaru' ? 'checked' : '' }} style="accent-color: #064635; cursor: pointer;"> Terbaru
                                    </label>
                                    <label class="flex items-center gap-2 text-sm" style="cursor: pointer;">
                                        <input type="radio" name="sortFilter" value="terlama" class="sort-filter" {{ request('sort', 'terlama') === 'terlama' ? 'checked' : '' }} style="accent-color: #064635; cursor: pointer;"> Terlama
                                    </label>
                                </div>
                                <div style="padding-top: 12px; border-top: 1px solid #e5e7eb;">
                                    <button onclick="applyFilters(); event.stopPropagation();" style="width: 100%; padding: 10px 16px; background: linear-gradient(135deg, #064635 0%, #2e7d5c 100%); color: white; border: none; border-radius: 8px; font-size: 14px; font-weight: 600; cursor: pointer; transition: all 0.2s; box-shadow: 0 3px 8px rgba(6, 70, 53, 0.25);">
                                        Terapkan Filter
                                    </button>
                                    <button onclick="resetFilters(); event.stopPropagation();" style="width: 100%; padding: 8px 16px; background: transparent; color: #6b7280; border: 1px solid #d1d5db; border-radius: 8px; font-size: 13px; margin-top: 8px; cursor: pointer; transition: all 0.2s;">
                                        Reset Filter
                                    </button>
                                </div>
                            </div>
                        </div>

                        <div id="markAllWrap" style="{{ $unreadCount > 0 ? '' : 'display:none;' }}">
                            <form method="POST" action="{{ route('notifications.mark-all') }}" id="markAllForm">
                                @csrf
                                <button type="submit" class="btn-mark-all">
                                    <i data-feather="check-circle"></i>
                                    Tandai semua dibaca
                                </button>
                            </form>
                        </div>

                        <div id="deleteReadWrap" style="{{ ($readCount ?? 0) > 0 ? '' : 'display:none;' }}">
                            <form id="deleteReadForm" method="POST" action="{{ route('notifications.delete-read') }}">
                                @csrf
                                <button type="button" class="btn-delete-read" onclick="confirmDeleteReadNotifications()">
                                    <i data-feather="trash-2"></i>
                                    Hapus semua dibaca
                                </button>
                            </form>
                        </div>
                    </div>
                </div>

                {{-- Notification list container --}}
                <div id="notif-list-container">
                    @forelse($grouped as $dateLabel => $items)
                        <div class="date-group-label">{{ $dateLabel }}</div>
                        @foreach($items as $notif)
                        <div class="notif-row-wrap">
                            <div class="notif-row {{ !$notif->is_read ? 'unread' : '' }}">
                                <a href="{{ route('notifications.read', $notif->id) }}" class="notif-main-link">
                                    <div class="notif-main-top">
                                        <div class="notif-row-title">
                                            {{ $notif->title }}
                                        </div>
                                    </div>

                                    @if($notif->body)
                                    <div class="notif-row-body-text">{{ $notif->body }}</div>
                                    @endif

                                    @if($notif->actor_nama)
                                    @php
                                        $actorLabel = match($notif->type) {
                                            'jenis_inventaris_created' => 'Ditambahkan oleh',
                                            default                    => 'Oleh',
                                        };
                                    @endphp
                                    <div style="font-size:11px;color:#6b7280;margin-top:4px;display:flex;align-items:center;gap:4px;">
                                        <i data-feather="user" style="width:11px;height:11px;flex-shrink:0;stroke:#9ca3af;"></i>
                                        <span>{{ $actorLabel }}: <strong>{{ $notif->actor_nama }}</strong></span>
                                    </div>
                                    @endif
                                </a>

                                <div class="notif-right-col">
                                    <div class="notif-row-time">
                                        <div>{{ $notif->created_at?->translatedFormat('d M Y') }}</div>
                                        <div style="color:#b0b7c3;">{{ $notif->created_at?->format('H:i') }}</div>
                                    </div>

                                    <form id="deleteNotifForm{{ $notif->id }}" method="POST" action="{{ route('notifications.destroy', $notif->id) }}" class="notif-delete-inline-form">
                                        @csrf
                                        @method('DELETE')
                                        <button type="button" class="notif-delete-inline-btn" title="Hapus notifikasi" onclick="confirmDeleteNotification({{ $notif->id }})">
                                            <i data-feather="trash-2"></i>
                                        </button>
                                    </form>
                                </div>
                            </div>
                        </div>
                        @endforeach
                    @empty
                        <div class="empty-state">
                            <i data-feather="bell-off"></i>
                            <div style="font-size:15px;font-weight:600;color:#6b7280;margin-bottom:4px;">Tidak ada notifikasi</div>
                            <div style="font-size:13px;">Aktivitas sistem akan muncul di sini</div>
                        </div>
                    @endforelse
                </div>

                {{-- Standard 3-Column Pagination Bar Footer --}}
                <div class="pagination-bar" id="paginationBar">
                    <div class="pagination-left">
                        <div class="page-size-wrap">
                            <span>Tampilkan</span>
                            <select id="perPageSelect" class="per-page-select" onchange="changePerPage(this.value)">
                                <option value="10" {{ ($notifications->perPage() == 10) ? 'selected' : '' }}>10</option>
                                <option value="20" {{ ($notifications->perPage() == 20) ? 'selected' : '' }}>20</option>
                                <option value="50" {{ ($notifications->perPage() == 50) ? 'selected' : '' }}>50</option>
                                <option value="100" {{ ($notifications->perPage() == 100) ? 'selected' : '' }}>100</option>
                            </select>
                            <span>data</span>
                        </div>
                    </div>

                    <div class="pagination-center">
                        <nav class="pagination-nav" id="paginationNav">
                            @if ($notifications->onFirstPage())
                                <span class="page-btn disabled" title="Previous"><i data-feather="chevron-left"></i></span>
                            @else
                                <button type="button" class="page-btn" onclick="goToPage({{ $notifications->currentPage() - 1 }})" title="Previous"><i data-feather="chevron-left"></i></button>
                            @endif

                            @php
                                $currentPage  = $notifications->currentPage();
                                $lastPage     = $notifications->lastPage();
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

                            @if ($notifications->hasMorePages())
                                <button type="button" class="page-btn" onclick="goToPage({{ $notifications->currentPage() + 1 }})" title="Next"><i data-feather="chevron-right"></i></button>
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
                                max="{{ $notifications->lastPage() }}"
                                value="{{ $notifications->currentPage() }}"
                                placeholder="1–{{ $notifications->lastPage() }}"
                                title="Masukkan nomor halaman (1–{{ $notifications->lastPage() }})"
                            >
                        </div>
                    </div>

                    <div class="pagination-right" id="paginationRangeInfo">
                        Menampilkan <strong>{{ $notifications->firstItem() ?? 0 }}-{{ $notifications->lastItem() ?? 0 }}</strong> dari <strong>{{ $notifications->total() }}</strong> data
                    </div>
                </div>
            </div>
        </div>
    </div>
    @include('layouts.footer-block')
    @include('layouts.footer-js')
    <script>
        function confirmDeleteNotification(notificationId) {
            AppModal.confirmDelete('notifikasi ini', function() {
                const form = document.getElementById('deleteNotifForm' + notificationId);
                if (form) form.submit();
            });
        }

        function confirmDeleteReadNotifications() {
            AppModal.confirm(
                'Hapus Semua Notifikasi Dibaca',
                'Apakah Anda yakin ingin menghapus semua notifikasi yang sudah dibaca? Aksi ini tidak dapat dibatalkan.',
                function() {
                    const form = document.getElementById('deleteReadForm');
                    if (form) form.submit();
                }
            );
        }

        let _searchTimer = null;
        let currentPage = {{ $notifications->currentPage() }};

        function onSearchInput(input) {
            toggleSearchClear(input);
            clearTimeout(_searchTimer);
            _searchTimer = setTimeout(function() { fetchNotificationsAJAX(1); }, 350);
        }

        function toggleSearchClear(input) {
            const clearBtn = document.getElementById('clear-search-btn');
            const iconBtn = document.getElementById('search-icon-btn');
            if (clearBtn) clearBtn.style.display = input.value ? '' : 'none';
            if (iconBtn) iconBtn.style.display = input.value ? 'none' : '';
        }

        function clearSearch() {
            const input = document.getElementById('searchInput');
            if (input) input.value = '';
            toggleSearchClear(input);
            fetchNotificationsAJAX(1);
        }

        function applyFilters() {
            fetchNotificationsAJAX(1);
            document.querySelectorAll('.dropdown-menu.show').forEach(el => el.classList.remove('show'));
            document.querySelectorAll('.dropdown.show').forEach(el => el.classList.remove('show'));
        }

        function resetFilters() {
            const input = document.getElementById('searchInput');
            if (input) input.value = '';
            toggleSearchClear(input);

            const defaultRange = document.querySelector('.range-filter[value="all"]');
            if (defaultRange) defaultRange.checked = true;

            const defaultStatus = document.querySelector('.status-filter[value="all"]');
            if (defaultStatus) defaultStatus.checked = true;

            const defaultSort = document.querySelector('.sort-filter[value="terbaru"]');
            if (defaultSort) defaultSort.checked = true;

            const perPageSelect = document.getElementById('perPageSelect');
            if (perPageSelect) perPageSelect.value = '10';

            fetchNotificationsAJAX(1);
            document.querySelectorAll('.dropdown-menu.show').forEach(el => el.classList.remove('show'));
            document.querySelectorAll('.dropdown.show').forEach(el => el.classList.remove('show'));
        }

        function changePerPage(val) {
            fetchNotificationsAJAX(1);
        }

        function goToPage(page) {
            currentPage = page;
            fetchNotificationsAJAX(page);
        }

        // ─── Instant Pure AJAX Fetch (No page reload) ─────────────────────
        function fetchNotificationsAJAX(page = 1) {
            const searchVal  = document.getElementById('searchInput')?.value || '';
            const rangeVal   = document.querySelector('.range-filter:checked')?.value || 'all';
            const statusVal  = document.querySelector('.status-filter:checked')?.value || 'all';
            const sortVal    = document.querySelector('.sort-filter:checked')?.value || 'terbaru';
            const perPageVal = document.getElementById('perPageSelect')?.value || '10';

            const params = new URLSearchParams();
            if (searchVal) params.set('search', searchVal);
            if (rangeVal !== 'all') params.set('range', rangeVal);
            if (statusVal !== 'all') params.set('status', statusVal);
            if (sortVal !== 'terbaru') params.set('sort', sortVal);
            if (perPageVal && perPageVal !== '10') params.set('per_page', perPageVal);
            if (page > 1) params.set('page', page);

            // Update browser URL without reloading page
            history.replaceState(null, '', window.location.pathname + (params.toString() ? '?' + params.toString() : ''));

            fetch('{{ route('notifications.index') }}?' + params.toString(), {
                headers: { 'X-Requested-With': 'XMLHttpRequest', 'Accept': 'application/json' }
            })
            .then(r => r.json())
            .then(data => {
                // Update total count span
                const totalSpan = document.getElementById('notif-total-text');
                if (totalSpan) totalSpan.textContent = 'Total ' + data.total + ' notifikasi';

                // Update Mark All & Delete Read button visibility
                const markAllWrap = document.getElementById('markAllWrap');
                if (markAllWrap) markAllWrap.style.display = data.unreadCount > 0 ? '' : 'none';

                const deleteReadWrap = document.getElementById('deleteReadWrap');
                if (deleteReadWrap) deleteReadWrap.style.display = data.readCount > 0 ? '' : 'none';

                // Render list
                renderNotifList(data.grouped);

                // Render pagination bar
                updatePaginationUI(data.pagination);

                if (typeof feather !== 'undefined') feather.replace();
            })
            .catch(err => console.error('fetchNotificationsAJAX error:', err));
        }

        function renderNotifList(grouped) {
            const container = document.getElementById('notif-list-container');
            if (!container) return;

            if (!grouped || grouped.length === 0) {
                container.innerHTML = `
                    <div class="empty-state">
                        <i data-feather="bell-off"></i>
                        <div style="font-size:15px;font-weight:600;color:#6b7280;margin-bottom:4px;">Tidak ada notifikasi</div>
                        <div style="font-size:13px;">Aktivitas sistem akan muncul di sini</div>
                    </div>`;
                return;
            }

            let html = '';
            grouped.forEach(group => {
                html += `<div class="date-group-label">${escapeHtml(group.label)}</div>`;
                group.items.forEach(item => {
                    const isUnread = !item.is_read;
                    const unreadClass = isUnread ? 'unread' : '';
                    const actorHtml = item.actor_nama ? `
                        <div style="font-size:11px;color:#6b7280;margin-top:4px;display:flex;align-items:center;gap:4px;">
                            <i data-feather="user" style="width:11px;height:11px;flex-shrink:0;stroke:#9ca3af;"></i>
                            <span>Oleh: <strong>${escapeHtml(item.actor_nama)}</strong></span>
                        </div>` : '';
                    const bodyHtml = item.body ? `<div class="notif-row-body-text">${escapeHtml(item.body)}</div>` : '';

                    html += `
                    <div class="notif-row-wrap">
                        <div class="notif-row ${unreadClass}">
                            <a href="${escapeHtml(item.read_url)}" class="notif-main-link">
                                <div class="notif-main-top">
                                    <div class="notif-row-title">${escapeHtml(item.title)}</div>
                                </div>
                                ${bodyHtml}
                                ${actorHtml}
                            </a>

                            <div class="notif-right-col">
                                <div class="notif-row-time">
                                    <div>${escapeHtml(item.date_formatted)}</div>
                                    <div style="color:#b0b7c3;">${escapeHtml(item.time_formatted)}</div>
                                </div>

                                <form id="deleteNotifForm${item.id}" method="POST" action="${escapeHtml(item.destroy_url)}" class="notif-delete-inline-form">
                                    <input type="hidden" name="_token" value="{{ csrf_token() }}">
                                    <input type="hidden" name="_method" value="DELETE">
                                    <button type="button" class="notif-delete-inline-btn" title="Hapus notifikasi" onclick="confirmDeleteNotification(${item.id})">
                                        <i data-feather="trash-2"></i>
                                    </button>
                                </form>
                            </div>
                        </div>
                    </div>`;
                });
            });

            container.innerHTML = html;
        }

        function updatePaginationUI(pagination) {
            if (!pagination) return;
            const firstItem = pagination.firstItem || 0;
            const lastItem  = pagination.lastItem || 0;
            const total     = pagination.total !== undefined ? pagination.total : 0;
            const cPage     = pagination.currentPage || 1;
            const lPage     = pagination.lastPage || 1;
            currentPage     = cPage;

            const rangeEl = document.getElementById('paginationRangeInfo');
            if (rangeEl) {
                rangeEl.innerHTML = `Menampilkan <strong>${firstItem}-${lastItem}</strong> dari <strong>${total}</strong> data`;
            }

            const jumpInput = document.getElementById('pageJumpInput');
            if (jumpInput) {
                jumpInput.max = lPage;
                jumpInput.placeholder = `1–${lPage}`;
                jumpInput.title = `Masukkan nomor halaman (1–${lPage})`;
                jumpInput.value = cPage;
            }

            const nav = document.getElementById('paginationNav');
            if (!nav) return;

            let html = '';
            if (cPage <= 1) {
                html += `<span class="page-btn disabled" title="Previous"><i data-feather="chevron-left"></i></span>`;
            } else {
                html += `<button type="button" class="page-btn" onclick="goToPage(${cPage - 1})" title="Previous"><i data-feather="chevron-left"></i></button>`;
            }

            let windowStart = Math.max(1, cPage - 2);
            let windowEnd   = Math.min(lPage, cPage + 2);
            if ((windowEnd - windowStart + 1) < 5) {
                if (windowStart === 1) {
                    windowEnd = Math.min(lPage, 5);
                } else {
                    windowStart = Math.max(1, windowEnd - 4);
                }
            }

            for (let p = windowStart; p <= windowEnd; p++) {
                if (p === cPage) {
                    html += `<span class="page-btn active">${p}</span>`;
                } else {
                    html += `<button type="button" class="page-btn" onclick="goToPage(${p})">${p}</button>`;
                }
            }

            if (cPage >= lPage) {
                html += `<span class="page-btn disabled" title="Next"><i data-feather="chevron-right"></i></span>`;
            } else {
                html += `<button type="button" class="page-btn" onclick="goToPage(${cPage + 1})" title="Next"><i data-feather="chevron-right"></i></button>`;
            }

            nav.innerHTML = html;
        }

        function escapeHtml(value) {
            return String(value ?? '')
                .replace(/&/g, '&amp;')
                .replace(/</g, '&lt;')
                .replace(/>/g, '&gt;')
                .replace(/"/g, '&quot;')
                .replace(/'/g, '&#039;');
        }

        // ─── Init ──────────────────────────────────────────
        document.addEventListener('DOMContentLoaded', function () {
            if (typeof feather !== 'undefined') feather.replace();

            const jumpInput = document.getElementById('pageJumpInput');
            if (jumpInput) {
                jumpInput.addEventListener('keydown', function(e) {
                    if (e.key === 'Enter') {
                        e.preventDefault();
                        let p = parseInt(this.value, 10);
                        const maxP = parseInt(this.max, 10) || 1;
                        if (isNaN(p) || p < 1) p = 1;
                        if (p > maxP) p = maxP;
                        goToPage(p);
                    }
                });
                jumpInput.addEventListener('change', function() {
                    let p = parseInt(this.value, 10);
                    const maxP = parseInt(this.max, 10) || 1;
                    if (isNaN(p) || p < 1) p = 1;
                    if (p > maxP) p = maxP;
                    goToPage(p);
                });
            }
        });
    </script>
</body>
</html>
