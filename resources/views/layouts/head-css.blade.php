<script>
	document.documentElement.setAttribute('dir', 'ltr');
</script>
<!-- [Google Fonts: Inter & Plus Jakarta Sans] -->
<link rel="preconnect" href="https://fonts.googleapis.com">
<link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
<link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&family=Plus+Jakarta+Sans:wght@400;500;600;700&display=swap" rel="stylesheet">

<!-- [Phosphor, Tabler, Feather, FontAwesome, Material Icons] -->
<link rel="stylesheet" href="{{ asset('assets/fonts/phosphor/duotone/style.css') }}" />
<link rel="stylesheet" href="{{ asset('assets/fonts/tabler-icons.min.css') }}" />
<link rel="stylesheet" href="{{ asset('assets/fonts/feather.css') }}" />
<link rel="stylesheet" href="{{ asset('assets/fonts/fontawesome.css') }}" />
<link rel="stylesheet" href="{{ asset('assets/fonts/material.css') }}" />

<!-- [Template CSS] -->
<link rel="stylesheet" href="{{ asset('assets/css/style.css') }}?v={{ file_exists(public_path('assets/css/style.css')) ? filemtime(public_path('assets/css/style.css')) : time() }}" id="main-style-link" />

<!-- [SIMA Design System - Core Tokens & Components] -->
<link rel="stylesheet" href="{{ asset('assets/css/design-system.css') }}?v={{ file_exists(public_path('assets/css/design-system.css')) ? filemtime(public_path('assets/css/design-system.css')) : time() }}" />

<style>
  /* Layout shell alignment */
  @media (min-width: 1024px) {
    .pc-sidebar {
      left: 0px !important;
      width: 260px !important;
    }
    .pc-sidebar .navbar-wrapper {
      width: 260px !important;
    }
    .pc-sidebar.pc-sidebar-hide {
      transform: translate3d(-260px, 0, 0) !important;
    }
    .pc-container, .pc-footer {
      margin-left: 260px !important;
    }
    .pc-header {
      left: 260px !important;
    }
    .pc-sidebar.pc-sidebar-hide ~ .pc-container,
    .pc-sidebar.pc-sidebar-hide ~ .pc-footer {
      margin-left: 0px !important;
    }
    .pc-sidebar.pc-sidebar-hide ~ .pc-header {
      left: 0px !important;
    }
  }

  @media not all and (min-width: 1024px) {
    .pc-sidebar {
      position: fixed !important;
      top: 0 !important;
      bottom: 0 !important;
      left: -280px !important;
      width: 270px !important;
      z-index: 1050 !important;
      transition: left 0.25s cubic-bezier(0.16, 1, 0.3, 1) !important;
      box-shadow: none !important;
      background-color: var(--color-bg-sidebar) !important;
    }
    .pc-sidebar .navbar-wrapper {
      width: 270px !important;
      height: 100vh !important;
      background-color: var(--color-bg-sidebar) !important;
    }
    .pc-sidebar.mob-sidebar-active {
      left: 0px !important;
      box-shadow: 4px 0 32px rgba(31, 58, 42, 0.35) !important;
    }
    .pc-container, .pc-footer, .pc-header {
      margin-left: 0px !important;
      left: 0px !important;
    }
    .pc-menu-overlay {
      position: fixed !important;
      inset: 0 !important;
      background: rgba(22, 37, 27, 0.45) !important;
      backdrop-filter: blur(2.5px) !important;
      -webkit-backdrop-filter: blur(2.5px) !important;
      z-index: 1045 !important;
      animation: simaFadeIn 0.2s ease forwards !important;
    }
  }

  /* ==========================================================================
     TOPBAR RESPONSIVE LAYOUT (Clean Single-Row 70px with Expandable Mobile Search)
     ========================================================================== */
  .pc-header {
    height: 70px !important;
    min-height: 70px !important;
    max-height: 70px !important;
    background: var(--color-surface) !important;
    border-bottom: 1px solid var(--color-border) !important;
    box-shadow: none !important;
    padding: 0 24px !important;
    display: flex !important;
    align-items: center !important;
    justify-content: space-between !important;
    z-index: 1020 !important;
    box-sizing: border-box !important;
    top: 0 !important;
    transition: all 0.2s cubic-bezier(0.4, 0, 0.2, 1) !important;
  }

  .pc-header .header-wrapper {
    display: flex !important;
    align-items: center !important;
    justify-content: space-between !important;
    width: 100% !important;
    height: 100% !important;
    position: relative !important;
  }

  /* ==========================================================================
     TOPBAR ICON HOVER: ONLY COLOR CHANGE, NO BACKGROUND "CARD" BOX
     ========================================================================== */
  .pc-header .btn-sima-icon,
  .pc-header .pc-head-link,
  .pc-header .mobile-search-trigger,
  .pc-header .mobile-search-close-btn,
  .pc-header #sidebar-hide,
  .pc-header #mobile-collapse,
  .pc-header #notif-bell-link,
  .pc-header .header-user-profile > a {
    background: transparent !important;
    background-color: transparent !important;
    border-color: transparent !important;
    box-shadow: none !important;
    transition: color 0.15s ease, stroke 0.15s ease !important;
  }

  .pc-header .btn-sima-icon:hover,
  .pc-header .btn-sima-icon:active,
  .pc-header .pc-head-link:hover,
  .pc-header .pc-head-link:active,
  .pc-header .mobile-search-trigger:hover,
  .pc-header .mobile-search-trigger:active,
  .pc-header .mobile-search-close-btn:hover,
  .pc-header .mobile-search-close-btn:active,
  .pc-header #sidebar-hide:hover,
  .pc-header #mobile-collapse:hover,
  .pc-header #notif-bell-link:hover,
  .pc-header .header-user-profile > a:hover {
    background: transparent !important;
    background-color: transparent !important;
    border-color: transparent !important;
    box-shadow: none !important;
    color: var(--color-primary, #1f3a2a) !important;
  }

  .pc-header .btn-sima-icon:hover i,
  .pc-header .btn-sima-icon:hover svg,
  .pc-header .pc-head-link:hover i,
  .pc-header .pc-head-link:hover svg,
  .pc-header .mobile-search-trigger:hover i,
  .pc-header .mobile-search-trigger:hover svg,
  .pc-header .mobile-search-close-btn:hover i,
  .pc-header .mobile-search-close-btn:hover svg,
  .pc-header #sidebar-hide:hover i,
  .pc-header #sidebar-hide:hover svg,
  .pc-header #mobile-collapse:hover i,
  .pc-header #mobile-collapse:hover svg,
  .pc-header #notif-bell-link:hover i,
  .pc-header #notif-bell-link:hover svg {
    color: var(--color-primary, #1f3a2a) !important;
    stroke: var(--color-primary, #1f3a2a) !important;
  }

  /* Disable template ::after card box on topbar icon links */
  .pc-header .pc-head-link::after,
  .pc-header .pc-head-link:hover::after,
  .pc-header .btn-sima-icon::after,
  .pc-header .btn-sima-icon:hover::after {
    display: none !important;
    content: none !important;
  }

  /* Universal Dropdown Safety:
     Ensure that any closed dropdown-menu is strictly non-interactive and completely hidden,
     preventing transparent phantom overlays from intercepting user clicks across the UI */
  .pc-header .pc-h-item.dropdown:not(.drp-show):not(.show) > .dropdown-menu,
  .dropdown:not(.drp-show):not(.show) > .dropdown-menu,
  .dropdown-menu:not(.show):not(.drp-show),
  .dropdown-user-profile:not(.show):not(.drp-show),
  .dropdown-notification:not(.show):not(.drp-show) {
    display: none !important;
    visibility: hidden !important;
    pointer-events: none !important;
    opacity: 0 !important;
  }

  .pc-header .pc-h-item.dropdown.drp-show > .dropdown-menu,
  .pc-header .pc-h-item.dropdown.show > .dropdown-menu,
  .dropdown.drp-show > .dropdown-menu,
  .dropdown.show > .dropdown-menu,
  .dropdown-menu.show,
  .dropdown-menu.drp-show {
    display: block !important;
    visibility: visible !important;
    pointer-events: auto !important;
    opacity: 1 !important;
  }

  /* Desktop layout (>= 992px) */
  @media (min-width: 992px) {
    .mobile-search-trigger {
      display: none !important;
    }
    .mobile-search-close-btn {
      display: none !important;
    }
    .topbar-left {
      order: 1 !important;
      margin-right: auto !important;
      display: flex !important;
      align-items: center !important;
    }
    .topbar-search-wrapper {
      order: 2 !important;
      margin-right: 14px !important;
      display: flex !important;
      align-items: center !important;
    }
    .topbar-actions {
      order: 3 !important;
      margin-left: 0 !important;
      display: flex !important;
      align-items: center !important;
      gap: 12px !important;
    }
  }

  /* Mobile & Tablet layout (< 992px, e.g. iPhone XR, Android, iPad) */
  @media (max-width: 991px) {
    .pc-header {
      padding: 0 16px !important;
      position: relative !important;
      height: 70px !important;
      min-height: 70px !important;
      max-height: 70px !important;
      left: 0 !important;
      right: 0 !important;
      width: 100% !important;
    }

    .pc-header .header-wrapper {
      height: 100% !important;
    }

    .topbar-left {
      display: flex !important;
      align-items: center !important;
      margin-right: auto !important;
      z-index: 1022 !important;
    }

    .topbar-actions {
      display: flex !important;
      align-items: center !important;
      gap: 10px !important;
      margin-left: auto !important;
      z-index: 1022 !important;
    }

    /* Mobile Search Trigger Icon (Beside bell) */
    .mobile-search-trigger {
      display: inline-flex !important;
      align-items: center !important;
      justify-content: center !important;
      width: 36px !important;
      height: 36px !important;
      border-radius: var(--radius-full, 9999px) !important;
      color: var(--color-text-muted) !important;
      background: transparent !important;
      border: 1px solid transparent !important;
      cursor: pointer !important;
      transition: all 0.2s ease !important;
      flex-shrink: 0 !important;
    }

    .mobile-search-trigger:hover,
    .mobile-search-trigger:active {
      background: var(--color-primary-tint, #e8f0e6) !important;
      color: var(--color-primary, #1f3a2a) !important;
    }

    /* When search is expanded: completely hide the trigger button to prevent duplicate icons */
    .pc-header.is-search-expanded .mobile-search-trigger {
      display: none !important;
    }

    /* Hidden by default on mobile */
    .topbar-search-wrapper {
      display: none !important;
    }

    /* When search is expanded on mobile: smoothly slide in to the left */
    .pc-header.is-search-expanded .topbar-search-wrapper {
      display: flex !important;
      position: absolute !important;
      left: 54px !important; /* Beside hamburger button */
      right: 92px !important; /* Beside bell and avatar on the right */
      top: 0 !important;
      bottom: 0 !important;
      align-items: center !important;
      transform: none !important; /* Avoid transform so child position:fixed is relative to viewport */
      z-index: 1028 !important;
    }

    .pc-header.is-search-expanded .topbar-search-box {
      width: 100% !important;
      max-width: 100% !important;
      height: 40px !important;
      box-shadow: 0 4px 16px rgba(31, 58, 42, 0.12) !important;
      border-color: var(--color-primary, #1f3a2a) !important;
      background-color: var(--color-surface, #ffffff) !important;
      padding: 0 12px !important;
      display: flex !important;
      align-items: center !important;
      gap: 8px !important;
      box-sizing: border-box !important;
      border-radius: var(--radius-full, 9999px) !important;
      animation: simaSearchSlideIn 0.22s cubic-bezier(0.16, 1, 0.3, 1) forwards !important;
      transform-origin: right center !important;
    }

    .pc-header.is-search-expanded .topbar-search-input {
      width: 100% !important;
      flex: 1 !important;
      min-width: 0 !important;
      font-size: 13px !important;
      border: none !important;
      outline: none !important;
      background: transparent !important;
      color: var(--color-text) !important;
    }

    .pc-header.is-search-expanded .topbar-search-shortcut {
      display: none !important;
    }

    .pc-header.is-search-expanded .mobile-search-close-btn {
      display: inline-flex !important;
      align-items: center !important;
      justify-content: center !important;
      width: 24px !important;
      height: 24px !important;
      background: transparent !important;
      border: none !important;
      color: var(--color-text-muted) !important;
      cursor: pointer !important;
      border-radius: 50% !important;
      padding: 0 !important;
      flex-shrink: 0 !important;
    }

    .pc-header.is-search-expanded .mobile-search-close-btn:hover {
      color: var(--color-primary, #1f3a2a) !important;
      background: transparent !important;
    }

    /* Command Palette Dropdown on Mobile: Symmetrical 16px Left & Right Margins */
    .global-search-dropdown-panel {
      position: fixed !important;
      left: 16px !important;
      right: 16px !important;
      top: 68px !important;
      width: auto !important;
      max-width: none !important;
      box-sizing: border-box !important;
      z-index: 1065 !important;
      box-shadow: 0 8px 28px rgba(31, 58, 42, 0.16) !important;
    }

    .global-search-shortcuts-hint {
      display: none !important;
    }

    /* Notification & User Profile Dropdown boundary on mobile: strictly pinned to right within screen */
    .pc-header .pc-h-item.dropdown:not(.drp-show):not(.show) > .dropdown-menu,
    .dropdown-notification:not(.show):not(.drp-show),
    .dropdown-user-profile:not(.show):not(.drp-show) {
      display: none !important;
      visibility: hidden !important;
      pointer-events: none !important;
      opacity: 0 !important;
    }

    html body .pc-header .pc-h-item.dropdown.drp-show > .dropdown-menu.dropdown-notification,
    html body .pc-header .pc-h-item.dropdown.show > .dropdown-menu.dropdown-notification,
    .notif-popup-panel.show,
    .notif-popup-panel.drp-show {
      display: block !important;
      visibility: visible !important;
      pointer-events: auto !important;
      opacity: 1 !important;
      position: absolute !important;
      left: auto !important;
      right: -46px !important;
      top: calc(100% + 8px) !important;
      transform: none !important;
      width: 360px !important;
      min-width: 0 !important;
      max-width: calc(100vw - 24px) !important;
      box-sizing: border-box !important;
      z-index: 1060 !important;
    }

    html body .pc-header .pc-h-item.dropdown.drp-show > .dropdown-menu.dropdown-user-profile,
    html body .pc-header .pc-h-item.dropdown.show > .dropdown-menu.dropdown-user-profile,
    .header-user-profile.drp-show .dropdown-user-profile,
    .header-user-profile.show .dropdown-user-profile,
    .dropdown-user-profile.show,
    .dropdown-user-profile.drp-show {
      display: block !important;
      visibility: visible !important;
      pointer-events: auto !important;
      opacity: 1 !important;
      position: absolute !important;
      left: auto !important;
      right: 0 !important;
      top: calc(100% + 8px) !important;
      transform: none !important;
      width: 280px !important;
      min-width: 0 !important;
      max-width: calc(100vw - 24px) !important;
      box-sizing: border-box !important;
      z-index: 1060 !important;
    }

    /* Universal Responsive Pagination Layout (Penghapusan Aset Golden Reference Standard) */
    .pagination-bar {
      display: flex !important;
      flex-wrap: wrap !important;
      justify-content: center !important;
      align-items: center !important;
      text-align: center !important;
      gap: 12px 16px !important;
      padding: 16px 12px 20px 12px !important;
      width: 100% !important;
      box-sizing: border-box !important;
    }
    .pagination-left {
      display: contents !important;
    }
    .pagination-center {
      display: contents !important;
    }
    .pagination-nav {
      order: 1 !important;
      width: 100% !important;
      display: flex !important;
      flex-wrap: wrap !important;
      justify-content: center !important;
      align-items: center !important;
      gap: 6px !important;
    }
    .page-size-wrap {
      order: 2 !important;
      display: inline-flex !important;
      align-items: center !important;
      justify-content: center !important;
      gap: 6px !important;
      font-size: 13px !important;
      margin: 0 !important;
    }
    .page-size-wrap select {
      padding: 4px 28px 4px 10px !important;
      height: 34px !important;
      font-size: 13px !important;
    }
    .page-jump-wrap {
      order: 3 !important;
      display: inline-flex !important;
      align-items: center !important;
      justify-content: center !important;
      gap: 6px !important;
      font-size: 13px !important;
      margin: 0 !important;
    }
    .page-jump-input {
      width: 52px !important;
      height: 34px !important;
      font-size: 13px !important;
    }
    .pagination-right {
      order: 4 !important;
      width: 100% !important;
      display: flex !important;
      justify-content: center !important;
      text-align: center !important;
    }

    /* Page container starts cleanly at 70px */
    .pc-container {
      top: 70px !important;
      min-height: calc(100vh - 70px) !important;
      padding: 16px 14px 36px 14px !important;
    }
  }

  @keyframes simaSearchSlideIn {
    0% {
      opacity: 0;
      transform: scaleX(0.2);
    }
    100% {
      opacity: 1;
      transform: scaleX(1);
    }
  }

  @keyframes simaExpandLeft {
    0% {
      opacity: 0;
      transform: translateY(-50%) scaleX(0.3);
      transform-origin: right center;
    }
    100% {
      opacity: 1;
      transform: translateY(-50%) scaleX(1);
      transform-origin: right center;
    }
  }
  @keyframes simaFadeIn {
    0% { opacity: 0; }
    100% { opacity: 1; }
  }

    .pc-container .pc-content {
      padding-left: 0 !important;
      padding-right: 0 !important;
      padding-top: 0 !important;
    }

    .sima-search-highlight-target {
      scroll-margin-top: 124px !important;
    }
  }

  /* Universal container & content alignment across all screens (exact match with footer) */
  .pc-container .pc-content {
    max-width: 1560px !important;
    margin-left: auto !important;
    margin-right: auto !important;
    box-sizing: border-box !important;
  }

  /* Global SIMA Card & Surface Styling - Standard 32px Margin to Footer (Exact Index Match) */
  .card,
  .sima-card {
    background-color: var(--color-surface, #f8faf7) !important;
    border: 1px solid var(--color-border, #cfdcc8) !important;
    border-radius: var(--radius-lg, 16px) !important;
    box-shadow: none !important;
    margin-bottom: 32px !important;
    overflow: visible !important;
  }

  /* ════════════════════════════════════════════════════════════════════
     STANDAR BAKU GLOBAL: ZERO SHADOW PADA SEMUA ELEMEN HOVER (SIMA DESA)
     Aturan mutlak: Seluruh elemen saat state :hover HANYA boleh berupa
     transisi background, warna teks, border, atau translasi fisik halus.
     DILARANG KERAS memunculkan atau memperbesar box-shadow saat hover!
     ════════════════════════════════════════════════════════════════════ */
  .btn:hover,
  button:hover,
  a:hover,
  .card:hover,
  .sima-card:hover,
  .btn-primary:hover,
  .btn-secondary:hover,
  .btn-submit:hover,
  .btn-cancel:hover,
  .btn-action-sima:hover,
  .dropdown-item:hover,
  .dropdown-trigger:hover,
  .badge:hover,
  .status-pill:hover,
  .nav-link:hover,
  .table-row:hover,
  tr:hover,
  .form-control:hover,
  .form-select:hover,
  .search-box:hover,
  .photo-card:hover,
  .widget-card:hover,
  .info-box:hover,
  [class*="hover:shadow"]:hover {
    box-shadow: none !important;
  }

  /* Spacing between filter trigger button and dropdown menu (breathing room, no sticking) */
  .dropdown-menu[data-bs-popper],
  .dropdown-menu.show,
  .dropdown .dropdown-menu,
  .btn-group .dropdown-menu {
    top: calc(100% + 10px) !important;
    margin-top: 10px !important;
  }
  .card .card-header,
  .card-header {
    background-color: transparent !important;
    padding: 24px 24px 0 24px !important;
    border: none !important;
    border-bottom: none !important;
  }
  .card .card-header > div,
  .card-header > div,
  .card-header-inner,
  .card-header > .flex {
    border-bottom: 1px dashed var(--color-border, #cfdcc8) !important;
    padding-bottom: 20px !important;
    width: 100% !important;
  }
  .card-body {
    background-color: transparent !important;
  }
  .card-body > .info-box:first-child,
  .card-body > form > .info-box:first-child,
  .card-body > .warning-box:first-child,
  .card-body > form > .warning-box:first-child,
  .card-body > .sima-info-box:first-child,
  .card-body > form > .sima-info-box:first-child,
  .card-body > .sima-warning-box:first-child,
  .card-body > form > .sima-warning-box:first-child,
  .info-box.top-info-box {
    margin-top: 0 !important;
  }
  /* Form Actions & Footer - No Divider Above Action Buttons */
  .card-footer,
  .form-actions,
  .card .card-footer,
  form .form-actions,
  .card-body .form-actions,
  .card-body > .form-actions {
    background-color: transparent !important;
    border-top: none !important;
    border: none !important;
  }

  /* Form Section inside Card - No Nested Box Borders */
  .card .form-section,
  .form-section {
    background: transparent !important;
    background-color: transparent !important;
    border: none !important;
    padding: 0 !important;
    margin-bottom: 24px !important;
    box-shadow: none !important;
  }
  .form-section + .form-section {
    border-top: 1px dashed var(--color-border, #cfdcc8) !important;
    margin-top: 24px !important;
    padding-top: 24px !important;
  }

  /* Global Form Controls & Inputs - Strict Warm Color Palette Token Alignment (No pure #FFF) */
  input:not([type="submit"]):not([type="button"]):not([type="checkbox"]):not([type="radio"]):not([type="range"]):not([type="color"]):not([type="hidden"]):not(.topbar-search-input):not(.is-invalid):not(.error),
  textarea:not(.is-invalid):not(.error),
  select:not(.is-invalid):not(.error),
  .form-control:not(.is-invalid):not(.error),
  .form-select:not(.is-invalid):not(.error),
  .custom-select:not(.is-invalid):not(.error),
  .dropdown-trigger,
  .ss-trigger {
    background-color: var(--color-surface, #f8faf7) !important;
    border: 1.5px solid var(--color-border, #cfdcc8) !important;
    color: var(--color-text, #16251b) !important;
    box-shadow: none !important;
    box-sizing: border-box !important;
  }

  .search-box,
  .topbar-search-box,
  .table-search-box {
    background-color: var(--color-surface, #f8faf7) !important;
    border: 1.5px solid var(--color-border, #cfdcc8) !important;
    color: var(--color-text, #16251b) !important;
    border-radius: var(--radius-full, 9999px) !important;
    width: 260px !important;
    max-width: 100% !important;
    transition: width 0.3s cubic-bezier(0.4, 0, 0.2, 1),
                border-color 0.2s ease,
                background-color 0.2s ease,
                box-shadow 0.25s cubic-bezier(0.4, 0, 0.2, 1) !important;
    box-shadow: none !important;
    box-sizing: border-box !important;
  }

  .search-box:hover,
  .topbar-search-box:hover,
  .table-search-box:hover {
    border-color: #b8c9b3 !important;
  }

  input:not(.topbar-search-input):not(.is-invalid):not(.error):focus,
  textarea:not(.is-invalid):not(.error):focus,
  select:not(.is-invalid):not(.error):focus,
  .form-control:not(.is-invalid):not(.error):focus,
  .form-control:not(.is-invalid):not(.error):focus-visible,
  .form-control:not(.is-invalid):not(.error):active,
  .form-select:not(.is-invalid):not(.error):focus,
  .form-select:not(.is-invalid):not(.error):focus-visible,
  .form-select:not(.is-invalid):not(.error):active,
  .custom-select:not(.is-invalid):not(.error):focus,
  .dropdown-trigger:focus,
  .ss-trigger:focus,
  .ss-trigger.ss-open {
    background-color: var(--color-surface, #f8faf7) !important;
    border-color: var(--color-primary, #1f3a2a) !important;
    color: var(--color-text, #16251b) !important;
    outline: none !important;
    box-shadow: none !important;
  }

  /* ==========================================================================
     STANDAR BAKU VALIDASI INPUT (SLICK UX - Dribbble Standard)
     Label merah (#D9383A) + Border merah (#E53935) + Zero Shadow + Warning Text
     ========================================================================== */
  .form-group.has-error .form-label,
  .form-group:has(.is-invalid) .form-label,
  .form-group:has(.error) .form-label,
  .form-group:has(.form-control.is-invalid) .form-label,
  .form-group:has(.form-select.is-invalid) .form-label,
  .form-group:has(textarea.is-invalid) .form-label,
  .form-group:has(.dropdown-trigger.is-invalid) .form-label,
  .form-group:has(.ss-trigger.is-invalid) .form-label,
  .kezak-field.has-error .kezak-label,
  .kezak-field:has(.is-invalid) .kezak-label,
  .kezak-field:has(.error) .kezak-label,
  .form-label.error,
  .form-label.is-invalid,
  .kezak-label.error,
  .kezak-label.is-invalid {
    color: #D9383A !important;
  }

  .form-group.has-error .form-label .required,
  .form-group:has(.is-invalid) .form-label .required,
  .form-group:has(.error) .form-label .required,
  .form-group:has(.form-control.is-invalid) .form-label .required,
  .form-label.error .required,
  .form-label.is-invalid .required,
  .kezak-field.has-error .kezak-label .req,
  .kezak-label.error .req {
    color: #D9383A !important;
  }

  /* Universal Validation Error: Red Border (1.5px solid #E53935) */
  input.is-invalid,
  input.error,
  select.is-invalid,
  select.error,
  textarea.is-invalid,
  textarea.error,
  .form-control.is-invalid,
  .form-control.error,
  .form-select.is-invalid,
  .form-select.error,
  .custom-select.is-invalid,
  .custom-select.error,
  .form-control-sima.is-invalid,
  .form-control-sima.error,
  .has-error input,
  .has-error select,
  .has-error textarea,
  .has-error .form-control,
  .has-error .form-select,
  .has-error .custom-select,
  .has-error .form-control-sima,
  .has-error .ss-trigger,
  .has-error .dropdown-trigger,
  .has-error .kezak-input,
  .ss-trigger.is-invalid,
  .dropdown-trigger.is-invalid,
  .ss-trigger.error,
  .dropdown-trigger.error {
    border: 1.5px solid #E53935 !important;
    border-color: #E53935 !important;
    box-shadow: none !important;
    outline: none !important;
  }

  input.is-invalid:focus,
  input.error:focus,
  select.is-invalid:focus,
  select.error:focus,
  textarea.is-invalid:focus,
  textarea.error:focus,
  .form-control.is-invalid:focus,
  .form-control.error:focus,
  .form-select.is-invalid:focus,
  .form-select.error:focus,
  .custom-select.is-invalid:focus,
  .custom-select.error:focus,
  .form-control-sima.is-invalid:focus,
  .form-control-sima.error:focus,
  .has-error input:focus,
  .has-error select:focus,
  .has-error textarea:focus,
  .has-error .form-control:focus,
  .has-error .kezak-input:focus,
  .has-error .ss-trigger:focus,
  .has-error .dropdown-trigger:focus,
  .dropdown-trigger.is-invalid:focus,
  .ss-trigger.is-invalid:focus {
    border: 1.5px solid #E53935 !important;
    border-color: #E53935 !important;
    outline: none !important;
    box-shadow: none !important;
  }

  /* Universal Feedback Error Message Container & Icon */
  .invalid-feedback-custom,
  .error-text,
  .invalid-feedback {
    display: flex;
    align-items: flex-start;
    gap: 6px;
    font-size: 12.5px;
    font-weight: 500;
    line-height: 1.4;
    color: #D9383A;
    margin-top: 6px;
  }

  .invalid-feedback-custom svg,
  .error-text svg,
  .invalid-feedback svg,
  .sima-error-icon {
    width: 14px;
    height: 14px;
    flex-shrink: 0;
    margin-top: 2px;
  }

  /* Universal Currency Prefix & Unit Suffix Input Wrapper Standard */
  .currency-input-wrapper,
  .unit-suffix-input-wrapper {
    position: relative !important;
    width: 100% !important;
    display: block !important;
  }

  .currency-prefix {
    position: absolute !important;
    left: 14px !important;
    top: 50% !important;
    transform: translateY(-50%) !important;
    font-size: 14px !important;
    color: var(--color-text-muted, #546958) !important;
    font-weight: 600 !important;
    line-height: 1 !important;
    pointer-events: none !important;
    user-select: none !important;
    z-index: 2 !important;
    white-space: nowrap !important;
  }

  .currency-input-wrapper .form-control,
  .currency-input-wrapper input.form-control,
  .currency-input-wrapper .currency-rupiah-input,
  input.currency-rupiah-input {
    padding-left: 44px !important;
  }

  .unit-suffix {
    position: absolute !important;
    right: 14px !important;
    top: 50% !important;
    transform: translateY(-50%) !important;
    font-size: 13px !important;
    color: var(--color-text-muted, #546958) !important;
    font-weight: 600 !important;
    line-height: 1 !important;
    pointer-events: none !important;
    user-select: none !important;
    z-index: 2 !important;
    white-space: nowrap !important;
  }

  .unit-suffix-input-wrapper .form-control,
  .unit-suffix-input-wrapper input.form-control {
    padding-right: 48px !important;
  }

  .file-upload-area.dragover {
    border-color: var(--color-primary, #1f3a2a) !important;
    background-color: var(--color-primary-tint, #d5e4d7) !important;
  }

  /* Photo Gallery Showcase (Standardized Consistent Height & Contain Sizing) */
  .portal-gallery {
    display: flex !important;
    flex-direction: column !important;
    gap: 12px !important;
    height: 100% !important;
    min-height: 0 !important;
  }

  .portal-gallery-main {
    width: 100% !important;
    flex: 1 1 0 !important;
    min-height: 0 !important;
    border-radius: 12px !important;
    border: 1px solid var(--color-border, #cfdcc8) !important;
    background: var(--color-bg, #eaf1e7) !important;
    overflow: hidden !important;
    position: relative !important;
    display: flex !important;
    align-items: center !important;
    justify-content: center !important;
  }

  .portal-gallery-main a {
    width: 100% !important;
    height: 100% !important;
    display: flex !important;
    align-items: center !important;
    justify-content: center !important;
    text-decoration: none !important;
  }

  .portal-gallery-main img {
    max-width: 100% !important;
    max-height: 100% !important;
    width: 100% !important;
    height: 100% !important;
    object-fit: contain !important;
    display: block !important;
    margin: auto !important;
    padding: 12px !important;
    box-sizing: border-box !important;
  }

  .portal-gallery-placeholder {
    display: flex !important;
    flex-direction: column !important;
    align-items: center !important;
    justify-content: center !important;
    gap: 10px !important;
    color: var(--color-text-muted, #546958) !important;
    text-align: center !important;
    padding: 24px !important;
    width: 100% !important;
    height: 100% !important;
    box-sizing: border-box !important;
  }

  .portal-gallery-thumbs {
    position: absolute !important;
    bottom: 12px !important;
    left: 12px !important;
    display: flex !important;
    gap: 8px !important;
    overflow-x: auto !important;
    max-width: calc(100% - 24px) !important;
    padding: 6px !important;
    background: rgba(255, 255, 255, 0.88) !important;
    backdrop-filter: blur(8px) !important;
    -webkit-backdrop-filter: blur(8px) !important;
    border: 1px solid rgba(207, 220, 200, 0.9) !important;
    border-radius: 10px !important;
    box-shadow: 0 4px 14px rgba(0, 0, 0, 0.08) !important;
    z-index: 5 !important;
    box-sizing: border-box !important;
    /* Dim by default (clearly faded); reveal full opacity on hover */
    opacity: 0.42 !important;
    transition: opacity 0.28s ease, transform 0.28s ease !important;
  }

  /* Reveal when user hovers anywhere on the main photo box or thumbnails */
  .portal-gallery-main:hover .portal-gallery-thumbs,
  .portal-gallery-thumbs:hover {
    opacity: 1 !important;
  }

  .portal-gallery-thumb {
    width: 48px !important;
    height: 48px !important;
    border-radius: 6px !important;
    border: 2px solid var(--color-border, #cfdcc8) !important;
    overflow: hidden !important;
    cursor: pointer !important;
    flex-shrink: 0 !important;
    transition: all 0.2s ease !important;
    background: var(--color-bg, #eaf1e7) !important;
    display: flex !important;
    align-items: center !important;
    justify-content: center !important;
    box-sizing: border-box !important;
  }

  .portal-gallery-thumb:hover {
    border-color: var(--color-primary, #1f3a2a) !important;
    transform: scale(1.08) !important;
  }

  .portal-gallery-thumb.active {
    border-color: var(--color-primary, #1f3a2a) !important;
    box-shadow: 0 0 0 2px var(--color-primary-tint, #d5e4d7) !important;
  }

  .portal-gallery-thumb img {
    width: 100% !important;
    height: 100% !important;
    object-fit: cover !important;
    display: block !important;
  }

  /* Cursor on clickable main image */
  .portal-gallery-main img,
  .portal-gallery-main .portal-main-foto-trigger {
    cursor: zoom-in !important;
  }

  /* ═══════════════════════════════════════════════════════════════
     SIMA FOTO VIEWER — Full-screen lightbox modal (Google Photos style)
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

  /* Top bar: close + counter */
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

  /* Main image area */
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

  /* Prev / Next arrow buttons */
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

  /* Bottom thumbnail strip */
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

  .search-box:focus-within,
  .topbar-search-box:focus-within,
  .table-search-box:focus-within {
    background-color: var(--color-surface, #f8faf7) !important;
    border-color: var(--color-primary, #1f3a2a) !important;
    color: var(--color-text, #16251b) !important;
    width: 360px !important;
    max-width: 100% !important;
    outline: none !important;
    box-shadow: 0 0 0 3px rgba(31, 58, 42, 0.1) !important;
  }

  .search-box > i,
  .search-box > svg,
  .topbar-search-box > i,
  .topbar-search-box > svg,
  .table-search-box > i,
  .table-search-box > svg {
    transition: transform 0.25s cubic-bezier(0.4, 0, 0.2, 1), color 0.2s ease !important;
    flex-shrink: 0 !important;
  }

  .search-box:focus-within > i,
  .search-box:focus-within > svg,
  .topbar-search-box:focus-within > i,
  .topbar-search-box:focus-within > svg,
  .table-search-box:focus-within > i,
  .table-search-box:focus-within > svg {
    color: var(--color-primary, #1f3a2a) !important;
    transform: scale(1.15);
  }

  /* Reset inner input inside composite search bar */
  .topbar-search-box {
    border-radius: var(--radius-full, 9999px) !important;
  }
  .topbar-search-input,
  .topbar-search-input:focus,
  .topbar-search-input:focus-visible,
  .topbar-search-input:active {
    background: transparent !important;
    background-color: transparent !important;
    border: none !important;
    border-width: 0 !important;
    outline: none !important;
    outline-offset: 0 !important;
    box-shadow: none !important;
    padding: 0 !important;
    margin: 0 !important;
    height: auto !important;
  }

  /* SIMA Standard Dashed Divider */
  hr, .divider, .sima-divider, .dropdown-divider {
    border: none !important;
    border-top: 1px dashed var(--color-border, #cfdcc8) !important;
    margin: 16px 0 !important;
    opacity: 1 !important;
    height: 0 !important;
  }
  .dropdown-menu hr,
  .dropdown-menu .divider,
  .dropdown-menu .sima-divider,
  .dropdown-menu .dropdown-divider,
  .export-dropdown-menu hr,
  .export-dropdown-menu .divider,
  .export-dropdown-menu .sima-divider,
  .export-dropdown-menu .dropdown-divider {
    border: none !important;
    border-top: 1px dashed var(--color-border, #cfdcc8) !important;
    margin: 4px 4px !important;
    opacity: 1 !important;
    height: 0 !important;
  }

  /* Status Tabs Underline Only & Clean Responsive Scroll (Scrollbar separated below active underline) */
  .tabs-sima {
    display: flex !important;
    gap: var(--space-6, 24px) !important;
    border-bottom: none !important;
    overflow-x: auto !important;
    overflow-y: hidden !important;
    max-width: 100% !important;
    padding-top: 4px !important;
    padding-bottom: 10px !important;
    margin-bottom: 18px !important;
    -webkit-overflow-scrolling: touch !important;
    scrollbar-width: thin !important;
    scrollbar-color: var(--color-border, #cfdcc8) transparent !important;
  }
  .tabs-sima::-webkit-scrollbar {
    height: 5px !important;
    display: block !important;
  }
  .tabs-sima::-webkit-scrollbar-track {
    background: rgba(31, 58, 42, 0.04) !important;
    border-radius: 9999px !important;
    margin: 0 4px !important;
  }
  .tabs-sima::-webkit-scrollbar-thumb {
    background-color: #b8c9b3 !important;
    border-radius: 9999px !important;
    border: none !important;
    transition: background-color 0.2s ease !important;
  }
  .tabs-sima::-webkit-scrollbar-thumb:hover {
    background-color: var(--color-text-muted, #546958) !important;
  }
  .tab-item-sima {
    padding: 8px 0 10px 0 !important;
    color: var(--color-text-muted) !important;
    font-size: 14px !important;
    font-weight: 500 !important;
    border: none !important;
    border-bottom: 2.5px solid transparent !important;
    text-decoration: none !important;
    display: inline-flex !important;
    align-items: center !important;
    gap: 6px !important;
    cursor: pointer !important;
    flex-shrink: 0 !important;
    white-space: nowrap !important;
    transition: color 150ms ease, border-color 150ms ease !important;
    background: transparent !important;
    user-select: none !important;
  }
  .tab-item-sima:hover {
    color: var(--color-text) !important;
  }
  .tab-item-sima.active {
    color: var(--color-primary, #1f3a2a) !important;
    border-bottom-color: var(--color-primary, #1f3a2a) !important;
    font-weight: 600 !important;
  }
  .tab-badge-sima {
    background: var(--color-primary-tint) !important;
    color: var(--color-primary) !important;
    font-size: 11px !important;
    font-weight: 600 !important;
    padding: 2px 7px !important;
    border-radius: var(--radius-full, 9999px) !important;
  }
  .notif-popup-tabs-wrapper {
    border-bottom: none !important;
  }

  /* Table & Table Card Separator Standards: Normal Straight Lines (Solid Line) */
  .table-sima th,
  .table-sima td,
  .modern-table thead th,
  .modern-table tbody td,
  .modern-table tbody tr,
  .table th,
  .table td {
    border-bottom: 1px solid var(--color-border, #cfdcc8) !important;
  }

  .sima-card > div[style*="border-bottom"],
  .sima-card > form[style*="border-bottom"] {
    border-bottom: 1px solid var(--color-border, #cfdcc8) !important;
  }

  .pagination-bar,
  .pagination-sima-container {
    border-top: 1px solid var(--color-border, #cfdcc8) !important;
  }

  /* ==========================================================================
     GLOBAL RESPONSIVE PAGINATION (Centered 4-Tier Standard across all views)
     Acuan Baku: Penghapusan Aset
     Row 1: Tampilkan [ 10 ˅ ] data
     Row 2: [ < ] [ 1 ] [ 2 ] [ 3 ] [ > ]
     Row 3: Ke hal. [ 1 ]
     Row 4: Menampilkan 1-10 dari 441 data
     ========================================================================== */
  @media (max-width: 768px) {
    /* 1. Main Pagination Containers */
    .pagination-sima-container,
    .pagination-bar {
      display: flex !important;
      flex-direction: column !important;
      align-items: center !important;
      justify-content: center !important;
      text-align: center !important;
      gap: 12px 14px !important;
      padding: 16px 12px 20px 12px !important;
      width: 100% !important;
      max-width: 100% !important;
      box-sizing: border-box !important;
      overflow-x: hidden !important;
    }

    /* 2. Direct sub-containers unpack via display: contents */
    .pagination-left,
    .pagination-center {
      display: contents !important;
    }

    /* Row 1: Per-Page Select Wrap */
    .page-size-wrap,
    .pagination-left .page-size-wrap,
    .pagination-sima-container .page-size-wrap {
      order: 1 !important;
      display: inline-flex !important;
      flex-direction: row !important;
      align-items: center !important;
      justify-content: center !important;
      gap: 6px !important;
      width: auto !important;
      margin: 0 auto !important;
      font-size: 13px !important;
      box-sizing: border-box !important;
    }

    /* Row 2: Page Navigation Number List (<nav>) */
    .pagination-sima,
    .pagination-nav {
      order: 2 !important;
      display: flex !important;
      flex-direction: row !important;
      align-items: center !important;
      justify-content: center !important;
      flex-wrap: wrap !important;
      gap: 4px !important;
      width: 100% !important;
      max-width: 100% !important;
      margin: 0 auto !important;
    }

    /* Page Buttons (Clean, compact, no overflow) */
    .page-btn-sima,
    .page-btn {
      min-width: 32px !important;
      height: 32px !important;
      padding: 0 6px !important;
      font-size: 12.5px !important;
      flex-shrink: 0 !important;
      display: inline-flex !important;
      align-items: center !important;
      justify-content: center !important;
      box-sizing: border-box !important;
    }

    /* Row 3: Jump to Page Wrap ("Ke hal. [ 1 ]") */
    .page-jump-wrap,
    .pagination-sima-container div:has(#pageJumpInput),
    .pagination-bar div:has(#pageJumpInput),
    .pagination-center .page-jump-wrap {
      order: 3 !important;
      display: inline-flex !important;
      flex-direction: row !important;
      align-items: center !important;
      justify-content: center !important;
      gap: 6px !important;
      width: auto !important;
      margin: 0 auto !important;
      font-size: 13px !important;
    }

    .page-jump-input,
    #pageJumpInput,
    #pageJumpInputAktif,
    #pageJumpInputRiwayat {
      width: 52px !important;
      height: 34px !important;
      font-size: 13px !important;
      text-align: center !important;
    }

    /* Row 4: Range Info Text (Menampilkan 1-10 dari 441 data) */
    #paginationRangeInfo,
    #paginationRangeInfoAktif,
    #paginationRangeInfoRiwayat,
    .pagination-right,
    .pagination-range-info {
      order: 4 !important;
      display: block !important;
      text-align: center !important;
      justify-content: center !important;
      width: 100% !important;
      font-size: 12.5px !important;
      color: var(--color-text-muted) !important;
      margin: 2px auto 0 auto !important;
      white-space: normal !important;
    }

    /* Per-Page Select */
    .per-page-select {
      height: 34px !important;
      font-size: 13px !important;
    }
  }

  @media (max-width: 420px) {
    .pagination-sima-container,
    .pagination-bar {
      padding: 16px 8px !important;
      gap: 12px !important;
    }

    .page-btn-sima,
    .page-btn {
      min-width: 29px !important;
      height: 29px !important;
      padding: 0 4px !important;
      font-size: 12px !important;
    }
  }

  /* ==========================================================================
     RESPONSIVE ZERO-CLIPPING: Export Dropdown & Filter Popover
     ========================================================================== */
  @media (max-width: 768px) {
    /* 1. Export Data Dropdown Menu (Prevents clipping off the left edge) */
    .export-dropdown-menu {
      left: 0 !important;
      right: auto !important;
      max-width: calc(100vw - 32px) !important;
    }

    /* 2. Filter Popover (Centered floating modal with screen-dimming backdrop) */
    .sima-filter-popover {
      position: fixed !important;
      top: 50% !important;
      left: 50% !important;
      transform: translate(-50%, -50%) !important;
      width: min(92vw, 360px) !important;
      min-width: unset !important;
      max-width: 92vw !important;
      max-height: 82vh !important;
      overflow-y: auto !important;
      z-index: 9999 !important;
      box-shadow: 0 16px 40px rgba(0, 0, 0, 0.35), 0 0 0 9999px rgba(15, 23, 42, 0.45) !important;
      border-radius: var(--radius-lg, 16px) !important;
      background: var(--color-surface, #ffffff) !important;
      border: 1px solid var(--color-border, #cfdcc8) !important;
      padding: 20px 20px !important;
      right: auto !important;
      bottom: auto !important;
      margin: 0 !important;
    }
  }

  /* Empty Data Fallback - Standard across project (plain text, no card / pill wrapper) */
  .text-empty-fallback,
  .empty-fallback {
    color: var(--color-warning, #B7791F) !important;
    font-weight: 500 !important;
    font-size: 13px !important;
    background: transparent !important;
    border: none !important;
    padding: 0 !important;
    display: inline !important;
  }

  /* Dropdown Triggers & Searchable Select Trigger Strict Palette Alignment */
  .dropdown-trigger {
    background-color: var(--color-surface, #f8faf7) !important;
    border: 1px solid var(--color-border, #cfdcc8) !important;
    border-radius: var(--radius-sm, 8px) !important;
    color: var(--color-text, #16251b) !important;
    min-height: 48px !important;
    padding: 12px 18px !important;
    font-size: 13.5px !important;
    font-weight: 500 !important;
    display: flex !important;
    align-items: center !important;
    justify-content: space-between !important;
    cursor: pointer !important;
    transition: all 0.2s ease !important;
    box-shadow: none !important;
    outline: none !important;
  }
  .ss-trigger,
  .month-only-picker-input {
    background-color: var(--color-surface, #f8faf7) !important;
    border: 1px solid var(--color-border, #cfdcc8) !important;
    color: var(--color-text, #16251b) !important;
  }
  .dropdown-trigger:hover,
  .ss-trigger:hover {
    border-color: var(--color-primary, #1f3a2a) !important;
    background-color: var(--color-row-hover, #eff5ed) !important;
  }
  .dropdown-trigger.open,
  .dropdown-trigger.active,
  .dropdown-trigger:focus,
  .searchable-dropdown.open .dropdown-trigger,
  .ss-trigger.ss-open,
  .ss-trigger:focus {
    border-color: var(--color-primary, #1f3a2a) !important;
    background-color: var(--color-surface, #f8faf7) !important;
    outline: none !important;
    box-shadow: none !important;
  }

  .dropdown-trigger-caret {
    transition: transform 0.2s ease !important;
    color: var(--color-text-muted, #546958) !important;
    width: 16px !important;
    height: 16px !important;
    flex-shrink: 0 !important;
  }
  .searchable-dropdown.open .dropdown-trigger-caret,
  .dropdown-trigger.open .dropdown-trigger-caret,
  .dropdown-trigger.active .dropdown-trigger-caret,
  .dropdown-trigger.active #existing-doc-caret {
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
    box-shadow: var(--shadow-float, 0 8px 24px rgba(31, 58, 42, 0.12)) !important;
    width: 100% !important;
    padding: 8px !important;
    box-sizing: border-box !important;
  }
  .searchable-dropdown.open .dropdown-menu-panel,
  .dropdown-menu-panel.show {
    display: block !important;
  }

  /* Form Select Global Standard (Native Dropdown Trigger) */
  .form-select,
  .form-select-sima,
  .custom-select {
    width: 100% !important;
    background-color: var(--color-surface, #f8faf7) !important;
    border: 1px solid var(--color-border, #cfdcc8) !important;
    border-radius: var(--radius-sm, 8px) !important;
    color: var(--color-text, #16251b) !important;
    min-height: 48px !important;
    padding: 12px 38px 12px 18px !important;
    font-size: 13.5px !important;
    font-weight: 500 !important;
    transition: all 0.2s ease !important;
    box-shadow: none !important;
    outline: none !important;
    appearance: none !important;
    -webkit-appearance: none !important;
    -moz-appearance: none !important;
    background-image: url("data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' width='16' height='16' viewBox='0 0 24 24' fill='none' stroke='%235E6C5A' stroke-width='2' stroke-linecap='round' stroke-linejoin='round'%3E%3Cpolyline points='6 9 12 15 18 9'%3E%3C/polyline%3E%3C/svg%3E") !important;
    background-repeat: no-repeat !important;
    background-position: right 18px center !important;
    background-size: 16px 16px !important;
    box-sizing: border-box !important;
    cursor: pointer !important;
  }
  .form-select:hover,
  .form-select-sima:hover,
  .custom-select:hover {
    border-color: var(--color-primary, #1f3a2a) !important;
    background-color: var(--color-row-hover, #eff5ed) !important;
  }
  .form-select:focus,
  .form-select-sima:focus,
  .custom-select:focus {
    border-color: var(--color-primary, #1f3a2a) !important;
    background-color: var(--color-surface, #f8faf7) !important;
    outline: none !important;
    box-shadow: none !important;
  }
  .form-select:disabled,
  .form-select-sima:disabled,
  .custom-select:disabled {
    opacity: 0.6 !important;
    cursor: not-allowed !important;
    background-color: #EFEFE9 !important;
  }
  .ss-trigger svg,
  .ss-trigger .ss-caret {
    transition: transform 0.2s ease !important;
    stroke: var(--color-text-muted, #546958) !important;
  }
  .ss-trigger.ss-open svg,
  .ss-trigger.ss-open .ss-caret {
    transform: rotate(180deg) !important;
  }

  /* Global Filter Popover Dropdown Standard */
  .filter-dropdown-menu {
    min-width: 320px !important;
    padding: 20px 24px !important;
    max-height: 85vh !important;
    overflow-y: auto !important;
    z-index: 1050 !important;
    border-radius: var(--radius-lg) !important;
    border: 1px solid var(--color-border) !important;
    box-shadow: var(--shadow-float) !important;
    background: var(--color-surface) !important;
    margin-top: 8px !important;
  }

  /* Segmented Card Option Radios - Hide native inputs strictly */
  .format-option input,
  .format-option input[type="radio"],
  .option-card input,
  .option-card input[type="radio"],
  .signature-method-option input,
  .signature-method-option input[type="radio"],
  input[type="radio"].sr-only,
  input[type="radio"][hidden] {
    display: none !important;
    position: absolute !important;
    opacity: 0 !important;
    width: 0 !important;
    height: 0 !important;
    pointer-events: none !important;
    visibility: hidden !important;
  }

  /* Month Only Picker (Popup Grid) */
  .flatpickr-calendar,
  .flatpickr-calendar.month-only-picker {
    background: var(--color-surface, #f8faf7) !important;
    background-color: var(--color-surface, #f8faf7) !important;
    border: 1.5px solid var(--color-border, #cfdcc8) !important;
    box-shadow: var(--shadow-float, 0 10px 25px rgba(0,0,0,0.08)) !important;
  }
  .flatpickr-calendar.month-only-picker .month-only-header {
    border-bottom: 1px dashed var(--color-border, #cfdcc8) !important;
  }
  .flatpickr-calendar.month-only-picker .month-only-title {
    color: var(--color-primary, #1f3a2a) !important;
  }
  .flatpickr-calendar.month-only-picker .month-only-option {
    border: 1.5px solid var(--color-border, #cfdcc8) !important;
    background: var(--color-surface, #f8faf7) !important;
    color: var(--color-text, #16251b) !important;
  }
  .flatpickr-calendar.month-only-picker .month-only-option:hover {
    background: var(--color-row-hover, #eff5ed) !important;
    border-color: var(--color-primary, #1f3a2a) !important;
    color: var(--color-primary, #1f3a2a) !important;
  }
  .flatpickr-calendar.month-only-picker .month-only-option.active {
    background: var(--color-primary, #1f3a2a) !important;
    border-color: var(--color-primary, #1f3a2a) !important;
    color: #ffffff !important;
    font-weight: 700 !important;
  }

  /* All Modals (Bootstrap modals & Custom Kode Barang Modals) */
  .modal-content,
  .modal-sima-box,
  #kb-search-modal > div,
  #kb-search-modal-edit > div {
    background: var(--color-surface, #f8faf7) !important;
    background-color: var(--color-surface, #f8faf7) !important;
    border: 1.5px solid var(--color-border, #cfdcc8) !important;
    color: var(--color-text, #16251b) !important;
    box-shadow: var(--shadow-float, 0 10px 25px rgba(0,0,0,0.08)) !important;
  }
  .modal-header,
  .modal-sima-header,
  .qr-modal-header {
    background: var(--color-surface, #f8faf7) !important;
    border-bottom: none !important;
    border-bottom-width: 0 !important;
    color: var(--color-text, #16251b) !important;
  }
  .modal-body,
  .modal-sima-body {
    background: var(--color-surface, #f8faf7) !important;
    color: var(--color-text, #16251b) !important;
  }
  .modal-footer,
  .modal-sima-footer,
  .qr-modal-footer {
    background: var(--color-surface, #f8faf7) !important;
    border-top: none !important;
    border-top-width: 0 !important;
  }

  .kb-level-btn,
  .kb-level-btn-edit {
    background: var(--color-bg, #eaf1e7) !important;
    border: 1.5px solid var(--color-border, #cfdcc8) !important;
    color: var(--color-text, #16251b) !important;
  }
  .kb-level-btn:hover,
  .kb-level-btn-edit:hover {
    background: var(--color-row-hover, #eff5ed) !important;
    border-color: var(--color-primary, #1f3a2a) !important;
    color: var(--color-primary, #1f3a2a) !important;
  }

  .dropdown-menu-panel,
  .ss-dropdown,
  .select2-dropdown,
  .dropdown-search-wrapper,
  .ss-search-wrap {
    background-color: var(--color-surface, #f8faf7) !important;
    border-color: var(--color-border, #cfdcc8) !important;
    color: var(--color-text, #16251b) !important;
  }

  .dropdown-search-input,
  .ss-search {
    transition: border-color 0.2s ease, box-shadow 0.25s cubic-bezier(0.4, 0, 0.2, 1) !important;
  }
  .dropdown-search-input:focus,
  .ss-search:focus {
    border-color: var(--color-primary, #1f3a2a) !important;
    box-shadow: 0 0 0 3px rgba(31, 58, 42, 0.1) !important;
    outline: none !important;
  }

  .dropdown-options-list,
  .ss-options {
    background-color: var(--color-surface, #f8faf7) !important;
  }

  .dropdown-option:hover,
  .ss-option:hover,
  .dropdown-option.selected,
  .ss-option.ss-selected {
    background-color: var(--color-row-hover, #eff5ed) !important;
    color: var(--color-primary, #1f3a2a) !important;
  }

  .btn-submit,
  button.btn-submit,
  a.btn-submit,
  .form-actions .btn-submit,
  .form-actions .btn-primary {
    height: 46px !important;
    padding: 0 24px !important;
    border: none !important;
    background-color: var(--color-primary, #1f3a2a) !important;
    color: #fff !important;
    border-radius: var(--radius-full, 9999px) !important;
    font-weight: 600 !important;
    font-size: 14px !important;
    cursor: pointer !important;
    box-shadow: none !important;
    transition: background-color 0.2s ease, transform 0.1s ease !important;
    display: inline-flex !important;
    align-items: center !important;
    justify-content: center !important;
    gap: 8px !important;
    text-decoration: none !important;
    box-sizing: border-box !important;
  }
  .btn-submit:hover,
  button.btn-submit:hover,
  a.btn-submit:hover,
  .form-actions .btn-submit:hover,
  .form-actions .btn-primary:hover {
    background-color: var(--color-primary-hover, #16251b) !important;
    color: #fff !important;
  }
  .btn-submit i,
  .btn-submit svg,
  .btn-submit .feather,
  .form-actions .btn-submit i,
  .form-actions .btn-submit svg,
  .form-actions .btn-primary i,
  .form-actions .btn-primary svg {
    width: 18px !important;
    height: 18px !important;
    stroke-width: 2 !important;
    stroke: currentColor !important;
    flex-shrink: 0 !important;
    display: inline-block !important;
  }

  .btn-cancel,
  button.btn-cancel,
  a.btn-cancel,
  .form-actions .btn-cancel,
  .form-actions .btn-secondary {
    height: 46px !important;
    padding: 0 20px !important;
    background-color: var(--color-surface, #f8faf7) !important;
    border: 1.5px solid var(--color-border, #cfdcc8) !important;
    color: var(--color-text, #16251b) !important;
    border-radius: var(--radius-full, 9999px) !important;
    font-weight: 500 !important;
    font-size: 14px !important;
    cursor: pointer !important;
    transition: all 0.2s ease !important;
    display: inline-flex !important;
    align-items: center !important;
    justify-content: center !important;
    gap: 8px !important;
    box-shadow: none !important;
    text-decoration: none !important;
    box-sizing: border-box !important;
  }
  .btn-cancel:hover,
  button.btn-cancel:hover,
  a.btn-cancel:hover,
  .form-actions .btn-cancel:hover,
  .form-actions .btn-secondary:hover {
    background-color: var(--color-row-hover, #eff5ed) !important;
    border-color: var(--color-border, #cfdcc8) !important;
    color: var(--color-text, #16251b) !important;
  }
  .btn-cancel i,
  .btn-cancel svg,
  .btn-cancel .feather,
  .form-actions .btn-cancel i,
  .form-actions .btn-cancel svg,
  .form-actions .btn-secondary i,
  .form-actions .btn-secondary svg {
    width: 16px !important;
    height: 16px !important;
    stroke-width: 2 !important;
    stroke: currentColor !important;
    flex-shrink: 0 !important;
    display: inline-block !important;
  }

  /* ==========================================================================
     GLOBAL RESPONSIVE FORM ACTIONS (Create & Edit Mobile Centered Buttons)
     ========================================================================== */
  @media (max-width: 768px) {
    .form-actions,
    form .form-actions,
    .card .form-actions,
    .card-body .form-actions,
    .card-body > .form-actions,
    div.form-actions {
      display: flex !important;
      justify-content: center !important;
      align-items: center !important;
      text-align: center !important;
      flex-wrap: wrap !important;
      gap: 12px !important;
      width: 100% !important;
      margin-left: auto !important;
      margin-right: auto !important;
    }
  }

  .file-upload-area {
    background-color: var(--color-surface, #f8faf7) !important;
    border: 2px dashed var(--color-border, #cfdcc8) !important;
  }
  .file-upload-area:hover,
  .file-upload-area.has-file {
    background-color: var(--color-row-hover, #eff5ed) !important;
    border-color: var(--color-primary, #1f3a2a) !important;
  }

  /* Upload Icon Standar Sistem (Clean upload-cloud Icon Polos, Dilarang Card/Badge/Background Box) */
  .file-upload-icon {
    background: transparent !important;
    width: auto !important;
    height: auto !important;
    border-radius: 0 !important;
    box-shadow: none !important;
    margin: 0 auto 8px auto !important;
    display: block !important;
  }
  .file-upload-icon i,
  .file-upload-icon svg {
    width: 28px !important;
    height: 28px !important;
    stroke-width: 1.5 !important;
    color: var(--color-text-muted, #546958) !important;
    stroke: var(--color-text-muted, #546958) !important;
  }

  /* Digital Signature Canvas & Draw Wrapper (Anti Pure-White / Sesuai Warna Sistem SIMA) */
  .signature-draw-wrapper {
    background-color: var(--color-surface, #f8faf7) !important;
    border: 1.5px solid var(--color-border, #cfdcc8) !important;
    border-radius: 10px !important;
    transition: border-color 0.2s ease, box-shadow 0.2s ease !important;
  }
  .signature-draw-wrapper:hover,
  .signature-draw-wrapper:focus-within {
    border-color: var(--color-primary, #1f3a2a) !important;
  }
  .signature-canvas {
    background-color: var(--color-surface, #f8faf7) !important;
  }

  .asal-usul-option label {
    background-color: var(--color-surface, #f8faf7) !important;
    border: 1.5px solid var(--color-border, #cfdcc8) !important;
    color: var(--color-text, #16251b) !important;
  }
  .asal-usul-option label:hover {
    background-color: var(--color-row-hover, #eff5ed) !important;
    border-color: var(--color-primary, #1f3a2a) !important;
  }

  /* Global Input Error State & Feedback (Dribbble Slick UX Pattern) */
  .is-invalid,
  .form-control.is-invalid,
  .form-select.is-invalid,
  input.is-invalid,
  textarea.is-invalid,
  select.is-invalid {
    border-color: #E53935 !important;
    background-color: var(--color-surface, #f8faf7) !important;
    box-shadow: none !important;
    outline: none !important;
  }

  .is-invalid:focus,
  .form-control.is-invalid:focus,
  .form-select.is-invalid:focus {
    border-color: #D32F2F !important;
    box-shadow: none !important;
    outline: none !important;
  }

  .invalid-feedback,
  .field-error-message {
    display: none;
    align-items: flex-start;
    gap: 6px;
    margin-top: 6px;
    font-size: 12.5px;
    font-weight: 500;
    line-height: 1.4;
    color: #D9383A !important;
  }

  .invalid-feedback::before,
  .field-error-message::before {
    content: "";
    display: inline-block;
    width: 14px;
    height: 14px;
    flex-shrink: 0;
    margin-top: 2px;
    background-color: #D9383A;
    -webkit-mask: url("data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' viewBox='0 0 16 16'%3E%3Cpath d='M7.134 1.603a1 1 0 0 1 1.732 0l6.062 10.5A1 1 0 0 1 14.062 13.6H1.938a1 1 0 0 1-.866-1.497l6.062-10.5z'/%3E%3Cpath stroke='%23fff' stroke-width='1.3' stroke-linecap='round' d='M8 5.5v3.2M8 11.2h.01'/%3E%3C/svg%3E") no-repeat center / contain;
    mask: url("data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' viewBox='0 0 16 16'%3E%3Cpath d='M7.134 1.603a1 1 0 0 1 1.732 0l6.062 10.5A1 1 0 0 1 14.062 13.6H1.938a1 1 0 0 1-.866-1.497l6.062-10.5z'/%3E%3Cpath stroke='%23fff' stroke-width='1.3' stroke-linecap='round' d='M8 5.5v3.2M8 11.2h.01'/%3E%3C/svg%3E") no-repeat center / contain;
  }

  /* Anti Double Icon: Nonaktifkan pseudo-element ::before jika sudah ada tag SVG atau class .has-svg-icon */
  .invalid-feedback:has(svg)::before,
  .invalid-feedback:has(i)::before,
  .invalid-feedback:has(.sima-error-icon)::before,
  .invalid-feedback.has-svg-icon::before,
  .field-error-message:has(svg)::before,
  .field-error-message:has(i)::before,
  .field-error-message:has(.sima-error-icon)::before,
  .field-error-message.has-svg-icon::before,
  .invalid-feedback-custom:has(svg)::before,
  .invalid-feedback-custom:has(i)::before,
  .invalid-feedback-custom:has(.sima-error-icon)::before,
  .invalid-feedback-custom.has-svg-icon::before {
    content: none !important;
    display: none !important;
  }

  .is-invalid ~ .invalid-feedback,
  .was-validated :invalid ~ .invalid-feedback,
  .invalid-feedback[style*="display: block"],
  .invalid-feedback[style*="display:block"] {
    display: flex !important;
  }

  /* Sidebar active item clean styling (removes legacy blue 04a9f5 indicators) */
  .pc-navbar > .pc-item::before,
  .pc-navbar > .pc-item::after,
  .pc-navbar > .pc-item.active::before,
  .pc-navbar > .pc-item.active::after,
  .pc-navbar > .pc-item.pc-trigger::before,
  .pc-navbar > .pc-item.pc-trigger::after,
  .pc-sidebar .pc-navbar > .pc-item::before,
  .pc-sidebar .pc-navbar > .pc-item::after,
  .pc-sidebar .pc-navbar > .pc-item.active::before,
  .pc-sidebar .pc-navbar > .pc-item.active::after,
  .pc-sidebar .pc-navbar > .pc-item.pc-trigger::before,
  .pc-sidebar .pc-navbar > .pc-item.pc-trigger::after {
    display: none !important;
    content: none !important;
    width: 0 !important;
    height: 0 !important;
    background-color: transparent !important;
  }

  /* Checkbox & Radio Card Wrapper Labels (Always Vertically Centered) */
  .checkbox-card,
  .toggle-card,
  label:has(> input[type="checkbox"]),
  label:has(> input[type="radio"]) {
    align-items: center !important;
  }

  /* Standardized Checkbox & Radio Controls (SIMA Warm Palette) */
  input[type="checkbox"],
  .form-check-input[type="checkbox"],
  .sima-checkbox {
    appearance: none !important;
    -webkit-appearance: none !important;
    -moz-appearance: none !important;
    width: 18px !important;
    height: 18px !important;
    background-color: var(--color-surface, #f8faf7) !important;
    border: 1.5px solid var(--color-border, #cfdcc8) !important;
    border-radius: 5px !important;
    cursor: pointer !important;
    display: inline-flex !important;
    align-items: center !important;
    justify-content: center !important;
    vertical-align: middle !important;
    margin: 0 !important;
    padding: 0 !important;
    transition: background-color 0.1s ease, border-color 0.1s ease !important;
    box-shadow: none !important;
    outline: none !important;
    flex-shrink: 0 !important;
    position: relative !important;
  }

  input[type="checkbox"]:hover,
  .form-check-input[type="checkbox"]:hover,
  .sima-checkbox:hover {
    border-color: var(--color-primary, #1f3a2a) !important;
    background-color: var(--color-row-hover, #eff5ed) !important;
  }

  input[type="checkbox"]:checked,
  .form-check-input[type="checkbox"]:checked,
  .sima-checkbox:checked {
    background-color: var(--color-primary, #1f3a2a) !important;
    border-color: var(--color-primary, #1f3a2a) !important;
    background-image: url("data:image/svg+xml,%3csvg xmlns='http://www.w3.org/2000/svg' viewBox='0 0 20 20'%3e%3cpath fill='none' stroke='%23ffffff' stroke-linecap='round' stroke-linejoin='round' stroke-width='2.8' d='M4.5 10.5l3.8 3.8L15.5 6'/%3e%3c/svg%3e") !important;
    background-size: 70% 70% !important;
    background-position: center !important;
    background-repeat: no-repeat !important;
  }

  input[type="checkbox"]:focus-visible,
  .form-check-input[type="checkbox"]:focus-visible,
  .sima-checkbox:focus-visible {
    border-color: var(--color-primary, #1f3a2a) !important;
    outline: none !important;
    box-shadow: 0 0 0 2px rgba(31, 58, 42, 0.2) !important;
  }

  input[type="checkbox"]:disabled,
  .form-check-input[type="checkbox"]:disabled,
  .sima-checkbox:disabled {
    opacity: 0.55 !important;
    cursor: not-allowed !important;
    background-color: var(--color-border, #cfdcc8) !important;
  }

  input[type="radio"],
  input[type="radio"]:not(.sr-only):not([hidden]),
  .form-check-input[type="radio"] {
    appearance: none !important;
    -webkit-appearance: none !important;
    -moz-appearance: none !important;
    width: 18px !important;
    height: 18px !important;
    background-color: var(--color-surface, #f8faf7) !important;
    border: 1.5px solid var(--color-border, #cfdcc8) !important;
    border-radius: 50% !important;
    cursor: pointer !important;
    display: inline-flex !important;
    align-items: center !important;
    justify-content: center !important;
    vertical-align: middle !important;
    margin: 0 !important;
    padding: 0 !important;
    transition: background-color 0.1s ease, border-color 0.1s ease !important;
    box-shadow: none !important;
    outline: none !important;
    flex-shrink: 0 !important;
    position: relative !important;
    background-repeat: no-repeat !important;
    background-position: center !important;
  }

  input[type="radio"]:hover,
  input[type="radio"]:hover:not(.sr-only):not([hidden]),
  .form-check-input[type="radio"]:hover {
    border-color: var(--color-primary, #1f3a2a) !important;
    background-color: var(--color-row-hover, #eff5ed) !important;
  }

  input[type="radio"]:checked,
  input[type="radio"]:checked:not(.sr-only):not([hidden]),
  .form-check-input[type="radio"]:checked {
    background-color: var(--color-surface, #f8faf7) !important;
    border-color: var(--color-primary, #1f3a2a) !important;
    border-width: 1.5px !important;
    background-image: url("data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' viewBox='0 0 16 16'%3E%3Ccircle cx='8' cy='8' r='4' fill='%232E4A2D'/%3E%3C/svg%3E") !important;
    background-size: 100% 100% !important;
    background-position: center !important;
    background-repeat: no-repeat !important;
  }

  input[type="radio"]:not(:checked),
  input[type="radio"]:not(:checked):not(.sr-only):not([hidden]),
  .form-check-input[type="radio"]:not(:checked) {
    background-image: none !important;
  }

  input[type="radio"]:focus-visible,
  .form-check-input[type="radio"]:focus-visible {
    border-color: var(--color-primary, #1f3a2a) !important;
    outline: none !important;
    box-shadow: 0 0 0 2px rgba(31, 58, 42, 0.2) !important;
  }

  input[type="radio"]:disabled,
  .form-check-input[type="radio"]:disabled {
    opacity: 0.55 !important;
    cursor: not-allowed !important;
    background-color: var(--color-border, #cfdcc8) !important;
  }

  /* Standardized Info & Hint Box (SIMA Warm Pill/Card Pattern - FULL WIDTH) */
  .info-box,
  .order-hint,
  .info-hint,
  .form-hint-box,
  .sima-info-box,
  .jenis-flow-helper,
  .doc-flow-helper,
  .flow-helper-box {
    background: var(--color-primary-tint, #d5e4d7) !important;
    border: 1px solid var(--color-border, #cfdcc8) !important;
    border-radius: var(--radius-sm, 8px) !important;
    padding: 8px 14px !important;
    display: flex !important;
    width: 100% !important;
    box-sizing: border-box !important;
    align-items: center !important;
    gap: 8px !important;
    color: var(--color-text, #16251b) !important;
    font-size: 12px !important;
    font-weight: 500 !important;
    line-height: 1.45 !important;
    margin-top: 12px !important;
    margin-bottom: 12px !important;
  }

  .info-box i,
  .info-box svg,
  .order-hint i,
  .order-hint svg,
  .info-hint i,
  .info-hint svg,
  .form-hint-box i,
  .form-hint-box svg,
  .sima-info-box i,
  .sima-info-box svg,
  .jenis-flow-helper i,
  .jenis-flow-helper svg,
  .doc-flow-helper i,
  .doc-flow-helper svg,
  .flow-helper-box i,
  .flow-helper-box svg {
    color: var(--color-primary, #1f3a2a) !important;
    stroke: var(--color-primary, #1f3a2a) !important;
    flex-shrink: 0 !important;
    width: 14px !important;
    height: 14px !important;
    display: block !important;
    margin: 0 !important;
  }

  .info-box-text,
  .info-box span,
  .order-hint span,
  .info-hint span,
  .form-hint-box span,
  .sima-info-box span,
  .jenis-flow-helper span,
  .doc-flow-helper span,
  .flow-helper-box span {
    font-size: 12px !important;
    color: var(--color-text, #16251b) !important;
    font-weight: 500 !important;
    line-height: 1.45 !important;
    flex: 1 !important;
  }

  .info-box-text strong,
  .info-box strong,
  .jenis-flow-helper strong,
  .doc-flow-helper strong,
  .sima-info-box strong {
    font-weight: 600 !important;
    color: var(--color-text, #16251b) !important;
  }

  /* Standardized Warning Box (SIMA Warm Amber Pattern - Sesuai Token Status Warning Soft) */
  .warning-box,
  .sima-warning-box {
    background: var(--color-warning-soft, #F4E7C3) !important;
    border: 1px solid #DFCA95 !important;
    border-radius: var(--radius-sm, 8px) !important;
    padding: 8px 14px !important;
    display: flex !important;
    width: 100% !important;
    box-sizing: border-box !important;
    align-items: center !important;
    gap: 8px !important;
    color: #784803 !important;
    margin-bottom: 16px !important;
  }

  .warning-box i,
  .warning-box svg,
  .sima-warning-box i,
  .sima-warning-box svg {
    color: var(--color-warning, #B7791F) !important;
    stroke: var(--color-warning, #B7791F) !important;
    flex-shrink: 0 !important;
    width: 15px !important;
    height: 15px !important;
  }

  .warning-box-text,
  .warning-box span,
  .warning-box div,
  .sima-warning-box-text,
  .sima-warning-box span,
  .sima-warning-box div {
    font-size: 12px !important;
    color: #784803 !important;
    line-height: 1.45 !important;
    font-weight: 500 !important;
    flex: 1 !important;
  }

  .warning-box strong,
  .sima-warning-box strong {
    font-weight: 600 !important;
    color: #573300 !important;
  }

  /* ==========================================================================
     SIMA Standard Alert & Helper Boxes Mobile Responsive (Vertical Centered)
     Icon & Text Align Center to maximize width and prevent vertical text cramming
     ========================================================================== */
  @media (max-width: 768px) {
    .info-box,
    .order-hint,
    .info-hint,
    .info-hint-box,
    .form-hint-box,
    .sima-info-box,
    .jenis-flow-helper,
    .doc-flow-helper,
    .flow-helper-box,
    .warning-box,
    .sima-warning-box {
      flex-direction: column !important;
      align-items: center !important;
      justify-content: center !important;
      text-align: center !important;
      gap: 6px !important;
      padding: 10px 14px !important;
    }

    .info-box i,
    .info-box svg,
    .order-hint i,
    .order-hint svg,
    .info-hint i,
    .info-hint svg,
    .info-hint-box i,
    .info-hint-box svg,
    .form-hint-box i,
    .form-hint-box svg,
    .sima-info-box i,
    .sima-info-box svg,
    .jenis-flow-helper i,
    .jenis-flow-helper svg,
    .doc-flow-helper i,
    .doc-flow-helper svg,
    .flow-helper-box i,
    .flow-helper-box svg,
    .warning-box i,
    .warning-box svg,
    .sima-warning-box i,
    .sima-warning-box svg {
      margin: 0 auto !important;
      align-self: center !important;
      flex-shrink: 0 !important;
      display: inline-block !important;
    }

    .info-box-text,
    .info-box span,
    .info-box div,
    .order-hint span,
    .order-hint div,
    .info-hint span,
    .info-hint div,
    .info-hint-box div,
    .info-hint-box span,
    .form-hint-box span,
    .form-hint-box div,
    .sima-info-box-text,
    .sima-info-box span,
    .sima-info-box div,
    .jenis-flow-helper span,
    .jenis-flow-helper div,
    .doc-flow-helper span,
    .doc-flow-helper div,
    .flow-helper-box span,
    .flow-helper-box div,
    .warning-box-text,
    .warning-box span,
    .warning-box div,
    .sima-warning-box-text,
    .sima-warning-box span,
    .sima-warning-box div {
      text-align: center !important;
      width: 100% !important;
      flex: none !important;
    }
  }

  /* ==========================================================================
     SIMA Universal Scrollbar Styling (Global & All Scrollable UI Components)
     ========================================================================== */
  html,
  body,
  *,
  *::before,
  *::after {
    scrollbar-width: thin !important;
    scrollbar-color: var(--color-border, #cfdcc8) transparent !important;
  }

  ::-webkit-scrollbar,
  *::-webkit-scrollbar {
    width: 6px !important;
    height: 6px !important;
  }

  ::-webkit-scrollbar-track,
  *::-webkit-scrollbar-track {
    background: transparent !important;
  }

  ::-webkit-scrollbar-thumb,
  *::-webkit-scrollbar-thumb {
    background-color: var(--color-border, #cfdcc8) !important;
    border-radius: 9999px !important;
    border: 1px solid transparent !important;
    background-clip: padding-box !important;
    transition: background-color 0.15s ease-in-out !important;
  }

  ::-webkit-scrollbar-thumb:hover,
  *::-webkit-scrollbar-thumb:hover {
    background-color: var(--color-text-muted, #546958) !important;
  }

  ::-webkit-scrollbar-thumb:active,
  *::-webkit-scrollbar-thumb:active {
    background-color: var(--color-primary, #1f3a2a) !important;
  }

  ::-webkit-scrollbar-corner,
  *::-webkit-scrollbar-corner {
    background: transparent !important;
  }

  /* SimpleBar Custom Palette Support (Sidebar, Notifications, Feed, etc.) */
  .simplebar-scrollbar:before {
    background-color: var(--color-border, #cfdcc8) !important;
    border-radius: 9999px !important;
    opacity: 0.85 !important;
  }

  .simplebar-scrollbar.simplebar-visible:before,
  .simplebar-scrollbar:hover:before {
    background-color: var(--color-text-muted, #546958) !important;
    opacity: 1 !important;
  }

  .simplebar-track {
    background: transparent !important;
  }

  .simplebar-track.simplebar-vertical {
    width: 6px !important;
  }

  .simplebar-track.simplebar-horizontal {
    height: 6px !important;
  }

  /* Specific Interactive / Floating Scrollable Containers */
  .pejabat-list,
  .pejabat-items,
  .pejabat-list-container,
  .dropdown-checklist-list,
  .dropdown-options-list,
  .dropdown-menu,
  .filter-dropdown-menu,
  .ss-options,
  .select2-results__options,
  .table-responsive,
  .dataTables_scrollBody,
  .overflow-y-auto,
  .overflow-x-auto,
  .modal-body,
  .offcanvas-body,
  .navbar-content,
  .scroll-div,
  textarea,
  pre,
  code {
    scrollbar-width: thin !important;
    scrollbar-color: var(--color-border, #cfdcc8) transparent !important;
  }

  .pejabat-list::-webkit-scrollbar,
  .pejabat-items::-webkit-scrollbar,
  .pejabat-list-container::-webkit-scrollbar,
  .dropdown-checklist-list::-webkit-scrollbar,
  .dropdown-options-list::-webkit-scrollbar,
  .dropdown-menu::-webkit-scrollbar,
  .filter-dropdown-menu::-webkit-scrollbar,
  .ss-options::-webkit-scrollbar,
  .select2-results__options::-webkit-scrollbar,
  .table-responsive::-webkit-scrollbar,
  .dataTables_scrollBody::-webkit-scrollbar,
  .overflow-y-auto::-webkit-scrollbar,
  .overflow-x-auto::-webkit-scrollbar,
  .modal-body::-webkit-scrollbar,
  .offcanvas-body::-webkit-scrollbar,
  .navbar-content::-webkit-scrollbar,
  .scroll-div::-webkit-scrollbar,
  textarea::-webkit-scrollbar,
  pre::-webkit-scrollbar,
  code::-webkit-scrollbar {
    width: 5px !important;
    height: 5px !important;
  }

  .pejabat-list::-webkit-scrollbar-track,
  .pejabat-items::-webkit-scrollbar-track,
  .pejabat-list-container::-webkit-scrollbar-track,
  .dropdown-checklist-list::-webkit-scrollbar-track,
  .dropdown-options-list::-webkit-scrollbar-track,
  .dropdown-menu::-webkit-scrollbar-track,
  .filter-dropdown-menu::-webkit-scrollbar-track,
  .ss-options::-webkit-scrollbar-track,
  .select2-results__options::-webkit-scrollbar-track,
  .table-responsive::-webkit-scrollbar-track,
  .dataTables_scrollBody::-webkit-scrollbar-track,
  .overflow-y-auto::-webkit-scrollbar-track,
  .overflow-x-auto::-webkit-scrollbar-track,
  .modal-body::-webkit-scrollbar-track,
  .offcanvas-body::-webkit-scrollbar-track,
  .navbar-content::-webkit-scrollbar-track,
  .scroll-div::-webkit-scrollbar-track,
  textarea::-webkit-scrollbar-track,
  pre::-webkit-scrollbar-track,
  code::-webkit-scrollbar-track {
    background: transparent !important;
  }

  .pejabat-list::-webkit-scrollbar-thumb,
  .pejabat-items::-webkit-scrollbar-thumb,
  .pejabat-list-container::-webkit-scrollbar-thumb,
  .dropdown-checklist-list::-webkit-scrollbar-thumb,
  .dropdown-options-list::-webkit-scrollbar-thumb,
  .dropdown-menu::-webkit-scrollbar-thumb,
  .filter-dropdown-menu::-webkit-scrollbar-thumb,
  .ss-options::-webkit-scrollbar-thumb,
  .select2-results__options::-webkit-scrollbar-thumb,
  .table-responsive::-webkit-scrollbar-thumb,
  .dataTables_scrollBody::-webkit-scrollbar-thumb,
  .overflow-y-auto::-webkit-scrollbar-thumb,
  .overflow-x-auto::-webkit-scrollbar-thumb,
  .modal-body::-webkit-scrollbar-thumb,
  .offcanvas-body::-webkit-scrollbar-thumb,
  .navbar-content::-webkit-scrollbar-thumb,
  .scroll-div::-webkit-scrollbar-thumb,
  textarea::-webkit-scrollbar-thumb,
  pre::-webkit-scrollbar-thumb,
  code::-webkit-scrollbar-thumb {
    background-color: var(--color-border, #cfdcc8) !important;
    border-radius: 9999px !important;
  }

  .pejabat-list::-webkit-scrollbar-thumb:hover,
  .pejabat-items::-webkit-scrollbar-thumb:hover,
  .pejabat-list-container::-webkit-scrollbar-thumb:hover,
  .dropdown-checklist-list::-webkit-scrollbar-thumb:hover,
  .dropdown-options-list::-webkit-scrollbar-thumb:hover,
  .dropdown-menu::-webkit-scrollbar-thumb:hover,
  .filter-dropdown-menu::-webkit-scrollbar-thumb:hover,
  .ss-options::-webkit-scrollbar-thumb:hover,
  .select2-results__options::-webkit-scrollbar-thumb:hover,
  .table-responsive::-webkit-scrollbar-thumb:hover,
  .dataTables_scrollBody::-webkit-scrollbar-thumb:hover,
  .overflow-y-auto::-webkit-scrollbar-thumb:hover,
  .overflow-x-auto::-webkit-scrollbar-thumb:hover,
  .modal-body::-webkit-scrollbar-thumb:hover,
  .offcanvas-body::-webkit-scrollbar-thumb:hover,
  .navbar-content::-webkit-scrollbar-thumb:hover,
  .scroll-div::-webkit-scrollbar-thumb:hover,
  textarea::-webkit-scrollbar-thumb:hover,
  pre::-webkit-scrollbar-thumb:hover,
  code::-webkit-scrollbar-thumb:hover {
    background-color: var(--color-text-muted, #546958) !important;
  }
</style>


