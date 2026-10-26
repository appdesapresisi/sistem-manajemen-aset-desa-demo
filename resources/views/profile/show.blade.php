<!doctype html>
<html lang="en" data-pc-preset="{{ $pc_preset_theme }}" data-pc-theme="{{ $pc_dark_layout }}">
  <head>
    @include('layouts.head-page-meta', ['title' => 'Profil Saya — ' . ($user->nama ?? $user->username)])
    @include('layouts.head-css')
    <style>
      .form-input-sima {
        width: 100%;
        height: 40px;
        padding: 0 14px;
        border: 1px solid var(--color-border, #cfdcc8);
        border-radius: var(--radius-sm, 8px);
        background: var(--color-surface, #f8faf7);
        color: var(--color-text, #16251b);
        font-size: 13.5px;
        box-sizing: border-box;
        transition: border-color 0.15s ease, box-shadow 0.15s ease;
      }
      .form-input-sima:focus {
        border-color: var(--color-primary, #1f3a2a);
        outline: none;
        box-shadow: 0 0 0 1px var(--color-primary, #1f3a2a);
      }
      .form-input-sima.is-invalid {
        border-color: var(--color-danger, #A63D2F);
      }
      .portal-card .portal-section:not(:first-child) {
        margin-top: 0 !important;
        padding-top: 36px !important;
        border-top: none !important;
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
      .btn-cancel i,
      .btn-cancel svg {
        width: 16px !important;
        height: 16px !important;
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
      .btn-submit i,
      .btn-submit svg {
        width: 18px !important;
        height: 18px !important;
      }
      .form-actions {
        display: flex;
        gap: 10px;
        justify-content: flex-end;
        align-items: center;
        padding-top: 10px;
      }
      @media (max-width: 768px) {
        .form-actions {
          justify-content: center !important;
        }
      }
    </style>
  </head>

  <body data-pc-preset="{{ $pc_preset_theme }}" data-pc-sidebar-theme="{{ $pc_sidebar_theme ?? 'light' }}">
    @include('layouts.notification')
    @include('layouts.modals')
    @include('layouts.layout-vertical')

    <div class="pc-container">
      <div class="pc-content">
        @include('layouts.breadcrumb', [
          'breadcrumbItemHome' => 'Akun',
          'breadcrumbItemActive' => 'Profil Saya'
        ])

        <!-- Top Action Bar -->
        <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 20px; flex-wrap: wrap; gap: 12px;">  
          <a href="{{ route('dashboard') }}" class="btn-sima-secondary" style="border: none !important; padding-left: 0 !important; background-color: transparent !important;">
            <i data-feather="arrow-left" style="width: 14px; height: 14px;"></i>
            <span>Kembali ke Dashboard</span>
          </a>
          <div style="display: flex; gap: 10px; align-items: center; flex-wrap: wrap;">
            <button type="button" class="btn-sima-primary" id="btn-toggle-edit" onclick="toggleEditProfile()">
              <i data-feather="edit-2" style="width: 14px; height: 14px;"></i>
              <span>Edit Profil</span>
            </button>
            <a href="{{ route('profile.password') }}" class="btn-sima-secondary">
              <i data-feather="lock" style="width: 14px; height: 14px;"></i>
              <span>Ganti Kata Sandi</span>
            </a>
          </div>
        </div>

        <!-- ===== MAIN PORTAL CARD ===== -->
        <div class="portal-card">

          <!-- Section 1: Kredensial & Hak Akses Pengguna -->
          <div class="portal-section" style="padding-top: 0; border-top: none; margin-top: 32px;">
            <div class="portal-section-header">
              <h2 class="portal-section-title">
                <span>Informasi Akun & Pengguna</span>
              </h2>
            </div>
            
            <div class="portal-stats-grid" style="grid-template-columns: repeat(2, 1fr); gap: 24px 36px;">
              <div class="portal-stat-item">
                <span class="portal-stat-label">Nama Lengkap</span>
                <span class="portal-stat-value">{{ $user->nama ?? '-' }}</span>
              </div>
              <div class="portal-stat-item">
                <span class="portal-stat-label">Username</span>
                <span class="portal-stat-value" style="font-family: monospace;">{{ '@' . $user->username }}</span>
              </div>
              <div class="portal-stat-item">
                <span class="portal-stat-label">Role Akses</span>
                <span class="portal-stat-value">{{ $user->role_name_display ?: ($user->role->nama ?? '-') }}</span>
              </div>
              <div class="portal-stat-item">
                <span class="portal-stat-label">Status Akun</span>
                <span class="portal-stat-value">
                  <span class="status-pill {{ $user->is_aktif ? 'status-pill-success' : 'status-pill-danger' }}" style="font-size: 11.5px; padding: 2px 10px;">
                    {{ $user->is_aktif ? 'Aktif' : 'Tidak Aktif' }}
                  </span>
                </span>
              </div>
              <div class="portal-stat-item" style="grid-column: 1 / -1;">
                <span class="portal-stat-label">Alamat Domisili / Kantor</span>
                <span class="portal-stat-value {{ !$user->alamat ? 'text-empty-fallback' : '' }}">{{ $user->alamat ?? 'Kantor Desa' }}</span>
              </div>
            </div>
          </div>

          <!-- Section 2: Riwayat & Aktivitas Akun -->
          <div class="portal-section" style="border-top: none; margin-top: 0 !important; padding-top: 36px !important; {{ !$user->pejabat ? 'margin-bottom: 24px;' : '' }}">
            <div class="portal-section-header">
              <h2 class="portal-section-title">
                <span>Riwayat & Aktivitas Akun</span>
              </h2>
            </div>

            <div class="portal-stats-grid" style="grid-template-columns: repeat(2, 1fr); gap: 24px 36px;">
              <div class="portal-stat-item">
                <span class="portal-stat-label">Bergabung Sejak</span>
                <span class="portal-stat-value">{{ $user->created_at ? $user->created_at->format('d M Y, H:i') . ' WITA' : '-' }}</span>
              </div>
              <div class="portal-stat-item">
                <span class="portal-stat-label">Terakhir Diperbarui</span>
                <span class="portal-stat-value">{{ $user->updated_at ? $user->updated_at->format('d M Y, H:i') . ' WITA' : '-' }}</span>
              </div>
              <div class="portal-stat-item" style="grid-column: 1 / -1;">
                <span class="portal-stat-label">Terakhir Login</span>
                <span class="portal-stat-value {{ $user->last_login_at ? '' : 'text-empty-fallback' }}">
                  {{ $user->last_login_at ? $user->last_login_at->format('d M Y, H:i') . ' WITA' : 'Belum pernah login' }}
                </span>
              </div>
            </div>
          </div>

          <!-- Section 3: Profil Pejabat Terkait (Jika ada) -->
          @if($user->pejabat)
          <div class="portal-section" style="border-top: none; margin-top: 0 !important; padding-top: 36px !important; margin-bottom: 24px;">
            <div class="portal-section-header" style="display: flex; justify-content: space-between; align-items: center;">
              <h2 class="portal-section-title">
                <span>Profil Pejabat Terkait</span>
              </h2>
              @if($user->pejabat->id)
              <a href="{{ route('pejabat.show', $user->pejabat->id) }}" style="flex-shrink: 0; font-size: 12px; color: var(--color-primary); font-weight: 600; text-decoration: none; display: inline-flex; align-items: center; gap: 4px; transition: gap 0.15s ease, opacity 0.15s ease;" onmouseenter="this.style.opacity='0.8'; this.style.gap='6px';" onmouseleave="this.style.opacity='1'; this.style.gap='4px';">
                <span>Lihat Profil Pejabat</span>
                <i data-feather="chevron-right" style="width: 13px; height: 13px;"></i>
              </a>
              @endif
            </div>
            <div class="portal-stats-grid" style="grid-template-columns: repeat(2, 1fr); gap: 24px 36px;">
              <div class="portal-stat-item">
                <span class="portal-stat-label">Nama Pejabat</span>
                <span class="portal-stat-value">{{ $user->pejabat->nama }}</span>
              </div>
              <div class="portal-stat-item">
                <span class="portal-stat-label">Jabatan</span>
                <span class="portal-stat-value">{{ $user->pejabat->jabatan }}</span>
              </div>
              <div class="portal-stat-item" style="grid-column: 1 / -1;">
                <span class="portal-stat-label">Tugas Pokok</span>
                <span class="portal-stat-value {{ !$user->pejabat->tugas ? 'text-empty-fallback' : '' }}" style="line-height: 1.6; font-weight: 500;">
                  {{ $user->pejabat->tugas ?: '-' }}
                </span>
              </div>
            </div>
          </div>
          @endif

          <!-- Section: Form Ubah Data Profil (Toggleable di Bagian Bawah Card) -->
          <div class="portal-section" id="profile-edit-section" style="display: none; border-top: none; margin-top: 0 !important; padding-top: 36px !important; margin-bottom: 32px;">
            <div class="portal-section-header" style="margin-bottom: 20px;">
              <h2 class="portal-section-title">
                <span>Ubah Data Profil</span>
              </h2>
            </div>

            @if($errors->has('nama') || $errors->has('username'))
            <div style="background: var(--color-danger-soft, #F3DAD3); border: 1px solid var(--color-danger, #A63D2F); border-radius: 8px; padding: 12px 16px; color: var(--color-danger, #A63D2F); font-size: 13px; margin-bottom: 18px;">
              <strong>Terdapat kesalahan:</strong>
              <ul style="margin: 4px 0 0 16px; padding: 0;">
                @foreach(['nama', 'username'] as $field)
                  @foreach($errors->get($field) as $err)
                    <li>{{ $err }}</li>
                  @endforeach
                @endforeach
              </ul>
            </div>
            @endif

            <form id="edit-profile-form" method="POST" action="{{ route('profile.update') }}" onsubmit="handleProfileSubmit(event)" novalidate>
              @csrf
              @method('PUT')
              <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(280px, 1fr)); gap: 16px; margin-bottom: 24px;">
                <div>
                  <label for="nama" style="display: block; font-size: 13px; font-weight: 600; color: var(--color-text, #16251b); margin-bottom: 6px;">
                    Nama Lengkap <span style="color: var(--color-danger, #A63D2F);">*</span>
                  </label>
                  <input type="text" id="nama" name="nama"
                    class="form-input-sima {{ $errors->has('nama') ? 'is-invalid' : '' }}"
                    value="{{ old('nama', $user->nama) }}" pattern="^[a-zA-Z\s\.,'\-]+$" title="Nama lengkap hanya boleh berisi huruf dan tanda baca nama/gelar (tidak boleh mengandung angka)" minlength="3" required placeholder="Masukkan nama lengkap">
                  @error('nama')
                    <div style="color: var(--color-danger, #A63D2F); font-size: 12px; margin-top: 4px;">{{ $message }}</div>
                  @enderror
                </div>

                <div>
                  <label for="username" style="display: block; font-size: 13px; font-weight: 600; color: var(--color-text, #16251b); margin-bottom: 6px;">
                    Username <span style="color: var(--color-danger, #A63D2F);">*</span>
                  </label>
                  <input type="text" id="username" name="username"
                    class="form-input-sima {{ $errors->has('username') ? 'is-invalid' : '' }}"
                    value="{{ old('username', $user->username) }}" required placeholder="Masukkan username">
                  @error('username')
                    <div style="color: var(--color-danger, #A63D2F); font-size: 12px; margin-top: 4px;">{{ $message }}</div>
                  @enderror
                </div>
              </div>

              <div class="form-actions">
                <button type="button" class="btn-cancel" onclick="toggleEditProfile()">
                  <i data-feather="x"></i>
                  <span>Batal</span>
                </button>
                <button type="submit" class="btn-submit">
                  <i data-feather="save"></i>
                  <span>Simpan Profil</span>
                </button>
              </div>
            </form>
          </div>

        </div><!-- /.portal-card -->

      </div>
    </div>

    @include('layouts.footer-block')
    @include('layouts.footer-js')

    <script>
      function toggleEditProfile() {
        const editSection = document.getElementById('profile-edit-section');
        const btnToggle = document.getElementById('btn-toggle-edit');
        if (editSection.style.display === 'none' || !editSection.style.display) {
          editSection.style.display = 'block';
          btnToggle.innerHTML = '<i data-feather="x" style="width: 14px; height: 14px;"></i> <span>Tutup Edit</span>';
          editSection.scrollIntoView({ behavior: 'smooth', block: 'nearest' });
        } else {
          editSection.style.display = 'none';
          btnToggle.innerHTML = '<i data-feather="edit-2" style="width: 14px; height: 14px;"></i> <span>Edit Profil</span>';
        }
        if (typeof feather !== 'undefined') feather.replace();
      }

      function handleProfileSubmit(e) {
        e.preventDefault();
        const form = document.getElementById('edit-profile-form');
        if (typeof window.simaValidateForm === 'function') {
          if (!window.simaValidateForm(form)) {
            return false;
          }
        } else if (form && !form.checkValidity()) {
          const firstTarget = form.querySelector(':invalid');
          if (firstTarget && typeof window.simaScrollToField === 'function') {
            window.simaScrollToField(firstTarget);
          }
          return false;
        }

        const nama = document.getElementById('nama').value.trim();
        const username = document.getElementById('username').value.trim();

        if (!nama || !username) {
          AppModal.warning('Data Tidak Lengkap', 'Nama Lengkap dan Username wajib diisi.');
          return false;
        }

        AppModal.confirm({
          type: 'info',
          title: 'Update Profil',
          message: 'Apakah Anda yakin ingin update data profil Anda?',
          confirmText: 'Ya, Update',
          cancelText: 'Batal',
          onConfirm: function() {
            form.submit();
          }
        });
        return false;
      }

      @if($errors->has('nama') || $errors->has('username'))
        document.addEventListener('DOMContentLoaded', function() {
          toggleEditProfile();
        });
      @endif
    </script>
  </body>
</html>
