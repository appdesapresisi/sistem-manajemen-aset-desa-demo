<!DOCTYPE html>
<html lang="en" data-pc-preset="{{ $pc_preset_theme }}" data-pc-theme="{{ $pc_dark_layout }}">
<head>
    @include('layouts.head-page-meta', ['title' => 'Tambah Ruangan'])
    @include('layouts.head-css')
    <style>
        .form-section { background: transparent; border: none; padding: 0; margin-bottom: 24px; }
        .form-section-title { font-size: 14.5px; font-weight: 700; color: var(--color-primary); margin-bottom: 16px; display: flex; align-items: center; gap: 8px; }
        .form-section-title i, .form-section-title svg { color: var(--color-primary); width: 16px; height: 16px; }
        .form-grid { display: grid; grid-template-columns: repeat(2, minmax(0, 1fr)); gap: 20px 24px; }
        .form-full { grid-column: 1 / -1; }
        .form-group { margin-bottom: 0; }
        .form-label { font-weight: 600; font-size: 13.5px; color: var(--color-text); margin-bottom: 8px; display: block; }
        .form-label .required { color: #D9383A; font-weight: 700; margin-left: 2px; }
        .form-control { width: 100%; height: 46px; padding: 0 16px; border: 1.5px solid var(--color-border); border-radius: 10px; font-size: 14px; transition: border-color 0.2s ease; background: var(--color-surface); color: var(--color-text); box-shadow: none !important; }
        .form-control:focus { outline: none !important; border-color: var(--color-primary) !important; box-shadow: none !important; }
        .form-control::placeholder { color: var(--color-text-muted); opacity: 0.65; }
        .form-hint { font-size: 12px; color: var(--color-text-muted); margin-top: 6px; }
        
        /* Card Header */
        .card .card-header,
        .card-header {
            background-color: transparent !important;
            padding: 32px 24px 0 24px !important;
            border: none !important;
            border-bottom: none !important;
        }
        .card .card-body,
        .card-body {
            padding-bottom: 0 !important;
        }
        .card .card-header > div,
        .card-header > div,
        .card-header-inner,
        .card-header > .flex {
            border-bottom: 1px dashed var(--color-border) !important;
            padding-bottom: 20px !important;
            width: 100% !important;
        }
        .card-header-icon { display: flex; align-items: center; justify-content: center; width: 44px; height: 44px; min-width: 44px; border-radius: 12px; background: var(--color-primary); margin-right: 14px; flex-shrink: 0; position: relative !important; }
        .card-header-icon svg, .card-header-icon i { color: #fff !important; stroke: #fff !important; width: 22px; height: 22px; position: relative !important; top: unset !important; left: unset !important; }
        
        .form-actions { display: flex; gap: 12px; justify-content: flex-end; padding-top: 24px; border-top: none; margin-top: 24px; margin-bottom: 32px; }
        .btn-cancel { height: 46px; padding: 0 20px; border: 1.5px solid var(--color-border); background: var(--color-surface); color: var(--color-text); border-radius: var(--radius-full); font-weight: 500; font-size: 14px; cursor: pointer; transition: all 0.2s; text-decoration: none; display: inline-flex; align-items: center; gap: 8px; box-shadow: none !important; }
        .btn-cancel:hover { background: var(--color-row-hover); border-color: var(--color-border); color: var(--color-text); }
        .btn-submit { height: 46px; padding: 0 24px; border: none; background: var(--color-primary); color: #fff; border-radius: var(--radius-full); font-weight: 600; font-size: 14px; cursor: pointer; box-shadow: none !important; transition: background-color 0.2s ease; display: inline-flex; align-items: center; gap: 8px; }
        .btn-submit:hover { background: var(--color-primary-hover); color: #fff; }
        .btn-cancel i, .btn-cancel svg { width: 16px; height: 16px; }
        .btn-submit i, .btn-submit svg { width: 18px; height: 18px; }
        @media(max-width:768px){ .form-grid { grid-template-columns: 1fr; } }
        
        /* Searchable Select Component */
        .searchable-select { position: relative; width: 100%; }
        .ss-trigger { display: flex; align-items: center; justify-content: space-between; min-height: 48px; padding: 12px 18px; border: 1px solid var(--color-border); border-radius: var(--radius-sm, 8px); background: var(--color-surface); cursor: pointer; font-size: 13.5px; font-weight: 500; color: var(--color-text); transition: all 0.2s; box-sizing: border-box; }
        .ss-trigger:hover, .ss-trigger.ss-open { border-color: var(--color-primary); background-color: var(--color-row-hover); box-shadow: none; }
        .ss-value-text { flex: 1; overflow: hidden; text-overflow: ellipsis; white-space: nowrap; }
        .ss-dropdown { position: absolute; top: calc(100% + 6px); left: 0; right: 0; background: var(--color-surface); border: 1.5px solid var(--color-border); border-radius: 12px; box-shadow: var(--shadow-float); z-index: 1000; max-height: 280px; display: flex; flex-direction: column; padding: 8px; }
        .ss-search-wrap { padding: 4px 4px 8px 4px; border-bottom: 1px solid var(--color-border); flex-shrink: 0; }
        .ss-search { width: 100%; height: 38px; padding: 0 12px; border: 1.5px solid var(--color-border); border-radius: 8px; font-size: 13px; outline: none; background: var(--color-surface); color: var(--color-text); }
        .ss-search:focus { border-color: var(--color-primary); box-shadow: none; }
        .ss-options { overflow-y: auto; max-height: 200px; padding: 4px 0 0 0; }
        .ss-option { padding: 9px 12px; font-size: 13px; font-weight: 500; color: var(--color-text); border-radius: 6px; cursor: pointer; transition: all 0.15s ease; }
        .ss-option:hover { background: var(--color-row-hover); color: var(--color-primary); }
        .ss-option.ss-selected { background: var(--color-row-hover); font-weight: 700; color: var(--color-primary); }
        .ss-option.ss-hidden { display: none; }
        .ss-empty { padding: 16px; text-align: center; color: var(--color-text-muted); font-size: 13px; }
    </style>
</head>
<body>
    @include('layouts.notification')
    @include('layouts.modals')
    @include('layouts.layout-vertical')
    <div class="pc-container">
        <div class="pc-content">
            @include('layouts.breadcrumb', ['breadcrumbItemHome'=>'Inventaris Desa','breadcrumbItem'=>'Ruangan','breadcrumbItemUrl'=>route('ruangan.index'),'breadcrumbItemActive'=>'Tambah Ruangan'])

            <div class="grid grid-cols-12 gap-x-6">
                <div class="col-span-12 w-full">
                    <div class="card">
                        <div class="card-header">
                            <div class="flex items-center">
                                <div class="card-header-icon"><i data-feather="map-pin"></i></div>
                                <div>
                                    <h5 class="mb-0">Tambah Ruangan Baru</h5>
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

                            <form action="{{ route('ruangan.store') }}" method="POST" novalidate>
                                @csrf
                                <div class="form-section">
                                    <div class="form-section-title">
                      Informasi Ruangan
                    </div>
                                    <div class="form-grid">
                                        <div class="form-group">
                                            <label class="form-label" for="kode">Kode Ruangan <span class="required">*</span></label>
                                             <input type="text" id="kode" name="kode" class="form-control"
                                                    placeholder="Contoh: {{ $nextSuggestedKode ?? 'R-001' }}" value="{{ old('kode', $nextSuggestedKode ?? '') }}"
                                                    maxlength="50" pattern="[a-zA-Z0-9\-_]+" title="Kode ruangan berupa huruf, angka, tanda minus (-), atau garis bawah (_)" required />
                                             @if(!empty($lastKodeRuangan))
                                                 <p class="form-hint" style="color: #4b5563;">Kode terakhir: <strong style="color: #1f3a2a;">{{ $lastKodeRuangan }}</strong> (Rekomendasi: <strong style="color: #1f3a2a;">{{ $nextSuggestedKode }}</strong>)</p>
                                             @else
                                                 <p class="form-hint">Kode unik ruangan (Contoh: R-001, R-002, atau angka 1, 2)</p>
                                             @endif
                                        </div>
                                        <div class="form-group">
                                            <label class="form-label" for="nama">Nama Ruangan <span class="required">*</span></label>
                                            <input type="text" id="nama" name="nama" class="form-control"
                                                   placeholder="Contoh: Ruang Rapat Utama" value="{{ old('nama') }}"
                                                   pattern=".*[a-zA-Z].*" title="Nama ruangan harus memuat huruf (tidak boleh hanya angka)" minlength="3" required />
                                            <p class="form-hint">Nama ruangan yang akan tampil di sistem</p>
                                        </div>
                                        <div class="form-group">
                                            <label class="form-label" for="penanggung_jawab_id">Penanggung Jawab <span class="required">*</span></label>
                                            <input type="hidden" name="penanggung_jawab_id" id="penanggung_jawab_id" value="{{ old('penanggung_jawab_id') }}">
                                            <div class="searchable-select" id="ss-pj">
                                                <div class="ss-trigger" onclick="ssToggle('ss-pj')">
                                                    <span class="ss-value-text" id="ss-pj-text">
                                                        @php
                                                            $oldPj = old('penanggung_jawab_id');
                                                            $oldPjUser = $oldPj ? $userList->firstWhere('id', $oldPj) : null;
                                                            if ($oldPjUser) {
                                                                $rLabel = $oldPjUser->role_name_display ?? '';
                                                                echo e($oldPjUser->nama . ($rLabel ? ' ('.$rLabel.')' : ''));
                                                            } else {
                                                                echo 'Pilih Penanggung Jawab';
                                                            }
                                                        @endphp
                                                    </span>
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
                                                            <div class="ss-option {{ old('penanggung_jawab_id') == $user->id ? 'ss-selected' : '' }}"
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
                                        <div class="form-group form-full">
                                            <label class="form-label" for="deskripsi">Deskripsi</label>
                                            <textarea id="deskripsi" name="deskripsi" class="form-control" rows="4"
                                                      placeholder="Keterangan tambahan tentang ruangan ini...">{{ old('deskripsi') }}</textarea>
                                        </div>
                                    </div>
                                </div>
                                <div class="form-actions">
                                    <a href="{{ route('ruangan.index') }}" class="btn-cancel" onclick="return confirmCancel(event, { returnKey: 'ruangan_returning', isEdit: false })">
                                        <i data-feather="x" style="width:16px;height:16px;"></i> Batal
                                    </a>
                                    <button type="button" class="btn-submit" onclick="confirmSimpan()">
                                        <i data-feather="save"></i> Simpan Ruangan
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
        function confirmSimpan() {
            const form = document.querySelector('form');
            let isValid = true;
            if (typeof window.simaValidateForm === 'function') {
                if (!window.simaValidateForm(form)) isValid = false;
            } else if (!form.checkValidity()) {
                isValid = false;
                const firstInvalid = form.querySelector(':invalid');
                if (firstInvalid) {
                    const target = (firstInvalid.offsetParent !== null) ? firstInvalid : (firstInvalid.closest('.form-group') || firstInvalid);
                    if (target && typeof target.scrollIntoView === 'function') {
                        target.scrollIntoView({ behavior: 'smooth', block: 'center' });
                    }
                    if (typeof firstInvalid.focus === 'function') {
                        try { firstInvalid.focus(); } catch(err) {}
                    }
                }
            }

            // Validasi manual: penanggung_jawab_id wajib
            const pj = document.getElementById('penanggung_jawab_id')?.value;
            if (!pj) {
                isValid = false;
                const pjEl = document.getElementById('penanggung_jawab_id');
                if (typeof window.simaShowFieldError === 'function') {
                    window.simaShowFieldError(pjEl, 'Penanggung Jawab wajib dipilih.');
                }
                const trigger = document.querySelector('#ss-pj .ss-trigger') || pjEl;
                if (trigger && typeof window.simaScrollToField === 'function') {
                    window.simaScrollToField(trigger);
                } else if (trigger && typeof trigger.scrollIntoView === 'function') {
                    trigger.scrollIntoView({ behavior: 'smooth', block: 'center' });
                }
            }

            if (!isValid) return;
            const nama = document.getElementById('nama').value.trim();
            AppModal.confirm(
                'Simpan Ruangan',
                'Apakah Anda yakin ingin menyimpan data ruangan <strong>' + nama + '</strong>?',
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
                // Reset error style saat dibuka
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
        document.addEventListener('DOMContentLoaded', function() {
            try { sessionStorage.setItem('ruangan_returning', '1'); } catch(e) {}
        });
    </script>
</body>
</html>
