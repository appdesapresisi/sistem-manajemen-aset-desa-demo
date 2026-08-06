<!doctype html>
<html lang="en" data-pc-preset="{{ $pc_preset_theme }}" data-pc-theme="{{ $pc_dark_layout }}">
  <head>
    @include('layouts.head-page-meta', ['title' => 'Profil Saya'])
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

      /* ===== PEJABAT CARD ===== */
      .pejabat-linked-card { margin:16px 20px; background:#f4faf5; border:1px solid #e2e8f0; border-left:3px solid #064635; border-radius:8px; padding:14px 16px; display:flex; align-items:center; gap:14px; }
      .pejabat-icon { width:44px; height:44px; border-radius:10px; background:linear-gradient(135deg,#064635,#519259); display:flex; align-items:center; justify-content:center; color:#fff; flex-shrink:0; }
      .pejabat-icon i { width:20px; height:20px; }
      .pejabat-info { flex:1; }
      .pejabat-name { font-size:14px; font-weight:600; color:#1e293b; }
      .pejabat-jabatan { font-size:12px; color:#64748b; margin-top:2px; }

      /* ===== ACTION BAR ===== */
      .action-bar { background:#fff; border:1px solid #e2e8f0; border-radius:12px; padding:14px 20px; display:flex; gap:10px; align-items:center; flex-wrap:wrap; margin-bottom:14px; box-shadow:0 1px 3px rgba(0,0,0,.05); justify-content:space-between; }
      .action-bar-right { display:flex; gap:10px; align-items:center; }
      .btn { padding:9px 18px; border-radius:8px; font-size:13px; font-weight:600; cursor:pointer; transition:all .2s; display:inline-flex; align-items:center; gap:7px; text-decoration:none; border:none; white-space:nowrap; }
      .btn i { width:15px; height:15px; }
      .btn-back { border:1px solid #d1d5db !important; background:#fff; color:#374151; }
      .btn-back:hover { background:#f4faf5; }
      .btn-edit { background:#064635; color:#fff; }
      .btn-edit:hover { background:#1c5240; }

      /* ===== EDIT FORM ===== */
      .form-group { margin-bottom:18px; }
      .form-group label { display:block; font-size:13px; font-weight:600; color:#374151; margin-bottom:6px; }
      .form-group .form-control { width:100%; padding:10px 14px; border:1px solid #d1d5db; border-radius:8px; font-size:14px; color:#1f2937; background:#fff; transition:border-color .2s; box-sizing:border-box; }
      .form-group .form-control:focus { border-color:#064635; outline:none; box-shadow:0 0 0 3px rgba(6, 70, 53,.1); }
      .form-group .form-control.is-invalid { border-color:#ef4444; }
      .invalid-feedback { font-size:12px; color:#ef4444; margin-top:4px; }
      .btn-save { display:inline-flex; align-items:center; gap:8px; padding:9px 20px; background:#064635; color:#fff; border:none; border-radius:8px; font-size:13px; font-weight:600; cursor:pointer; transition:background .2s; }
      .btn-save:hover { background:#1c5240; }
      .btn-cancel { display:inline-flex; align-items:center; gap:8px; padding:9px 20px; background:#fff; color:#374151; border:1px solid #d1d5db; border-radius:8px; font-size:13px; font-weight:600; cursor:pointer; transition:background .2s; }
      .btn-cancel:hover { background:#f3f4f6; }
    </style>
  </head>

  <body>
    @include('layouts.modals')
    @include('layouts.layout-vertical')

    <div class="pc-container">
      <div class="pc-content">
        @include('layouts.breadcrumb', ['breadcrumbItemHome' => 'Akun', 'breadcrumbItemActive' => 'Profil Saya'])

        <div class="page-center">

          {{-- Action Bar --}}
          <div class="action-bar">
            <a href="{{ route('dashboard') }}" class="btn btn-back">
              <i data-feather="arrow-left"></i>
              Kembali
            </a>
            <div class="action-bar-right">
              <button type="button" class="btn btn-edit" id="btn-edit-profil" onclick="openEditProfile()">
                <i data-feather="edit-2"></i>
                Edit Profil
              </button>
            </div>
          </div>

          {{-- Informasi Profil --}}
          <div class="d-card">
            <div class="d-card-header">
              <i data-feather="user"></i>
              Informasi Profil
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
                <span class="dl-label">Role</span>
                <span class="dl-value">
                  @if($user->role)
                    @php
                      $roleNamaRaw = $user->role->nama ?? '';
                      $roleName = strtolower($roleNamaRaw);
                      $roleNameDisplay = ucwords(str_replace('_', ' ', $roleNamaRaw ?: 'User'));

                      if ($roleName === 'super_admin' || stripos($roleName, 'super_admin') !== false) {
                          $roleInfo = ['name' => 'Super Admin', 'class' => 'super-admin', 'icon' => 'shield'];
                      } elseif (stripos($roleName, 'sekretaris') !== false) {
                          $roleInfo = ['name' => 'Sekretaris Desa', 'class' => 'admin', 'icon' => 'shield'];
                      } elseif (stripos($roleName, 'kaur') !== false) {
                          $roleInfo = ['name' => 'Kaur', 'class' => 'admin', 'icon' => 'shield'];
                      } elseif (stripos($roleName, 'perbekel') !== false) {
                          $roleInfo = ['name' => 'Perbekel', 'class' => 'perbekel', 'icon' => 'shield'];
                      } else {
                          $roleInfo = ['name' => $roleNameDisplay, 'class' => 'viewer', 'icon' => 'user'];
                      }
                    @endphp
                    {{ $roleInfo['name'] ?? $roleNameDisplay }}
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
              {{-- total login di komen dulu --}}
              {{-- <li>
                <span class="dl-label">Total Login</span>
                <span class="dl-value">{{ $user->login_count ?? 0 }} kali</span>
              </li> --}}
              <li>
                <span class="dl-label">Bergabung Sejak</span>
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

          {{-- Pejabat Terkait --}}
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
                <div class="pejabat-jabatan">{{ optional($user->pejabat)->tugas }}</div>
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

          {{-- Profil (hidden by default) --}}
          <div class="d-card" id="profile-edit-card" style="display:none;">
            <div class="d-card-header">
              <i data-feather="edit-2"></i>
              Edit Profil
            </div>
            <div style="padding:20px;">
              @if($errors->has('nama') || $errors->has('username'))
              <div style="background:#fee2e2;border:1px solid #fca5a5;border-radius:8px;padding:12px 16px;color:#dc2626;font-size:13px;margin-bottom:16px;">
                <strong>Terdapat kesalahan:</strong>
                <ul style="margin:4px 0 0 16px;padding:0;">
                  @foreach(['nama','username'] as $field)
                    @foreach($errors->get($field) as $err)
                      <li>{{ $err }}</li>
                    @endforeach
                  @endforeach
                </ul>
              </div>
              @endif
              <form method="POST" action="{{ route('profile.update') }}">
                @csrf
                @method('PUT')
                <div class="form-group">
                  <label for="nama">Nama Lengkap <span style="color:#ef4444;">*</span></label>
                  <input type="text" id="nama" name="nama"
                    class="form-control {{ $errors->has('nama') ? 'is-invalid' : '' }}"
                    value="{{ old('nama', $user->nama) }}" required placeholder="Masukkan nama lengkap">
                  @error('nama')<div class="invalid-feedback">{{ $message }}</div>@enderror
                </div>
                <div class="form-group">
                  <label for="username">Username <span style="color:#ef4444;">*</span></label>
                  <input type="text" id="username" name="username"
                    class="form-control {{ $errors->has('username') ? 'is-invalid' : '' }}"
                    value="{{ old('username', $user->username) }}" required placeholder="Masukkan username">
                  @error('username')<div class="invalid-feedback">{{ $message }}</div>@enderror
                </div>
                <div style="display:flex;gap:10px;justify-content:flex-end;">
                  <button type="button" class="btn-cancel" onclick="closeEditProfile()">
                    <i data-feather="x" style="width:15px;height:15px;"></i>
                    Batal
                  </button>
                  <button type="submit" class="btn-save">
                    <i data-feather="save" style="width:15px;height:15px;"></i>
                    Edit Profil
                  </button>
                </div>
              </form>
            </div>
          </div>

        </div>{{-- /.page-center --}}
      </div>
    </div>

    @include('layouts.footer-block')
    @include('layouts.footer-js')
    <script>
      function openEditProfile() {
        document.getElementById('profile-edit-card').style.display = 'block';
        document.getElementById('btn-edit-profil').style.display = 'none';
      }
      function closeEditProfile() {
        document.getElementById('profile-edit-card').style.display = 'none';
        document.getElementById('btn-edit-profil').style.display = 'inline-flex';
      }
      document.addEventListener('DOMContentLoaded', function() {
        @if($errors->has('nama') || $errors->has('username'))
          openEditProfile();
        @endif
      });
    </script>
  </body>
</html>
