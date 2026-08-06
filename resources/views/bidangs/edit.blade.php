<!doctype html>
<html lang="en" data-pc-preset="{{ $pc_preset_theme }}" data-pc-theme="{{ $pc_dark_layout }}">
  <head>
    @include('layouts.head-page-meta', ['title' => 'Edit Bidang'])
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
      .form-control, .form-select, textarea.form-control {
        width: 100%;
        padding: 10px 14px;
        border: 1px solid #d1d5db;
        border-radius: 8px;
        font-size: 14px;
        transition: all 0.2s ease;
        background: #fff;
      }
      .form-control:focus, .form-select:focus, textarea.form-control:focus {
        outline: none;
        border-color: #064635;
        box-shadow: 0 0 0 3px rgba(6, 70, 53, 0.1);
      }
      .form-control::placeholder, textarea.form-control::placeholder {
        color: #9ca3af;
      }
      .form-control.is-invalid, textarea.form-control.is-invalid {
        border-color: #ef4444;
      }
      .text-danger {
        color: #ef4444;
        font-size: 12px;
        margin-top: 4px;
      }
      .form-hint {
        font-size: 12px;
        color: #6b7280;
        margin-top: 4px;
      }
      .form-full {
        grid-column: 1 / -1;
      }

      /* Warning box */
      .warning-box {
        background: linear-gradient(135deg, #fef3c7 0%, #fde68a 100%);
        border: 1px solid #fbbf24;
        border-radius: 8px;
        padding: 12px 16px;
        display: flex;
        align-items: center;
        gap: 10px;
        margin-bottom: 16px;
      }
      .warning-box i,
      .warning-box svg {
        color: #d97706 !important;
        stroke: #d97706 !important;
        flex-shrink: 0;
        width: 18px;
        height: 18px;
      }
      .warning-box-text {
        font-size: 13px;
        color: #92400e;
        line-height: 1.5;
      }

      /* Info box */
      .info-box {
        background: linear-gradient(135deg, #dbeafe 0%, #bfdbfe 100%);
        border: 1px solid #60a5fa;
        border-radius: 8px;
        padding: 12px 16px;
        display: flex;
        align-items: center;
        gap: 10px;
        margin-top: 12px;
      }
      .info-box i {
        color: #1e3a8a;
        stroke: #1e3a8a;
        flex-shrink: 0;
        width: 18px;
        height: 18px;
      }
      .info-box-text {
        font-size: 13px;
        color: #1e3a8a;
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
        width: 40px;
        height: 40px;
        border-radius: 10px;
        background: #519259;
        margin-right: 12px;
        flex-shrink: 0;
      }
      .card-header-icon i,
      .card-header-icon svg {
        color: #fff !important;
        stroke: #fff !important;
        width: 20px;
        height: 20px;
      }

      @media (max-width: 768px) {
        .form-grid {
          grid-template-columns: 1fr;
        }
      }
    </style>
  </head>

  <body>
    @include('layouts.notification')
    @include('layouts.modals')
    @include('layouts.layout-vertical')

    <div class="pc-container">
      <div class="pc-content">
        @include('layouts.breadcrumb', ['breadcrumbItemHome' => 'Master Data', 'breadcrumbItem' => 'Bidang', 'breadcrumbItemUrl' => route('bidangs.index'), 'breadcrumbItemActive' => 'Edit Bidang'])

        <div class="grid grid-cols-12 gap-x-6">
          <div class="col-span-12 lg:col-span-8 lg:col-start-3">
            <div class="card">
              <div class="card-header border-b border-gray-100">
                <div class="flex items-center">
                  <div class="card-header-icon">
                    <i data-feather="edit-2"></i>
                  </div>
                  <div>
                    <h5 class="mb-1">Edit Bidang</h5>
                    <p class="text-muted text-sm mb-0">Perbarui informasi data bidang</p>
                  </div>
                </div>
              </div>

              <div class="card-body pt-4">
                @if($bidang->users_count > 0)
                  <div class="warning-box">
                    <i data-feather="alert-triangle"></i>
                    <div class="warning-box-text">
                      <strong>Perhatian:</strong> Bidang ini memiliki <strong>{{ $bidang->users_count }} user</strong> yang terkait. Perubahan nama bidang akan mempengaruhi data user tersebut.
                    </div>
                  </div>
                @endif

                <form id="bidang-form" action="{{ route('bidangs.update', $bidang->id) }}" method="POST" onsubmit="return confirmUpdate(event)">
                  @csrf
                  @method('PUT')
                  
                  <!-- Informasi Bidang -->
                  <div class="form-section">
                    <div class="form-section-title">
                      <i data-feather="folder"></i>
                      Informasi Bidang
                    </div>
                    <div class="form-grid">
                      <div class="form-group">
                        <label class="form-label" for="nama">
                          Nama Bidang <span class="required">*</span>
                        </label>
                        <input 
                          type="text" 
                          id="nama" 
                          name="nama" 
                          class="form-control @error('nama') is-invalid @enderror" 
                          placeholder="Masukkan nama bidang" 
                          value="{{ old('nama', $bidang->nama) }}" 
                          required 
                        />
                        @error('nama')
                        <div class="text-danger">{{ $message }}</div>
                        @enderror
                        <p class="form-hint">Contoh: Keuangan, Umum, Pembangunan, Kesejahteraan</p>
                      </div>
                      
                      <div class="form-group">
                        <label class="form-label" for="deskripsi">
                          Deskripsi <span class="optional">(opsional)</span>
                        </label>
                        <textarea 
                          id="deskripsi" 
                          name="deskripsi" 
                          class="form-control @error('deskripsi') is-invalid @enderror" 
                          rows="4" 
                          placeholder="Masukkan deskripsi bidang...">{{ old('deskripsi', $bidang->deskripsi) }}</textarea>
                        @error('deskripsi')
                        <div class="text-danger">{{ $message }}</div>
                        @enderror
                        <p class="form-hint">Deskripsi singkat tentang tugas dan fungsi bidang ini</p>
                      </div>
                    </div>

                    <div class="info-box">
                      <i data-feather="info" style="color:#1e40af;stroke:#1e40af;"></i>
                      <div class="info-box-text">
                        <strong>Informasi:</strong> Bidang ini saat ini digunakan oleh <strong>{{ $bidang->users_count }} user</strong>.
                      </div>
                    </div>
                  </div>

                  <!-- Form Actions -->
                  <div class="form-actions">
                    <a href="{{ route('bidangs.index') }}" class="btn-cancel" onclick="return confirm('Apakah Anda yakin ingin membatalkan? Data yang sudah diisi tidak akan tersimpan.');">
                      <i data-feather="x"></i>
                      Batal
                    </a>
                    <button type="submit" class="btn-submit">
                      <i data-feather="save"></i>
                      Update Bidang
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
      // Confirmation before update
      function confirmUpdate(e) {
        e.preventDefault();
        const nama = document.getElementById('nama').value.trim();
        AppModal.confirm('Simpan Perubahan', 'Apakah Anda yakin ingin menyimpan perubahan data bidang <strong>' + nama + '</strong>?',
            function() { document.getElementById('bidang-form').submit(); });
        return false;
      }

      // Initialize feather icons
      if (typeof feather !== 'undefined') {
        feather.replace();
      }
    </script>
  </body>
</html>
