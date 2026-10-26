<!doctype html>
<html lang="en" data-pc-preset="{{ $pc_preset_theme }}" data-pc-theme="{{ $pc_dark_layout }}">
  <head>
    @include('layouts.head-page-meta', ['title' => 'Edit User'])
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
      
      /* Info Box */
      .info-box {
        background: var(--color-primary-tint);
        border: 1px solid var(--color-border);
        border-radius: var(--radius-sm);
        padding: 10px 14px;
        display: flex;
        align-items: center;
        gap: 10px;
        margin-top: 16px;
      }
      .info-box i {
        color: var(--color-primary);
        stroke: var(--color-primary);
        flex-shrink: 0;
        width: 16px;
        height: 16px;
      }
      .info-box-text {
        font-size: 12.5px;
        color: var(--color-text);
        line-height: 1.5;
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
        width: 28px;
        height: 28px;
        z-index: 3;
        pointer-events: auto;
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

      /* File Upload Area (TTD) */
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
        color: #374151;
        font-weight: 500;
      }
      .file-upload-hint {
        font-size: 12px;
        color: #9ca3af;
      }
      /* Existing TTD Preview */
      .ttd-existing-preview {
        margin-bottom: 12px;
        padding: 12px;
        background: var(--color-surface);
        border: 1px solid var(--color-border);
        border-radius: var(--radius-sm);
        display: flex;
        align-items: center;
        gap: 12px;
      }
      .ttd-existing-img {
        width: 80px;
        height: 80px;
        object-fit: contain;
        border-radius: var(--radius-sm);
        border: 1px solid var(--color-border);
        background: var(--color-surface, #f8faf7);
        padding: 4px;
      }
      .ttd-existing-info {
        flex: 1;
      }
      .ttd-existing-info .ttd-label {
        font-size: 11px;
        font-weight: 600;
        color: var(--color-primary);
        text-transform: uppercase;
        letter-spacing: 0.5px;
        margin-bottom: 4px;
      }
      .ttd-existing-info .ttd-filename {
        font-size: 13px;
        font-weight: 500;
        color: var(--color-text);
      }
      /* New file preview */
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
    </style>
  </head>

  <body>
    @include('layouts.modals')
    @include('layouts.layout-vertical')

    <div class="pc-container">
      <div class="pc-content">
        @include('layouts.breadcrumb', ['breadcrumbItemHome' => 'Master Data', 'breadcrumbItem' => 'Manajemen Pengguna', 'breadcrumbItemUrl' => route('users.index'), 'breadcrumbItemActive' => 'Edit Pengguna'])
        
        <div class="grid grid-cols-12 gap-x-6">
          <div class="col-span-12 w-full">
            <div class="card">
              <div class="card-header">
                <div class="flex items-center">
                  <div class="card-header-icon">
                    <i data-feather="users"></i>
                  </div>
                  <div>
                    <h5 class="mb-0">Edit User</h5>
                  </div>
                </div>
              </div>

              <div class="card-body pt-4">
                <form id="edit-user-form" method="POST" action="{{ route('users.update', $user->id) }}" enctype="multipart/form-data" novalidate>
                  @csrf
                  @method('PUT')
                  <input type="hidden" name="is_aktif" value="0" />
                  
                  <!-- Informasi User -->
                  <div class="form-section">
                    <div class="form-section-title">
                      Informasi User
                    </div>
                    <div class="form-grid">
                      <div class="form-group">
                        <label class="form-label" for="nama">
                          Nama Lengkap <span class="required">*</span>
                        </label>
                        <input type="text" id="nama" name="nama" class="form-control @error('nama') is-invalid @enderror" value="{{ old('nama', $user->nama) }}" pattern="^[a-zA-Z\s\.,'\-]+$" title="Nama lengkap hanya boleh berisi huruf dan tanda baca nama/gelar (tidak boleh angka)" minlength="3" required />
                        @error('nama')
                        <div class="text-danger">{{ $message }}</div>
                        @enderror
                      </div>
                      <div class="form-group">
                        <label class="form-label" for="username">
                          Username <span class="required">*</span>
                        </label>
                        <input type="text" id="username" name="username" class="form-control @error('username') is-invalid @enderror" value="{{ old('username', $user->username) }}" required />
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
                        <textarea id="alamat" name="alamat" class="form-control @error('alamat') is-invalid @enderror" placeholder="Masukkan alamat lengkap" rows="4" required>{{ old('alamat', $user->alamat) }}</textarea>
                        @error('alamat')
                        <div class="text-danger" style="font-size:12px;margin-top:4px;">{{ $message }}</div>
                        @enderror
                      </div>
                      
                      <div class="form-group">
                        <label class="form-label" for="role">
                          Role <span class="required">*</span>
                        </label>
                        <select id="role" name="role" class="form-select @error('role') is-invalid @enderror" required>
                          <option value="" disabled {{ !$user->role ? 'selected' : '' }} style="display:none;">Pilih Role</option>
                          @foreach($roles as $role)
                            @php
                              $isSuperAdmin = $role->nama === 'super_admin';
                              $isPerbekel   = $role->nama === 'perbekel';
                              $isSekretaris = str_contains(strtolower($role->nama), 'sekretaris');
                              $skip = ($isSuperAdmin && $superAdminExists) || ($isPerbekel && $perbekelExists) || ($isSekretaris && $sekretarisExists);
                            @endphp
                            @if(!$skip)
                            <option value="{{ $role->nama }}" {{ old('role', $user->role ? $user->role->nama : '') == $role->nama ? 'selected' : '' }}>
                              {{ ucwords(str_replace('_', ' ', $role->nama)) }}
                            </option>
                            @endif
                          @endforeach
                        </select>
                        <div id="perbekel-hint" style="display:none; margin-top:6px; padding:8px 12px; background:#fff3cd; border:1px solid #ffc107; border-radius:6px; font-size:0.82rem; color:#856404;">
                            <i data-feather="alert-triangle" style="width:14px;height:14px;display:inline;vertical-align:middle;margin-right:4px;"></i>
                            @if($perbekelExists)
                                Sudah ada user lain dengan role Perbekel. Tidak dapat mengubah ke role ini.
                            @else
                                Role Perbekel hanya boleh dimiliki oleh 1 user.
                            @endif
                        </div>
                        <div id="super-admin-hint" style="display:none; margin-top:6px; padding:8px 12px; background:#fff3cd; border:1px solid #ffc107; border-radius:6px; font-size:0.82rem; color:#856404;">
                            <i data-feather="alert-triangle" style="width:14px;height:14px;display:inline;vertical-align:middle;margin-right:4px;"></i>
                            @if($superAdminExists)
                                Sudah ada Super Admin lain. Tidak dapat mengubah ke role ini.
                            @else
                                Role Super Admin hanya boleh dimiliki oleh 1 user.
                            @endif
                        </div>
                        <div id="sekretaris-hint" style="display:none; margin-top:6px; padding:8px 12px; background:#fff3cd; border:1px solid #ffc107; border-radius:6px; font-size:0.82rem; color:#856404;">
                          <i data-feather="alert-triangle" style="width:14px;height:14px;display:inline;vertical-align:middle;margin-right:4px;"></i>
                          @if($sekretarisExists)
                            Sudah ada user lain dengan role Sekretaris Desa. Tidak dapat mengubah ke role ini.
                          @else
                            Role Sekretaris Desa hanya boleh dimiliki oleh 1 user.
                          @endif
                        </div>
                        @error('role')
                        <div class="text-danger">{{ $message }}</div>
                        @enderror
                      </div>
                      
                      <div class="form-group" id="bidang-section" style="display:none;">
                        <label class="form-label" for="bidang_id">
                          Bidang <span class="required">*</span>
                        </label>
                        <select id="bidang_id" name="bidang_id" class="form-select @error('bidang_id') is-invalid @enderror">
                          <option value="" disabled {{ !$user->bidang_id ? 'selected' : '' }} style="display:none;">Pilih Bidang</option>
                          @foreach($bidangs as $bidangItem)
                            <option value="{{ $bidangItem->id }}" {{ old('bidang_id', $user->bidang_id) == $bidangItem->id ? 'selected' : '' }}>
                              {{ $bidangItem->nama }}
                            </option>
                          @endforeach
                        </select>
                        @error('bidang_id')
                          <div class="text-danger">{{ $message }}</div>
                        @enderror
                      </div>
                      
                      <div class="form-group" id="dinas-section" style="display:none;">
                        <label class="form-label" for="dinas_id">
                          Dinas <span class="required">*</span>
                        </label>
                        <select id="dinas_id" name="dinas_id" class="form-select @error('dinas_id') is-invalid @enderror">
                          <option value="" disabled {{ !$user->dinas_id ? 'selected' : '' }} style="display:none;">Pilih Dinas</option>
                          @foreach($dinas as $dinasItem)
                            <option value="{{ $dinasItem->id }}" {{ old('dinas_id', $user->dinas_id) == $dinasItem->id ? 'selected' : '' }}>
                              {{ $dinasItem->nama }}
                            </option>
                          @endforeach
                        </select>
                        @error('dinas_id')
                          <div class="text-danger">{{ $message }}</div>
                        @enderror
                      </div>
                      
                      <div class="form-group">
                        <label class="form-label">Status User</label>
                        <div class="toggle-wrapper" style="margin-top: 8px;">
                          <label class="toggle-switch">
                            <input type="checkbox" id="is_aktif" name="is_aktif" value="1" {{ old('is_aktif', $user->is_aktif) ? 'checked' : '' }} />
                            <span class="toggle-slider"></span>
                          </label>
                          <span class="toggle-label">User</span>
                          <span id="status-badge" class="toggle-status {{ old('is_aktif', $user->is_aktif) ? 'active' : 'inactive' }}">
                            {{ old('is_aktif', $user->is_aktif) ? 'Aktif' : 'Tidak Aktif' }}
                          </span>
                        </div>
                        <p class="form-hint">User tidak aktif tidak dapat login ke sistem</p>
                      </div>
                    </div>
                  </div>
                  
                  <!-- Password Section -->
                  <div class="form-section">
                    <div class="form-section-title">
                      Ubah Password <span class="optional">(Opsional)</span>
                    </div>
                    <div class="form-grid">
                      <div class="form-group">
                        <label class="form-label" for="password">
                          Password Baru 
                          {{-- <span class="optional">(kosongkan jika tidak ingin mengubah)</span> --}}
                        </label>
                        <div class="password-wrapper">
                          <input type="password" id="password" name="password" class="form-control @error('password') is-invalid @enderror" placeholder="Kosongkan jika tidak ganti password" autocomplete="new-password" />
                          <button type="button" class="password-toggle" data-password-target="password" onclick="return togglePassword('password', this)" aria-label="Tampilkan/Sembunyikan password baru">
                            <i data-feather="eye"></i>
                          </button>
                        </div>
                        @error('password')
                        <div class="text-danger">{{ $message }}</div>
                        @enderror
                        <p class="form-hint">Minimal 8 karakter. Disarankan menggunakan kombinasi huruf besar, huruf kecil, dan angka</p>
                      </div>
                      
                      <div class="form-group">
                        <label class="form-label" for="password_confirmation">
                          Konfirmasi Password Baru
                        </label>
                        <div class="password-wrapper">
                          <input type="password" id="password_confirmation" name="password_confirmation" class="form-control" placeholder="Ulangi password baru" autocomplete="new-password" />
                          <button type="button" class="password-toggle" data-password-target="password_confirmation" onclick="return togglePassword('password_confirmation', this)" aria-label="Tampilkan/Sembunyikan konfirmasi password baru">
                            <i data-feather="eye"></i>
                          </button>
                        </div>
                        <div id="password-confirm-live-error" class="invalid-feedback-custom" style="display:none;"></div>
                      </div>
                    </div>
                  </div>
                  
                  <!-- Petugas Barang — hanya tampil jika user punya pejabat -->
                  @if($user->pejabat)
                  @php $petugasBarangLain = \App\Models\Pejabat::where('is_petugas_barang', 1)->where('id', '!=', $user->pejabat->id)->first(); @endphp
                  <div class="form-section">
                    <div class="form-section-title">
                      Petugas / Pengurus Barang Milik Desa
                    </div>
                    @if($petugasBarangLain)
                      <div class="warning-box">
                        <i data-feather="alert-triangle"></i>
                        <span>Petugas/Pengurus Barang Milik Desa saat ini adalah <strong>{{ $petugasBarangLain->nama }}</strong>{{ $petugasBarangLain->jabatan ? ' (' . $petugasBarangLain->jabatan . ')' : '' }}. Untuk menggantinya, edit pejabat tersebut dan hapus centang terlebih dahulu.</span>
                      </div>
                    @else
                      {{-- Hidden input fallback agar is_petugas_barang = 0 terkirim saat un-check --}}
                      <input type="hidden" name="is_petugas_barang" value="0" />
                      <label style="display:flex;align-items:center;gap:12px;padding:14px 16px;background:var(--color-surface, #f8faf7);border:1.5px solid {{ $user->pejabat->is_petugas_barang ? 'var(--color-primary, #1f3a2a)' : 'var(--color-border, #cfdcc8)' }};border-radius:10px;cursor:pointer;transition:background-color 0.15s ease, border-color 0.15s ease;" id="petugas-barang-edit-wrapper">
                        <input type="checkbox" name="is_petugas_barang" value="1"
                               {{ $user->pejabat->is_petugas_barang ? 'checked' : '' }}
                               onchange="document.getElementById('petugas-barang-edit-wrapper').style.borderColor=this.checked?'var(--color-primary, #1f3a2a)':'var(--color-border, #cfdcc8)';document.getElementById('petugas-barang-edit-wrapper').style.background=this.checked?'var(--color-primary-tint, #d5e4d7)':'var(--color-surface, #f8faf7)';" />
                        <div>
                          <div style="font-weight:600;font-size:13.5px;color:var(--color-text, #16251b);">Jadikan sebagai Petugas/Pengurus Barang Milik Desa</div>
                          <div style="font-size:12px;color:var(--color-text-muted, #546958);margin-top:2px;">
                            @if($user->pejabat->is_petugas_barang)
                              Pejabat ini saat ini adalah Petugas/Pengurus Barang Milik Desa. Hapus centang untuk melepas.
                            @else
                              Pejabat ini akan otomatis muncul sebagai penandatangan di kolom kanan pada export Kartu Inventaris Ruangan dan export Buku Inventaris (KIB).
                            @endif
                          </div>
                        </div>
                      </label>
                    @endif
                  </div>
                  @endif

                  <!-- Form Actions -->
                  <div class="form-actions">
                    <a href="{{ route('users.index') }}" class="btn-cancel" onclick="return confirmCancel(event, { returnKey: 'users_returning', isEdit: true })">
                      <i data-feather="x"></i>
                      Batal
                    </a>
                    <button type="submit" class="btn-submit">
                      <i data-feather="save"></i>
                      Update Pengguna
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
      try { sessionStorage.setItem('users_returning', '1'); } catch(e) {}
      function togglePassword(fieldId, btn){
        let field = null;

        // Prioritas: input di wrapper tombol yang diklik, agar tidak salah target.
        if (btn) {
          const wrapper = btn.closest('.password-wrapper');
          if (wrapper) {
            field = wrapper.querySelector('input');
          }
        }

        if (!field && fieldId) {
          field = document.getElementById(fieldId);
        }

        if (!field) return;

        const isCurrentlyVisible = field.getAttribute('data-password-visible') === '1' || field.type === 'text';
        const shouldShow = !isCurrentlyVisible;

        try {
          field.type = shouldShow ? 'text' : 'password';
        } catch (error) {
          // Fallback visual untuk browser/ekstensi yang menolak perubahan type.
        }

        // Safeguard: paksa teks terlihat saat mode tampil di browser berbasis WebKit.
        field.style.webkitTextSecurity = shouldShow ? 'none' : '';
        field.setAttribute('data-password-visible', shouldShow ? '1' : '0');

        if (btn) {
          // Feather mengubah <i data-feather> menjadi <svg>, jadi cari keduanya.
          const icon = btn.querySelector('[data-feather], svg');
          const nextIcon = shouldShow ? 'eye-off' : 'eye';

          if (icon && icon.tagName && icon.tagName.toLowerCase() === 'svg' && typeof feather !== 'undefined' && feather.icons && feather.icons[nextIcon]) {
            icon.outerHTML = feather.icons[nextIcon].toSvg({ width: 18, height: 18 });
          } else if (icon) {
            icon.setAttribute('data-feather', nextIcon);
            if (typeof feather !== 'undefined') {
              feather.replace();
            }
          }
        }

        return false;
      }

      (function(){
        const isAktif = document.getElementById('is_aktif');
        const statusBadge = document.getElementById('status-badge');
        const usernameInput = document.getElementById('username');
        const usernameLiveError = document.getElementById('username-live-error');
        const passwordInput = document.getElementById('password');
        const passwordConfirmationInput = document.getElementById('password_confirmation');
        const passwordConfirmLiveError = document.getElementById('password-confirm-live-error');

        // Toggle status badge
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

        if (usernameInput) {
          let usernameCheckTimer = null;
          let lastUsernameChecked = '';
          const currentUserId = {{ (int) $user->id }};

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

            const url = `{{ route('users.check-username') }}?username=${encodeURIComponent(normalized)}&ignore_id=${currentUserId}`;
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
            if (!val) {
              clearUsernameLiveError();
              return;
            }
            clearTimeout(usernameCheckTimer);
            usernameCheckTimer = setTimeout(() => {
              checkUsernameAvailability(val);
            }, 500);
          });
        }

        function showPasswordConfirmLiveError(message) {
          if (!passwordConfirmationInput || !passwordConfirmLiveError) return;
          passwordConfirmationInput.classList.add('is-invalid');
          passwordConfirmLiveError.innerHTML = '<svg width="13" height="13" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round" style="flex-shrink:0;margin-top:2px;"><path d="m21.73 18-8-14a2 2 0 0 0-3.48 0l-8 14A2 2 0 0 0 4 21h16a2 2 0 0 0 1.73-3Z"></path><line x1="12" y1="9" x2="12" y2="13"></line><line x1="12" y1="17" x2="12.01" y2="17"></line></svg><span>' + escapeHtml(message) + '</span>';
          passwordConfirmLiveError.style.display = 'flex';
        }

        function clearPasswordConfirmLiveError() {
          if (!passwordConfirmationInput || !passwordConfirmLiveError) return;
          passwordConfirmationInput.classList.remove('is-invalid');
          passwordConfirmLiveError.innerHTML = '';
          passwordConfirmLiveError.style.display = 'none';
        }

        function validatePasswordConfirmationLive(focusOnError = false) {
          if (!passwordInput || !passwordConfirmationInput) return true;

          const passwordVal = passwordInput.value || '';
          const confirmVal = passwordConfirmationInput.value || '';

          if (!passwordVal && !confirmVal) {
            passwordConfirmationInput.setCustomValidity('');
            clearPasswordConfirmLiveError();
            return true;
          }

          if (passwordVal !== confirmVal) {
            const message = 'Konfirmasi password tidak sama dengan password baru';
            passwordConfirmationInput.setCustomValidity(message);
            showPasswordConfirmLiveError(message);
            if (focusOnError) {
              passwordConfirmationInput.focus();
            }
            return false;
          }

          passwordConfirmationInput.setCustomValidity('');
          clearPasswordConfirmLiveError();
          return true;
        }

        if (passwordInput) {
          passwordInput.addEventListener('input', function(){
            validatePasswordConfirmationLive();
          });
        }

        if (passwordConfirmationInput) {
          passwordConfirmationInput.addEventListener('input', function(){
            validatePasswordConfirmationLive();
          });
        }
      })();

      // Form submit with confirmation
      (function() {
        const editForm = document.getElementById('edit-user-form');
        const passwordInput = document.getElementById('password');
        const passwordConfirmationInput = document.getElementById('password_confirmation');
        if (editForm) {
          editForm.addEventListener('submit', function(e) {
            e.preventDefault();

            if (passwordInput && passwordConfirmationInput) {
              const passwordVal = passwordInput.value || '';
              const confirmVal = passwordConfirmationInput.value || '';
              if ((passwordVal || confirmVal) && passwordVal !== confirmVal) {
                passwordConfirmationInput.setCustomValidity('Konfirmasi password tidak sama dengan password baru');
                passwordConfirmationInput.focus();
              } else {
                passwordConfirmationInput.setCustomValidity('');
              }
            }

            // Strict Validation via SIMA Standard
            if (typeof window.simaValidateForm === 'function') {
              if (!window.simaValidateForm(editForm)) {
                return false;
              }
            } else if (!editForm.checkValidity()) {
              const firstTarget = editForm.querySelector(':invalid');
              if (firstTarget && typeof window.simaScrollToField === 'function') {
                window.simaScrollToField(firstTarget);
              }
              return false;
            }

            const usernameLiveErrorEl = document.getElementById('username-live-error');
            const isUsernameInvalid = usernameLiveErrorEl && usernameLiveErrorEl.style.display !== 'none' && usernameLiveErrorEl.textContent.trim() !== '';
            const isPassConfirmInvalid = passwordConfirmLiveError && passwordConfirmLiveError.style.display !== 'none' && passwordConfirmLiveError.textContent.trim() !== '';

            if (isUsernameInvalid || isPassConfirmInvalid) {
              const target = (isUsernameInvalid ? usernameInput : null) || (isPassConfirmInvalid ? passwordConfirmationInput : null);
              if (target) {
                if (typeof window.simaScrollToField === 'function') {
                  window.simaScrollToField(target);
                } else {
                  target.scrollIntoView({ behavior: 'smooth', block: 'center' });
                  target.focus();
                }
              }
              return false;
            }
            
            AppModal.confirm(
              'Konfirmasi Update Data',
              'Apakah Anda yakin ingin menyimpan perubahan data user ini?',
              function() {
                editForm.submit();
              }
            );
          });
        }
      })();

      // Perbekel & Super Admin hint + Bidang field visibility
      (function() {
          const roleSelect = document.getElementById('role');
          const perbekelHint = document.getElementById('perbekel-hint');
          const superAdminHint = document.getElementById('super-admin-hint');
            const sekretarisHint = document.getElementById('sekretaris-hint');
          const bidangSection = document.getElementById('bidang-section');
          const bidangSelect = document.getElementById('bidang_id');
          const dinasSection = document.getElementById('dinas-section');
          const dinasSelect = document.getElementById('dinas_id');
          const tambahBidangBtn = document.getElementById('tambah-bidang-btn');
          if (roleSelect) {
              const superAdminExists = {{ $superAdminExists ? 'true' : 'false' }};
              function updateRoleHints() {
                  if (perbekelHint) perbekelHint.style.display = (roleSelect.value === 'perbekel') ? 'block' : 'none';
                  if (superAdminHint) superAdminHint.style.display = (roleSelect.value === 'super_admin') ? 'block' : 'none';
                if (sekretarisHint) sekretarisHint.style.display = (roleSelect.value.toLowerCase().includes('sekretaris')) ? 'block' : 'none';
                  
                  const roleValue = roleSelect.value ? roleSelect.value.toLowerCase() : '';
                  
                  // Show BIDANG for: kaur, kasi, staf
                  const needsBidang = roleValue.includes('kaur') || roleValue.includes('kasi') || roleValue.includes('staf');
                  if (bidangSection) {
                      bidangSection.style.display = needsBidang ? 'block' : 'none';
                  }
                  
                  if (bidangSelect) {
                      if (needsBidang) {
                          bidangSelect.setAttribute('required', 'required');
                      } else {
                          bidangSelect.removeAttribute('required');
                          bidangSelect.value = '';
                      }
                  }
                  
                  // Show DINAS for: kelian_banjar
                  const needsDinas = roleValue.includes('kelian_banjar');
                  if (dinasSection) {
                      dinasSection.style.display = needsDinas ? 'block' : 'none';
                  }
                  
                  if (dinasSelect) {
                      if (needsDinas) {
                          dinasSelect.setAttribute('required', 'required');
                      } else {
                          dinasSelect.removeAttribute('required');
                          dinasSelect.value = '';
                      }
                  }
                  
                  if (typeof feather !== 'undefined') feather.replace();
              }
              roleSelect.addEventListener('change', updateRoleHints);
              updateRoleHints();
          }
      })();

          function showSuccessToast(message) {
            let toast = document.getElementById('inline-success-toast');
            if (!toast) {
              toast = document.createElement('div');
              toast.id = 'inline-success-toast';
              toast.style.position = 'fixed';
              toast.style.top = '16px';
              toast.style.right = '16px';
              toast.style.zIndex = '9999';
              toast.style.background = '#16a34a';
              toast.style.color = '#fff';
              toast.style.padding = '12px 16px';
              toast.style.borderRadius = '10px';
              toast.style.boxShadow = '0 6px 16px rgba(0,0,0,0.2)';
              toast.style.fontSize = '14px';
              toast.style.fontWeight = '600';
              toast.style.display = 'none';
              document.body.appendChild(toast);
            }

            toast.textContent = message;
            toast.style.display = 'block';
            clearTimeout(window.__inlineToastTimer);
            window.__inlineToastTimer = setTimeout(() => {
              toast.style.display = 'none';
            }, 3000);
            return true;
          }
    </script>
  </body>
</html>
