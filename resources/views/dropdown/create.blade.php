<!DOCTYPE html>
<html lang="en" data-pc-preset="{{ $pc_preset_theme }}" data-pc-theme="{{ $pc_dark_layout }}">
<head>
    @include('layouts.head-page-meta', ['title' => 'Tambah Dropdown'])
    @include('layouts.head-css')
    <style>
        .form-section { background:#f8f9fa; border-radius:12px; padding:20px; margin-bottom:16px; }
        .form-section-title { font-size:14px; font-weight:600; color:#064635; margin-bottom:16px; display:flex; align-items:center; gap:8px; }
        .form-section-title i { width:18px; height:18px; }
        .form-group { margin-bottom:16px; }
        .form-label { font-weight:500; font-size:13px; color:#374151; margin-bottom:6px; display:block; }
        .form-label .required { color:#ef4444; margin-left:2px; }
        .form-control { width:100%; padding:10px 14px; border:1px solid #d1d5db; border-radius:8px; font-size:14px; transition:all 0.2s; background:#fff; box-sizing:border-box; }
        .form-control:focus { outline:none; border-color:#064635; box-shadow:0 0 0 3px rgba(6, 70, 53,0.1); }
        .form-hint { font-size:12px; color:#6b7280; margin-top:4px; }
        .card-header-icon { display:flex; align-items:center; justify-content:center; width:44px; height:44px; min-width:44px; border-radius:12px; background:#519259; margin-right:14px; flex-shrink:0; }
        .card-header-icon i, .card-header-icon svg { color:#fff !important; stroke:#fff !important; width:22px; height:22px; fill:none !important; }
        .form-actions { display:flex; gap:12px; justify-content:flex-end; padding-top:20px; border-top:1px solid #e5e7eb; margin-top:8px; }
        .btn-cancel { padding:10px 20px; border:1px solid #d1d5db; background:#fff; color:#374151; border-radius:8px; font-weight:500; cursor:pointer; transition:all 0.2s; text-decoration:none; display:inline-flex; align-items:center; gap:8px; }
        .btn-cancel:hover { background:#f3f4f6; }
        .btn-submit { padding:10px 24px; border:none; background:#064635; color:#fff; border-radius:8px; font-weight:500; cursor:pointer; transition:all 0.2s; display:flex; align-items:center; gap:8px; }
        .btn-submit:hover { background:#1c5240; }
        .btn-submit i { width:18px; height:18px; }

        /* Items section */
        .items-container { border:1px solid #e2e8f0; border-radius:10px; overflow:hidden; }
        .items-header { background:linear-gradient(135deg,#064635,#519259); padding:12px 16px; display:flex; align-items:center; justify-content:space-between; }
        .items-header-title { font-size:13px; font-weight:600; color:#fff; display:flex; align-items:center; gap:8px; }
        .items-header-title i { width:16px; height:16px; }
        .btn-add-item { display:flex; align-items:center; gap:6px; padding:6px 12px; background:rgba(255,255,255,0.2); border:1px solid rgba(255,255,255,0.3); border-radius:6px; color:#fff; font-size:12px; font-weight:500; cursor:pointer; transition:all 0.2s; }
        .btn-add-item:hover { background:rgba(255,255,255,0.3); }
        .btn-add-item i { width:14px; height:14px; }
        .items-body { padding:0; background:#fff; }
        .item-row { display:flex; align-items:center; gap:8px; padding:10px 12px; border-bottom:1px solid #f1f5f9; }
        .item-row:last-child { border-bottom:none; }
        .item-num { width:28px; height:28px; border-radius:6px; background:#e5e7eb; color:#374151; font-size:11px; font-weight:600; display:flex; align-items:center; justify-content:center; flex-shrink:0; }
        .item-input { flex:1; padding:8px 12px; border:1px solid #e5e7eb; border-radius:6px; font-size:13px; transition:all 0.15s; }
        .item-input:focus { outline:none; border-color:#064635; box-shadow:0 0 0 2px rgba(6, 70, 53,0.1); }
        .btn-del-item { width:32px; height:32px; border:none; border-radius:6px; background:#fee2e2; color:#dc2626; cursor:pointer; display:flex; align-items:center; justify-content:center; transition:all 0.15s; flex-shrink:0; }
        .btn-del-item:hover { background:#fecaca; }
        .btn-del-item i { width:15px; height:15px; }
        .items-empty { padding:32px 20px; text-align:center; color:#9ca3af; font-size:13px; }
    </style>
</head>
<body>
    @include('layouts.notification')
    @include('layouts.modals')
    @include('layouts.layout-vertical')
    <div class="pc-container">
        <div class="pc-content">
            @include('layouts.breadcrumb', ['breadcrumbItemHome'=>'Master Data','breadcrumbItem'=>'Dropdown','breadcrumbItemUrl'=>route('dropdown.index'),'breadcrumbItemActive'=>'Tambah Dropdown'])

            @if($errors->any())
            <div style="background:#fee2e2;border:1px solid #fca5a5;border-radius:8px;padding:12px 16px;margin-bottom:16px;">
                <div style="font-weight:600;color:#dc2626;margin-bottom:6px;">Terdapat kesalahan:</div>
                <ul style="margin:0;padding-left:20px;color:#b91c1c;font-size:13px;">
                    @foreach($errors->all() as $error)<li>{{ $error }}</li>@endforeach
                </ul>
            </div>
            @endif

            <div class="grid grid-cols-12 gap-x-6">
                <div class="col-span-12 lg:col-span-8 lg:col-start-3">
                    <div class="card">
                        <div class="card-header border-b border-gray-100">
                            <div class="flex items-center">
                                <div class="card-header-icon"><i data-feather="list"></i></div>
                                <div>
                                    <h5 class="mb-1">Tambah Dropdown Baru</h5>
                                    <p class="text-muted text-sm mb-0">Buat master data dropdown dengan daftar pilihan</p>
                                </div>
                            </div>
                        </div>
                        <div class="card-body pt-4">
                            <form id="dropdown-form" action="{{ route('dropdown.store') }}" method="POST">
                                @csrf

                                <!-- Informasi Dropdown -->
                                <div class="form-section">
                                    <div class="form-section-title"><i data-feather="info"></i> Informasi Dropdown</div>
                                    <div class="form-group">
                                        <label class="form-label" for="nama">Nama Dropdown <span class="required">*</span></label>
                                        <input type="text" id="nama" name="nama" class="form-control"
                                               placeholder="Contoh: Jenis Konstruksi, Status Tanah"
                                               value="{{ old('nama') }}" required />
                                        <p class="form-hint">Nama ini akan muncul saat memilih tipe field di jenis inventaris</p>
                                    </div>
                                    <div class="form-group" style="margin-bottom:0;">
                                        <label class="form-label" for="deskripsi">Deskripsi <span style="color:#9ca3af;font-weight:400;font-size:11px;">(opsional)</span></label>
                                        <textarea id="deskripsi" name="deskripsi" class="form-control" rows="2"
                                                  placeholder="Keterangan singkat tentang dropdown ini...">{{ old('deskripsi') }}</textarea>
                                    </div>
                                </div>

                                <!-- Daftar Pilihan -->
                                <div class="form-section" style="padding:0;overflow:hidden;">
                                    <div class="items-container">
                                        <div class="items-header">
                                            <div class="items-header-title">
                                                <i data-feather="chevron-down"></i>
                                                Daftar Pilihan
                                            </div>
                                            <button type="button" class="btn-add-item" onclick="addItem()">
                                                <i data-feather="plus"></i> Tambah Pilihan
                                            </button>
                                        </div>
                                        <div class="items-body" id="items-body">
                                            <div class="items-empty" id="items-empty">
                                                Belum ada pilihan. Klik "Tambah Pilihan" untuk menambahkan.
                                            </div>
                                        </div>
                                    </div>
                                    @error('items')
                                        <small class="text-danger" style="padding:8px 12px;display:block;">{{ $message }}</small>
                                    @enderror
                                </div>

                                <div class="form-actions">
                                    <a href="{{ route('dropdown.index') }}" class="btn-cancel" onclick="return confirm('Apakah Anda yakin ingin membatalkan? Data yang sudah diisi tidak akan tersimpan.');">
                                        <i data-feather="x" style="width:16px;height:16px;"></i> Batal
                                    </a>
                                    <button type="button" class="btn-submit" onclick="confirmSimpan()">
                                        <i data-feather="save"></i> Simpan Dropdown
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
        let itemCount = 0;

        function addItem(label) {
            itemCount++;
            const empty = document.getElementById('items-empty');
            if (empty) empty.style.display = 'none';

            const body = document.getElementById('items-body');
            const row = document.createElement('div');
            row.className = 'item-row';
            row.id = 'item-row-' + itemCount;
            row.innerHTML = `
                <div class="item-num" id="item-num-${itemCount}">${itemCount}</div>
                <input type="text" name="items[${itemCount}][label]" class="item-input"
                       placeholder="Nama pilihan..." value="${label || ''}" required />
                <button type="button" class="btn-del-item" onclick="removeItem(${itemCount})">
                    <i data-feather="trash-2"></i>
                </button>
            `;
            body.appendChild(row);
            if (typeof feather !== 'undefined') feather.replace();
            renumberItems();
        }

        function removeItem(id) {
            const row = document.getElementById('item-row-' + id);
            if (row) row.remove();
            renumberItems();
            const body = document.getElementById('items-body');
            const rows = body.querySelectorAll('.item-row');
            const empty = document.getElementById('items-empty');
            if (rows.length === 0 && empty) empty.style.display = '';
        }

        function renumberItems() {
            const rows = document.querySelectorAll('.item-row');
            rows.forEach((row, idx) => {
                const num = row.querySelector('[id^="item-num-"]');
                if (num) num.textContent = idx + 1;
            });
        }

        function confirmSimpan() {
            const form = document.getElementById('dropdown-form');
            if (!form.checkValidity()) { form.reportValidity(); return; }
            const rows = document.querySelectorAll('.item-row');
            if (rows.length === 0) {
                AppModal.error('Validasi', 'Minimal satu pilihan harus ditambahkan.');
                return;
            }
            const nama = document.getElementById('nama').value.trim();
            AppModal.confirm('Simpan Dropdown', 'Simpan dropdown <strong>' + nama + '</strong>?',
                function() { form.submit(); });
        }

        // Restore old input jika ada error
        @if(old('items'))
            @foreach(old('items') as $item)
                addItem('{{ addslashes($item['label'] ?? '') }}');
            @endforeach
        @else
            // Default: tambah 1 baris kosong
            addItem();
        @endif
    </script>
</body>
</html>
