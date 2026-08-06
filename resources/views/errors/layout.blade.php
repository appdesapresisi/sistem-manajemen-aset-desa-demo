@php
    $defaultPrimaryUrl = auth()->check() && \Illuminate\Support\Facades\Route::has('dashboard')
        ? route('dashboard')
        : (\Illuminate\Support\Facades\Route::has('login') ? route('login') : url('/'));
    $defaultPrimaryLabel = auth()->check() ? 'Ke Dashboard' : 'Ke Login';
    $defaultSecondaryUrl = url()->previous() !== url()->current() ? url()->previous() : url('/');

    $primaryUrl = trim($__env->yieldContent('primary_action_url')) ?: $defaultPrimaryUrl;
    $primaryLabel = trim($__env->yieldContent('primary_action_label')) ?: $defaultPrimaryLabel;
    $secondaryUrl = trim($__env->yieldContent('secondary_action_url')) ?: $defaultSecondaryUrl;
    $secondaryLabel = trim($__env->yieldContent('secondary_action_label')) ?: 'Kembali';
    $variant = trim($__env->yieldContent('status_variant')) ?: 'info';

    $userName   = auth()->check() ? (auth()->user()->nama ?? auth()->user()->username ?? '-') : 'Tamu';
    $userRole   = auth()->check() ? (optional(auth()->user()->role)->nama ?? '-') : 'Tidak ada sesi aktif';
@endphp
<!doctype html>
<html lang="id">
  <head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>@yield('title', 'Terjadi Kendala') - {{ config('app.name') }}</title>
    <style>
      :root {
        --primary:        #064635;
        --primary-deep:   #283548;
        --accent:         #68a7db;
        --text:           #1f2937;
        --muted:          #6b7280;
        --line:           rgba(15, 23, 42, 0.09);
        --info-soft:      #e0f2fe; --info-line:    #bae6fd; --info-text:    #075985;
        --warning-soft:   #fff7ed; --warning-line: #fed7aa; --warning-text: #9a3412;
        --danger-soft:    #fef2f2; --danger-line:  #fecaca; --danger-text:  #b91c1c;
        --success-soft:   #ecfdf5; --success-line: #bbf7d0; --success-text: #166534;
      }

      *, *::before, *::after { box-sizing: border-box; margin: 0; padding: 0; }

      html, body {
        height: 100vh;
        overflow: hidden;
      }

      body {
        font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
        color: var(--text);
        background: linear-gradient(145deg, #f1f5f9 0%, #e4ecf5 100%);
        display: flex;
        flex-direction: column;
        align-items: center;
        justify-content: center;
        gap: 14px;
        padding: 20px;
      }

      /* -- Card -- */
      .card {
        width: 100%;
        max-width: 500px;
        background: #fff;
        border-radius: 18px;
        box-shadow: 0 4px 28px rgba(15, 23, 42, 0.11), 0 1px 4px rgba(15, 23, 42, 0.06);
        overflow: hidden;
      }

      /* -- Card header (dark strip) -- */
      .card-head {
        background: linear-gradient(150deg, var(--primary) 0%, var(--primary-deep) 100%);
        padding: 20px 24px 18px;
        display: flex;
        align-items: center;
        gap: 16px;
        color: #fff;
      }
      .err-code {
        font-size: 52px;
        font-weight: 800;
        line-height: 1;
        letter-spacing: -0.05em;
        color: rgba(255,255,255,0.92);
        flex-shrink: 0;
      }
      .head-divider {
        width: 2px;
        height: 48px;
        background: rgba(255,255,255,0.18);
        border-radius: 99px;
        flex-shrink: 0;
      }
      .head-meta { flex: 1; min-width: 0; }
      .head-meta .badge {
        display: inline-block;
        padding: 3px 9px;
        border-radius: 99px;
        font-size: 10px;
        font-weight: 700;
        letter-spacing: 0.05em;
        margin-bottom: 5px;
      }
      .badge.info    { background: var(--info-soft);    color: var(--info-text);    border: 1px solid var(--info-line);    }
      .badge.warning { background: var(--warning-soft); color: var(--warning-text); border: 1px solid var(--warning-line); }
      .badge.danger  { background: var(--danger-soft);  color: var(--danger-text);  border: 1px solid var(--danger-line);  }
      .badge.success { background: var(--success-soft); color: var(--success-text); border: 1px solid var(--success-line); }
      .head-meta h1 {
        font-size: 15px;
        font-weight: 700;
        line-height: 1.35;
        color: #fff;
      }

      /* -- Card body -- */
      .card-body { padding: 26px 28px 28px; }

      .lead {
        font-size: 13.5px;
        color: var(--muted);
        line-height: 1.75;
        margin-bottom: 18px;
      }

      /* -- Hint box -- */
      .hint {
        background: #f4faf5;
        border: 1px solid var(--line);
        border-left: 3px solid var(--accent);
        border-radius: 0 10px 10px 0;
        padding: 13px 16px;
        margin-bottom: 18px;
      }
      .hint.note { border-left-color: #f59e0b; }
      .hint strong {
        display: block;
        font-size: 10px;
        font-weight: 700;
        text-transform: uppercase;
        letter-spacing: 0.06em;
        color: var(--primary);
        margin-bottom: 5px;
      }
      .hint p { font-size: 12.5px; color: var(--muted); line-height: 1.65; }

      /* -- Info grid -- */
      .info-grid {
        display: grid;
        grid-template-columns: 1fr 1fr 1fr;
        gap: 1px;
        background: var(--line);
        border: 1px solid var(--line);
        border-radius: 10px;
        overflow: hidden;
        margin-bottom: 18px;
      }
      .info-cell {
        background: #f4faf5;
        padding: 12px 14px;
      }
      .info-cell label {
        display: block;
        font-size: 9.5px;
        font-weight: 700;
        text-transform: uppercase;
        letter-spacing: 0.07em;
        color: #94a3b8;
        margin-bottom: 4px;
      }
      .info-cell span {
        display: block;
        font-size: 12px;
        font-weight: 600;
        color: var(--text);
        white-space: nowrap;
        overflow: hidden;
        text-overflow: ellipsis;
      }

      /* -- Actions -- */
      .actions { display: flex; gap: 10px; }
      .btn {
        flex: 1;
        display: inline-flex;
        align-items: center;
        justify-content: center;
        padding: 12px 14px;
        border-radius: 9px;
        text-decoration: none;
        font-size: 13.5px;
        font-weight: 700;
        transition: transform 0.15s ease, box-shadow 0.15s ease;
      }
      .btn:hover { transform: translateY(-1px); }
      .btn-primary {
        background: linear-gradient(135deg, var(--primary) 0%, var(--primary-deep) 100%);
        color: #fff;
        box-shadow: 0 4px 14px rgba(6, 70, 53, 0.28);
      }
      .btn-primary:hover { box-shadow: 0 6px 18px rgba(6, 70, 53, 0.36); }
      .btn-secondary {
        background: rgba(6, 70, 53, 0.07);
        color: var(--primary);
        border: 1px solid rgba(6, 70, 53, 0.14);
      }
    </style>
  </head>
  <body>
    <main class="card" role="main" aria-labelledby="error-title">

      {{-- -- Header strip -- --}}
      <div class="card-head">
        <div class="err-code" aria-label="Kode error @yield('status_code', '500')">@yield('status_code', '500')</div>
        <div class="head-divider" aria-hidden="true"></div>
        <div class="head-meta">
          <span class="badge {{ $variant }}">HTTP @yield('status_code', '500')</span>
          <h1 id="error-title">@yield('status_title', 'Terjadi kendala pada sistem')</h1>
        </div>
      </div>

      {{-- -- Body -- --}}
      <div class="card-body">

        <p class="lead">@yield('status_message', 'Halaman ini muncul saat sistem perlu menghentikan alur normal.')</p>

        <div class="hint">
          <strong>Yang bisa dilakukan</strong>
          <p>@yield('status_hint', 'Coba kembali beberapa saat lagi, atau kembali ke halaman sebelumnya.')</p>
        </div>

        @hasSection('extra_note')
          <div class="hint note">
            <strong>Catatan</strong>
            <p>@yield('extra_note')</p>
          </div>
        @endif

        {{-- -- Info grid -- --}}
        <div class="info-grid" aria-label="Informasi konteks">
          <div class="info-cell">
            <label>Pengguna</label>
            <span title="{{ $userName }}">{{ $userName }}</span>
          </div>
          <div class="info-cell">
            <label>Peran</label>
            <span>{{ $userRole }}</span>
          </div>
          <div class="info-cell">
            <label>Waktu</label>
            <span>{{ now()->format('d M Y, H:i') }}</span>
          </div>
        </div>

        {{-- -- Buttons -- --}}
        <div class="actions">
          <a href="{{ $primaryUrl }}" class="btn btn-primary">{{ $primaryLabel }}</a>
          <a href="{{ $secondaryUrl }}" class="btn btn-secondary">{{ $secondaryLabel }}</a>
        </div>

      </div>
    </main>
  </body>
</html>
