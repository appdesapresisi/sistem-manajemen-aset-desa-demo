<!doctype html>
<html lang="en" data-pc-preset="{{ $pc_preset_theme }}" data-pc-theme="{{ $pc_dark_layout }}">
  <head>
    @include('layouts.head-page-meta', ['title' => 'Ganti Password - Sistem Manajemen Aset Desa'])
    @include('layouts.head-css')
    <style>
      .form-section {
        background: transparent;
        border: none;
        padding: 0;
        margin-bottom: 24px;
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
        grid-template-columns: repeat(2, minmax(0, 1fr));
        gap: 20px 24px;
      }
      .form-full {
        grid-column: 1 / -1;
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
        color: var(--color-danger, #D9383A);
        font-weight: 700;
        margin-left: 2px;
      }
      .form-control {
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
        box-sizing: border-box;
      }
      .form-control:focus {
        outline: none !important;
        border-color: var(--color-primary) !important;
        box-shadow: none !important;
      }
      .form-control::placeholder {
        color: var(--color-text-muted);
        opacity: 0.65;
      }
      .form-hint {
        font-size: 12px;
        color: var(--color-text-muted);
        margin-top: 6px;
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
        position: relative !important;
        flex-shrink: 0;
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

      /* Password input with toggle */
      .password-wrapper {
        position: relative;
        width: 100%;
      }
      .password-wrapper .form-control {
        padding-right: 44px;
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
      .password-strength {
        height: 4px;
        border-radius: 2px;
        margin-top: 6px;
        transition: all 0.3s;
        background: var(--color-border, #cfdcc8);
      }
      .password-strength-text {
        font-size: 12px;
        margin-top: 4px;
        font-weight: 600;
      }

      /* Form Actions */
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

      @media (max-width: 768px) {
        .form-grid {
          grid-template-columns: 1fr;
        }
        .form-actions {
          justify-content: center !important;
        }
      }
    </style>
  </head>

  <body>
    @include('layouts.notification')
    @include('layouts.modals')
    @include('layouts.layout-vertical')

    <div class="pc-container">
      <div class="pc-content">
        @include('layouts.breadcrumb', [
          'breadcrumbItemHome' => 'Akun',
          'breadcrumbItem' => 'Profil Saya',
          'breadcrumbItemUrl' => route('profile'),
          'breadcrumbItemActive' => 'Ganti Password'
        ])

        <div class="grid grid-cols-12 gap-x-6">
          <div class="col-span-12 w-full">

            <div class="card">
              <div class="card-header">
                <div class="flex items-center">
                  <div class="card-header-icon">
                    <i data-feather="key"></i>
                  </div>
                  <div>
                    <h5 class="mb-0">Ganti Password</h5>
                  </div>
                </div>
              </div>

              <div class="card-body pt-4">
                {{-- Error alert --}}
                @if($errors->any())
                <div style="background: var(--color-danger-soft, #F3DAD3); border: 1px solid var(--color-danger, #A63D2F); border-radius: 8px; padding: 12px 16px; color: var(--color-danger, #A63D2F); font-size: 13px; margin-bottom: 20px;">
                  <strong>Terdapat kesalahan:</strong>
                  <ul style="margin: 4px 0 0 16px; padding: 0;">
                    @foreach(['current_password', 'password'] as $field)
                      @foreach($errors->get($field) as $err)
                        <li>{{ $err }}</li>
                      @endforeach
                    @endforeach
                  </ul>
                </div>
                @endif

                {{-- Success alert --}}
                @if(session('success'))
                <div style="background: var(--color-success-soft, #dce8d5); border: 1px solid var(--color-success, #3F7D3A); border-radius: 8px; padding: 12px 16px; color: var(--color-primary, #1f3a2a); font-size: 13px; margin-bottom: 20px; display: flex; align-items: center; gap: 8px;">
                  <i data-feather="check-circle" style="width: 16px; height: 16px; flex-shrink: 0;"></i>
                  <span>{{ session('success') }}</span>
                </div>
                @endif

                <form id="change-password-form" method="POST" action="{{ route('profile.change-password') }}" novalidate>
                  @csrf

                  <div class="form-section">
                    <div class="form-section-title">
                      <span>Keamanan Password</span>
                    </div>

                    <div class="form-grid">
                      {{-- Password saat ini --}}
                      <div class="form-group form-full">
                        <label class="form-label" for="current_password">
                          Password Saat Ini <span class="required">*</span>
                        </label>
                        <div class="password-wrapper">
                          <input type="password" id="current_password" name="current_password"
                            class="form-control {{ $errors->has('current_password') ? 'is-invalid' : '' }}"
                            placeholder="Masukkan password saat ini" required>
                          <button type="button" class="password-toggle" data-password-target="current_password" aria-label="Tampilkan/Sembunyikan password saat ini">
                            <i data-feather="eye" style="width: 16px; height: 16px;"></i>
                          </button>
                        </div>
                        @error('current_password')
                          <div class="form-hint" style="color: var(--color-danger, #A63D2F);">{{ $message }}</div>
                        @enderror
                      </div>

                      {{-- Password baru --}}
                      <div class="form-group">
                        <label class="form-label" for="password">
                          Password Baru <span class="required">*</span>
                        </label>
                        <div class="password-wrapper">
                          <input type="password" id="password" name="password"
                            class="form-control {{ $errors->has('password') ? 'is-invalid' : '' }}"
                            placeholder="Minimal 8 karakter" required minlength="8" oninput="checkStrength(this.value); checkMatch();">
                          <button type="button" class="password-toggle" data-password-target="password" aria-label="Tampilkan/Sembunyikan password baru">
                            <i data-feather="eye" style="width: 16px; height: 16px;"></i>
                          </button>
                        </div>
                        <div id="strength-container" style="display: none;">
                          <div class="password-strength" id="strength-bar"></div>
                          <div class="password-strength-text" id="strength-text"></div>
                        </div>
                        <div class="form-hint">Minimal 8 karakter. Disarankan kombinasi huruf besar, huruf kecil, dan angka.</div>
                        @error('password')
                          <div class="form-hint" style="color: var(--color-danger, #A63D2F);">{{ $message }}</div>
                        @enderror
                      </div>

                      {{-- Konfirmasi password --}}
                      <div class="form-group">
                        <label class="form-label" for="password_confirmation">
                          Konfirmasi Password Baru <span class="required">*</span>
                        </label>
                        <div class="password-wrapper">
                          <input type="password" id="password_confirmation" name="password_confirmation"
                            class="form-control" placeholder="Ulangi password baru" required oninput="checkMatch()">
                          <button type="button" class="password-toggle" data-password-target="password_confirmation" aria-label="Tampilkan/Sembunyikan konfirmasi password baru">
                            <i data-feather="eye" style="width: 16px; height: 16px;"></i>
                          </button>
                        </div>
                        <div id="match-text" style="font-size: 12px; margin-top: 6px; font-weight: 600; min-height: 16px;"></div>
                      </div>
                    </div>
                  </div>

                  <div class="form-actions">
                    <a href="{{ route('profile') }}" class="btn-cancel">
                      <i data-feather="x"></i>
                      <span>Batal</span>
                    </a>
                    <button type="submit" class="btn-submit">
                      <i data-feather="save"></i>
                      <span>Update Password</span>
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
      function togglePassword(fieldId, btn) {
        const field = document.getElementById(fieldId);
        if (!field) return;

        const isHidden = field.type === 'password';
        field.type = isHidden ? 'text' : 'password';

        if (btn) {
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
          bar.style.background = 'var(--color-border, #cfdcc8)';
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
          { color: '#A63D2F', label: 'Lemah' },
          { color: '#C9A24A', label: 'Cukup' },
          { color: '#B7791F', label: 'Sedang' },
          { color: '#3F7D3A', label: 'Kuat' },
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
          el.style.color = 'var(--color-success, #3F7D3A)';
          el.textContent = 'Password cocok';
          return true;
        } else {
          confField.setCustomValidity('Konfirmasi password tidak cocok');
          el.style.color = 'var(--color-danger, #A63D2F)';
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
          e.preventDefault();
          if (!checkMatch(true)) return;
          if (typeof window.simaValidateForm === 'function') {
            if (!window.simaValidateForm(form)) return;
          } else if (!form.checkValidity()) {
            const firstInvalid = form.querySelector(':invalid');
            if (firstInvalid && typeof window.simaScrollToField === 'function') {
              window.simaScrollToField(firstInvalid);
            }
            return;
          }

          if (typeof AppModal !== 'undefined' && typeof AppModal.confirm === 'function') {
            AppModal.confirm(
              'Konfirmasi Update Password',
              'Apakah Anda yakin ingin memperbarui password akun Anda?',
              function() {
                form.submit();
              }
            );
          } else {
            form.submit();
          }
        });

        if (typeof feather !== 'undefined') {
          feather.replace();
        }
      });
    </script>
  </body>
</html>
