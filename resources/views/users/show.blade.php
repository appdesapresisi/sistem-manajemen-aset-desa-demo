<!doctype html>
<html lang="en" data-pc-preset="{{ $pc_preset_theme }}" data-pc-theme="{{ $pc_dark_layout }}">
  <head>
    @include('layouts.head-page-meta', ['title' => 'Detail User — ' . ($user->nama ?? $user->username)])
    @include('layouts.head-css')
    <style>
      /* Switch */
      .toggle-switch { position: relative; width: 48px; height: 26px; flex-shrink: 0; cursor: pointer; }
      .toggle-switch input { opacity: 0; width: 0; height: 0; position: absolute; }
      .toggle-slider { position: absolute; inset: 0; border-radius: 26px; cursor: pointer; transition: .25s; background: var(--color-border, #cfdcc8); }
      .toggle-slider:before { content: ''; position: absolute; width: 20px; height: 20px; left: 3px; bottom: 3px; border-radius: 50%; background: var(--color-surface, #f8faf7); transition: .25s; box-shadow: none; }
      .toggle-switch input:checked + .toggle-slider { background: var(--color-primary, #1f3a2a); }
      .toggle-switch input:checked + .toggle-slider:before { transform: translateX(22px); }
      .toggle-switch input:disabled + .toggle-slider { opacity: .5; cursor: not-allowed; }
    </style>
  </head>

  <body data-pc-preset="{{ $pc_preset_theme }}" data-pc-sidebar-theme="{{ $pc_sidebar_theme ?? 'light' }}">
    @include('layouts.notification')
    @include('layouts.modals')
    @include('layouts.layout-vertical')

    <div class="pc-container">
      <div class="pc-content">
        @include('layouts.breadcrumb', [
          'breadcrumbItemHome' => 'Master Data',
          'breadcrumbItem' => 'Manajemen Pengguna',
          'breadcrumbItemUrl' => route('users.index'),
          'breadcrumbItemActive' => 'Detail Pengguna'
        ])

        <!-- Top Action Bar -->
        <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 20px; flex-wrap: wrap; gap: 12px;">
          <a href="{{ route('users.index') }}" class="btn-sima-secondary" style="border: none !important; padding-left: 0 !important; background-color: transparent !important;" onclick="try { sessionStorage.setItem('users_returning', '1'); } catch(e) {} if (window.history.length > 1 && document.referrer && document.referrer.includes('/users')) { window.history.back(); return false; } return true;">
            <i data-feather="arrow-left" style="width: 14px; height: 14px;"></i>
            <span>Kembali ke Daftar Pengguna</span>
          </a>
          @canany(['update', 'delete'], $user)
          <div style="display: flex; gap: 10px; align-items: center;">
            @can('update', $user)
            <a href="{{ route('users.edit', $user->id) }}" class="btn-sima-primary">
              <i data-feather="edit-2" style="width: 14px; height: 14px;"></i>
              <span>Edit User</span>
            </a>
            @endcan
            @can('delete', $user)
            <form method="POST" action="{{ route('users.destroy', $user->id) }}" id="delete-user-form" style="display:inline;">
              @csrf
              @method('DELETE')
              <button type="button" class="btn-sima-danger" onclick="confirmDeleteUser()">
                <i data-feather="trash-2" style="width: 14px; height: 14px;"></i>
                <span>Hapus User</span>
              </button>
            </form>
            @endcan
          </div>
          @endcanany
        </div>

        <!-- ===== MAIN PORTAL CARD ===== -->
        <div class="portal-card">

          <!-- Section: Profil Pengguna -->
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
                <span class="portal-stat-value">{{ $user->role_info['name'] ?? ($user->role->nama ?? '-') }}</span>
              </div>
              <div class="portal-stat-item">
                <span class="portal-stat-label">Status Akun</span>
                <span class="portal-stat-value">
                  <span class="status-pill {{ $user->is_aktif ? 'status-pill-success' : 'status-pill-danger' }}" style="font-size: 11.5px; padding: 2px 10px;">
                    {{ $user->is_aktif ? 'Aktif' : 'Nonaktif' }}
                  </span>
                </span>
              </div>
              <div class="portal-stat-item">
                <span class="portal-stat-label">Terakhir Login</span>
                <span class="portal-stat-value {{ $user->last_login_at ? '' : 'text-empty-fallback' }}">
                  {{ $user->last_login_at ? $user->last_login_at->format('d M Y, H:i') : 'Belum pernah login' }}
                </span>
              </div>
              <div class="portal-stat-item">
                <span class="portal-stat-label">Alamat</span>
                <span class="portal-stat-value {{ !$user->alamat ? 'text-empty-fallback' : '' }}">{{ $user->alamat ?: 'Tidak ada alamat' }}</span>
              </div>
            </div>
          </div>

          <!-- Section: Pejabat Terkait (Jika ada) -->
          @if($user->pejabat)
          <div class="portal-section" style="border-top: none; margin-top: 0; padding-top: 36px;">
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
          <!-- Section: Audit Data -->
          <div class="portal-section" style="border-top: none; margin-top: 0; padding-top: 36px; margin-bottom: 32px;">
            <div class="portal-section-header">
              <h2 class="portal-section-title">
                <span>Informasi Sistem & Audit Data</span>
              </h2>
            </div>
            <div style="display: flex; justify-content: space-evenly; gap: 36px; font-size: 12.5px; flex-wrap: wrap;">
              <div class="portal-kv-item" style="flex: 1; min-width: 180px;">
                <span class="portal-kv-label">User ID</span>
                <span class="portal-kv-value" style="font-family: monospace;">#{{ $user->id }}</span>
              </div>
              <div class="portal-kv-item" style="flex: 1; min-width: 180px;">
                <span class="portal-kv-label">Tanggal Dibuat</span>
                <span class="portal-kv-value">{{ $user->created_at ? $user->created_at->format('d M Y, H:i') : '-' }}</span>
              </div>
              <div class="portal-kv-item" style="flex: 1; min-width: 180px;">
                <span class="portal-kv-label">Terakhir Diperbarui</span>
                <span class="portal-kv-value">{{ $user->updated_at ? $user->updated_at->format('d M Y, H:i') : '-' }}</span>
              </div>
            </div>
          </div>

        </div><!-- /.portal-card -->
      </div>
    </div>

    @include('layouts.footer-block')
    @include('layouts.footer-js')

    <script>
      try { sessionStorage.setItem('users_returning', '1'); } catch(e) {}
      function confirmDeleteUser() {
        if (!document.getElementById('delete-user-form')) return;
        AppModal.confirmDelete('user "{{ addslashes($user->nama ?? $user->username) }}"', function() {
          document.getElementById('delete-user-form').submit();
        });
      }

      function confirmToggleAktifShow(userName, currentlyActive) {
        const willBeActive = !currentlyActive;
        const title  = willBeActive ? 'Aktifkan User' : 'Nonaktifkan User';
        const msg    = willBeActive
          ? `Aktifkan user <strong>${userName}</strong>? User akan dapat login kembali ke sistem.`
          : `Nonaktifkan user <strong>${userName}</strong>? User tidak akan dapat login ke sistem sampai diaktifkan kembali.`;

        // Kembalikan checkbox ke posisi semula dulu (sebelum submit)
        const checkbox = event.target;
        checkbox.checked = currentlyActive;

        AppModal.show({
          type: willBeActive ? 'success' : 'warning',
          title: title,
          message: msg,
          confirmText: willBeActive ? 'Ya, Aktifkan' : 'Ya, Nonaktifkan',
          cancelText: 'Batal',
          showCancel: true,
          onConfirm: function() { document.getElementById('toggle-aktif-form').submit(); }
        });
      }
    </script>
  </body>
</html>
