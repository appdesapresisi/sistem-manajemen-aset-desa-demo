<!doctype html>
<html lang="en" data-pc-preset="preset-1" data-pc-sidebar-theme="light" data-pc-sidebar-caption="true">
  <head>
    @include('layouts.head-page-meta', ['title' => 'Reset Password - Sistem Manajemen Aset Desa Demo'])
    @include('layouts.head-css')
    <script src="https://cdn.jsdelivr.net/npm/feather-icons/dist/feather.min.js"></script>
    <style>
      body { margin: 0; padding: 0; min-height: 100vh; font-family: 'Inter', -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, sans-serif; overflow-x: hidden; }
      .login-container { display: flex; min-height: 100vh; }

      /* Left Panel */
      .login-brand-panel {
        flex: 1;
        background: #064635;
        display: flex; flex-direction: column; justify-content: center; align-items: center;
        padding: 60px; position: relative; overflow: hidden;
      }
      .login-brand-panel::before {
        display: none !important;
        content: ''; position: absolute; top: -50%; right: -50%; width: 100%; height: 100%;
        background: radial-gradient(circle, rgba(255,255,255,0.05) 0%, transparent 60%);
        animation: pulse 8s ease-in-out infinite;
      }
      @keyframes pulse { 0%, 100% { transform: scale(1); opacity: 0.5; } 50% { transform: scale(1.1); opacity: 0.8; } }
      .brand-content { position: relative; z-index: 10; text-align: center; max-width: 400px; }
      .brand-logo {
        width: 110px; height: 110px;
        display: flex; align-items: center; justify-content: center;
        margin: 0 auto 32px;
      }
      .brand-logo img { width: 110px; height: 110px; object-fit: contain; filter: drop-shadow(0 8px 24px rgba(0,0,0,0.35)); }
      .brand-title { font-size: 32px; font-weight: 700; color: #fff; margin-bottom: 16px; line-height: 1.3; }
      .brand-subtitle { font-size: 16px; color: rgba(255,255,255,0.8); margin: 0; line-height: 1.6; }

      /* Right Panel */
      .login-form-panel {
        flex: 1; display: flex; flex-direction: column; justify-content: center; align-items: center;
        padding: 40px; background: #f4faf5; position: relative; overflow: hidden;
      }
      .login-form-container { width: 100%; max-width: 440px; }
      .login-header { text-align: center; margin-bottom: 36px; }
      .login-header-icon {
        width: 64px; height: 64px;
        background: #064635;
        border-radius: 16px; display: flex; align-items: center; justify-content: center;
        margin: 0 auto 20px; box-shadow: 0 10px 25px rgba(6, 70, 53, 0.2);
      }
      .login-header-icon i { width: 32px; height: 32px; color: #fff !important; stroke: #fff !important; }
      .login-header h1 { font-size: 26px; font-weight: 700; color: #1f2937; margin: 0 0 8px; }
      .login-header p { font-size: 14px; color: #6b7280; margin: 0; }

      .login-form-card {
        background: #fff; border-radius: 20px; padding: 36px;
        box-shadow: 0 10px 40px rgba(0,0,0,0.08); border: 1px solid rgba(0,0,0,0.05);
      }
      .form-group { margin-bottom: 20px; }
      .form-label { display: block; font-size: 14px; font-weight: 600; color: #374151; margin-bottom: 8px; }
      .form-input-wrapper { position: relative; }
      .form-input-icon { position: absolute; left: 16px; top: 50%; transform: translateY(-50%); color: #064635; }
      .form-input-icon i { width: 20px; height: 20px; }
      .form-input {
        width: 100%; padding: 14px 16px 14px 50px; font-size: 15px;
        border: 2px solid #e5e7eb; border-radius: 12px; background: #f9fafb;
        transition: all 0.2s; box-sizing: border-box;
      }
      .form-input:focus { outline: none; border-color: #064635; background: #fff; box-shadow: 0 0 0 4px rgba(6, 70, 53, 0.1); }
      .form-input::placeholder { color: #9ca3af; }
      .form-input:disabled { background: #f3f4f6; color: #6b7280; cursor: not-allowed; }

      .password-toggle { position: absolute; right: 16px; top: 50%; transform: translateY(-50%); background: none; border: none; color: #064635; cursor: pointer; padding: 4px;
      }
      .password-toggle:hover { color: #053a2c; }
      .password-toggle i { width: 20px; height: 20px; }

      .password-hint {
        font-size: 12px; color: #9ca3af; margin-top: 6px;
      }

      .submit-btn {
        width: 100%; padding: 16px 24px; font-size: 15px; font-weight: 600; color: #fff;
        background: #064635;
        border: none; border-radius: 12px; cursor: pointer; transition: all 0.3s; margin-top: 8px;
        display: flex; align-items: center; justify-content: center; gap: 10px;
      }
      .submit-btn:hover { transform: translateY(-2px); box-shadow: 0 10px 30px rgba(6, 70, 53, 0.3); }
      .submit-btn:disabled { opacity: 0.6; cursor: not-allowed; transform: none; }
      .submit-btn i { width: 18px; height: 18px; }

      .alert-error {
        background: #fef2f2; border: 1px solid #fecaca; border-radius: 10px;
        padding: 14px 16px; margin-bottom: 20px; display: flex; align-items: flex-start; gap: 10px;
      }
      .alert-error i { width: 18px; height: 18px; color: #dc2626; flex-shrink: 0; margin-top: 1px; }
      .alert-error p { margin: 0; font-size: 14px; color: #b91c1c; line-height: 1.5; }

      .back-link {
        display: block; text-align: center; margin-top: 20px; font-size: 14px;
        color: #6b7280; text-decoration: none;
      }
      .back-link a { color: #064635; font-weight: 600; text-decoration: none; }
      .back-link a:hover { text-decoration: underline; }

      .version-info {
        position: absolute; bottom: 24px; left: 50%; transform: translateX(-50%);
        font-size: 12px; color: #9ca3af;
      }
      .deco-circle { display: none !important; }
      .deco-circle-1 { width: 200px; height: 200px; background: linear-gradient(135deg, #e8f5e9 0%, #d4edd9 100%); top: -50px; right: -50px; }
      .deco-circle-2 { width: 150px; height: 150px; background: linear-gradient(-135deg, #d4edd9 0%, #c8e6c9 100%); bottom: 50px; left: -30px; }

      @keyframes spin { from { transform: rotate(0deg); } to { transform: rotate(360deg); } }

      @media (max-width: 1024px) { .login-brand-panel { display: none; } .login-form-panel { flex: none; width: 100%; } }
      @media (max-width: 480px) { .login-form-panel { padding: 24px; } .login-form-card { padding: 24px; } }
    </style>
  </head>

  <body>
    @include('layouts.loader')
    <div class="login-container">

      <!-- Left Panel - Branding -->
      <div class="login-brand-panel">
        <div class="brand-content">
          <div class="brand-logo">
            <img src="{{ asset('assets/images/logo/logo-demo.png') }}" alt="Logo Mengwi" />
          </div>
          <h1 class="brand-title">Sistem Manajemen Aset</h1>
          <p class="brand-subtitle">
            Buat password baru yang kuat dan mudah diingat.<br>
            Minimal 8 karakter.
          </p>
        </div>
      </div>

      <!-- Right Panel - Reset Password Form -->
      <div class="login-form-panel">
        <div class="deco-circle deco-circle-1"></div>
        <div class="deco-circle deco-circle-2"></div>

        <div class="login-form-container">
          <div class="login-header">
            <div class="login-header-icon">
              <i data-feather="shield"></i>
            </div>
            <h1>Reset Password</h1>
            <p>Masukkan password baru untuk akun Anda.</p>
          </div>

          <div class="login-form-card">

            {{-- Error Message --}}
            @if ($errors->has('email'))
              <div class="alert-error">
                <i data-feather="alert-circle"></i>
                <p>{{ $errors->first('email') }}</p>
              </div>
            @endif

            <form id="resetForm" method="POST" action="{{ route('password.update') }}">
              @csrf

              {{-- Hidden fields --}}
              <input type="hidden" name="token" value="{{ $token }}">

              <div class="form-group">
                <label class="form-label" for="email">Email</label>
                <div class="form-input-wrapper">
                  <span class="form-input-icon">
                    <i data-feather="mail"></i>
                  </span>
                  <input
                    type="email"
                    id="email"
                    name="email"
                    class="form-input"
                    value="{{ old('email', $email) }}"
                    placeholder="Email terdaftar"
                    required
                  />
                </div>
              </div>

              <div class="form-group">
                <label class="form-label" for="password">Password Baru</label>
                <div class="form-input-wrapper">
                  <span class="form-input-icon">
                    <i data-feather="lock"></i>
                  </span>
                  <input
                    type="password"
                    id="password"
                    name="password"
                    class="form-input"
                    placeholder="Minimal 8 karakter"
                    required
                    autofocus
                  />
                  <button type="button" class="password-toggle" onclick="toggleVisibility('password', 'eyeIcon1')">
                    <i data-feather="eye" id="eyeIcon1"></i>
                  </button>
                </div>
                <p class="password-hint">Minimal 8 karakter, gunakan kombinasi huruf dan angka.</p>
              </div>

              <div class="form-group">
                <label class="form-label" for="password_confirmation">Konfirmasi Password Baru</label>
                <div class="form-input-wrapper">
                  <span class="form-input-icon">
                    <i data-feather="lock"></i>
                  </span>
                  <input
                    type="password"
                    id="password_confirmation"
                    name="password_confirmation"
                    class="form-input"
                    placeholder="Ulangi password baru"
                    required
                  />
                  <button type="button" class="password-toggle" onclick="toggleVisibility('password_confirmation', 'eyeIcon2')">
                    <i data-feather="eye" id="eyeIcon2"></i>
                  </button>
                </div>
              </div>

              <button type="submit" class="submit-btn" id="submitBtn">
                <i data-feather="check-circle"></i>
                Update Password
              </button>
            </form>

            <p class="back-link">
              <a href="{{ route('login') }}">&#8592; Kembali ke Login</a>
            </p>
          </div>
        </div>

        <div class="version-info">Sistem Manajemen Aset Desa Demo v1.0.0</div>
      </div>
    </div>

    @include('layouts.footer-js')

    <script>
      function toggleVisibility(inputId, iconId) {
        const input = document.getElementById(inputId);
        const icon  = document.getElementById(iconId);
        if (input.type === 'password') {
          input.type = 'text';
          icon.setAttribute('data-feather', 'eye-off');
        } else {
          input.type = 'password';
          icon.setAttribute('data-feather', 'eye');
        }
        feather.replace();
      }

      document.getElementById('resetForm').addEventListener('submit', function () {
        const btn = document.getElementById('submitBtn');
        btn.disabled = true;
      });
    </script>
  </body>
</html>
