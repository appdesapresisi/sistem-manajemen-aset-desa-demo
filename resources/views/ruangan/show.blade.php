<!DOCTYPE html>
<html lang="en" data-pc-preset="{{ $pc_preset_theme }}" data-pc-theme="{{ $pc_dark_layout }}">
<head>
    @include('layouts.head-page-meta', ['title' => 'Detail Ruangan - '.$ruangan->nama])
    @include('layouts.head-css')
    <style>
        .page-center { max-width:720px; margin:0 auto; }
        .d-card { background:#fff; border-radius:12px; border:1px solid #e2e8f0; box-shadow:0 1px 3px rgba(0,0,0,.05); margin-bottom:14px; overflow:hidden; }
        .d-card-header { padding:13px 20px; border-bottom:1px solid #f1f5f9; display:flex; align-items:center; gap:8px; font-size:12px; font-weight:700; color:#064635; text-transform:uppercase; letter-spacing:.5px; }
        .d-card-header i { width:15px; height:15px; }
        .detail-list { list-style:none; margin:0; padding:0; }
        .detail-list li { display:flex; align-items:flex-start; padding:12px 20px; border-bottom:1px solid #f1f5f9; gap:12px; }
        .detail-list li:last-child { border-bottom:none; }
        .detail-list li:hover { background:#fafbfc; }
        .dl-label { width:140px; flex-shrink:0; font-size:11px; font-weight:700; color:#94a3b8; text-transform:uppercase; letter-spacing:.4px; padding-top:3px; }
        .dl-value { flex:1; font-size:14px; color:#1e293b; font-weight:500; word-break:break-word; }
        .dl-value.empty { color:#cbd5e1; font-style:italic; font-weight:400; }
        .action-bar { background:#fff; border:1px solid #e2e8f0; border-radius:12px; padding:14px 20px; display:flex; gap:10px; align-items:center; flex-wrap:wrap; margin-bottom:14px; box-shadow:0 1px 3px rgba(0,0,0,.05); justify-content:space-between; }
        .action-bar-right { display:flex; gap:10px; align-items:center; }
        .btn { padding:9px 18px; border-radius:8px; font-size:13px; font-weight:600; cursor:pointer; transition:all .2s; display:inline-flex; align-items:center; gap:7px; text-decoration:none; border:none; white-space:nowrap; }
        .btn i { width:15px; height:15px; }
        .btn-back   { border:1px solid #d1d5db !important; background:#fff; color:#374151; }
        .btn-back:hover { background:#f4faf5; }
        .btn-edit   { background:#064635; color:#fff; }
        .btn-edit:hover { background:#1c5240; }
        .btn-delete { background:#dc2626; color:#fff; }
        .btn-delete:hover { background:#b91c1c; }
        .jenis-badge { font-size:13px; font-weight:500; color:#374151; }
        .code-badge { font-size:12px; font-weight:600; color:#374151; }
        .aset-table { width:100%; border-collapse:collapse; }
        .aset-table thead th { background:#f4faf5; padding:12px 16px; text-align:left; font-size:11px; font-weight:600; color:#6b7280; text-transform:uppercase; letter-spacing:.5px; border-bottom:1px solid #f3f4f6; }
        .aset-table tbody td { padding:12px 16px; border-bottom:1px solid #f3f4f6; font-size:13px; color:#374151; }
        .aset-table tbody tr:last-child td { border-bottom:none; }
        .aset-table tbody tr:hover td { background:#f4faf5; }

        /* ── Pagination (sama persis dengan index manajemen-aset) ── */
        .pagination-bar {
            display: grid;
            grid-template-columns: 1fr auto auto 1fr;
            align-items: center;
            padding: 16px 20px;
            border-top: 1px solid #f3f4f6;
            gap: 16px;
        }
        .pagination-info { font-size: 13px; color: #6b7280; }
        .pagination-info strong { color: #064635; font-weight: 600; }
        .pagination-info:last-child { text-align: right; }
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
        .page-btn.disabled { opacity: 0.4; cursor: not-allowed; pointer-events: none; }
        .page-btn i { width: 16px; height: 16px; }
        .page-jump-wrap { display: flex; align-items: center; gap: 6px; margin-left: 6px; }
        .page-jump-wrap label { font-size: 13px; color: #6b7280; white-space: nowrap; }
        .page-jump-input { width: 56px; height: 34px; border: 1px solid #d1d5db; border-radius: 6px; text-align: center; font-size: 13px; color: #374151; outline: none; transition: border-color .15s; }
        .page-jump-input:focus { border-color: #064635; box-shadow: 0 0 0 2px rgba(6, 70, 53,.15); }
        @media (max-width: 575px) {
            .pagination-bar { grid-template-columns: 1fr; gap: 10px; padding: 14px 16px; }
            .pagination-nav { flex-wrap: wrap; justify-content: center; }
            .page-jump-wrap { margin-left: 0; justify-content: center; }
            .pagination-info { text-align: center; }
            .pagination-info:last-child { text-align: center; }
        }
    </style>
</head>
<body data-pc-preset="{{ $pc_preset_theme }}" data-pc-sidebar-theme="{{ $pc_sidebar_theme ?? 'light' }}">
    @include('layouts.notification')
    @include('layouts.modals')
    @include('layouts.layout-vertical')
    <div class="pc-container">
        <div class="pc-content">
            @include('layouts.breadcrumb', ['breadcrumbItemHome'=>'Master Data','breadcrumbItem'=>'Ruangan','breadcrumbItemUrl'=>route('ruangan.index'),'breadcrumbItemActive'=>'Detail Ruangan'])

            <div class="page-center">

                <div class="action-bar">
                    <a href="{{ route('ruangan.index') }}" class="btn btn-back">
                        <i data-feather="arrow-left"></i> Kembali
                    </a>
                    <div class="action-bar-right">
                        @can('update', $ruangan)
                        <a href="{{ route('ruangan.edit', $ruangan->id) }}" class="btn btn-edit">
                            <i data-feather="edit-2"></i> Edit
                        </a>
                        @endcan
                        @can('delete', $ruangan)
                        <form id="deleteForm" action="{{ route('ruangan.destroy', $ruangan->id) }}" method="POST" style="display:none;">
                            @csrf @method('DELETE')
                        </form>
                        <button type="button" class="btn btn-delete"
                                onclick="AppModal.confirmDelete('{{ addslashes($ruangan->nama) }}', function(){ document.getElementById('deleteForm').submit(); })">
                            <i data-feather="trash-2"></i> Hapus
                        </button>
                        @endcan
                    </div>
                </div>

                <div class="d-card">
                    <div class="d-card-header">
                        <i data-feather="info"></i>
                        Informasi Ruangan
                    </div>
                    <ul class="detail-list">
                        <li>
                            <span class="dl-label">Nama</span>
                            <span class="dl-value">{{ $ruangan->nama }}</span>
                        </li>
                        <li>
                            <span class="dl-label">Kode</span>
                            <span class="dl-value {{ $ruangan->kode ? '' : 'empty' }}">
                                {{ $ruangan->kode ?: 'Tidak ada kode' }}
                            </span>
                        </li>
                        <li>
                            <span class="dl-label">Deskripsi</span>
                            <span class="dl-value {{ $ruangan->deskripsi ? '' : 'empty' }}">
                                {{ $ruangan->deskripsi ?: 'Tidak ada deskripsi' }}
                            </span>
                        </li>
                        <li>
                            <span class="dl-label">Penanggung Jawab</span>
                            <span class="dl-value {{ $ruangan->penanggungJawab ? '' : 'empty' }}">
                                {{ $ruangan->penanggungJawab?->nama ?? 'Belum ditentukan' }}
                            </span>
                        </li>
                        <li>
                            <span class="dl-label">Dibuat</span>
                            <span class="dl-value">{{ $ruangan->created_at?->format('d M Y') }}</span>
                        </li>
                    </ul>
                </div>

                <div class="d-card">
                    <div class="d-card-header">
                        <i data-feather="package"></i>
                        Daftar Aset di Ruangan Ini
                        <span id="aset-total-badge" style="margin-left:auto;color:#064635;font-size:12px;font-weight:600;text-transform:none;letter-spacing:normal;">
                            {{ $inventaris->total() }} Aset
                        </span>
                    </div>

                    <div id="aset-section">
                    @if($inventaris->isEmpty())
                    <div style="display:flex;flex-direction:column;align-items:center;justify-content:center;text-align:center;padding:40px;color:#9ca3af;gap:8px;">
                        <i data-feather="inbox" style="width:40px;height:40px;opacity:0.5;"></i>
                        <p style="font-size:13px;margin:0;">Belum ada aset di ruangan ini</p>
                    </div>
                    @else
                    <table class="aset-table" id="aset-table-body">
                        <thead>
                            <tr>
                                <th>No</th>
                                <th>Nama Aset</th>
                                <th>Jenis Inventaris</th>
                                <th>Kode Barang</th>
                                <th>Aksi</th>
                            </tr>
                        </thead>
                        <tbody id="aset-tbody">
                            @foreach($inventaris as $i => $aset)
                            <tr>
                                <td>{{ $inventaris->firstItem() + $i }}</td>
                                <td>{{ $aset->display_name ?? '-' }}</td>
                                <td><span class="jenis-badge">{{ $aset->jenis->nama ?? '-' }}</span></td>
                                <td><span class="code-badge">{{ $aset->kode_barang }}</span></td>
                                <td>
                                    <a href="{{ route('manajemen-aset.show', $aset->id) }}"
                                       style="font-size:12px;color:#064635;font-weight:600;text-decoration:none;">
                                        Lihat &rarr;
                                    </a>
                                </td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>

                    {{-- Pagination Bar --}}
                    @if($inventaris->lastPage() > 1)
                    <div class="pagination-bar" id="pagination-bar">
                        <div class="pagination-info" id="pagination-info-left">
                            Menampilkan <strong>{{ $inventaris->firstItem() }}-{{ $inventaris->lastItem() }}</strong> dari <strong>{{ $inventaris->total() }}</strong> data
                        </div>
                        <nav class="pagination-nav" id="pagination-nav">
                            @if($inventaris->onFirstPage())
                                <span class="page-btn disabled" title="Previous"><i data-feather="chevron-left"></i></span>
                            @else
                                <a href="{{ $inventaris->previousPageUrl() }}" class="page-btn page-link" title="Previous"><i data-feather="chevron-left"></i></a>
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
                                    <span class="page-btn active">{{ $page }}</span>
                                @else
                                    <a href="{{ $url }}" class="page-btn page-link">{{ $page }}</a>
                                @endif
                            @endforeach

                            @if($inventaris->hasMorePages())
                                <a href="{{ $inventaris->nextPageUrl() }}" class="page-btn page-link" title="Next"><i data-feather="chevron-right"></i></a>
                            @else
                                <span class="page-btn disabled" title="Next"><i data-feather="chevron-right"></i></span>
                            @endif
                        </nav>
                        <div class="page-jump-wrap">
                            <label for="pageJumpInput">Ke hal.</label>
                            <input id="pageJumpInput" type="number" class="page-jump-input"
                                   min="1" max="{{ $inventaris->lastPage() }}"
                                   placeholder="1–{{ $inventaris->lastPage() }}"
                                   data-last-page="{{ $inventaris->lastPage() }}">
                        </div>
                        <div class="pagination-info" id="pagination-info-right">
                            Total <strong>{{ $inventaris->total() }}</strong> aset
                        </div>
                    </div>
                    @endif
                    @endif
                    </div>{{-- /#aset-section --}}
                </div>

            </div>{{-- /.page-center --}}
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
            const tbody = document.getElementById('aset-tbody');
            if (!tbody) return;
            tbody.innerHTML = items.map((aset, i) => `
                <tr>
                    <td>${firstItem + i}</td>
                    <td>${escHtml(aset.display_name)}</td>
                    <td><span class="jenis-badge">${escHtml(aset.jenis_nama)}</span></td>
                    <td><span class="code-badge">${escHtml(aset.kode_barang)}</span></td>
                    <td><a href="${escHtml(aset.show_url)}" style="font-size:12px;color:#064635;font-weight:600;text-decoration:none;">Lihat &rarr;</a></td>
                </tr>
            `).join('');
        }

        // ── Render pagination bar ──────────────────────────────────────────
        function renderPagination(data) {
            const infoLeft  = document.getElementById('pagination-info-left');
            const infoRight = document.getElementById('pagination-info-right');
            const nav       = document.getElementById('pagination-nav');
            const badge     = document.getElementById('aset-total-badge');
            const jumpInput = document.getElementById('pageJumpInput');
            const bar       = document.getElementById('pagination-bar');

            if (badge) badge.textContent = data.total + ' Aset';

            if (infoLeft)  infoLeft.innerHTML  = `Menampilkan <strong>${data.firstItem}-${data.lastItem}</strong> dari <strong>${data.total}</strong> data`;
            if (infoRight) infoRight.innerHTML = `Total <strong>${data.total}</strong> aset`;

            if (jumpInput) {
                jumpInput.max = data.lastPage;
                jumpInput.placeholder = `1–${data.lastPage}`;
                jumpInput.dataset.lastPage = data.lastPage;
                jumpInput.value = '';
            }

            if (bar) bar.style.display = data.lastPage > 1 ? '' : 'none';

            if (!nav) return;

            // Hitung window halaman
            const cur = data.currentPage, last = data.lastPage;
            let wStart = Math.max(1, cur - 2);
            let wEnd   = Math.min(last, cur + 2);
            if ((wEnd - wStart + 1) < 5) {
                if (wStart === 1) wEnd = Math.min(last, 5);
                else wStart = Math.max(1, wEnd - 4);
            }

            let html = '';

            // Prev
            if (cur <= 1) {
                html += `<span class="page-btn disabled" title="Previous"><i data-feather="chevron-left"></i></span>`;
            } else {
                const prevUrl = buildUrl(cur - 1);
                html += `<a href="${prevUrl}" class="page-btn page-link" title="Previous"><i data-feather="chevron-left"></i></a>`;
            }

            // Page numbers
            for (let p = wStart; p <= wEnd; p++) {
                if (p === cur) {
                    html += `<span class="page-btn active">${p}</span>`;
                } else {
                    html += `<a href="${buildUrl(p)}" class="page-btn page-link">${p}</a>`;
                }
            }

            // Next
            if (cur >= last) {
                html += `<span class="page-btn disabled" title="Next"><i data-feather="chevron-right"></i></span>`;
            } else {
                const nextUrl = buildUrl(cur + 1);
                html += `<a href="${nextUrl}" class="page-btn page-link" title="Next"><i data-feather="chevron-right"></i></a>`;
            }

            nav.innerHTML = html;
            if (typeof feather !== 'undefined') feather.replace();

            // Re-bind click pada link baru
            bindPageLinks();
        }

        // ── Build URL dengan query string page ─────────────────────────────
        function buildUrl(page) {
            const u = new URL(window.location.href);
            u.searchParams.set('page', page);
            return u.toString();
        }

        // ── Fetch halaman via AJAX ─────────────────────────────────────────
        function fetchPage(page) {
            const url = buildUrl(page);
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
                window.location.href = buildUrl(page);
            })
            .finally(() => setLoading(false));
        }

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
            jumpInput.addEventListener('input', function () {
                const last = parseInt(this.dataset.lastPage || this.max, 10);
                const val  = parseInt(this.value, 10);
                if (!isNaN(val) && val > last) this.value = '';
            });
            jumpInput.addEventListener('keydown', function (e) {
                if (e.key !== 'Enter') return;
                const last = parseInt(this.dataset.lastPage || this.max, 10);
                const val  = parseInt(this.value, 10);
                if (isNaN(val) || val < 1 || val > last) return;
                this.value = '';
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
    })();
    </script>
</body>
</html>
