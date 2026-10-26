<!DOCTYPE html>
<html lang="en" data-pc-preset="{{ $pc_preset_theme }}" data-pc-theme="{{ $pc_dark_layout }}">
<head>
    @include('layouts.head-page-meta', ['title' => 'Detail Ruangan — ' . ($ruangan->nama ?? 'Ruangan')])
    @include('layouts.head-css')
</head>
<body data-pc-preset="{{ $pc_preset_theme }}" data-pc-sidebar-theme="{{ $pc_sidebar_theme ?? 'light' }}">
    @include('layouts.notification')
    @include('layouts.modals')
    @include('layouts.layout-vertical')
    <div class="pc-container">
        <div class="pc-content">
            @include('layouts.breadcrumb', [
                'breadcrumbItemHome' => 'Inventaris Desa',
                'breadcrumbItem' => 'Ruangan',
                'breadcrumbItemUrl' => route('ruangan.index'),
                'breadcrumbItemActive' => 'Detail Ruangan'
            ])

            <!-- Top Action Bar -->
            <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 20px; flex-wrap: wrap; gap: 12px;">
                <a href="{{ route('ruangan.index') }}" class="btn-sima-secondary" style="border: none !important; padding-left: 0 !important; background-color: transparent !important;" onclick="try { sessionStorage.setItem('ruangan_returning', '1'); } catch(e) {} if (window.history.length > 1 && document.referrer && document.referrer.includes('/ruangan')) { window.history.back(); return false; }">
                    <i data-feather="arrow-left" style="width: 14px; height: 14px;"></i>
                    <span>Kembali ke Daftar Ruangan</span>
                </a>
                <div style="display: flex; gap: 10px; align-items: center;">
                    @can('update', $ruangan)
                    <a href="{{ route('ruangan.edit', $ruangan->id) }}" class="btn-sima-primary">
                        <i data-feather="edit-2" style="width: 14px; height: 14px;"></i>
                        <span>Edit Ruangan</span>
                    </a>
                    @endcan
                    @can('delete', $ruangan)
                    <form id="deleteForm" action="{{ route('ruangan.destroy', $ruangan->id) }}" method="POST" style="display:none;">
                        @csrf @method('DELETE')
                    </form>
                    <button type="button" class="btn-sima-danger"
                            onclick="AppModal.confirmDelete('{{ addslashes($ruangan->nama) }}', function(){ document.getElementById('deleteForm').submit(); })">
                        <i data-feather="trash-2" style="width: 14px; height: 14px;"></i>
                        <span>Hapus Ruangan</span>
                    </button>
                    @endcan
                </div>
            </div>

            <!-- ===== MAIN PORTAL CARD ===== -->
            <div class="portal-card">

                <!-- Section: Informasi & Spesifikasi Ruangan -->
                <div class="portal-section" style="padding-top: 0; border-top: none; margin-top: 32px;">
                    <div class="portal-section-header">
                        <h2 class="portal-section-title">
                            <span>Informasi Ruangan</span>
                        </h2>
                    </div>
                    <div class="portal-stats-grid" style="grid-template-columns: repeat(2, 1fr); gap: 24px 36px;">
                        <div class="portal-stat-item">
                            <span class="portal-stat-label">Nama Ruangan</span>
                            <span class="portal-stat-value">{{ $ruangan->nama }}</span>
                        </div>
                        <div class="portal-stat-item">
                            <span class="portal-stat-label">Kode Ruangan</span>
                            <span class="portal-stat-value {{ $ruangan->kode ? '' : 'text-empty-fallback' }}" style="font-family: monospace;">{{ $ruangan->kode ?: '-' }}</span>
                        </div>
                        <div class="portal-stat-item">
                            <span class="portal-stat-label">Penanggung Jawab</span>
                            <span class="portal-stat-value {{ $ruangan->penanggungJawab ? '' : 'text-empty-fallback' }}">{{ $ruangan->penanggungJawab?->nama ?? 'Belum ditentukan' }}</span>
                        </div>
                        <div class="portal-stat-item">
                            <span class="portal-stat-label">Total Aset Terdaftar</span>
                            <span class="portal-stat-value">{{ $inventaris->total() }} aset</span>
                        </div>
                        <div class="portal-stat-item" style="grid-column: 1 / -1;">
                            <span class="portal-stat-label">Deskripsi</span>
                            <span class="portal-stat-value {{ !$ruangan->deskripsi ? 'text-empty-fallback' : '' }}">{{ $ruangan->deskripsi ?: 'Tidak ada deskripsi lokasi' }}</span>
                        </div>
                    </div>
                </div>

                <!-- Section: Tabel Aset dalam Ruangan -->
                <div class="portal-section" style="border-top: none; margin-top: 0; padding-top: 36px; padding-bottom: 8px;">
                    <div class="portal-section-header">
                        <h2 class="portal-section-title">
                            <span>Daftar Aset di Ruangan Ini ({{ $inventaris->total() }})</span>
                        </h2>
                    </div>
                    <div id="aset-section">
                    @if($inventaris->isEmpty())
                        <div class="empty-state-sima">
                            <i data-feather="inbox" style="width: 42px; height: 42px; stroke-width: 1.5; color: var(--color-border-dark);"></i>
                            <p>Belum ada aset di ruangan ini.</p>
                        </div>
                    @else
                        <div id="aset-list-container" style="display: flex; flex-direction: column; margin-top: 6px;">
                            @foreach($inventaris as $i => $aset)
                            <div style="display: flex; align-items: center; justify-content: space-between; font-size: 13.5px; line-height: 1.5; color: var(--color-text); padding: 12px 0; border-bottom: 1px dashed var(--color-border, #cfdcc8);">
                                <div style="display: flex; align-items: center; min-width: 0; padding-right: 12px;">
                                    <span style="color: var(--color-text-muted); font-variant-numeric: tabular-nums; min-width: 24px; font-weight: 500; flex-shrink: 0;">
                                        {{ $inventaris->firstItem() + $i }}.
                                    </span>
                                    <span style="font-weight: 600; color: var(--color-text);">
                                        {{ $aset->display_name ?? '-' }}
                                    </span>
                                    <span style="color: var(--color-text-muted); font-size: 13px; font-weight: 500; margin-left: 6px;">
                                        ({{ $aset->jenis->nama ?? '-' }})
                                    </span>
                                </div>
                                <a href="{{ route('manajemen-aset.show', $aset->id) }}" style="flex-shrink: 0; font-size: 12px; color: var(--color-primary); font-weight: 600; text-decoration: none; display: inline-flex; align-items: center; gap: 4px; transition: gap 0.15s ease, opacity 0.15s ease;" onmouseenter="this.style.opacity='0.8'; this.style.gap='6px';" onmouseleave="this.style.opacity='1'; this.style.gap='4px';">
                                    <span>Lihat detail</span>
                                    <i data-feather="chevron-right" style="width: 13px; height: 13px;"></i>
                                </a>
                            </div>
                            @endforeach
                        </div>

                        {{-- 4. PAGINATION FOOTER BAR --}}
                        <div class="pagination-bar pagination-sima-container" id="pagination-bar" style="margin-top: 14px; padding: 14px 0; border-top: none !important; background: transparent; flex-wrap: wrap; gap: 14px; {{ $inventaris->total() > 10 ? 'display: flex;' : 'display: none;' }}">
                            <!-- Per Page Select -->
                            <div class="pagination-left">
                                <div class="page-size-wrap">
                                    <span>Tampilkan</span>
                                    <select id="perPageSelect" class="per-page-select" style="height: 32px;" onchange="changePerPage(this.value)">
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
                                <nav class="pagination-sima pagination-nav" id="pagination-nav">
                                    @if ($inventaris->onFirstPage())
                                        <span class="page-btn-sima page-btn disabled" style="opacity: 0.5; cursor: not-allowed;" title="Previous"><i data-feather="chevron-left" style="width: 14px; height: 14px;"></i></span>
                                    @else
                                        <a href="{{ $inventaris->previousPageUrl() }}" class="page-btn-sima page-btn page-link" title="Previous"><i data-feather="chevron-left" style="width: 14px; height: 14px;"></i></a>
                                    @endif

                                    @php
                                        $currentPage  = $inventaris->currentPage();
                                        $lastPage     = $inventaris->lastPage();
                                        $windowStart  = max(1, $currentPage - 2);
                                        $windowEnd    = min($lastPage, $currentPage + 2);
                                        if (($windowEnd - $windowStart + 1) < 5) {
                                            if ($windowStart === 1) { $windowEnd = min($lastPage, 5); }
                                            else { $windowStart = max(1, $windowEnd - 4); }
                                        }
                                        $pageUrls = $inventaris->getUrlRange($windowStart, $windowEnd);
                                    @endphp
                                    @foreach($pageUrls as $page => $url)
                                        @if($page == $currentPage)
                                            <span class="page-btn-sima page-btn active">{{ $page }}</span>
                                        @else
                                            <a href="{{ $url }}" class="page-btn-sima page-btn page-link">{{ $page }}</a>
                                        @endif
                                    @endforeach

                                    @if ($inventaris->hasMorePages())
                                        <a href="{{ $inventaris->nextPageUrl() }}" class="page-btn-sima page-btn page-link" title="Next"><i data-feather="chevron-right" style="width: 14px; height: 14px;"></i></a>
                                    @else
                                        <span class="page-btn-sima page-btn disabled" style="opacity: 0.5; cursor: not-allowed;" title="Next"><i data-feather="chevron-right" style="width: 14px; height: 14px;"></i></span>
                                    @endif
                                </nav>

                                <!-- Jump to Page Input -->
                                <div class="page-jump-wrap">
                                    <label for="pageJumpInput" style="color: var(--color-text-muted);">Ke hal.</label>
                                    <input id="pageJumpInput" type="number" class="page-jump-input form-control-sima" style="width: 52px; height: 32px; padding: 2px 6px; text-align: center; font-size: 13px;" min="1" max="{{ $inventaris->lastPage() }}" value="{{ $inventaris->currentPage() }}" placeholder="1–{{ $inventaris->lastPage() }}" data-last-page="{{ $inventaris->lastPage() }}">
                                </div>
                            </div>

                            <!-- Range Info Text -->
                            <div class="pagination-right" id="paginationRangeInfo" style="font-size: 13px; color: var(--color-text-muted); font-variant-numeric: tabular-nums;">
                                Menampilkan <strong>{{ $inventaris->firstItem() ?? 0 }}-{{ $inventaris->lastItem() ?? 0 }}</strong> dari <strong>{{ $inventaris->total() }}</strong> aset
                            </div>
                        </div>
                    @endif
                    </div>{{-- /#aset-section --}}
                </div><!-- /.portal-section -->

                <!-- Section: Audit Data -->
                <div class="portal-section" style="border-top: none; margin-top: 0; padding-top: 36px; margin-bottom: 32px;">
                    <div class="portal-section-header">
                        <h2 class="portal-section-title">
                            <span>Informasi Sistem & Audit Data</span>
                        </h2>
                    </div>
                    <div style="display: flex; justify-content: space-evenly; gap: 36px; font-size: 12.5px; flex-wrap: wrap;">
                        <div class="portal-kv-item" style="flex: 1; min-width: 180px;">
                            <span class="portal-kv-label">ID Ruangan</span>
                            <span class="portal-kv-value" style="font-family: monospace;">#{{ $ruangan->id }}</span>
                        </div>
                        <div class="portal-kv-item" style="flex: 1; min-width: 180px;">
                            <span class="portal-kv-label">Tanggal Dibuat</span>
                            <span class="portal-kv-value">{{ $ruangan->created_at ? $ruangan->created_at->format('d M Y, H:i') : '-' }}</span>
                        </div>
                        <div class="portal-kv-item" style="flex: 1; min-width: 180px;">
                            <span class="portal-kv-label">Terakhir Diperbarui</span>
                            <span class="portal-kv-value">{{ $ruangan->updated_at ? $ruangan->updated_at->format('d M Y, H:i') : '-' }}</span>
                        </div>
                    </div>
                </div>

            </div><!-- /.portal-card -->
        </div>
    </div>
    @include('layouts.footer-block')
    @include('layouts.footer-js')
    <script>
    (function () {
        const baseUrl = window.location.pathname; // /ruangan/{id}

        // ── Loading state ──────────────────────────────────────────────────
        function setLoading(on) {
            const section = document.getElementById('aset-section');
            if (!section) return;
            section.style.opacity = on ? '0.45' : '1';
            section.style.pointerEvents = on ? 'none' : '';
            section.style.transition = 'opacity 0.15s';
        }

        // ── Render rows ────────────────────────────────────────────────────
        function renderRows(items, firstItem) {
            const container = document.getElementById('aset-list-container');
            if (!container) return;
            container.innerHTML = items.map((aset, i) => `
                <div style="display: flex; align-items: center; justify-content: space-between; font-size: 13.5px; line-height: 1.5; color: var(--color-text); padding: 12px 0; border-bottom: 1px dashed var(--color-border, #cfdcc8);">
                    <div style="display: flex; align-items: center; min-width: 0; padding-right: 12px;">
                        <span style="color: var(--color-text-muted); font-variant-numeric: tabular-nums; min-width: 24px; font-weight: 500; flex-shrink: 0;">
                            ${firstItem + i}.
                        </span>
                        <span style="font-weight: 600; color: var(--color-text);">
                            ${escHtml(aset.display_name)}
                        </span>
                        <span style="color: var(--color-text-muted); font-size: 13px; font-weight: 500; margin-left: 6px;">
                            (${escHtml(aset.jenis_nama)})
                        </span>
                    </div>
                    <a href="${escHtml(aset.show_url)}" style="flex-shrink: 0; font-size: 12px; color: var(--color-primary); font-weight: 600; text-decoration: none; display: inline-flex; align-items: center; gap: 4px; transition: gap 0.15s ease, opacity 0.15s ease;" onmouseenter="this.style.opacity='0.8'; this.style.gap='6px';" onmouseleave="this.style.opacity='1'; this.style.gap='4px';">
                        <span>Lihat detail</span>
                        <i data-feather="chevron-right" style="width: 13px; height: 13px;"></i>
                    </a>
                </div>
            `).join('');
            if (typeof feather !== 'undefined') feather.replace();
        }

        // ── Render pagination bar ──────────────────────────────────────────
        function renderPagination(data) {
            const rangeEl       = document.getElementById('paginationRangeInfo');
            const nav           = document.getElementById('pagination-nav');
            const jumpInput     = document.getElementById('pageJumpInput');
            const bar           = document.getElementById('pagination-bar');
            const perPageSelect = document.getElementById('perPageSelect');

            if (rangeEl) {
                rangeEl.innerHTML = `Menampilkan <strong>${data.firstItem || 0}-${data.lastItem || 0}</strong> dari <strong>${data.total || 0}</strong> aset`;
            }

            if (perPageSelect && data.perPage) {
                perPageSelect.value = data.perPage;
            }

            if (jumpInput) {
                jumpInput.max = data.lastPage || 1;
                jumpInput.placeholder = `1–${data.lastPage || 1}`;
                jumpInput.dataset.lastPage = data.lastPage || 1;
                jumpInput.value = data.currentPage || 1;
            }

            if (bar) bar.style.display = (data.total && data.total > 10) ? 'flex' : 'none';

            if (!nav) return;

            // Hitung window halaman
            const cur = data.currentPage || 1, last = data.lastPage || 1;
            let wStart = Math.max(1, cur - 2);
            let wEnd   = Math.min(last, cur + 2);
            if ((wEnd - wStart + 1) < 5) {
                if (wStart === 1) wEnd = Math.min(last, 5);
                else wStart = Math.max(1, wEnd - 4);
            }

            let html = '';

            // Prev
            if (cur <= 1) {
                html += `<span class="page-btn-sima" style="opacity: 0.5; cursor: not-allowed;" title="Previous"><i data-feather="chevron-left" style="width: 14px; height: 14px;"></i></span>`;
            } else {
                const prevUrl = buildUrl(cur - 1);
                html += `<a href="${prevUrl}" class="page-btn-sima page-link" title="Previous"><i data-feather="chevron-left" style="width: 14px; height: 14px;"></i></a>`;
            }

            // Page numbers
            for (let p = wStart; p <= wEnd; p++) {
                if (p === cur) {
                    html += `<span class="page-btn-sima active">${p}</span>`;
                } else {
                    html += `<a href="${buildUrl(p)}" class="page-btn-sima page-link">${p}</a>`;
                }
            }

            // Next
            if (cur >= last) {
                html += `<span class="page-btn-sima" style="opacity: 0.5; cursor: not-allowed;" title="Next"><i data-feather="chevron-right" style="width: 14px; height: 14px;"></i></span>`;
            } else {
                const nextUrl = buildUrl(cur + 1);
                html += `<a href="${nextUrl}" class="page-btn-sima page-link" title="Next"><i data-feather="chevron-right" style="width: 14px; height: 14px;"></i></a>`;
            }

            nav.innerHTML = html;
            if (typeof feather !== 'undefined') feather.replace();

            // Re-bind click pada link baru
            bindPageLinks();
        }

        // ── Build URL dengan query string page & per_page ───────────────────
        function buildUrl(page, limit = null) {
            const u = new URL(window.location.href);
            u.searchParams.set('page', page);
            const pp = limit || document.getElementById('perPageSelect')?.value;
            if (pp) u.searchParams.set('per_page', pp);
            return u.toString();
        }

        // ── Fetch halaman via AJAX ─────────────────────────────────────────
        function fetchPage(page, limit = null) {
            const url = buildUrl(page, limit);
            setLoading(true);
            fetch(url, {
                headers: {
                    'X-Requested-With': 'XMLHttpRequest',
                    'Accept': 'application/json',
                }
            })
            .then(r => r.json())
            .then(data => {
                renderRows(data.items, data.firstItem);
                renderPagination(data);
                // Update URL di browser tanpa reload
                window.history.replaceState(null, '', url);
                // Scroll ke atas card aset
                const card = document.getElementById('aset-section');
                if (card) card.scrollIntoView({ behavior: 'smooth', block: 'nearest' });
            })
            .catch(() => {
                // Fallback: full page reload jika AJAX gagal
                window.location.href = buildUrl(page, limit);
            })
            .finally(() => setLoading(false));
        }

        window.changePerPage = function(limit) {
            fetchPage(1, limit);
        };

        // ── Bind click pada semua .page-link ──────────────────────────────
        function bindPageLinks() {
            document.querySelectorAll('.page-link').forEach(el => {
                el.onclick = function (e) {
                    e.preventDefault();
                    const u = new URL(this.href);
                    const page = parseInt(u.searchParams.get('page') || '1', 10);
                    fetchPage(page);
                };
            });
        }

        // ── Jump input ────────────────────────────────────────────────────
        function bindJumpInput() {
            const jumpInput = document.getElementById('pageJumpInput');
            if (!jumpInput) return;
            jumpInput.addEventListener('keydown', function (e) {
                if (e.key === 'Enter') {
                    e.preventDefault();
                    const last = parseInt(this.dataset.lastPage || this.max || 1, 10);
                    let val    = parseInt(this.value, 10);
                    if (isNaN(val)) return;
                    if (val < 1) val = 1;
                    if (val > last) val = last;
                    this.value = val;
                    fetchPage(val);
                }
            });
            jumpInput.addEventListener('blur', function () {
                const last = parseInt(this.dataset.lastPage || this.max || 1, 10);
                const val  = parseInt(this.value, 10);
                if (isNaN(val) || val < 1 || val > last) return;
                fetchPage(val);
            });
        }

        // ── Escape HTML helper ────────────────────────────────────────────
        function escHtml(str) {
            return String(str ?? '').replace(/&/g,'&amp;').replace(/</g,'&lt;').replace(/>/g,'&gt;').replace(/"/g,'&quot;');
        }

        // ── Init ──────────────────────────────────────────────────────────
        bindPageLinks();
        bindJumpInput();
        try { sessionStorage.setItem('ruangan_returning', '1'); } catch(e) {}
    })();
    </script>
</body>
</html>
