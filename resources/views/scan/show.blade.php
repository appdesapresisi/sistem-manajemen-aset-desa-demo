<!DOCTYPE html>
<html lang="id">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <title>Informasi Aset — {{ $inventaris ? ($inventaris->display_name ?? $inventaris->kode_barang ?? 'SIMA Desa') : 'Aset Tidak Ditemukan - SIMA Desa' }}</title>
  
  <link rel="preconnect" href="https://fonts.googleapis.com">
  <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
  <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800&family=JetBrains+Mono:wght@500;600&display=swap" rel="stylesheet">
  
  <!-- Feather Icons CDN -->
  <script src="https://cdn.jsdelivr.net/npm/feather-icons/dist/feather.min.js"></script>

  <style>
    :root {
      --color-primary: #1f3a2a;
      --color-primary-hover: #162c1f;
      --color-primary-soft: #2e523b;
      --color-primary-tint: #d5e4d7;
      --color-accent: #C9A24A;
      --color-bg: #eaf1e7;
      --color-surface: #f8faf7;
      --color-border: #cfdcc8;
      --color-row-hover: #eff5ed;
      --color-text: #16251b;
      --color-text-muted: #546958;
      --color-text-on-primary: #eaf1e7;
      --color-success: #3F7D3A;
      --color-success-soft: #dce8d5;
      --color-warning: #B7791F;
      --color-warning-soft: #F4E7C3;
      --color-danger: #A63D2F;
      --color-danger-soft: #F3DAD3;
      --color-fallback-empty: #B7791F;
      --radius-sm: 8px;
      --radius-md: 12px;
      --radius-lg: 16px;
      --radius-full: 9999px;
    }

    * {
      box-sizing: border-box;
      margin: 0;
      padding: 0;
    }

    body {
      font-family: 'Inter', -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, sans-serif;
      background: var(--color-bg);
      color: var(--color-text);
      min-height: 100vh;
      display: flex;
      flex-direction: column;
      overflow-x: hidden;
    }

    /* Split Wrapper */
    .scan-wrapper {
      display: flex;
      flex: 1;
      width: 100%;
      min-height: 100vh;
      background: var(--color-bg);
    }

    @media (min-width: 993px) {
      .scan-wrapper {
        min-height: 100vh;
      }
    }

    /* ════════════════════════════════════════════════════════════════════
       LEFT COLUMN (45%): DEEP FOREST SHOWCASE WITH 3D MODEL
       ════════════════════════════════════════════════════════════════════ */
    .scan-showcase-side {
      flex: 0 0 45%;
      width: 45%;
      max-width: 45%;
      background: linear-gradient(155deg, #102418 0%, #1f3a2a 50%, #162c1f 100%);
      position: relative;
      display: flex;
      flex-direction: column;
      justify-content: space-between;
      padding: 44px 48px;
      overflow: hidden;
      color: #ffffff;
      border-right: 1px solid rgba(255, 255, 255, 0.08);
    }

    .scan-grid-bg {
      position: absolute;
      inset: 0;
      background-size: 36px 36px;
      background-image: 
        linear-gradient(to right, rgba(255, 255, 255, 0.04) 1px, transparent 1px),
        linear-gradient(to bottom, rgba(255, 255, 255, 0.04) 1px, transparent 1px);
      pointer-events: none;
      mask-image: radial-gradient(circle at 50% 50%, rgba(0,0,0,1) 65%, transparent 100%);
    }

    .scan-glow-orb {
      position: absolute;
      border-radius: 50%;
      pointer-events: none;
      filter: blur(80px);
    }

    .scan-glow-top {
      top: -10%;
      right: 0%;
      width: 440px;
      height: 440px;
      background: radial-gradient(circle, rgba(213, 228, 215, 0.35) 0%, transparent 70%);
    }

    .scan-glow-bottom {
      bottom: -15%;
      left: -10%;
      width: 480px;
      height: 480px;
      background: radial-gradient(circle, rgba(201, 162, 74, 0.25) 0%, transparent 70%);
    }

    /* Brand Header Top (White Card for SIMA Dark Green Logo Contrast) */
    .scan-brand-header {
      position: relative;
      z-index: 5;
      display: flex;
      align-items: center;
      gap: 14px;
      text-decoration: none;
      color: #ffffff;
      width: fit-content;
    }

    .scan-brand-logo {
      width: 46px;
      height: 46px;
      border-radius: 12px;
      background: #ffffff;
      border: 1px solid rgba(255, 255, 255, 0.4);
      display: flex;
      align-items: center;
      justify-content: center;
      padding: 5px;
      flex-shrink: 0;
      box-shadow: 0 4px 14px rgba(0, 0, 0, 0.25);
    }

    .scan-brand-logo img {
      width: 100%;
      height: 100%;
      object-fit: contain;
    }

    .scan-brand-title {
      font-size: 18px;
      font-weight: 800;
      letter-spacing: -0.3px;
      line-height: 1.2;
      color: #ffffff;
    }

    .scan-brand-subtitle {
      font-size: 11.5px;
      color: rgba(255, 255, 255, 0.7);
      font-weight: 500;
    }

    /* 3D Model Stage (Clean without surrounding badges/text - Prominent & Bold) */
    .scan-3d-stage {
      position: relative;
      z-index: 5;
      width: 100%;
      max-width: 560px;
      margin: auto;
      padding: 10px 0;
      display: flex;
      flex-direction: column;
      align-items: center;
      justify-content: center;
    }

    .scan-3d-ambient {
      position: absolute;
      width: 440px;
      height: 440px;
      border-radius: 50%;
      background: radial-gradient(circle, rgba(201, 162, 74, 0.32) 0%, rgba(31, 58, 42, 0.6) 45%, transparent 70%);
      filter: blur(60px);
      pointer-events: none;
      animation: pulseGlow 4s ease-in-out infinite alternate;
    }

    @keyframes pulseGlow {
      0% { transform: scale(0.9); opacity: 0.7; }
      100% { transform: scale(1.1); opacity: 1; }
    }

    .scan-3d-wrapper {
      position: relative;
      z-index: 2;
      display: flex;
      align-items: center;
      justify-content: center;
      width: 100%;
      animation: float3D 4.5s ease-in-out infinite alternate;
    }

    @keyframes float3D {
      0% { transform: translateY(0px) rotate(0deg); }
      50% { transform: translateY(-8px) rotate(-1deg); }
      100% { transform: translateY(0px) rotate(0deg); }
    }

    .scan-3d-asset {
      width: 100%;
      max-width: 520px;
      max-height: 420px;
      object-fit: contain;
      filter: drop-shadow(0 26px 48px rgba(0, 0, 0, 0.58));
      transition: transform 0.3s ease;
    }

    .scan-3d-asset:hover {
      transform: scale(1.05);
    }

    /* ════════════════════════════════════════════════════════════════════
       RIGHT COLUMN (55%): PORTAL DATA CANVAS (SIMA STANDARDS)
       ════════════════════════════════════════════════════════════════════ */
    .scan-data-side {
      flex: 0 0 55%;
      width: 55%;
      max-width: 55%;
      background: var(--color-bg);
      display: flex;
      flex-direction: column;
      justify-content: center;
      padding: 36px 40px;
      position: relative;
      z-index: 2;
      box-sizing: border-box;
      overflow-y: auto;
    }

    /* Main Portal Card (Seragam & Luas Mengikuti Ketinggian Penuh Informasi Rincian) */
    .portal-card {
      background: var(--color-surface, #f8faf7);
      border: 1px solid var(--color-border, #cfdcc8);
      border-radius: var(--radius-lg, 16px);
      padding: 40px 42px;
      display: flex;
      flex-direction: column;
      box-shadow: none;
      width: 100%;
      min-height: 670px;
      box-sizing: border-box;
    }

    /* Portal Header: Title Only (No divider line below tabs) */
    .portal-header {
      padding-bottom: 0;
      border-bottom: none;
      flex-shrink: 0;
    }

    .portal-title {
      font-size: 22px;
      font-weight: 800;
      color: var(--color-text, #16251b);
      letter-spacing: -0.02em;
      line-height: 1.3;
      margin: 0;
    }

    /* Underline Tab Navigation (Responsive, No Overflow Past Card, No Scrollbar Arrows) */
    .tabs-sima {
      display: flex;
      align-items: center;
      gap: 18px;
      margin-top: 18px;
      margin-bottom: 0;
      padding-bottom: 10px;
      overflow-x: auto;
      overflow-y: hidden;
      max-width: 100%;
      border-bottom: none;
      scrollbar-width: none;
      -ms-overflow-style: none;
    }

    .tabs-sima::-webkit-scrollbar {
      display: none;
    }

    .tab-item-sima {
      padding: 8px 0 10px 0;
      color: var(--color-text-muted, #546958);
      font-size: 13.5px;
      font-weight: 500;
      border: none;
      border-bottom: 2.5px solid transparent;
      text-decoration: none;
      display: inline-flex;
      align-items: center;
      cursor: pointer;
      transition: color 150ms ease, border-color 150ms ease;
      background: transparent;
      font-family: inherit;
      white-space: nowrap;
      flex-shrink: 0;
    }

    .tab-item-sima:hover {
      color: var(--color-text, #16251b);
    }

    .tab-item-sima.active {
      color: var(--color-primary, #1f3a2a);
      border-bottom-color: var(--color-primary, #1f3a2a);
      font-weight: 700;
    }

    /* ════════════════════════════════════════════════════════════════════
       STANDAR DROPDOWN SIMA (Acuan Resmi Pusat Ekspor / Design.md)
       ════════════════════════════════════════════════════════════════════ */
    .tabs-sima-dropdown-wrap {
      display: none;
      margin-top: 16px;
      margin-bottom: 6px;
      width: 100%;
      position: relative;
    }

    .searchable-dropdown {
      position: relative;
      width: 100%;
    }

    .dropdown-trigger {
      width: 100% !important;
      min-height: 48px !important;
      text-align: left !important;
      background-color: var(--color-surface, #f8faf7) !important;
      cursor: pointer !important;
      display: flex !important;
      align-items: center !important;
      justify-content: space-between !important;
      padding: 12px 18px !important;
      border: 1px solid var(--color-border, #cfdcc8) !important;
      border-radius: var(--radius-sm, 8px) !important;
      color: var(--color-text, #16251b) !important;
      font-size: 13.5px !important;
      font-weight: 600 !important;
      font-family: inherit !important;
      transition: all 0.2s ease !important;
      box-shadow: none !important;
      outline: none !important;
      box-sizing: border-box !important;
    }

    .dropdown-trigger:hover {
      border-color: var(--color-primary, #1f3a2a) !important;
      background-color: var(--color-row-hover, #eff5ed) !important;
    }

    .dropdown-trigger:focus,
    .searchable-dropdown.open .dropdown-trigger {
      border-color: var(--color-primary, #1f3a2a) !important;
      background-color: var(--color-surface, #f8faf7) !important;
      box-shadow: none !important;
      outline: none !important;
    }

    .dropdown-selected-label {
      color: var(--color-text, #16251b);
      font-size: 13.5px;
      font-weight: 600;
      white-space: nowrap;
      overflow: hidden;
      text-overflow: ellipsis;
    }

    .dropdown-trigger-caret {
      transition: transform 0.2s ease !important;
      color: var(--color-text-muted, #546958) !important;
      width: 16px !important;
      height: 16px !important;
      flex-shrink: 0 !important;
    }

    .searchable-dropdown.open .dropdown-trigger-caret {
      transform: rotate(180deg) !important;
    }

    .dropdown-menu-panel {
      display: none;
      position: absolute;
      top: calc(100% + 6px);
      left: 0;
      right: 0;
      z-index: 1050;
      background: var(--color-surface, #f8faf7) !important;
      border: 1px solid var(--color-border, #cfdcc8) !important;
      border-radius: var(--radius-md, 12px) !important;
      box-shadow: 0 10px 25px -5px rgba(0, 0, 0, 0.1), 0 8px 10px -6px rgba(0, 0, 0, 0.05) !important;
      width: 100% !important;
      padding: 6px !important;
      box-sizing: border-box !important;
    }

    .searchable-dropdown.open .dropdown-menu-panel {
      display: block !important;
      animation: fadeInDropdown 0.15s ease-out;
    }

    @keyframes fadeInDropdown {
      from { opacity: 0; transform: translateY(-4px); }
      to { opacity: 1; transform: translateY(0); }
    }

    .dropdown-options-list {
      max-height: 280px;
      overflow-y: auto;
      padding: 0;
      margin: 0;
      list-style: none;
    }

    .dropdown-options-list::-webkit-scrollbar {
      width: 5px;
    }
    .dropdown-options-list::-webkit-scrollbar-thumb {
      background: var(--color-border, #cfdcc8);
      border-radius: 9999px;
    }

    .dropdown-option-item {
      display: flex;
      align-items: center;
      gap: 10px;
      padding: 11px 14px;
      border-radius: var(--radius-sm, 8px);
      cursor: pointer;
      transition: background 0.15s ease;
      user-select: none;
      border-bottom: 1px solid var(--color-border, #cfdcc8);
      color: var(--color-text, #16251b);
    }

    .dropdown-option-item:last-child {
      border-bottom: none;
    }

    .dropdown-option-item:hover {
      background: var(--color-row-hover, #eff5ed);
    }

    .dropdown-option-item.selected {
      background: var(--color-primary-tint, #d5e4d7) !important;
      color: var(--color-primary, #1f3a2a) !important;
      font-weight: 600;
    }

    .dropdown-option-name {
      font-size: 13.5px;
      font-weight: 600;
      color: inherit;
      line-height: 1.4;
    }

    /* Portal Card Body (Flex fill to keep stable card height) */
    .portal-card-body {
      padding: 24px 0 0 0;
      flex: 1 1 auto;
      display: flex;
      flex-direction: column;
    }

    /* Step Panels */
    .portal-step-panel {
      display: none;
      flex: 1;
      animation: fadeInStep 0.2s ease-out;
    }

    .portal-step-panel.active {
      display: flex;
      flex-direction: column;
    }

    @keyframes fadeInStep {
      from { opacity: 0; transform: translateY(3px); }
      to { opacity: 1; transform: translateY(0); }
    }

    .portal-section-header {
      display: flex;
      justify-content: space-between;
      align-items: center;
      margin-bottom: 20px;
      gap: 12px;
    }

    .portal-section-title {
      font-size: 14px;
      font-weight: 700;
      color: var(--color-text, #16251b);
      margin: 0;
      display: flex;
      align-items: center;
      gap: 8px;
    }

    .portal-section-title i,
    .portal-section-title svg {
      width: 15px;
      height: 15px;
      color: var(--color-primary, #1f3a2a);
    }

    /* Key-Value Grids (Unboxed Layout with Dashed Dividers - Luwes & Tidak Sesak) */
    .portal-kv-grid {
      display: grid;
      gap: 20px 34px;
    }

    .portal-kv-grid.grid-3 {
      grid-template-columns: repeat(3, 1fr);
    }

    .portal-kv-grid.grid-2 {
      grid-template-columns: repeat(2, 1fr);
    }

    @media (max-width: 1200px) {
      .portal-kv-grid.grid-3 {
        grid-template-columns: repeat(2, 1fr);
      }
    }

    @media (max-width: 576px) {
      .portal-kv-grid.grid-3,
      .portal-kv-grid.grid-2 {
        grid-template-columns: 1fr;
        gap: 16px;
      }
    }

    .portal-kv-item {
      display: flex;
      flex-direction: column;
      gap: 6px;
      padding-bottom: 14px;
      background: transparent !important;
      border: none !important;
      border-bottom: 1px dashed var(--color-border, #cfdcc8) !important;
      border-radius: 0 !important;
      box-shadow: none !important;
    }

    .portal-kv-label {
      font-size: 11px;
      font-weight: 600;
      text-transform: uppercase;
      letter-spacing: 0.5px;
      color: var(--color-text-muted, #546958);
    }

    .portal-kv-value {
      font-size: 14px;
      font-weight: 600;
      color: var(--color-text, #16251b);
      line-height: 1.4;
      word-break: break-word;
    }

    .portal-kv-value.empty {
      color: var(--color-fallback-empty, #B7791F);
      font-weight: 500;
    }

    .portal-kv-value.primary-highlight {
      font-size: 16px;
      font-weight: 700;
      color: var(--color-primary, #1f3a2a);
      letter-spacing: -0.01em;
    }

    /* ═══════════════════════════════════════════════════════════════
       FOTO SHOWCASE — Standard Galeri Portal SIMA (floating overlay)
       ═══════════════════════════════════════════════════════════════ */
    .portal-gallery-wrap {
      flex: 1 1 auto;
      display: flex;
      flex-direction: column;
      min-height: 360px;
    }

    /* Main photo frame — always full height, no shrink */
    .portal-gallery-main {
      width: 100%;
      flex: 1 1 0;
      min-height: 360px;
      border-radius: var(--radius-md, 12px);
      border: 1px solid var(--color-border, #cfdcc8);
      background: var(--color-bg, #eaf1e7);
      overflow: hidden;
      position: relative;
      display: flex;
      align-items: center;
      justify-content: center;
    }

    .portal-gallery-main img {
      width: 100%;
      height: 100%;
      max-width: 100%;
      max-height: 100%;
      object-fit: contain !important;
      display: block;
      margin: auto;
      padding: 12px;
      box-sizing: border-box;
    }

    /* Placeholder when no photos */
    .portal-gallery-placeholder {
      display: flex;
      flex-direction: column;
      align-items: center;
      justify-content: center;
      gap: 16px;
      color: var(--color-text-muted, #546958);
      text-align: center;
      padding: 48px 24px;
      width: 100%;
      height: 100%;
      box-sizing: border-box;
    }

    .portal-gallery-placeholder i,
    .portal-gallery-placeholder svg {
      width: 56px;
      height: 56px;
      color: var(--color-primary-soft, #2e523b);
      stroke: var(--color-primary-soft, #2e523b);
      opacity: 0.6;
    }

    /* Floating overlay thumbnail strip — INSIDE .portal-gallery-main */
    .portal-gallery-thumbs {
      position: absolute;
      bottom: 12px;
      left: 12px;
      display: flex;
      gap: 8px;
      overflow-x: auto;
      max-width: calc(100% - 24px);
      padding: 6px;
      background: rgba(255, 255, 255, 0.88);
      backdrop-filter: blur(8px);
      -webkit-backdrop-filter: blur(8px);
      border: 1px solid rgba(207, 220, 200, 0.9);
      border-radius: 10px;
      box-shadow: 0 4px 14px rgba(0, 0, 0, 0.08);
      z-index: 5;
      box-sizing: border-box;
      /* Dim by default (hanya muncul saat foto > 1); terang saat hover */
      opacity: 0.42;
      transition: opacity 0.28s ease, transform 0.28s ease;
    }

    .portal-gallery-main:hover .portal-gallery-thumbs,
    .portal-gallery-thumbs:hover {
      opacity: 1;
    }

    .portal-gallery-thumbs::-webkit-scrollbar { display: none; }
    .portal-gallery-thumbs { scrollbar-width: none; }

    .portal-gallery-thumb {
      width: 48px;
      height: 48px;
      border-radius: 6px;
      border: 2px solid var(--color-border, #cfdcc8);
      overflow: hidden;
      cursor: pointer;
      flex-shrink: 0;
      transition: all 0.2s ease;
      background: var(--color-bg, #eaf1e7);
    }

    .portal-gallery-thumb img {
      width: 100%;
      height: 100%;
      object-fit: cover !important;
      display: block;
    }

    .portal-gallery-thumb:hover {
      border-color: var(--color-primary, #1f3a2a);
      transform: scale(1.05);
    }

    .portal-gallery-thumb.active {
      border-color: var(--color-primary, #1f3a2a);
      box-shadow: 0 0 0 2px rgba(31, 58, 42, 0.25);
    }

    /* ═══════════════════════════════════════════════════════════════
       SIMA FOTO VIEWER — Full-screen lightbox modal (Google Drive style)
       ═══════════════════════════════════════════════════════════════ */
    .sima-foto-viewer {
      display: none;
      position: fixed;
      inset: 0;
      z-index: 99990;
      background: rgba(10, 18, 12, 0.96);
      backdrop-filter: blur(16px);
      -webkit-backdrop-filter: blur(16px);
      flex-direction: column;
      align-items: stretch;
      justify-content: stretch;
      user-select: none;
    }

    .sima-foto-viewer.open {
      display: flex !important;
      animation: sfvFadeIn 0.2s ease-out;
    }

    @keyframes sfvFadeIn {
      from { opacity: 0; }
      to   { opacity: 1; }
    }

    .sfv-topbar {
      position: absolute;
      top: 0;
      left: 0;
      right: 0;
      height: 56px;
      display: flex;
      align-items: center;
      justify-content: space-between;
      padding: 0 20px;
      z-index: 10;
      background: linear-gradient(to bottom, rgba(0,0,0,0.6) 0%, transparent 100%);
    }

    .sfv-counter {
      font-family: 'Inter', sans-serif;
      font-size: 13px;
      font-weight: 600;
      color: rgba(255,255,255,0.75);
      letter-spacing: 0.3px;
    }

    .sfv-close-btn {
      width: 40px;
      height: 40px;
      border-radius: 50%;
      background: rgba(255,255,255,0.12);
      border: none;
      color: #fff;
      cursor: pointer;
      display: flex;
      align-items: center;
      justify-content: center;
      transition: background 0.2s ease;
      flex-shrink: 0;
    }

    .sfv-close-btn:hover {
      background: rgba(255,255,255,0.22);
    }

    .sfv-close-btn svg {
      width: 20px;
      height: 20px;
    }

    .sfv-stage {
      flex: 1 1 0;
      min-height: 0;
      display: flex;
      align-items: center;
      justify-content: center;
      position: relative;
      padding: 64px 72px 8px;
      box-sizing: border-box;
    }

    .sfv-main-img {
      max-width: 100%;
      max-height: 100%;
      object-fit: contain;
      border-radius: 6px;
      display: block;
      animation: sfvImgIn 0.18s ease-out;
      box-shadow: 0 24px 60px rgba(0,0,0,0.55);
    }

    @keyframes sfvImgIn {
      from { opacity: 0; transform: scale(0.97); }
      to   { opacity: 1; transform: scale(1); }
    }

    .sfv-nav-btn {
      position: absolute;
      top: 50%;
      transform: translateY(-50%);
      width: 48px;
      height: 48px;
      border-radius: 50%;
      background: rgba(255,255,255,0.12);
      border: 1px solid rgba(255,255,255,0.15);
      color: #fff;
      cursor: pointer;
      display: flex;
      align-items: center;
      justify-content: center;
      transition: background 0.2s ease, transform 0.15s ease;
      z-index: 5;
    }

    .sfv-nav-btn:hover {
      background: rgba(255,255,255,0.22);
      transform: translateY(-50%) scale(1.08);
    }

    .sfv-nav-btn:disabled {
      opacity: 0.25;
      cursor: default;
      pointer-events: none;
    }

    .sfv-nav-btn svg {
      width: 22px;
      height: 22px;
    }

    .sfv-prev { left: 14px; }
    .sfv-next { right: 14px; }

    .sfv-strip {
      flex-shrink: 0;
      display: flex;
      gap: 8px;
      align-items: center;
      justify-content: center;
      padding: 10px 20px 18px;
      overflow-x: auto;
      scrollbar-width: none;
    }

    .sfv-strip::-webkit-scrollbar { display: none; }

    .sfv-strip-thumb {
      width: 56px;
      height: 56px;
      flex-shrink: 0;
      border-radius: 6px;
      overflow: hidden;
      border: 2px solid rgba(255,255,255,0.2);
      cursor: pointer;
      opacity: 0.5;
      transition: opacity 0.18s ease, border-color 0.18s ease, transform 0.15s ease;
    }

    .sfv-strip-thumb:hover {
      opacity: 0.85;
      transform: translateY(-2px);
    }

    .sfv-strip-thumb.active {
      opacity: 1;
      border-color: #fff;
      transform: translateY(-3px);
      box-shadow: 0 4px 16px rgba(0,0,0,0.45);
    }

    .sfv-strip-thumb img {
      width: 100%;
      height: 100%;
      object-fit: cover;
      display: block;
    }



    .btn-sima-primary {
      display: inline-flex;
      align-items: center;
      gap: 6px;
      background: var(--color-primary, #1f3a2a);
      color: var(--color-text-on-primary, #eaf1e7);
      border: 1px solid var(--color-primary, #1f3a2a);
      border-radius: var(--radius-sm, 8px);
      padding: 8px 16px;
      font-size: 13px;
      font-weight: 600;
      cursor: pointer;
      text-decoration: none;
      transition: all 0.2s ease;
      font-family: inherit;
    }

    .btn-sima-primary:hover {
      background: var(--color-primary-hover, #162c1f);
      border-color: var(--color-primary-hover, #162c1f);
      color: var(--color-text-on-primary, #eaf1e7);
    }

    .btn-sima-secondary {
      display: inline-flex;
      align-items: center;
      gap: 6px;
      background: var(--color-bg, #eaf1e7);
      color: var(--color-text, #16251b);
      border: 1px solid var(--color-border, #cfdcc8);
      border-radius: var(--radius-sm, 8px);
      padding: 8px 16px;
      font-size: 13px;
      font-weight: 600;
      cursor: pointer;
      text-decoration: none;
      transition: all 0.2s ease;
      font-family: inherit;
    }

    .btn-sima-secondary:hover {
      background: var(--color-primary-tint, #d5e4d7);
      border-color: var(--color-border, #cfdcc8);
      color: var(--color-primary, #1f3a2a);
    }

    /* Lightbox Modal */
    .lightbox {
      display: none;
      position: fixed;
      inset: 0;
      background: rgba(31, 58, 42, 0.88);
      backdrop-filter: blur(8px);
      z-index: 9999;
      align-items: center;
      justify-content: center;
      padding: 24px;
    }

    .lightbox.active {
      display: flex;
    }

    .lightbox img {
      max-width: 90vw;
      max-height: 85vh;
      border-radius: 12px;
      box-shadow: 0 25px 60px rgba(0,0,0,0.5);
      border: 2px solid rgba(255,255,255,0.2);
    }

    .lightbox-close {
      position: absolute;
      top: 24px;
      right: 24px;
      color: #ffffff;
      font-size: 32px;
      cursor: pointer;
      line-height: 1;
      width: 44px;
      height: 44px;
      display: flex;
      align-items: center;
      justify-content: center;
      background: rgba(255,255,255,0.1);
      border-radius: 50%;
    }

    /* ═══════════════════════════════════════════════════════════════
       RESPONSIVE
       ═══════════════════════════════════════════════════════════════ */
    @media (max-width: 992px) {
      .scan-wrapper {
        flex-direction: column;
        min-height: auto;
      }
      .scan-showcase-side {
        flex: none;
        width: 100%;
        max-width: 100%;
        min-height: 280px;
        padding: 28px 20px;
        border-right: none;
        border-bottom: 1px solid rgba(255, 255, 255, 0.08);
      }
      .scan-data-side {
        flex: none;
        width: 100%;
        max-width: 100%;
        padding: 20px 16px 28px;
        min-height: auto;
        overflow-y: visible;
      }
      .portal-card {
        padding: 24px 18px;
        min-height: auto;
      }
      .scan-3d-stage {
        max-width: 400px;
      }
      .scan-3d-asset {
        max-height: 240px;
      }
      .portal-gallery-main,
      .portal-gallery-wrap {
        min-height: 280px;
      }
    }

    @media (max-width: 768px) {
      .tabs-sima {
        display: none !important;
      }
      .tabs-sima-dropdown-wrap {
        display: block !important;
      }
      .scan-data-side {
        padding: 16px 12px 24px;
      }
      .portal-card {
        padding: 20px 14px;
      }
      .portal-kv-grid.grid-3 {
        grid-template-columns: repeat(2, 1fr);
        gap: 14px 20px;
      }
    }

    @media (max-width: 576px) {
      .portal-title {
        font-size: 18px;
      }
      .portal-card {
        padding: 16px 12px;
      }
      .scan-showcase-side {
        min-height: 220px;
        padding: 20px 16px;
      }
      .scan-3d-stage {
        max-width: 300px;
      }
      .scan-3d-asset {
        max-height: 180px;
      }
      .portal-kv-grid.grid-3,
      .portal-kv-grid.grid-2 {
        grid-template-columns: 1fr;
        gap: 14px;
      }
      .portal-gallery-main,
      .portal-gallery-wrap {
        min-height: 220px;
      }
      .portal-gallery-thumb {
        width: 40px;
        height: 40px;
      }
    }

    @media (max-width: 375px) {
      .portal-title {
        font-size: 16px;
      }
      .scan-showcase-side {
        min-height: 180px;
        padding: 16px 14px;
      }
      .scan-3d-asset {
        max-height: 150px;
      }
    }

  </style>
</head>
<body>

@if(!empty($inventaris))
  @php
    $kibFields = [];
    if ($inventaris->jenis && $inventaris->jenis->kib_type) {
      $kibFields = config('kib.' . $inventaris->jenis->kib_type . '.fields', []);
    }
    $hasKir = (bool) ($inventaris->ruangan_id || $inventaris->ruangan);
    $hasFotos = (bool) ($inventaris->fotos && $inventaris->fotos->count() > 0);

    $kondisiLabel = match($inventaris->kondisi) {
      'baik' => 'Baik (B)',
      'rusak_ringan' => 'Kurang Baik (KB)',
      'rusak_berat' => 'Rusak Berat (RB)',
      default => ucfirst($inventaris->kondisi ?? '-')
    };

    $bulanNames = [
      1 => 'Januari', 2 => 'Februari', 3 => 'Maret', 4 => 'April',
      5 => 'Mei', 6 => 'Juni', 7 => 'Juli', 8 => 'Agustus',
      9 => 'September', 10 => 'Oktober', 11 => 'November', 12 => 'Desember'
    ];
    $bp = method_exists($inventaris, 'getBulanPerolehan') ? $inventaris->getBulanPerolehan() : null;
  @endphp

  <div class="scan-wrapper">
    
    <!-- ════════════════════════════════════════════════════════════════════
         LEFT SIDE: 3D MODEL SHOWCASE (Bersih tanpa badge/teks sekitar)
         ════════════════════════════════════════════════════════════════════ -->
    <div class="scan-showcase-side">
      <!-- Ambient Visuals -->
      <div class="scan-grid-bg"></div>
      <div class="scan-glow-orb scan-glow-top"></div>
      <div class="scan-glow-orb scan-glow-bottom"></div>

      <!-- Brand Header (White Box for SIMA Logo Contrast) -->
      <a href="{{ url('/') }}" class="scan-brand-header">
        <div class="scan-brand-logo">
          <img src="{{ asset('assets/images/logo/logo-sima-desa-demo.png') }}" alt="Logo SIMA">
        </div>
        <div>
          <div class="scan-brand-title">SIMA DESA</div>
          <div class="scan-brand-subtitle">Pemerintah Desa {{ config('desa.nama_lokasi', 'Demo') }}</div>
        </div>
      </a>

      <!-- 3D Model Stage (Bersih) -->
      <div class="scan-3d-stage">
        <div class="scan-3d-ambient"></div>

        <div class="scan-3d-wrapper">
          <img src="{{ asset('assets/images/slider/slider-3-clean.png') }}" 
               alt="3D Asset Identification" 
               class="scan-3d-asset"
               onerror="this.onerror=null; this.src='{{ asset('assets/images/slider/slider-3.png') }}';">
        </div>
      </div>

      <!-- Bottom Spacer for Vertical Balance -->
      <div style="height: 30px;"></div>
    </div>


    <!-- ════════════════════════════════════════════════════════════════════
         RIGHT SIDE: ASSET DETAILS (Tabs Navigasi Underline)
         ════════════════════════════════════════════════════════════════════ -->
    <div class="scan-data-side">
      <div class="portal-card">

        <!-- Portal Header: Hanya Judul Aset & Underline Tabs -->
        <div class="portal-header">
          <h1 class="portal-title">{{ $inventaris->display_name ?? $inventaris->nama_barang }}</h1>

          <!-- Underline Tabs Navigation (Murni Text, Sesuai Filter SIMA) -->
          <div class="tabs-sima">
            <button type="button" class="tab-item-sima active" data-step="step-umum" onclick="switchScanTab('step-umum')">
              <span>Informasi Rincian</span>
            </button>

            @if(!empty($kibFields))
              <button type="button" class="tab-item-sima" data-step="step-kib" onclick="switchScanTab('step-kib')">
                <span>Data KIB {{ $inventaris->jenis->kib_type }}</span>
              </button>
            @endif

            @if($hasKir)
              <button type="button" class="tab-item-sima" data-step="step-kir" onclick="switchScanTab('step-kir')">
                <span>Data Ruangan (KIR)</span>
              </button>
            @endif

            <button type="button" class="tab-item-sima" data-step="step-sistem" onclick="switchScanTab('step-sistem')">
              <span>Informasi Sistem</span>
            </button>

            <button type="button" class="tab-item-sima" data-step="step-foto" onclick="switchScanTab('step-foto')">
              <span>Foto Fisik</span>
            </button>
          </div>

          <!-- Responsive Standard SIMA Dropdown for Mobile / Narrow Screens -->
          <div class="tabs-sima-dropdown-wrap">
            <div class="searchable-dropdown" id="scan-section-dropdown">
              <button type="button" class="dropdown-trigger" id="scan-section-trigger" onclick="toggleScanDropdown(event)" aria-haspopup="true" aria-expanded="false">
                <span class="dropdown-selected-label" id="scan-section-label">Informasi Rincian</span>
                <svg class="dropdown-trigger-caret" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                  <polyline points="6 9 12 15 18 9"></polyline>
                </svg>
              </button>

              <div class="dropdown-menu-panel" id="scan-section-menu">
                <ul class="dropdown-options-list">
                  <li class="dropdown-option-item selected" data-value="step-umum" onclick="selectScanOption('step-umum', 'Informasi Rincian')">
                    <div class="dropdown-option-text">
                      <div class="dropdown-option-name">Informasi Rincian</div>
                    </div>
                  </li>
                  @if(!empty($kibFields))
                    <li class="dropdown-option-item" data-value="step-kib" onclick="selectScanOption('step-kib', 'Data KIB {{ $inventaris->jenis->kib_type }}')">
                      <div class="dropdown-option-text">
                        <div class="dropdown-option-name">Data KIB {{ $inventaris->jenis->kib_type }}</div>
                      </div>
                    </li>
                  @endif
                  @if($hasKir)
                    <li class="dropdown-option-item" data-value="step-kir" onclick="selectScanOption('step-kir', 'Data Ruangan (KIR)')">
                      <div class="dropdown-option-text">
                        <div class="dropdown-option-name">Data Ruangan (KIR)</div>
                      </div>
                    </li>
                  @endif
                  <li class="dropdown-option-item" data-value="step-sistem" onclick="selectScanOption('step-sistem', 'Informasi Sistem')">
                    <div class="dropdown-option-text">
                      <div class="dropdown-option-name">Informasi Sistem</div>
                    </div>
                  </li>
                  <li class="dropdown-option-item" data-value="step-foto" onclick="selectScanOption('step-foto', 'Foto Fisik')">
                    <div class="dropdown-option-text">
                      <div class="dropdown-option-name">Foto Fisik</div>
                    </div>
                  </li>
                </ul>
              </div>
            </div>
          </div>
        </div>

        <!-- Portal Body: Section Content Panels -->
        <div class="portal-card-body">

          <!-- Section 1: Informasi Rincian Inventaris -->
          <div id="step-umum" class="portal-step-panel active">
            <div class="portal-kv-grid grid-3">
              <div class="portal-kv-item">
                <span class="portal-kv-label">Jenis Inventaris</span>
                <span class="portal-kv-value primary-highlight">{{ $inventaris->jenis ? $inventaris->jenis->nama : '-' }}</span>
              </div>

              <div class="portal-kv-item">
                <span class="portal-kv-label">Kondisi Aset</span>
                <span class="portal-kv-value">{{ $kondisiLabel }}</span>
              </div>

              <div class="portal-kv-item">
                <span class="portal-kv-label">Kode Barang</span>
                <span class="portal-kv-value" style="font-family: 'JetBrains Mono', monospace;">{{ $inventaris->kode_barang ?: '-' }}</span>
              </div>

              <div class="portal-kv-item">
                <span class="portal-kv-label">Nomor Register (NUP)</span>
                <span class="portal-kv-value">{{ $inventaris->nup ?: '-' }}</span>
              </div>

              <div class="portal-kv-item">
                <span class="portal-kv-label">Tahun Perolehan</span>
                <span class="portal-kv-value">{{ $inventaris->tahun_perolehan ?: '-' }}</span>
              </div>

              <div class="portal-kv-item">
                <span class="portal-kv-label">Nilai Perolehan</span>
                <span class="portal-kv-value primary-highlight">
                  {{ $inventaris->nilai_perolehan ? 'Rp ' . number_format($inventaris->nilai_perolehan, 0, ',', '.') : '-' }}
                </span>
              </div>

              <div class="portal-kv-item">
                <span class="portal-kv-label">Asal Usul Barang</span>
                <span class="portal-kv-value">
                  @if($inventaris->asal_usul === 'apbdes') APBDes
                  @elseif($inventaris->asal_usul === 'perolehan_lain') Perolehan Lain yang Sah
                  @elseif($inventaris->asal_usul === 'kekayaan_asli') Kekayaan Asli
                  @else {{ $inventaris->asal_usul ?: '-' }}
                  @endif
                </span>
              </div>

              <div class="portal-kv-item">
                <span class="portal-kv-label">Lokasi / Ruangan</span>
                <span class="portal-kv-value">
                  @if($inventaris->ruangan)
                    {{ $inventaris->ruangan->nama }} {{ $inventaris->ruangan->kode ? '(Kode: ' . $inventaris->ruangan->kode . ')' : '' }}
                  @else
                    {{ $inventaris->lokasi ?? '-' }}
                  @endif
                </span>
              </div>

              <div class="portal-kv-item">
                <span class="portal-kv-label">Pengguna / Unit</span>
                <span class="portal-kv-value">{{ $inventaris->pengguna ?: '-' }}</span>
              </div>

              <div class="portal-kv-item">
                <span class="portal-kv-label">Satuan Barang</span>
                <span class="portal-kv-value">{{ is_string($inventaris->satuan) ? $inventaris->satuan : ($inventaris->satuan?->nama ?? ($inventaris->satuan ?: '-')) }}</span>
              </div>

              <div class="portal-kv-item">
                <span class="portal-kv-label">Penanggung Jawab</span>
                <span class="portal-kv-value">
                  @if($inventaris->ruangan && $inventaris->ruangan->penanggungJawab)
                    {{ $inventaris->ruangan->penanggungJawab->nama }}
                  @else
                    {{ $inventaris->penanggung_jawab ?? '-' }}
                  @endif
                </span>
              </div>

              <div class="portal-kv-item">
                <span class="portal-kv-label">Bukti Kepemilikan</span>
                <span class="portal-kv-value">{{ $inventaris->bukti_kepemilikan ?: '-' }}</span>
              </div>

              <div class="portal-kv-item">
                <span class="portal-kv-label">Bulan Perolehan</span>
                <span class="portal-kv-value">{{ $bp && isset($bulanNames[$bp]) ? $bulanNames[$bp] : '-' }}</span>
              </div>

              <div class="portal-kv-item" style="grid-column: 1 / -1;">
                <span class="portal-kv-label">Keterangan</span>
                <span class="portal-kv-value">{{ $inventaris->keterangan ?: '-' }}</span>
              </div>
            </div>
          </div>

          <!-- Section 2: Data KIB -->
          @if(!empty($kibFields))
            <div id="step-kib" class="portal-step-panel">
              <div class="portal-kv-grid grid-3">
                @foreach($kibFields as $field)
                  @php
                    $fieldKey = $field['key'] ?? $field['name'] ?? null;
                    $val = null;
                    if ($fieldKey && method_exists($inventaris, 'getKibValue')) {
                      $val = $inventaris->getKibValue($fieldKey);
                    } elseif ($fieldKey) {
                      $val = $inventaris->{$fieldKey} ?? null;
                    }
                  @endphp
                  <div class="portal-kv-item">
                    <span class="portal-kv-label">{{ $field['label'] ?? $fieldKey }}</span>
                    <span class="portal-kv-value {{ empty($val) && $val !== '0' && $val !== 0 ? 'empty' : '' }}">
                      {{ (!empty($val) || $val === '0' || $val === 0) ? $val : '-' }}
                    </span>
                  </div>
                @endforeach
              </div>
            </div>
          @endif

          <!-- Section 3: Data Ruangan (KIR) -->
          @if($hasKir)
            <div id="step-kir" class="portal-step-panel">
              <div class="portal-kv-grid grid-2">
                <div class="portal-kv-item">
                  <span class="portal-kv-label">Nama Ruangan</span>
                  <span class="portal-kv-value primary-highlight">{{ $inventaris->ruangan?->nama ?? '-' }}</span>
                </div>
                <div class="portal-kv-item">
                  <span class="portal-kv-label">Kode Ruangan</span>
                  <span class="portal-kv-value" style="font-family: 'JetBrains Mono', monospace;">{{ $inventaris->ruangan?->kode ?? '-' }}</span>
                </div>
                <div class="portal-kv-item">
                  <span class="portal-kv-label">Penanggung Jawab Ruangan</span>
                  <span class="portal-kv-value">{{ $inventaris->ruangan?->penanggungJawab?->nama ?? '-' }}</span>
                </div>
                <div class="portal-kv-item">
                  <span class="portal-kv-label">Jabatan / Peran Penanggung Jawab</span>
                  <span class="portal-kv-value">{{ $inventaris->ruangan?->penanggungJawab?->role_name_display ?? ($inventaris->ruangan?->penanggungJawab?->pejabat?->jabatan ?? ($inventaris->ruangan?->penanggungJawab?->nip ?? '-')) }}</span>
                </div>
              </div>
            </div>
          @endif

          <!-- Section 4: Informasi Sistem -->
          <div id="step-sistem" class="portal-step-panel">
            <div class="portal-kv-grid grid-3">
              <div class="portal-kv-item">
                <span class="portal-kv-label">Kode Barcode</span>
                <span class="portal-kv-value" style="font-family: 'JetBrains Mono', monospace;">{{ $inventaris->barcode ?? '-' }}</span>
              </div>
              <div class="portal-kv-item">
                <span class="portal-kv-label">Tanggal Dibuat</span>
                <span class="portal-kv-value">{{ $inventaris->created_at ? $inventaris->created_at->format('d M Y, H:i') : '-' }}</span>
              </div>
              <div class="portal-kv-item">
                <span class="portal-kv-label">Terakhir Diperbarui</span>
                <span class="portal-kv-value">{{ $inventaris->updated_at ? $inventaris->updated_at->format('d M Y, H:i') : '-' }}</span>
              </div>
            </div>
          </div>

          <!-- Section 5: Foto Fisik Aset — Standard Portal Gallery -->
          <div id="step-foto" class="portal-step-panel">
            @if($hasFotos)
              @php
                $fotos = $inventaris->fotos;
                $firstFoto = $fotos->first();
                $scanFotoUrls = $fotos->map(function($f) {
                    return asset('storage/' . $f->path);
                })->values();
              @endphp
              <div class="portal-gallery-wrap">
                <!-- Main photo frame — always full height, never shrinks -->
                <div class="portal-gallery-main" id="scan-main-foto-box">
                  <div onclick="openScanFotoViewer()" style="width:100%;height:100%;display:flex;align-items:center;justify-content:center;cursor:zoom-in;" title="Klik untuk memperbesar foto (Layar Penuh)">
                    <img src="{{ asset('storage/' . $firstFoto->path) }}"
                         id="scan-main-foto-img"
                         alt="Foto Fisik Aset"
                         style="cursor: zoom-in;">
                  </div>

                  {{-- Floating overlay thumbnails — HANYA JIKA > 1 FOTO --}}
                  @if($fotos->count() > 1)
                    <div class="portal-gallery-thumbs">
                      @foreach($fotos as $idx => $foto)
                        <div class="portal-gallery-thumb {{ $idx === 0 ? 'active' : '' }}"
                             onclick="switchScanFoto('{{ asset('storage/' . $foto->path) }}', {{ $idx }}, this)"
                             title="Foto {{ $loop->iteration }}">
                          <img src="{{ asset('storage/' . $foto->path) }}" alt="Thumbnail {{ $loop->iteration }}" loading="lazy">
                        </div>
                      @endforeach
                    </div>
                  @endif
                </div>
              </div>
            @else
              <div class="portal-gallery-wrap">
                <div class="portal-gallery-main">
                  <div class="portal-gallery-placeholder">
                    <i data-feather="camera"></i>
                    <div style="font-weight: 600; color: var(--color-text); font-size: 15px;">Belum Ada Foto Aset</div>
                    <div style="font-size: 13px; color: var(--color-text-muted);">Foto fisik belum diunggah untuk aset ini</div>
                  </div>
                </div>
              </div>
            @endif
          </div>

        </div><!-- /.portal-card-body -->



      </div><!-- /.portal-card -->
    </div><!-- /.scan-data-side -->

  </div>

@else
  <!-- Error Not Found Screen -->
  <div style="min-height: 100vh; display: flex; align-items: center; justify-content: center; padding: 24px; background: var(--color-bg);">
    <div style="max-width: 440px; background: var(--color-surface); border: 1px solid var(--color-border); border-radius: 16px; padding: 40px 28px; text-align: center;">
      <div style="width: 60px; height: 60px; border-radius: 50%; background: var(--color-danger-soft); color: var(--color-danger); display: flex; align-items: center; justify-content: center; margin: 0 auto 16px;">
        <i data-feather="alert-triangle" style="width: 28px; height: 28px;"></i>
      </div>
      <h2 style="font-size: 19px; font-weight: 700; color: var(--color-text); margin-bottom: 8px;">Aset Tidak Ditemukan</h2>
      <p style="font-size: 13px; color: var(--color-text-muted); line-height: 1.5; margin-bottom: 24px;">
        Barcode atau QR Code ini tidak terhubung dengan data aset manapun pada sistem inventaris desa.
      </p>
      <a href="{{ url('/') }}" class="btn-sima-primary" style="justify-content: center;">
        <span>Kembali ke Halaman Utama</span>
      </a>
    </div>
  </div>
@endif

<!-- FULL SCREEN GOOGLE PHOTOS/DRIVE STYLE VIEWER -->
@if(!empty($inventaris) && isset($fotos) && $fotos->count() > 0)
<div class="sima-foto-viewer" id="scanSimaImageViewer" role="dialog" aria-modal="true" aria-label="Penampil Foto Aset" onclick="if(event.target === this) closeScanFotoViewer()">
  <div class="sfv-topbar">
    <div class="sfv-counter" id="scanSfvCounter">
      Foto <span id="scanSfvCurrentIdx">1</span> dari {{ $fotos->count() }} &bull; {{ $inventaris->display_name ?? 'Foto Fisik Aset' }}
    </div>
    <button type="button" class="sfv-close-btn" onclick="closeScanFotoViewer()" title="Tutup (Esc)">
      <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
        <line x1="18" y1="6" x2="6" y2="18"></line>
        <line x1="6" y1="6" x2="18" y2="18"></line>
      </svg>
    </button>
  </div>

  <div class="sfv-stage" onclick="if(event.target === this) closeScanFotoViewer()">
    @if($fotos->count() > 1)
    <button type="button" class="sfv-nav-btn sfv-prev" id="scanSfvPrevBtn" onclick="prevScanFoto(event)" title="Foto Sebelumnya (Panah Kiri)">
      <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round">
        <polyline points="15 18 9 12 15 6"></polyline>
      </svg>
    </button>
    @endif

    <img src="{{ asset('storage/' . $firstFoto->path) }}" class="sfv-main-img" id="scanSfvMainImg" alt="{{ $inventaris->display_name ?? 'Foto Aset' }}">

    @if($fotos->count() > 1)
    <button type="button" class="sfv-nav-btn sfv-next" id="scanSfvNextBtn" onclick="nextScanFoto(event)" title="Foto Berikutnya (Panah Kanan)">
      <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round">
        <polyline points="9 18 15 12 9 6"></polyline>
      </svg>
    </button>
    @endif
  </div>

  {{-- Bottom Thumbnail Strip: HANYA MUNCUL JIKA LEBIH DARI 1 FOTO --}}
  @if($fotos->count() > 1)
  <div class="sfv-strip" id="scanSfvStrip">
    @foreach($fotos as $idx => $f)
      <div class="sfv-strip-thumb {{ $idx === 0 ? 'active' : '' }}" 
           id="scan-sfv-thumb-{{ $idx }}" 
           onclick="goToScanFoto({{ $idx }})" 
           title="Foto {{ $loop->iteration }}">
        <img src="{{ asset('storage/' . $f->path) }}" alt="Thumbnail {{ $loop->iteration }}">
      </div>
    @endforeach
  </div>
  @endif
</div>
@endif

<script>
  // Initialize Feather Icons
  document.addEventListener('DOMContentLoaded', function () {
    if (typeof feather !== 'undefined') {
      feather.replace();
    }
  });

  // Standard SIMA Dropdown Handlers
  function toggleScanDropdown(e) {
    if (e) e.stopPropagation();
    const dropdown = document.getElementById('scan-section-dropdown');
    if (!dropdown) return;
    const trigger = document.getElementById('scan-section-trigger');
    const isOpen = dropdown.classList.toggle('open');
    if (trigger) {
      trigger.setAttribute('aria-expanded', isOpen ? 'true' : 'false');
    }
  }

  function selectScanOption(stepId, labelText) {
    // 1. Update dropdown selected label
    const labelElem = document.getElementById('scan-section-label');
    if (labelElem) {
      labelElem.textContent = labelText;
    }

    // 2. Update selected class on dropdown option items
    document.querySelectorAll('#scan-section-menu .dropdown-option-item').forEach(function(item) {
      if (item.getAttribute('data-value') === stepId) {
        item.classList.add('selected');
      } else {
        item.classList.remove('selected');
      }
    });

    // 3. Close dropdown
    const dropdown = document.getElementById('scan-section-dropdown');
    if (dropdown) {
      dropdown.classList.remove('open');
      const trigger = document.getElementById('scan-section-trigger');
      if (trigger) trigger.setAttribute('aria-expanded', 'false');
    }

    // 4. Switch tab panel
    switchScanTab(stepId);
  }

  // Close dropdown when clicking outside
  document.addEventListener('click', function(e) {
    const dropdown = document.getElementById('scan-section-dropdown');
    if (dropdown && dropdown.classList.contains('open') && !dropdown.contains(e.target)) {
      dropdown.classList.remove('open');
      const trigger = document.getElementById('scan-section-trigger');
      if (trigger) trigger.setAttribute('aria-expanded', 'false');
    }
  });

  // Tab Switching Function
  function switchScanTab(stepId) {
    // 1. Update underline tabs
    document.querySelectorAll('.tab-item-sima').forEach(function(tab) {
      if (tab.getAttribute('data-step') === stepId) {
        tab.classList.add('active');
      } else {
        tab.classList.remove('active');
      }
    });

    // 2. Synchronize standard dropdown selected item and label
    const activeOption = document.querySelector(`#scan-section-menu .dropdown-option-item[data-value="${stepId}"]`);
    if (activeOption) {
      document.querySelectorAll('#scan-section-menu .dropdown-option-item').forEach(function(item) {
        item.classList.remove('selected');
      });
      activeOption.classList.add('selected');
      const optName = activeOption.querySelector('.dropdown-option-name');
      const labelElem = document.getElementById('scan-section-label');
      if (optName && labelElem) {
        labelElem.textContent = optName.textContent.trim();
      }
    }

    // 3. Switch active content panel
    document.querySelectorAll('.portal-step-panel').forEach(function(panel) {
      if (panel.id === stepId) {
        panel.classList.add('active');
      } else {
        panel.classList.remove('active');
      }
    });

    if (typeof feather !== 'undefined') {
      feather.replace();
    }
  }

  // Auto-detect when tabs get cut off and switch to clean dropdown
  function updateTabsResponsive() {
    const tabs = document.querySelector('.tabs-sima');
    const dropdown = document.querySelector('.tabs-sima-dropdown-wrap');
    if (!tabs || !dropdown) return;

    if (window.innerWidth <= 768) {
      tabs.style.display = 'none';
      dropdown.style.display = 'block';
    } else {
      tabs.style.display = 'flex';
      dropdown.style.display = 'none';

      // If tabs overflow/cut off inside card boundary, switch to dropdown
      if (tabs.scrollWidth > tabs.clientWidth + 4) {
        tabs.style.display = 'none';
        dropdown.style.display = 'block';
      }
    }
  }

  window.addEventListener('resize', updateTabsResponsive);
  window.addEventListener('DOMContentLoaded', updateTabsResponsive);

  // Foto showcase switcher & Google Drive style viewer (Scan Page)
  const scanFotoList = @json($scanFotoUrls ?? []);
  let currentScanFotoIdx = 0;

  function switchScanFoto(src, idx, thumbEl) {
    currentScanFotoIdx = typeof idx === 'number' ? idx : 0;
    const mainImg = document.getElementById('scan-main-foto-img');
    if (mainImg) {
      mainImg.src = src;
    }
    // Update active thumbnail
    document.querySelectorAll('.portal-gallery-thumb').forEach(function(t) {
      t.classList.remove('active');
    });
    if (thumbEl) {
      thumbEl.classList.add('active');
    }
  }

  function openScanFotoViewer(idx) {
    if (!scanFotoList || scanFotoList.length === 0) return;
    if (typeof idx === 'number') {
      currentScanFotoIdx = idx;
    }
    updateScanFotoViewerUI();
    const viewer = document.getElementById('scanSimaImageViewer');
    if (viewer) {
      viewer.classList.add('open');
      document.body.style.overflow = 'hidden';
    }
  }

  function closeScanFotoViewer() {
    const viewer = document.getElementById('scanSimaImageViewer');
    if (viewer) {
      viewer.classList.remove('open');
      document.body.style.overflow = '';
    }
  }

  function updateScanFotoViewerUI() {
    if (!scanFotoList || scanFotoList.length === 0) return;
    if (currentScanFotoIdx < 0) currentScanFotoIdx = 0;
    if (currentScanFotoIdx >= scanFotoList.length) currentScanFotoIdx = scanFotoList.length - 1;

    const currentSrc = scanFotoList[currentScanFotoIdx];
    const img = document.getElementById('scanSfvMainImg');
    if (img && currentSrc) {
      img.src = currentSrc;
    }

    const counterSpan = document.getElementById('scanSfvCurrentIdx');
    if (counterSpan) {
      counterSpan.textContent = (currentScanFotoIdx + 1);
    }

    // Strip thumbnails di bawah
    document.querySelectorAll('#scanSfvStrip .sfv-strip-thumb').forEach((t, i) => {
      if (i === currentScanFotoIdx) {
        t.classList.add('active');
        t.scrollIntoView({ behavior: 'smooth', block: 'nearest', inline: 'center' });
      } else {
        t.classList.remove('active');
      }
    });

    // Sinkronkan juga gambar di kartu utama
    const mainImg = document.getElementById('scan-main-foto-img');
    if (mainImg && currentSrc) {
      mainImg.src = currentSrc;
    }
    document.querySelectorAll('.portal-gallery-thumb').forEach((t, i) => {
      if (i === currentScanFotoIdx) {
        t.classList.add('active');
      } else {
        t.classList.remove('active');
      }
    });

    // Disabled state tombol prev/next
    const prevBtn = document.getElementById('scanSfvPrevBtn');
    const nextBtn = document.getElementById('scanSfvNextBtn');
    if (prevBtn) prevBtn.disabled = (currentScanFotoIdx === 0);
    if (nextBtn) nextBtn.disabled = (currentScanFotoIdx === scanFotoList.length - 1);
  }

  function prevScanFoto(e) {
    if (e) e.stopPropagation();
    if (currentScanFotoIdx > 0) {
      currentScanFotoIdx--;
      updateScanFotoViewerUI();
    }
  }

  function nextScanFoto(e) {
    if (e) e.stopPropagation();
    if (currentScanFotoIdx < scanFotoList.length - 1) {
      currentScanFotoIdx++;
      updateScanFotoViewerUI();
    }
  }

  function goToScanFoto(idx) {
    currentScanFotoIdx = idx;
    updateScanFotoViewerUI();
  }

  // Keyboard navigation
  document.addEventListener('keydown', function(e) {
    const viewer = document.getElementById('scanSimaImageViewer');
    if (!viewer || !viewer.classList.contains('open')) return;
    if (e.key === 'Escape') closeScanFotoViewer();
    else if (e.key === 'ArrowLeft') prevScanFoto();
    else if (e.key === 'ArrowRight') nextScanFoto();
  });
</script>
</body>
</html>
