<!doctype html>
<html lang="en" data-pc-preset="{{ $pc_preset_theme }}" data-pc-theme="{{ $pc_dark_layout }}">
  <head>
    @include('layouts.head-page-meta', ['title' => 'Detail Pejabat — ' . ($pejabat->nama ?? 'Pejabat')])
    @include('layouts.head-css')
  </head>

  <body data-pc-preset="{{ $pc_preset_theme }}" data-pc-sidebar-theme="{{ $pc_sidebar_theme ?? 'light' }}">
    @include('layouts.notification')
    @include('layouts.modals')
    @include('layouts.layout-vertical')

    <div class="pc-container">
      <div class="pc-content">
        @include('layouts.breadcrumb', [
          'breadcrumbItemHome' => 'Master Data',
          'breadcrumbItem' => 'Manajemen Pejabat',
          'breadcrumbItemUrl' => route('pejabat.index'),
          'breadcrumbItemActive' => 'Detail Pejabat'
        ])

        <!-- Action Bar Top -->
        <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 20px; flex-wrap: wrap; gap: 12px;">
          <a href="{{ route('pejabat.index') }}" class="btn-sima-secondary" style="border: none !important; padding-left: 0 !important; background-color: transparent !important;" onclick="try { sessionStorage.setItem('pejabat_returning', '1'); } catch(e) {} if (window.history.length > 1 && document.referrer && document.referrer.includes('/pejabat')) { window.history.back(); return false; } return true;">
            <i data-feather="arrow-left" style="width: 14px; height: 14px;"></i>
            <span>Kembali ke Daftar Pejabat</span>
          </a>
          @canany(['update', 'delete'], $pejabat)
          <div style="display: flex; gap: 10px; align-items: center;">
            @can('update', $pejabat)
            <a href="{{ route('pejabat.edit', $pejabat->id) }}" class="btn-sima-primary">
              <i data-feather="edit-2" style="width: 14px; height: 14px;"></i>
              <span>Edit Pejabat</span>
            </a>
            @endcan
            @can('delete', $pejabat)
            <form method="POST" action="{{ route('pejabat.destroy', $pejabat->id) }}" id="delete-pejabat-form" style="display:inline;">
              @csrf
              @method('DELETE')
              <button type="button" class="btn-sima-danger" onclick="confirmDeletePejabat()">
                <i data-feather="trash-2" style="width: 14px; height: 14px;"></i>
                <span>Hapus Pejabat</span>
              </button>
            </form>
            @endcan
          </div>
          @endcanany
        </div>

        <!-- ===== MAIN PORTAL CARD ===== -->
        <div class="portal-card">

          <!-- Hero 2-Column: Left (TTD Preview) & Right (Profile Overview) -->
          <div class="portal-hero-grid" style="padding-top: 32px; margin-bottom: 0;">

            <!-- KIRI: Tanda Tangan Digital -->
            <div class="portal-gallery">
              <div class="portal-gallery-main">
                @if($pejabat->ttd_path)
                  <img src="{{ route('pejabat.ttd.view', $pejabat->id) }}" 
                       alt="Tanda Tangan {{ $pejabat->nama }}" 
                       onclick="if(window.openSimaImageViewer) openSimaImageViewer('{{ route('pejabat.ttd.view', $pejabat->id) }}', 0, 'Tanda Tangan &bull; {{ addslashes($pejabat->nama) }}')" 
                       title="Klik untuk memperbesar (Layar Penuh)" 
                       style="max-height: 250px; width: auto; object-fit: contain; margin: auto; cursor: zoom-in;">
                @else
                  <div class="portal-gallery-placeholder">
                    <i data-feather="edit-3"></i>
                    <div style="font-weight: 600; color: var(--color-text);">Tanda Tangan Digital Kosong</div>
                    <div style="font-size: 12px; color: var(--color-text-muted);">Unggah tanda tangan resmi melalui menu Edit Pejabat</div>
                  </div>
                @endif
              </div>
            </div>

            <!-- KANAN: Ringkasan Profil & Tugas -->
            <div class="portal-overview-column">
              <div>
                <div class="portal-overview-title">
                  <span>Informasi Pejabat Desa</span>
                </div>
                <div class="portal-stats-grid" style="grid-template-columns: repeat(2, 1fr); gap: 24px 36px;">
                  <div class="portal-stat-item">
                    <span class="portal-stat-label">Nama Lengkap</span>
                    <span class="portal-stat-value">{{ $pejabat->nama ?? '-' }}</span>
                  </div>

                  <div class="portal-stat-item">
                    <span class="portal-stat-label">Jabatan</span>
                    <span class="portal-stat-value">{{ $pejabat->jabatan ?? '-' }}</span>
                  </div>

                  <div class="portal-stat-item">
                    <span class="portal-stat-label">Status Pejabat</span>
                    <span class="portal-stat-value">
                      <span class="status-pill {{ $pejabat->is_aktif ? 'status-pill-success' : 'status-pill-danger' }}" style="font-size: 12px; padding: 3px 12px; display: inline-flex; align-items: center; gap: 6px;">
                        {{ $pejabat->is_aktif ? 'Aktif' : 'Nonaktif' }}
                      </span>
                    </span>
                  </div>

                  <div class="portal-stat-item">
                    <span class="portal-stat-label">Pengurus Barang Desa</span>
                    <span class="portal-stat-value">
                      @if($pejabat->is_petugas_barang)
                        <span class="status-pill status-pill-success" style="font-size: 12px; padding: 3px 12px; display: inline-flex; align-items: center; gap: 6px;">
                          <i data-feather="check-circle" style="width: 12px; height: 12px;"></i>
                          Petugas Pengurus Barang
                        </span>
                      @else
                        <span>
                          Bukan Pengurus Barang
                        </span>
                      @endif
                    </span>
                  </div>

                  <div class="portal-stat-item" style="grid-column: 1 / -1;">
                    <span class="portal-stat-label">Alamat</span>
                    <span class="portal-stat-value {{ !$pejabat->alamat ? 'text-empty-fallback' : '' }}">{{ $pejabat->alamat ?: 'Tidak ada alamat tercatat' }}</span>
                  </div>

                  <div class="portal-stat-item" style="grid-column: 1 / -1;">
                    <span class="portal-stat-label">Tugas & Wewenang</span>
                    <span class="portal-stat-value {{ !$pejabat->tugas ? 'text-empty-fallback' : '' }}" style="line-height: 1.6; font-weight: 500;">
                      {{ $pejabat->tugas ?: 'Belum ada catatan tugas & wewenang spesifik.' }}
                    </span>
                  </div>
                </div>
              </div>
            </div>

          </div>

          <!-- Section: Audit Log & Catatan Sistem -->
          <div class="portal-section" style="border-top: none; margin-top: 0; padding-top: 36px; margin-bottom: 32px;">
            <div class="portal-section-header">
              <h2 class="portal-section-title">
                <span>Informasi Sistem & Audit Data</span>
              </h2>
            </div>
            <div class="portal-kv-grid grid-4" style="font-size: 13px;">
              <div class="portal-kv-item">
                <span class="portal-kv-label">ID Sistem</span>
                <span class="portal-kv-value" style="font-family: monospace;">#{{ $pejabat->id }}</span>
              </div>
              <div class="portal-kv-item">
                <span class="portal-kv-label">Status Keaktifan</span>
                <span class="portal-kv-value">
                  <span class="status-pill {{ $pejabat->is_aktif ? 'status-pill-success' : 'status-pill-danger' }}" style="font-size: 11px; padding: 2px 8px;">
                    {{ $pejabat->is_aktif ? 'Aktif' : 'Nonaktif' }}
                  </span>
                </span>
              </div>
              <div class="portal-kv-item">
                <span class="portal-kv-label">Tanggal Dibuat</span>
                <span class="portal-kv-value">{{ $pejabat->created_at ? $pejabat->created_at->format('d M Y, H:i') : '-' }}</span>
              </div>
              <div class="portal-kv-item">
                <span class="portal-kv-label">Terakhir Diperbarui</span>
                <span class="portal-kv-value">{{ $pejabat->updated_at ? $pejabat->updated_at->format('d M Y, H:i') : '-' }}</span>
              </div>
            </div>
          </div>

        </div><!-- /.portal-card -->
      </div>
    </div>

    @include('layouts.footer-block')
    @include('layouts.footer-js')

    <script>
      try { sessionStorage.setItem('pejabat_returning', '1'); } catch(e) {}
      function confirmDeletePejabat() {
        if (!document.getElementById('delete-pejabat-form')) return;
        AppModal.confirmDelete('pejabat "{{ addslashes($pejabat->nama) }}"', function() {
          document.getElementById('delete-pejabat-form').submit();
        });
      }
    </script>
  </body>
</html>
