<!doctype html>
<html lang="id" data-pc-preset="preset-1" data-pc-sidebar-theme="light" data-pc-sidebar-caption="true">
  <head>
    @include('layouts.head-page-meta', ['title' => 'Login - Sistem Manajemen Aset Desa'])
    @include('layouts.head-css')
    <!-- Feather Icons CDN -->
    <script src="https://cdn.jsdelivr.net/npm/feather-icons/dist/feather.min.js"></script>
    <style>
      :root {
        --color-primary: #1f3a2a;
        --color-primary-hover: #162c1f;
        --color-primary-soft: #2e523b;
        --color-primary-tint: #d5e4d7;
        --color-accent: #C9A24A;
        --color-surface: #f8faf7;
        --color-bg: #eaf1e7;
        --color-border: #cfdcc8;
        --color-border-subtle: #dce5d8;
        --color-text: #16251b;
        --color-text-muted: #546958;
        --color-text-on-primary: #eaf1e7;
        --radius-sm: 8px;
        --radius-md: 12px;
        --radius-lg: 16px;
        --radius-xl: 20px;
        --shadow-card: 0 24px 48px -12px rgba(2, 26, 19, 0.45);
        --shadow-subtle: 0 4px 16px rgba(0, 0, 0, 0.04);
      }

      * {
        box-sizing: border-box;
        margin: 0;
        padding: 0;
      }

      html, body {
        height: 100%;
        width: 100%;
        margin: 0;
        padding: 0;
        font-family: 'Inter', -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, sans-serif;
        background: var(--color-bg);
        color: var(--color-text);
        overflow: hidden;
      }

      /* ════════════════════════════════════════════════════════════════════
         BALANCED 50% / 50% SPLIT CANVAS (Exact Dribbble Kezak Layout)
         ════════════════════════════════════════════════════════════════════ */
      .kezak-login-wrapper {
        display: flex;
        width: 100vw;
        height: 100vh;
        overflow: hidden;
        background: var(--color-bg);
      }

      /* ════════════════════════════════════════════════════════════════════
         LEFT COLUMN: 50% CLEAN WARM NEUTRAL FORM CANVAS
         ════════════════════════════════════════════════════════════════════ */
      .kezak-form-side {
        flex: 0 0 50%;
        width: 50%;
        max-width: 50%;
        height: 100vh;
        background: var(--color-bg);
        display: flex;
        flex-direction: column;
        justify-content: space-between;
        padding: 44px 64px 36px 64px;
        overflow-y: auto;
        position: relative;
        z-index: 2;
        border-right: 1px solid var(--color-border);
      }

      /* Brand Logo Header */
      .kezak-brand-header {
        display: flex;
        flex-direction: column;
        align-items: center;
        justify-content: center;
        gap: 8px;
        text-decoration: none;
        width: fit-content;
        margin: 0 auto 16px auto;
        text-align: center;
      }

      .kezak-brand-text {
        display: flex;
        flex-direction: column;
        align-items: center;
        text-align: center;
      }

      .kezak-brand-name {
        font-size: 20px;
        font-weight: 800;
        color: var(--color-primary);
        letter-spacing: -0.3px;
        line-height: 1.2;
      }

      .kezak-brand-tag {
        font-size: 12px;
        font-weight: 600;
        color: var(--color-text-muted);
        letter-spacing: 0.1px;
      }

      /* Form Body Center */
      .kezak-form-main {
        width: 100%;
        max-width: 440px;
        margin: auto;
        padding: 20px 0;
      }

      .kezak-form-headline {
        margin-bottom: 32px;
        text-align: center;
      }

      .kezak-form-headline h1 {
        font-size: 32px;
        font-weight: 800;
        color: var(--color-text);
        letter-spacing: -0.03em;
        line-height: 1.2;
        margin-bottom: 8px;
        text-align: center;
      }

      .kezak-form-headline p {
        font-size: 14px;
        color: var(--color-text-muted);
        line-height: 1.5;
        text-align: center;
        max-width: 360px;
        margin: 0 auto;
      }

      /* Inputs & Labels */
      .kezak-field {
        margin-bottom: 22px;
      }

      .kezak-label {
        display: block;
        font-size: 13.5px;
        font-weight: 600;
        color: var(--color-text);
        margin-bottom: 8px;
        transition: color 0.15s ease;
      }

      .kezak-label .req {
        color: #a63d2f;
        margin-left: 2px;
      }

      /* Judul input field ikut warna merah saat error (Standar Dribbble Slick UX) */
      .kezak-label.error,
      .kezak-field.has-error .kezak-label,
      .kezak-field:has(.kezak-input.error) .kezak-label,
      .kezak-field:has(.kezak-input.is-invalid) .kezak-label {
        color: #D9383A !important;
      }

      .kezak-label.error .req,
      .kezak-field.has-error .kezak-label .req,
      .kezak-field:has(.kezak-input.error) .kezak-label .req,
      .kezak-field:has(.kezak-input.is-invalid) .kezak-label .req {
        color: #D9383A !important;
      }

      .kezak-input-box {
        position: relative;
        display: flex;
        align-items: center;
      }

      .kezak-input-icon-left {
        position: absolute;
        left: 16px;
        top: 50%;
        transform: translateY(-50%);
        color: var(--color-text-muted);
        display: flex;
        align-items: center;
        justify-content: center;
        pointer-events: none;
      }

      .kezak-input-icon-left i, .kezak-input-icon-left svg {
        width: 18px;
        height: 18px;
      }

      .kezak-input {
        width: 100%;
        height: 50px;
        padding: 0 46px 0 46px;
        background: var(--color-surface);
        border: 1.5px solid var(--color-border);
        border-radius: var(--radius-md);
        font-size: 14.5px;
        color: var(--color-text);
        font-family: inherit;
        outline: none;
        box-shadow: none !important;
        transition: border-color 0.2s ease;
      }

      .kezak-input::placeholder {
        color: var(--color-text-muted);
        font-size: 14px;
      }

      .kezak-input:hover {
        border-color: #b8c9b3;
      }

      .kezak-input:focus,
      .kezak-input:focus-visible,
      .kezak-input:active {
        border-color: var(--color-primary) !important;
        background: var(--color-surface) !important;
        box-shadow: none !important;
        outline: none !important;
      }

      .kezak-input:-webkit-autofill,
      .kezak-input:-webkit-autofill:hover, 
      .kezak-input:-webkit-autofill:focus, 
      .kezak-input:-webkit-autofill:active {
        -webkit-box-shadow: 0 0 0 30px var(--color-surface) inset !important;
        -webkit-text-fill-color: var(--color-text) !important;
        transition: background-color 5000s ease-in-out 0s;
      }

      .kezak-input.error,
      .kezak-input.is-invalid,
      input.kezak-input.error,
      input.kezak-input.is-invalid,
      .kezak-field.has-error .kezak-input,
      .kezak-field.has-error input,
      .kezak-input.error:hover,
      .kezak-input.is-invalid:hover,
      .kezak-input.error:focus,
      .kezak-input.error:focus-visible,
      .kezak-input.is-invalid:focus,
      .kezak-input.is-invalid:focus-visible,
      .kezak-input.error:active,
      .kezak-input.is-invalid:active,
      input.kezak-input.error:focus,
      input.kezak-input.is-invalid:focus,
      .kezak-field.has-error .kezak-input:focus,
      .kezak-field.has-error input:focus {
        border: 1.5px solid #E53935 !important;
        border-color: #E53935 !important;
        background: var(--color-surface) !important;
        box-shadow: none !important;
        outline: none !important;
      }

      /* Dribbble Style Field Error Message (Below Input) */
      .kezak-field-error {
        display: none;
        align-items: flex-start;
        gap: 6px;
        margin-top: 7px;
        font-size: 12.5px;
        font-weight: 500;
        line-height: 1.4;
        color: #D9383A;
        animation: fadeInError 0.2s ease-out;
      }

      .kezak-field-error.show {
        display: flex !important;
      }

      .kezak-error-icon {
        width: 14px;
        height: 14px;
        flex-shrink: 0;
        margin-top: 1.5px;
      }

      @keyframes fadeInError {
        from { opacity: 0; transform: translateY(-3px); }
        to { opacity: 1; transform: translateY(0); }
      }

      .kezak-input-icon-right {
        position: absolute;
        right: 14px;
        top: 50%;
        transform: translateY(-50%);
        background: transparent;
        border: none;
        padding: 6px;
        cursor: pointer;
        color: var(--color-text-muted);
        display: flex;
        align-items: center;
        justify-content: center;
        border-radius: 6px;
        transition: color 0.15s;
      }

      .kezak-input-icon-right:hover {
        color: var(--color-primary);
      }

      .kezak-input-icon-right i, .kezak-input-icon-right svg {
        width: 18px;
        height: 18px;
      }

      /* Submit Button */
      .kezak-btn-submit {
        width: 100%;
        height: 50px;
        background: #1f3a2a;
        color: #ffffff;
        border: 1px solid #1f3a2a;
        border-radius: var(--radius-full);
        font-size: 15px;
        font-weight: 700;
        font-family: inherit;
        cursor: pointer;
        transition: background-color 0.2s ease, transform 0.2s ease, border-color 0.2s ease;
        display: flex;
        align-items: center;
        justify-content: center;
        gap: 8px;
        margin-top: 30vpx;
        box-shadow: none !important;
      }

      .kezak-btn-submit:hover {
        background: #162c1f;
        border-color: #162c1f;
        transform: translateY(-1px);
        box-shadow: none !important;
      }

      .kezak-btn-submit:active {
        transform: translateY(0);
        box-shadow: none !important;
      }

      .kezak-btn-submit:disabled {
        opacity: 0.65;
        cursor: not-allowed;
        transform: none;
      }

      /* Status Alert (for success flash) */
      .kezak-alert {
        display: none;
        padding: 12px 16px;
        border-radius: var(--radius-sm);
        font-size: 13.5px;
        line-height: 1.45;
        margin-bottom: 22px;
      }

      .kezak-alert.show {
        display: block;
      }

      .kezak-alert-success {
        background: #f0fdf4;
        border: 1px solid #bbf7d0;
        color: #16a34a;
      }

      /* Form Footer */
      .kezak-form-footer {
        font-size: 12.5px;
        color: #94a3b8;
        display: flex;
        flex-direction: column;
        align-items: center;
        justify-content: center;
        text-align: center;
        padding-top: 20px;
        gap: 4px;
      }

      .kezak-form-footer a {
        color: #64748b;
        text-decoration: none;
        transition: color 0.15s;
      }

      .kezak-form-footer a:hover {
        color: var(--color-primary);
      }


      /* ════════════════════════════════════════════════════════════════════
         RIGHT COLUMN: 50% RICH DEEP FOREST SHOWCASE
         ════════════════════════════════════════════════════════════════════ */
      .kezak-showcase-side {
        flex: 0 0 50%;
        width: 50%;
        max-width: 50%;
        height: 100vh;
        background: linear-gradient(155deg, #102418 0%, #1f3a2a 50%, #162c1f 100%);
        position: relative;
        display: flex;
        flex-direction: column;
        justify-content: space-between;
        align-items: center;
        padding: 44px 48px 32px 48px;
        overflow: hidden;
        color: #ffffff;
      }

      /* Decorative Floating Corner Squares (Dribbble 25809226 Style) */
      .kezak-grid-bg {
        display: none;
      }

      .kezak-corner-squares {
        position: absolute;
        pointer-events: none;
        z-index: 1;
      }

      .kezak-squares-tr {
        top: 0;
        right: 0;
        width: 320px;
        height: 320px;
      }

      .kezak-squares-bl {
        bottom: 0;
        left: 0;
        width: 320px;
        height: 320px;
      }

      .kezak-sq {
        position: absolute;
        background: rgba(213, 228, 215, 0.08);
        border-radius: 8px;
        backdrop-filter: blur(2px);
      }

      /* Top Right Squares */
      .kezak-squares-tr .sq-1 { width: 56px; height: 56px; top: 18px; right: 28px; border-radius: 10px; background: rgba(213, 228, 215, 0.12); }
      .kezak-squares-tr .sq-2 { width: 42px; height: 42px; top: 35px; right: 105px; border-radius: 8px; background: rgba(213, 228, 215, 0.07); }
      .kezak-squares-tr .sq-3 { width: 28px; height: 28px; top: 92px; right: 48px; border-radius: 6px; background: rgba(213, 228, 215, 0.14); }
      .kezak-squares-tr .sq-4 { width: 48px; height: 48px; top: 95px; right: 120px; border-radius: 9px; background: rgba(213, 228, 215, 0.06); }
      .kezak-squares-tr .sq-5 { width: 22px; height: 22px; top: 22px; right: 168px; border-radius: 5px; background: rgba(213, 228, 215, 0.10); }
      .kezak-squares-tr .sq-6 { width: 34px; height: 34px; top: 62px; right: 195px; border-radius: 7px; background: rgba(213, 228, 215, 0.08); }
      .kezak-squares-tr .sq-7 { width: 18px; height: 18px; top: 120px; right: 220px; border-radius: 4px; background: rgba(213, 228, 215, 0.13); }
      .kezak-squares-tr .sq-8 { width: 38px; height: 38px; top: 160px; right: 80px; border-radius: 8px; background: rgba(213, 228, 215, 0.05); }
      .kezak-squares-tr .sq-9 { width: 24px; height: 24px; top: 155px; right: 145px; border-radius: 6px; background: rgba(213, 228, 215, 0.09); }
      .kezak-squares-tr .sq-10 { width: 16px; height: 16px; top: 170px; right: 30px; border-radius: 4px; background: rgba(213, 228, 215, 0.12); }
      .kezak-squares-tr .sq-11 { width: 30px; height: 30px; top: 215px; right: 105px; border-radius: 7px; background: rgba(213, 228, 215, 0.06); }
      .kezak-squares-tr .sq-12 { width: 20px; height: 20px; top: 225px; right: 55px; border-radius: 5px; background: rgba(213, 228, 215, 0.09); }

      /* Bottom Left Squares */
      .kezak-squares-bl .sq-1 { width: 54px; height: 54px; bottom: 25px; left: 25px; border-radius: 10px; background: rgba(213, 228, 215, 0.12); }
      .kezak-squares-bl .sq-2 { width: 40px; height: 40px; bottom: 45px; left: 95px; border-radius: 8px; background: rgba(213, 228, 215, 0.07); }
      .kezak-squares-bl .sq-3 { width: 26px; height: 26px; bottom: 98px; left: 42px; border-radius: 6px; background: rgba(213, 228, 215, 0.13); }
      .kezak-squares-bl .sq-4 { width: 46px; height: 46px; bottom: 102px; left: 110px; border-radius: 9px; background: rgba(213, 228, 215, 0.06); }
      .kezak-squares-bl .sq-5 { width: 20px; height: 20px; bottom: 30px; left: 155px; border-radius: 5px; background: rgba(213, 228, 215, 0.10); }
      .kezak-squares-bl .sq-6 { width: 32px; height: 32px; bottom: 70px; left: 180px; border-radius: 7px; background: rgba(213, 228, 215, 0.08); }
      .kezak-squares-bl .sq-7 { width: 16px; height: 16px; bottom: 128px; left: 205px; border-radius: 4px; background: rgba(213, 228, 215, 0.12); }
      .kezak-squares-bl .sq-8 { width: 36px; height: 36px; bottom: 165px; left: 70px; border-radius: 8px; background: rgba(213, 228, 215, 0.05); }
      .kezak-squares-bl .sq-9 { width: 22px; height: 22px; bottom: 160px; left: 135px; border-radius: 5px; background: rgba(213, 228, 215, 0.09); }
      .kezak-squares-bl .sq-10 { width: 14px; height: 14px; bottom: 175px; left: 25px; border-radius: 4px; background: rgba(213, 228, 215, 0.11); }
      .kezak-squares-bl .sq-11 { width: 28px; height: 28px; bottom: 220px; left: 95px; border-radius: 6px; background: rgba(213, 228, 215, 0.06); }
      .kezak-squares-bl .sq-12 { width: 18px; height: 18px; bottom: 230px; left: 45px; border-radius: 4px; background: rgba(213, 228, 215, 0.09); }

      /* Radial Glow Ambient Lights */
      .kezak-glow-orb {
        position: absolute;
        border-radius: 50%;
        pointer-events: none;
        filter: blur(90px);
      }

      .kezak-glow-top {
        top: -10%;
        right: 5%;
        width: 460px;
        height: 460px;
        background: radial-gradient(circle, rgba(213, 228, 215, 0.32) 0%, transparent 70%);
      }

      .kezak-glow-bottom {
        bottom: -15%;
        left: -5%;
        width: 520px;
        height: 520px;
        background: radial-gradient(circle, rgba(4, 47, 36, 0.7) 0%, transparent 70%);
      }

      /* Floating Mockup Stage (Matching Exact Kezak Staging Triangle & Spacing) */
      .kezak-stage {
        position: relative;
        z-index: 5;
        height: 500px;
        width: 100%;
        max-width: 620px;
        margin: 150px auto 0;
      }

      /* Base Glass Card */
      .kezak-card {
        position: absolute;
        background: rgba(248, 250, 247, 0.96);
        backdrop-filter: blur(20px);
        -webkit-backdrop-filter: blur(20px);
        border: 1.5px solid #d5e4d7;
        border-radius: 18px;
        color: var(--color-text);
        box-shadow: 0 20px 40px -10px rgba(0, 0, 0, 0.35), 0 0 20px rgba(213, 228, 215, 0.22);
        transition: transform 0.3s ease;
        padding: 18px 20px;
      }

      .kezak-card:hover {
        transform: translateY(-6px) scale(1.02);
        animation-play-state: paused;
        z-index: 10 !important;
      }

      /* Card 1: Top Center ("Financial Plan" -> "Kondisi Aset") */
      .kezak-card-plan {
        top: 0;
        left: 95px;
        width: 290px;
        z-index: 1;
        animation: floatCardPlan 5s ease-in-out infinite alternate;
      }

      .kezak-card-head {
        display: flex;
        justify-content: space-between;
        align-items: left;
        margin-bottom: 10px;
      }

      .kezak-card-title {
        font-size: 13px;
        font-weight: 700;
        color: var(--color-text);
      }

      .kezak-card-badge {
        font-size: 10.5px;
        font-weight: 600;
        padding: 2px 8px;
        background: var(--color-bg);
        color: var(--color-text-muted);
        border-radius: 6px;
      }

      .kezak-donut-wrap {
        display: flex;
        align-items: center;
        gap: 12px;
        margin-bottom: 14px;
      }

      .kezak-donut-svg {
        width: 60px;
        height: 60px;
        flex-shrink: 0;
      }

      .kezak-legend-list {
        display: flex;
        flex-direction: column;
        gap: 3px;
        font-size: 10.5px;
      }

      .kezak-legend-item {
        display: flex;
        align-items: center;
        gap: 5px;
        color: var(--color-text-muted);
        font-weight: 500;
      }

      .kezak-dot {
        width: 6px;
        height: 6px;
        border-radius: 50%;
        flex-shrink: 0;
      }

      .kezak-dot-baik { background: var(--color-primary); }
      .kezak-dot-rr { background: #b7791f; }
      .kezak-dot-rb { background: #a63d2f; }

      .kezak-mini-cols {
        display: grid;
        grid-template-columns: 1fr 1fr;
        gap: 8px;
        padding: 8px 0;
        border-top: 1px dashed var(--color-border);
        margin-bottom: 14px;
      }

      .kezak-mini-col-label {
        font-size: 10.5px;
        font-weight: 600;
        color: var(--color-text);
        margin-bottom: 2px;
      }

      .kezak-mini-col-val {
        font-size: 10px;
        color: var(--color-text-muted);
        margin-bottom: 4px;
      }

      /* Card 2: Middle Right ("Future Funds" -> "Pelaporan KIB") */
      .kezak-card-funds {
        top: 110px;
        left: 310px;
        width: 290px;
        z-index: 2;
        animation: floatCardFunds 5.8s ease-in-out 0.8s infinite alternate;
      }

      .kezak-progress-item {
        margin-bottom: 14px;
      }

      .kezak-progress-head {
        display: flex;
        justify-content: space-between;
        font-size: 11px;
        font-weight: 700;
        color: var(--color-text);
        margin-bottom: 2px;
      }

      .kezak-progress-sub {
        display: flex;
        justify-content: space-between;
        font-size: 10px;
        color: var(--color-text-muted);
        margin-bottom: 4px;
      }

      .kezak-progress-track {
        height: 5px;
        background: var(--color-border-subtle);
        border-radius: 99px;
        overflow: hidden;
      }

      .kezak-progress-fill {
        height: 100%;
        border-radius: 99px;
      }

      .fill-teal { background: var(--color-primary); }
      .fill-amber { background: #b7791f; }
      .fill-blue { background: #3e6a7a; }

      /* Card 3: Bottom Left Foreground ("Capital Allocations" -> "Total Aset") */
      .kezak-card-alloc {
        top: 210px;
        left: 40px;
        width: 290px;
        z-index: 3;
        animation: floatCardAlloc 5.2s ease-in-out 1.5s infinite alternate;
      }

      .kezak-alloc-val {
        font-size: 13px;
        font-weight: 700;
        color: var(--color-primary);
        letter-spacing: -0.01em;
        line-height: 1;
        margin: 0;
      }

      .kezak-alloc-badge {
        font-size: 10.5px;
        font-weight: 700;
        color: var(--color-primary);
        background: var(--color-primary-tint);
        padding: 2px 6px;
        border-radius: 6px;
        display: inline-flex;
        align-items: center;
        gap: 2px;
        margin-left: 6px;
        vertical-align: middle;
      }

      .kezak-item-row {
        display: flex;
        justify-content: space-between;
        align-items: center;
        padding: 6px 0;
        border-bottom: 1px dashed var(--color-border);
        font-size: 11.5px;
        margin-bottom: 14px;
      }

      .kezak-item-row:last-of-type {
        border-bottom: none;
      }

      .kezak-item-name {
        font-weight: 600;
        color: var(--color-text);
      }

      .kezak-item-sub {
        font-size: 10px;
        color: var(--color-text-muted);
      }

      .kezak-item-val {
        font-weight: 700;
        color: var(--color-text);
        font-size: 11px;
      }

      .kezak-btn-card {
        width: 100%;
        padding: 8px 12px;
        margin-top: 8px;
        background: var(--color-primary);
        color: #ffffff;
        border: none;
        border-radius: 10px;
        font-size: 11.5px;
        font-weight: 600;
        cursor: pointer;
        display: flex;
        align-items: center;
        justify-content: center;
        gap: 6px;
        text-decoration: none;
        transition: background 0.15s;
      }

      .kezak-btn-card:hover {
        background: var(--color-primary-hover);
      }

      .kezak-btn-card i, .kezak-btn-card svg {
        width: 12px;
        height: 12px;
      }

      /* ─── SLIDE 1 (SLIDE 2): 3D ASSET SHOWCASE STYLING ─── */
      .kezak-3d-stage {
        position: relative;
        width: 100%;
        max-width: 520px;
        height: 420px;
        margin: 0 auto;
        display: flex;
        align-items: center;
        justify-content: center;
      }

      .kezak-3d-ambient {
        position: absolute;
        width: 360px;
        height: 360px;
        border-radius: 50%;
        background: radial-gradient(circle, rgba(201, 162, 74, 0.22) 0%, rgba(31, 58, 42, 0.45) 45%, transparent 70%);
        filter: blur(45px);
        pointer-events: none;
        animation: pulseGlow 4s ease-in-out infinite alternate;
      }

      .kezak-3d-wrapper {
        position: relative;
        z-index: 2;
        display: flex;
        align-items: center;
        justify-content: center;
        animation: float3D 4.5s ease-in-out infinite alternate;
      }

      .kezak-3d-asset {
        width: 340px;
        height: 340px;
        max-width: 100%;
        max-height: 100%;
        aspect-ratio: 1 / 1;
        object-fit: contain;
        display: block;
        filter: drop-shadow(0 25px 40px rgba(0, 0, 0, 0.45));
        transition: transform 0.3s ease;
      }

      .kezak-3d-asset:hover {
        transform: scale(1.03);
      }

      .kezak-3d-asset-wide {
        width: 100%;
        max-width: 440px;
        max-height: 270px;
        height: auto;
        object-fit: contain;
        display: block;
        filter: drop-shadow(0 25px 40px rgba(0, 0, 0, 0.5));
        transition: transform 0.3s ease;
      }

      .kezak-3d-asset-wide:hover {
        transform: scale(1.03);
      }

      @keyframes float3D {
        0% { transform: translateY(0px) rotate(0deg); }
        50% { transform: translateY(-8px) rotate(0.6deg); }
        100% { transform: translateY(-14px) rotate(-0.6deg); }
      }

      @keyframes floatCardPlan {
        0% { transform: translateY(0px) rotate(0deg); }
        50% { transform: translateY(-7px) rotate(0.4deg); }
        100% { transform: translateY(-13px) rotate(-0.3deg); }
      }

      @keyframes floatCardFunds {
        0% { transform: translateY(0px) rotate(0deg); }
        50% { transform: translateY(-8px) rotate(-0.5deg); }
        100% { transform: translateY(-14px) rotate(0.3deg); }
      }

      @keyframes floatCardAlloc {
        0% { transform: translateY(0px) rotate(0deg); }
        50% { transform: translateY(-8px) rotate(0.3deg); }
        100% { transform: translateY(-15px) rotate(-0.2deg); }
      }

      @keyframes pulseGlow {
        0% { transform: scale(0.92); opacity: 0.6; }
        100% { transform: scale(1.08); opacity: 1; }
      }

      .kezak-floating-pill {
        position: absolute;
        display: inline-flex;
        align-items: center;
        gap: 7px;
        padding: 7px 14px;
        border-radius: 8px;
        font-size: 12px;
        font-weight: 700;
        box-shadow: 0 12px 28px -5px rgba(0, 0, 0, 0.35), 0 0 0 1px rgba(255, 255, 255, 0.15) inset;
        z-index: 10;
        animation: floatPill 3.5s ease-in-out infinite alternate;
        backdrop-filter: blur(12px);
        -webkit-backdrop-filter: blur(12px);
      }

      .kezak-floating-pill-top {
        top: 30px;
        right: 25px;
        background: #d5e4d7;
        color: #1f3a2a;
        border: 1px solid rgba(31, 58, 42, 0.2);
        font-weight: 700;
      }

      .kezak-floating-pill-top-left {
        top: 30px;
        left: 25px;
        background: #d5e4d7;
        color: #1f3a2a;
        border: 1px solid rgba(31, 58, 42, 0.2);
        font-weight: 700;
      }

      .kezak-floating-pill-bottom {
        bottom: 35px;
        left: 20px;
        background: rgba(248, 250, 247, 0.96);
        color: var(--color-text);
        border: 1px solid var(--color-border);
        animation-delay: 1.5s;
      }

      .kezak-floating-pill-bottom-right,
      .kezak-floating-pill-accent {
        bottom: 35px;
        right: 20px;
        background: rgba(248, 250, 247, 0.96);
        color: var(--color-text);
        border: 1px solid var(--color-border);
        animation-delay: 0.8s;
      }

      .kezak-floating-pill-center-top {
        top: 10px;
        left: 0;
        right: 0;
        margin-left: auto;
        margin-right: auto;
        width: -moz-fit-content;
        width: fit-content;
        background: #d5e4d7;
        color: #1f3a2a;
        border: 1px solid rgba(31, 58, 42, 0.2);
        font-weight: 700;
      }

      .kezak-floating-pill-center-bottom {
        bottom: 10px;
        left: 0;
        right: 0;
        margin-left: auto;
        margin-right: auto;
        width: -moz-fit-content;
        width: fit-content;
        background: rgba(247, 248, 236, 0.96);
        color: var(--color-text);
        border: 1px solid var(--color-border);
        animation-delay: 1.5s;
      }

      @keyframes floatPill {
        0% { transform: translateY(0); }
        100% { transform: translateY(-5px); }
      }

      /* ─── SLIDE 2 (SLIDE 3): 3D ASSET & QR SCANNER STYLING ─── */
      .kezak-pic-frame {
        position: relative;
        border-radius: 22px;
        overflow: hidden;
        box-shadow: 0 25px 50px -10px rgba(0, 0, 0, 0.55), 0 0 0 1px rgba(255, 255, 255, 0.15);
        background: #ffffff;
        display: flex;
        align-items: center;
        justify-content: center;
        max-width: 460px;
        max-height: 280px;
      }

      .kezak-pic-asset {
        width: 100%;
        height: auto;
        max-height: 280px;
        object-fit: cover;
        display: block;
        transition: transform 0.4s ease;
      }

      .kezak-pic-frame:hover .kezak-pic-asset {
        transform: scale(1.03);
      }

      /* ─── SLIDE 3 (SLIDE 4): DASHBOARD SHOWCASE STYLING ─── */
      .kezak-dashboard-frame {
        position: relative;
        width: 100%;
        max-width: 480px;
        border-radius: 14px;
        overflow: hidden;
        border: 1px solid rgba(216, 220, 195, 0.5);
        background: #f8faf7;
        box-shadow: 0 25px 50px -12px rgba(0, 0, 0, 0.6), 0 0 0 1px rgba(255, 255, 255, 0.15) inset;
        transition: transform 0.35s ease;
      }

      .kezak-dashboard-frame:hover {
        transform: translateY(-4px) scale(1.01);
      }

      .kezak-dashboard-browser-bar {
        height: 28px;
        background: #eaf1e7;
        border-bottom: 1px solid #cfdcc8;
        display: flex;
        align-items: center;
        padding: 0 12px;
        position: relative;
      }

      .kezak-browser-dots {
        display: flex;
        align-items: center;
        gap: 6px;
      }

      .kezak-browser-dot {
        width: 8px;
        height: 8px;
        border-radius: 50%;
        display: inline-block;
      }

      .kezak-browser-dot.dot-red { background: #EF4444; }
      .kezak-browser-dot.dot-yellow { background: #F59E0B; }
      .kezak-browser-dot.dot-green { background: #10B981; }

      .kezak-browser-url {
        position: absolute;
        left: 50%;
        transform: translateX(-50%);
        display: inline-flex;
        align-items: center;
        gap: 5px;
        padding: 2px 14px;
        background: rgba(247, 248, 236, 0.9);
        border: 1px solid rgba(216, 220, 195, 0.8);
        border-radius: 6px;
        font-size: 10px;
        font-weight: 500;
        color: #546958;
      }

      .kezak-dashboard-asset {
        width: 100%;
        height: auto;
        display: block;
        object-fit: cover;
      }

      /* ─── SLIDE 3: EXPORT HUB STYLING ─── */
      .kezak-export-hub {
        position: relative;
        width: 530px;
        max-width: 100%;
        margin: 12px auto 0;
        background: rgba(247, 248, 236, 0.98);
        backdrop-filter: blur(20px);
        -webkit-backdrop-filter: blur(20px);
        border-radius: 20px;
        border: 1px solid rgba(216, 220, 195, 0.9);
        box-shadow: 0 24px 48px -12px rgba(0, 0, 0, 0.45), 0 0 0 1px rgba(255, 255, 255, 0.3) inset;
        padding: 22px 24px 20px 24px;
        color: var(--color-text);
      }

      .kezak-export-grid {
        display: grid;
        grid-template-columns: 1fr 1fr;
        gap: 12px;
        margin-bottom: 14px;
      }

      .kezak-export-card {
        background: var(--color-surface);
        border: 1px solid var(--color-border);
        border-radius: 12px;
        padding: 12px;
        display: flex;
        align-items: flex-start;
        gap: 10px;
        transition: all 0.2s ease;
      }

      .kezak-export-card:hover {
        background: #ffffff;
        border-color: var(--color-primary);
      }

      .kezak-export-icon {
        width: 32px;
        height: 32px;
        border-radius: 8px;
        display: flex;
        align-items: center;
        justify-content: center;
        flex-shrink: 0;
      }

      .icon-excel { background: var(--color-primary-tint); color: var(--color-primary); }
      .icon-pdf { background: #f3dad3; color: #a63d2f; }

      .kezak-export-title {
        font-size: 11.5px;
        font-weight: 700;
        color: var(--color-text);
      }

      .kezak-export-desc {
        font-size: 10px;
        color: var(--color-text-muted);
        margin-top: 2px;
      }

      .kezak-log-list {
        background: var(--color-bg);
        border: 1px solid var(--color-border);
        border-radius: 12px;
        padding: 10px 14px;
        margin-top: 10px;
      }

      .kezak-log-header {
        font-size: 10.5px;
        font-weight: 700;
        color: var(--color-text);
        text-transform: uppercase;
        letter-spacing: 0.04em;
        margin-bottom: 8px;
        display: flex;
        justify-content: space-between;
        align-items: center;
      }

      .kezak-log-item {
        display: flex;
        justify-content: space-between;
        align-items: center;
        padding: 6px 0;
        border-bottom: 1px dashed var(--color-border);
        font-size: 11px;
      }

      .kezak-log-item:last-child {
        border-bottom: none;
      }

      .kezak-log-name {
        font-family: ui-monospace, SFMono-Regular, Menlo, Monaco, Consolas, monospace;
        font-size: 10px;
        font-weight: 600;
        color: var(--color-text);
      }

      .kezak-log-meta {
        font-size: 9.5px;
        color: var(--color-text-muted);
      }

      .kezak-log-status {
        display: inline-flex;
        align-items: center;
        gap: 3px;
        font-size: 9.5px;
        font-weight: 700;
        color: var(--color-primary);
        background: var(--color-primary-tint);
        padding: 2px 6px;
        border-radius: 4px;
      }

      /* Showcase Slide System */
      .kezak-slide {
        position: absolute;
        inset: 0;
        opacity: 0;
        pointer-events: none;
        transform: scale(0.96) translateY(10px);
        transition: opacity 0.6s cubic-bezier(0.4, 0, 0.2, 1), transform 0.6s cubic-bezier(0.4, 0, 0.2, 1);
      }

      .kezak-slide.active {
        opacity: 1;
        pointer-events: auto;
        transform: scale(1) translateY(0);
      }

      .kezak-showcase-footer {
        position: relative;
        z-index: 10;
        text-align: center;
        max-width: 480px;
        margin: 0 auto;
        min-height: auto;
      }

      .kezak-footer-text-slide {
        display: none;
        opacity: 0;
        transform: translateY(8px);
        transition: opacity 0.45s ease, transform 0.45s ease;
      }

      .kezak-footer-text-slide.active {
        display: block;
        opacity: 1;
        transform: translateY(0);
        animation: footerFadeIn 0.5s ease forwards;
      }

      @keyframes footerFadeIn {
        from { opacity: 0; transform: translateY(8px); }
        to { opacity: 1; transform: translateY(0); }
      }

      .kezak-showcase-side h1,
      .kezak-showcase-side h2,
      .kezak-showcase-side h3,
      .kezak-showcase-side .kezak-footer-title,
      .kezak-footer-title {
        font-size: 21px !important;
        font-weight: 800 !important;
        color: #ffffff !important;
        line-height: 1.25 !important;
        letter-spacing: -0.02em !important;
        margin-bottom: 6px !important;
      }

      .kezak-showcase-side p,
      .kezak-showcase-side .kezak-footer-desc,
      .kezak-footer-desc {
        font-size: 13px !important;
        color: rgba(255, 255, 255, 0.85) !important;
        line-height: 1.45 !important;
        margin-bottom: 14px !important;
      }

      /* Stories-Style Segmented Progress Bars */
      .kezak-slider-bars {
        display: flex;
        align-items: center;
        width: 100%;
        max-width: 500px;
        margin: 0 auto;
        gap: 8px;
        cursor: pointer;
      }

      .kezak-slider-bar {
        flex: 1;
        height: 4px;
        border-radius: 99px;
        background: rgba(255, 255, 255, 0.22);
        position: relative;
        overflow: hidden;
        transition: transform 0.2s ease, background 0.2s ease;
      }

      .kezak-slider-bar:hover {
        background: rgba(255, 255, 255, 0.35);
        transform: scaleY(1.4);
      }

      .kezak-slider-fill {
        position: absolute;
        left: 0;
        top: 0;
        height: 100%;
        width: 0%;
        background: #ffffff;
        border-radius: 99px;
        will-change: width;
      }

      /* Responsive Rules */
      @media (min-width: 1025px) and (max-width: 1360px) {
        .kezak-stage {
          transform: scale(0.85);
          transform-origin: center center;
          margin-top: 0;
          margin-bottom: -10px;
        }
      }

      @media (max-width: 1024px) {
        .kezak-login-wrapper {
          flex-direction: column;
        }
        .kezak-showcase-side {
          display: none;
        }
        .kezak-form-side {
          flex: 1 0 100%;
          width: 100%;
          max-width: 100%;
          padding: 36px 28px;
          height: auto;
          min-height: 100vh;
        }
      }

    </style>
  </head>

  <body>
    <div class="kezak-login-wrapper">

      <!-- ════════════════════════════════════════════════════════════════════
           LEFT PANEL (50%): LOGIN FORM CANVAS
           ════════════════════════════════════════════════════════════════════ -->
      <div class="kezak-form-side">
        <!-- Form Main Center -->
        <div class="kezak-form-main">
          <div class="kezak-form-headline">
            <!-- Brand Header directly above Selamat Datang -->
            <a href="/" class="kezak-brand-header">
              <img src="{{ asset('assets/images/logo/logo-sima-desa-demo.png') }}" alt="Logo Desa" style="width: 52px; height: 52px; object-fit: contain;" />
              <div class="kezak-brand-text">
                <span class="kezak-brand-name">SIMA</span>
                <span class="kezak-brand-tag">Desa {{ config('desa.nama_lokasi', 'Demo') ?: 'Demo' }}</span>
              </div>
            </a>
            <h1>Selamat Datang</h1>
            <p>Masuk untuk mengelola data aset desa.</p>
          </div>

          @if (session('status'))
            <div class="kezak-alert kezak-alert-success show">
              {{ session('status') }}
            </div>
          @endif

          <!-- Login Form -->
          <form id="loginForm" method="POST" action="{{ route('login.perform') }}" novalidate>
            @csrf
            
            <!-- Username Input -->
            <div class="kezak-field {{ $errors->has('username') ? 'has-error' : '' }}" id="field-username">
              <label class="kezak-label {{ $errors->has('username') ? 'error' : '' }}" for="username" id="label-username">
                Username <span class="req">*</span>
              </label>
              <div class="kezak-input-box">
                <span class="kezak-input-icon-left">
                  <i data-feather="user"></i>
                </span>
                <input 
                  type="text" 
                  id="username" 
                  name="username" 
                  class="kezak-input {{ $errors->has('username') ? 'error is-invalid' : '' }}" 
                  value="{{ old('username') }}"
                  placeholder="Masukkan username Anda" 
                  autocomplete="username"
                  autofocus
                />
              </div>
              <div class="kezak-field-error {{ $errors->has('username') ? 'show' : '' }}" id="error-username">
                <svg class="kezak-error-icon" viewBox="0 0 16 16" width="14" height="14">
                  <path fill="#D9383A" d="M7.134 1.603a1 1 0 0 1 1.732 0l6.062 10.5A1 1 0 0 1 14.062 13.6H1.938a1 1 0 0 1-.866-1.497l6.062-10.5z"/>
                  <path stroke="#FFFFFF" stroke-width="1.3" stroke-linecap="round" d="M8 5.5v3.2M8 11.2h.01"/>
                </svg>
                <span class="kezak-error-text">{{ $errors->first('username') }}</span>
              </div>
            </div>

            <!-- Password Input -->
            <div class="kezak-field {{ $errors->has('password') ? 'has-error' : '' }}" id="field-password">
              <label class="kezak-label {{ $errors->has('password') ? 'error' : '' }}" for="password" id="label-password">
                Password <span class="req">*</span>
              </label>
              <div class="kezak-input-box">
                <span class="kezak-input-icon-left">
                  <i data-feather="lock"></i>
                </span>
                <input 
                  type="password" 
                  id="password" 
                  name="password" 
                  class="kezak-input {{ $errors->has('password') ? 'error is-invalid' : '' }}" 
                  placeholder="Masukkan kata sandi Anda" 
                  autocomplete="current-password"
                />
                <button type="button" class="kezak-input-icon-right" onclick="togglePassword()" title="Tampilkan/Sembunyikan password">
                  <i data-feather="eye" id="eyeIcon"></i>
                </button>
              </div>
              <div class="kezak-field-error {{ $errors->has('password') ? 'show' : '' }}" id="error-password">
                <svg class="kezak-error-icon" viewBox="0 0 16 16" width="14" height="14">
                  <path fill="#D9383A" d="M7.134 1.603a1 1 0 0 1 1.732 0l6.062 10.5A1 1 0 0 1 14.062 13.6H1.938a1 1 0 0 1-.866-1.497l6.062-10.5z"/>
                  <path stroke="#FFFFFF" stroke-width="1.3" stroke-linecap="round" d="M8 5.5v3.2M8 11.2h.01"/>
                </svg>
                <span class="kezak-error-text">{{ $errors->first('password') }}</span>
              </div>
            </div>

            <!-- Submit Button -->
            <button type="submit" class="kezak-btn-submit">
              <span>Masuk</span>
            </button>
          </form>
        </div>

        <!-- Footer Copyright -->
        <div class="kezak-form-footer">
          <span>Hak Cipta &copy; {{ date('Y') }} SIMA Desa.</span>
          <span>Sistem Informasi Manajemen Aset</span>
        </div>
      </div>


      <!-- ════════════════════════════════════════════════════════════════════
           RIGHT PANEL (50%): RICH DEEP FOREST SHOWCASE WITH AUTO STORIES CAROUSEL
           ════════════════════════════════════════════════════════════════════ -->
      <div class="kezak-showcase-side" id="showcaseContainer">
        <!-- Decorative Floating Squares (Top-Right & Bottom-Left) - Dribbble Style -->
        <div class="kezak-corner-squares kezak-squares-tr" aria-hidden="true">
          <div class="kezak-sq sq-1"></div>
          <div class="kezak-sq sq-2"></div>
          <div class="kezak-sq sq-3"></div>
          <div class="kezak-sq sq-4"></div>
          <div class="kezak-sq sq-5"></div>
          <div class="kezak-sq sq-6"></div>
          <div class="kezak-sq sq-7"></div>
          <div class="kezak-sq sq-8"></div>
          <div class="kezak-sq sq-9"></div>
          <div class="kezak-sq sq-10"></div>
          <div class="kezak-sq sq-11"></div>
          <div class="kezak-sq sq-12"></div>
        </div>

        <div class="kezak-corner-squares kezak-squares-bl" aria-hidden="true">
          <div class="kezak-sq sq-1"></div>
          <div class="kezak-sq sq-2"></div>
          <div class="kezak-sq sq-3"></div>
          <div class="kezak-sq sq-4"></div>
          <div class="kezak-sq sq-5"></div>
          <div class="kezak-sq sq-6"></div>
          <div class="kezak-sq sq-7"></div>
          <div class="kezak-sq sq-8"></div>
          <div class="kezak-sq sq-9"></div>
          <div class="kezak-sq sq-10"></div>
          <div class="kezak-sq sq-11"></div>
          <div class="kezak-sq sq-12"></div>
        </div>
        <div class="kezak-glow-orb kezak-glow-top"></div>
        <div class="kezak-glow-orb kezak-glow-bottom"></div>

        <!-- Floating Cards Showcase Stage -->
        <div class="kezak-stage">
          
          <!-- ─── SLIDE 0: OVERVIEW & REKAPITULASI ─── -->
          <div class="kezak-slide active" data-slide="0">
            <!-- Soft ambient backdrop glow -->
            <div class="kezak-3d-ambient" style="top: 20px; left: 90px; width: 440px; height: 440px;"></div>

            <!-- Card 1: Top Center-Left (Kondisi Aset) -->
            <div class="kezak-card kezak-card-plan">
              <div class="kezak-card-head" style="margin-top: 16px; margin-bottom: 16px;">
                <span class="kezak-card-title">Kondisi Aset</span>
              </div>
              
              <div class="kezak-donut-wrap">
                <svg class="kezak-donut-svg" viewBox="0 0 36 36">
                  <path d="M18 2.0845 a 15.9155 15.9155 0 0 1 0 31.831 a 15.9155 15.9155 0 0 1 0 -31.831" fill="none" stroke="#e2e8f0" stroke-width="4.2"/>
                  <path d="M18 2.0845 a 15.9155 15.9155 0 1 1 -11.25 4.66" fill="none" stroke="#10b981" stroke-width="4.2" stroke-linecap="round"/>
                  <path d="M6.75 6.74 a 15.9155 15.9155 0 0 1 4.5 -3.2" fill="none" stroke="#f59e0b" stroke-width="4.2" stroke-linecap="round"/>
                  <path d="M11.25 3.54 a 15.9155 15.9155 0 0 1 6.75 -1.45" fill="none" stroke="#ef4444" stroke-width="4.2" stroke-linecap="round"/>
                </svg>
                <div>
                  <div style="font-size: 15px; font-weight: 800; color: #0f172a; line-height: 1;">Rp 3,85 M</div>
                  <div style="font-size: 10px; color: #64748b; margin: 2px 0 5px;">Total Nilai Aset</div>
                  <div class="kezak-legend-list">
                    <div class="kezak-legend-item">
                      <span class="kezak-dot kezak-dot-baik"></span>
                      <span>Baik (87.5%)</span>
                    </div>
                    <div class="kezak-legend-item">
                      <span class="kezak-dot kezak-dot-rr"></span>
                      <span>Rusak Ringan (8.4%)</span>
                    </div>
                    <div class="kezak-legend-item">
                      <span class="kezak-dot kezak-dot-rb"></span>
                      <span>Rusak Berat (4.1%)</span>
                    </div>
                  </div>
                </div>
              </div>

              <div class="kezak-mini-cols">
                <div>
                  <div class="kezak-mini-col-label">Barang Operasional</div>
                  <div class="kezak-mini-col-val">422 Unit Siap Pakai</div>
                  <div class="kezak-progress-track" style="height: 3px;">
                    <div class="kezak-progress-fill fill-teal" style="width: 95%;"></div>
                  </div>
                </div>
                <div>
                  <div class="kezak-mini-col-label">Perlu Perbaikan</div>
                  <div class="kezak-mini-col-val">20 Unit Terdata</div>
                  <div class="kezak-progress-track" style="height: 3px;">
                    <div class="kezak-progress-fill fill-amber" style="width: 5%;"></div>
                  </div>
                </div>
              </div>
            </div>

            <!-- Card 2: Middle Right (Klasifikasi KIB) -->
            <div class="kezak-card kezak-card-funds">
              <div class="kezak-card-head" style="margin-top: 16px; margin-bottom: 16px;">
                <span class="kezak-card-title">Klasifikasi KIB</span>
              </div>

              <div class="kezak-progress-item">
                <div class="kezak-progress-head">
                  <span>Peralatan &amp; Mesin (KIB B)</span>
                </div>
                <div class="kezak-progress-sub">
                  <span>372 Aset</span>
                  <span style="font-weight: 700; color: #0f172a;">Rp 6,01 M</span>
                </div>
                <div class="kezak-progress-track">
                  <div class="kezak-progress-fill fill-teal" style="width: 100%;"></div>
                </div>
              </div>

              <div class="kezak-progress-item" style="border-top: 1px dashed #e2e8f0; padding-top: 6px;">
                <div class="kezak-progress-head">
                  <span>Gedung &amp; Bangunan (KIB C)</span>
                </div>
                <div class="kezak-progress-sub">
                  <span>16 Aset</span>
                  <span style="font-weight: 700; color: #0f172a;">Rp 8,28 M</span>
                </div>
                <div class="kezak-progress-track">
                  <div class="kezak-progress-fill fill-amber" style="width: 100%;"></div>
                </div>
              </div>

              <div class="kezak-progress-item" style="border-top: 1px dashed #e2e8f0; padding-top: 6px;">
                <div class="kezak-progress-head">
                  <span>Jalan &amp; Jaringan (KIB D)</span>
                </div>
                <div class="kezak-progress-sub">
                  <span>22 Aset</span>
                  <span style="font-weight: 700; color: #0f172a;">Rp 1,58 M</span>
                </div>
                <div class="kezak-progress-track" style="margin-bottom: 14px;">
                  <div class="kezak-progress-fill fill-blue" style="width: 100%;"></div>
                </div>
              </div>
            </div>

            <!-- Card 3: Bottom Left Foreground (Total Nilai Perolehan) -->
            <div class="kezak-card kezak-card-alloc">
              <div class="kezak-card-head" style="margin-bottom: 16px; margin-top:16px; flex-direction: column !important;">
                <span class="kezak-card-title">Total Nilai Perolehan</span>
                <span class="kezak-alloc-val">Rp 3.850.000.000</span>
              </div>

              <div class="kezak-item-row">
                <div>
                  <div class="kezak-item-name">Laptop Operasional IT</div>
                  <div class="kezak-item-sub">KIB B &bull; 40 unit</div>
                </div>
                <div class="kezak-item-val">
                  Rp 18.500.000
                  <div style="font-size: 9.5px; color: var(--color-primary); text-align: right; font-weight: 600;">Kondisi Baik</div>
                </div>
              </div>

              <div class="kezak-item-row">
                <div>
                  <div class="kezak-item-name">Gedung Balai Serbaguna</div>
                  <div class="kezak-item-sub">KIB C &bull; Ruang Publik</div>
                </div>
                <div class="kezak-item-val">
                  Rp 850.000.000
                  <div style="font-size: 9.5px; color: var(--color-primary); text-align: right; font-weight: 600;">Kondisi Baik</div>
                </div>
              </div>

              <div class="kezak-item-row">
                <div>
                  <div class="kezak-item-name">Kendaraan Dinas Desa</div>
                  <div class="kezak-item-sub">KIB B &bull; Logistik</div>
                </div>
                <div class="kezak-item-val">
                  Rp 140.000.000
                  <div style="font-size: 9.5px; color: var(--color-primary); text-align: right; font-weight: 600;">Kondisi Baik</div>
                </div>
              </div>
            </div>
          </div>


          <!-- ─── SLIDE 1 (SLIDE 2): FORMAT LEMBAR DOKUMEN RESMI & STEMPEL 3D (PERMENDAGRI NO. 1/2016) ─── -->
          <div class="kezak-slide" data-slide="1">
            <div class="kezak-3d-stage">
              <!-- Soft ambient backdrop glow -->
              <div class="kezak-3d-ambient"></div>

              <!-- Floating Pill Top Right -->
              <div class="kezak-floating-pill kezak-floating-pill-top">
                <span>Permendagri No. 1 / 2016</span>
              </div>

              <!-- 3D Hero Illustration -->
              <div class="kezak-3d-wrapper">
                <img src="{{ asset('assets/images/slider/3d-icon-slider-2.svg') }}" 
                     alt="Standar Baku Permendagri No. 1/2016" 
                     class="kezak-3d-asset"
                     onerror="this.onerror=null; this.src='{{ asset('assets/images/slider/img-slide-2.jpg') }}';">
              </div>

              <!-- Floating Pill Bottom Left -->
              <div class="kezak-floating-pill kezak-floating-pill-bottom">
                <span>Standarisasi KIB A - F</span>
              </div>
            </div>
          </div>


          <!-- ─── SLIDE 2 (SLIDE 3): DIGITALISASI LABELING & IDENTIFIKASI FISIK BARANG ─── -->
          <div class="kezak-slide" data-slide="2">
            <div class="kezak-3d-stage">
              <!-- Soft ambient backdrop glow -->
              <div class="kezak-3d-ambient"></div>

              <!-- Floating Pill Top Left -->
              <div class="kezak-floating-pill kezak-floating-pill-top-left">
                <span>Kodefikasi QR</span>
              </div>

              <!-- 3D Hero Illustration -->
              <div class="kezak-3d-wrapper">
                <img src="{{ asset('assets/images/slider/slider-3.png') }}" 
                     alt="Digitalisasi Labeling &amp; Identifikasi Fisik Barang" 
                     class="kezak-3d-asset-wide">
              </div>

              <!-- Floating Pill Bottom Right -->
              <div class="kezak-floating-pill kezak-floating-pill-bottom-right">
                <span>Label Barcode Cetak</span>
              </div>
            </div>
          </div>


          <!-- ─── SLIDE 3 (SLIDE 4): DASHBOARD MONITORING & KELENGKAPAN ASET REAL-TIME ─── -->
          <div class="kezak-slide" data-slide="3">
            <div class="kezak-3d-stage">
              <!-- Soft ambient backdrop glow -->
              <div class="kezak-3d-ambient"></div>

              <!-- Floating Pill Top Center -->
              <div class="kezak-floating-pill kezak-floating-pill-center-top">
                <span>Monitoring Real-Time</span>
              </div>

              <!-- 3D Wrapper with Browser Mockup Frame -->
              <div class="kezak-3d-wrapper">
                <div class="kezak-dashboard-frame">
                  <div class="kezak-dashboard-browser-bar">
                    <div class="kezak-browser-dots">
                      <span class="kezak-browser-dot dot-red"></span>
                      <span class="kezak-browser-dot dot-yellow"></span>
                      <span class="kezak-browser-dot dot-green"></span>
                    </div>
                    <div class="kezak-browser-url">
                      <i data-feather="lock" style="width: 10px; height: 10px; color: var(--color-primary);"></i>
                      <span>sima.desa.id/dashboard</span>
                    </div>
                  </div>
                  <img src="{{ asset('assets/images/slider/ss-slide-4.png') }}" 
                       alt="Dashboard Manajemen Aset Desa" 
                       class="kezak-dashboard-asset">
                </div>
              </div>

              <!-- Floating Pill Bottom Center -->
              <div class="kezak-floating-pill kezak-floating-pill-center-bottom">
                <span>Dashboard Sistem</span>
              </div>
            </div>
          </div>

        </div>

        <!-- Lower Inspiring Typography with Dynamic Slide Headlines -->
        <div class="kezak-showcase-footer">
          <!-- Slide 0 Text -->
          <div class="kezak-footer-text-slide active" data-slide="0">
            <h2 class="kezak-footer-title">
              Pencatatan Aset Desa yang Rapi dan Terpusat            </h2>
            <p class="kezak-footer-desc">
              SIMA Desa membantu pemerintah desa mencatat seluruh aset, dari tanah dan bangunan hingga aset tetap lainnya.</p>
          </div>

          <!-- Slide 1 Text (Slide 2) -->
          <div class="kezak-footer-text-slide" data-slide="1">
            <h2 class="kezak-footer-title">
              Sesuai Permendagri No. 1/2016
            </h2>
            <p class="kezak-footer-desc">
              Aset dikelompokkan ke dalam KIB A sampai F, sehingga buku inventaris dan laporan mengikuti ketentuan yang berlaku.
            </p>
          </div>

          <!-- Slide 2 Text (Slide 3) -->
          <div class="kezak-footer-text-slide" data-slide="2">
            <h2 class="kezak-footer-title">
             Label QR pada Setiap Aset
            </h2>
            <p class="kezak-footer-desc">
            Setiap aset memiliki kode barang dan label QR. Petugas cukup memindainya untuk melihat data aset saat pengecekan.           
            </p>
          </div>

          <!-- Slide 3 Text (Slide 4) -->
          <div class="kezak-footer-text-slide" data-slide="3">
            <h2 class="kezak-footer-title">
              Dashboard &amp; Monitoring Real-Time
            </h2>
            <p class="kezak-footer-desc">
              Pantau total nilai perolehan, sebaran kategori KIB, kondisi fisik, hingga tingkat kelengkapan data aset secara visual dan akuntabel.
            </p>
          </div>

          <!-- Stories-Style Segmented Progress Indicator Bars -->
          <div class="kezak-slider-bars" id="sliderBarsContainer" title="Klik untuk berpindah sorotan fitur">
            <div class="kezak-slider-bar" data-index="0"><div class="kezak-slider-fill"></div></div>
            <div class="kezak-slider-bar" data-index="1"><div class="kezak-slider-fill"></div></div>
            <div class="kezak-slider-bar" data-index="2"><div class="kezak-slider-fill"></div></div>
            <div class="kezak-slider-bar" data-index="3"><div class="kezak-slider-fill"></div></div>
          </div>
        </div>

      </div>

    </div>

    @include('layouts.footer-js')
    @include('layouts.notification')

    <script>
      // Initialize Feather Icons
      document.addEventListener('DOMContentLoaded', function () {
        if (typeof feather !== 'undefined') {
          feather.replace();
        }
        initShowcaseStories();
      });

      // ════════════════════════════════════════════════════════════════════
      // STORIES-STYLE AUTO ROTATING SHOWCASE CAROUSEL
      // ════════════════════════════════════════════════════════════════════
      function initShowcaseStories() {
        const slides = document.querySelectorAll('.kezak-slide');
        const textSlides = document.querySelectorAll('.kezak-footer-text-slide');
        const bars = document.querySelectorAll('.kezak-slider-bar');
        const totalSlides = slides.length;
        if (totalSlides === 0) return;

        const SLIDE_DURATION = 5000; // 5 seconds per slide
        let currentSlide = 0;
        let slideStartTime = Date.now();
        let isPaused = false;
        let animationFrameId = null;

        function updateSlideUI(index) {
          slides.forEach((s, i) => {
            if (i === index) {
              s.classList.add('active');
            } else {
              s.classList.remove('active');
            }
          });

          textSlides.forEach((t, i) => {
            if (i === index) {
              t.classList.add('active');
            } else {
              t.classList.remove('active');
            }
          });

          if (typeof feather !== 'undefined') {
            feather.replace();
          }
        }

        function goToSlide(index) {
          currentSlide = (index + totalSlides) % totalSlides;
          slideStartTime = Date.now();
          updateSlideUI(currentSlide);
          renderProgress(0);
        }

        function renderProgress(elapsed) {
          const progressPercent = Math.min(100, Math.max(0, (elapsed / SLIDE_DURATION) * 100));

          bars.forEach((bar, i) => {
            const fill = bar.querySelector('.kezak-slider-fill');
            if (!fill) return;

            if (i < currentSlide) {
              fill.style.width = '100%';
            } else if (i === currentSlide) {
              fill.style.width = `${progressPercent}%`;
            } else {
              fill.style.width = '0%';
            }
          });
        }

        function storyLoop() {
          if (!isPaused) {
            const elapsed = Date.now() - slideStartTime;
            if (elapsed >= SLIDE_DURATION) {
              goToSlide(currentSlide + 1);
            } else {
              renderProgress(elapsed);
            }
          }
          animationFrameId = requestAnimationFrame(storyLoop);
        }

        // Click on segmented bar to jump directly
        bars.forEach((bar) => {
          bar.addEventListener('click', function () {
            const targetIndex = parseInt(this.getAttribute('data-index'), 10);
            if (!isNaN(targetIndex)) {
              goToSlide(targetIndex);
            }
          });
        });

        // Hover container to pause/resume
        const container = document.getElementById('showcaseContainer');
        if (container) {
          container.addEventListener('mouseenter', () => {
            isPaused = true;
          });
          container.addEventListener('mouseleave', () => {
            isPaused = false;
            // Adjust start time so it doesn't jump forward
            slideStartTime = Date.now() - (parseFloat(bars[currentSlide]?.querySelector('.kezak-slider-fill')?.style.width || '0') / 100) * SLIDE_DURATION;
          });
        }

        // Start initial slide
        goToSlide(0);
        animationFrameId = requestAnimationFrame(storyLoop);
      }

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

      // AJAX Login Form Submission
      const loginForm = document.getElementById('loginForm');
      const loginBtn = loginForm.querySelector('.kezak-btn-submit');

      function clearFieldError(fieldId) {
        const inputField = document.getElementById(fieldId);
        if (inputField) {
          inputField.classList.remove('error', 'is-invalid');
        }
        const fieldWrapper = document.getElementById(`field-${fieldId}`) || inputField?.closest('.kezak-field');
        if (fieldWrapper) {
          fieldWrapper.classList.remove('has-error');
        }
        const labelEl = document.getElementById(`label-${fieldId}`) || fieldWrapper?.querySelector('.kezak-label');
        if (labelEl) {
          labelEl.classList.remove('error');
        }
        const errorEl = document.getElementById(`error-${fieldId}`);
        if (errorEl) {
          errorEl.classList.remove('show');
          const textEl = errorEl.querySelector('.kezak-error-text');
          if (textEl) textEl.textContent = '';
        }
      }

      function showFieldError(fieldId, message) {
        const inputField = document.getElementById(fieldId);
        if (inputField) {
          inputField.classList.add('error', 'is-invalid');
        }
        const fieldWrapper = document.getElementById(`field-${fieldId}`) || inputField?.closest('.kezak-field');
        if (fieldWrapper) {
          fieldWrapper.classList.add('has-error');
        }
        const labelEl = document.getElementById(`label-${fieldId}`) || fieldWrapper?.querySelector('.kezak-label');
        if (labelEl) {
          labelEl.classList.add('error');
        }
        const errorEl = document.getElementById(`error-${fieldId}`);
        if (errorEl) {
          const textEl = errorEl.querySelector('.kezak-error-text');
          if (textEl) textEl.textContent = message;
          if (message && message.trim() !== '') {
            errorEl.style.display = 'flex';
            errorEl.classList.add('show');
          } else {
            errorEl.style.display = 'none';
            errorEl.classList.remove('show');
          }
        }
      }

      function clearAllErrors() {
        ['username', 'password'].forEach(id => clearFieldError(id));
      }

      // Mekanisme: jika user sudah mulai mengetik, hilangkan warning text serta perubahan warna border dan perubahan warna judul input fieldnya seketika
      ['username', 'password'].forEach(id => {
        const el = document.getElementById(id);
        if (el) {
          el.addEventListener('input', () => {
            clearFieldError(id);
          });
        }
      });

      loginForm.addEventListener('submit', async function(e) {
        e.preventDefault();
        clearAllErrors();
        
        const usernameInput = document.getElementById('username');
        const passwordInput = document.getElementById('password');
        const usernameVal = (usernameInput?.value || '').trim();
        const passwordVal = (passwordInput?.value || '').trim();

        let hasClientError = false;

        // Validasi saat field belum terisi tapi user sudah klik tombol Masuk
        if (!usernameVal) {
          showFieldError('username', 'Username wajib diisi.');
          hasClientError = true;
        }

        if (!passwordVal) {
          showFieldError('password', 'Password wajib diisi.');
          hasClientError = true;
        }

        if (hasClientError) {
          const firstErrInput = !usernameVal ? usernameInput : passwordInput;
          if (firstErrInput) firstErrInput.focus();
          return false;
        }
        
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
          
          if (response.status === 419) {
            if (typeof AppLoader !== 'undefined') AppLoader.hide();
            showFieldError('username', 'Sesi halaman telah kedaluwarsa. Memperbarui halaman...');
            setTimeout(() => {
              window.location.reload();
            }, 1200);
            return;
          }

          let data = {};
          try {
            data = await response.json();
          } catch (jsonErr) {
            data = {};
          }
          
          if (response.ok && data.success) {
            window.location.href = data.redirect || "{{ route('dashboard') }}";
          } else {
            if (typeof AppLoader !== 'undefined') AppLoader.hide();
            
            if (response.status === 419 || (data.message && data.message.toLowerCase().includes('csrf'))) {
              showFieldError('username', 'Sesi halaman telah kedaluwarsa. Memperbarui halaman...');
              setTimeout(() => {
                window.location.reload();
              }, 1200);
              return;
            }

            if (data.errors && Object.keys(data.errors).length > 0) {
              Object.keys(data.errors).forEach(field => {
                const msg = Array.isArray(data.errors[field]) ? data.errors[field][0] : data.errors[field];
                showFieldError(field, msg);
              });
              // Jika login gagal (credential salah) dan salah satu field error, tandai field lainnya juga agar keduanya sinkron (border merah, judul merah, dan teks pesan error)
              if (data.errors.username && !data.errors.password) {
                const passMsg = Array.isArray(data.errors.username) ? data.errors.username[0] : data.errors.username;
                showFieldError('password', passMsg);
              } else if (data.errors.password && !data.errors.username) {
                const userMsg = Array.isArray(data.errors.password) ? data.errors.password[0] : data.errors.password;
                showFieldError('username', userMsg);
              }
            } else if (data.message) {
              // Jika login gagal (credential salah atau akun non-aktif):
              showFieldError('username', data.message);
              showFieldError('password', data.message);
            }
            loginBtn.disabled = false;
          }
        } catch (error) {
          if (typeof AppLoader !== 'undefined') AppLoader.hide();
          console.error('Login error:', error);
          showFieldError('username', 'Terjadi kesalahan koneksi. Silakan coba lagi.');
          loginBtn.disabled = false;
        }
      });
    </script>
  </body>
</html>
