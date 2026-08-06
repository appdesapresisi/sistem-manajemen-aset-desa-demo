<!doctype html>
<html lang="en" data-pc-preset="preset-1" data-pc-sidebar-theme="light" data-pc-sidebar-caption="true">
  <head>
    @include('layouts.head-page-meta', ['title' => 'Login - Sistem Manajemen Aset Desa'])
    @include('layouts.head-css')
    <!-- Feather Icons CDN -->
    <script src="https://cdn.jsdelivr.net/npm/feather-icons/dist/feather.min.js"></script>
    <style>
      :root {
        --primary-dark: #064635;
        --primary-medium: #1c5240;
        --primary-light: #519259;
        --bg-surface: #ffffff;
      }

      * {
        box-sizing: border-box;
      }

      body {
        margin: 0;
        padding: 0;
        min-height: 100vh;
        font-family: 'Inter', -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, sans-serif;
        background: #f8fafc;
        color: #0f172a;
        overflow-x: hidden;
      }

      .login-wrapper {
        display: flex;
        min-height: 100vh;
        width: 100vw;
        overflow: hidden;
      }

      /* ─── LEFT PANEL: STUNNING FLOATING PRODUCT SHOWCASE ─── */
      .hero-panel {
        flex: 1.2;
        background: linear-gradient(145deg, #043327 0%, #064635 55%, #0b2d21 100%);
        position: relative;
        display: flex;
        flex-direction: column;
        justify-content: space-between;
        padding: 44px 52px;
        overflow: hidden;
        color: #ffffff;
      }

      /* User Favorite Ambient Radial Dot-Grid Pattern */
      .hero-panel::before {
        content: '';
        position: absolute;
        inset: 0;
        background-image: radial-gradient(rgba(255, 255, 255, 0.14) 1.5px, transparent 1.5px);
        background-size: 24px 24px;
        pointer-events: none;
        opacity: 0.95;
      }

      /* Soft Floating Glow Orbs */
      .hero-glow-1 {
        position: absolute;
        top: -10%;
        right: -10%;
        width: 480px;
        height: 480px;
        background: radial-gradient(circle, rgba(81, 146, 89, 0.28) 0%, transparent 65%);
        pointer-events: none;
        animation: floatGlow 12s ease-in-out infinite alternate;
      }

      .hero-glow-2 {
        position: absolute;
        bottom: -10%;
        left: -10%;
        width: 450px;
        height: 450px;
        background: radial-gradient(circle, rgba(6, 70, 53, 0.5) 0%, transparent 65%);
        pointer-events: none;
        animation: floatGlow 15s ease-in-out infinite alternate-reverse;
      }

      @keyframes floatGlow {
        0% { transform: translate(0, 0) scale(1); }
        100% { transform: translate(30px, -20px) scale(1.12); }
      }

      /* Standalone Logo Header (No Box Wrapper) */
      .hero-header {
        position: relative;
        z-index: 10;
        display: flex;
        align-items: center;
        gap: 14px;
      }

      .hero-logo-img {
        width: 42px;
        height: 42px;
        object-fit: contain;
        filter: drop-shadow(0 4px 14px rgba(0,0,0,0.35));
      }

      .hero-brand-title {
        font-size: 20px;
        font-weight: 800;
        color: #ffffff;
        letter-spacing: -0.3px;
        margin: 0;
        line-height: 1.2;
      }

      .hero-brand-subtitle {
        font-size: 12px;
        color: rgba(255, 255, 255, 0.75);
        font-weight: 500;
        margin: 0;
      }

      /* ─── DYNAMIC FLOATING OVERLAPPING STAGE ─── */
      .hero-visual-stage {
        position: relative;
        z-index: 10;
        width: 100%;
        max-width: 560px;
        height: 440px;
        margin: auto;
      }

      /* Premium Glassmorphic Cards (Wide Width & Dynamic Overlap Stack) */
      .kezak-floating-card {
        position: absolute;
        background: rgba(255, 255, 255, 0.96);
        backdrop-filter: blur(20px);
        border-radius: 20px;
        padding: 22px 24px;
        color: #0f172a;
        box-shadow: 0 24px 56px rgba(0, 0, 0, 0.32), 0 2px 8px rgba(0, 0, 0, 0.08);
        border: 1px solid rgba(255, 255, 255, 0.75);
        transition: transform 0.4s ease, box-shadow 0.4s ease, z-index 0.2s;
      }

      .kezak-floating-card:hover {
        transform: translateY(-8px) scale(1.03) !important;
        box-shadow: 0 32px 70px rgba(0, 0, 0, 0.42);
        z-index: 35 !important;
      }

      /* CARD 1: Top Center-Left (Total Inventaris & Donut Chart) */
      .card-1-top {
        width: 350px;
        top: 0px;
        left: 20px;
        z-index: 15;
        animation: floatCard1 8s ease-in-out infinite alternate;
      }

      .card-1-top .card-head {
        display: flex;
        align-items: center;
        justify-content: space-between;
        font-size: 13px;
        font-weight: 700;
        color: #0f172a;
        margin-bottom: 12px;
      }

      .card-1-top .live-pill {
        font-size: 10.5px;
        font-weight: 700;
        color: #059669;
        background: #d1fae5;
        padding: 3px 8px;
        border-radius: 12px;
        display: flex;
        align-items: center;
        gap: 4px;
      }

      .card-1-top .stat-value-big {
        font-size: 26px;
        font-weight: 800;
        color: #0f172a;
        letter-spacing: -0.5px;
        line-height: 1.1;
      }

      .card-1-top .stat-sub-label {
        font-size: 11.5px;
        font-weight: 600;
        color: #64748b;
        margin-bottom: 14px;
      }

      .card-1-top .donut-and-details {
        display: flex;
        align-items: center;
        gap: 16px;
        background: #f8fafc;
        border-radius: 14px;
        padding: 12px 14px;
        border: 1px solid #e2e8f0;
      }

      .card-1-top .donut-svg-wrap {
        width: 64px;
        height: 64px;
        flex-shrink: 0;
      }

      .card-1-top .donut-legend-col {
        display: flex;
        flex-direction: column;
        gap: 4px;
        font-size: 11px;
      }

      .legend-row {
        display: flex;
        align-items: center;
        justify-content: space-between;
        gap: 12px;
        font-weight: 600;
      }

      .legend-left {
        display: flex;
        align-items: center;
        gap: 6px;
        color: #475569;
      }

      .dot-pill {
        width: 7px;
        height: 7px;
        border-radius: 50%;
      }

      .dot-pill.baik { background: #10b981; }
      .dot-pill.rr   { background: #f59e0b; }
      .dot-pill.rb   { background: #ef4444; }

      .legend-val {
        color: #0f172a;
        font-weight: 700;
      }


      /* CARD 2: Middle Right (Target & Distribusi Jenis KIB) */
      .card-2-mid {
        width: 340px;
        top: 90px;
        right: 0px;
        z-index: 10;
        animation: floatCard2 9.5s ease-in-out infinite alternate-reverse;
      }

      .card-2-mid .card-head {
        font-size: 13px;
        font-weight: 700;
        color: #0f172a;
        margin-bottom: 14px;
        display: flex;
        align-items: center;
        justify-content: space-between;
      }

      .card-2-mid .card-head span.badge-kib {
        font-size: 10.5px;
        font-weight: 700;
        color: #2563eb;
        background: #eff6ff;
        padding: 2px 7px;
        border-radius: 6px;
      }

      .kib-progress-item {
        margin-bottom: 11px;
      }

      .kib-progress-item:last-child {
        margin-bottom: 0;
      }

      .kib-item-header {
        display: flex;
        justify-content: space-between;
        font-size: 11.5px;
        font-weight: 600;
        color: #334155;
        margin-bottom: 4px;
      }

      .kib-item-val {
        font-weight: 700;
        color: #064635;
      }

      .kib-bar-track {
        height: 6px;
        width: 100%;
        background: #e2e8f0;
        border-radius: 10px;
        overflow: hidden;
      }

      .kib-bar-fill {
        height: 100%;
        border-radius: 10px;
        background: linear-gradient(90deg, #1c5240, #519259);
      }


      /* CARD 3: Bottom Left (Nilai Perolehan & Action Trigger) */
      .card-3-bottom {
        width: 360px;
        bottom: 0px;
        left: 30px;
        z-index: 25;
        animation: floatCard3 7.5s ease-in-out infinite alternate;
      }

      .card-3-bottom .card-head {
        display: flex;
        align-items: center;
        justify-content: space-between;
        font-size: 13px;
        font-weight: 700;
        color: #0f172a;
        margin-bottom: 4px;
      }

      .card-3-bottom .sync-tag {
        font-size: 10px;
        font-weight: 600;
        color: #64748b;
        display: flex;
        align-items: center;
        gap: 4px;
      }

      .card-3-bottom .total-value-row {
        display: flex;
        align-items: baseline;
        gap: 10px;
        margin-bottom: 12px;
      }

      .card-3-bottom .main-amount {
        font-size: 24px;
        font-weight: 800;
        color: #0f172a;
        letter-spacing: -0.5px;
      }

      .card-3-bottom .trend-badge {
        font-size: 11px;
        font-weight: 700;
        color: #059669;
        background: #d1fae5;
        padding: 2px 7px;
        border-radius: 10px;
      }

      .quick-log-box {
        background: #f8fafc;
        border-radius: 12px;
        padding: 10px 12px;
        border: 1px solid #e2e8f0;
        margin-bottom: 14px;
      }

      .quick-log-item {
        display: flex;
        align-items: center;
        gap: 10px;
        font-size: 11.5px;
      }

      .log-icon-circle {
        width: 30px;
        height: 30px;
        border-radius: 8px;
        background: #064635;
        color: #ffffff;
        display: flex;
        align-items: center;
        justify-content: center;
        flex-shrink: 0;
      }

      .log-icon-circle i {
        width: 15px;
        height: 15px;
      }

      .log-details {
        flex: 1;
        min-width: 0;
      }

      .log-details strong {
        display: block;
        color: #0f172a;
        white-space: nowrap;
        overflow: hidden;
        text-overflow: ellipsis;
      }

      .log-details span {
        color: #64748b;
        font-size: 10.5px;
      }

      .card-cta-btn {
        width: 100%;
        padding: 11px;
        font-size: 13px;
        font-weight: 700;
        color: #ffffff;
        background: linear-gradient(135deg, #064635 0%, #519259 100%);
        border: none;
        border-radius: 12px;
        cursor: pointer;
        display: flex;
        align-items: center;
        justify-content: center;
        gap: 8px;
        transition: all 0.25s ease;
        box-shadow: 0 4px 12px rgba(6, 70, 53, 0.25);
      }

      .card-cta-btn:hover {
        background: linear-gradient(135deg, #053a2c 0%, #46804d 100%);
        transform: translateY(-1px);
        box-shadow: 0 6px 16px rgba(6, 70, 53, 0.35);
      }

      .card-cta-btn i {
        width: 16px;
        height: 16px;
      }

      @keyframes floatCard1 {
        0% { transform: translateY(0px) rotate(-1deg); }
        100% { transform: translateY(-12px) rotate(1deg); }
      }

      @keyframes floatCard2 {
        0% { transform: translateY(0px) rotate(1deg); }
        100% { transform: translateY(-14px) rotate(-1.5deg); }
      }

      @keyframes floatCard3 {
        0% { transform: translateY(0px) rotate(-0.5deg); }
        100% { transform: translateY(-10px) rotate(1deg); }
      }

      /* Hero Bottom Text */
      .hero-footer {
        position: relative;
        z-index: 10;
        max-width: 480px;
        margin: 0 auto;
        text-align: center;
      }

      .hero-headline {
        font-size: 25px;
        font-weight: 800;
        color: #ffffff;
        margin: 0 0 8px;
        line-height: 1.3;
        letter-spacing: -0.4px;
      }

      .hero-desc {
        font-size: 13.5px;
        color: rgba(255, 255, 255, 0.78);
        line-height: 1.5;
        margin: 0;
      }


      /* ─── RIGHT PANEL: LOGIN FORM ─── */
      .form-panel {
        flex: 0.85;
        background: #ffffff;
        display: flex;
        flex-direction: column;
        justify-content: space-between;
        padding: 56px;
        overflow-y: auto;
      }

      .form-center-container {
        width: 100%;
        max-width: 380px;
        margin: auto;
      }

      .form-header {
        margin-bottom: 32px;
        text-align: center;
      }

      .form-header h2 {
        text-align: center;
      }

      .form-header p {
        text-align: center;
      }

      .mobile-logo-wrap {
        display: none;
        align-items: center;
        gap: 10px;
        margin-bottom: 20px;
      }

      .mobile-logo-wrap img {
        width: 36px;
        height: 36px;
        object-fit: contain;
      }

      .mobile-logo-wrap span {
        font-size: 16px;
        font-weight: 700;
        color: var(--primary-dark);
      }

      .form-header h2 {
        font-size: 28px;
        font-weight: 800;
        color: #0f172a;
        margin: 0 0 8px;
        letter-spacing: -0.5px;
      }

      .form-header p {
        font-size: 14px;
        color: #64748b;
        margin: 0;
        line-height: 1.4;
      }

      .field-group {
        margin-bottom: 20px;
      }

      .field-label {
        display: block;
        font-size: 13px;
        font-weight: 600;
        color: #334155;
        margin-bottom: 8px;
      }

      .input-wrapper {
        position: relative;
        display: flex;
        align-items: center;
      }

      .input-icon-left {
        position: absolute;
        left: 16px;
        color: #94a3b8;
        display: flex;
        align-items: center;
        justify-content: center;
        pointer-events: none;
        transition: color 0.2s;
      }

      .input-icon-left i,
      .input-icon-left svg {
        width: 18px;
        height: 18px;
      }

      .custom-input {
        width: 100%;
        padding: 13px 16px 13px 46px;
        font-size: 14px;
        font-weight: 500;
        color: #0f172a;
        background: #f8fafc;
        border: 1.5px solid #e2e8f0;
        border-radius: 12px;
        transition: all 0.2s ease;
        outline: none;
      }

      .custom-input::placeholder {
        color: #94a3b8;
        font-weight: 400;
      }

      .custom-input:hover {
        border-color: #cbd5e1;
        background: #ffffff;
      }

      .custom-input:focus {
        border-color: var(--primary-dark);
        background: #ffffff;
        box-shadow: 0 0 0 4px rgba(6, 70, 53, 0.1);
      }

      .input-wrapper:focus-within .input-icon-left {
        color: var(--primary-dark);
      }

      .btn-toggle-password {
        position: absolute;
        right: 14px;
        background: none;
        border: none;
        padding: 4px;
        color: #94a3b8;
        cursor: pointer;
        display: flex;
        align-items: center;
        justify-content: center;
        transition: color 0.2s;
      }

      .btn-toggle-password:hover {
        color: var(--primary-dark);
      }

      .btn-toggle-password i,
      .btn-toggle-password svg {
        width: 18px;
        height: 18px;
      }

      .error-container {
        font-size: 13px;
        color: #dc2626;
        background: #fef2f2;
        border: 1px solid #fecaca;
        border-radius: 10px;
        padding: 0;
        margin: 0;
        max-height: 0;
        overflow: hidden;
        opacity: 0;
        transition: all 0.3s ease;
      }

      .error-container.show {
        max-height: 120px;
        opacity: 1;
        padding: 12px 14px;
        margin-bottom: 20px;
      }

      .custom-input.error {
        border-color: #ef4444;
        background: #fff5f5;
      }

      .btn-submit-login {
        width: 100%;
        padding: 14px 20px;
        font-size: 15px;
        font-weight: 700;
        color: #ffffff;
        background: linear-gradient(135deg, #064635 0%, #519259 100%);
        border: none;
        border-radius: 12px;
        cursor: pointer;
        transition: all 0.25s ease;
        display: flex;
        align-items: center;
        justify-content: center;
        gap: 8px;
        box-shadow: 0 4px 14px rgba(6, 70, 53, 0.3);
        margin-top: 8px;
      }

      .btn-submit-login:hover {
        background: linear-gradient(135deg, #053a2c 0%, #46804d 100%);
        transform: translateY(-2px);
        box-shadow: 0 8px 22px rgba(6, 70, 53, 0.4);
      }

      .btn-submit-login:active {
        transform: translateY(0);
      }

      .btn-submit-login:disabled {
        opacity: 0.65;
        cursor: not-allowed;
        transform: none;
      }

      .btn-submit-login i,
      .btn-submit-login svg {
        width: 18px;
        height: 18px;
      }

      .form-footer-info {
        font-size: 12px;
        color: #94a3b8;
        text-align: center;
        margin-top: 24px;
      }

      @media (max-width: 1024px) {
        .hero-panel {
          display: none;
        }

        .form-panel {
          flex: 1;
          padding: 40px 24px;
          min-height: 100vh;
        }

        .form-logo-wrap {
          margin-bottom: 20px;
        }

        .form-header h2 {
          font-size: 24px;
        }
      }
    </style>
  </head>

  <body>
    @include('layouts.loader')

    <div class="login-wrapper">
      <!-- ─── LEFT PANEL: STUNNING FLOATING PRODUCT SHOWCASE ─── -->
      <div class="hero-panel">
        <div class="hero-glow-1"></div>
        <div class="hero-glow-2"></div>



        <!-- Dynamic Overlapping Floating Cards Showcase Stage -->
        <div class="hero-visual-stage">
          
          <!-- CARD 1: Top Center-Left (Total Inventaris & Donut Chart) -->
          <div class="kezak-floating-card card-1-top">
            <div class="card-head">
              <span>Inventaris Aset Desa</span>
            </div>
            <div class="stat-value-big">1.428 Unit</div>
            <div class="stat-sub-label">Total Aset Terdata Pemerintah Desa</div>

            <div class="donut-and-details">
              <svg class="donut-svg-wrap" viewBox="0 0 36 36">
                <!-- Segment 1: Baik (87.5%) -->
                <path d="M18 2.0845 a 15.9155 15.9155 0 0 1 0 31.831 a 15.9155 15.9155 0 0 1 0 -31.831"
                      fill="none" stroke="#e2e8f0" stroke-width="3.8"/>
                <path d="M18 2.0845 a 15.9155 15.9155 0 1 1 -11.25 4.66"
                      fill="none" stroke="#10b981" stroke-width="4.2" stroke-linecap="round"/>
                <!-- Segment 2: Rusak Ringan (8.4%) -->
                <path d="M6.75 6.74 a 15.9155 15.9155 0 0 1 4.5 -3.2"
                      fill="none" stroke="#f59e0b" stroke-width="4.2" stroke-linecap="round"/>
                <!-- Segment 3: Rusak Berat (4.1%) -->
                <path d="M11.25 3.54 a 15.9155 15.9155 0 0 1 6.75 -1.45"
                      fill="none" stroke="#ef4444" stroke-width="4.2" stroke-linecap="round"/>
              </svg>

              <div class="donut-legend-col">
                <div class="legend-row">
                  <span class="legend-left"><span class="dot-pill baik"></span>Baik</span>
                  <span class="legend-val">1.250</span>
                </div>
                <div class="legend-row">
                  <span class="legend-left"><span class="dot-pill rr"></span>Rusak Ringan</span>
                  <span class="legend-val">120</span>
                </div>
                <div class="legend-row">
                  <span class="legend-left"><span class="dot-pill rb"></span>Rusak Berat</span>
                  <span class="legend-val">58</span>
                </div>
              </div>
            </div>
          </div>

          <!-- CARD 2: Middle Right (Target & Distribusi Jenis KIB) -->
          <div class="kezak-floating-card card-2-mid">
            <div class="card-head">
              <span>Distribusi Jenis KIB</span>
              <span class="badge-kib">Laporan KIB</span>
            </div>

            <div class="kib-progress-item">
              <div class="kib-item-header">
                <span>Peralatan & Mesin (KIB B)</span>
                <span class="kib-item-val">124 Unit</span>
              </div>
              <div class="kib-bar-track">
                <div class="kib-bar-fill" style="width: 82%;"></div>
              </div>
            </div>

            <div class="kib-progress-item">
              <div class="kib-item-header">
                <span>Gedung & Bangunan (KIB C)</span>
                <span class="kib-item-val">18 Unit</span>
              </div>
              <div class="kib-bar-track">
                <div class="kib-bar-fill" style="width: 90%;"></div>
              </div>
            </div>

            <div class="kib-progress-item">
              <div class="kib-item-header">
                <span>Jalan & Irigasi Desa (KIB D)</span>
                <span class="kib-item-val">42 Lokasi</span>
              </div>
              <div class="kib-bar-track">
                <div class="kib-bar-fill" style="width: 94%;"></div>
              </div>
            </div>
          </div>

          <!-- CARD 3: Bottom Left Overlay (Nilai Perolehan & Action Trigger) -->
          <div class="kezak-floating-card card-3-bottom">
            <div class="card-head">
              <span>Total Nilai Perolehan Aset</span>
              <span class="sync-tag">
                <i data-feather="refresh-cw" style="width:10px;height:10px;"></i> Real-time
              </span>
            </div>

            <div class="total-value-row">
              <span class="main-amount">Rp 3,85 Miliar</span>
              <span class="trend-badge">+12.4%</span>
            </div>

            <div class="quick-log-box">
              <div class="quick-log-item">
                <div class="log-details">
                  <strong>Aset Baru: Laptop ThinkPad T14</strong>
                  <span>Kode: INV-2026-004 &bull; QR Code Terverifikasi</span>
                </div>
              </div>
            </div>

            <button type="button" class="card-cta-btn">
              <span>Lihat Detail Inventaris</span>
              <i data-feather="arrow-right"></i>
            </button>
          </div>

        </div>

        <!-- Hero Bottom Headline & Subtitle -->
        <div class="hero-footer">
          <h3 class="hero-headline">Sistem Manajemen Aset Desa</h3>
          <p class="hero-desc">
            Platform digitalisasi tata kelola, inventarisasi, dan pelaporan aset desa yang transparan, akuntabel, dan terintegrasi.
          </p>
        </div>
      </div>

      <!-- ─── RIGHT PANEL: LOGIN FORM ─── -->
      <div class="form-panel">
        <div></div> <!-- Top Spacer -->

        <div class="form-center-container">
          <!-- Form Header -->
          <div class="form-header">
            <div class="form-logo-wrap" style="display:flex;flex-direction:column;align-items:center;gap:6px;margin-bottom:28px;">
              <img src="{{ asset('assets/images/logo/logo-demo.png') }}" alt="SIMA Logo" style="width:56px;height:56px;object-fit:contain;" />
              <div style="text-align:center;">
                <div style="font-size:18px;font-weight:800;color:var(--primary-dark);letter-spacing:0.5px;line-height:1.3;">SIMA</div>
                <div style="font-size:12px;font-weight:500;color:#64748b;">Desa Demo</div>
              </div>
            </div>
            <h2>Selamat Datang Kembali</h2>
            <p>Silakan masuk dengan username dan password Anda untuk mengelola sistem.</p>
          </div>

          <!-- Error Container -->
          <div class="error-container" id="errorContainer"></div>

          @if (session('status'))
            <div style="background:#f0fdf4;border:1px solid #bbf7d0;border-radius:10px;padding:12px 14px;margin-bottom:20px;font-size:13px;color:#15803d;font-weight:500;">
              {{ session('status') }}
            </div>
          @endif

          <!-- Login Form -->
          <form id="loginForm" method="POST" action="{{ route('login.perform') }}">
            @csrf
            <div class="field-group">
              <label class="field-label" for="username">Username</label>
              <div class="input-wrapper">
                <span class="input-icon-left">
                  <i data-feather="user"></i>
                </span>
                <input 
                  type="text" 
                  id="username" 
                  name="username" 
                  class="custom-input" 
                  placeholder="Masukkan username Anda" 
                  required 
                  autocomplete="username"
                />
              </div>
            </div>

            <div class="field-group">
              <label class="field-label" for="password">Password</label>
              <div class="input-wrapper">
                <span class="input-icon-left">
                  <i data-feather="lock"></i>
                </span>
                <input 
                  type="password" 
                  id="password" 
                  name="password" 
                  class="custom-input" 
                  placeholder="Masukkan password" 
                  required 
                  autocomplete="current-password"
                />
                <button type="button" class="btn-toggle-password" onclick="togglePassword()" title="Tampilkan/Sembunyikan password">
                  <i data-feather="eye" id="eyeIcon"></i>
                </button>
              </div>
            </div>

            <button type="submit" class="btn-submit-login">
              <span>Masuk Sistem</span>
              <i data-feather="arrow-right"></i>
            </button>
          </form>
        </div>

        <!-- Form Footer Info -->
        <div class="form-footer-info">
          Sistem Manajemen Aset Desa Demo v1.0.0.
        </div>
      </div>
    </div>

    @include('layouts.footer-js')
    @include('layouts.notification')

    <script>
      // Initialize Feather Icons on Load
      document.addEventListener('DOMContentLoaded', function () {
        if (typeof feather !== 'undefined') {
          feather.replace();
        }
      });

      // Toggle Password Visibility
      function togglePassword() {
        const passwordInput = document.getElementById('password');
        const eyeIcon = document.getElementById('eyeIcon');
        
        if (passwordInput.type === 'password') {
          passwordInput.type = 'text';
          eyeIcon.setAttribute('data-feather', 'eye-off');
        } else {
          passwordInput.type = 'password';
          eyeIcon.setAttribute('data-feather', 'eye');
        }
        if (typeof feather !== 'undefined') feather.replace();
      }

      // Form Handling & Errors
      const loginForm = document.getElementById('loginForm');
      const loginBtn = loginForm.querySelector('.btn-submit-login');
      let errorTimeout;

      function showError(message) {
        const errorContainer = document.getElementById('errorContainer');
        errorContainer.textContent = message;
        errorContainer.classList.add('show');
        
        if (errorTimeout) clearTimeout(errorTimeout);
        errorTimeout = setTimeout(() => {
          errorContainer.classList.remove('show');
        }, 5000);
      }

      function hideError() {
        const errorContainer = document.getElementById('errorContainer');
        errorContainer.classList.remove('show');
        if (errorTimeout) clearTimeout(errorTimeout);
      }

      loginForm.addEventListener('submit', async function(e) {
        e.preventDefault();
        hideError();
        document.querySelectorAll('.custom-input').forEach(el => el.classList.remove('error'));
        
        if (typeof AppLoader !== 'undefined') AppLoader.show();
        loginBtn.disabled = true;
        
        try {
          const formData = new FormData(loginForm);
          const response = await fetch(loginForm.action, {
            method: 'POST',
            headers: {
              'X-Requested-With': 'XMLHttpRequest',
              'Accept': 'application/json'
            },
            body: formData
          });
          
          const data = await response.json();
          
          if (response.ok && data.success) {
            window.location.href = data.redirect || "{{ route('dashboard') }}";
          } else {
            if (typeof AppLoader !== 'undefined') AppLoader.hide();
            
            if (data.errors) {
              const errorMessages = [];
              Object.keys(data.errors).forEach(field => {
                const inputField = document.getElementById(field);
                if (inputField) inputField.classList.add('error');
                const fieldName = field === 'username' ? 'Username' : 'Password';
                errorMessages.push(`${fieldName}: ${data.errors[field][0]}`);
              });
              
              if (errorMessages.length > 0) {
                showError(errorMessages.join(' • '));
              }
            } else if (data.message) {
              showError(data.message);
            }
            loginBtn.disabled = false;
          }
        } catch (error) {
          if (typeof AppLoader !== 'undefined') AppLoader.hide();
          console.error('Login error:', error);
          showError('Terjadi kesalahan koneksi. Silakan coba lagi.');
          loginBtn.disabled = false;
        }
      });
    </script>
  </body>
</html>
