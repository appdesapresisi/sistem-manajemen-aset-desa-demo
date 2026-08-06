<!doctype html>
<html lang="en" data-pc-preset="{{ $pc_preset_theme }}" data-pc-theme="{{ $pc_dark_layout }}">
  <head>
    @include('layouts.head-page-meta', ['title' => 'Ganti Password'])
    @include('layouts.head-css')
    <style>
      .profile-card {
        background: #fff;
        border-radius: 12px;
        padding: 28px;
        margin-bottom: 24px;
        border: 1px solid #e5e7eb;
      }
      .profile-header {
        display: flex;
        align-items: center;
        gap: 20px;
        padding-bottom: 24px;
        border-bottom: 2px solid #e5e7eb;
        margin-bottom: 28px;
      }
      .profile-avatar {
        width: 72px;
        height: 72px;
        border-radius: 50%;
        background: linear-gradient(135deg, #064635 0%, #519259 100%);
        display: flex;
        align-items: center;
        justify-content: center;
        flex-shrink: 0;
      }
      .profile-header-info h1 {
        font-size: 22px;
        font-weight: 700;
        color: #1f2937;
        margin: 0 0 4px 0;
      }
      .profile-header-info p {
        font-size: 14px;
        color: #6b7280;
        margin: 0;
      }
      .section-title {
        font-size: 15px;
        font-weight: 700;
        color: #374151;
        margin-bottom: 20px;
        display: flex;
        align-items: center;
        gap: 8px;
      }
      .section-title::after {
        content: '';
        flex: 1;
        height: 1px;
        background: #e5e7eb;
        margin-left: 8px;
      }
      .form-group { margin-bottom: 20px; }
      .form-group label {
        display: block;
        font-size: 13px;
        font-weight: 600;
        color: #374151;
        margin-bottom: 6px;
      }
      .form-group .form-control {
        width: 100%;
        padding: 10px 14px;
        border: 1px solid #d1d5db;
        border-radius: 8px;
        font-size: 14px;
        color: #1f2937;
        background: #fff;
        transition: border-color 0.2s;
      }
      .form-group .form-control:focus {
        border-color: #064635;
        outline: none;
        box-shadow: 0 0 0 3px rgba(6, 70, 53,0.1);
      }
      .form-group .form-control.is-invalid { border-color: #ef4444; }
      .invalid-feedback { font-size: 12px; color: #ef4444; margin-top: 4px; }
      .form-hint { font-size: 12px; color: #9ca3af; margin-top: 4px; }
      .password-strength {
        height: 4px;
        border-radius: 2px;
        margin-top: 6px;
        transition: all 0.3s;
        background: #e5e7eb;
      }
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
        color: #9ca3af;
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
        color: #374151;
      }
      .password-wrapper .form-control {
        padding-right: 44px;
      }
      .password-strength-text { font-size: 12px; margin-top: 4px; font-weight: 600; }
      .btn-save {
        display: inline-flex;
        align-items: center;
        gap: 8px;
        padding: 10px 24px;
        background: #064635;
        color: #fff;
        border: none;
        border-radius: 8px;
        font-size: 14px;
        font-weight: 600;
        cursor: pointer;
        transition: background 0.2s;
        text-decoration: none;
      }
      .btn-save:hover { background: #1c5240; color: #fff; }
      .btn-secondary {
        display: inline-flex;
        align-items: center;
        gap: 8px;
        padding: 10px 24px;
        background: #fff;
        color: #374151;
        border: 1px solid #d1d5db;
        border-radius: 8px;
        font-size: 14px;
        font-weight: 600;
        cursor: pointer;
        transition: background 0.2s;
        text-decoration: none;
      }
      .btn-secondary:hover { background: #f3f4f6; color: #374151; }
      .tips-card {
        background: #f4faf5;
        border: 1px solid #bbf7d0;
        border-radius: 10px;
        padding: 16px 20px;
        margin-bottom: 24px;
      }
      .tips-card h6 {
        font-size: 13px;
        font-weight: 700;
        color: #064635;
        margin: 0 0 10px 0;
        display: flex;
        align-items: center;
        gap: 6px;
      }
      .tips-card ul {
        margin: 0;
        padding-left: 18px;
        color: #4b5a75;
        font-size: 12.5px;
        line-height: 1.8;
      }
      @media (max-width: 768px) {
        .profile-header { flex-direction: column; text-align: center; }
      }
      .card-header-icon {
        display: flex;
        align-items: center;
        justify-content: center;
        width: 44px;
        height: 44px;
        min-width: 44px;
        border-radius: 12px;
        background: #519259;
        margin-right: 14px;
        flex-shrink: 0;
      }
      .card-header-icon i,
      .card-header-icon svg {
        color: #fff !important;
        stroke: #fff !important;
        width: 22px;
        height: 22px;
        fill: none !important;
      }
      .form-actions {
        display: flex;
        gap: 12px;
        justify-content: flex-end;
        padding-top: 20px;
        border-top: 1px solid #e5e7eb;
        margin-top: 24px;
      }
    </style>
  </head>

  <body>
    @include('layouts.modals')
    @include('layouts.layout-vertical')

    <div class="pc-container">
      <div class="pc-content">
        @include('layouts.breadcrumb', ['breadcrumbItemHome' => 'Akun', 'breadcrumbItem' => 'Profil Saya', 'breadcrumbItemUrl' => route('profile'), 'breadcrumbItemActive' => 'Ganti Password'])

        <div class="grid grid-cols-12 gap-x-6">
          <div class="col-span-12 lg:col-span-8 lg:col-start-3">

            {{-- Header --}}
            <div class="card">
              <div class="card-header border-b border-gray-100">
                <div class="flex items-center">
                  <div class="card-header-icon">
                    <i data-feather="key"></i>
                  </div>
                  <div>
                    <h5 class="mb-1">Ganti Password</h5>
                    <p class="text-muted text-sm mb-0">Ubah password akun Anda secara berkala untuk menjaga keamanan akses sistem.</p>
                  </div>
                </div>
              </div>

              <div class="card-body pt-4">
                {{-- Tips keamanan --}}
                <div class="tips-card">
                  <h6>Tips Keamanan Password</h6>
                  <p style="margin: 0; color: #4b5a75; font-size: 12.5px; line-height: 1.8;">Minimal 8 karakter</p>
                </div>

                {{-- Error alert --}}
                @if($errors->any())
                <div style="background:#fee2e2;border:1px solid #fca5a5;border-radius:8px;padding:12px 16px;color:#dc2626;font-size:13px;margin-bottom:20px;">
                  <strong>Terdapat kesalahan:</strong>
                  <ul style="margin:4px 0 0 16px;padding:0;">
                    @foreach(['current_password','password'] as $field)
                      @foreach($errors->get($field) as $err)
                        <li>{{ $err }}</li>
                      @endforeach
                    @endforeach
                  </ul>
                </div>
                @endif

                {{-- Success alert --}}
                @if(session('success'))
                <div style="background:#dcfce7;border:1px solid #86efac;border-radius:8px;padding:12px 16px;color:#16a34a;font-size:13px;margin-bottom:20px;display:flex;align-items:center;gap:8px;">
                  <i data-feather="check-circle" style="width:16px;height:16px;flex-shrink:0;"></i>
                  {{ session('success') }}
                </div>
                @endif

                <form id="change-password-form" method="POST" action="{{ route('profile.change-password') }}">
                  @csrf

                  {{-- Password saat ini --}}
                  <div class="form-group">
                    <label for="current_password">Password Saat Ini <span style="color:#ef4444;">*</span></label>
                    <div class="password-wrapper">
                      <input type="password" id="current_password" name="current_password"
                        class="form-control {{ $errors->has('current_password') ? 'is-invalid' : '' }}"
                        placeholder="Masukkan password saat ini" required>
                      <button type="button" class="password-toggle" data-password-target="current_password" aria-label="Tampilkan/Sembunyikan password saat ini">
                        <i data-feather="eye" style="width:16px;height:16px;"></i>
                      </button>
                    </div>
                    @error('current_password')<div class="invalid-feedback" style="display:block;">{{ $message }}</div>@enderror
                  </div>

                  <div>
                    {{-- Password baru --}}
                    <div class="form-group">
                      <label for="password">Password Baru <span style="color:#ef4444;">*</span></label>
                      <div class="password-wrapper">
                        <input type="password" id="password" name="password"
                          class="form-control {{ $errors->has('password') ? 'is-invalid' : '' }}"
                          placeholder="Minimal 8 karakter" required minlength="8" oninput="checkStrength(this.value); checkMatch();">
                        <button type="button" class="password-toggle" data-password-target="password" aria-label="Tampilkan/Sembunyikan password baru">
                          <i data-feather="eye" style="width:16px;height:16px;"></i>
                        </button>
                      </div>
                      <div id="strength-container" style="display: none;">
                        <div class="password-strength" id="strength-bar"></div>
                        <div class="password-strength-text" id="strength-text"></div>
                      </div>
                      @error('password')<div class="invalid-feedback" style="display:block;">{{ $message }}</div>@enderror
                      <p style="font-size:12px;color:#6b7280;margin-top:4px;">Minimal 8 karakter. Disarankan menggunakan kombinasi huruf besar, huruf kecil, dan angka</p>
                    </div>

                    {{-- Konfirmasi password --}}
                    <div class="form-group">
                      <label for="password_confirmation">Konfirmasi Password Baru <span style="color:#ef4444;">*</span></label>
                      <div class="password-wrapper">
                        <input type="password" id="password_confirmation" name="password_confirmation"
                          class="form-control" placeholder="Ulangi password baru" required oninput="checkMatch()">
                        <button type="button" class="password-toggle" data-password-target="password_confirmation" aria-label="Tampilkan/Sembunyikan konfirmasi password baru">
                          <i data-feather="eye" style="width:16px;height:16px;"></i>
                        </button>
                      </div>
                      <div id="match-text" style="font-size:12px;margin-top:4px;font-weight:600;min-height:16px;"></div>
                    </div>
                  </div>

                  <div class="form-actions">
                    <a href="{{ route('profile') }}" class="btn-secondary" onclick="return confirm('Apakah Anda yakin ingin membatalkan? Data yang sudah diisi tidak akan tersimpan.');">
                      <i data-feather="x" style="width:15px;height:15px;"></i>
                      Batal
                    </a>
                    <button type="submit" class="btn-save">
                      <i data-feather="save" style="width:16px;height:16px;"></i>
                      Update Password
                    </button>
                  </div>
                </form>
              </div>
            </div>

          </div>
        </div>
      </div>
    </div>

    @include('layouts.footer-js')
    <script>
      function togglePassword(fieldId, btn) {
        const field = document.getElementById(fieldId);
        if (!field) return;

        const isHidden = field.type === 'password';
        field.type = isHidden ? 'text' : 'password';

        if (btn) {
          // Feather mengubah <i data-feather> menjadi <svg>, jadi cari keduanya.
          const icon = btn.querySelector('[data-feather], svg');
          const nextIcon = isHidden ? 'eye-off' : 'eye';

          if (icon && icon.tagName && icon.tagName.toLowerCase() === 'svg' && typeof feather !== 'undefined' && feather.icons && feather.icons[nextIcon]) {
            icon.outerHTML = feather.icons[nextIcon].toSvg({ width: 16, height: 16 });
          } else if (icon) {
            icon.setAttribute('data-feather', nextIcon);
            if (typeof feather !== 'undefined') {
              feather.replace();
            }
          }
        }
      }

      document.addEventListener('click', function(event) {
        const btn = event.target.closest('.password-toggle[data-password-target]');
        if (!btn) return;

        event.preventDefault();
        const fieldId = btn.getAttribute('data-password-target');
        togglePassword(fieldId, btn);
      });

      function checkStrength(val) {
        const bar = document.getElementById('strength-bar');
        const text = document.getElementById('strength-text');
        const container = document.getElementById('strength-container');
        if (!val) {
          if (container) container.style.display = 'none';
          bar.style.background = '#e5e7eb';
          bar.style.width = '100%';
          text.textContent = '';
          return;
        }
        if (container) container.style.display = 'block';
        let score = 0;
        if (val.length >= 8) score++;
        if (/[A-Z]/.test(val)) score++;
        if (/[0-9]/.test(val)) score++;
        if (/[^A-Za-z0-9]/.test(val)) score++;
        const levels = [
          { color: '#ef4444', label: 'Lemah' },
          { color: '#f97316', label: 'Cukup' },
          { color: '#eab308', label: 'Sedang' },
          { color: '#22c55e', label: 'Kuat' },
        ];
        const level = levels[score - 1] || levels[0];
        bar.style.background = level.color;
        bar.style.width = '100%';
        text.style.color = level.color;
        text.textContent = 'Kekuatan: ' + level.label;
      }

      function checkMatch(focusOnError = false) {
        const pwField = document.getElementById('password');
        const confField = document.getElementById('password_confirmation');
        const pw = pwField ? pwField.value : '';
        const conf = confField ? confField.value : '';
        const el = document.getElementById('match-text');

        if (!confField || !el) return true;

        if (!conf) {
          confField.setCustomValidity('');
          el.textContent = '';
          return true;
        }

        if (pw === conf) {
          confField.setCustomValidity('');
          el.style.color = '#16a34a';
          el.textContent = 'Password cocok';
          return true;
        } else {
          confField.setCustomValidity('Konfirmasi password tidak cocok');
          el.style.color = '#ef4444';
          el.textContent = 'Password tidak cocok';
          if (focusOnError) {
            confField.focus();
          }
          return false;
        }
      }

      document.addEventListener('DOMContentLoaded', function() {
        const form = document.getElementById('change-password-form');
        if (!form) return;

        form.addEventListener('submit', function(e) {
          checkMatch(true);
          if (!form.checkValidity()) {
            e.preventDefault();
            form.reportValidity();
          }
        });
      });
    </script>
  </body>
</html>
