<!doctype html>
<html lang="en" data-pc-preset="{{ $pc_preset_theme }}" data-pc-theme="{{ $pc_dark_layout }}">
  <head>
    @include('layouts.head-page-meta', ['title' => 'Tambah Bidang'])
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
      .form-section-title i, .form-section-title svg {
        width: 16px;
        height: 16px;
        color: var(--color-primary);
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

      /* Info box */
      .info-box {
        background: var(--color-primary-tint);
        border: 1px solid var(--color-border);
        border-radius: 8px;
        padding: 10px 14px;
        display: flex;
        width: 100%;
        box-sizing: border-box;
        align-items: center;
        gap: 10px;
        margin-top: 14px;
      }
      .info-box i, .info-box svg {
        color: var(--color-primary);
        stroke: var(--color-primary);
        flex-shrink: 0;
        width: 16px;
        height: 16px;
      }
      .info-box-text {
        font-size: 12px;
        font-weight: 500;
        color: var(--color-text);
        line-height: 1.45;
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
        background: var(--color-primary-hover, #3A5C39);
        color: #fff;
      }
      .btn-cancel i, .btn-cancel svg {
        width: 16px;
        height: 16px;
      }
      .btn-submit i, .btn-submit svg {
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
      .card-header-icon i,
      .card-header-icon svg {
        color: #fff !important;
        stroke: #fff !important;
        width: 22px;
        height: 22px;
        position: relative !important;
        top: unset !important;
        left: unset !important;
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
        @include('layouts.breadcrumb', ['breadcrumbItemHome' => 'Master Data', 'breadcrumbItem' => 'Manajemen Bidang', 'breadcrumbItemUrl' => route('bidangs.index'), 'breadcrumbItemActive' => 'Tambah Bidang'])

        <div class="grid grid-cols-12 gap-x-6">
          <div class="col-span-12 w-full">
            <div class="card">
              <div class="card-header">
                <div class="flex items-center">
                  <div class="card-header-icon">
                    <i data-feather="briefcase"></i>
                  </div>
                  <div>
                    <h5 class="mb-0">Tambah Bidang Baru</h5>
                  </div>
                </div>
              </div>

              <div class="card-body pt-4">
                <form id="bidang-form" action="{{ route('bidangs.store') }}" method="POST" onsubmit="return confirmSimpan(event)" novalidate>
                  @csrf
                  
                  <!-- Informasi Bidang -->
                  <div class="form-section">
                    <div class="form-section-title">
                      Informasi Bidang
                    
                    </div>
                    <div class="form-grid">
                      <div class="form-group form-full">
                        <label class="form-label" for="nama">
                          Nama Bidang <span class="required">*</span>
                        </label>
                        <input 
                          type="text" 
                          id="nama" 
                          name="nama" 
                          class="form-control @error('nama') is-invalid @enderror" 
                          placeholder="Masukkan nama bidang" 
                          value="{{ old('nama') }}" 
                          pattern=".*[a-zA-Z].*"
                          title="Nama bidang harus memuat huruf (tidak boleh hanya angka)"
                          minlength="3"
                          required 
                        />
                        @error('nama')
                        <div class="text-danger" style="font-size:12px;margin-top:4px;">{{ $message }}</div>
                        @enderror
                        <p class="form-hint">Contoh: Keuangan, Umum, Pembangunan, Kesejahteraan</p>
                      </div>
                      
                      <div class="form-group form-full">
                        <label class="form-label" for="deskripsi">
                          Deskripsi <span class="optional">(opsional)</span>
                        </label>
                        <textarea 
                          id="deskripsi" 
                          name="deskripsi" 
                          class="form-control @error('deskripsi') is-invalid @enderror" 
                          rows="4" 
                          placeholder="Masukkan deskripsi bidang...">{{ old('deskripsi') }}</textarea>
                        @error('deskripsi')
                        <div class="text-danger" style="font-size:12px;margin-top:4px;">{{ $message }}</div>
                        @enderror
                        <p class="form-hint">Deskripsi singkat tentang tugas dan fungsi bidang ini</p>
                      </div>
                    </div>

                    <div class="info-box">
                      <i data-feather="info"></i>
                      <div class="info-box-text">
                        <strong>Catatan:</strong> Bidang yang sudah dibuat dapat digunakan untuk mengorganisir user dengan role Kaur, Kasi, atau Staf.
                      </div>
                    </div>
                  </div>

                  <!-- Form Actions -->
                  <div class="form-actions">
                    <a href="{{ route('bidangs.index') }}" class="btn-cancel" onclick="return confirmCancel(event, { returnKey: 'bidang_returning', isEdit: false })">
                      <i data-feather="x"></i>
                      Batal
                    </a>
                    <button type="submit" class="btn-submit">
                      <i data-feather="save"></i>
                      Simpan Bidang
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
      // Confirmation before submit
      function confirmSimpan(e) {
        e.preventDefault();
        const form = document.getElementById('bidang-form');
        if (typeof window.simaValidateForm === 'function') {
          if (!window.simaValidateForm(form)) return false;
        } else if (!form.checkValidity()) {
          const firstInvalid = form.querySelector(':invalid');
          if (firstInvalid) {
            firstInvalid.scrollIntoView({ behavior: 'smooth', block: 'center' });
            try { firstInvalid.focus(); } catch(err) {}
          }
          return false;
        }
        const nama = document.getElementById('nama').value.trim();
        AppModal.confirm('Simpan Bidang', 'Apakah Anda yakin ingin menyimpan bidang <strong>' + nama + '</strong>?',
            function() { form.submit(); });
        return false;
      }

      // Initialize feather icons
      if (typeof feather !== 'undefined') {
        feather.replace();
      }
      try { sessionStorage.setItem('bidang_returning', '1'); } catch(e) {}
    </script>
  </body>
</html>
