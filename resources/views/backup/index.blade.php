<!DOCTYPE html>
<html lang="id">
<head>
    @include('layouts.head-page-meta', ['title' => 'Cadangan Basis Data - Sistem Manajemen Aset Desa'])
    @include('layouts.head-css')
    <style>
        .stat-card-sima {
            user-select: none;
            background: var(--color-surface);
            border: 1px solid var(--color-border);
            border-radius: var(--radius-lg);
            padding: 20px 24px;
            display: flex;
            flex-direction: column;
            min-height: 120px;
        }
        .stat-card-sima .stat-header {
            height: 14px;
            margin-bottom: 8px !important;
            font-size: 11.5px;
            font-weight: 600;
            color: var(--color-text-muted);
            letter-spacing: 0.5px;
            text-transform: uppercase;
        }
        .stat-card-sima .stat-number {
            height: 32px;
            min-height: 32px;
            max-height: 32px;
            display: flex;
            align-items: center;
            margin-bottom: 10px !important;
            font-size: 22px;
            font-weight: 700;
            color: var(--color-text);
        }
        .stat-card-sima .stat-delta-wrap {
            margin-top: auto !important;
            font-size: 11.5px;
            font-weight: 500;
            color: var(--color-text-muted);
            white-space: nowrap;
        }
        .info-hint-box {
            display: flex !important;
            width: 100% !important;
            box-sizing: border-box !important;
            background: var(--color-primary-tint, #d5e4d7) !important;
            border: 1px solid var(--color-border, #cfdcc8) !important;
            border-radius: var(--radius-sm, 8px) !important;
            padding: 12px 18px !important;
            align-items: center !important;
            gap: 12px;
            margin-bottom: 24px;
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
                        Cadangan Basis Data
                    </h1>
                    <p style="margin: 4px 0 0 0; font-size: 13px; color: var(--color-text-muted);">
                        Utilitas pencadangan dan pemulihan berkas basis data sistem SIMA Desa
                    </p>
                </div>
                <div style="display: flex; gap: 10px; align-items: center; flex-wrap: wrap;">
                    <form method="POST" action="{{ route('backup.store') }}" id="form-create-backup">
                        @csrf
                        <button type="button" onclick="confirmCreateBackup()" class="btn-sima-primary" id="btn-create-backup">
                            <i data-feather="database" style="width: 14px; height: 14px;"></i>
                            <span>Buat Cadangan Sekarang</span>
                        </button>
                    </form>
                </div>
            </div>

            <!-- 1. STAT CARDS (Prodex 4-Card Summary Bar) -->
            <div class="stat-card-row">
                <!-- Card 1: Total Cadangan -->
                <div class="stat-card-sima">
                    <div class="stat-header">
                        <span>TOTAL CADANGAN</span>
                    </div>
                    <div class="stat-number">{{ number_format($stats['total_backups'], 0, ',', '.') }}</div>
                    <div class="stat-delta-wrap">
                        <span>Berkas .SQL tersimpan di server</span>
                    </div>
                </div>

                <!-- Card 2: Estimasi Ukuran Basis Data -->
                <div class="stat-card-sima">
                    <div class="stat-header">
                        <span>UKURAN BASIS DATA</span>
                    </div>
                    <div class="stat-number">{{ $stats['db_size_formatted'] }}</div>
                    <div class="stat-delta-wrap">
                        <span>Skema & indeks ({{ $stats['db_name'] }})</span>
                    </div>
                </div>

                <!-- Card 3: Jumlah Tabel Sistem -->
                <div class="stat-card-sima">
                    <div class="stat-header">
                        <span>JUMLAH TABEL</span>
                    </div>
                    <div class="stat-number">{{ $stats['total_tables'] }} Tabel</div>
                    <div class="stat-delta-wrap">
                        <span>Struktur tabel aktif tercakup</span>
                    </div>
                </div>

                <!-- Card 4: Terakhir Dicadangkan -->
                <div class="stat-card-sima">
                    <div class="stat-header">
                        <span>TERAKHIR DICADANGKAN</span>
                    </div>
                    <div class="stat-number" style="font-size: 15px; font-weight: 600;">
                        {{ $stats['latest_backup'] }}
                    </div>
                    <div class="stat-delta-wrap">
                        <span>Waktu pembuatan terakhir</span>
                    </div>
                </div>
            </div>

            <!-- 2. FULL WIDTH INFO HELPER (Design.md Golden Reference) -->
            <div class="info-hint-box">
                <i data-feather="info" style="width: 16px; height: 16px; color: var(--color-primary); flex-shrink: 0; align-self: center; margin: 0;"></i>
                <div style="font-size: 12.5px; font-weight: 500; color: var(--color-text); line-height: 1.5; flex: 1;">
                    Cadangan basis data mencakup seluruh struktur tabel dan data operasional (inventaris, mutasi, ruangan, pejabat, pengguna, dan pengaturan sistem). Sangat disarankan untuk mengunduh berkas <strong>.SQL</strong> secara teratur dan menyimpannya pada media penyimpanan eksternal yang aman.
                </div>
            </div>

            <!-- 3. DATA CARD CONTAINER (Sesuai Standar Seluruh Index SIMA) -->
            <div class="sima-card" style="padding: 0; overflow: visible; margin-bottom: 32px;">
                <!-- Header Controls Bar (Pencarian & Urutan) -->
                <div style="display: flex; justify-content: space-between; align-items: center; padding: 16px 20px; border-bottom: 1px solid var(--color-border); flex-wrap: wrap; gap: 12px; background: var(--color-surface); border-top-left-radius: var(--radius-lg, 12px); border-top-right-radius: var(--radius-lg, 12px);">
                    <!-- Left: Search Box -->
                    <div style="display: flex; align-items: center; gap: 12px; flex: 1; max-width: 440px;">
                        <div class="topbar-search-box" style="width: 100%;">
                            <i data-feather="search" style="width: 15px; height: 15px; color: var(--color-text-muted); flex-shrink: 0;"></i>
                            <input
                                type="text"
                                id="searchInput"
                                class="topbar-search-input"
                                placeholder="Cari nama berkas cadangan (.sql)..."
                                value="{{ request('search') }}"
                                oninput="onSearchInput(this)"
                                onkeypress="if(event.key==='Enter'){applyFilters();}"
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
                    </div>

                    <!-- Right Controls: Reset Filter & Filter/Sort Dropdown -->
                    <div style="display: flex; align-items: center; gap: 10px;">
                        @if(request('search') || (request('sort') && request('sort') !== 'terbaru') || (request('per_page') && request('per_page') != 10))
                        <button type="button" class="btn-sima-secondary" onclick="resetFilters()" title="Reset semua filter" style="padding: 7px 12px; font-size: 13px;">
                            <i data-feather="rotate-ccw" style="width: 13px; height: 13px;"></i>
                            <span>Reset Filter</span>
                        </button>
                        @endif

                        <div class="dropdown" id="filter-dropdown-container" style="position: relative;">
                            <button type="button" id="btn-filter-toggle" class="btn-sima-secondary" onclick="toggleFilterPopover(event)" title="Urutkan Berkas" style="padding: 7px 12px; font-size: 13px; display: inline-flex; align-items: center; gap: 6px;">
                                <i data-feather="sliders" style="width: 13px; height: 13px;"></i>
                                <span>Urutkan</span>
                            </button>
                            <div id="filter-dropdown-menu" class="sima-filter-popover" onclick="event.stopPropagation();" style="display: none; position: absolute; top: calc(100% + 4px); right: 0; min-width: 260px; padding: 18px 20px; z-index: 1050; background: var(--color-surface); border: 1px solid var(--color-border); border-radius: var(--radius-lg); box-shadow: var(--shadow-float); margin: 0;">
                                <div style="color: var(--color-primary); font-size: 11.5px; font-weight: 700; text-transform: uppercase; letter-spacing: 0.04em; margin-bottom: 10px;">Urutkan Berdasarkan</div>
                                <div style="display: flex; flex-direction: column; gap: 8px; margin-bottom: 16px;">
                                    <label style="cursor: pointer; display: flex; align-items: center; gap: 8px; font-size: 13px; color: var(--color-text); margin: 0;">
                                        <input type="radio" name="sortFilter" value="terbaru" class="sort-filter" {{ (!request('sort') || request('sort') === 'terbaru') ? 'checked' : '' }} style="accent-color: var(--color-primary); cursor: pointer;">
                                        <span>Terbaru Dibuat</span>
                                    </label>
                                    <label style="cursor: pointer; display: flex; align-items: center; gap: 8px; font-size: 13px; color: var(--color-text); margin: 0;">
                                        <input type="radio" name="sortFilter" value="terlama" class="sort-filter" {{ request('sort') === 'terlama' ? 'checked' : '' }} style="accent-color: var(--color-primary); cursor: pointer;">
                                        <span>Terlama Dibuat</span>
                                    </label>
                                    <label style="cursor: pointer; display: flex; align-items: center; gap: 8px; font-size: 13px; color: var(--color-text); margin: 0;">
                                        <input type="radio" name="sortFilter" value="nama_asc" class="sort-filter" {{ request('sort') === 'nama_asc' ? 'checked' : '' }} style="accent-color: var(--color-primary); cursor: pointer;">
                                        <span>Nama Berkas (A-Z)</span>
                                    </label>
                                    <label style="cursor: pointer; display: flex; align-items: center; gap: 8px; font-size: 13px; color: var(--color-text); margin: 0;">
                                        <input type="radio" name="sortFilter" value="nama_desc" class="sort-filter" {{ request('sort') === 'nama_desc' ? 'checked' : '' }} style="accent-color: var(--color-primary); cursor: pointer;">
                                        <span>Nama Berkas (Z-A)</span>
                                    </label>
                                    <label style="cursor: pointer; display: flex; align-items: center; gap: 8px; font-size: 13px; color: var(--color-text); margin: 0;">
                                        <input type="radio" name="sortFilter" value="ukuran_desc" class="sort-filter" {{ request('sort') === 'ukuran_desc' ? 'checked' : '' }} style="accent-color: var(--color-primary); cursor: pointer;">
                                        <span>Ukuran Terbesar</span>
                                    </label>
                                    <label style="cursor: pointer; display: flex; align-items: center; gap: 8px; font-size: 13px; color: var(--color-text); margin: 0;">
                                        <input type="radio" name="sortFilter" value="ukuran_asc" class="sort-filter" {{ request('sort') === 'ukuran_asc' ? 'checked' : '' }} style="accent-color: var(--color-primary); cursor: pointer;">
                                        <span>Ukuran Terkecil</span>
                                    </label>
                                </div>
                                <div style="padding-top: 12px; border-top: 1px dashed var(--color-border);">
                                    <button type="button" onclick="applyFilters(); event.stopPropagation();" class="btn-sima-primary" style="width: 100%; justify-content: center; font-size: 13px; height: 34px;">
                                        Terapkan Urutan
                                    </button>
                                </div>
                            </div>
                        </div>

                        <span style="font-size: 12px; color: var(--color-text-muted); font-family: monospace; background: var(--color-row-hover); padding: 5px 10px; border-radius: var(--radius-sm); border: 1px solid var(--color-border);">
                            storage/app/backups/
                        </span>
                    </div>
                </div>

                <!-- Table Viewport -->
                <div class="table-sima-wrapper" style="border: none; border-radius: 0;">
                    <table class="table-sima">
                        <thead>
                            <tr>
                                <th style="width: 60px; text-align: center;">No</th>
                                <th style="min-width: 320px;">Nama Berkas Cadangan</th>
                                <th style="width: 240px;">Waktu Pembuatan</th>
                                <th style="text-align: center; width: 160px;">Ukuran Berkas</th>
                                <th style="width: 200px; text-align: center;">Aksi</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($files as $index => $file)
                            <tr>
                                <td style="text-align: center; color: var(--color-text-muted); font-size: 13px; font-variant-numeric: tabular-nums;">
                                    {{ ($files->firstItem() ?? 1) + $index }}
                                </td>
                                <td>
                                    <div style="display: flex; align-items: center; gap: 10px;">
                                        <div style="width: 32px; height: 32px; border-radius: var(--radius-sm, 6px); background: var(--color-primary-tint, #d5e4d7); display: flex; align-items: center; justify-content: center; color: var(--color-primary); flex-shrink: 0;">
                                            <i data-feather="database" style="width: 16px; height: 16px;"></i>
                                        </div>
                                        <div>
                                            <div style="font-weight: 600; font-family: monospace, sans-serif; font-size: 13.5px; color: var(--color-text);">
                                                {{ $file['filename'] }}
                                            </div>
                                            <div style="font-size: 11px; color: var(--color-text-muted); margin-top: 1px;">
                                                Format berkas dump basis data MySQL (.sql)
                                            </div>
                                        </div>
                                    </div>
                                </td>
                                <td>
                                    <div style="font-size: 13px; font-weight: 500; color: var(--color-text);">
                                        {{ $file['date_formatted'] }}
                                    </div>
                                    <div style="font-size: 11.5px; color: var(--color-text-muted);">
                                        {{ $file['age_human'] }}
                                    </div>
                                </td>
                                <td style="text-align: center; font-size: 13px; font-weight: 600; color: var(--color-text); font-variant-numeric: tabular-nums;">
                                    {{ $file['size_formatted'] }}
                                </td>
                                <td style="text-align: center;">
                                    <div style="display: inline-flex; align-items: center; gap: 8px;">
                                        <a href="{{ route('backup.download', $file['filename']) }}" class="btn-sima-secondary" style="padding: 5px 14px; font-size: 12px; border-radius: var(--radius-full); text-decoration: none;" title="Unduh Berkas SQL">
                                            <i data-feather="download" style="width: 12px; height: 12px;"></i>
                                            <span>Unduh</span>
                                        </a>
                                        <form method="POST" action="{{ route('backup.destroy', $file['filename']) }}" id="form-delete-{{ md5($file['filename']) }}" style="display: inline;">
                                            @csrf
                                            @method('DELETE')
                                            <button type="button" onclick="confirmDeleteBackup('{{ $file['filename'] }}', '{{ md5($file['filename']) }}')" class="btn-sima-danger" style="padding: 5px 14px; font-size: 12px; border-radius: var(--radius-full);" title="Hapus Berkas Cadangan">
                                                <i data-feather="trash-2" style="width: 12px; height: 12px;"></i>
                                                <span>Hapus</span>
                                            </button>
                                        </form>
                                    </div>
                                </td>
                            </tr>
                            @empty
                            <tr>
                                <td colspan="5" style="text-align: center; padding: 64px 20px;">
                                    <div style="display: flex; flex-direction: column; align-items: center; justify-content: center; gap: 10px;">
                                        <i data-feather="inbox" style="width: 44px; height: 44px; stroke-width: 1.5; color: var(--color-border-dark);"></i>
                                        <p style="margin: 0; font-size: 14px; font-weight: 500; color: var(--color-text-muted);">
                                            {{ request('search') ? 'Tidak ada berkas cadangan yang sesuai dengan pencarian' : 'Belum ada berkas cadangan basis data yang dibuat' }}
                                        </p>
                                        @if(request('search'))
                                        <button type="button" class="btn-sima-secondary" onclick="resetFilters()" style="margin-top: 6px; padding: 6px 14px; font-size: 12.5px;">
                                            <i data-feather="rotate-ccw" style="width: 12px; height: 12px;"></i>
                                            <span>Reset Pencarian</span>
                                        </button>
                                        @endif
                                    </div>
                                </td>
                            </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>

                <!-- 4. PAGINATION FOOTER BAR -->
                <div class="pagination-sima-container" style="padding: 20px 24px; border-top: 1px solid var(--color-border); background: var(--color-surface); flex-wrap: wrap; gap: 14px; border-bottom-left-radius: var(--radius-lg, 12px); border-bottom-right-radius: var(--radius-lg, 12px);">
                    <!-- Per Page Select -->
                    <div style="display: flex; align-items: center; gap: 8px; font-size: 13px;">
                        <span>Tampilkan</span>
                        <select id="perPageSelect" class="per-page-select" style="height: 32px;" onchange="changePerPage(this.value)">
                            <option value="10" {{ ($files->perPage() == 10) ? 'selected' : '' }}>10</option>
                            <option value="20" {{ ($files->perPage() == 20) ? 'selected' : '' }}>20</option>
                            <option value="50" {{ ($files->perPage() == 50) ? 'selected' : '' }}>50</option>
                            <option value="100" {{ ($files->perPage() == 100) ? 'selected' : '' }}>100</option>
                        </select>
                        <span>data per halaman</span>
                    </div>

                    <!-- Page Navigation Numbers -->
                    <div style="display: flex; align-items: center; gap: 10px;">
                        <nav class="pagination-sima" id="paginationNav">
                            @if($files->onFirstPage())
                                <span class="page-btn-sima" style="opacity: 0.5; cursor: not-allowed;" title="Sebelumnya"><i data-feather="chevron-left" style="width: 14px; height: 14px;"></i></span>
                            @else
                                <button type="button" class="page-btn-sima" onclick="goToPage({{ $files->currentPage() - 1 }})" title="Sebelumnya"><i data-feather="chevron-left" style="width: 14px; height: 14px;"></i></button>
                            @endif

                            @php
                                $currentPage = $files->currentPage();
                                $lastPage    = max(1, $files->lastPage());
                                $wStart = max(1, $currentPage - 2);
                                $wEnd   = min($lastPage, $currentPage + 2);
                                if (($wEnd - $wStart + 1) < 5) {
                                    if ($wStart === 1) { $wEnd = min($lastPage, 5); }
                                    else { $wStart = max(1, $wEnd - 4); }
                                }
                            @endphp
                            @for ($p = $wStart; $p <= $wEnd; $p++)
                                @if ($p == $currentPage)
                                    <span class="page-btn-sima active">{{ $p }}</span>
                                @else
                                    <button type="button" class="page-btn-sima" onclick="goToPage({{ $p }})">{{ $p }}</button>
                                @endif
                            @endfor

                            @if($files->hasMorePages())
                                <button type="button" class="page-btn-sima" onclick="goToPage({{ $files->currentPage() + 1 }})" title="Berikutnya"><i data-feather="chevron-right" style="width: 14px; height: 14px;"></i></button>
                            @else
                                <span class="page-btn-sima" style="opacity: 0.5; cursor: not-allowed;" title="Berikutnya"><i data-feather="chevron-right" style="width: 14px; height: 14px;"></i></span>
                            @endif
                        </nav>
                        <div style="display: flex; align-items: center; gap: 6px; font-size: 13px;">
                            <label for="pageJumpInput" style="color: var(--color-text-muted);">Ke hal.</label>
                            <input id="pageJumpInput" type="number" class="form-control-sima" style="width: 52px; height: 32px; padding: 2px 6px; text-align: center; font-size: 13px;" min="1" max="{{ max(1, $files->lastPage()) }}" value="{{ $files->currentPage() }}">
                        </div>
                    </div>

                    <!-- Range Info -->
                    <div id="paginationRangeInfo" style="font-size: 13px; color: var(--color-text-muted); font-variant-numeric: tabular-nums;">
                        Menampilkan <strong>{{ $files->firstItem() ?? 0 }}-{{ $files->lastItem() ?? 0 }}</strong> dari <strong>{{ $files->total() }}</strong> data
                    </div>
                </div>
            </div>

        </div>
    </div>

    @include('layouts.footer-block')
    @include('layouts.footer-js')

    <script>
        function confirmCreateBackup() {
            if (typeof AppModal !== 'undefined') {
                AppModal.confirm({
                    type: 'info',
                    title: 'Buat Cadangan Basis Data Sekarang?',
                    message: 'Sistem akan mengekstrak struktur tabel dan seluruh data saat ini ke dalam berkas cadangan SQL.',
                    confirmText: 'Mulai Pencadangan',
                    cancelText: 'Batal',
                    onConfirm: function() {
                        const btn = document.getElementById('btn-create-backup');
                        if (btn) {
                            btn.disabled = true;
                            btn.innerHTML = '<span class="spinner-border spinner-border-sm" role="status" aria-hidden="true" style="width: 12px; height: 12px; margin-right: 6px;"></span> Memproses Cadangan...';
                        }
                        document.getElementById('form-create-backup').submit();
                    }
                });
            } else {
                if (confirm('Buat cadangan basis data sekarang?')) {
                    document.getElementById('form-create-backup').submit();
                }
            }
        }

        function confirmDeleteBackup(filename, formIdHash) {
            if (typeof AppModal !== 'undefined') {
                AppModal.confirm({
                    type: 'danger',
                    title: 'Hapus Berkas Cadangan?',
                    message: 'Apakah Anda yakin ingin menghapus berkas cadangan <strong style="color: var(--color-text);">' + filename + '</strong>? Tindakan ini tidak dapat dibatalkan.',
                    confirmText: 'Hapus Berkas',
                    cancelText: 'Batal',
                    onConfirm: function() {
                        document.getElementById('form-delete-' + formIdHash).submit();
                    }
                });
            } else {
                if (confirm('Hapus berkas cadangan ' + filename + '?')) {
                    document.getElementById('form-delete-' + formIdHash).submit();
                }
            }
        }

        // Pagination & Filter Helper Functions
        function changePerPage(perPage) {
            const url = new URL(window.location.href);
            url.searchParams.set('per_page', perPage);
            url.searchParams.set('page', '1');
            window.location.href = url.toString();
        }

        function goToPage(page) {
            const url = new URL(window.location.href);
            url.searchParams.set('page', page);
            window.location.href = url.toString();
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

        function applyFilters() {
            closeFilterPopover();
            const url = new URL(window.location.href);
            const searchVal = document.getElementById('searchInput')?.value?.trim();
            if (searchVal) {
                url.searchParams.set('search', searchVal);
            } else {
                url.searchParams.delete('search');
            }
            const sortVal = document.querySelector('input[name="sortFilter"]:checked')?.value || 'terbaru';
            url.searchParams.set('sort', sortVal);
            url.searchParams.set('page', '1');
            window.location.href = url.toString();
        }

        function resetFilters() {
            window.location.href = '{{ route("backup.index") }}';
        }

        function clearSearch() {
            const searchInput = document.getElementById('searchInput');
            if (searchInput) searchInput.value = '';
            applyFilters();
        }

        function onSearchInput(el) {
            const clearBtn = document.getElementById('clear-search-btn');
            if (clearBtn) {
                clearBtn.style.display = el.value.trim() ? 'block' : 'none';
            }
        }

        // Page Jump input listener
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

        document.addEventListener('DOMContentLoaded', function() {
            if (typeof feather !== 'undefined') {
                feather.replace();
            }
        });
    </script>
</body>
</html>
