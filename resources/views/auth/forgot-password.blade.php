<!doctype html>
<html lang="en" data-pc-preset="preset-1" data-pc-sidebar-theme="light" data-pc-sidebar-caption="true">
  <head>
    @include('layouts.head-page-meta', ['title' => 'Lupa Password - Sistem Manajemen Aset Desa Demo'])
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
      .login-form-container { width: 100%; max-width: 420px; }
      .login-header { text-align: center; margin-bottom: 36px; }
      .login-header-icon {
        width: 64px; height: 64px;
        background: #1c5240;
        border-radius: 16px; display: flex; align-items: center; justify-content: center;
        margin: 0 auto 20px; box-shadow: 0 10px 25px rgba(6, 70, 53, 0.25);
      }
      .login-header-icon i { width: 32px; height: 32px; color: #fff !important; stroke: #fff !important; }
      .login-header h1 { font-size: 26px; font-weight: 700; color: #1f2937; margin: 0 0 8px; }
      .login-header p { font-size: 14px; color: #6b7280; margin: 0; }

      .login-form-card {
        background: #fff; border-radius: 20px; padding: 36px;
        box-shadow: 0 10px 40px rgba(0,0,0,0.08); border: 1px solid rgba(0,0,0,0.05);
      }
      .form-group { margin-bottom: 24px; }
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

      .submit-btn {
        width: 100%; padding: 16px 24px; font-size: 15px; font-weight: 600; color: #fff;
        background: #064635;
        border: none; border-radius: 12px; cursor: pointer; transition: all 0.3s;
        display: flex; align-items: center; justify-content: center; gap: 10px;
      }
      .submit-btn:hover { transform: translateY(-2px); box-shadow: 0 10px 30px rgba(6, 70, 53, 0.3); }
      .submit-btn:disabled { opacity: 0.6; cursor: not-allowed; transform: none; }
      .submit-btn i { width: 18px; height: 18px; }

      @keyframes spin { from { transform: rotate(0deg); } to { transform: rotate(360deg); } }

      .alert-success {
        background: #f0fdf4; border: 1px solid #bbf7d0; border-radius: 10px;
        padding: 14px 16px; margin-bottom: 20px; display: flex; align-items: flex-start; gap: 10px;
      }
      .alert-success i { width: 18px; height: 18px; color: #16a34a; flex-shrink: 0; margin-top: 1px; }
      .alert-success p { margin: 0; font-size: 14px; color: #15803d; line-height: 1.5; }

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
            Fitur reset password via email tidak tersedia pada sistem ini.<br>
            Silakan hubungi <strong>Super Admin</strong> untuk mereset password Anda.

            {{--
            === KONTEN LAMA — DINONAKTIFKAN (sistem tidak menggunakan email) ===
            Masukkan alamat email terdaftar Anda.<br>
            Kami akan mengirimkan link untuk mereset password.
          </p>
        </div>
      </div>

      <!-- Right Panel - Forgot Password Form -->
      <div class="login-form-panel">
        <div class="deco-circle deco-circle-1"></div>
        <div class="deco-circle deco-circle-2"></div>

        <div class="login-form-container">
          <div class="login-header">
            <div class="login-header-icon">
              <i data-feather="key"></i>
            </div>
            <h1>Lupa Password?</h1>
            Tidak masalah! Password akan direset oleh Super Admin.
            {{-- Kami akan kirimkan link reset ke email Anda. --}}
          </div>

          <div class="login-form-card">

            {{-- Success Message --}}
            @if (session('status'))
              <div class="alert-success">
                <i data-feather="check-circle"></i>
                <p>{{ session('status') }}</p>
              </div>
            @endif

            {{-- Error Message --}}
            {{-- Error Message (email) — dinonaktifkan, sistem tidak menggunakan email
            @if ($errors->has('email'))
              <div class="alert-error">
                <i data-feather="alert-circle"></i>
                <p>{{ $errors->first('email') }}</p>
              </div>
            @endif
            --}}

            {{-- Form email dinonaktifkan — sistem menggunakan username, bukan email.
                 Reset password dilakukan oleh Super Admin melalui halaman manajemen user.
            <form id="forgotForm" method="POST" action="{{ route('password.email') }}">
              @csrf
              <div class="form-group">
                <label class="form-label" for="email">Alamat Email</label>
                <input type="email" id="email" name="email" class="form-input" placeholder="Masukkan email terdaftar Anda" value="{{ old('email') }}" required autofocus />
              </div>
              <button type="submit" class="submit-btn" id="submitBtn">
                <i data-feather="send"></i>
                Kirim Link Reset Password
              </button>
            </form>
            --}}

            <div style="text-align:center;padding:16px 0;">
              <p style="font-size:14px;color:#6b7280;margin-bottom:12px;">Hubungi Super Admin untuk mereset password Anda.</p>
              <a href="{{ route('login') }}" class="submit-btn" style="display:inline-block;text-decoration:none;">
                <i data-feather="arrow-left"></i>
                Kembali ke Login
              </a>
            </div>
          </div>
        </div>

        <div class="version-info">Sistem Manajemen Aset Desa Demo v1.0.0</div>
      </div>
    </div>

    @include('layouts.footer-js')

    <script>
      const forgotForm = document.getElementById('forgotForm');
      const submitBtn  = document.getElementById('submitBtn');

      forgotForm.addEventListener('submit', function () {
        submitBtn.disabled = true;
        submitBtn.innerHTML = '<i data-feather="loader" style="animation: spin 1s linear infinite;"></i> Mengirim...';
        feather.replace();
      });
    </script>
  </body>
</html>
