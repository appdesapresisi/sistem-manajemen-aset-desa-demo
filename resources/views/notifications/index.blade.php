<!doctype html>
<html lang="id">
<head>
    @include('layouts.head-page-meta', ['title' => 'Pusat Notifikasi - Sistem Manajemen Aset Desa'])
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

        /* Time Range Dropdown Styling */
        .range-dropdown-wrap {
            width: 240px;
        }
        #btn-filter-range {
            width: 100% !important;
            transition: all 0.2s ease;
        }
        #btn-filter-range[aria-expanded="true"] {
            border-color: var(--color-primary) !important;
            background-color: var(--color-row-hover) !important;
        }
        #btn-filter-range[aria-expanded="true"] svg.feather-chevron-down {
            transform: rotate(180deg);
        }
        #btn-filter-range svg.feather-chevron-down {
            transition: transform 0.2s ease;
        }
        #range-dropdown-menu,
        .dropdown-menu.range-dropdown-menu {
            width: 100% !important;
            min-width: 100% !important;
            max-width: 100% !important;
            box-sizing: border-box !important;
            margin-top: 8px !important;
            left: 0 !important;
            right: 0 !important;
        }
        .range-option-item {
            transition: background 0.15s ease, color 0.15s ease;
        }
        .range-option-item:hover {
            background-color: var(--color-row-hover) !important;
        }
        .range-option-item.is-selected {
            background-color: var(--color-primary-tint, #d5e4d7) !important;
            color: var(--color-primary, #1f3a2a) !important;
            font-weight: 600;
        }

        /* Date Group Label */
        .date-group-label {
            padding: 10px 24px;
            font-size: 11px;
            font-weight: 700;
            color: var(--color-primary, #1f3a2a);
            text-transform: uppercase;
            letter-spacing: 0.05em;
            background: var(--color-bg, #eaf1e7);
            border-bottom: 1px solid var(--color-border, #cfdcc8);
        }

        /* Notification Row Standard */
        .notif-row-wrap {
            border-bottom: 1px solid var(--color-border, #cfdcc8);
            transition: background 0.15s ease;
        }
        .notif-row-wrap:last-child {
            border-bottom: none;
        }
        .notif-row {
            display: flex;
            align-items: center;
            gap: 16px;
            padding: 16px 24px;
            background: var(--color-surface, #f8faf7);
            transition: background 0.15s ease;
            text-decoration: none;
            color: inherit;
        }
        .notif-row-icon {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            align-self: center !important;
            margin: 0 !important;
            flex-shrink: 0 !important;
        }
        .notif-row.unread {
            background: var(--color-surface, #f8faf7);
        }
        .notif-row-wrap:hover .notif-row {
            background: var(--color-row-hover, #eff5ed);
        }


        .notif-row-title {
            font-size: 14px;
            font-weight: 600;
            color: var(--color-text, #16251b);
            line-height: 1.35;
            margin-bottom: 3px;
        }
        .notif-row.unread .notif-row-title {
            font-weight: 700;
            color: var(--color-text, #16251b);
        }
        .notif-row-body-text {
            font-size: 13px;
            color: var(--color-text-muted, #546958);
            line-height: 1.45;
            margin-bottom: 6px;
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
            background: var(--color-surface, #f8faf7);
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
            width: 48px;
            height: 32px;
            border: 1px solid var(--color-border);
            border-radius: var(--radius-sm);
            text-align: center;
            font-size: 13px;
            color: var(--color-text);
            background: var(--color-surface);
            outline: none;
        }

        .empty-state {
            padding: 60px 24px;
            display: flex;
            flex-direction: column;
            align-items: center;
            justify-content: center;
            text-align: center;
            color: var(--color-text-muted, #546958);
        }
        .empty-state i {
            width: 40px;
            height: 40px;
            margin-bottom: 12px;
            opacity: 0.6;
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
                        Pusat Notifikasi
                    </h1>
                </div>
                <div style="display: flex; gap: 10px; align-items: center; flex-wrap: wrap;">
                    <div id="markAllWrap" style="{{ $unreadCount > 0 ? '' : 'display:none;' }}">
                        <form method="POST" action="{{ route('notifications.mark-all') }}" id="markAllForm">
                            @csrf
                            <button type="submit" class="btn-sima-primary">
                                <i data-feather="check-circle" style="width: 14px; height: 14px;"></i>
                                <span>Tandai Semua Dibaca</span>
                            </button>
                        </form>
                    </div>

                    <div id="deleteReadWrap" style="{{ ($readCount ?? 0) > 0 ? '' : 'display:none;' }}">
                        <form id="deleteReadForm" method="POST" action="{{ route('notifications.delete-read') }}">
                            @csrf
                            <button type="button" class="btn-sima-danger" onclick="confirmDeleteReadNotifications()">
                                <i data-feather="trash-2" style="width: 14px; height: 14px;"></i>
                                <span>Hapus Sudah Dibaca</span>
                            </button>
                        </form>
                    </div>
                </div>
            </div>

            <!-- 1. STAT CARDS (Prodex Summary Bar) -->
            <div class="stat-card-row">
                <!-- Card 1: Total Notifikasi -->
                <div class="stat-card-sima is-clickable is-active-filter" id="card-status-all" onclick="filterByCardStatus('all')" title="Klik untuk menampilkan semua notifikasi">
                    <div class="stat-header">
                        <span style="font-weight: 600;">TOTAL NOTIFIKASI</span>
                    </div>
                    <div class="stat-number stat-card-value">{{ number_format($notifications->total(), 0, ',', '.') }}</div>
                    <div class="kpi-trend-wrapper stat-delta">
                        <span class="stat-delta" style="color: var(--color-text-muted);">Semua riwayat masuk</span>
                    </div>
                </div>

                <!-- Card 2: Belum Dibaca -->
                <div class="stat-card-sima is-clickable" id="card-status-unread" onclick="filterByCardStatus('unread')" title="Klik untuk memfilter belum dibaca">
                    <div class="stat-header">
                        <span style="font-weight: 600;">BELUM DIBACA</span>
                    </div>
                    <div class="stat-number stat-card-value">{{ number_format($unreadCount, 0, ',', '.') }}</div>
                    <div class="kpi-trend-wrapper stat-delta">
                        <span class="stat-delta" style="color: var(--color-warning);">Perlu tanggapan</span>
                    </div>
                </div>

                <!-- Card 3: Sudah Dibaca -->
                <div class="stat-card-sima is-clickable" id="card-status-read" onclick="filterByCardStatus('read')" title="Klik untuk memfilter sudah dibaca">
                    <div class="stat-header">
                        <span style="font-weight: 600;">SUDAH DIBACA</span>
                    </div>
                    <div class="stat-number stat-card-value">{{ number_format($readCount, 0, ',', '.') }}</div>
                    <div class="kpi-trend-wrapper stat-delta">
                        <span class="stat-delta" style="color: var(--color-success);">Telah diarsipkan</span>
                    </div>
                </div>
            </div>

            <!-- 2. STATUS TABS (Underline Pattern) -->
            <div class="tabs-sima">
                <div class="tab-item-sima {{ request('status', 'all') === 'all' ? 'active' : '' }}" id="tab-status-all" onclick="filterByTabStatus('all')">
                    <span>Semua Notifikasi</span>
                    <span class="tab-badge-sima">{{ number_format($notifications->total(), 0, ',', '.') }}</span>
                </div>
                <div class="tab-item-sima {{ request('status') === 'unread' ? 'active' : '' }}" id="tab-status-unread" onclick="filterByTabStatus('unread')">
                    <span>Belum Dibaca</span>
                    <span class="tab-badge-sima" style="background: var(--color-warning-soft); color: var(--color-warning);">{{ number_format($unreadCount, 0, ',', '.') }}</span>
                </div>
                <div class="tab-item-sima {{ request('status') === 'read' ? 'active' : '' }}" id="tab-status-read" onclick="filterByTabStatus('read')">
                    <span>Sudah Dibaca</span>
                    <span class="tab-badge-sima" style="background: var(--color-success-soft); color: var(--color-success);">{{ number_format($readCount, 0, ',', '.') }}</span>
                </div>
            </div>

            <!-- 3. TABLE / CARD CONTAINER (Prodex Table Standard) -->
            <div class="sima-card" style="padding: 0; overflow: visible; margin-bottom: 32px;">
                
                <!-- Action Controls Bar -->
                <div style="padding: 20px 20px; border-bottom: 1px solid var(--color-border); display: flex; justify-content: space-between; align-items: center; flex-wrap: wrap; gap: 12px; border-top-left-radius: var(--radius-lg, 12px); border-top-right-radius: var(--radius-lg, 12px);">
                    <div style="display: flex; align-items: center; gap: 10px; flex: 1; max-width: 420px;">
                        <!-- Search Box with Shortcut Badge -->
                        <div class="topbar-search-box" style="height: 36px; background: var(--color-surface); box-sizing: border-box;">
                            <i data-feather="search" style="width: 15px; height: 15px; color: var(--color-text-muted); flex-shrink: 0;"></i>
                            <input type="text" id="searchInput" class="topbar-search-input" placeholder="Cari isi pemberitahuan..." oninput="onSearchInput(this)" onkeypress="if(event.key==='Enter'){applyFilters();}" value="{{ request('search') }}" />
                            <span class="topbar-search-shortcut" id="clear-search-btn" onclick="clearSearch()" style="cursor: pointer; display: {{ request('search') ? 'inline-block' : 'none' }}; color: var(--color-danger);">Batal</span>
                        </div>
                    </div>

                    <div style="display: flex; align-items: center; gap: 10px; flex-wrap: wrap;">
                        <!-- Time Range Dropdown -->
                        <div class="dropdown range-dropdown-wrap" style="position: relative; width: 280px;">
                            @php
                                $currentRange = request('range', 'all');
                                $rangeLabels = [
                                    'all' => 'Semua Waktu',
                                    '7d'  => '7 Hari Terakhir',
                                    '30d' => '30 Hari Terakhir',
                                    '90d' => '90 Hari Terakhir',
                                ];
                                $currentRangeLabel = $rangeLabels[$currentRange] ?? 'Semua Waktu';
                            @endphp
                            <button type="button" class="btn-sima-secondary" id="btn-filter-range" data-bs-toggle="dropdown" data-pc-toggle="dropdown" data-bs-offset="0,4" aria-expanded="false" title="Filter Periode Waktu" style="width: 100%; height: 36px; padding: 0 14px; font-size: 13px; display: inline-flex; align-items: center; justify-content: space-between; box-sizing: border-box; border-radius: var(--radius-sm);">
                                <div style="display: flex; align-items: center; gap: 8px; white-space: nowrap;">
                                    <i data-feather="calendar" style="width: 14px; height: 14px; color: var(--color-primary); flex-shrink: 0;"></i>
                                    <span style="white-space: nowrap;">Periode: <strong id="range-filter-text" style="font-weight: 600; color: var(--color-text);">{{ $currentRangeLabel }}</strong></span>
                                </div>
                                <i data-feather="chevron-down" style="width: 13px; height: 13px; opacity: 0.7; flex-shrink: 0; margin-left: 6px;"></i>
                            </button>
                            <div class="dropdown-menu range-dropdown-menu" id="range-dropdown-menu" onclick="event.stopPropagation();" style="width: 100% !important; min-width: 100% !important; max-width: 100% !important; left: 0 !important; right: 0 !important; box-sizing: border-box; padding: 14px; margin-top: 4px !important; top: calc(100% + 4px) !important; z-index: 1050; background: var(--color-surface); border: 1px solid var(--color-border); border-radius: var(--radius-lg); box-shadow: var(--shadow-float);">
                                <div style="color: var(--color-primary); font-size: 11.5px; font-weight: 700; text-transform: uppercase; letter-spacing: 0.04em; margin-bottom: 10px;">Rentang Waktu</div>
                                <div style="display: flex; flex-direction: column; gap: 4px;">
                                    @foreach($rangeLabels as $rVal => $rLabel)
                                    <div class="range-option-item {{ $currentRange === $rVal ? 'is-selected' : '' }}" data-value="{{ $rVal }}" onclick="selectRangeFilter('{{ $rVal }}', '{{ $rLabel }}')" style="cursor: pointer; display: flex; align-items: center; gap: 10px; font-size: 13px; color: var(--color-text); padding: 7px 10px; border-radius: var(--radius-sm);">
                                        <input type="radio" name="rangeFilter" value="{{ $rVal }}" class="range-filter" {{ $currentRange === $rVal ? 'checked' : '' }} style="accent-color: var(--color-primary); cursor: pointer; width: 15px; height: 15px; margin: 0; pointer-events: none;">
                                        <span style="user-select: none;">{{ $rLabel }}</span>
                                    </div>
                                    @endforeach
                                </div>
                            </div>
                        </div>

                        <!-- Reset Filter Button -->
                        <button type="button" class="btn-sima-secondary btn-reset-filter" id="btn-reset-filter" onclick="resetFilters()" title="Reset Filter" style="height: 36px; padding: 0 14px; font-size: 13px; display: inline-flex; align-items: center; justify-content: center; gap: 6px; box-sizing: border-box;">
                            <i data-feather="rotate-ccw" style="width: 13px; height: 13px;"></i>
                            <span>Reset</span>
                        </button>
                    </div>
                </div>

                {{-- Notification list container --}}
                <div id="notif-list-container">
                    @forelse($grouped as $dateLabel => $items)
                        <div class="date-group-label">{{ $dateLabel }}</div>
                        @foreach($items as $notif)
                        @php
                            $isUnread = !$notif->is_read;
                            $icon = $notif->icon ?? 'bell';
                        @endphp
                        <div class="notif-row-wrap">
                            <div class="notif-row {{ $isUnread ? 'unread' : '' }}">
                                <!-- Clean Direct Icon (Centered Vertically) -->
                                <i data-feather="{{ $icon }}" class="notif-row-icon" style="width: 18px; height: 18px; color: var(--color-text-muted, #546958); flex-shrink: 0; align-self: center; margin: 0;"></i>

                                <!-- Notification Content -->
                                <div style="flex: 1; min-width: 0;">
                                    <a href="{{ route('notifications.read', $notif->id) }}" style="text-decoration: none; color: inherit; display: block;">
                                        <div class="notif-row-title">
                                            <span>{{ $notif->title }}</span>
                                        </div>

                                        @if($notif->body)
                                        <div class="notif-row-body-text">
                                            {{ $notif->body }}
                                        </div>
                                        @endif

                                        <div style="display: flex; align-items: center; gap: 8px; flex-wrap: wrap;">
                                            <span style="font-size: 12px; color: var(--color-text-muted, #546958); font-weight: 500;">
                                                {{ $notif->created_at ? ($notif->created_at->diffForHumans(null, true) . ' lalu') : '' }}
                                            </span>
                                            @if($notif->actor_nama)
                                            <span style="font-size: 12px; color: var(--color-text-muted, #546958);">
                                                &bull; Oleh: <strong>{{ $notif->actor_nama }}</strong>
                                            </span>
                                            @endif
                                        </div>
                                    </a>
                                </div>

                                <!-- Actions & Timestamp -->
                                <div style="display: flex; flex-direction: column; align-items: flex-end; gap: 8px; flex-shrink: 0;">
                                    <span style="font-size: 11.5px; color: var(--color-text-muted, #546958); font-weight: 500;">
                                        {{ $notif->created_at?->translatedFormat('d M Y') }} {{ $notif->created_at?->format('H:i') }}
                                    </span>
                                    <form id="deleteNotifForm{{ $notif->id }}" method="POST" action="{{ route('notifications.destroy', $notif->id) }}" style="margin: 0;">
                                        @csrf
                                        @method('DELETE')
                                        <button type="button" class="btn-sima-icon" title="Hapus notifikasi" onclick="confirmDeleteNotification({{ $notif->id }})" style="width: 30px; height: 30px;">
                                            <i data-feather="trash-2" style="width: 14px; height: 14px; color: var(--color-danger, #A63D2F);"></i>
                                        </button>
                                    </form>
                                </div>
                            </div>
                        </div>
                        @endforeach
                    @empty
                        <div style="display: flex; flex-direction: column; align-items: center; justify-content: center; gap: 8px; padding: 48px 20px; color: var(--color-text-muted);">
                            <i data-feather="inbox" style="width: 42px; height: 42px; stroke-width: 1.5; color: var(--color-border-dark);"></i>
                            <p style="margin: 0; font-size: 13.5px; font-weight: 500;">Belum ada notifikasi yang sesuai</p>
                        </div>
                    @endforelse
                </div>

                {{-- Standard 3-Column Pagination Bar Footer --}}
                <div class="pagination-bar" id="paginationBar"  style="padding: 32px 20px; border-top: 1px solid var(--color-border); background: var(--color-surface); flex-wrap: wrap; gap: 14px; border-bottom-left-radius: var(--radius-lg, 12px); border-bottom-right-radius: var(--radius-lg, 12px);">
                    <div class="pagination-left">
                        <div class="page-size-wrap">
                            <span>Tampilkan</span>
                            <select id="perPageSelect" class="per-page-select" onchange="changePerPage(this.value)">
                                <option value="10" {{ request('per_page') == 10 ? 'selected' : '' }}>10</option>
                                <option value="20" {{ request('per_page') == 20 ? 'selected' : '' }}>20</option>
                                <option value="50" {{ request('per_page') == 50 ? 'selected' : '' }}>50</option>
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

                            @for ($i = max(1, $notifications->currentPage() - 2); $i <= min($notifications->lastPage(), $notifications->currentPage() + 2); $i++)
                                @if ($i == $notifications->currentPage())
                                    <span class="page-btn active">{{ $i }}</span>
                                @else
                                    <button type="button" class="page-btn" onclick="goToPage({{ $i }})">{{ $i }}</button>
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
                                title="Masukkan nomor halaman"
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
            AppModal.show({
                type: 'danger',
                title: 'Hapus Notifikasi Dibaca',
                message: 'Apakah Anda yakin ingin menghapus semua notifikasi yang sudah dibaca? Aksi ini tidak dapat dibatalkan.',
                confirmText: 'Hapus Semua',
                cancelText: 'Batal',
                showCancel: true,
                onConfirm: function() {
                    const form = document.getElementById('deleteReadForm');
                    if (form) form.submit();
                }
            });
        }

        let _searchTimer = null;
        let currentPage = {{ $notifications->currentPage() }};
        let currentStatusFilter = '{{ request("status", "all") }}';

        function onSearchInput(input) {
            toggleSearchClear(input);
            clearTimeout(_searchTimer);
            _searchTimer = setTimeout(function() { fetchNotificationsAJAX(1); }, 350);
        }

        function toggleSearchClear(input) {
            const clearBtn = document.getElementById('clear-search-btn');
            if (clearBtn) clearBtn.style.display = input.value ? 'inline-block' : 'none';
        }

        function clearSearch() {
            const input = document.getElementById('searchInput');
            if (input) input.value = '';
            toggleSearchClear(input);
            fetchNotificationsAJAX(1);
        }

        function filterByTabStatus(status) {
            currentStatusFilter = status;
            
            // Update tabs
            document.querySelectorAll('.tab-item-sima').forEach(t => t.classList.remove('active'));
            const activeTab = document.getElementById('tab-status-' + status);
            if (activeTab) activeTab.classList.add('active');

            // Update cards
            document.querySelectorAll('.stat-card-sima').forEach(c => c.classList.remove('is-active-filter'));
            const activeCard = document.getElementById('card-status-' + status);
            if (activeCard) activeCard.classList.add('is-active-filter');

            fetchNotificationsAJAX(1);
        }

        function filterByCardStatus(status) {
            filterByTabStatus(status);
        }

        function selectRangeFilter(val, label) {
            const radio = document.querySelector(`.range-filter[value="${val}"]`);
            if (radio) radio.checked = true;

            const textEl = document.getElementById('range-filter-text');
            if (textEl) textEl.textContent = label;

            document.querySelectorAll('.range-option-item').forEach(item => {
                if (item.dataset.value === val) {
                    item.classList.add('is-selected');
                } else {
                    item.classList.remove('is-selected');
                }
            });

            const btn = document.getElementById('btn-filter-range');
            if (btn) {
                if (typeof bootstrap !== 'undefined' && bootstrap.Dropdown) {
                    const bsDropdown = bootstrap.Dropdown.getInstance(btn) || new bootstrap.Dropdown(btn);
                    if (bsDropdown) bsDropdown.hide();
                } else if (btn.classList.contains('show')) {
                    btn.click();
                }
            }

            applyFilters();
        }

        function applyFilters() {
            fetchNotificationsAJAX(1);
        }

        function resetFilters() {
            const input = document.getElementById('searchInput');
            if (input) input.value = '';
            toggleSearchClear(input);

            const defaultRange = document.querySelector('.range-filter[value="all"]');
            if (defaultRange) defaultRange.checked = true;
            const rangeText = document.getElementById('range-filter-text');
            if (rangeText) rangeText.textContent = 'Semua Waktu';
            document.querySelectorAll('.range-option-item').forEach(item => {
                if (item.dataset.value === 'all') {
                    item.classList.add('is-selected');
                } else {
                    item.classList.remove('is-selected');
                }
            });

            const perPageSelect = document.getElementById('perPageSelect');
            if (perPageSelect) perPageSelect.value = '10';

            currentStatusFilter = 'all';
            document.querySelectorAll('.tab-item-sima').forEach(t => t.classList.remove('active'));
            const allTab = document.getElementById('tab-status-all');
            if (allTab) allTab.classList.add('active');

            document.querySelectorAll('.stat-card-sima').forEach(c => c.classList.remove('is-active-filter'));
            const allCard = document.getElementById('card-status-all');
            if (allCard) allCard.classList.add('is-active-filter');

            try {
                sessionStorage.removeItem('notification_filter_state');
            } catch(e) {}

            fetchNotificationsAJAX(1);
        }

        function changePerPage(val) {
            fetchNotificationsAJAX(1);
        }

        function goToPage(page) {
            currentPage = page;
            fetchNotificationsAJAX(page);
        }

        // ─── Instant Pure AJAX Fetch (No page reload) ─────────────────────
        function fetchNotificationsAJAX(page = 1, isAutoRefresh = false) {
            const q = document.getElementById('searchInput')?.value?.trim() || '';
            const rangeVal = document.querySelector('.range-filter:checked')?.value || 'all';
            const statusVal = currentStatusFilter;
            const perPageVal = document.getElementById('perPageSelect')?.value || '10';

            const params = new URLSearchParams();
            if (q) params.set('search', q);
            if (rangeVal && rangeVal !== 'all') params.set('range', rangeVal);
            if (statusVal && statusVal !== 'all') params.set('status', statusVal);
            if (perPageVal && perPageVal !== '10') params.set('per_page', perPageVal);
            if (page > 1) params.set('page', page);

            const filterQuery = params.toString();
            try {
                if (filterQuery) {
                    sessionStorage.setItem('notification_filter_state', filterQuery);
                } else {
                    sessionStorage.removeItem('notification_filter_state');
                }
            } catch(e) {}

            history.replaceState(null, '', window.location.pathname + (filterQuery ? '?' + filterQuery : ''));

            fetch('{{ route('notifications.index') }}?' + filterQuery, {
                headers: { 'X-Requested-With': 'XMLHttpRequest', 'Accept': 'application/json' }
            })
            .then(r => r.json())
            .then(data => {
                if (!data) return;
                renderNotifList(data.grouped || []);
                updatePaginationUI(data.pagination);

                const markAllWrap = document.getElementById('markAllWrap');
                if (markAllWrap) markAllWrap.style.display = (data.unreadCount > 0) ? '' : 'none';

                const deleteReadWrap = document.getElementById('deleteReadWrap');
                if (deleteReadWrap) deleteReadWrap.style.display = (data.readCount > 0) ? '' : 'none';

                if (typeof feather !== 'undefined') feather.replace();
            })
            .catch(err => console.error('fetchNotificationsAJAX error:', err));
        }

        function renderNotifList(grouped) {
            const container = document.getElementById('notif-list-container');
            if (!container) return;

            if (!grouped || grouped.length === 0) {
                container.innerHTML = `
                    <div style="display: flex; flex-direction: column; align-items: center; justify-content: center; gap: 8px; padding: 48px 20px; color: var(--color-text-muted);">
                        <i data-feather="inbox" style="width: 42px; height: 42px; stroke-width: 1.5; color: var(--color-border-dark);"></i>
                        <p style="margin: 0; font-size: 13.5px; font-weight: 500;">Belum ada notifikasi yang sesuai</p>
                    </div>`;
                if (typeof feather !== 'undefined') feather.replace();
                return;
            }

            let html = '';
            grouped.forEach(group => {
                html += `<div class="date-group-label">${escapeHtml(group.label)}</div>`;
                group.items.forEach(item => {
                    const isUnread = !item.is_read;
                    const unreadClass = isUnread ? 'unread' : '';
                    const actorHtml = item.actor_nama ? `
                        <span style="font-size: 11.5px; color: var(--color-text-muted, #546958);">
                            &bull; Oleh: <strong>${escapeHtml(item.actor_nama)}</strong>
                        </span>` : '';
                    const bodyHtml = item.body ? `<div class="notif-row-body-text">${escapeHtml(item.body)}</div>` : '';

                    html += `
                    <div class="notif-row-wrap">
                        <div class="notif-row ${unreadClass}">
                            <i data-feather="${escapeHtml(item.icon || 'bell')}" class="notif-row-icon" style="width: 18px; height: 18px; color: var(--color-text-muted, #546958); flex-shrink: 0; align-self: center; margin: 0;"></i>

                            <div style="flex: 1; min-width: 0;">
                                <a href="${escapeHtml(item.read_url)}" style="text-decoration: none; color: inherit; display: block;">
                                    <div class="notif-row-title">${escapeHtml(item.title)}</div>
                                    ${bodyHtml}
                                    <div style="display: flex; align-items: center; gap: 8px; flex-wrap: wrap;">
                                        <span style="font-size: 12px; color: var(--color-text-muted, #546958); font-weight: 500;">
                                            ${escapeHtml(item.time_formatted || '')}
                                        </span>
                                        ${actorHtml}
                                    </div>
                                </a>
                            </div>

                            <div style="display: flex; flex-direction: column; align-items: flex-end; gap: 8px; flex-shrink: 0;">
                                <span style="font-size: 11.5px; color: var(--color-text-muted, #546958); font-weight: 500;">
                                    ${escapeHtml(item.date_formatted)}
                                </span>
                                <form id="deleteNotifForm${item.id}" method="POST" action="${escapeHtml(item.destroy_url)}" style="margin: 0;">
                                    <input type="hidden" name="_token" value="{{ csrf_token() }}">
                                    <input type="hidden" name="_method" value="DELETE">
                                    <button type="button" class="btn-sima-icon" title="Hapus notifikasi" onclick="confirmDeleteNotification(${item.id})" style="width: 30px; height: 30px;">
                                        <i data-feather="trash-2" style="width: 14px; height: 14px; color: var(--color-danger, #A63D2F);"></i>
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

        document.addEventListener('DOMContentLoaded', function () {
            if (typeof feather !== 'undefined') feather.replace();

            const jumpInput = document.getElementById('pageJumpInput');
            if (jumpInput) {
                jumpInput.addEventListener('keypress', function (e) {
                    if (e.key !== 'Enter') return;
                    e.preventDefault();
                    let p = parseInt(this.value, 10);
                    if (isNaN(p) || p < 1) p = 1;
                    const maxP = parseInt(this.max, 10) || 1;
                    if (p > maxP) p = maxP;
                    goToPage(p);
                });
            }
        });
    </script>
</body>
</html>
