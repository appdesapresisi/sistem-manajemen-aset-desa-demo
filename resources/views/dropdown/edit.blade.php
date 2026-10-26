<!DOCTYPE html>
<html lang="en" data-pc-preset="{{ $pc_preset_theme }}" data-pc-theme="{{ $pc_dark_layout }}">
<head>
    @include('layouts.head-page-meta', ['title' => 'Edit Dropdown'])
    @include('layouts.head-css')
    <style>
        .form-section { background:transparent; border:none; padding:0; margin-bottom:24px; }
        .form-section + .form-section { margin-top:24px; padding-top:24px; border-top:1px dashed var(--color-border); }
        .form-section-title { font-size:14.5px; font-weight:700; color:var(--color-primary); margin-bottom:16px; display:flex; align-items:center; gap:8px; }
        .form-section-title i, .form-section-title svg { width:16px; height:16px; color:var(--color-primary); }
        .form-group { margin-bottom:16px; }
        .form-label { font-weight:600; font-size:13.5px; color:var(--color-text); margin-bottom:8px; display:block; }
        .form-label .required { color:#D9383A; font-weight:700; margin-left:2px; }
        .form-control { width:100%; height:46px; padding:0 16px; border:1.5px solid var(--color-border); border-radius:10px; font-size:14px; transition:border-color 0.2s ease; background:var(--color-surface); color:var(--color-text); box-sizing:border-box; box-shadow:none !important; }
        textarea.form-control { height:auto; min-height:80px; padding:12px 16px; }
        .form-control:focus, textarea.form-control:focus { outline:none !important; border-color:var(--color-primary) !important; box-shadow:none !important; }
        .form-hint { font-size:12px; color:var(--color-text-muted); margin-top:6px; }

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
        .card-header-icon {
            display: flex;
            align-items: center;
            justify-content: center;
            width: 44px;
            height: 44px;
            min-width: 44px;
            border-radius: 12px;
            background: var(--color-primary);
            margin-right: 14px;
            position: relative !important;
            flex-shrink: 0;
        }
        .card-header-icon i, .card-header-icon svg {
            color: #fff !important;
            stroke: #fff !important;
            width: 22px;
            height: 22px;
            position: relative !important;
            top: unset !important;
            left: unset !important;
        }

        .form-actions { display:flex; gap:12px; justify-content:flex-end; padding-top:24px; border-top: none; margin-top:24px; margin-bottom:32px; }
        .btn-cancel { height:46px; padding:0 20px; border:1.5px solid var(--color-border); background:var(--color-surface); color:var(--color-text); border-radius:var(--radius-full); font-weight:500; font-size:14px; cursor:pointer; transition:all 0.2s; text-decoration:none; display:inline-flex; align-items:center; gap:8px; box-shadow:none !important; }
        .btn-cancel:hover { background:var(--color-row-hover); border-color:var(--color-border); color:var(--color-text); }
        .btn-submit { height:46px; padding:0 24px; border:none; background:var(--color-primary); color:#fff; border-radius:var(--radius-full); font-weight:600; font-size:14px; cursor:pointer; box-shadow:none !important; transition:background-color 0.2s ease; display:inline-flex; align-items:center; gap:8px; }
        .btn-submit:hover { background:var(--color-primary-hover, #3A5C39); color:#fff; }
        .btn-cancel i, .btn-cancel svg { width:16px; height:16px; }
        .btn-submit i, .btn-submit svg { width:18px; height:18px; }

        /* Items section (Clean vertical list, Anti-Table Box) */
        .items-header {
            display: flex;
            align-items: center;
            justify-content: space-between;
            margin-bottom: 14px;
        }
        .items-header-title {
            font-size: 14.5px;
            font-weight: 700;
            color: var(--color-primary);
            display: flex;
            align-items: center;
            gap: 8px;
        }
        .btn-add-item {
            display: inline-flex;
            align-items: center;
            gap: 6px;
            padding: 7px 16px;
            background: var(--color-primary);
            border: 1px solid var(--color-primary);
            border-radius: var(--radius-full);
            color: #fff;
            font-size: 13px;
            font-weight: 600;
            cursor: pointer;
            transition: all 0.2s ease;
        }
        .btn-add-item:hover {
            background: var(--color-primary-hover, #162c1f);
            border-color: var(--color-primary-hover, #162c1f);
        }
        .btn-add-item i, .btn-add-item svg { width: 14px; height: 14px; stroke-width: 2.2; }
        .items-body {
            display: flex;
            flex-direction: column;
        }
        .item-row {
            display: flex;
            align-items: center;
            gap: 12px;
            padding: 12px 0;
            border-bottom: 1px dashed var(--color-border);
        }
        .item-num {
            color: var(--color-text-muted);
            font-variant-numeric: tabular-nums;
            min-width: 24px;
            font-weight: 600;
            font-size: 14px;
            flex-shrink: 0;
        }
        .item-input {
            flex: 1;
            height: 42px !important;
            padding: 0 14px !important;
            border: 1.5px solid var(--color-border, #cfdcc8) !important;
            border-radius: 10px !important;
            font-size: 13.5px;
            font-weight: 500;
            background: var(--color-surface, #f8faf7) !important;
            color: var(--color-text, #16251b) !important;
            box-shadow: none !important;
            outline: none !important;
            box-sizing: border-box !important;
            transition: border-color 0.15s ease !important;
        }
        .item-input:focus {
            outline: none !important;
            border-color: var(--color-primary, #1f3a2a) !important;
            box-shadow: none !important;
            background: var(--color-surface, #f8faf7) !important;
        }
        .item-input::placeholder {
            color: var(--color-text-muted);
            font-weight: 400;
        }
        .btn-del-item {
            width: 36px;
            height: 36px;
            border: 1px solid var(--color-danger-soft, #fecaca);
            border-radius: var(--radius-full);
            background: var(--color-danger-soft, #fee2e2);
            color: var(--color-danger, #dc2626);
            cursor: pointer;
            display: flex;
            align-items: center;
            justify-content: center;
            transition: all 0.15s;
            flex-shrink: 0;
        }
        .btn-del-item:hover {
            background: #fca5a5;
            color: #991b1b;
        }
        .btn-del-item i, .btn-del-item svg { width: 15px; height: 15px; }
        .items-empty {
            padding: 24px 0;
            text-align: center;
            color: var(--color-text-muted);
            font-size: 13px;
            border-bottom: 1px dashed var(--color-border);
        }
    </style>
</head>
<body>
    @include('layouts.notification')
    @include('layouts.modals')
    @include('layouts.layout-vertical')
    <div class="pc-container">
        <div class="pc-content">
            @include('layouts.breadcrumb', ['breadcrumbItemHome'=>'Master Data','breadcrumbItem'=>'Dropdown Dinamis','breadcrumbItemUrl'=>route('dropdown.index'),'breadcrumbItemActive'=>'Edit Dropdown'])

            <div class="grid grid-cols-12 gap-x-6">
                <div class="col-span-12 w-full">
                    <div class="card">
                        <div class="card-header">
                            <div class="flex items-center">
                                <div class="card-header-icon"><i data-feather="list"></i></div>
                                <div>
                                    <h5 class="mb-0">Edit Dropdown</h5>
                                </div>
                            </div>
                        </div>
                        <div class="card-body pt-4">
                            <form id="dropdown-form" action="{{ route('dropdown.update', $dropdown->id) }}" method="POST" novalidate>
                                @csrf @method('PUT')

                                <div class="form-section">
                                    <div class="form-section-title">
                                        Informasi Dropdown
                                    </div>
                                    <div class="form-grid">
                                        <div class="form-group form-full">
                                            <label class="form-label" for="nama">Nama Dropdown <span class="required">*</span></label>
                                            <input type="text" id="nama" name="nama" class="form-control"
                                                   value="{{ old('nama', $dropdown->nama) }}"
                                                   pattern=".*[a-zA-Z].*" title="Nama dropdown harus memuat huruf (tidak boleh hanya angka)" minlength="3" required />
                                        </div>
                                        <div class="form-group form-full">
                                            <label class="form-label" for="deskripsi">Deskripsi <span class="optional" style="color:#9ca3af;font-weight:400;font-size:11px;">(opsional)</span></label>
                                            <textarea id="deskripsi" name="deskripsi" class="form-control" rows="4">{{ old('deskripsi', $dropdown->deskripsi) }}</textarea>
                                        </div>
                                    </div>
                                </div>

                                <!-- Daftar Pilihan -->
                                <div class="form-section">
                                    <div class="items-header">
                                        <div class="items-header-title">
                                            Daftar Pilihan
                                        </div>
                                        <button type="button" class="btn-sima-secondary" onclick="addItem()">
                                            <i data-feather="plus"></i> Tambah Pilihan
                                        </button>
                                    </div>
                                    <div class="items-body" id="items-body">
                                        <div class="items-empty" id="items-empty" style="display:none;">
                                            Belum ada pilihan. Klik "Tambah Pilihan" untuk menambahkan.
                                        </div>
                                    </div>
                                    @error('items')
                                        <small class="text-danger" style="padding:8px 0;display:block;">{{ $message }}</small>
                                    @enderror
                                </div>

                                <div class="form-actions">
                                    <a href="{{ route('dropdown.index') }}" class="btn-cancel" onclick="return confirmCancel(event, { returnKey: 'dropdown_returning', isEdit: true })">
                                        <i data-feather="x" style="width:16px;height:16px;"></i> Batal
                                    </a>
                                    <button type="button" class="btn-submit" onclick="confirmUpdate()">
                                        <i data-feather="save"></i> Update Dropdown
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
                <span class="item-num" id="item-num-${itemCount}">${itemCount}.</span>
                <input type="text" name="items[${itemCount}][label]" class="item-input" style="border: none !important;"          
                       placeholder="Nama pilihan..." value="${label || ''}" required />
                <button type="button" class="btn-del-item" onclick="removeItem(${itemCount})" title="Hapus pilihan" style="background-color: transparent; border: none !important;">
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
                if (num) num.textContent = (idx + 1) + '.';
            });
        }

        function confirmUpdate() {
            const form = document.getElementById('dropdown-form');
            if (typeof window.simaValidateForm === 'function') {
                if (!window.simaValidateForm(form)) return;
            } else if (!form.checkValidity()) {
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
                return;
            }
            const rows = document.querySelectorAll('.item-row');
            if (rows.length === 0) {
                AppModal.error('Validasi', 'Minimal satu pilihan harus ditambahkan.');
                return;
            }
            AppModal.confirm('Update Dropdown', 'Update data dropdown ini?',
                function() { form.submit(); });
        }

        // Load existing items
        @if(old('items'))
            @foreach(old('items') as $item)
                addItem('{{ addslashes($item['label'] ?? '') }}');
            @endforeach
        @else
            @foreach($dropdown->items as $item)
                addItem('{{ addslashes($item->label) }}');
            @endforeach
        @endif
        document.addEventListener('DOMContentLoaded', function() {
            try { sessionStorage.setItem('dropdown_returning', '1'); } catch(e) {}
        });
    </script>
</body>
</html>
