<!doctype html>
<html lang="en" data-pc-preset="{{ $pc_preset_theme }}" data-pc-theme="{{ $pc_dark_layout }}">
  <head>
    @include('layouts.head-page-meta', ['title' => 'Detail Bidang — ' . ($bidang->nama ?? 'Bidang')])
    @include('layouts.head-css')
    <style>


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
          'breadcrumbItem' => 'Manajemen Bidang',
          'breadcrumbItemUrl' => route('bidangs.index'),
          'breadcrumbItemActive' => 'Detail Bidang'
        ])

        <!-- Top Action Bar -->
        <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 20px; flex-wrap: wrap; gap: 12px;">
          <a href="{{ route('bidangs.index') }}" class="btn-sima-secondary" style="border: none !important; padding-left: 0 !important; background-color: transparent !important;" onclick="try { sessionStorage.setItem('bidang_returning', '1'); } catch(e) {} if (window.history.length > 1 && document.referrer && document.referrer.includes('/bidangs')) { window.history.back(); return false; } return true;">
            <i data-feather="arrow-left" style="width: 14px; height: 14px;"></i>
            <span>Kembali ke Daftar Bidang</span>
          </a>
          @canany(['update', 'delete'], $bidang)
          <div style="display: flex; gap: 10px; align-items: center;">
            @can('update', $bidang)
            <a href="{{ route('bidangs.edit', $bidang->id) }}" class="btn-sima-primary">
              <i data-feather="edit-2" style="width: 14px; height: 14px;"></i>
              <span>Edit Bidang</span>
            </a>
            @endcan
            @can('delete', $bidang)
            <form method="POST" action="{{ route('bidangs.destroy', $bidang->id) }}" id="delete-bidang-form" style="display:inline;">
              @csrf
              @method('DELETE')
              <button type="button" class="btn-sima-danger" onclick="confirmDeleteBidang({{ $bidang->users_count }}, '{{ addslashes($bidang->nama) }}')">
                <i data-feather="trash-2" style="width: 14px; height: 14px;"></i>
                <span>Hapus Bidang</span>
              </button>
            </form>
            @endcan
          </div>
          @endcanany
        </div>

        <!-- ===== MAIN PORTAL CARD ===== -->
        <div class="portal-card">

          <!-- Section: Informasi Bidang -->
          <div class="portal-section" style="padding-top: 0; border-top: none; margin-top: 32px;">
            <div class="portal-section-header">
              <h2 class="portal-section-title">
                <span>Informasi Bidang</span>
              </h2>
            </div>
            <div class="portal-stats-grid" style="grid-template-columns: repeat(2, 1fr); gap: 24px 36px;">
              <div class="portal-stat-item">
                <span class="portal-stat-label">Nama Bidang</span>
                <span class="portal-stat-value">{{ $bidang->nama ?? '-' }}</span>
              </div>
              <div class="portal-stat-item">
                <span class="portal-stat-label">Total Pengguna Terdaftar</span>
                <span class="portal-stat-value">{{ $bidang->users_count ?? 0 }} pengguna</span>
              </div>
              <div class="portal-stat-item" style="grid-column: 1 / -1;">
                <span class="portal-stat-label">Deskripsi</span>
                <span class="portal-stat-value {{ !$bidang->deskripsi ? 'text-empty-fallback' : '' }}">
                  {{ $bidang->deskripsi ?: 'Tidak ada deskripsi untuk bidang ini.' }}
                </span>
              </div>
            </div>
          </div>

          <!-- Section: Daftar User di Bidang Ini -->
          <div class="portal-section" style="border-top: none; margin-top: 0; padding-top: 36px;">
            <div class="portal-section-header">
              <h2 class="portal-section-title">
                <span>Daftar User di Bidang Ini ({{ $bidang->users->count() }})</span>
              </h2>
            </div>

            @if($bidang->users->count() > 0)
              <div style="display: flex; flex-direction: column; margin-top: 6px;">
                @foreach($bidang->users as $index => $user)
                <div style="display: flex; align-items: center; justify-content: space-between; font-size: 13.5px; line-height: 1.5; color: var(--color-text); padding: 12px 0; border-bottom: 1px dashed var(--color-border, #cfdcc8);">
                  <div style="display: flex; align-items: center; min-width: 0; padding-right: 12px;">
                    <span style="color: var(--color-text-muted); font-variant-numeric: tabular-nums; min-width: 24px; font-weight: 500; flex-shrink: 0;">
                      {{ $index + 1 }}.
                    </span>
                    <span style="font-weight: 600; color: var(--color-text);">
                      {{ $user->nama }}
                    </span>
                    <span style="color: var(--color-text-muted); font-size: 13px; font-weight: 500; margin-left: 6px;">
                      ({{ $user->role ? ucwords(str_replace('_', ' ', $user->role->nama)) : '-' }})
                    </span>
                  </div>
                  <a href="{{ route('users.show', $user->id) }}" style="flex-shrink: 0; font-size: 12px; color: var(--color-primary); font-weight: 600; text-decoration: none; display: inline-flex; align-items: center; gap: 4px; transition: gap 0.15s ease, opacity 0.15s ease;" onmouseenter="this.style.opacity='0.8'; this.style.gap='6px';" onmouseleave="this.style.opacity='1'; this.style.gap='4px';">
                    <span>Lihat detail</span>
                    <i data-feather="chevron-right" style="width: 13px; height: 13px;"></i>
                  </a>
                </div>
                @endforeach
              </div>
            @else
              <div class="empty-state-sima">
                <i data-feather="users" style="width: 42px; height: 42px; stroke-width: 1.5; color: var(--color-border-dark);"></i>
                <p>Belum ada user yang terdaftar di bidang ini.</p>
              </div>
            @endif
          </div>

          <!-- Section: Audit Data -->
          <div class="portal-section" style="border-top: none; margin-top: 0; padding-top: 36px; margin-bottom: 32px;">
            <div class="portal-section-header">
              <h2 class="portal-section-title">
                <span>Informasi Sistem & Audit Data</span>
              </h2>
            </div>
            <div style="display: flex; justify-content: space-evenly; gap: 36px; font-size: 12.5px; flex-wrap: wrap;">
              <div class="portal-kv-item" style="flex: 1; min-width: 180px;">
                <span class="portal-kv-label">ID Bidang</span>
                <span class="portal-kv-value" style="font-family: monospace;">#{{ $bidang->id }}</span>
              </div>
              <div class="portal-kv-item" style="flex: 1; min-width: 180px;">
                <span class="portal-kv-label">Tanggal Dibuat</span>
                <span class="portal-kv-value">{{ $bidang->created_at ? $bidang->created_at->format('d M Y, H:i') : '-' }}</span>
              </div>
              <div class="portal-kv-item" style="flex: 1; min-width: 180px;">
                <span class="portal-kv-label">Terakhir Diperbarui</span>
                <span class="portal-kv-value">{{ $bidang->updated_at ? $bidang->updated_at->format('d M Y, H:i') : '-' }}</span>
              </div>
            </div>
          </div>

        </div><!-- /.portal-card -->

      </div>
    </div>

    @include('layouts.footer-block')
    @include('layouts.footer-js')

    <script>
      function confirmDeleteBidang(usersCount, bidangNama) {
        if (usersCount > 0) {
          AppModal.error('Tidak Dapat Dihapus',
            `Bidang <strong>${bidangNama}</strong> masih memiliki <strong>${usersCount} user</strong> yang terkait.<br><br>Silakan pindahkan atau hapus user tersebut terlebih dahulu.`);
          return;
        }

        AppModal.confirmDelete(bidangNama, function() {
          document.getElementById('delete-bidang-form').submit();
        });
      }
      try { sessionStorage.setItem('bidang_returning', '1'); } catch(e) {}
    </script>
  </body>
</html>
