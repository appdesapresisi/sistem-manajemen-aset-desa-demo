<!doctype html>
<html lang="en" data-pc-preset="{{ $pc_preset_theme }}" data-pc-theme="{{ $pc_dark_layout }}">
  <head>
    @include('layouts.head-page-meta', ['title' => 'Detail User'])
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
      .status-badge { display:inline-flex; align-items:center; gap:6px; padding:6px 12px; border-radius:20px; font-size:12px; font-weight:600; }
      .status-badge.active { background:#ecfdf5; color:#059669; }
      .status-badge.inactive { background:#fef2f2; color:#dc2626; }
      .status-dot { width:8px; height:8px; border-radius:50%; }
      .status-badge.active .status-dot { background:#10b981; }
      .status-badge.inactive .status-dot { background:#ef4444; }
      .role-badge { display:inline-flex; align-items:center; gap:6px; padding:6px 12px; border-radius:20px; font-size:12px; font-weight:600; }
      .role-badge.super-admin { background:linear-gradient(135deg,#f3e8ff,#e9d5ff); color:#7c3aed; }
      .role-badge.perbekel    { background:linear-gradient(135deg,#f3e8ff,#e9d5ff); color:#7c3aed; }
      .role-badge.admin       { background:linear-gradient(135deg,#dbeafe,#bfdbfe); color:#2563eb; }
      .role-badge.operator    { background:linear-gradient(135deg,#d1fae5,#a7f3d0); color:#059669; }
      .role-badge.viewer      { color:#6b7280; }

      /* ===== TOGGLE AKTIF ===== */
      .toggle-section { margin:0 20px 16px; padding:14px 16px; background:#f8f9fa; border-radius:10px; display:flex; align-items:center; justify-content:space-between; gap:12px; }
      .toggle-section-info { flex:1; }
      .toggle-section-title { font-size:13px; font-weight:700; color:#1e293b; margin-bottom:3px; display:flex; align-items:center; gap:6px; }
      .toggle-section-title i { width:14px; height:14px; }
      .toggle-section-desc { font-size:11px; color:#64748b; line-height:1.4; }
      .toggle-section-desc.danger { color:#dc2626; }
      /* Switch */
      .toggle-switch { position:relative; width:48px; height:26px; flex-shrink:0; }
      .toggle-switch input { opacity:0; width:0; height:0; position:absolute; }
      .toggle-slider { position:absolute; inset:0; border-radius:26px; cursor:pointer; transition:.25s; background:#d1d5db; }
      .toggle-slider:before { content:''; position:absolute; width:20px; height:20px; left:3px; bottom:3px; border-radius:50%; background:#fff; transition:.25s; box-shadow:0 1px 3px rgba(0,0,0,.2); }
      .toggle-switch input:checked + .toggle-slider { background:#10b981; }
      .toggle-switch input:checked + .toggle-slider:before { transform:translateX(22px); }
      .toggle-switch input:disabled + .toggle-slider { opacity:.5; cursor:not-allowed; }

      .pejabat-linked-card { display:flex; align-items:flex-start; gap:12px; padding:14px 20px; }
      .pejabat-icon { width:44px; height:44px; border-radius:10px; background:linear-gradient(135deg,#064635,#519259); display:flex; align-items:center; justify-content:center; color:#fff; flex-shrink:0; }
      .pejabat-icon i, .pejabat-icon svg { width:20px; height:20px; }
      .pejabat-info { flex:1; min-width:0; }
      .pejabat-name { font-size:14px; font-weight:600; color:#1e293b; }
      .pejabat-jabatan { font-size:12px; color:#64748b; margin-top:2px; }
      .pejabat-tugas { font-size:12px; color:#64748b; margin-top:4px; line-height:1.5; }
      .pejabat-linked-card .btn { margin-left:auto; align-self:flex-start; }

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

      @media (max-width: 640px) {
        .pejabat-linked-card { flex-direction:column; align-items:flex-start; }
        .pejabat-linked-card .btn { margin-left:0; margin-top:6px; }
      }
    </style>
  </head>

  <body>
    @include('layouts.modals')
    @include('layouts.layout-vertical')

    <div class="pc-container">
      <div class="pc-content">
        @include('layouts.breadcrumb', ['breadcrumbItemHome' => 'Master Data', 'breadcrumbItem' => 'Users', 'breadcrumbItemUrl' => route('users.index'), 'breadcrumbItemActive' => 'Detail User'])

        <!-- Action Bar -->
        <div class="page-center">
        <div class="action-bar">
          <a href="{{ route('users.index') }}" class="btn btn-back">
            <i data-feather="arrow-left"></i>
            Kembali
          </a>
          @canany(['update', 'delete'], $user)
          <div class="action-bar-right">
            @can('update', $user)
            <a href="{{ route('users.edit', $user->id) }}" class="btn btn-edit">
              <i data-feather="edit-2"></i>
              Edit User
            </a>
            @endcan
            @can('delete', $user)
            <form method="POST" action="{{ route('users.destroy', $user->id) }}" id="delete-user-form" style="display:inline;">
              @csrf
              @method('DELETE')
              <button type="button" class="btn btn-danger" onclick="confirmDeleteUser()">
                <i data-feather="trash-2"></i>
                Hapus User
              </button>
            </form>
            @endcan
          </div>
          @endcanany
        </div>

        <!-- Informasi User -->
        <div class="d-card">
          <div class="d-card-header">
            <i data-feather="user"></i>
            Informasi User
          </div>
          <ul class="detail-list">
            <li>
              <span class="dl-label">Nama Lengkap</span>
              <span class="dl-value">{{ $user->nama ?? '-' }}</span>
            </li>
            <li>
              <span class="dl-label">Username</span>
              <span class="dl-value">{{ $user->username ?? '-' }}</span>
            </li>
            <li>
              <span class="dl-label">Alamat</span>
              <span class="dl-value {{ !($user->alamat ?? null) ? 'empty' : '' }}">{{ $user->alamat ?? 'Tidak ada alamat tercatat' }}</span>
            </li>
            <li>
              <span class="dl-label">Role</span>
              <span class="dl-value">
                @if($user->role)
                  {{ $user->role_info['name'] ?? ucwords(str_replace('_', ' ', $user->role->nama ?? 'User')) }}
                @else
                  <span class="dl-value empty">Tidak ada role</span>
                @endif
              </span>
            </li>
            <li>
              <span class="dl-label">Status Akun</span>
              <span class="dl-value">
                <span class="status-badge {{ $user->is_aktif ? 'active' : 'inactive' }}">
                  <span class="status-dot"></span>
                  {{ $user->is_aktif ? 'Aktif' : 'Tidak Aktif' }}
                </span>
              </span>
            </li>
            <li>
              <span class="dl-label">Dibuat</span>
              <span class="dl-value">{{ $user->created_at ? $user->created_at->format('d M Y, H:i') : '-' }}</span>
            </li>
            <li>
              <span class="dl-label">Diperbarui</span>
              <span class="dl-value">{{ $user->updated_at ? $user->updated_at->format('d M Y, H:i') : '-' }}</span>
            </li>
            @if($user->last_login_at)
            <li>
              <span class="dl-label">Terakhir Login</span>
              <span class="dl-value">{{ $user->last_login_at->format('d M Y, H:i') }}</span>
            </li>
            @endif
          </ul>
        </div>

        @if(auth()->user()->isSuperAdmin() && $user->id !== auth()->id())
        <!-- Toggle Aktif / Nonaktif -->
        <form method="POST" action="{{ route('users.toggle-aktif', $user->id) }}" id="toggle-aktif-form">
          @csrf
          @method('PATCH')
        </form>
        <div class="d-card">
          <div class="d-card-header">
            <i data-feather="shield"></i>
            Status Akun
          </div>
          <div class="toggle-section">
            <div class="toggle-section-info">
              <div class="toggle-section-title">
                {{ $user->is_aktif ? 'User Aktif' : 'User Tidak Aktif' }}
              </div>
              <div class="toggle-section-desc {{ $user->is_aktif ? '' : 'danger' }}">
                @if($user->is_aktif)
                  User ini dapat login dan menggunakan sistem. Nonaktifkan untuk memblokir akses tanpa menghapus data.
                @else
                  User ini tidak dapat login ke sistem. Aktifkan kembali untuk memulihkan aksesnya.
                @endif
              </div>
            </div>
            <label class="toggle-switch" title="{{ $user->is_aktif ? 'Klik untuk nonaktifkan' : 'Klik untuk aktifkan' }}" style="display:block;position:relative;width:48px;height:26px;flex-shrink:0;cursor:pointer;">
              <input type="checkbox" {{ $user->is_aktif ? 'checked' : '' }} onchange="confirmToggleAktifShow('{{ addslashes($user->nama ?? $user->username) }}', {{ $user->is_aktif ? 'true' : 'false' }})" style="opacity:0;width:0;height:0;position:absolute;">
              <span class="toggle-slider"></span>
            </label>
          </div>
        </div>
        @endif

        @if($user->pejabat)
        <div class="d-card">
          <div class="d-card-header">
            <i data-feather="user-check"></i>
            Pejabat Terkait
          </div>
          <div class="pejabat-linked-card">
            <div class="pejabat-info">
              <div class="pejabat-name">{{ optional($user->pejabat)->nama ?? 'Nama tidak tersedia' }}</div>
              <div class="pejabat-jabatan">{{ optional($user->pejabat)->jabatan ?? 'Jabatan tidak tersedia' }}</div>
              @if(optional($user->pejabat)->tugas)
              <div class="pejabat-tugas">{{ optional($user->pejabat)->tugas }}</div>
              @endif
            </div>
            @if($user->pejabat && $user->pejabat->id)
            <a href="{{ route('pejabat.show', $user->pejabat->id) }}" class="btn btn-back" style="font-size:12px;padding:7px 14px;">
              <i data-feather="external-link" style="width:13px;height:13px;"></i>
              Lihat Detail
            </a>
            @endif
          </div>
        </div>
        @endif

        </div><!-- /.page-center -->
      </div>
    </div>

    @include('layouts.footer-block')
    @include('layouts.footer-js')

    <script>
      function confirmDeleteUser() {
        if (!document.getElementById('delete-user-form')) return;
        AppModal.confirmDelete('user "{{ addslashes($user->nama ?? $user->username) }}"', function() {
          document.getElementById('delete-user-form').submit();
        });
      }

      function confirmToggleAktifShow(userName, currentlyActive) {
        const willBeActive = !currentlyActive;
        const action = willBeActive ? 'mengaktifkan' : 'menonaktifkan';
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
