<!doctype html>
<html lang="en" data-pc-preset="{{ $pc_preset_theme }}" data-pc-theme="{{ $pc_dark_layout }}">
  <head>
    @include('layouts.head-page-meta', ['title' => 'Tambah Pejabat'])
    @include('layouts.head-css')
    <style>
      /* Form Styling - SIMA Standard */
      .form-section {
        background: transparent;
        border: none;
        padding: 0;
        margin-bottom: 24px;
      }
      .form-section + .form-section {
        margin-top: 24px;
        padding-top: 24px;
        border-top: 1px dashed var(--color-border);
      }
      .form-section-title {
        font-size: 14.5px;
        font-weight: 700;
        color: var(--color-primary);
        margin-bottom: 16px;
        display: flex;
        align-items: center;
        gap: 8px;
      }
      .form-section-title i,
      .form-section-title svg {
        color: var(--color-primary);
        width: 16px;
        height: 16px;
      }
      .form-grid {
        display: grid;
        grid-template-columns: 1fr;
        gap: 16px;
      }
      .form-group {
        margin-bottom: 0;
      }
      .form-label {
        font-weight: 600;
        font-size: 13.5px;
        color: var(--color-text);
        margin-bottom: 8px;
        display: block;
      }
      .form-label .required {
        color: #D9383A;
        font-weight: 700;
        margin-left: 2px;
      }
      .form-label .optional {
        color: var(--color-text-muted);
        font-weight: 400;
        font-size: 12px;
        margin-left: 4px;
      }
      .form-control, textarea.form-control {
        width: 100%;
        height: 46px;
        padding: 0 16px;
        border: 1.5px solid var(--color-border);
        border-radius: 10px;
        font-size: 14px;
        transition: border-color 0.2s ease;
        background: var(--color-surface);
        color: var(--color-text);
        box-shadow: none !important;
      }
      textarea.form-control {
        height: auto;
        min-height: 80px;
        padding: 12px 16px;
      }
      .form-control:focus, textarea.form-control:focus {
        outline: none !important;
        border-color: var(--color-primary) !important;
        box-shadow: none !important;
      }
      .form-control::placeholder, textarea.form-control::placeholder {
        color: var(--color-text-muted);
        opacity: 0.65;
      }
      .form-hint {
        font-size: 12px;
        color: var(--color-text-muted);
        margin-top: 6px;
      }
      .form-full {
        grid-column: 1 / -1;
      }
      
      /* Toggle Switch */
      .toggle-wrapper {
        display: flex;
        align-items: center;
        gap: 12px;
      }
      .toggle-switch {
        position: relative;
        width: 48px;
        height: 26px;
      }
      .toggle-switch input {
        opacity: 0;
        width: 0;
        height: 0;
      }
      .toggle-slider {
        position: absolute;
        cursor: pointer;
        top: 0;
        left: 0;
        right: 0;
        bottom: 0;
        background-color: #cbd5e1;
        transition: 0.3s;
        border-radius: 26px;
      }
      .toggle-slider:before {
        position: absolute;
        content: "";
        height: 20px;
        width: 20px;
        left: 3px;
        bottom: 3px;
        background-color: white;
        transition: 0.3s;
        border-radius: 50%;
        box-shadow: none;
      }
      .toggle-switch input:checked + .toggle-slider {
        background-color: #22c55e;
      }
      .toggle-switch input:checked + .toggle-slider:before {
        transform: translateX(22px);
      }
      .toggle-label {
        font-size: 14px;
        color: #374151;
      }
      .toggle-status {
        font-size: 12px;
        padding: 2px 8px;
        border-radius: 9999px;
        font-weight: 500;
      }
      .toggle-status.active {
        background: #dcfce7;
        color: #16a34a;
      }
      .toggle-status.inactive {
        background: #fee2e2;
        color: #dc2626;
      }

      /* File upload */
      .file-upload-wrapper {
        position: relative;
      }
      .file-upload-area {
        display: flex;
        flex-direction: column;
        align-items: center;
        justify-content: center;
        gap: 12px;
        padding: 30px 20px;
        background: #fff;
        border: 2px dashed #d1d5db;
        border-radius: 12px;
        cursor: pointer;
        transition: all 0.2s;
        text-align: center;
      }
      .file-upload-area:hover {
        border-color: #1f3a2a;
        background: #f4faf5;
      }
      .file-upload-area.has-file {
        border-color: #22c55e;
        background: #f0fdf4;
      }
      .file-upload-icon {
        background: transparent !important;
        width: auto !important;
        height: auto !important;
        display: block !important;
        margin: 0 auto 8px auto !important;
      }
      .file-upload-icon i,
      .file-upload-icon svg {
        width: 28px !important;
        height: 28px !important;
        stroke-width: 1.5 !important;
        color: var(--color-text-muted) !important;
        stroke: var(--color-text-muted) !important;
      }
      .file-upload-text {
        font-size: 14px;
        color: #374151;
        font-weight: 500;
      }
      .file-upload-hint {
        font-size: 12px;
        color: #9ca3af;
      }
      .file-preview {
        display: none;
        margin-top: 12px;
        padding: 12px;
        background: #f0fdf4;
        border: 1px solid #bbf7d0;
        border-radius: 8px;
      }
      .file-preview.show {
        display: flex;
        align-items: center;
        gap: 12px;
      }
      .file-preview-img {
        width: 60px;
        height: 60px;
        border-radius: 8px;
        object-fit: contain;
        background: #fff;
        border: 1px solid #e5e7eb;
        padding: 4px;
      }
      .file-preview-info {
        flex: 1;
      }
      .file-preview-name {
        font-size: 13px;
        font-weight: 500;
        color: #374151;
        margin-bottom: 2px;
      }
      .file-preview-size {
        font-size: 11px;
        color: #6b7280;
      }
      .file-preview-remove {
        width: 32px;
        height: 32px;
        border-radius: 8px;
        border: none;
        background: #fee2e2;
        color: #dc2626;
        cursor: pointer;
        display: flex;
        align-items: center;
        justify-content: center;
        transition: all 0.2s;
      }
      .file-preview-remove:hover {
        background: #fecaca;
      }
      .file-preview-remove i {
        width: 16px;
        height: 16px;
      }

@include('partials.signature-pad-styles')

      /* Info box */
      .info-box {
        background: var(--color-primary-tint);
        border: 1px solid var(--color-border);
        border-radius: var(--radius-sm);
        padding: 8px 14px;
        display: flex;
        width: 100%;
        box-sizing: border-box;
        align-items: center;
        gap: 8px;
        margin-top: 14px;
        color: var(--color-text);
      }
      .info-box i,
      .info-box svg {
        color: var(--color-primary);
        stroke: var(--color-primary);
        flex-shrink: 0;
        width: 14px;
        height: 14px;
      }
      .info-box-text {
        font-size: 11.5px;
        color: var(--color-text);
        line-height: 1.45;
        font-weight: 500;
        flex: 1;
      }

      /* Buttons */
      .form-actions {
        display: flex;
        gap: 12px;
        justify-content: flex-end;
        padding-top: 24px;
        border-top: none;
        margin-top: 24px;
        margin-bottom: 32px;
      }
      .btn-cancel {
        height: 46px;
        padding: 0 20px;
        border: 1.5px solid var(--color-border);
        background: var(--color-surface);
        color: var(--color-text);
        border-radius: var(--radius-full);
        font-weight: 500;
        font-size: 14px;
        cursor: pointer;
        transition: all 0.2s;
        text-decoration: none;
        display: inline-flex;
        align-items: center;
        gap: 8px;
        box-shadow: none !important;
      }
      .btn-cancel:hover {
        background: var(--color-row-hover);
        border-color: var(--color-border);
        color: var(--color-text);
      }
      .btn-submit {
        height: 46px;
        padding: 0 24px;
        border: none;
        background: var(--color-primary);
        color: #fff;
        border-radius: var(--radius-full);
        font-weight: 600;
        font-size: 14px;
        cursor: pointer;
        box-shadow: none !important;
        transition: background-color 0.2s ease;
        display: inline-flex;
        align-items: center;
        gap: 8px;
      }
      .btn-submit:hover {
        background: var(--color-primary-hover);
        color: #fff;
      }
      .btn-cancel i,
      .btn-cancel svg {
        width: 16px;
        height: 16px;
      }
      .btn-submit i,
      .btn-submit svg {
        width: 18px;
        height: 18px;
      }

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
      .card-header-icon svg,
      .card-header-icon i {
        color: #fff !important;
        stroke: #fff !important;
        width: 22px;
        height: 22px;
        position: relative !important;
        top: unset !important;
        left: unset !important;
      }

      /* Signature preview styling */
      .signature-guide {
        margin-top: 20px;
        padding: 16px;
        background: #fffbeb;
        border: 1px solid #fbbf24;
        border-radius: 8px;
      }
      .signature-guide-title {
        font-size: 13px;
        font-weight: 600;
        color: #92400e;
        margin-bottom: 8px;
        display: flex;
        align-items: center;
        gap: 6px;
      }
      .signature-guide-title i {
        width: 16px;
        height: 16px;
      }
      .signature-guide-list {
        font-size: 12px;
        color: #a16207;
        margin: 0;
        padding-left: 20px;
      }
      .signature-guide-list li {
        margin-bottom: 4px;
      }

      @media (max-width: 768px) {
        .form-grid {
          grid-template-columns: 1fr;
        }
      }
    </style>
  </head>

  <body>
    @include('layouts.modals')
    @include('layouts.layout-vertical')

    <div class="pc-container">
      <div class="pc-content">
        @include('layouts.breadcrumb', ['breadcrumbItemHome' => 'Master Data', 'breadcrumbItem' => 'Manajemen Pejabat', 'breadcrumbItemUrl' => route('pejabat.index'), 'breadcrumbItemActive' => 'Tambah Pejabat'])
        <div class="grid grid-cols-12 gap-x-6">
          <div class="col-span-12 w-full">
            <div class="card">
              <div class="card-header">
                <div class="flex items-center">
                  <div class="card-header-icon">
                    <i data-feather="user-check"></i>
                  </div>
                  <div>
                    <h5 class="mb-0">Tambah Pejabat Baru</h5>
                  </div>
                </div>
              </div>

              <div class="card-body" style="padding-top: 14px !important;">
                <form id="pejabat-form" method="POST" action="{{ route('pejabat.store') }}" enctype="multipart/form-data" novalidate>
                  @csrf

                  <div class="info-box" style="margin-top: 0 !important; margin-bottom:16px;">
                    <i data-feather="info"></i>
                    <div class="info-box-text">
                      <strong>Catatan:</strong> Data pejabat ini akan digunakan pada fitur Export Data untuk menandatangani dokumen resmi seperti Buku Inventaris Aset Desa, Laporan Inventaris, dan dokumen lainnya.
                    </div>
                  </div>
                  
                  <!-- Informasi Pejabat -->
                  <div class="form-section" style="margin-top:0;">
                    <div class="form-section-title">
                      Informasi Pejabat
                    </div>
                    <div class="form-grid">
                      <div class="form-group">
                        <label class="form-label" for="nama">
                          Nama Lengkap <span class="required">*</span>
                        </label>
                        <input type="text" id="nama" name="nama" class="form-control @error('nama') is-invalid @enderror" placeholder="Masukkan nama lengkap dengan gelar" value="{{ old('nama') }}" pattern="^[a-zA-Z\s\.,'\-]+$" title="Nama pejabat hanya boleh berisi huruf dan tanda baca nama/gelar (tidak boleh mengandung angka)" minlength="3" required />
                        @error('nama')
                        <div class="text-danger text-sm mt-1">{{ $message }}</div>
                        @enderror
                        <p class="form-hint">Contoh: I Nyoman Suwarjana, SE atau Drs. Ahmad Sudrajat, M.Si</p>
                      </div>
                      <div class="form-group">
                        <label class="form-label" for="jabatan">
                          Jabatan <span class="required">*</span>
                        </label>
                        <input type="text" id="jabatan" name="jabatan" class="form-control @error('jabatan') is-invalid @enderror" placeholder="Masukkan jabatan pejabat" value="{{ old('jabatan') }}" pattern=".*[a-zA-Z].*" title="Jabatan pejabat harus memuat huruf (tidak boleh hanya angka)" minlength="3" required />
                        @error('jabatan')
                        <div class="text-danger text-sm mt-1">{{ $message }}</div>
                        @enderror
                        <p class="form-hint">Contoh: Perbekel Desa, Sekretaris Desa, Kaur Umum</p>
                      </div>
                      <div class="form-group">
                        <label class="form-label">Status Pejabat</label>
                        <div class="toggle-wrapper" style="margin-top: 8px;">
                          <label class="toggle-switch">
                            <input type="checkbox" id="is_aktif" name="is_aktif" value="1" {{ old('is_aktif', '1') == '1' ? 'checked' : '' }} />
                            <span class="toggle-slider"></span>
                          </label>
                          <span class="toggle-label">Pejabat</span>
                          <span id="status-badge" class="toggle-status {{ old('is_aktif', '1') == '1' ? 'active' : 'inactive' }}">{{ old('is_aktif', '1') == '1' ? 'Aktif' : 'Tidak Aktif' }}</span>
                        </div>
                        <p class="form-hint">Pejabat tidak aktif tidak dapat digunakan untuk tanda tangan dokumen</p>
                      </div>
                      <div class="form-group form-full">
                        <label class="form-label" for="tugas">
                          Tugas / Kewenangan <span class="optional">(opsional)</span>
                        </label>
                        <textarea id="tugas" name="tugas" class="form-control @error('tugas') is-invalid @enderror" rows="4" placeholder="Deskripsi tugas dan kewenangan pejabat...">{{ old('tugas') }}</textarea>
                        @error('tugas')
                        <div class="text-danger text-sm mt-1">{{ $message }}</div>
                        @enderror
                        <p class="form-hint">Contoh: Pemegang Kekuasaan Pengelolaan Aset Desa, Pengelola Barang Milik Desa, dll.</p>
                      </div>
                      <div class="form-group form-full">
                        <label class="form-label" for="alamat">Alamat</label>
                        <textarea id="alamat" name="alamat" class="form-control @error('alamat') is-invalid @enderror" placeholder="Masukkan alamat lengkap" rows="4">{{ old('alamat') }}</textarea>
                        @error('alamat')
                        <div class="text-danger text-sm mt-1">{{ $message }}</div>
                        @enderror
                      </div>
                    </div>
                  </div>

                  <!-- Tanda Tangan Digital -->
                  <div class="form-section">
                    <div class="form-section-title">
                      Tanda Tangan Digital
                    </div>
                    <div class="form-grid">
                      <div class="form-group form-full">
                        @include('partials.signature-pad-field', [
                          'optionalNote' => 'opsional',
                          'defaultMethod' => 'draw',
                          'uploadHint' => 'Format: PNG dan JPG (Max 5MB) - untuk tanda tangan elektronik berkode',
                          'guideItems' => [
                            'Pilih <strong>Gambar Tanda Tangan</strong> untuk tanda tangan manual langsung di sistem',
                            'Pilih <strong>Upload Tanda Tangan Elektronik</strong> untuk tanda tangan berkode (PNG/JPG)',
                            'Ukuran file upload maksimal 5MB',
                            'Boleh dikosongkan jika tanda tangan belum tersedia',
                          ],
                        ])
                      </div>
                    </div>
                  </div>

                  <!-- Petugas / Pengurus Barang Milik Desa -->
                  @php $petugasBarang = \App\Models\Pejabat::where('is_petugas_barang', 1)->first(); @endphp
                  <div class="form-section">
                    <div class="form-section-title">
                      Petugas / Pengurus Barang Milik Desa
                    </div>
                    @if($petugasBarang)
                      <div class="warning-box">
                        <i data-feather="alert-triangle"></i>
                        <span>Petugas/Pengurus Barang Milik Desa saat ini adalah <strong>{{ $petugasBarang->nama }}</strong> ({{ $petugasBarang->jabatan }}). Untuk menggantinya, edit pejabat tersebut dan hapus centang terlebih dahulu.</span>
                      </div>
                    @else
                      <div class="form-group">
                        {{-- Hidden input fallback agar is_petugas_barang = 0 terkirim saat un-check --}}
                        <input type="hidden" name="is_petugas_barang" value="0" />
                        <label style="display:flex;align-items:center;gap:12px;padding:14px 16px;background:var(--color-surface);border:1.5px solid var(--color-border);border-radius:10px;cursor:pointer;transition:background-color 0.15s ease, border-color 0.15s ease;" id="petugas-barang-wrapper">
                          <input type="checkbox" id="is_petugas_barang" name="is_petugas_barang" class="sima-checkbox" value="1"
                                 {{ old('is_petugas_barang') ? 'checked' : '' }}
                                 onchange="document.getElementById('petugas-barang-wrapper').style.borderColor=this.checked?'var(--color-primary)':'var(--color-border)';document.getElementById('petugas-barang-wrapper').style.background=this.checked?'var(--color-primary-tint)':'var(--color-surface)';" />
                          <div>
                            <div style="font-weight:600;font-size:14px;color:var(--color-text);">Jadikan sebagai Petugas/Pengurus Barang Milik Desa</div>
                            <div style="font-size:12px;color:var(--color-text-muted);margin-top:2px;">Pejabat ini akan otomatis muncul sebagai penandatangan di kolom kanan pada export Kartu Inventaris Ruangan dan export Buku Inventaris (KIB).</div>
                          </div>
                        </label>
                      </div>
                    @endif
                  </div>

                  <!-- Form Actions -->
                  <div class="form-actions">
                    <a href="{{ route('pejabat.index') }}" class="btn-cancel" onclick="return confirmCancel(event, { returnKey: 'pejabat_returning', isEdit: false })">
                      <i data-feather="x"></i>
                      Batal
                    </a>
                    <button type="submit" class="btn-submit">
                      <i data-feather="save"></i>
                      Simpan Pejabat
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

    @include('partials.signature-pad-script')

    <script>
      (function(){
        try { sessionStorage.setItem('pejabat_returning', '1'); } catch(e) {}
        const pejabatForm = document.getElementById('pejabat-form');
        const isAktif = document.getElementById('is_aktif');
        const statusBadge = document.getElementById('status-badge');

        const signatureField = window.initSignaturePadField({
          defaultMethod: 'draw',
          requireSignature: false,
          requiredUploadMessage: 'Silakan upload file tanda tangan elektronik terlebih dahulu.',
          requiredDrawMessage: 'Silakan gambar tanda tangan terlebih dahulu.'
        });

        // Toggle status badge
        isAktif.addEventListener('change', function(){
          if(this.checked){
            statusBadge.textContent = 'Aktif';
            statusBadge.classList.remove('inactive');
            statusBadge.classList.add('active');
          } else {
            statusBadge.textContent = 'Tidak Aktif';
            statusBadge.classList.remove('active');
            statusBadge.classList.add('inactive');
          }
        });

        if (pejabatForm) {
          pejabatForm.addEventListener('submit', function(e) {
            e.preventDefault();

            if (typeof window.simaValidateForm === 'function') {
              if (!window.simaValidateForm(pejabatForm)) {
                return;
              }
            } else if (!pejabatForm.checkValidity()) {
              const firstInvalid = pejabatForm.querySelector(':invalid');
              if (firstInvalid && typeof window.simaScrollToField === 'function') {
                window.simaScrollToField(firstInvalid);
              }
              return;
            }

            AppModal.confirm(
              'Konfirmasi Simpan Data',
              'Apakah Anda yakin data pejabat yang dimasukkan sudah benar dan siap disimpan?',
              function() {
                signatureField.prepareForSubmit().then(function(isValid) {
                  if (isValid) {
                    pejabatForm.submit();
                  }
                });
              }
            );
          });
        }
      })();
    </script>
  </body>
</html>
