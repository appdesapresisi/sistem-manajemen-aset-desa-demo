<!doctype html>
<html lang="en" data-pc-preset="{{ $pc_preset_theme }}" data-pc-theme="{{ $pc_dark_layout }}">
  <head>
    @include('layouts.head-page-meta', ['title' => 'Detail Bidang'])
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
      .dl-value { flex:1; font-size:14px; color:#1e293b; font-weight:500; word-break:break-word; line-height:1.5; }
      .dl-value.empty { color:#cbd5e1; font-style:italic; font-weight:400; }

      /* ===== BADGE ===== */
      .info-badge { display:inline-flex; align-items:center; gap:6px; padding:6px 12px; border-radius:20px; font-size:12px; font-weight:600; background:linear-gradient(135deg,#dbeafe,#bfdbfe); color:#2563eb; }
      .success-badge { display:inline-flex; align-items:center; gap:6px; padding:6px 12px; border-radius:20px; font-size:12px; font-weight:600; background:linear-gradient(135deg,#d1fae5,#a7f3d0); color:#059669; }
      .warning-badge { display:inline-flex; align-items:center; gap:6px; padding:6px 12px; border-radius:20px; font-size:12px; font-weight:600; background:linear-gradient(135deg,#fef3c7,#fde68a); color:#d97706; }
      .info-badge i, .success-badge i, .warning-badge i { width:13px; height:13px; }

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

      /* ===== USER TABLE ===== */
      .aset-table { width:100%; border-collapse:collapse; font-size:13px; }
      .aset-table thead th { padding:10px 20px; font-size:11px; font-weight:700; color:#94a3b8; text-transform:uppercase; letter-spacing:.4px; text-align:left; border-bottom:1px solid #f1f5f9; }
      .aset-table tbody td { padding:12px 20px; border-bottom:1px solid #f1f5f9; color:#1e293b; }
      .aset-table tbody tr:last-child td { border-bottom:none; }
      .aset-table tbody tr:hover { background:#fafbfc; }

      .empty-state { 
        text-align: center !important; 
        padding: 40px 20px; 
        color: #94a3b8; 
        display: flex !important;
        flex-direction: column !important;
        align-items: center !important;
        justify-content: center !important;
      }
      .empty-state i { 
        width: 48px !important; 
        height: 48px !important; 
        margin: 0 auto 12px !important; 
        opacity: 0.5; 
        display: block !important; 
      }
      .empty-state p { 
        margin: 0; 
        font-size: 14px; 
      }

      @media (max-width: 640px) {
        .aset-table { font-size:12px; }
      }
    </style>
  </head>

  <body>
    @include('layouts.modals')
    @include('layouts.layout-vertical')

    <div class="pc-container">
      <div class="pc-content">
        @include('layouts.breadcrumb', ['breadcrumbItemHome' => 'Master Data', 'breadcrumbItem' => 'Bidang', 'breadcrumbItemUrl' => route('bidangs.index'), 'breadcrumbItemActive' => 'Detail Bidang'])

        <!-- Action Bar -->
        <div class="page-center">
        <div class="action-bar">
          <a href="{{ route('bidangs.index') }}" class="btn btn-back">
            <i data-feather="arrow-left"></i>
            Kembali
          </a>
          @canany(['update', 'delete'], $bidang)
          <div class="action-bar-right">
            @can('update', $bidang)
            <a href="{{ route('bidangs.edit', $bidang->id) }}" class="btn btn-edit">
              <i data-feather="edit-2"></i>
              Edit Bidang
            </a>
            @endcan
            @can('delete', $bidang)
            <form method="POST" action="{{ route('bidangs.destroy', $bidang->id) }}" id="delete-bidang-form" style="display:inline;">
              @csrf
              @method('DELETE')
              <button type="button" class="btn btn-danger" onclick="confirmDeleteBidang({{ $bidang->users_count }}, '{{ $bidang->nama }}')">
                <i data-feather="trash-2"></i>
                Hapus Bidang
              </button>
            </form>
            @endcan
          </div>
          @endcanany
        </div>

        <!-- Informasi Bidang -->
        <div class="d-card">
          <div class="d-card-header">
            <i data-feather="briefcase"></i>
            Informasi Bidang
          </div>
          <ul class="detail-list">
            <li>
              <span class="dl-label">Nama Bidang</span>
              <span class="dl-value">{{ $bidang->nama }}</span>
            </li>
            <li>
              <span class="dl-label">Deskripsi</span>
              <span class="dl-value {{ !$bidang->deskripsi ? 'empty' : '' }}">
                {{ $bidang->deskripsi ?? 'Tidak ada deskripsi' }}
              </span>
            </li>
            <li>
              <span class="dl-label">Status Penggunaan</span>
              <span class="dl-value">
                {{ $bidang->users_count }} user
              </span>
            </li>
            <li>
              <span class="dl-label">Dibuat Pada</span>
              <span class="dl-value">{{ $bidang->created_at->format('d F Y, H:i') }} WIB</span>
            </li>
            <li>
              <span class="dl-label">Terakhir Diubah</span>
              <span class="dl-value">{{ $bidang->updated_at->format('d F Y, H:i') }} WIB</span>
            </li>
          </ul>
        </div>

        <!-- Daftar User di Bidang Ini -->
        <div class="d-card">
          <div class="d-card-header">
            <i data-feather="users"></i>
            Daftar User di Bidang Ini
            <span style="margin-left:auto;color:#064635;font-size:12px;font-weight:600;text-transform:none;letter-spacing:normal;">
              {{ $bidang->users_count }} User
            </span>
          </div>
          @if($bidang->users->count() > 0)
            <table class="aset-table">
              <thead>
                <tr>
                  <th>No</th>
                  <th>Nama</th>
                  <th>Role</th>
                  <th>Aksi</th>
                </tr>
              </thead>
              <tbody>
                @foreach($bidang->users as $index => $user)
                <tr>
                  <td>{{ $index + 1 }}</td>
                  <td>{{ $user->nama }}</td>
                  <td>{{ $user->role ? ucwords(str_replace('_', ' ', $user->role->nama)) : '-' }}</td>
                  <td>
                    <a href="{{ route('users.show', $user->id) }}" style="font-size:12px;color:#064635;font-weight:600;text-decoration:none;">
                      Lihat &rarr;
                    </a>
                  </td>
                </tr>
                @endforeach
              </tbody>
            </table>
          @else
            <div class="empty-state">
              <i data-feather="users"></i>
              <p>Belum ada user yang terdaftar di bidang ini</p>
            </div>
          @endif
        </div>

        </div>
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

      // Initialize feather icons
      if (typeof feather !== 'undefined') {
        feather.replace();
      }
    </script>
  </body>
</html>
