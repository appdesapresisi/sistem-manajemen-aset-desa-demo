@php
    $defaultDashboardUrl = auth()->check() && \Illuminate\Support\Facades\Route::has('dashboard')
        ? route('dashboard')
        : (\Illuminate\Support\Facades\Route::has('login') ? route('login') : url('/'));
    $defaultDashboardLabel = 'Kembali ke Dashboard';

    $primaryUrl = trim($__env->yieldContent('primary_action_url')) ?: $defaultDashboardUrl;
    $primaryLabel = trim($__env->yieldContent('primary_action_label')) ?: $defaultDashboardLabel;
    $secondaryUrl = trim($__env->yieldContent('secondary_action_url')) ?: '';
    $secondaryLabel = trim($__env->yieldContent('secondary_action_label')) ?: '';

    $statusCode = trim($__env->yieldContent('status_code')) ?: '500';
    $statusTitle = trim($__env->yieldContent('status_title')) ?: 'Terjadi Kendala pada Sistem';
    $statusMessage = trim($__env->yieldContent('status_message')) ?: 'Halaman yang Anda tuju sedang tidak dapat diproses oleh sistem.';

    // In Kartunopolis layout, the pill button is the main return action
    $pillActionUrl = $primaryUrl ?: $defaultDashboardUrl;
    $pillActionLabel = $primaryLabel ?: $defaultDashboardLabel;
@endphp
<!DOCTYPE html>
<html lang="id">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <title>@yield('title', $statusCode . ' — Terjadi Kendala') — {{ config('app.name', 'SIMA Desa') }}</title>

  <link rel="preconnect" href="https://fonts.googleapis.com">
  <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
  <link href="https://fonts.googleapis.com/css2?family=Fredoka:wght@600;700&family=Outfit:wght@300;400;500;600;700&family=Plus+Jakarta+Sans:wght@400;500;600;700;800&display=swap" rel="stylesheet">

  <style>
    :root {
      --bg-top: #cfdcc4;
      --bg-mid: #e8f0e2;
      --bg-bottom: #ffffff;
      --big-num: rgba(255, 255, 255, 0.82);
      --ink: #16251b;
      --muted: #546958;
      --accent: #1f3a2a;
      --accent-hover: #162c1f;
      --accent-tint: #d5e4d7;
    }

    * {
      box-sizing: border-box;
      margin: 0;
      padding: 0;
    }

    html, body {
      height: 100%;
    }

    body {
      font-family: 'Outfit', 'Plus Jakarta Sans', system-ui, sans-serif;
      color: var(--ink);
      background: linear-gradient(180deg, var(--bg-top) 0%, var(--bg-mid) 52%, var(--bg-bottom) 100%);
      overflow-x: hidden;
      position: relative;
    }

    /* TOP NAVBAR (KARTUNOPOLIS STYLE) */
    .topbar {
      position: absolute;
      top: 0;
      left: 0;
      right: 0;
      z-index: 20;
      max-width: 1160px;
      margin: 0 auto;
      padding: 28px 32px;
      display: flex;
      align-items: center;
      justify-content: space-between;
    }

    .topbar-brand {
      display: flex;
      align-items: center;
      gap: 12px;
      text-decoration: none;
      color: var(--ink);
    }

    .topbar-brand img {
      height: 34px;
      width: auto;
      object-fit: contain;
    }

    .topbar-brand span {
      font-family: 'Fredoka', 'Outfit', sans-serif;
      font-weight: 700;
      font-size: 20px;
      letter-spacing: -0.01em;
      color: var(--accent);
    }

    .topbar-badge {
      font-size: 12px;
      font-weight: 700;
      letter-spacing: 0.06em;
      text-transform: uppercase;
      color: var(--accent);
      opacity: 0.85;
    }

    /* PANGGUNG UTAMA (KARTUNOPOLIS STAGE) */
    .stage {
      position: relative;
      min-height: 100vh;
      max-width: 1160px;
      margin: 0 auto;
      display: flex;
      align-items: center;
      justify-content: center;
    }

    /* Angka besar transparan */
    .big {
      position: absolute;
      top: 10vh;
      left: 50%;
      transform: translateX(-50%);
      font-family: 'Outfit', 'Plus Jakarta Sans', sans-serif;
      font-weight: 700;
      font-size: clamp(220px, 36vw, 460px);
      line-height: 0.88;
      letter-spacing: -0.04em;
      color: var(--big-num);
      white-space: nowrap;
      user-select: none;
      text-shadow: 0 20px 48px rgba(31, 58, 42, 0.06);
      z-index: 1;
    }

    /* Ilustrasi / Maskot — Kiri bawah, menimpa angka */
    .mascot {
      position: absolute;
      left: 6%;
      bottom: 10vh;
      z-index: 3;
      width: clamp(320px, 36vw, 470px);
      height: auto;
      filter: drop-shadow(0 20px 28px rgba(31, 58, 42, 0.16));
      animation: floatMascot 5s ease-in-out infinite;
      user-select: none;
      pointer-events: none;
    }

    @keyframes floatMascot {
      0%, 100% { transform: translateY(0); }
      50% { transform: translateY(-12px); }
    }

    /* Teks sisi kanan */
    .copy {
      position: absolute;
      left: 42%;
      right: 4%;
      bottom: 12vh;
      z-index: 3;
      text-align: center;
    }

    .copy h1 {
      font-family: 'Fredoka', 'Outfit', sans-serif;
      font-weight: 700;
      font-size: clamp(28px, 3.5vw, 42px);
      line-height: 1.22;
      letter-spacing: -0.02em;
      color: var(--ink);
      margin-bottom: 14px;
    }

    .copy p {
      margin: 0 auto;
      max-width: 440px;
      font-size: 14.5px;
      line-height: 1.75;
      color: var(--muted);
      font-weight: 400;
    }

    /* Tombol mengambang pill */
    .btn-floating-pill {
      position: absolute;
      right: 8%;
      top: 53%;
      z-index: 4;
      display: inline-flex;
      align-items: center;
      gap: 10px;
      background: rgba(255, 255, 255, 0.75);
      backdrop-filter: blur(10px);
      border: 1px solid rgba(255, 255, 255, 0.9);
      padding: 10px 22px;
      border-radius: 999px;
      color: var(--ink);
      text-decoration: none;
      font-size: 13.5px;
      font-weight: 600;
      box-shadow: 0 8px 24px rgba(31, 58, 42, 0.12);
      transition: transform 0.2s ease, box-shadow 0.2s ease, background 0.2s ease, color 0.2s ease;
    }

    .btn-floating-pill:hover {
      transform: translateY(-2px);
      background: rgba(255, 255, 255, 0.95);
      color: var(--accent);
    }

    .btn-floating-pill svg {
      width: 17px;
      height: 17px;
      stroke: var(--accent);
      flex-shrink: 0;
      transition: transform 0.2s ease;
    }

    .btn-floating-pill:hover svg {
      transform: scale(1.08);
    }

    /* MOBILE RESPONSIVE */
    @media (max-width: 860px) {
      .topbar {
        position: relative;
        padding: 20px 24px;
        justify-content: center;
      }
      .topbar-badge {
        display: none;
      }
      .big {
        position: relative;
        top: auto;
        left: auto;
        transform: none;
        font-size: clamp(140px, 32vw, 220px);
        margin-top: 10px;
      }
      .mascot {
        position: relative;
        display: block;
        left: auto;
        bottom: auto;
        margin: -40px auto 0;
        width: 58vw;
        max-width: 280px;
      }
      .copy {
        position: relative;
        left: auto;
        right: auto;
        bottom: auto;
        padding: 0 24px;
        margin-top: 14px;
      }
      .btn-floating-pill {
        position: relative;
        right: auto;
        top: auto;
        margin: 24px auto 16px;
      }
      .stage {
        flex-direction: column;
        align-items: center;
        min-height: auto;
        padding-bottom: 40px;
      }
    }

    @media (prefers-reduced-motion: reduce) {
      .mascot {
        animation: none;
      }
    }
  </style>
</head>
<body>

  <!-- Top Navigation Bar -->
  <header class="topbar">
    <a href="{{ $defaultDashboardUrl }}" class="topbar-brand">
      <img src="{{ asset('assets/images/logo/logo-sima-desa-demo.png') }}" alt="SIMA Desa" onerror="this.style.display='none'">
      <span>SIMA Desa</span>
    </a>
    <div class="topbar-badge">
      Status: HTTP {{ $statusCode }}
    </div>
  </header>

  <!-- Kartunopolis Stage -->
  <main class="stage">
    <!-- Giant Code Watermark -->
    <div class="big" aria-hidden="true">{{ $statusCode }}</div>

    <!-- Mascot / Illustration Asset -->
    <img class="mascot" 
         src="{{ asset('assets/images/errors/Error-Handling.svg') }}" 
         alt="Ilustrasi kendala sistem {{ $statusCode }}">

    <!-- Floating Pill Button -->
    <a class="btn-floating-pill" href="{{ $pillActionUrl }}" title="{{ $pillActionLabel }}">
      @if(str_contains(strtolower($pillActionLabel), 'dashboard') || str_contains(strtolower($pillActionLabel), 'base camp') || str_contains(strtolower($pillActionLabel), 'utama'))
        <svg width="17" height="17" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round">
          <rect x="3" y="3" width="7" height="7"></rect>
          <rect x="14" y="3" width="7" height="7"></rect>
          <rect x="14" y="14" width="7" height="7"></rect>
          <rect x="3" y="14" width="7" height="7"></rect>
        </svg>
      @elseif(str_contains(strtolower($pillActionLabel), 'muat ulang') || str_contains(strtolower($pillActionLabel), 'refresh') || str_contains(strtolower($pillActionLabel), 'coba lagi'))
        <svg width="17" height="17" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round">
          <polyline points="1 4 1 10 7 10"></polyline>
          <path d="M3.51 15a9 9 0 1 0 2.13-9.36L1 10"></path>
        </svg>
      @else
        <svg width="17" height="17" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round">
          <line x1="19" y1="12" x2="5" y2="12"></line>
          <polyline points="12 19 5 12 12 5"></polyline>
        </svg>
      @endif
      <span>{{ $pillActionLabel }}</span>
    </a>

    <!-- Copy Content -->
    <div class="copy">
      <h1>{!! nl2br(e($statusTitle)) !!}</h1>
      <p>{{ $statusMessage }}</p>
    </div>
  </main>

</body>
</html>
