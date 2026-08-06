<!DOCTYPE html>
<html lang="en" data-pc-preset="{{ $pc_preset_theme }}" data-pc-theme="{{ $pc_dark_layout }}">
<head>
    @include('layouts.head-page-meta', ['title' => 'Edit Ruangan'])
    @include('layouts.head-css')
    <style>
        .form-section { background:#f8f9fa; border-radius:12px; padding:20px; margin-bottom:16px; }
        .form-section-title { font-size:14px; font-weight:600; color:#064635; margin-bottom:16px; display:flex; align-items:center; gap:8px; }
        .form-section-title i { width:18px; height:18px; }
        .form-grid { display:grid; grid-template-columns:repeat(2,minmax(0,1fr)); gap:16px; }
        .form-full { grid-column:1/-1; }
        .form-group { margin-bottom:0; }
        .form-label { font-weight:500; font-size:13px; color:#374151; margin-bottom:6px; display:block; }
        .form-label .required { color:#ef4444; margin-left:2px; }
        .form-control { width:100%; padding:10px 14px; border:1px solid #d1d5db; border-radius:8px; font-size:14px; transition:all 0.2s; background:#fff; }
        .form-control:focus { outline:none; border-color:#064635; box-shadow:0 0 0 3px rgba(6, 70, 53,0.1); }
        .form-control::placeholder { color:#9ca3af; }
        .form-select { appearance:none; -webkit-appearance:none; }
        .form-hint { font-size:12px; color:#6b7280; margin-top:4px; }
        .card-header-icon { display:flex; align-items:center; justify-content:center; width:44px; height:44px; min-width:44px; border-radius:12px; background:#519259; margin-right:14px; flex-shrink:0; position:relative !important; }
        .card-header-icon svg, .card-header-icon i { color:#fff !important; stroke:#fff !important; width:22px; height:22px; position:relative !important; top:unset !important; left:unset !important; }
        .form-actions { display:flex; gap:12px; justify-content:flex-end; padding-top:20px; border-top:1px solid #e5e7eb; margin-top:24px; }
        .btn-cancel { padding:10px 20px; border:1px solid #d1d5db; background:#fff; color:#374151; border-radius:8px; font-weight:500; cursor:pointer; transition:all 0.2s; text-decoration:none; display:inline-flex; align-items:center; gap:8px; }
        .btn-cancel:hover { background:#f3f4f6; }
        .btn-submit { padding:10px 24px; border:none; background:#064635; color:#fff; border-radius:8px; font-weight:500; cursor:pointer; transition:all 0.2s; display:flex; align-items:center; gap:8px; }
        .btn-submit:hover { background:#1c5240; }
        .btn-submit i { width:18px; height:18px; }
        @media(max-width:768px){ .form-grid { grid-template-columns:1fr; } }
        /* Searchable Select Component */
        .searchable-select { position: relative; width: 100%; }
        .ss-trigger { display: flex; align-items: center; justify-content: space-between; padding: 10px 14px; border: 1px solid #d1d5db; border-radius: 8px; background: #fff; cursor: pointer; font-size: 14px; color: #374151; transition: all 0.2s; }
        .ss-trigger:hover { border-color: #d1d5db; }
        .ss-trigger.ss-open { border-color: #064635; box-shadow: 0 0 0 3px rgba(6, 70, 53,0.1); }
        .ss-value-text { flex: 1; overflow: hidden; text-overflow: ellipsis; white-space: nowrap; }
        .ss-dropdown { position: absolute; top: calc(100% + 4px); left: 0; right: 0; background: #fff; border: 1px solid #e5e7eb; border-radius: 10px; box-shadow: 0 10px 40px rgba(0,0,0,0.12); z-index: 1000; max-height: 280px; display: flex; flex-direction: column; }
        .ss-search-wrap { padding: 10px 12px; border-bottom: 1px solid #f3f4f6; flex-shrink: 0; }
        .ss-search { width: 100%; padding: 8px 12px; border: 1px solid #e5e7eb; border-radius: 6px; font-size: 13px; outline: none; }
        .ss-search:focus { border-color: #064635; box-shadow: 0 0 0 3px rgba(6, 70, 53,0.1); }
        .ss-options { overflow-y: auto; max-height: 200px; }
        .ss-option { padding: 9px 14px; font-size: 13px; color: #374151; cursor: pointer; transition: background 0.15s; }
        .ss-option:hover { background: #f4faf5; }
        .ss-option.ss-selected { background: #f0f4f8; font-weight: 600; color: #064635; }
        .ss-option.ss-hidden { display: none; }
        .ss-empty { padding: 16px; text-align: center; color: #9ca3af; font-size: 13px; }
    </style>
</head>
<body>
    @include('layouts.notification')
    @include('layouts.modals')
    @include('layouts.layout-vertical')
    <div class="pc-container">
        <div class="pc-content">
            @include('layouts.breadcrumb', ['breadcrumbItemHome'=>'Master Data','breadcrumbItem'=>'Ruangan','breadcrumbItemUrl'=>route('ruangan.index'),'breadcrumbItemActive'=>'Edit Ruangan'])

            <div class="grid grid-cols-12 gap-x-6">
                <div class="col-span-12 lg:col-span-8 lg:col-start-3">
                    <div class="card">
                        <div class="card-header border-b border-gray-100">
                            <div class="flex items-center">
                                <div class="card-header-icon"><i data-feather="edit-2"></i></div>
                                <div>
                                    <h5 class="mb-1">Edit Ruangan</h5>
                                    <p class="text-muted text-sm mb-0">Perbarui informasi dan detail ruangan</p>
                                </div>
                            </div>
                        </div>
                        <div class="card-body pt-4">
                            @if($errors->any())
                            <div style="background:#fee2e2;border:1px solid #fca5a5;border-radius:8px;padding:12px 16px;margin-bottom:16px;">
                                <div style="font-weight:600;color:#dc2626;margin-bottom:6px;">Terdapat kesalahan:</div>
                                <ul style="margin:0;padding-left:0;list-style-position:inside;color:#b91c1c;font-size:13px;">
                                    @foreach($errors->all() as $error)<li>{{ $error }}</li>@endforeach
                                </ul>
                            </div>
                            @endif

                            <form action="{{ route('ruangan.update', $ruangan->id) }}" method="POST">
                                @csrf @method('PUT')
                                <div class="form-section">
                                    <div class="form-section-title"><i data-feather="info"></i> Informasi Ruangan</div>
                                    <div class="form-grid">
                                        <div class="form-group form-full">
                                            <label class="form-label" for="nama">Nama Ruangan <span class="required">*</span></label>
                                            <input type="text" id="nama" name="nama" class="form-control"
                                                   placeholder="Contoh: Ruang Rapat Utama"
                                                   value="{{ old('nama', $ruangan->nama) }}" required />
                                        </div>
                                        <div class="form-group form-full">
                                            <label class="form-label" for="kode">Kode Ruangan <span class="required">*</span></label>
                                            <input type="text" id="kode" name="kode" class="form-control"
                                                   value="{{ old('kode', $ruangan->kode) }}"
                                                   inputmode="numeric" pattern="[0-9]*" required />
                                            <p class="form-hint">Angka unik, tidak boleh sama dengan ruangan lain</p>
                                        </div>
                                        {{-- <div class="form-group">
                                            <label class="form-label" for="lokasi">Lokasi / Gedung</label>
                                            <input type="text" id="lokasi" name="lokasi" class="form-control"
                                                   placeholder="Contoh: Gedung A Lantai 2"
                                                   value="{{ old('lokasi', $ruangan->lokasi) }}" maxlength="255" />
                                        </div> --}}
                                        <div class="form-group form-full">
                                            <label class="form-label" for="deskripsi">Deskripsi</label>
                                            <textarea id="deskripsi" name="deskripsi" class="form-control" rows="3"
                                                      placeholder="Keterangan tambahan...">{{ old('deskripsi', $ruangan->deskripsi) }}</textarea>
                                        </div>
                                        <div class="form-group form-full">
                                            <label class="form-label" for="penanggung_jawab_id">Penanggung Jawab <span class="required">*</span></label>
                                            @php
                                                $currentPjId = old('penanggung_jawab_id', $ruangan->penanggung_jawab_id);
                                                $currentPjUser = $currentPjId ? $userList->firstWhere('id', $currentPjId) : null;
                                                $currentPjLabel = $currentPjUser
                                                    ? $currentPjUser->nama . ($currentPjUser->role_name_display ? ' ('.$currentPjUser->role_name_display.')' : '')
                                                    : '-- Pilih Penanggung Jawab --';
                                            @endphp
                                            <input type="hidden" name="penanggung_jawab_id" id="penanggung_jawab_id" value="{{ $currentPjId ?? '' }}">
                                            <div class="searchable-select" id="ss-pj">
                                                <div class="ss-trigger" onclick="ssToggle('ss-pj')">
                                                    <span class="ss-value-text" id="ss-pj-text">{{ $currentPjLabel }}</span>
                                                    <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="#9ca3af" stroke-width="2"><polyline points="6 9 12 15 18 9"/></svg>
                                                </div>
                                                <div class="ss-dropdown" style="display:none;">
                                                    <div class="ss-search-wrap">
                                                        <input type="text" class="ss-search" placeholder="Cari penanggung jawab..." oninput="ssFilter('ss-pj', this.value)">
                                                    </div>
                                                    <div class="ss-options">
                                                        @forelse($userList as $user)
                                                            @php
                                                                $roleLabel = $user->role_name_display ?? '';
                                                                $label = $user->nama . ($roleLabel ? ' ('.$roleLabel.')' : '');
                                                            @endphp
                                                            <div class="ss-option {{ $currentPjId == $user->id ? 'ss-selected' : '' }}"
                                                                 data-value="{{ $user->id }}"
                                                                 data-search="{{ strtolower($label) }}"
                                                                 onclick="ssSelect('ss-pj', '{{ $user->id }}', '{{ addslashes($label) }}')">
                                                                {{ $label }}
                                                            </div>
                                                        @empty
                                                            <div class="ss-empty">Belum ada data pengguna</div>
                                                        @endforelse
                                                    </div>
                                                </div>
                                            </div>
                                            @error('penanggung_jawab_id')
                                                <small class="text-danger">{{ $message }}</small>
                                            @enderror
                                        </div>
                                    </div>
                                </div>
                                <div class="form-actions">
                                    <a href="{{ route('ruangan.index') }}" class="btn-cancel" onclick="return confirm('Apakah Anda yakin ingin membatalkan? Data yang sudah diisi tidak akan tersimpan.');">
                                        <i data-feather="x" style="width:16px;height:16px;"></i> Batal
                                    </a>
                                    <button type="button" class="btn-submit" onclick="confirmUpdate()">
                                        <i data-feather="save"></i> Simpan Perubahan
                                    </button>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    @include('layouts.footer-block')
    @include('layouts.footer-js')
    <script>
        function confirmUpdate() {
            const form = document.querySelector('form');
            if (!form.checkValidity()) {
                form.reportValidity();
                return;
            }
            // Validasi manual: penanggung_jawab_id wajib
            const pj = document.getElementById('penanggung_jawab_id').value;
            if (!pj) {
                const trigger = document.querySelector('#ss-pj .ss-trigger');
                if (trigger) { trigger.style.borderColor = '#ef4444'; trigger.style.boxShadow = '0 0 0 3px rgba(239,68,68,0.1)'; }
                alert('Penanggung Jawab wajib dipilih.');
                return;
            }
            const nama = document.getElementById('nama').value.trim();
            AppModal.confirm(
                'Simpan Perubahan',
                'Apakah Anda yakin ingin menyimpan perubahan data ruangan <strong>' + nama + '</strong>?',
                function() { form.submit(); }
            );
        }

        // ── Searchable Select Component ──
        function ssToggle(id) {
            const wrap = document.getElementById(id);
            const dd = wrap.querySelector('.ss-dropdown');
            const trigger = wrap.querySelector('.ss-trigger');
            const isOpen = dd.style.display !== 'none';
            document.querySelectorAll('.searchable-select .ss-dropdown').forEach(d => { d.style.display = 'none'; });
            document.querySelectorAll('.ss-trigger').forEach(t => t.classList.remove('ss-open'));
            if (!isOpen) {
                dd.style.display = 'flex';
                trigger.classList.add('ss-open');
                trigger.style.borderColor = '';
                trigger.style.boxShadow = '';
                const search = wrap.querySelector('.ss-search');
                if (search) { search.value = ''; ssFilter(id, ''); setTimeout(() => search.focus(), 50); }
            }
        }
        function ssFilter(id, q) {
            const wrap = document.getElementById(id);
            const opts = wrap.querySelectorAll('.ss-option');
            const lower = q.toLowerCase();
            let visible = 0;
            opts.forEach(opt => {
                const match = opt.getAttribute('data-search').includes(lower);
                opt.classList.toggle('ss-hidden', !match);
                if (match) visible++;
            });
            const empty = wrap.querySelector('.ss-empty');
            if (empty) empty.style.display = visible === 0 ? 'block' : 'none';
        }
        function ssSelect(id, value, label) {
            const wrap = document.getElementById(id);
            const hidden = wrap.parentElement.querySelector('input[type="hidden"]');
            const text = wrap.querySelector('.ss-value-text');
            if (hidden) hidden.value = value;
            if (text) text.textContent = label || value;
            wrap.querySelectorAll('.ss-option').forEach(opt => {
                opt.classList.toggle('ss-selected', opt.getAttribute('data-value') === String(value));
            });
            wrap.querySelector('.ss-dropdown').style.display = 'none';
            wrap.querySelector('.ss-trigger').classList.remove('ss-open');
        }
        document.addEventListener('click', function(e) {
            if (!e.target.closest('.searchable-select')) {
                document.querySelectorAll('.searchable-select .ss-dropdown').forEach(d => { d.style.display = 'none'; });
                document.querySelectorAll('.ss-trigger').forEach(t => t.classList.remove('ss-open'));
            }
        });
    </script>
</body>
</html>
