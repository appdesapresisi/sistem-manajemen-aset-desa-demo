<!doctype html>
<html lang="en" data-pc-preset="{{ $pc_preset_theme }}" data-pc-theme="{{ $pc_dark_layout }}">
  <head>
    @include('layouts.head-page-meta', ['title' => 'Tambah Pejabat'])
    @include('layouts.head-css')
    <style>
      /* Form Styling */
      .form-section {
        background: #f8f9fa;
        border-radius: 12px;
        padding: 20px;
        margin-bottom: 16px;
      }
      .form-section-title {
        font-size: 14px;
        font-weight: 600;
        color: #064635;
        margin-bottom: 16px;
        display: flex;
        align-items: center;
        gap: 8px;
      }
      .form-section-title i {
        width: 18px;
        height: 18px;
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
        font-weight: 500;
        font-size: 13px;
        color: #374151;
        margin-bottom: 6px;
        display: block;
      }
      .form-label .required {
        color: #ef4444;
        margin-left: 2px;
      }
      .form-label .optional {
        color: #9ca3af;
        font-weight: 400;
        font-size: 11px;
        margin-left: 4px;
      }
      .form-control, .form-select {
        width: 100%;
        padding: 10px 14px;
        border: 1px solid #d1d5db;
        border-radius: 8px;
        font-size: 14px;
        transition: all 0.2s ease;
        background: #fff;
      }
      .form-control:focus, .form-select:focus {
        outline: none;
        border-color: #064635;
        box-shadow: 0 0 0 3px rgba(6, 70, 53, 0.1);
      }
      .form-control::placeholder {
        color: #9ca3af;
      }
      .form-control.is-invalid,
      .form-select.is-invalid {
        border-color: #ef4444;
      }
      .form-control.is-invalid:focus,
      .form-select.is-invalid:focus {
        border-color: #ef4444;
        box-shadow: 0 0 0 3px rgba(239, 68, 68, 0.1);
      }
      .text-danger {
        color: #ef4444;
      }
      .form-hint {
        font-size: 12px;
        color: #6b7280;
        margin-top: 4px;
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
        box-shadow: 0 2px 4px rgba(0,0,0,0.1);
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
        border-color: #064635;
        background: #f4faf5;
      }
      .file-upload-area.has-file {
        border-color: #22c55e;
        background: #f0fdf4;
      }
      .file-upload-icon {
        width: 48px;
        height: 48px;
        border-radius: 12px;
        background: linear-gradient(135deg, #064635 0%, #519259 100%);
        display: flex;
        align-items: center;
        justify-content: center;
        color: #fff;
      }
      .file-upload-icon i,
      .file-upload-icon svg {
        width: 24px;
        height: 24px;
        color: #fff !important;
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
        background: linear-gradient(135deg, #eff6ff 0%, #dbeafe 100%);
        border: 1px solid #93c5fd;
        border-radius: 8px;
        padding: 12px 16px;
        display: flex;
        align-items: center;
        gap: 10px;
        margin-top: 16px;
      }
      .info-box i {
        color: #1e40af !important;
        stroke: #1e40af !important;
        flex-shrink: 0;
        width: 18px;
        height: 18px;
      }
      .info-box-text {
        font-size: 13px;
        color: #1e40af;
        line-height: 1.5;
      }

      /* Buttons */
      .form-actions {
        display: flex;
        gap: 12px;
        justify-content: flex-end;
        padding-top: 20px;
        border-top: 1px solid #e5e7eb;
        margin-top: 24px;
      }
      .btn-cancel {
        padding: 10px 20px;
        border: 1px solid #d1d5db;
        background: #fff;
        color: #374151;
        border-radius: 8px;
        font-weight: 500;
        cursor: pointer;
        transition: all 0.2s;
        text-decoration: none;
        display: inline-flex;
        align-items: center;
        gap: 8px;
      }
      .btn-cancel:hover {
        background: #f3f4f6;
      }
      .btn-submit {
        padding: 10px 24px;
        border: none;
        background: #064635;
        color: #fff;
        border-radius: 8px;
        font-weight: 500;
        cursor: pointer;
        transition: all 0.2s;
        display: flex;
        align-items: center;
        gap: 8px;
      }
      .btn-submit:hover {
        background: #1c5240;
      }
      .btn-submit i {
        width: 18px;
        height: 18px;
      }

      /* Card Header */
      .card-header-icon {
        display: flex;
        align-items: center;
        justify-content: center;
        width: 44px;
        height: 44px;
        min-width: 44px;
        border-radius: 10px;
        background: #519259;
        margin-right: 12px;
        position: relative !important;
        flex-shrink: 0;
      }
      .card-header-icon svg,
      .card-header-icon i {
        color: #fff !important;
        stroke: #fff !important;
        width: 20px;
        height: 20px;
        position: relative !important;
        top: auto !important;
        left: auto !important;
      }

      /* Signature preview styling */
      .signature-guide {
        margin-top: 12px;
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
        @include('layouts.breadcrumb', ['breadcrumbItemHome' => 'Master Data', 'breadcrumbItem' => 'Pejabat', 'breadcrumbItemUrl' => route('pejabat.index'), 'breadcrumbItemActive' => 'Tambah Pejabat'])
        <div class="grid grid-cols-12 gap-x-6">
          <div class="col-span-12 lg:col-span-8 lg:col-start-3">
            <div class="card">
              <div class="card-header border-b border-gray-100">
                <div class="flex items-center">
                  <div class="card-header-icon">
                    <i data-feather="user-check"></i>
                  </div>
                  <div>
                    <h5 class="mb-1">Tambah Pejabat Baru</h5>
                    <p class="text-muted text-sm mb-0">Isi data untuk menambahkan pejabat yang berwenang menandatangani dokumen</p>
                  </div>
                </div>
              </div>

              <div class="card-body pt-4">
                <form id="pejabat-form" method="POST" action="{{ route('pejabat.store') }}" enctype="multipart/form-data">
                  @csrf

                  <div class="info-box" style="margin-bottom:16px;">
                    <i data-feather="info" style="color:#1e40af !important;stroke:#1e40af !important;width:18px;height:18px;flex-shrink:0;"></i>
                    <div class="info-box-text">
                      <strong>Catatan:</strong> Data pejabat ini akan digunakan pada fitur Export Data untuk menandatangani dokumen resmi seperti Buku Inventaris Aset Desa, Laporan Inventaris, dan dokumen lainnya.
                    </div>
                  </div>
                  
                  <!-- Informasi Pejabat -->
                  <div class="form-section">
                    <div class="form-section-title">
                      <i data-feather="user"></i>
                      Informasi Pejabat
                    </div>
                    <div class="form-grid">
                      <div class="form-group form-full">
                        <label class="form-label" for="nama">
                          Nama Lengkap <span class="required">*</span>
                        </label>
                        <input type="text" id="nama" name="nama" class="form-control @error('nama') is-invalid @enderror" placeholder="Masukkan nama lengkap dengan gelar" value="{{ old('nama') }}" required />
                        @error('nama')
                        <div class="text-danger text-sm mt-1">{{ $message }}</div>
                        @enderror
                        <p class="form-hint">Contoh: I Nyoman Suwarjana, SE atau Drs. Ahmad Sudrajat, M.Si</p>
                      </div>
                      <div class="form-group">
                        <label class="form-label" for="jabatan">
                          Jabatan <span class="required">*</span>
                        </label>
                        <input type="text" id="jabatan" name="jabatan" class="form-control @error('jabatan') is-invalid @enderror" placeholder="Masukkan jabatan pejabat" value="{{ old('jabatan') }}" required />
                        @error('jabatan')
                        <div class="text-danger text-sm mt-1">{{ $message }}</div>
                        @enderror
                        <p class="form-hint">Contoh: Perbekel Mengwi, Sekretaris Desa, Kaur Umum</p>
                      </div>
                      <div class="form-group">
                        <label class="form-label" for="tugas">
                          Tugas / Kewenangan <span class="optional">(opsional)</span>
                        </label>
                        <textarea id="tugas" name="tugas" class="form-control @error('tugas') is-invalid @enderror" rows="3" placeholder="Deskripsi tugas dan kewenangan pejabat...">{{ old('tugas') }}</textarea>
                        @error('tugas')
                        <div class="text-danger text-sm mt-1">{{ $message }}</div>
                        @enderror
                        <p class="form-hint">Contoh: Pemegang Kekuasaan Pengelolaan Aset Desa, Pengelola Barang Milik Desa, dll.</p>
                      </div>
                      <div class="form-group form-full">
                        <label class="form-label" for="alamat">Alamat</label>
                        <textarea id="alamat" name="alamat" class="form-control @error('alamat') is-invalid @enderror" placeholder="Masukkan alamat lengkap" rows="2">{{ old('alamat') }}</textarea>
                        @error('alamat')
                        <div class="text-danger text-sm mt-1">{{ $message }}</div>
                        @enderror
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
                    </div>
                  </div>

                  <!-- Tanda Tangan Digital -->
                  <div class="form-section">
                    <div class="form-section-title">
                      <i data-feather="edit-3"></i>
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
                      <i data-feather="shield"></i>
                      Petugas / Pengurus Barang Milik Desa
                    </div>
                    @if($petugasBarang)
                      <div style="background:#fef3c7;border:1px solid #fcd34d;border-radius:8px;padding:12px 16px;display:flex;align-items:center;gap:10px;">
                        <i data-feather="alert-triangle" style="width:16px;height:16px;color:#d97706;flex-shrink:0;"></i>
                        <div style="font-size:13px;color:#92400e;line-height:1.5;">
                          Petugas/Pengurus Barang Milik Desa saat ini adalah <strong>{{ $petugasBarang->nama }}</strong> ({{ $petugasBarang->jabatan }}).
                          Untuk menggantinya, edit pejabat tersebut dan hapus centang terlebih dahulu.
                        </div>
                      </div>
                    @else
                      <div class="form-group">
                        {{-- Hidden input fallback agar is_petugas_barang = 0 terkirim saat un-check --}}
                        <input type="hidden" name="is_petugas_barang" value="0" />
                        <label style="display:flex;align-items:flex-start;gap:10px;padding:14px 16px;background:#fff;border:2px solid #e2e8f0;border-radius:10px;cursor:pointer;transition:all 0.2s;" id="petugas-barang-wrapper">
                          <input type="checkbox" id="is_petugas_barang" name="is_petugas_barang" value="1"
                                 {{ old('is_petugas_barang') ? 'checked' : '' }}
                                 style="width:18px;height:18px;accent-color:#064635;cursor:pointer;flex-shrink:0;margin-top:2px;"
                                 onchange="document.getElementById('petugas-barang-wrapper').style.borderColor=this.checked?'#064635':'#e2e8f0';" />
                          <div>
                            <div style="font-weight:600;font-size:14px;color:#374151;">Jadikan sebagai Petugas/Pengurus Barang Milik Desa</div>
                            <div style="font-size:12px;color:#6b7280;margin-top:2px;">Pejabat ini akan otomatis muncul sebagai penandatangan di kolom kanan pada export Kartu Inventaris Ruangan dan export Buku Inventaris (KIB).</div>
                          </div>
                        </label>
                      </div>
                    @endif
                  </div>

                  <!-- Form Actions -->
                  <div class="form-actions">
                    <a href="{{ route('pejabat.index') }}" class="btn-cancel" onclick="return confirm('Apakah Anda yakin ingin membatalkan? Data yang sudah diisi tidak akan tersimpan.');">
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

            if (!pejabatForm.checkValidity()) {
              pejabatForm.reportValidity();
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
