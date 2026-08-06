<!doctype html>
<html lang="en" data-pc-preset="{{ $pc_preset_theme }}" data-pc-theme="{{ $pc_dark_layout }}">
  <head>
    @include('layouts.head-page-meta', ['title' => 'Detail Pejabat'])
    @include('layouts.head-css')
    <style>
      /* ===== LAYOUT CENTER ===== */
      .page-center { max-width:720px; margin:0 auto; }

      /* ===== CARD ===== */
      .d-card { background:#fff; border-radius:12px; border:1px solid #e2e8f0; box-shadow:0 1px 3px rgba(0,0,0,.05); margin-bottom:14px; overflow:hidden; }
      .d-card-header { padding:13px 20px; border-bottom:1px solid #f1f5f9; display:flex; align-items:center; gap:8px; font-size:12px; font-weight:700; color:#064635; text-transform:uppercase; letter-spacing:.5px; }
      .d-card-header i { width:15px; height:15px; }

      /* ===== DETAIL LIST ===== */
      .detail-list { list-style:none; margin:0; padding:0; }
      .detail-list li { display:flex; align-items:flex-start; padding:12px 20px; border-bottom:1px solid #f1f5f9; gap:12px; }
      .detail-list li:last-child { border-bottom:none; }
      .detail-list li:hover { background:#fafbfc; }
      .dl-label { width:165px; flex-shrink:0; font-size:11px; font-weight:700; color:#94a3b8; text-transform:uppercase; letter-spacing:.4px; padding-top:3px; }
      .dl-value { flex:1; font-size:14px; color:#1e293b; font-weight:500; word-break:break-word; }
      .dl-value.empty { color:#cbd5e1; font-style:italic; font-weight:400; }

      /* ===== BADGE ===== */
      .status-badge { display:inline-flex; align-items:center; gap:6px; padding:4px 10px; border-radius:20px; font-size:12px; font-weight:600; }
      .status-badge.active { background:#dcfce7; color:#16a34a; }
      .status-badge.inactive { background:#fee2e2; color:#dc2626; }

      /* ===== TTD ===== */
      .ttd-wrap { padding:20px; text-align:center; }
      .ttd-img { max-width:280px; max-height:180px; border:1px solid #e2e8f0; border-radius:8px; object-fit:contain; }
      .ttd-empty { padding:32px 20px; text-align:center; color:#cbd5e1; }

      /* ===== ACTION BAR ===== */
      .action-bar { background:#fff; border:1px solid #e2e8f0; border-radius:12px; padding:14px 20px; display:flex; gap:10px; align-items:center; flex-wrap:wrap; margin-bottom:14px; box-shadow:0 1px 3px rgba(0,0,0,.05); justify-content:space-between; }
      .action-bar-right { display:flex; gap:10px; align-items:center; }
      .btn { padding:9px 18px; border-radius:8px; font-size:13px; font-weight:600; cursor:pointer; transition:all .2s; display:inline-flex; align-items:center; gap:7px; text-decoration:none; border:none; white-space:nowrap; }
      .btn i { width:15px; height:15px; }
      .btn-back { border:1px solid #d1d5db !important; background:#fff; color:#374151; }
      .btn-back:hover { background:#f4faf5; }
      .btn-edit { background:#064635; color:#fff; }
      .btn-edit:hover { background:#1c5240; }
      .btn-danger { background:#dc2626; color:#fff; }
      .btn-danger:hover { background:#b91c1c; }
    </style>
  </head>

  <body>
    @include('layouts.modals')
    @include('layouts.layout-vertical')

    <div class="pc-container">
      <div class="pc-content">
        @include('layouts.breadcrumb', ['breadcrumbItemHome' => 'Master Data', 'breadcrumbItem' => 'Pejabat', 'breadcrumbItemUrl' => route('pejabat.index'), 'breadcrumbItemActive' => 'Detail Pejabat'])

        <!-- Action Bar -->
        <div class="page-center">
        <div class="action-bar">
          <a href="{{ route('pejabat.index') }}" class="btn btn-back">
            <i data-feather="arrow-left"></i>
            Kembali
          </a>
          @canany(['update', 'delete'], $pejabat)
          <div class="action-bar-right">
            @can('update', $pejabat)
            <a href="{{ route('pejabat.edit', $pejabat->id) }}" class="btn btn-edit">
              <i data-feather="edit-2"></i>
              Edit Pejabat
            </a>
            @endcan
            @can('delete', $pejabat)
            <form method="POST" action="{{ route('pejabat.destroy', $pejabat->id) }}" id="delete-pejabat-form" style="display:inline;">
              @csrf
              @method('DELETE')
              <button type="button" class="btn btn-danger" onclick="confirmDeletePejabat()">
                <i data-feather="trash-2"></i>
                Hapus Pejabat
              </button>
            </form>
            @endcan
          </div>
          @endcanany
        </div>

        <!-- Informasi Pejabat -->
        <div class="d-card">
          <div class="d-card-header">
            <i data-feather="user"></i>
            Informasi Pejabat
          </div>
          <ul class="detail-list">
            <li>
              <span class="dl-label">Nama Lengkap</span>
              <span class="dl-value">{{ $pejabat->nama ?? '-' }}</span>
            </li>
            <li>
              <span class="dl-label">Jabatan</span>
              <span class="dl-value">{{ $pejabat->jabatan ?? '-' }}</span>
            </li>
            <li>
              <span class="dl-label">Alamat</span>
              <span class="dl-value {{ !($pejabat->alamat ?? null) ? 'empty' : '' }}">{{ $pejabat->alamat ?? 'Tidak ada alamat tercatat' }}</span>
            </li>
            <li>
              <span class="dl-label">Tugas</span>
              <span class="dl-value {{ !($pejabat->tugas ?? null) ? 'empty' : '' }}">
                {{ $pejabat->tugas ?? 'Tidak ada tugas tercatat' }}
              </span>
            </li>
            <li>
              <span class="dl-label">Dibuat</span>
              <span class="dl-value">{{ $pejabat->created_at ? $pejabat->created_at->format('d M Y, H:i') : '-' }}</span>
            </li>
            <li>
              <span class="dl-label">Diperbarui</span>
              <span class="dl-value">{{ $pejabat->updated_at ? $pejabat->updated_at->format('d M Y, H:i') : '-' }}</span>
            </li>
          </ul>
        </div>

        <!-- Tanda Tangan Digital -->
        <div class="d-card">
          <div class="d-card-header">
            <i data-feather="pen-tool"></i>
            Tanda Tangan Digital
          </div>
          @if($pejabat->ttd_path)
            <div class="ttd-wrap">
              <img src="{{ route('pejabat.ttd.view', $pejabat->id) }}" alt="Tanda Tangan {{ $pejabat->nama }}" class="ttd-img">
            </div>
          @else
            <div class="ttd-empty">
              <div><i data-feather="file-minus" style="display:block;margin:0 auto 8px;width:36px;height:36px;color:#cbd5e1;"></i></div>
              <div style="font-size:13px;color:#94a3b8;">Tanda tangan digital belum diunggah</div>
            </div>
          @endif
        </div>

        </div><!-- /.page-center -->
      </div>
    </div>

    @include('layouts.footer-block')
    @include('layouts.footer-js')

    <script>
      function confirmDeletePejabat() {
        if (!document.getElementById('delete-pejabat-form')) return;
        AppModal.confirmDelete('pejabat "{{ addslashes($pejabat->nama) }}"', function() {
          document.getElementById('delete-pejabat-form').submit();
        });
      }
    </script>
  </body>
</html>
