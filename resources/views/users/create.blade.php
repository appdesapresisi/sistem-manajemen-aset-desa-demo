<!doctype html>
<html lang="en" data-pc-preset="{{ $pc_preset_theme }}" data-pc-theme="{{ $pc_dark_layout }}">
  <head>
    @include('layouts.head-page-meta', ['title' => 'Tambah User'])
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
      .form-select option[value="add-new"] {
        color: var(--color-primary);
        font-weight: 600;
      }
      .form-control:focus, textarea.form-control:focus {
        outline: none !important;
        border-color: var(--color-primary) !important;
        box-shadow: none !important;
      }
      .form-control.is-invalid, textarea.form-control.is-invalid, .form-select.is-invalid {
        border-color: #E53935 !important;
        box-shadow: none !important;
      }
      .error-text, .invalid-feedback-custom {
        display: flex;
        align-items: flex-start;
        gap: 6px;
        font-size: 12.5px;
        font-weight: 500;
        color: #D9383A;
        margin-top: 6px;
        line-height: 1.4;
      }
      .error-text svg, .invalid-feedback-custom svg {
        width: 13px;
        height: 13px;
        flex-shrink: 0;
        margin-top: 2px;
      }
      .form-control::placeholder, textarea.form-control::placeholder {
        color: var(--color-text-muted);
        opacity: 0.65;
      }
      /* Neutralize Browser Autofill Blue Tint */
      input:-webkit-autofill,
      input:-webkit-autofill:hover, 
      input:-webkit-autofill:focus,
      input:-webkit-autofill:active,
      textarea:-webkit-autofill,
      select:-webkit-autofill {
        -webkit-box-shadow: 0 0 0 1000px var(--color-surface, #f8faf7) inset !important;
        -webkit-text-fill-color: var(--color-text, #1E231C) !important;
        box-shadow: 0 0 0 1000px var(--color-surface, #f8faf7) inset !important;
        transition: background-color 5000s ease-in-out 0s !important;
        background-color: var(--color-surface, #f8faf7) !important;
        color: var(--color-text, #1E231C) !important;
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
        color: var(--color-text);
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

      /* Checkbox styled */
      .checkbox-wrapper {
        display: flex;
        align-items: center;
        gap: 12px;
        padding: 14px 16px;
        background: var(--color-surface);
        border: 1.5px solid var(--color-border);
        border-radius: 10px;
        cursor: pointer;
        transition: all 0.2s;
      }
      .checkbox-wrapper:hover {
        border-color: var(--color-primary);
        background: var(--color-row-hover);
      }
      .checkbox-wrapper.checked {
        border-color: var(--color-primary);
        background: var(--color-row-hover);
      }
      .checkbox-wrapper input[type="checkbox"] {
        width: 18px;
        height: 18px;
        accent-color: var(--color-primary);
        cursor: pointer;
        flex-shrink: 0;
        margin: 0;
      }
      .checkbox-content {
        flex: 1;
      }
      .checkbox-title {
        font-weight: 600;
        font-size: 14px;
        color: var(--color-text);
        margin-bottom: 2px;
      }
      .checkbox-desc {
        font-size: 12px;
        color: var(--color-text-muted);
        line-height: 1.4;
      }

      /* Pejabat Section */
      .pejabat-section {
        display: none;
        margin-top: 16px;
        padding: 20px;
        background: var(--color-surface);
        border: 1.5px solid var(--color-border);
        border-radius: var(--radius-md);
      }
      .pejabat-section.show {
        display: block;
        animation: slideDown 0.3s ease;
      }
      @keyframes slideDown {
        from { opacity: 0; transform: translateY(-10px); }
        to { opacity: 1; transform: translateY(0); }
      }
      .pejabat-section-title {
        font-size: 14px;
        font-weight: 600;
        color: var(--color-primary);
        margin-bottom: 16px;
        display: flex;
        align-items: center;
        gap: 8px;
      }
      .pejabat-section-title i {
        width: 18px;
        height: 18px;
        color: var(--color-primary);
      }

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
        margin-top: 12px;
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
        font-size: 12px;
        color: var(--color-text);
        line-height: 1.45;
        font-weight: 500;
        flex: 1;
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
        background: var(--color-surface);
        border: 2px dashed var(--color-border);
        border-radius: 12px;
        cursor: pointer;
        transition: all 0.2s;
        text-align: center;
      }
      .file-upload-area:hover {
        border-color: var(--color-primary);
        background: var(--color-row-hover);
      }
      .file-upload-area.has-file {
        border-color: var(--color-primary);
        background: var(--color-row-hover);
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
        color: var(--color-text);
        font-weight: 500;
      }
      .file-upload-hint {
        font-size: 12px;
        color: var(--color-text-muted);
      }
      .file-preview {
        display: none;
        margin-top: 12px;
        padding: 12px;
        background: var(--color-row-hover);
        border: 1px solid var(--color-border);
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
        background: var(--color-surface);
        border: 1px solid var(--color-border);
        padding: 4px;
      }
      .file-preview-info {
        flex: 1;
      }
      .file-preview-name {
        font-size: 13px;
        font-weight: 500;
        color: var(--color-text);
        margin-bottom: 2px;
      }
      .file-preview-size {
        font-size: 11px;
        color: var(--color-text-muted);
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
        flex-shrink: 0;
        position: relative !important;
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

      /* Password visibility toggle */
      .password-wrapper {
        position: relative;
      }
      .password-toggle {
        position: absolute;
        right: 12px;
        top: 50%;
        transform: translateY(-50%);
        background: none;
        border: none;
        cursor: pointer;
        color: var(--color-text-muted);
        padding: 0;
        display: flex;
        align-items: center;
        justify-content: center;
      }
      .password-toggle:hover {
        color: var(--color-text);
      }
      .password-toggle i {
        width: 18px;
        height: 18px;
      }
      .password-wrapper .form-control {
        padding-right: 44px;
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
        @include('layouts.breadcrumb', ['breadcrumbItemHome' => 'Master Data', 'breadcrumbItem' => 'Manajemen Pengguna', 'breadcrumbItemUrl' => route('users.index'), 'breadcrumbItemActive' => 'Tambah Pengguna'])

        <div class="grid grid-cols-12 gap-x-6">
          <div class="col-span-12 w-full">
            <div class="card">
              <div class="card-header">
                <div class="flex items-center">
                  <div class="card-header-icon">
                    <i data-feather="users"></i>
                  </div>
                  <div>
                    <h5 class="mb-0">Tambah User Baru</h5>
                  </div>
                </div>
              </div>

              <div class="card-body pt-4">
                <form id="user-form" action="{{ route('users.store') }}" method="POST" enctype="multipart/form-data" autocomplete="off" novalidate>
                  @csrf
                  <!-- Hidden field untuk is_aktif=0 jika checkbox unchecked -->
                  <input type="hidden" name="is_aktif" value="0" />
                  
                  <!-- Informasi Akun -->
                  <div class="form-section">
                    <div class="form-section-title">
                      Informasi Akun
                    </div>
                    <div class="form-grid">
                      <div class="form-group">
                        <label class="form-label" for="nama">
                          Nama Lengkap <span class="required">*</span>
                        </label>
                        <input type="text" id="nama" name="nama" class="form-control @error('nama') is-invalid @enderror" placeholder="Masukkan nama lengkap" value="{{ old('nama') }}" pattern="^[a-zA-Z\s\.,'\-]+$" title="Nama lengkap hanya boleh berisi huruf dan tanda baca nama/gelar (tidak boleh angka)" minlength="3" required />
                        @error('nama')
                        <div class="text-danger" style="font-size:12px;margin-top:4px;">{{ $message }}</div>
                        @enderror
                      </div>
                      <div class="form-group">
                        <label class="form-label" for="username">
                          Username <span class="required">*</span>
                        </label>
                        <input type="text" id="username" name="username" class="form-control @error('username') is-invalid @enderror" placeholder="contoh: budi.santoso" value="{{ old('username') }}" autocomplete="off" required />
                        @error('username')
                        <div class="invalid-feedback-custom">
                          <svg width="13" height="13" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><path d="m21.73 18-8-14a2 2 0 0 0-3.48 0l-8 14A2 2 0 0 0 4 21h16a2 2 0 0 0 1.73-3Z"></path><line x1="12" y1="9" x2="12" y2="13"></line><line x1="12" y1="17" x2="12.01" y2="17"></line></svg>
                          <span>{{ $message }}</span>
                        </div>
                        @enderror
                        <div id="username-live-error" class="invalid-feedback-custom" style="display:none;"></div>
                        <p class="form-hint">Minimal 3 karakter, diawali huruf. Boleh mengandung angka, titik, strip, atau underscore</p>
                      </div>
                      <div class="form-group form-full">
                        <label class="form-label" for="alamat">Alamat <span class="required">*</span></label>
                        <textarea id="alamat" name="alamat" class="form-control @error('alamat') is-invalid @enderror" placeholder="Masukkan alamat lengkap" rows="4" required>{{ old('alamat') }}</textarea>
                        @error('alamat')
                        <div class="text-danger" style="font-size:12px;margin-top:4px;">{{ $message }}</div>
                        @enderror
                      </div>
                      <div class="form-group">
                        <label class="form-label" for="password">
                          Password <span class="required">*</span>
                        </label>
                        <div class="password-wrapper">
                          <input type="password" id="password" name="password" class="form-control @error('password') is-invalid @enderror" placeholder="Masukkan Password" autocomplete="new-password" required minlength="8" />
                          <button type="button" class="password-toggle" onclick="togglePassword('password', this)">
                            <i data-feather="eye"></i>
                          </button>
                        </div>
                        @error('password')
                        <div class="text-danger" style="font-size:12px;margin-top:4px;">{{ $message }}</div>
                        @enderror
                        <p class="form-hint">Minimal 8 karakter. Disarankan menggunakan kombinasi huruf besar, huruf kecil, dan angka</p>
                      </div>
                      <div class="form-group">
                        <label class="form-label" for="password_confirmation">
                          Konfirmasi Password <span class="required">*</span>
                        </label>
                        <div class="password-wrapper">
                          <input type="password" id="password_confirmation" name="password_confirmation" class="form-control" placeholder="Ulangi password" autocomplete="new-password" required />
                          <button type="button" class="password-toggle" onclick="togglePassword('password_confirmation', this)">
                            <i data-feather="eye"></i>
                          </button>
                        </div>
                      </div>
                    </div>
                  </div>

                  <!-- Role & Akses -->
                  <div class="form-section">
                    <div class="form-section-title">
                      Role & Hak Akses
                    </div>
                    <div class="form-grid">
                      <div class="form-group">
                        <label class="form-label" for="role">
                          Role <span class="required">*</span>
                        </label>
                        <select id="role" name="role" class="form-select @error('role') is-invalid @enderror" required>
                          <option value="" disabled hidden selected>Pilih Role</option>
                          @foreach($roles as $roleItem)
                            @php
                              $isSuperAdmin = $roleItem->nama === 'super_admin';
                              $isPerbekel   = $roleItem->nama === 'perbekel';
                              $isSekretaris = str_contains(strtolower($roleItem->nama), 'sekretaris');
                              $skip = ($isSuperAdmin && $superAdminExists) || ($isPerbekel && $perbekelExists) || ($isSekretaris && $sekretarisExists);
                            @endphp
                            @if(!$skip)
                              <option value="{{ $roleItem->nama }}" {{ old('role') == $roleItem->nama ? 'selected' : '' }}>{{ ucwords(str_replace('_', ' ', $roleItem->nama)) }}</option>
                            @endif
                          @endforeach
                        </select>
                        <div id="perbekel-hint" style="display:none; margin-top:6px; padding:8px 12px; background:#fff3cd; border:1px solid #ffc107; border-radius:6px; font-size:0.82rem; color:#856404;">
                            <i data-feather="alert-triangle" style="width:14px;height:14px;display:inline;vertical-align:middle;margin-right:4px;"></i>
                            @if($perbekelExists)
                                Sudah ada user dengan role Perbekel. Tidak dapat menambah lebih dari 1 Perbekel.
                            @else
                                Role Perbekel hanya boleh dimiliki oleh 1 user.
                            @endif
                        </div>
                        <div id="super-admin-hint" style="display:none; margin-top:6px; padding:8px 12px; background:#fff3cd; border:1px solid #ffc107; border-radius:6px; font-size:0.82rem; color:#856404;">
                            <i data-feather="alert-triangle" style="width:14px;height:14px;display:inline;vertical-align:middle;margin-right:4px;"></i>
                            @if($superAdminExists)
                                Sudah ada Super Admin. Hanya 1 Super Admin yang diperbolehkan.
                            @else
                                Role Super Admin hanya boleh dimiliki oleh 1 user.
                            @endif
                        </div>
                        <div id="sekretaris-hint" style="display:none; margin-top:6px; padding:8px 12px; background:#fff3cd; border:1px solid #ffc107; border-radius:6px; font-size:0.82rem; color:#856404;">
                          <i data-feather="alert-triangle" style="width:14px;height:14px;display:inline;vertical-align:middle;margin-right:4px;"></i>
                          @if($sekretarisExists)
                            Sudah ada user dengan role Sekretaris Desa. Tidak dapat menambah lebih dari 1 Sekretaris Desa.
                          @else
                            Role Sekretaris Desa hanya boleh dimiliki oleh 1 user.
                          @endif
                        </div>
                      </div>
                      <div class="form-group" id="bidang-section" style="display:none;">
                        <label class="form-label" for="bidang_id">
                          Bidang <span class="required">*</span>
                        </label>
                        <select id="bidang_id" name="bidang_id" class="form-select @error('bidang_id') is-invalid @enderror">
                          <option value="" disabled hidden selected>Pilih Bidang</option>
                          @foreach($bidangs as $bidangItem)
                            <option value="{{ $bidangItem->id }}" {{ old('bidang_id') == $bidangItem->id ? 'selected' : '' }}>
                              {{ $bidangItem->nama }}
                            </option>
                          @endforeach
                        </select>
                        @error('bidang_id')
                          <div class="invalid-feedback" style="display: block;">{{ $message }}</div>
                        @enderror
                      </div>
                      <div class="form-group" id="dinas-section" style="display:none;">
                        <label class="form-label" for="dinas_id">
                          Dinas <span class="required">*</span>
                        </label>
                        <select id="dinas_id" name="dinas_id" class="form-select @error('dinas_id') is-invalid @enderror">
                          <option value="" disabled hidden selected>Pilih Dinas</option>
                          @foreach($dinas as $dinasItem)
                            <option value="{{ $dinasItem->id }}" {{ old('dinas_id') == $dinasItem->id ? 'selected' : '' }}>
                              {{ $dinasItem->nama }}
                            </option>
                          @endforeach
                        </select>
                        @error('dinas_id')
                          <div class="invalid-feedback" style="display: block;">{{ $message }}</div>
                        @enderror
                      </div>
                      <div class="form-group form-full">
                        <label class="form-label">Status Akun</label>
                        <div class="toggle-wrapper" style="margin-top: 8px;">
                          <label class="toggle-switch">
                            <input type="checkbox" id="is_aktif" name="is_aktif" value="1" {{ old('is_aktif', '1') == '1' ? 'checked' : '' }} />
                            <span class="toggle-slider"></span>
                          </label>
                          <span class="toggle-label">User</span>
                          <span id="status-badge" class="toggle-status active">Aktif</span>
                        </div>
                        <p class="form-hint">User tidak aktif tidak dapat login ke sistem</p>
                      </div>
                    </div>
                  </div>

                  <!-- Pejabat TTD Option -->
                  <div class="form-section form-section-last">
                    <div class="form-section-title">
                      Pejabat Tanda Tangan
                    </div>
                    
                    <label class="checkbox-wrapper" id="pejabat-checkbox-wrapper">
                      <input type="checkbox" id="create_pejabat" name="create_pejabat" class="sima-checkbox" value="1" {{ old('create_pejabat') ? 'checked' : '' }} />
                      <div class="checkbox-content">
                        <div class="checkbox-title">User ini adalah Pejabat yang perlu Tanda Tangan</div>
                        <div class="checkbox-desc">Centang jika user ini perlu menandatangani dokumen pada Export Data manajemen aset. Data pejabat akan otomatis dibuat bersamaan dengan user.</div>
                      </div>
                    </label>

                    <div class="info-box" style="margin-top:12px;">
                      <i data-feather="info"></i>
                      <div class="info-box-text">
                        <strong>Catatan:</strong> Jika user adalah pejabat, maka data pejabat dan user akan dibuat bersamaan.
                      </div>
                    </div>

                    <!-- Form Pejabat (muncul jika checkbox dicentang) -->
                    <div class="pejabat-section" id="pejabat-section">
                      <div class="pejabat-section-title">
                      Data Pejabat
                    </div>
                      <div class="form-grid">
                        <div class="form-group">
                          <label class="form-label" for="pejabat_nama">
                            Nama Pejabat <span class="required">*</span>
                          </label>
                          <input type="text" id="pejabat_nama" name="pejabat_nama" class="form-control @error('pejabat_nama') is-invalid @enderror" placeholder="Nama lengkap dengan gelar" value="{{ old('pejabat_nama') }}" />
                          @error('pejabat_nama')
                          <div class="text-danger" style="font-size:12px;margin-top:4px;">{{ $message }}</div>
                          @enderror
                          <p class="form-hint">Contoh: I Nyoman Suwarjana, SE</p>
                        </div>
                        <div class="form-group">
                          <label class="form-label" for="pejabat_jabatan">
                            Jabatan <span class="required">*</span>
                          </label>
                          <input type="text" id="pejabat_jabatan" name="pejabat_jabatan" class="form-control @error('pejabat_jabatan') is-invalid @enderror" placeholder="Jabatan pejabat" value="{{ old('pejabat_jabatan') }}" />
                          @error('pejabat_jabatan')
                          <div class="text-danger" style="font-size:12px;margin-top:4px;">{{ $message }}</div>
                          @enderror
                          <p class="form-hint">Contoh: Perbekel Desa, Sekretaris Desa</p>
                        </div>
                        <div class="form-group form-full">
                          <label class="form-label" for="pejabat_tugas">
                            Tugas Pejabat <span class="required">*</span>
                          </label>
                          <textarea id="pejabat_tugas" name="pejabat_tugas" class="form-control @error('pejabat_tugas') is-invalid @enderror" rows="4" placeholder="Deskripsi tugas pejabat...">{{ old('pejabat_tugas') }}</textarea>
                          @error('pejabat_tugas')
                          <div class="text-danger" style="font-size:12px;margin-top:4px;">{{ $message }}</div>
                          @enderror
                          <p class="form-hint">Contoh: Pemegang Kekuasaan Pengelolaan Aset Desa</p>
                        </div>
                        <div class="form-group form-full">
                          <label class="form-label" for="pejabat_alamat">
                            Alamat Pejabat <span class="required">*</span>
                          </label>
                          <textarea id="pejabat_alamat" name="pejabat_alamat" class="form-control @error('pejabat_alamat') is-invalid @enderror" rows="4" placeholder="Masukkan alamat lengkap pejabat...">{{ old('pejabat_alamat') }}</textarea>
                          @error('pejabat_alamat')
                          <div class="text-danger" style="font-size:12px;margin-top:4px;">{{ $message }}</div>
                          @enderror
                        </div>
                        <div class="form-group form-full">
                          @include('partials.signature-pad-field', [
                            'optionalNote' => 'opsional',
                            'defaultMethod' => 'draw',
                            'uploadHint' => 'Format: PNG dan JPG (Max 5MB) - untuk tanda tangan elektronik berkode',
                            'guideItems' => [
                              'Pilih <strong>Gambar Tanda Tangan</strong> untuk tanda tangan manual langsung di sistem',
                              'Pilih <strong>Upload Tanda Tangan Elektronik</strong> untuk tanda tangan berkode (PNG/JPG)',
                              'Ukuran file upload maksimal 5MB',
                              'Boleh dikosongkan jika belum memiliki tanda tangan digital',
                            ],
                          ])
                        </div>
                      </div>

                      {{-- Section Petugas Barang — di dalam pejabat section --}}
                      @php $petugasBarang = \App\Models\Pejabat::where('is_petugas_barang', 1)->first(); @endphp
                      <div class="form-section" id="petugas-barang-section-user" style="margin-top:24px;padding-top:24px;border-top:1px dashed var(--color-border);">
                        <div class="form-section-title">
                          Petugas / Pengurus Barang Milik Desa
                        </div>
                        @if($petugasBarang)
                          <div class="warning-box">
                            <i data-feather="alert-triangle"></i>
                            <span>Petugas/Pengurus Barang Milik Desa saat ini adalah <strong>{{ $petugasBarang->nama }}</strong>{{ $petugasBarang->jabatan ? ' (' . $petugasBarang->jabatan . ')' : '' }}. Untuk menggantinya, edit pejabat tersebut dan hapus centang terlebih dahulu.</span>
                          </div>
                        @else
                          <div class="form-group">
                            {{-- Hidden input fallback agar is_petugas_barang = 0 terkirim saat tidak dicentang --}}
                            <input type="hidden" name="is_petugas_barang" value="0" />
                            <label style="display:flex;align-items:center;gap:12px;padding:14px 16px;background:var(--color-surface);border:1.5px solid var(--color-border);border-radius:10px;cursor:pointer;transition:background-color 0.15s ease, border-color 0.15s ease;" id="petugas-barang-user-wrapper">
                              <input type="checkbox" id="is_petugas_barang" name="is_petugas_barang" value="1"
                                     {{ old('is_petugas_barang') ? 'checked' : '' }}
                                     onchange="document.getElementById('petugas-barang-user-wrapper').style.borderColor=this.checked?'var(--color-primary)':'var(--color-border)';document.getElementById('petugas-barang-user-wrapper').style.background=this.checked?'var(--color-primary-tint)':'var(--color-surface)';" />
                              <div>
                                <div style="font-weight:600;font-size:14px;color:var(--color-text);">Jadikan sebagai Petugas/Pengurus Barang Milik Desa</div>
                                <div style="font-size:12px;color:var(--color-text-muted);margin-top:2px;">Pejabat ini akan otomatis muncul sebagai penandatangan di kolom kanan pada export Kartu Inventaris Ruangan dan export Buku Inventaris (KIB).</div>
                              </div>
                            </label>
                          </div>
                        @endif
                      </div>
                    </div>
                  </div>

                  <!-- Form Actions -->
                  <div class="form-actions">
                    <button type="button" class="btn-cancel" onclick="confirmCancel()">
                      <i data-feather="x"></i>
                      Batal
                    </button>
                    <button type="button" class="btn-submit" onclick="confirmSubmit()">
                      <i data-feather="save"></i>
                      Simpan Pengguna
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
        try { sessionStorage.setItem('users_returning', '1'); } catch(e) {}
        const form = document.getElementById('user-form');
        const isAktif = document.getElementById('is_aktif');
        const statusBadge = document.getElementById('status-badge');
        const createPejabat = document.getElementById('create_pejabat');
        const pejabatSection = document.getElementById('pejabat-section');
        const namaUser = document.getElementById('nama');
        const namaPejabat = document.getElementById('pejabat_nama');
        const usernameInput = document.getElementById('username'); // max 50 char, harus diawali huruf
        // const emailInput = document.getElementById('email'); // email tidak digunakan aktif
        const passwordInput = document.getElementById('password');
        const passwordConfirmationInput = document.getElementById('password_confirmation');
        const jabatanPejabat = document.getElementById('pejabat_jabatan');
        const tugasPejabat = document.getElementById('pejabat_tugas');
        const alamatUser = document.getElementById('alamat');
        const alamatPejabat = document.getElementById('pejabat_alamat');
        const usernameLiveError = document.getElementById('username-live-error');

        function showError(input, message){
          if (!input) return;
          const errorContainer = input.closest('.form-group') || input.parentElement;
          if (errorContainer) {
            errorContainer.classList.add('has-error');
            const labelEl = errorContainer.querySelector('.form-label, label');
            if (labelEl) {
              labelEl.classList.add('error', 'is-invalid');
            }
          }
          let errorEl = errorContainer ? errorContainer.querySelector('.error-text, .invalid-feedback-custom') : null;
          if(!errorEl && errorContainer){
            errorEl = document.createElement('div');
            errorEl.classList.add('invalid-feedback-custom', 'sima-dynamic-feedback');
            const formHint = errorContainer.querySelector('.form-hint');
            if (formHint) {
              errorContainer.insertBefore(errorEl, formHint);
            } else {
              errorContainer.appendChild(errorEl);
            }
          }
          if (errorEl) {
            errorEl.innerHTML = '<svg width="13" height="13" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round" style="flex-shrink:0;margin-top:2px;"><path d="m21.73 18-8-14a2 2 0 0 0-3.48 0l-8 14A2 2 0 0 0 4 21h16a2 2 0 0 0 1.73-3Z"></path><line x1="12" y1="9" x2="12" y2="13"></line><line x1="12" y1="17" x2="12.01" y2="17"></line></svg><span>' + escapeHtml(message) + '</span>';
            errorEl.style.display = 'flex';
          }
          input.classList.add('is-invalid', 'error');
          if (typeof input.setCustomValidity === 'function') {
            input.setCustomValidity(message);
          }
        }

        function clearError(input){
          if (!input) return;
          if (typeof window.simaClearFieldError === 'function') {
            window.simaClearFieldError(input);
          }
          const errorContainer = input.closest('.form-group') || input.parentElement;
          if (errorContainer) {
            errorContainer.classList.remove('has-error');
            const labelEl = errorContainer.querySelector('.form-label, label');
            if (labelEl) {
              labelEl.classList.remove('error', 'is-invalid');
            }
            const errorEls = errorContainer.querySelectorAll('.error-text, .text-danger, .invalid-feedback-custom, .sima-dynamic-feedback');
            errorEls.forEach(function(el) {
              if (el.classList.contains('sima-dynamic-feedback')) {
                el.remove();
              } else {
                el.innerHTML = '';
                el.style.display = 'none';
              }
            });
          }
          input.classList.remove('is-invalid', 'error');
          if (typeof input.setCustomValidity === 'function') {
            input.setCustomValidity('');
          }
        }
        window.clearError = clearError;

        function syncPejabatRequiredFields() {
          const isRequired = !!(createPejabat && createPejabat.checked);

          if (namaPejabat) {
            if (isRequired) {
              namaPejabat.setAttribute('required', 'required');
            } else {
              namaPejabat.removeAttribute('required');
            }
          }

          if (jabatanPejabat) {
            if (isRequired) {
              jabatanPejabat.setAttribute('required', 'required');
            } else {
              jabatanPejabat.removeAttribute('required');
            }
          }

          if (tugasPejabat) {
            if (isRequired) {
              tugasPejabat.setAttribute('required', 'required');
            } else {
              tugasPejabat.removeAttribute('required');
            }
          }

          const alamatPejabat = document.getElementById('pejabat_alamat');
          if (alamatPejabat) {
            if (isRequired) {
              alamatPejabat.setAttribute('required', 'required');
            } else {
              alamatPejabat.removeAttribute('required');
            }
          }
        }

        const signatureField = window.initSignaturePadField({
          defaultMethod: 'draw',
          requireSignature: false,
          requiredUploadMessage: 'Silakan upload file tanda tangan elektronik terlebih dahulu.',
          requiredDrawMessage: 'Silakan gambar tanda tangan terlebih dahulu.'
        });

        function validatePasswordConfirmationLive(focusOnError = false){
          if(!passwordInput || !passwordConfirmationInput) return true;

          const passwordVal = passwordInput.value || '';
          const confirmVal = passwordConfirmationInput.value || '';

          // Biarkan rule required bawaan browser menangani kondisi kosong
          if(confirmVal.length === 0){
            passwordConfirmationInput.setCustomValidity('');
            clearError(passwordConfirmationInput);
            return true;
          }

          if(passwordVal !== confirmVal){
            const message = 'Konfirmasi password tidak sama dengan password';
            passwordConfirmationInput.setCustomValidity(message);
            showError(passwordConfirmationInput, message);
            if(focusOnError){
              passwordConfirmationInput.focus();
            }
            return false;
          }

          passwordConfirmationInput.setCustomValidity('');
          clearError(passwordConfirmationInput);
          return true;
        }

        window.prepareUserSignatureFile = signatureField.prepareForSubmit;

        if(namaUser){
          namaUser.addEventListener('input', function(){
            const val = this.value.trim();
            const regex = /^[a-zA-Z\s\.,'\-]+$/;

            if(val.length === 0){
              showError(this, 'Nama wajib diisi');
            }
            else if(val.length < 3){
              showError(this, 'Nama minimal 3 karakter');
            }
            else if(!regex.test(val)){
              showError(this, 'Nama tidak boleh mengandung angka atau simbol');
            }
            else{
              clearError(this);
            }

            if(createPejabat && createPejabat.checked && namaPejabat){
              namaPejabat.value = this.value;
              if(val.length >= 3){
                clearError(namaPejabat);
              } else if(val.length === 0){
                showError(namaPejabat, 'Nama Pejabat wajib diisi');
              } else {
                showError(namaPejabat, 'Nama pejabat minimal 3 karakter');
              }
              namaPejabat.dispatchEvent(new Event('input', { bubbles: true }));
            }
          });
        }

        if(usernameInput){
          let usernameCheckTimer = null;
          let lastUsernameChecked = '';

          function showUsernameLiveError(message) {
            if (!usernameLiveError) return;
            usernameLiveError.innerHTML = '<svg width="13" height="13" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round" style="flex-shrink:0;margin-top:2px;"><path d="m21.73 18-8-14a2 2 0 0 0-3.48 0l-8 14A2 2 0 0 0 4 21h16a2 2 0 0 0 1.73-3Z"></path><line x1="12" y1="9" x2="12" y2="13"></line><line x1="12" y1="17" x2="12.01" y2="17"></line></svg><span>' + escapeHtml(message) + '</span>';
            usernameLiveError.style.display = 'flex';
            usernameInput.classList.add('is-invalid');
            if (typeof usernameInput.setCustomValidity === 'function') {
              usernameInput.setCustomValidity(message);
            }
          }

          function clearUsernameLiveError() {
            if (!usernameLiveError) return;
            usernameLiveError.innerHTML = '';
            usernameLiveError.style.display = 'none';
            if (usernameInput) {
              usernameInput.classList.remove('is-invalid');
              if (typeof usernameInput.setCustomValidity === 'function') {
                usernameInput.setCustomValidity('');
              }
            }
          }

          function isUsernameFormatValid(value) {
            const regex = /^[A-Za-z][A-Za-z0-9]*([._-][A-Za-z0-9]+)*$/;
            if (!value) return false;
            if (value.length < 3 || value.length > 50) return false;
            return regex.test(value);
          }

          function checkUsernameAvailability(value) {
            const normalized = value.trim();
            if (!isUsernameFormatValid(normalized)) {
              clearUsernameLiveError();
              return;
            }

            if (normalized === lastUsernameChecked) return;
            lastUsernameChecked = normalized;

            const url = `{{ route('users.check-username') }}?username=${encodeURIComponent(normalized)}`;
            fetch(url, { headers: { 'X-Requested-With': 'XMLHttpRequest' } })
              .then(response => response.json())
              .then(data => {
                if (data && data.exists) {
                  showUsernameLiveError('Username sudah digunakan, tolong buat username lain');
                } else {
                  clearUsernameLiveError();
                }
              })
              .catch(() => {
                // Abaikan error network untuk live check
              });
          }

          usernameInput.addEventListener('input', function(){
            const val = this.value.trim();
            const regex = /^[A-Za-z][A-Za-z0-9]*([._-][A-Za-z0-9]+)*$/;
            if(val.length === 0){
              showError(this, 'Username wajib diisi');
              clearUsernameLiveError();
            } else if(val.length < 3){
              showError(this, 'Username minimal 3 karakter');
              clearUsernameLiveError();
            } else if(val.length > 50){
              showError(this, 'Username maksimal 50 karakter');
              clearUsernameLiveError();
            } else if(!regex.test(val)){
              showError(this, 'Username harus diawali huruf; titik, strip, underscore hanya boleh di tengah dan tidak berurutan');
              clearUsernameLiveError();
            } else {
              clearError(this);
              clearTimeout(usernameCheckTimer);
              usernameCheckTimer = setTimeout(() => {
                checkUsernameAvailability(val);
              }, 500);
            }
          });
        }

        /*
        // Email input validation — dinonaktifkan (sistem tidak menggunakan email)
        if(emailInput){
          emailInput.addEventListener('input', function(){
            const val = this.value.trim();
            const regex = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
            if(val.length === 0){
              showError(this, 'Email wajib diisi');
            } else if(!regex.test(val)){
              showError(this, 'Format email tidak valid');
            } else {
              clearError(this);
            }
          });
        }
        */

        if(passwordInput){
          passwordInput.addEventListener('input', function(){
            const val = this.value;

            if(val.length < 8){
              showError(this, 'Password minimal 8 karakter');
            } else {
              clearError(this);
            }

            validatePasswordConfirmationLive();
          });
        }

        if(passwordConfirmationInput){
          passwordConfirmationInput.addEventListener('input', function(){
            validatePasswordConfirmationLive();
          });
        }

        if(jabatanPejabat){
          jabatanPejabat.addEventListener('input', function(){
            if(createPejabat && createPejabat.checked){
              const val = this.value.trim();

              if(val.length === 0){
                showError(this, 'Jabatan wajib diisi');
              }
              else if(val.length < 3){
                showError(this, 'Jabatan minimal 3 karakter');
              }
              else{
                clearError(this);
              }
            } else {
              clearError(this);
            }
          });
        }

        if(tugasPejabat){
          tugasPejabat.addEventListener('input', function(){
            if(createPejabat && createPejabat.checked){
              const val = this.value.trim();

              if(val.length === 0){
                showError(this, 'Tugas pejabat wajib diisi');
              }
              else if(val.length < 3){
                showError(this, 'Tugas pejabat minimal 3 karakter');
              }
              else{
                clearError(this);
              }
            } else {
              clearError(this);
            }
          });
        }

        if(alamatPejabat){
          alamatPejabat.addEventListener('input', function(){
            if(createPejabat && createPejabat.checked){
              const val = this.value.trim();
              if(val.length === 0){
                showError(this, 'Alamat Pejabat wajib diisi');
              } else {
                clearError(this);
              }
            } else {
              clearError(this);
            }
          });
        }

        if (isAktif) {
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
        }

        if(createPejabat && pejabatSection){
          createPejabat.addEventListener('change', function(){
            syncPejabatRequiredFields();

            if(this.checked){
              pejabatSection.classList.add('show');
              if(namaUser && namaPejabat){
                namaPejabat.value = namaUser.value;
                if(namaPejabat.value.trim().length >= 3){
                  clearError(namaPejabat);
                  namaPejabat.dispatchEvent(new Event('input', { bubbles: true }));
                }
              }
              if(alamatUser && alamatPejabat){
                alamatPejabat.value = alamatUser.value;
                if(alamatPejabat.value.trim().length > 0){
                  clearError(alamatPejabat);
                  alamatPejabat.dispatchEvent(new Event('input', { bubbles: true }));
                }
              }
              if (typeof updateJabatanPejabat === 'function') {
                updateJabatanPejabat();
              }
              if (signatureField && typeof signatureField.ensureSetup === 'function') {
                signatureField.ensureSetup();
              }
            } else {
              pejabatSection.classList.remove('show');
              if (namaPejabat) {
                clearError(namaPejabat);
              }
              if (jabatanPejabat) {
                jabatanPejabat.value = '';
                clearError(jabatanPejabat);
              }
              if (tugasPejabat) {
                clearError(tugasPejabat);
              }
              if (alamatPejabat) {
                clearError(alamatPejabat);
              }
              signatureField.clearError();
            }
          });

          if(createPejabat.checked){
            pejabatSection.classList.add('show');
            if (typeof updateJabatanPejabat === 'function') {
              updateJabatanPejabat();
            }
            if (signatureField && typeof signatureField.ensureSetup === 'function') {
              signatureField.ensureSetup();
            }
          }

          syncPejabatRequiredFields();
        }

        if(isAktif && !isAktif.checked){
          statusBadge.textContent = 'Tidak Aktif';
          statusBadge.classList.remove('active');
          statusBadge.classList.add('inactive');
        }

        if(alamatUser && alamatPejabat){
          alamatUser.addEventListener('input', function(){
            if(createPejabat && createPejabat.checked){
              alamatPejabat.value = this.value;
              if(this.value.trim().length > 0){
                clearError(alamatPejabat);
              }
              alamatPejabat.dispatchEvent(new Event('input', { bubbles: true }));
            }
          });
        }

        form.addEventListener('submit', function(e){
          e.preventDefault();
          confirmSubmit();
        });
      })();

      function escapeHtml(unsafe) {
        if (!unsafe) return '';
        return String(unsafe)
          .replace(/&/g, '&amp;')
          .replace(/</g, '&lt;')
          .replace(/>/g, '&gt;')
          .replace(/"/g, '&quot;')
          .replace(/'/g, '&#039;');
      }

      // Toggle password visibility
      function togglePassword(fieldId, btn){
        const field = document.getElementById(fieldId);
        const icon = btn.querySelector('i');
        
        if(field.type === 'password'){
          field.type = 'text';
          icon.setAttribute('data-feather', 'eye-off');
        } else {
          field.type = 'password';
          icon.setAttribute('data-feather', 'eye');
        }
        feather.replace();
      }

      // Perbekel, Super Admin, dan Sekretaris hint
      const roleSelect = document.getElementById('role');
      const perbekelHint = document.getElementById('perbekel-hint');
      const superAdminHint = document.getElementById('super-admin-hint');
      const sekretarisHint = document.getElementById('sekretaris-hint');
      const bidangSection = document.getElementById('bidang-section');
      const bidangIdSelect = document.getElementById('bidang_id');
      const dinasSection = document.getElementById('dinas-section');
      const dinasIdSelect = document.getElementById('dinas_id');
      const pejabatCheckbox = document.getElementById('create_pejabat');
      const jabatanPejabatInput = document.getElementById('pejabat_jabatan');
      
      if (roleSelect) {
          const perbekelExists = {{ $perbekelExists ? 'true' : 'false' }};
          const superAdminExists = {{ $superAdminExists ? 'true' : 'false' }};
          function updateRoleHints() {
              if (perbekelHint) perbekelHint.style.display = (roleSelect.value === 'perbekel') ? 'block' : 'none';
              if (superAdminHint) superAdminHint.style.display = (roleSelect.value === 'super_admin') ? 'block' : 'none';
              if (sekretarisHint) sekretarisHint.style.display = (roleSelect.value.toLowerCase().includes('sekretaris')) ? 'block' : 'none';
              
              const roleValue = roleSelect.value.toLowerCase();
              
              // Show BIDANG for: kaur, kasi, staf
              const needsBidang = roleValue.includes('kaur') || roleValue.includes('kasi') || roleValue.includes('staf');
              if (bidangSection) {
                  bidangSection.style.display = needsBidang ? 'block' : 'none';
                  
                  if (bidangIdSelect) {
                      if (needsBidang) {
                          bidangIdSelect.setAttribute('required', 'required');
                      } else {
                          bidangIdSelect.removeAttribute('required');
                          bidangIdSelect.value = '';
                      }
                  }
              }
              
              // Show DINAS for: kelian_banjar
              const needsDinas = roleValue.includes('kelian_banjar');
              if (dinasSection) {
                  dinasSection.style.display = needsDinas ? 'block' : 'none';
                  
                  if (dinasIdSelect) {
                      if (needsDinas) {
                          dinasIdSelect.setAttribute('required', 'required');
                      } else {
                          dinasIdSelect.removeAttribute('required');
                          dinasIdSelect.value = '';
                      }
                  }
              }
              
              if (typeof feather !== 'undefined') feather.replace();
          }
          roleSelect.addEventListener('change', updateRoleHints);
          updateRoleHints();
      }

      function toTitleCase(text) {
        return (text || '')
          .toString()
          .toLowerCase()
          .replace(/_/g, ' ')
          .replace(/\b\w/g, function(char) { return char.toUpperCase(); })
          .trim();
      }

      function getAutoJabatanByRole() {
        if (!roleSelect || !roleSelect.value) return '';

        const roleValue = roleSelect.value.toLowerCase();
        const roleLabel = roleSelect.selectedOptions.length
          ? roleSelect.selectedOptions[0].textContent.trim()
          : toTitleCase(roleValue);

        // Kaur mengikuti pola lama: jika ada bidang, hasilkan "Kaur <Bidang>".
        if (roleValue.includes('kaur')) {
          const bidangLabel = (bidangIdSelect && bidangIdSelect.selectedOptions.length)
            ? bidangIdSelect.selectedOptions[0].textContent.trim()
            : '';

          if (bidangIdSelect && bidangIdSelect.value && bidangLabel) {
            return `Kaur ${bidangLabel}`;
          }

          return roleLabel || 'Kaur';
        }

        // Kasi: jika ada bidang, hasilkan "Kasi <Bidang>"
        if (roleValue.includes('kasi')) {
          const bidangLabel = (bidangIdSelect && bidangIdSelect.selectedOptions.length)
            ? bidangIdSelect.selectedOptions[0].textContent.trim()
            : '';

          if (bidangIdSelect && bidangIdSelect.value && bidangLabel) {
            return `Kasi ${bidangLabel}`;
          }

          return roleLabel || 'Kasi';
        }

        // Kelian Banjar: jika ada dinas, hasilkan "Kelian Banjar Dinas <Dinas>"
        if (roleValue.includes('kelian_banjar')) {
          const dinasLabel = (dinasIdSelect && dinasIdSelect.selectedOptions.length)
            ? dinasIdSelect.selectedOptions[0].textContent.trim()
            : '';

          if (dinasIdSelect && dinasIdSelect.value && dinasLabel) {
            return `Kelian Banjar Dinas ${dinasLabel}`;
          }

          return roleLabel || 'Kelian Banjar';
        }

        // Staf: jika ada bidang, hasilkan "Staf <Bidang>"
        if (roleValue.includes('staf')) {
          const bidangLabel = (bidangIdSelect && bidangIdSelect.selectedOptions.length)
            ? bidangIdSelect.selectedOptions[0].textContent.trim()
            : '';

          if (bidangIdSelect && bidangIdSelect.value && bidangLabel) {
            return `Staf ${bidangLabel}`;
          }

          return roleLabel || 'Staf';
        }

        // Role lain gunakan label role yang tampil di dropdown.
        return roleLabel || toTitleCase(roleValue);
      }

      function updateJabatanPejabat() {
        if (!pejabatCheckbox || !jabatanPejabatInput || !roleSelect) return;
        if (!pejabatCheckbox.checked) return;

        const autoJabatan = getAutoJabatanByRole();
        if (autoJabatan) {
          jabatanPejabatInput.value = autoJabatan;
          if (typeof window.clearError === 'function') {
            window.clearError(jabatanPejabatInput);
          } else if (typeof window.simaClearFieldError === 'function') {
            window.simaClearFieldError(jabatanPejabatInput);
          }
          jabatanPejabatInput.dispatchEvent(new Event('input', { bubbles: true }));
        }
      }

      if (roleSelect) roleSelect.addEventListener('change', updateJabatanPejabat);
      if (bidangIdSelect) bidangIdSelect.addEventListener('change', updateJabatanPejabat);
      if (dinasIdSelect) dinasIdSelect.addEventListener('change', updateJabatanPejabat);
      if (pejabatCheckbox) pejabatCheckbox.addEventListener('change', updateJabatanPejabat);
      updateJabatanPejabat();

      // Confirm Cancel
      function confirmCancel(){
        AppModal.confirmCancel({
          url: "{{ route('users.index') }}",
          returnKey: 'users_returning',
          isEdit: false
        });
      }

      // Confirm Submit
      function confirmSubmit(){
        const form = document.getElementById('user-form');
        if (!form) return;

        const passwordInput = document.getElementById('password');
        const passwordConfirmationInput = document.getElementById('password_confirmation');

        if (passwordInput && passwordConfirmationInput) {
          const passwordVal = passwordInput.value || '';
          const confirmVal = passwordConfirmationInput.value || '';

          if (confirmVal.length > 0 && passwordVal !== confirmVal) {
            passwordConfirmationInput.setCustomValidity('Konfirmasi password tidak sama dengan password');
          } else {
            passwordConfirmationInput.setCustomValidity('');
          }
        }
        
        // Strict Validation via SIMA Standard
        if (typeof window.simaValidateForm === 'function') {
          if (!window.simaValidateForm(form)) {
            return false;
          }
        } else if (!form.checkValidity()) {
          const firstTarget = form.querySelector(':invalid');
          if (firstTarget && typeof window.simaScrollToField === 'function') {
            window.simaScrollToField(firstTarget);
          }
          return false;
        }

        const usernameLiveError = document.getElementById('username-live-error');
        const isUsernameInvalid = usernameLiveError && usernameLiveError.style.display !== 'none' && usernameLiveError.textContent.trim() !== '';
        if (isUsernameInvalid) {
          const usernameInput = document.getElementById('username');
          if (usernameInput) {
            if (typeof window.simaScrollToField === 'function') {
              window.simaScrollToField(usernameInput);
            } else {
              usernameInput.scrollIntoView({ behavior: 'smooth', block: 'center' });
              usernameInput.focus();
            }
          }
          return false;
        }

        AppModal.confirm(
          'Konfirmasi Simpan Data',
          'Apakah Anda yakin data user yang dimasukkan sudah benar dan siap disimpan?',
          function() {
            if (typeof window.prepareUserSignatureFile === 'function') {
              window.prepareUserSignatureFile().then(function(isValid) {
                if (isValid) {
                  form.submit();
                }
              });
            } else {
              form.submit();
            }
          }
        );
      }
    </script>
  </body>
</html>
