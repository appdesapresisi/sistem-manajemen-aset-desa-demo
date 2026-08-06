<!doctype html>
<html lang="en" data-pc-preset="{{ $pc_preset_theme }}" data-pc-theme="{{ $pc_dark_layout }}">
  <!-- [Head] start -->

  <head>
    @include('layouts.head-page-meta', ['title' => 'Home - Sistem Manajemen Aset Desa Demo'])
    @include('layouts.head-css')
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/flatpickr/dist/flatpickr.min.css">
    <style>
      /* Premium Dashboard Styles */
      .dashboard-header {
        background: linear-gradient(135deg, #064635 0%, #1c5240 50%, #1a2535 100%);
        border-radius: 20px;
        padding: 28px 32px;
        margin-bottom: 28px;
        position: relative;
        overflow: hidden;
      }
      .dashboard-header::before {
        content: '';
        position: absolute;
        top: -50%;
        right: -20%;
        width: 400px;
        height: 400px;
        background: radial-gradient(circle, rgba(255,255,255,0.08) 0%, transparent 70%);
        animation: pulse-slow 8s ease-in-out infinite;
      }
      .dashboard-header::after {
        content: '';
        position: absolute;
        bottom: -60%;
        left: -10%;
        width: 300px;
        height: 300px;
        background: radial-gradient(circle, rgba(99, 102, 241, 0.15) 0%, transparent 70%);
        animation: pulse-slow 10s ease-in-out infinite reverse;
      }
      @keyframes pulse-slow {
        0%, 100% { transform: scale(1); opacity: 0.5; }
        50% { transform: scale(1.15); opacity: 0.8; }
      }
      .dashboard-title {
        position: relative;
        z-index: 10;
      }
      .dashboard-title h1 {
        font-size: 26px;
        font-weight: 700;
        color: #fff;
        margin-bottom: 6px;
      }
      .dashboard-title p {
        font-size: 14px;
        color: rgba(255,255,255,0.75);
        margin: 0;
      }

      /* Premium KPI Cards */
      .kpi-card {
        background: #fff;
        border-radius: 16px;
        padding: 16px 20px;
        border: 1px solid rgba(0,0,0,0.04);
        box-shadow: 0 4px 20px rgba(0,0,0,0.04);
        transition: all 0.3s ease;
        position: relative;
        overflow: hidden;
      }
      .kpi-card:hover {
        transform: translateY(-4px);
        box-shadow: 0 12px 30px rgba(0,0,0,0.08);
      }
      .kpi-card-icon {
        width: 52px;
        height: 52px;
        border-radius: 14px;
        display: flex !important;
        align-items: center !important;
        justify-content: center !important;
        position: relative !important;
        isolation: isolate;
        padding: 0 !important;
        flex-shrink: 0;
      }

      .kpi-card-icon svg,
      .kpi-card-icon i {
        /* RESET POSISI: Menghancurkan aturan 'absolute' dari template */
        position: relative !important; 
        top: unset !important;
        left: unset !important;
        right: unset !important;
        bottom: unset !important;

        /* TRICK TERBAIK: Force center di dalam flexbox */
        margin: auto !important; 
        
        /* Pastikan ukuran tetap sesuai keinginan Anda */
        width: 24px !important;
        height: 24px !important;
        flex-shrink: 0; /* Mencegah icon penyet */
      }

      .kpi-card-icon::after {
        content: '';
        position: absolute;
        inset: 0;
        border-radius: 14px;
        background: #fff;
        opacity: 0.15;
        transform: scale(1.4);
        z-index: -1;
        display: flex;
        justify-content: center;
        align-items: center;
      }
      .kpi-card-label {
        font-size: 12px;
        font-weight: 500;
        color: #064635;
        text-transform: uppercase;
        letter-spacing: 0.5px;
        margin-bottom: 4px;
      }
      .kpi-card-value {
        font-size: 16px;
        font-weight: 700;
        color: #1f2937;
        line-height: 1.2;
      }
      .kpi-card-trend {
        display: inline-flex;
        align-items: center;
        gap: 4px;
        font-size: 12px;
        font-weight: 600;
        padding: 4px 8px;
        border-radius: 20px;
        margin-top: 8px;
      }
      .kpi-card-trend.up { background: #d1fae5; color: #059669; }
      .kpi-card-trend.down { background: #fee2e2; color: #dc2626; }

      .kpi-trend-wrapper {
        display: flex;
        align-items: center;
        gap: 6px;
        margin-top: 8px;
      }
      .kpi-trend-pill {
        display: inline-flex;
        align-items: center;
        gap: 3px;
        padding: 2px 7px;
        border-radius: 12px;
        font-size: 11px;
        font-weight: 700;
        line-height: 1.3;
      }
      .kpi-trend-pill.up {
        background: #d1fae5;
        color: #047857;
      }
      .kpi-trend-pill.down {
        background: #fee2e2;
        color: #b91c1c;
      }
      .kpi-trend-pill.neutral {
        background: #f1f5f9;
        color: #64748b;
      }
      .kpi-trend-label {
        font-size: 11.5px;
        color: #94a3b8;
        font-weight: 500;
      }

      /* Premium Chart Cards */
      .chart-card {
        background: #fff;
        border-radius: 20px;
        border: 1px solid rgba(0,0,0,0.04);
        box-shadow: 0 4px 20px rgba(0,0,0,0.04);
        overflow: hidden;
      }
      .chart-card-header {
        padding: 20px 24px;
        border-bottom: 1px solid #f3f4f6;
        display: flex;
        align-items: center;
        justify-content: space-between;
      }
      .chart-card-title {
        display: flex;
        align-items: center;
        gap: 12px;
      }
      .chart-card-icon {
        width: 42px;
        height: 42px;
        border-radius: 12px;
        display: flex;
        align-items: center;
        justify-content: center;
      }
      .chart-card-icon svg,
      .chart-card-icon i { 
        width: 20px; 
        height: 20px; 
        color: #fff !important; 
        stroke: #fff !important;
      }
      .chart-card-title h5 { 
        margin: 0; 
        font-size: 16px; 
        font-weight: 600; 
        color: #064635; 
      }
      .chart-card-title span { 
        font-size: 13px; 
        color: #6b7280; 
      
      }
      .chart-card-body { padding: 20px 24px; }

      .chart-export-row {
        padding: 10px 20px;
        border-bottom: 1px solid #f3f4f6;
        background: #fff;
        display: flex;
        align-items: center;
        justify-content: flex-start;
        gap: 10px;
        flex-wrap: wrap;
      }
      .chart-actions-left {
        display: flex;
        align-items: center;
        gap: 8px;
        flex-wrap: wrap;
      }
      .btn-chart-export {
        display: inline-flex;
        align-items: center;
        gap: 6px;
        padding: 6px 12px;
        border: none;
        border-radius: 8px;
        background: linear-gradient(135deg, #519259 0%, #064635 100%);
        color: #ffffff;
        font-size: 12px;
        font-weight: 600;
        cursor: pointer;
        transition: all 0.15s;
      }
      .btn-chart-export:hover {
        background: linear-gradient(135deg, #519259 0%, #064635 100%);
        filter: brightness(0.92);
        color: #ffffff;
      }
      .btn-chart-export i,
      .btn-chart-export svg {
        width: 13px;
        height: 13px;
      }
      .btn-chart-filter {
        display: inline-flex;
        align-items: center;
        gap: 6px;
        padding: 6px 12px;
        border: 1px solid #d1d5db;
        border-radius: 8px;
        background: #ffffff;
        color: #4b5563;
        font-size: 12px;
        font-weight: 600;
        cursor: pointer;
        transition: all 0.15s;
      }
      .btn-chart-filter:hover {
        border-color: #519259;
        color: #519259;
        background: #f4faf5;
      }
      .btn-chart-filter.active {
        border-color: #519259 !important;
        color: #519259 !important;
        background: #f4faf5 !important;
      }
      .btn-chart-filter:hover i,
      .btn-chart-filter:hover svg,
      .btn-chart-filter.active i,
      .btn-chart-filter.active svg {
        color: #519259 !important;
        stroke: #519259 !important;
      }
      .btn-chart-filter i,
      .btn-chart-filter svg {
        width: 13px;
        height: 13px;
      }
      .chart-filter-panel {
        display: none;
        padding: 10px 20px 12px;
        border-bottom: 1px solid #f3f4f6;
        background: #f4faf5;
      }
      .chart-filter-panel.open {
        display: block;
      }

      /* Chart Filter Row */
      .chart-filter-row {
        display: flex;
        gap: 8px;
        padding: 0;
        flex-wrap: wrap;
        align-items: center;
      }

      /* Year Picker Input di Chart Filter */
      .chart-year-picker-wrapper {
        position: relative;
        display: inline-flex;
        align-items: center;
      }
      .chart-year-picker {
        font-size: 11px !important;
        padding: 5px 28px 5px 10px !important;
        border: 1px solid #e5e7eb !important;
        border-radius: 6px !important;
        background: #fff !important;
        color: #374151 !important;
        cursor: pointer !important;
        outline: none !important;
        font-family: inherit !important;
        width: 115px !important;
        transition: border-color 0.15s !important;
      }
      .chart-year-picker:hover, .chart-year-picker:focus {
        border-color: #6366f1 !important;
      }
      .chart-year-picker::placeholder {
        color: #374151 !important;
        opacity: 1 !important;
      }
      .chart-year-picker-clear {
        position: absolute;
        right: 6px;
        top: 50%;
        transform: translateY(-50%);
        width: 16px;
        height: 16px;
        border: none;
        background: none;
        cursor: pointer;
        color: #9ca3af;
        font-size: 14px;
        line-height: 1;
        padding: 0;
        display: none;
        align-items: center;
        justify-content: center;
        border-radius: 3px;
        transition: color 0.15s;
      }
      .chart-year-picker-clear:hover { color: #ef4444; }
      .chart-year-picker-clear.visible { display: flex; }

      /* Flatpickr Year-Only Popup */
      .flatpickr-calendar.year-only-picker {
        width: 236px;
        min-width: 236px;
      }
      .flatpickr-calendar.year-only-picker .flatpickr-innerContainer,
      .flatpickr-calendar.year-only-picker .flatpickr-weekdays,
      .flatpickr-calendar.year-only-picker .flatpickr-monthDropdown-months,
      .flatpickr-calendar.year-only-picker .flatpickr-prev-month,
      .flatpickr-calendar.year-only-picker .flatpickr-next-month {
        display: none !important;
      }
      .flatpickr-calendar.year-only-picker .flatpickr-months {
        display: none !important;
      }
      .flatpickr-calendar.year-only-picker .year-picker-panel {
        padding: 10px;
      }
      .flatpickr-calendar.year-only-picker .year-picker-header {
        display: flex;
        align-items: center;
        justify-content: space-between;
        margin-bottom: 10px;
      }
      .flatpickr-calendar.year-only-picker .year-picker-range {
        font-size: 12px;
        font-weight: 700;
        color: #1f2937;
      }
      .flatpickr-calendar.year-only-picker .year-picker-nav {
        width: 26px;
        height: 26px;
        border-radius: 7px;
        border: 1px solid #d1d5db;
        background: #fff;
        color: #374151;
        font-size: 16px;
        line-height: 1;
        display: inline-flex;
        align-items: center;
        justify-content: center;
        cursor: pointer;
      }
      .flatpickr-calendar.year-only-picker .year-picker-nav:hover:not(:disabled) {
        background: #f3f4f6;
      }
      .flatpickr-calendar.year-only-picker .year-picker-nav:disabled {
        opacity: .45;
        cursor: not-allowed;
      }
      .flatpickr-calendar.year-only-picker .year-picker-grid {
        display: grid;
        grid-template-columns: repeat(3, minmax(0, 1fr));
        gap: 8px;
      }
      .flatpickr-calendar.year-only-picker .year-picker-option {
        border: 1px solid #d1d5db;
        border-radius: 8px;
        background: #fff;
        color: #374151;
        font-size: 12px;
        font-weight: 600;
        padding: 8px 0;
        cursor: pointer;
        text-align: center;
        width: 100%;
      }
      .flatpickr-calendar.year-only-picker .year-picker-option:hover {
        background: #f3f4f6;
      }
      .flatpickr-calendar.year-only-picker .year-picker-option.active {
        background: #1f2937;
        border-color: #1f2937;
        color: #fff;
      }
      .chart-filter-select {
        font-size: 11px;
        padding: 5px 10px;
        border: 1px solid #e5e7eb;
        border-radius: 6px;
        background: #fff;
        color: #374151;
        cursor: pointer;
        outline: none;
        font-family: inherit;
        transition: border-color 0.15s;
      }
      .chart-filter-select:hover, .chart-filter-select:focus {
        border-color: #6366f1;
      }
      /* Status Text (no badge background) */
      .status-pill {
        display: inline-flex;
        align-items: center;
        gap: 6px;
        font-size: 13px;
        font-weight: 500;
        color: #064635;
      }
      .status-pill strong {
        font-weight: 700;
        color: inherit;
      }

      /* Dashboard Table Pagination */
      .dash-pagination-bar {
        display: flex;
        align-items: center;
        justify-content: space-between;
        padding: 14px 8px;
        border-top: 1px solid #f3f4f6;
        gap: 12px;
        flex-wrap: wrap;
      }
      .dash-pagination-info {
        font-size: 12px;
        color: #6b7280;
      }
      .dash-pagination-info strong {
        color: #064635;
        font-weight: 600;
      }
      .dash-pagination-nav {
        display: flex;
        align-items: center;
        gap: 4px;
      }
      .dash-page-btn {
        min-width: 30px;
        height: 30px;
        padding: 0 8px;
        border: none;
        border-radius: 8px;
        background: #f3f4f6;
        color: #6b7280;
        font-size: 12px;
        font-weight: 500;
        display: flex;
        align-items: center;
        justify-content: center;
        cursor: pointer;
        transition: all 0.2s;
      }
      .dash-page-btn:hover { background: #e5e7eb; color: #374151; }
      .dash-page-btn.active {
        background: linear-gradient(135deg, #064635 0%, #1c5240 100%);
        color: #fff;
      }
      .dash-page-btn.disabled { opacity: 0.4; cursor: not-allowed; pointer-events: none; }
      .dash-page-btn svg, .dash-page-btn i { width: 14px; height: 14px; }
      @media (max-width: 575px) {
        .dash-pagination-bar { flex-direction: column; align-items: center; gap: 8px; padding: 12px 8px; }
      }

      /* Info Panel */
      .info-panel {
        background: #fff;
        border-radius: 20px;
        border: 1px solid rgba(0,0,0,0.04);
        box-shadow: 0 4px 20px rgba(0,0,0,0.04);
        overflow: hidden;
      }
      .info-panel-header {
        padding: 18px 24px;
        border-bottom: 1px solid #f3f4f6;
        display: flex;
        align-items: center;
        gap: 12px;
      }

      /* Responsive: header panel dengan total di pojok kanan → pindah ke bawah di mobile */
      @media (max-width: 575px) {
        .info-panel-header-with-total {
          flex-wrap: wrap !important;
        }
        .info-panel-header-with-total .info-panel-icon {
          flex-shrink: 0 !important;
          width: 40px !important;
          height: 40px !important;
          min-width: 40px !important;
        }
        .info-panel-header-with-total .info-panel-icon svg,
        .info-panel-header-with-total .info-panel-icon i {
          width: 18px !important;
          height: 18px !important;
        }
        .info-panel-header-total {
          width: 100% !important;
          text-align: left !important;
          padding-left: 0 !important;
          padding-top: 8px !important;
          border-top: 1px solid #f3f4f6;
          margin-top: 4px;
        }
        .info-panel-header-total p {
          margin-bottom: 2px !important;
        }
        .info-panel-header-total h5 {
          font-size: 16px !important;
        }
      }
      .info-panel-icon {
        width: 40px;
        height: 40px;
        border-radius: 12px;
        display: flex;
        align-items: center;
        justify-content: center;
      }

      .info-panel-icon svg,
      .info-panel-icon i { width: 18px; height: 18px; color: #fff !important; 
        stroke: #fff !important;
      }
      .info-panel-title { 
        font-size: 15px; font-weight: 600; color: #064635; margin: 0; 
      }
      .info-panel-body { padding: 20px 24px; 
      }

      /* Activity Timeline */
      .activity-timeline { list-style: none; padding: 0; margin: 0; }
      .activity-item {
        display: flex;
        gap: 16px;
        padding: 14px 0;
        border-bottom: 1px solid #f3f4f6;
        transition: all 0.2s;
      }
      .activity-item:last-child { border-bottom: none; }
      .activity-item:hover { background: #fafafa; margin: 0 -24px; padding-left: 24px; padding-right: 24px; }
      .activity-dot {
        width: 10px;
        height: 10px;
        border-radius: 50%;
        margin-top: 5px;
        flex-shrink: 0;
      }
      .activity-content { flex: 1; }
      .activity-text { font-size: 14px; color: #374151; margin-bottom: 2px; }
      .activity-time { font-size: 12px; color: #9ca3af; }

      /* Pending Table */
      .pending-table { width: 100%; border-collapse: collapse; }
      .pending-table th { 
        font-size: 11px; 
        font-weight: 600; 
        color: #064635; 
        text-transform: uppercase; 
        letter-spacing: 0.5px; 
        padding: 10px 8px; 
        background: #f4faf5;
        border-bottom: none; 
      }
      .pending-table td { 
        padding: 12px 8px; 
        font-size: 13px; 
        color: #374151; 
        border-bottom: 1px solid #f3f4f6; 
      }
      .pending-table tr:nth-child(even) td { background-color: #f4faf5; }
      .pending-table tr:hover td { background: #e6efe9; }
      .pending-row-clickable { cursor: pointer; }
      .pending-row-clickable:hover td { background: #e6efe9 !important; color: inherit; }
    </style>
  </head>
  <!-- [Head] end -->
  <!-- [Body] Start -->

  <body>
    @include('layouts.notification')
    @include('layouts.layout-vertical')
    {{-- Sembunyikan loader segera saat halaman selesai dimuat --}}
    <script>
      // Sembunyikan loader segera (dashboard tidak ada form submit, loader tidak auto-hidden)
      if (document.readyState === 'loading') {
        document.addEventListener('DOMContentLoaded', function() {
          if (typeof AppLoader !== 'undefined') AppLoader.hide();
        });
      } else {
        if (typeof AppLoader !== 'undefined') AppLoader.hide();
      }
      // Fallback window.load
      window.addEventListener('load', function() {
        if (typeof AppLoader !== 'undefined') AppLoader.hide();
      });
    </script>

    <!-- [ Main Content ] start -->
    <div class="pc-container">
      <div class="pc-content">
        @include('layouts.breadcrumb', [
            'breadcrumbItemHome' => 'Navigasi', 
            'breadcrumbItem' => 'Home', 
            'breadcrumbItemActive' => 'Dashboard'
        ])
        
        <!-- Dashboard Header -->
        <div class="dashboard-header">
          <div class="dashboard-title">
            <h1>Sistem Manajemen Aset Desa Demo</h1>
            <p>Selamat datang! Pantau dan kelola inventaris aset desa dengan mudah.</p>
          </div>
        </div>

        <!-- SECTION 1: Premium KPI Cards -->
        <div class="grid grid-cols-12 gap-5 mb-6">
          <!-- Total Aset -->
          <div class="col-span-6 md:col-span-3">
            <div class="kpi-card">
              <div class="kpi-card-label">Total Aset</div>
              <div class="kpi-card-value" id="kpi-total-assets">{{ number_format($kpis['totalAssets'] ?? 0, 0, ',', '.') }}</div>
              <div class="kpi-trend-wrapper">
                @php $tAssets = $kpis['trends']['assets']['percent'] ?? 0; @endphp
                @if($tAssets > 0)
                  <span class="kpi-trend-pill up">
                    <svg width="10" height="10" viewBox="0 0 24 24" fill="currentColor"><path d="M12 4l-8 8h16z"/></svg>
                    +{{ $tAssets }}%
                  </span>
                @elseif($tAssets < 0)
                  <span class="kpi-trend-pill down">
                    <svg width="10" height="10" viewBox="0 0 24 24" fill="currentColor"><path d="M12 20l8-8H4z"/></svg>
                    {{ $tAssets }}%
                  </span>
                @else
                  <span class="kpi-trend-pill neutral">0%</span>
                @endif
                <span class="kpi-trend-label">vs. kemarin</span>
              </div>
            </div>
          </div>
          <!-- Total Nilai -->
          <div class="col-span-6 md:col-span-3">
            <div class="kpi-card">
              <div class="kpi-card-label">Total Nilai</div>
              <div class="kpi-card-value" id="kpi-total-value">{{ $kpis['totalValueFormatted'] ?? 'Rp 0' }}</div>
              <div class="kpi-trend-wrapper">
                @php $tValue = $kpis['trends']['value']['percent'] ?? 0; @endphp
                @if($tValue > 0)
                  <span class="kpi-trend-pill up">
                    <svg width="10" height="10" viewBox="0 0 24 24" fill="currentColor"><path d="M12 4l-8 8h16z"/></svg>
                    +{{ $tValue }}%
                  </span>
                @elseif($tValue < 0)
                  <span class="kpi-trend-pill down">
                    <svg width="10" height="10" viewBox="0 0 24 24" fill="currentColor"><path d="M12 20l8-8H4z"/></svg>
                    {{ $tValue }}%
                  </span>
                @else
                  <span class="kpi-trend-pill neutral">0%</span>
                @endif
                <span class="kpi-trend-label">vs. kemarin</span>
              </div>
            </div>
          </div>
          <!-- Total Pejabat -->
          <div class="col-span-6 md:col-span-3">
            <div class="kpi-card">
              <div class="kpi-card-label">Total Pejabat</div>
              <div class="kpi-card-value" id="kpi-pejabat">{{ number_format($kpis['pejabatActive'] ?? 0, 0, ',', '.') }}</div>
              <div class="kpi-trend-wrapper">
                @php $tPejabat = $kpis['trends']['pejabat']['percent'] ?? 0; @endphp
                @if($tPejabat > 0)
                  <span class="kpi-trend-pill up">
                    <svg width="10" height="10" viewBox="0 0 24 24" fill="currentColor"><path d="M12 4l-8 8h16z"/></svg>
                    +{{ $tPejabat }}%
                  </span>
                @elseif($tPejabat < 0)
                  <span class="kpi-trend-pill down">
                    <svg width="10" height="10" viewBox="0 0 24 24" fill="currentColor"><path d="M12 20l8-8H4z"/></svg>
                    {{ $tPejabat }}%
                  </span>
                @else
                  <span class="kpi-trend-pill neutral">0%</span>
                @endif
                <span class="kpi-trend-label">vs. kemarin</span>
              </div>
            </div>
          </div>
          <!-- Admin Aktif -->
          <div class="col-span-6 md:col-span-3">
            <div class="kpi-card">
              <div class="kpi-card-label">Total Users</div>
              <div class="kpi-card-value" id="kpi-users">{{ number_format($kpis['usersActive'] ?? 0, 0, ',', '.') }}</div>
              <div class="kpi-trend-wrapper">
                @php $tUsers = $kpis['trends']['users']['percent'] ?? 0; @endphp
                @if($tUsers > 0)
                  <span class="kpi-trend-pill up">
                    <svg width="10" height="10" viewBox="0 0 24 24" fill="currentColor"><path d="M12 4l-8 8h16z"/></svg>
                    +{{ $tUsers }}%
                  </span>
                @elseif($tUsers < 0)
                  <span class="kpi-trend-pill down">
                    <svg width="10" height="10" viewBox="0 0 24 24" fill="currentColor"><path d="M12 20l8-8H4z"/></svg>
                    {{ $tUsers }}%
                  </span>
                @else
                  <span class="kpi-trend-pill neutral">0%</span>
                @endif
                <span class="kpi-trend-label">vs. kemarin</span>
              </div>
            </div>
          </div>
        </div>

        <!-- SECTION 2: Kondisi Aset Status Bar -->
        <div class="mb-6">
          <div class="kpi-card">
            <div style="display:flex; align-items:center; justify-content:space-between; flex-wrap:wrap; gap:12px;">
              <div>
                <h5 style="font-size:14px; font-weight:600; color:#064635; margin:0 0 2px;">Kondisi Aset</h5>
                <div style="font-size:12px; color:#9ca3af;">Ringkasan status kondisi seluruh aset</div>
              </div>
              <div style="display:flex; align-items:center; gap:20px; flex-wrap:wrap;">
                <div class="status-pill baik" style="font-size:13px; color:#374151;">
                  Baik: <strong id="kpi-kondisi-baik" style="color:#059669; font-weight:700;">—</strong>
                </div>
                <div class="status-pill rusak-ringan" style="font-size:13px; color:#374151;">
                  Rusak Ringan: <strong id="kpi-kondisi-rr" style="color:#d97706; font-weight:700;">—</strong>
                </div>
                <div class="status-pill rusak-berat" style="font-size:13px; color:#374151;">
                  Rusak Berat: <strong id="kpi-kondisi-rb" style="color:#dc2626; font-weight:700;">—</strong>
                </div>
              </div>
            </div>
          </div>
        </div>

        <!-- SECTION 3: Charts Row -->
        <div class="grid grid-cols-12 gap-5 mb-6">
          <!-- Bar Chart -->
          <div class="col-span-12 xl:col-span-8">
            <div class="chart-card h-full">
              <div class="chart-card-header">
                <div class="chart-card-title">
                  <div>
                    <h5>Distribusi Nilai Aset per Jenis</h5>
                    <span>Total nilai aset berdasarkan kategori</span>
                  </div>
                </div>
              </div>
              <div class="chart-export-row">
                <div class="chart-actions-left">
                  <button type="button" class="btn-chart-filter" data-panel-target="panel-bar-filters">
                    <i data-feather="sliders"></i>
                    Filter
                  </button>
                  <button type="button" class="btn-chart-export" id="btn-export-chart-bar">
                    <i data-feather="download"></i>
                    Export PDF Chart
                  </button>
                </div>
              </div>
              <div class="chart-filter-panel" id="panel-bar-filters">
                <div class="chart-filter-row">
                  <select class="chart-filter-select" id="filter-bar-metric">
                    <option value="nilai">Nilai Perolehan</option>
                    <option value="jumlah">Jumlah Unit</option>
                  </select>
                  <div class="chart-year-picker-wrapper">
                    <input type="text"
                           class="chart-filter-select chart-year-picker"
                           id="filter-bar-tahun"
                           readonly
                           placeholder="Semua Tahun"
                           data-min-year="2000"
                           data-max-year="{{ now()->year }}" />
                    <button type="button" class="chart-year-picker-clear" id="clear-bar-tahun" title="Hapus filter tahun">&times;</button>
                  </div>
                  <select class="chart-filter-select" id="filter-bar-kondisi">
                    <option value="">Semua Kondisi</option>
                    <option value="Baik">Baik</option>
                    <option value="Rusak Ringan">Rusak Ringan</option>
                    <option value="Rusak Berat">Rusak Berat</option>
                  </select>
                </div>
              </div>
              <div class="chart-card-body" style="position:relative;">
                <canvas id="chart-value-by-type" style="width:100%; height:280px;"></canvas>
                <div id="bar-axis-tooltip" style="
                  display:none;position:absolute;background:rgba(31,41,55,0.95);color:#fff;
                  font-size:12px;font-weight:600;padding:6px 10px;border-radius:6px;
                  pointer-events:none;white-space:nowrap;z-index:10;
                  transform:translateX(-50%);
                "></div>
                <div id="bar-empty-state" style="display:none; flex-direction:column; align-items:center; justify-content:center; text-align:center; padding:80px 20px; color:#9ca3af;">
                  <i data-feather="bar-chart-2" style="display:block;width:40px;height:40px;margin:0 auto 12px;color:#e5e7eb;"></i>
                  <p style="font-size:14px;">Belum ada data nilai aset</p>
                  <p style="font-size:12px; margin-top:4px;">Isi nilai perolehan pada inventaris untuk menampilkan grafik</p>
                </div>
              </div>
            </div>
          </div>
          <!-- Pie Chart -->
          <div class="col-span-12 xl:col-span-4">
            <div class="chart-card h-full">
              <div class="chart-card-header">
                <div class="chart-card-title">
                  <div>
                    <h5>Komposisi Aset</h5>
                    <span>Distribusi jumlah per jenis</span>
                  </div>
                </div>
              </div>
              <div class="chart-export-row">
                <div class="chart-actions-left">
                  <button type="button" class="btn-chart-filter" data-panel-target="panel-donut-filters">
                    <i data-feather="sliders"></i>
                    Filter
                  </button>
                  <button type="button" class="btn-chart-export" id="btn-export-chart-donut">
                    <i data-feather="download"></i>
                    Export PDF Chart
                  </button>
                </div>
              </div>
              <div class="chart-filter-panel" id="panel-donut-filters">
                <div class="chart-filter-row">
                  <select class="chart-filter-select" id="filter-donut-metric">
                    <option value="jumlah">Jumlah Unit</option>
                    <option value="nilai">Total Nilai</option>
                  </select>
                  <div class="chart-year-picker-wrapper">
                    <input type="text"
                           class="chart-filter-select chart-year-picker"
                           id="filter-donut-tahun"
                           readonly
                           placeholder="Semua Tahun"
                           data-min-year="2000"
                           data-max-year="{{ now()->year }}" />
                    <button type="button" class="chart-year-picker-clear" id="clear-donut-tahun" title="Hapus filter tahun">&times;</button>
                  </div>
                  <select class="chart-filter-select" id="filter-donut-kondisi">
                    <option value="">Semua Kondisi</option>
                    <option value="Baik">Baik</option>
                    <option value="Rusak Ringan">Rusak Ringan</option>
                    <option value="Rusak Berat">Rusak Berat</option>
                  </select>
                </div>
              </div>
              <div class="chart-card-body">
                <canvas id="chart-jenis-pie" style="width:100%; height:280px;"></canvas>
                <div id="pie-empty-state" style="display:none; flex-direction:column; align-items:center; justify-content:center; text-align:center; padding:80px 20px; color:#9ca3af; gap:8px;">
                  <i data-feather="pie-chart" style="width:40px;height:40px;opacity:0.5;"></i>
                  <p style="font-size:14px;margin:0;">Belum ada data</p>
                </div>
              </div>
            </div>
          </div>
        </div>

        <!-- ROW 2: Aktivitas Terbaru (left) + Bar Chart Ringkas (right) -->
        <div class="grid grid-cols-12 gap-5 mb-6">
          <!-- Left: Aktivitas Terbaru -->
          <div class="col-span-12 xl:col-span-6">
            <div class="info-panel h-full">
              <div class="info-panel-header">
                <div>
                  <h5 class="info-panel-title">Aktivitas Terbaru</h5>
                  <span style="font-size:12px; color:#9ca3af;">Riwayat aktivitas Aset dan Jenis Inventaris</span>
                </div>
              </div>
              <div class="info-panel-body">
                <ul class="activity-timeline" id="recent-activities"></ul>
              </div>
              <div id="link-all-activities" style="display:none; padding: 8px 24px 16px; border-top: 1px solid #f3f4f6;">
                <a href="{{ route('dashboard.activities') }}" style="font-size:12px; color:#064635; font-weight:600; text-decoration:underline; text-underline-offset:2px;">Lihat Semua Aktivitas &rarr;</a>
              </div>
            </div>
          </div>

          <!-- Right: Ringkasan Jenis Inventaris Table -->
          <div class="col-span-12 xl:col-span-6">
            <div class="info-panel h-full flex flex-col">
              <div class="info-panel-header info-panel-header-with-total" style="display: flex; align-items: center; justify-content: space-between;">
                <div>
                  <h5 class="info-panel-title">Ringkasan Jenis Inventaris</h5>
                  <span style="font-size:12px; color:#9ca3af;">Daftar lengkap jenis aset dan total datanya</span>
                </div>
                <div class="info-panel-header-total" style="text-align: right; padding-left: 20px;">
                  <p style="font-size: 10px; color: #9ca3af; margin: 0 0 2px 0; text-transform: uppercase; letter-spacing: 0.5px;">Total Aset</p>
                  <h5 style="font-size: 18px; font-weight: 700; color: #1f2937; margin: 0;" id="table-total-assets">0</h5>
                </div>
              </div>
              <div class="info-panel-body flex-1">
                <style>
                  .jenis-inventory-table {
                    width: 100%;
                    border-collapse: collapse;
                  }
                  .jenis-inventory-table th {
                    font-size: 11px;
                    font-weight: 600;
                    color: #064635;
                    text-transform: uppercase;
                    letter-spacing: 0.5px;
                    padding: 10px 8px;
                    border-bottom: none;
                    background: #f4faf5;
                  }
                  .jenis-inventory-table th:nth-child(1) {
                    width: 50px;
                    text-align: center;
                  }
                  .jenis-inventory-table th:nth-child(2) {
                    width: 50%;
                    text-align: center;
                  }
                  .jenis-inventory-table th:nth-child(3) {
                    width: 120px;
                    text-align: center;
                  }
                  .jenis-inventory-table td {
                    padding: 12px 8px;
                    font-size: 13px;
                    color: #374151;
                    border-bottom: 1px solid #f3f4f6;
                  }
                  .jenis-inventory-table td:nth-child(1) {
                    text-align: center;
                    width: 50px;
                    font-weight: 600;
                    color: #064635;
                  }
                  .jenis-inventory-table td:nth-child(2) {
                    text-align: left;
                    width: 50%;
                    padding-left: 12px;
                    word-break: break-word;
                  }
                  .jenis-inventory-table td:nth-child(3) {
                    text-align: center;
                    width: 120px;
                    font-weight: 600;
                    color: #064635;
                  }
                  .jenis-inventory-table tr:nth-child(even) td {
                    background-color: #f4faf5;
                  }
                  .jenis-inventory-table tr:hover td {
                    background: #e6efe9;
                  }
                </style>
                <table class="jenis-inventory-table">
                  <thead>
                    <tr>
                      <th>No</th>
                      <th>Nama Inventaris</th>
                      <th>Total Aset</th>
                    </tr>
                  </thead>
                  <tbody id="jenis-inventory-table"></tbody>
                </table>
                <div id="jenis-table-pagination" class="dash-pagination-bar" style="display:none;"></div>
                <div id="jenis-table-empty-state" style="display:none; flex-direction:column; align-items:center; justify-content:center; text-align:center; padding:40px 20px; color:#9ca3af; gap:8px;">
                  <i data-feather="inbox" style="width:36px;height:36px;opacity:0.5;"></i>
                  <p style="font-size:13px;margin:0;">Belum ada data jenis inventaris</p>
                </div>
              </div>
            </div>
          </div>
        </div>

        <!-- ROW 2B: Nilai Perolehan Inventaris (left) + Kondisi Inventaris (right) -->
        <div class="grid grid-cols-12 gap-5 mb-6">
          <!-- Left: Nilai Perolehan Inventaris -->
          <div class="col-span-12 xl:col-span-6">
            <div class="info-panel h-full flex flex-col">
              <div class="info-panel-header info-panel-header-with-total" style="display: flex; align-items: center; justify-content: space-between;">
                <div>
                  <h5 class="info-panel-title">Nilai Perolehan Inventaris</h5>
                  <span style="font-size:12px; color:#9ca3af;">Daftar aset berdasarkan nilai perolehannya</span>
                </div>
                <div class="info-panel-header-total" style="text-align: right; padding-left: 20px;">
                  <p style="font-size: 10px; color: #9ca3af; margin: 0 0 2px 0; text-transform: uppercase; letter-spacing: 0.5px;">Total Perolehan</p>
                  <h5 style="font-size: 18px; font-weight: 700; color: #1f2937; margin: 0;" id="table-total-perolehan">Rp 0</h5>
                </div>
              </div>
              <div class="info-panel-body flex-1">
                <style>
                  .nilai-inventory-table {
                    width: 100%;
                    border-collapse: collapse;
                  }
                  .nilai-inventory-table th {
                    font-size: 11px;
                    font-weight: 600;
                    color: #064635;
                    text-transform: uppercase;
                    letter-spacing: 0.5px;
                    padding: 10px 8px;
                    border-bottom: none;
                    background: #f4faf5;
                  }
                  .nilai-inventory-table th:nth-child(1) {
                    width: 50px;
                    text-align: center;
                  }
                  .nilai-inventory-table th:nth-child(2) {
                    width: 50%;
                    text-align: center;
                  }
                  .nilai-inventory-table th:nth-child(3) {
                    width: auto;
                    text-align: center;
                  }
                  .nilai-inventory-table td {
                    padding: 12px 8px;
                    font-size: 13px;
                    color: #374151;
                    border-bottom: 1px solid #f3f4f6;
                  }
                  .nilai-inventory-table td:nth-child(1) {
                    text-align: center;
                    width: 50px;
                    font-weight: 600;
                    color: #064635;
                  }
                  .nilai-inventory-table td:nth-child(2) {
                    text-align: left;
                    width: 40%;
                    padding-left: 12px;
                    word-break: break-word;
                  }
                  .nilai-inventory-table td:nth-child(3) {
                    text-align: left;
                    width: 60%;
                    padding-left: 12px;
                    font-weight: 600;
                    color: #064635;
                  }
                  .nilai-inventory-table tr:nth-child(even) td {
                    background-color: #f4faf5;
                  }
                  .nilai-inventory-table tr:hover td {
                    background: #e6efe9;
                  }
                </style>
                <table class="nilai-inventory-table">
                  <thead>
                    <tr>
                      <th>No</th>
                      <th>Nama Inventaris</th>
                      <th>Nilai Perolehan</th>
                    </tr>
                  </thead>
                  <tbody id="nilai-inventory-table"></tbody>
                </table>
                <div id="nilai-table-pagination" class="dash-pagination-bar" style="display:none;"></div>
                <div id="nilai-table-empty-state" style="display:none; flex-direction:column; align-items:center; justify-content:center; text-align:center; padding:40px 20px; color:#9ca3af; gap:8px;">
                  <i data-feather="inbox" style="width:36px;height:36px;opacity:0.5;"></i>
                  <p style="font-size:13px;margin:0;">Belum ada data nilai perolehan</p>
                </div>
              </div>
            </div>
          </div>

          <!-- Right: Kondisi Inventaris Detail -->
          <div class="col-span-12 xl:col-span-6">
            <div class="info-panel h-full flex flex-col">
              <div class="info-panel-header">
                <div>
                  <h5 class="info-panel-title">Kondisi Inventaris</h5>
                  <span style="font-size:12px; color:#9ca3af;">Status kondisi aset desa</span>
                </div>
              </div>
              <div class="info-panel-body flex-1">
                <style>
                  .kondisi-inventory-table {
                    width: 100%;
                    border-collapse: collapse;
                  }
                  .kondisi-inventory-table th {
                    font-size: 10px;
                    font-weight: 600;
                    color: #064635;
                    text-transform: uppercase;
                    letter-spacing: 0.5px;
                    padding: 10px 6px;
                    border-bottom: none;
                    background: #f4faf5;
                    text-align: center;
                  }
                  .kondisi-inventory-table th:nth-child(2) {
                    text-align: center;
                  }
                  .kondisi-inventory-table td {
                    padding: 10px 6px;
                    font-size: 12px;
                    color: #374151;
                    border-bottom: 1px solid #f3f4f6;
                    text-align: center;
                  }
                  .kondisi-inventory-table td:nth-child(2) {
                    text-align: left;
                    font-weight: 600;
                    color: #064635;
                  }
                  .kondisi-inventory-table td:nth-child(1) {
                    font-weight: 600;
                    color: #064635;
                  }
                  .kondisi-inventory-table tr:nth-child(even) td {
                    background-color: #f4faf5;
                  }
                  .kondisi-inventory-table tr:hover td {
                    background: #e6efe9;
                  }
                </style>
                <table class="kondisi-inventory-table">
                  <thead>
                    <tr>
                      <th>NO</th>
                      <th>Nama Inventaris</th>
                      <th>Baik</th>
                      <th>Rusak Ringan</th>
                      <th>Rusak Berat</th>
                    </tr>
                  </thead>
                  <tbody id="kondisi-inventory-detail-table"></tbody>
                </table>
                <div id="kondisi-table-pagination" class="dash-pagination-bar" style="display:none;"></div>
                <div id="kondisi-detail-table-empty-state" style="display:none; flex-direction:column; align-items:center; justify-content:center; text-align:center; padding:40px 20px; color:#9ca3af; gap:8px;">
                  <i data-feather="inbox" style="width:36px;height:36px;opacity:0.5;"></i>
                  <p style="font-size:13px;margin:0;">Belum ada data kondisi inventaris</p>
                </div>
              </div>
            </div>
          </div>
        </div>

        <!-- ROW 2C duplicate removed -->

        <!-- ROW 3: Trend Bulanan (left) + Export Terbaru (right) -->
        <div class="grid grid-cols-12 gap-5 mb-6">
          <!-- Left: Trend Bulanan -->
          <div class="col-span-12 xl:col-span-6">
            <div class="chart-card h-full">
              <div class="chart-card-header" style="padding: 16px 20px;">
                <div class="chart-card-title">
                  <div>
                    <h5 style="font-size:14px; color:#064635;">Trend Bulanan</h5>
                    <span style="font-size:12px; color:#9ca3af;">Jumlah inventaris yang ditambahkan per bulan</span>
                  </div>
                </div>
              </div>
              <div class="chart-export-row">
                <div class="chart-actions-left">
                  <button type="button" class="btn-chart-filter" data-panel-target="panel-line-filters">
                    <i data-feather="sliders"></i>
                    Filter
                  </button>
                  <button type="button" class="btn-chart-export" id="btn-export-chart-line">
                    <i data-feather="download"></i>
                    Export PDF Chart
                  </button>
                </div>
              </div>
              <div class="chart-filter-panel" id="panel-line-filters">
                <div class="chart-filter-row">
                  <select class="chart-filter-select" id="filter-line-rentang">
                    <option value="6" selected>6 Bulan</option>
                    <option value="12">1 Tahun</option>
                  </select>
                  <select class="chart-filter-select" id="filter-line-semester">
                    <option value="1">Semester I &nbsp;(Jan – Jun)</option>
                    <option value="2">Semester II (Jul – Des)</option>
                  </select>
                  <div class="chart-year-picker-wrapper">
                    <input type="text"
                           class="chart-filter-select chart-year-picker"
                           id="filter-line-tahun"
                           readonly
                           placeholder="Semua Tahun"
                           data-min-year="2000"
                           data-max-year="{{ now()->year }}" />
                    <button type="button" class="chart-year-picker-clear" id="clear-line-tahun" title="Hapus filter tahun">&times;</button>
                  </div>
                  <select class="chart-filter-select" id="filter-line-jenis">
                    <option value="">Semua Jenis</option>
                  </select>
                  <select class="chart-filter-select" id="filter-line-kondisi">
                    <option value="">Semua Kondisi</option>
                    <option value="Baik">Baik</option>
                    <option value="Rusak Ringan">Rusak Ringan</option>
                    <option value="Rusak Berat">Rusak Berat</option>
                  </select>
                </div>
              </div>
              <div class="chart-card-body" style="padding: 16px 20px 5px 20px;">
                <canvas id="chart-month-trend" style="width:100%; height:260px;"></canvas>
              </div>
            </div>
          </div>

          <!-- Right: Export Terbaru -->
          <div class="col-span-12 xl:col-span-6">
            <div class="info-panel h-full">
              <div class="info-panel-header">
                <div>
                  <h5 class="info-panel-title">Export Terbaru</h5>
                  <span style="font-size:12px; color:#9ca3af;">Data ekspor dokumen inventaris terakhir</span>
                </div>
              </div>
              <div class="info-panel-body">
                <table class="pending-table">
                  <thead>
                    <tr>
                      <th style="text-align:left;">User</th>
                      <th style="text-align:left;">Jenis</th>
                      <th style="text-align:left;">Tanggal &amp; Jam</th>
                    </tr>
                  </thead>
                  <tbody id="export-log"></tbody>
                </table>
              </div>
              <div id="link-all-exports" style="display:none; padding: 8px 24px 16px;">
                <a href="{{ route('export-data.history', ['from' => 'dashboard']) }}" style="font-size:12px; color:#064635; font-weight:600; text-decoration:underline; text-underline-offset:2px;">Lihat Semua Export &rarr;</a>
              </div>
            </div>
          </div>
        </div>

        <!-- Duplicate full-width Trend removed to keep single left-column Trend -->
<!-- SECTION 4: Inventaris per Jenis - Uniform Cards -->
        <div class="mb-4">
          <h5 class="text-lg font-semibold mb-1" style="color:#064635;">Ringkasan Inventaris per Jenis</h5>
          <p class="text-muted text-sm">Klik pada kartu untuk melihat detail inventaris</p>
        </div>
        <style>
          .jenis-grid {
            display: grid;
            grid-template-columns: repeat(4, 1fr);
            gap: 20px;
            margin-bottom: 24px;
          }
          @media (max-width: 1199px) {
            .jenis-grid { grid-template-columns: repeat(3, 1fr); }
          }
          @media (max-width: 767px) {
            .jenis-grid { grid-template-columns: repeat(2, 1fr); }
          }
          @media (max-width: 479px) {
            .jenis-grid { grid-template-columns: repeat(1, 1fr); }
          }
          .jenis-card {
            background: #fff;
            border-radius: 16px;
            border: 1px solid rgba(0,0,0,0.05);
            box-shadow: 0 2px 12px rgba(0,0,0,0.06);
            padding: 20px;
            display: flex;
            flex-direction: column;
            justify-content: space-between;
            min-height: 148px;
            transition: transform 0.2s ease, box-shadow 0.2s ease;
            cursor: pointer;
            overflow: hidden;
            position: relative;
          }
          .jenis-card:hover {
            transform: translateY(-3px);
            box-shadow: 0 8px 24px rgba(0,0,0,0.1);
          }
          .jenis-card-top {
            display: flex;
            align-items: flex-start;
            justify-content: space-between;
            gap: 10px;
          }
          .jenis-card-icon {
            width: 44px;
            height: 44px;
            border-radius: 12px;
            display: flex;
            align-items: center;
            justify-content: center;
            flex-shrink: 0;
            transition: transform 0.2s ease;
          }
          .jenis-card:hover .jenis-card-icon { transform: scale(1.1); }
          .jenis-card-icon svg,
          .jenis-card-icon i {
            width: 20px !important;
            height: 20px !important;
            position: relative !important;
            top: unset !important;
            left: unset !important;
            margin: auto !important;
          }
          .jenis-card-name {
            font-size: 13px;
            font-weight: 600;
            color: #1f2937;
            margin: 0 0 2px 0;
            line-height: 1.3;
            /* Allow 2 lines, then ellipsis */
            display: -webkit-box;
            -webkit-line-clamp: 2;
            -webkit-box-orient: vertical;
            overflow: hidden;
          }
          .jenis-card-desc {
            font-size: 11px;
            color: #9ca3af;
            margin: 0;
            white-space: nowrap;
            overflow: hidden;
            text-overflow: ellipsis;
          }
          .jenis-card-bottom {
            margin-top: 14px;
          }
          .jenis-card-count {
            font-size: 26px;
            font-weight: 700;
            color: #064635;
            line-height: 1;
          }
          .jenis-card-label {
            font-size: 12px;
            color: #9ca3af;
            margin-left: 4px;
          }
          .jenis-card-bar {
            height: 4px;
            background: #f3f4f6;
            border-radius: 99px;
            overflow: hidden;
            margin-top: 8px;
          }
          .jenis-card-bar-fill {
            height: 100%;
            border-radius: 99px;
            min-width: 2px;
          }
          /* LHI warning card variant */
          .jenis-card.lhi-card {
            background: linear-gradient(135deg, #fff7ed 0%, #fef3c7 100%);
            border-left: 4px solid #f59e0b;
          }
          .jenis-card.lhi-card .jenis-card-name { color: #92400e; }
          .jenis-card.lhi-card .jenis-card-desc { color: #b45309; }
          .jenis-card.lhi-card .jenis-card-count { color: #b45309; }
          .jenis-card.lhi-card .jenis-card-label { color: #d97706; }
          .jenis-card.lhi-card .jenis-card-bar { background: rgba(245,158,11,0.15); }
        </style>
        <div class="jenis-grid">
          {{-- Hero card: Total semua aset --}}
          <div class="jenis-card" style="background: #519259; border: none;">
            <div class="jenis-card-top">
              <div>
                <p style="color:rgba(255,255,255,0.65); font-size:11px; margin:0 0 2px 0; text-transform:uppercase; letter-spacing:.5px;">Semua jenis</p>
                <p class="jenis-card-name" style="color:#fff; font-size:16px;">Aset Desa</p>
              </div>
              <div class="jenis-card-icon" style="background:rgba(255,255,255,0.15);">
                <i data-feather="book-open" style="color:#fff; stroke:#fff;"></i>
              </div>
            </div>
            <div class="jenis-card-bottom" style="border-top:1px solid rgba(255,255,255,0.15); padding-top:12px; margin-top:12px; display:flex; flex-direction:column; gap:8px;">
              <div style="display:flex; align-items:center; justify-content:space-between;">
                <div>
                  <span style="font-size:28px; font-weight:700; color:#fff; line-height:1;">{{ $kpis['totalAssets'] ?? 0 }}</span>
                  <span style="font-size:12px; color:rgba(255,255,255,0.6); margin-left:5px;">Total Data</span>
                </div>
                <span style="font-size:12px; color:rgba(255,255,255,0.6);">{{ $jenisInventarisList->count() }} jenis</span>
              </div>
              <div class="kpi-trend-wrapper" style="margin-top:0;">
                @php $tMain = $kpis['trends']['assets']['percent'] ?? 0; @endphp
                @if($tMain > 0)
                  <span class="kpi-trend-pill" style="background:rgba(255,255,255,0.25); color:#fff;">
                    <svg width="10" height="10" viewBox="0 0 24 24" fill="currentColor"><path d="M12 4l-8 8h16z"/></svg>
                    +{{ $tMain }}%
                  </span>
                @elseif($tMain < 0)
                  <span class="kpi-trend-pill" style="background:rgba(255,255,255,0.25); color:#fff;">
                    <svg width="10" height="10" viewBox="0 0 24 24" fill="currentColor"><path d="M12 20l8-8H4z"/></svg>
                    {{ $tMain }}%
                  </span>
                @else
                  <span class="kpi-trend-pill" style="background:rgba(255,255,255,0.2); color:rgba(255,255,255,0.85);">0%</span>
                @endif
                <span class="kpi-trend-label" style="color:rgba(255,255,255,0.7); font-size:11px;">vs. kemarin</span>
              </div>
            </div>
          </div>

          {{-- Dynamic cards: loop semua jenis inventaris --}}
          @foreach($jenisInventarisList as $jenis)
            @php
              $gradientCss = $jenis->getColorGradientCss();
              $icon        = $jenis->icon;
              $count       = $jenis->inventaris_count;
              $isLhi       = stripos($jenis->nama, 'tidak diketemukan') !== false
                          || stripos($jenis->nama, 'tidak ditemukan') !== false;
              $totalAssets = $kpis['totalAssets'] ?? 0;
              $barWidth    = $totalAssets > 0 ? round(($count / $totalAssets) * 100, 1) : 0;
            @endphp

            <div class="jenis-card {{ $isLhi ? 'lhi-card' : '' }}">
              <div class="jenis-card-top">
                <div style="flex:1; min-width:0;">
                  <p class="jenis-card-name" title="{{ $jenis->nama }}">{{ $jenis->nama }}</p>
                  <p class="jenis-card-desc">{{ $jenis->deskripsi ?: '-' }}</p>
                </div>
                @if($isLhi)
                  <div class="jenis-card-icon" style="background:rgba(245,158,11,0.15);">
                    <i data-feather="{{ $icon }}" style="color:#f59e0b; stroke:#f59e0b;"></i>
                  </div>
                @else
                  <div class="jenis-card-icon" style="background:{{ $gradientCss }};">
                    <i data-feather="{{ $icon }}" style="color:#fff; stroke:#fff;"></i>
                  </div>
                @endif
              </div>
              <div class="jenis-card-bottom">
                <div style="display:flex; align-items:center; justify-content:space-between; width:100%;">
                  <div style="display:flex; align-items:baseline; gap:5px;">
                    <span class="jenis-card-count">{{ $count }}</span>
                    <span class="jenis-card-label">data</span>
                  </div>
                  <div class="kpi-trend-wrapper" style="margin-top:0;">
                    @php $tJenis = $jenis->trend_percent ?? 0; @endphp
                    @if($tJenis > 0)
                      <span class="kpi-trend-pill up">
                        <svg width="10" height="10" viewBox="0 0 24 24" fill="currentColor"><path d="M12 4l-8 8h16z"/></svg>
                        +{{ $tJenis }}%
                      </span>
                    @elseif($tJenis < 0)
                      <span class="kpi-trend-pill down">
                        <svg width="10" height="10" viewBox="0 0 24 24" fill="currentColor"><path d="M12 20l8-8H4z"/></svg>
                        {{ $tJenis }}%
                      </span>
                    @else
                      <span class="kpi-trend-pill neutral">0%</span>
                    @endif
                    <span class="kpi-trend-label" style="font-size:11px;">vs. kemarin</span>
                  </div>
                </div>
                <div class="jenis-card-bar" style="margin-top:8px;">
                  <div class="jenis-card-bar-fill"
                       style="width:{{ $barWidth }}%; background:{{ $isLhi ? 'linear-gradient(90deg,#f59e0b,#d97706)' : $gradientCss }};"></div>
                </div>
              </div>
            </div>
          @endforeach
        </div>
        <!-- [ Main Content ] end -->
      </div>
    </div>
    <!-- [ Main Content ] end -->
    @include('layouts.footer-block') 
    @include('layouts.modals')
    @include('layouts.footer-js')
    <!-- Flatpickr JS untuk year picker -->
    <script src="https://cdn.jsdelivr.net/npm/flatpickr"></script>
    <!-- Chart.js CDN and dashboard demo script -->
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <script>
      (function(){
        const el = id => document.getElementById(id);

        // Format nilai singkat: Rp 3,19 M / Rp 850 Jt / Rp 500.000
        function formatNilaiSingkat(value) {
          if (value >= 1_000_000_000) {
            var v = value / 1_000_000_000;
            return 'Rp ' + (Number.isInteger(v) ? v : parseFloat(v.toFixed(2)).toLocaleString('en-US')) + ' M';
          } else if (value >= 1_000_000) {
            var v = value / 1_000_000;
            return 'Rp ' + (Number.isInteger(v) ? v : parseFloat(v.toFixed(2)).toLocaleString('en-US')) + ' Jt';
          }
          return new Intl.NumberFormat('id-ID', {style: 'currency', currency: 'IDR', maximumFractionDigits: 0}).format(value);
        }
        
        function refreshKpiData() {
          apiGet('/api/dashboard/kpi')
            .then(r => r.json())
            .then(data => {
              if(el('kpi-total-assets')) el('kpi-total-assets').textContent = data.totalAssets || 0;
              if(el('kpi-total-value')) {
                const nilaiAsli = data.totalValue || 0;
                const nilaiSingkat = formatNilaiSingkat(nilaiAsli);
                const nilaiPenuh = new Intl.NumberFormat('id-ID', {style:'currency', currency:'IDR', maximumFractionDigits:0}).format(nilaiAsli);
                el('kpi-total-value').textContent = nilaiPenuh;
                el('kpi-total-value').title = nilaiPenuh;
              }
              if(el('kpi-pejabat')) el('kpi-pejabat').textContent = data.pejabatActive || 0;
              if(el('kpi-users')) el('kpi-users').textContent = data.usersActive || 0;
              if(el('kpi-kondisi-baik')) el('kpi-kondisi-baik').textContent = data.kondisiAset?.baik || 0;
              if(el('kpi-kondisi-rr')) el('kpi-kondisi-rr').textContent = data.kondisiAset?.rusakRingan || 0;
              if(el('kpi-kondisi-rb')) el('kpi-kondisi-rb').textContent = data.kondisiAset?.rusakBerat || 0;
            })
            .catch(err => {
              console.error('Error loading KPI data:', err);
              if(el('kpi-total-assets')) el('kpi-total-assets').textContent = '—';
              if(el('kpi-total-value')) el('kpi-total-value').textContent = '—';
              if(el('kpi-pejabat')) el('kpi-pejabat').textContent = '—';
              if(el('kpi-users')) el('kpi-users').textContent = '—';
              if(el('kpi-kondisi-baik')) el('kpi-kondisi-baik').textContent = '—';
              if(el('kpi-kondisi-rr')) el('kpi-kondisi-rr').textContent = '—';
              if(el('kpi-kondisi-rb')) el('kpi-kondisi-rb').textContent = '—';
            });
        }

        function refreshRecentActivities() {
          apiGet('/api/dashboard/activities')
            .then(r => r.json())
            .then(activities => {
              const list = Array.isArray(activities) ? activities : [];
              const actEl = document.getElementById('recent-activities');
              const actLink = document.getElementById('link-all-activities');
              if(actLink) actLink.style.display = list.length > 5 ? 'block' : 'none';
              if(actEl){
                const slice = list.slice(0, 5);
                if(slice.length > 0){
                  actEl.innerHTML = slice.map(a => `
                    <li class="activity-item">
                      <div class="activity-dot" style="background: ${a.color || '#6b7280'};"></div>
                      <div class="activity-content">
                        <div class="activity-text">${a.text || 'Aktivitas tidak diketahui'}</div>
                        <div class="activity-time">${a.time || '-'}</div>
                      </div>
                    </li>
                  `).join('');
                } else {
                  actEl.innerHTML = '<li style="text-align:center; padding:24px; color:#9ca3af; font-style:italic; list-style:none;">Belum ada aktivitas terbaru</li>';
                }
              }
            })
            .catch(err => {
              console.error('Error loading activities:', err);
              const actEl = document.getElementById('recent-activities');
              if(actEl) actEl.innerHTML = '<li style="text-align:center; padding:24px; color:#ef4444; font-style:italic; list-style:none;">Gagal memuat data</li>';
            });
        }

        // Pending approvals removed from UI; no-op.

        function refreshExportLogs() {
          apiGet('/api/dashboard/exports')
            .then(r => r.json())
            .then(logs => {
              const list = Array.isArray(logs) ? logs : [];
              const expEl = document.getElementById('export-log');
              const expLink = document.getElementById('link-all-exports');
              if(expLink) expLink.style.display = list.length > 5 ? 'block' : 'none';
              if(expEl){
                const slice = list.slice(0, 5);
                if(slice.length > 0){
                  expEl.innerHTML = slice.map(e =>
                    `<tr><td style="font-weight:500;">${e.user || 'User tidak diketahui'}</td><td>${e.jenis || '-'}</td><td style="color:#6b7280;">${e.tanggal || '-'}</td></tr>`
                  ).join('');
                } else {
                  expEl.innerHTML = '<tr><td colspan="3" style="text-align:center; padding:24px; color:#9ca3af; font-style:italic;">Belum ada riwayat export</td></tr>';
                }
              }
            })
            .catch(err => {
              console.error('Error loading export logs:', err);
              const expEl = document.getElementById('export-log');
              if(expEl) expEl.innerHTML = '<tr><td colspan="3" style="text-align:center; padding:24px; color:#ef4444; font-style:italic;">Gagal memuat data</td></tr>';
            });
        }

        refreshKpiData();
        refreshRecentActivities();
        refreshExportLogs();

        // ===== CHART INSTANCES & HELPERS =====
        let barChartInst = null, donutChartInst = null, lineChartInst = null;

        // ===== CHART YEAR PICKER (Pakem sistem) =====
        function clampYearValue(year, minYear, maxYear) {
          var numericYear = Number.parseInt(String(year), 10);
          if (!Number.isFinite(numericYear)) return '';
          return String(Math.min(Math.max(numericYear, minYear), maxYear));
        }

        function ensureYearPickerPanel(instance, minYear, maxYear) {
          var calendar = instance.calendarContainer;
          if (!calendar) return;
          calendar.classList.add('year-only-picker');
          var state = calendar._yearPickerState;
          if (!state) {
            var panel = document.createElement('div');
            panel.className = 'year-picker-panel';
            panel.innerHTML =
              '<div class="year-picker-header">' +
                '<button type="button" class="year-picker-nav" data-nav="prev" aria-label="Tahun sebelumnya">&lsaquo;</button>' +
                '<span class="year-picker-range"></span>' +
                '<button type="button" class="year-picker-nav" data-nav="next" aria-label="Tahun berikutnya">&rsaquo;</button>' +
              '</div>' +
              '<div class="year-picker-grid"></div>';
            calendar.appendChild(panel);
            state = {
              panel: panel,
              range: panel.querySelector('.year-picker-range'),
              grid: panel.querySelector('.year-picker-grid'),
              prev: panel.querySelector('[data-nav="prev"]'),
              next: panel.querySelector('[data-nav="next"]'),
              pageStart: minYear,
              selectedYear: minYear
            };
            calendar._yearPickerState = state;
            state.prev.addEventListener('click', function(ev) {
              ev.preventDefault(); ev.stopPropagation();
              state.pageStart = Math.max(minYear, state.pageStart - 12);
              renderYearPickerPanel(instance, state, minYear, maxYear);
            });
            state.next.addEventListener('click', function(ev) {
              ev.preventDefault(); ev.stopPropagation();
              var maxPageStart = Math.max(minYear, maxYear - 11);
              state.pageStart = Math.min(maxPageStart, state.pageStart + 12);
              renderYearPickerPanel(instance, state, minYear, maxYear);
            });
            state.grid.addEventListener('click', function(ev) {
              var btn = ev.target.closest('.year-picker-option');
              if (!btn) return;
              var year = Number.parseInt(btn.dataset.year, 10);
              if (!Number.isFinite(year)) return;
              instance.setDate(year + '-01-01', true, 'Y');
              instance.input.value = String(year);
              state.selectedYear = year;
              instance.input.dispatchEvent(new Event('input', { bubbles: true }));
              instance.input.dispatchEvent(new Event('change', { bubbles: true }));
              instance.close();
              // Tampilkan tombol clear
              var clearBtn = instance.input.parentElement.querySelector('.chart-year-picker-clear');
              if (clearBtn) clearBtn.classList.add('visible');
            });
          }
          var rawYear = clampYearValue(instance.input.value, minYear, maxYear);
          var fallbackYear = Math.min(Math.max(new Date().getFullYear(), minYear), maxYear);
          var selectedYear = rawYear ? Number.parseInt(rawYear, 10) : fallbackYear;
          if (!Number.isFinite(state.pageStart) || selectedYear < state.pageStart || selectedYear > (state.pageStart + 11)) {
            var maxPageStart = Math.max(minYear, maxYear - 11);
            state.pageStart = Math.min(maxPageStart, Math.max(minYear, selectedYear - 5));
          }
          state.selectedYear = selectedYear;
          renderYearPickerPanel(instance, state, minYear, maxYear);
        }

        function renderYearPickerPanel(instance, state, minYear, maxYear) {
          var start = state.pageStart;
          var end = Math.min(start + 11, maxYear);
          state.range.textContent = start + ' - ' + end;
          state.prev.disabled = start <= minYear;
          state.next.disabled = end >= maxYear;
          var html = '';
          for (var year = start; year <= end; year++) {
            var activeClass = year === state.selectedYear ? ' active' : '';
            html += '<button type="button" class="year-picker-option' + activeClass + '" data-year="' + year + '">' + year + '</button>';
          }
          state.grid.innerHTML = html;
        }

        function initChartYearPickers() {
          if (typeof flatpickr === 'undefined') return;
          document.querySelectorAll('.chart-year-picker').forEach(function(input) {
            if (input._flatpickr) { input._flatpickr.destroy(); }
            var minAllowed = Number.parseInt(input.dataset.minYear || '2000', 10);
            var maxAllowed = Number.parseInt(input.dataset.maxYear || String(new Date().getFullYear()), 10);
            flatpickr(input, {
              dateFormat: 'Y',
              allowInput: false,
              disableMobile: true,
              minDate: minAllowed + '-01-01',
              maxDate: maxAllowed + '-12-31',
              position: 'auto left',
              monthSelectorType: 'static',
              onReady: function(_, __, instance) {
                ensureYearPickerPanel(instance, minAllowed, maxAllowed);
              },
              onOpen: function(_, __, instance) {
                ensureYearPickerPanel(instance, minAllowed, maxAllowed);
              },
              onValueUpdate: function(_, __, instance) {
                var norm = clampYearValue(instance.input.value, minAllowed, maxAllowed);
                instance.input.value = norm;
                ensureYearPickerPanel(instance, minAllowed, maxAllowed);
              }
            });
          });

          // Bind tombol clear (×)
          [['clear-bar-tahun','filter-bar-tahun', fetchBarDonut],
           ['clear-donut-tahun','filter-donut-tahun', fetchBarDonut],
           ['clear-line-tahun','filter-line-tahun', fetchLine]].forEach(function(pair) {
            var clearBtn = document.getElementById(pair[0]);
            var inputEl  = document.getElementById(pair[1]);
            var fetchFn  = pair[2];
            if (!clearBtn || !inputEl) return;
            clearBtn.addEventListener('click', function(ev) {
              ev.stopPropagation();
              if (inputEl._flatpickr) inputEl._flatpickr.clear();
              inputEl.value = '';
              clearBtn.classList.remove('visible');
              inputEl.dispatchEvent(new Event('change', { bubbles: true }));
            });
            // Tampilkan clear button jika sudah ada nilai saat init
            if (inputEl.value) clearBtn.classList.add('visible');
          });
        }

        function populateYearDropdowns(years) {
          // Year picker sudah diinit dengan data-min-year="2000" di HTML.
          // Tidak perlu membatasi range dari availableYears API agar user
          // tetap bisa memilih tahun mana pun (bukan hanya tahun yg ada data).
          // Fungsi ini sengaja dibiarkan no-op.
        }
        function populateJenisDropdown(jenisList) {
          var sel = document.getElementById('filter-line-jenis');
          if (!sel) return;
          (jenisList || []).forEach(function(j) {
            var o = document.createElement('option'); o.value = j.id; o.textContent = j.nama; sel.appendChild(o);
          });
        }
        function getBarDonutParams() {
          var p = {};
          var bm = document.getElementById('filter-bar-metric');    if (bm && bm.value) p.bar_metric   = bm.value;
          var bt = document.getElementById('filter-bar-tahun');     if (bt && bt.value) p.bar_tahun    = bt.value;
          var bk = document.getElementById('filter-bar-kondisi');   if (bk && bk.value) p.bar_kondisi  = bk.value;
          var dm = document.getElementById('filter-donut-metric');  if (dm && dm.value) p.donut_metric = dm.value;
          var dt = document.getElementById('filter-donut-tahun');   if (dt && dt.value) p.donut_tahun  = dt.value;
          var dk = document.getElementById('filter-donut-kondisi'); if (dk && dk.value) p.donut_kondisi = dk.value;
          return p;
        }
        function getLineParams() {
          var p = {};
          var r = document.getElementById('filter-line-rentang');  if (r && r.value)  p.rentang       = r.value;
          // Kirim semester hanya saat rentang = 6 Bulan
          if (r && r.value === '6') {
            var s = document.getElementById('filter-line-semester');
            if (s && s.value) p.semester = s.value;
          }
          var t = document.getElementById('filter-line-tahun');    if (t && t.value)  p.trend_tahun   = t.value;
          var j = document.getElementById('filter-line-jenis');    if (j && j.value)  p.jenis_id      = j.value;
          var k = document.getElementById('filter-line-kondisi');  if (k && k.value)  p.kondisi_trend = k.value;
          return p;
        }

        function toggleSemesterFilter() {
          var rentangEl  = document.getElementById('filter-line-rentang');
          var semesterEl = document.getElementById('filter-line-semester');
          if (!rentangEl || !semesterEl) return;
          semesterEl.style.display = rentangEl.value === '6' ? '' : 'none';
        }
        function buildChartUrl(params) {
          var q = Object.entries(params).map(function(e) { return e[0] + '=' + encodeURIComponent(e[1]); }).join('&');
          return '/api/dashboard/charts' + (q ? '?' + q : '');
        }

        function toggleFilterPanel(panelId) {
          document.querySelectorAll('.chart-filter-panel').forEach(function(panel) {
            var btn = document.querySelector('[data-panel-target="' + panel.id + '"]');
            if (panel.id === panelId) {
              var isOpen = panel.classList.toggle('open');
              if (btn) {
                if (isOpen) {
                  btn.classList.add('active');
                } else {
                  btn.classList.remove('active');
                }
              }
            } else {
              panel.classList.remove('open');
              if (btn) btn.classList.remove('active');
            }
          });
        }

        function closeAllFilterPanels() {
          document.querySelectorAll('.chart-filter-panel').forEach(function(panel) {
            panel.classList.remove('open');
            var btn = document.querySelector('[data-panel-target="' + panel.id + '"]');
            if (btn) btn.classList.remove('active');
          });
        }

        function bindFilterPanelToggles() {
          document.querySelectorAll('[data-panel-target]').forEach(function(btn) {
            btn.addEventListener('click', function() {
              var targetId = btn.getAttribute('data-panel-target');
              if (!targetId) return;
              toggleFilterPanel(targetId);
            });
          });
        }

        function triggerChartExport(chartType) {
          var chartLabels = {
            bar: 'Distribusi Nilai Aset per Jenis',
            donut: 'Komposisi Aset',
            line: 'Trend Bulanan'
          };

          var params = {};
          if (chartType === 'bar') {
            params = {
              bar_metric: document.getElementById('filter-bar-metric')?.value || 'nilai',
              bar_tahun: document.getElementById('filter-bar-tahun')?.value || '',
              bar_kondisi: document.getElementById('filter-bar-kondisi')?.value || ''
            };
          } else if (chartType === 'donut') {
            params = {
              donut_metric: document.getElementById('filter-donut-metric')?.value || 'jumlah',
              donut_tahun: document.getElementById('filter-donut-tahun')?.value || '',
              donut_kondisi: document.getElementById('filter-donut-kondisi')?.value || ''
            };
          } else {
            var lineRentang = document.getElementById('filter-line-rentang')?.value || '6';
            params = {
              rentang: lineRentang,
              semester: lineRentang === '6' ? (document.getElementById('filter-line-semester')?.value || '1') : '',
              trend_tahun: document.getElementById('filter-line-tahun')?.value || '',
              jenis_id: document.getElementById('filter-line-jenis')?.value || '',
              kondisi_trend: document.getElementById('filter-line-kondisi')?.value || ''
            };
          }

          var cleanParams = Object.entries(params)
            .filter(function(entry) { return String(entry[1]).trim() !== ''; })
            .map(function(entry) { return entry[0] + '=' + encodeURIComponent(entry[1]); })
            .join('&');

          var exportBase = '{{ route('dashboard.charts.export', ['chart' => '__CHART__']) }}';
          var exportUrl = exportBase.replace('__CHART__', chartType);
          var finalUrl = exportUrl + (cleanParams ? ('?' + cleanParams) : '');
          var chartName = chartLabels[chartType] || String(chartType || '').toUpperCase();

          if (typeof AppModal !== 'undefined') {
            AppModal.confirm(
              'Konfirmasi Export',
              'Anda akan mengexport chart "' + chartName + '" ke PDF. Lanjutkan export?',
              function() {
                window.location.href = finalUrl;
              }
            );
            return;
          }

          if (confirm('Anda akan mengexport chart "' + chartName + '" ke PDF. Lanjutkan export?')) {
            window.location.href = finalUrl;
          }
        }

        // ===== RENDER FUNCTIONS =====
        function renderBarChart(data) {
          var barCtxEl = document.getElementById('chart-value-by-type');
          if (!barCtxEl) return;
          if (barChartInst) { barChartInst.destroy(); barChartInst = null; }
          var barValues = data.barValues || [];
          var isNilai = (data.barMetric || 'nilai') === 'nilai';
          var hasBarData = barValues.some(function(v) { return v > 0; });
          if (!hasBarData) {
            barCtxEl.style.display = 'none';
            document.getElementById('bar-empty-state').style.display = 'flex';
            return;
          }
          barCtxEl.style.display = 'block';
          document.getElementById('bar-empty-state').style.display = 'none';
          var ctx = barCtxEl.getContext('2d');
          var barColors = data.barColors || '#519259';
          barChartInst = new Chart(ctx, {
            type: 'bar',
            data: {
              labels: data.barLabels || [],
              datasets: [{ label: isNilai ? 'Nilai Aset (IDR)' : 'Jumlah Unit', data: barValues,
                backgroundColor: barColors, borderRadius: 8, borderSkipped: false, barThickness: 36 }]
            },
            options: {
              responsive: true, maintainAspectRatio: false,
              plugins: {
                legend: { display: false },
                tooltip: {
                  backgroundColor: 'rgba(31, 41, 55, 0.95)', titleFont: { size: 13, weight: '600' },
                  bodyFont: { size: 12 }, padding: 12, cornerRadius: 8,
                  callbacks: {
                    title: function(items) {
                      // Nama lengkap saat hover (termasuk "Inventaris")
                      return (data.barLabels || [])[items[0].dataIndex] || items[0].label;
                    },
                    label: function(ctx2) {
                      return isNilai ? new Intl.NumberFormat('id-ID',{style:'currency',currency:'IDR',maximumFractionDigits:0}).format(ctx2.raw) : ctx2.raw + ' unit';
                    }
                  }
                }
              },
              scales: {
                x: {
                  grid: { display: false },
                  ticks: {
                    font: { size: 11 },
                    color: '#6b7280',
                    maxRotation: 0,
                    minRotation: 0,
                    callback: function(value, index) {
                      var label = (data.barLabels || [])[index] || '';
                      // Hapus prefix "Inventaris " agar label lebih pendek
                      label = label.replace(/^Inventaris\s+/i, '');
                      return label.length > 14 ? label.substring(0, 13) + '…' : label;
                    }
                  }
                },
                y: { min: 0, grid: { color: 'rgba(0,0,0,0.04)' }, ticks: { font: { size: 11 }, color: '#6b7280',
                  callback: function(value) {
                    if (!isNilai) return value;
                    if (value === 0) return 'Rp 0';
                    if (value >= 1000000000) {
                      var v = value/1000000000;
                      return 'Rp ' + (Number.isInteger(v) ? v : parseFloat(v.toFixed(2))) + ' M';
                    }
                    if (value >= 1000000) {
                      var v = value/1000000;
                      return 'Rp ' + (Number.isInteger(v) ? v : parseFloat(v.toFixed(1))) + ' jt';
                    }
                    if (value >= 1000) {
                      var v = value/1000;
                      return 'Rp ' + (Number.isInteger(v) ? v : parseFloat(v.toFixed(1))) + ' rb';
                    }
                    return 'Rp ' + value.toLocaleString('id-ID');
                  }
                }}
              }
            }
          });

          // Tooltip untuk label X-axis yang terpotong
          var axisTooltip = document.getElementById('bar-axis-tooltip');
          if (barCtxEl && axisTooltip) {
            barCtxEl.addEventListener('mousemove', function(e) {
              var chart = barChartInst;
              if (!chart) return;
              var rect = barCtxEl.getBoundingClientRect();
              var mouseX = e.clientX - rect.left;
              var mouseY = e.clientY - rect.top;
              var xAxis = chart.scales.x;
              if (mouseY < xAxis.top) { axisTooltip.style.display = 'none'; return; }
              var labels = data.barLabels || [];
              var found = false;
              for (var i = 0; i < labels.length; i++) {
                var tickX = xAxis.getPixelForValue(i);
                if (Math.abs(mouseX - tickX) < 44) {
                  var fullLabel = labels[i];
                  var shortLabel = fullLabel.replace(/^Inventaris\s+/i, '');
                  if (shortLabel.length > 14 || fullLabel !== shortLabel) {
                    axisTooltip.textContent = fullLabel;
                    axisTooltip.style.display = 'block';
                    axisTooltip.style.left = tickX + 'px';
                    axisTooltip.style.top = (xAxis.bottom + 4) + 'px';
                    found = true;
                  }
                  break;
                }
              }
              if (!found) axisTooltip.style.display = 'none';
            });
            barCtxEl.addEventListener('mouseleave', function() {
              axisTooltip.style.display = 'none';
            });
          }
        }

        // ===== HELPER: format nilai singkat =====
        function formatNilaiSingkatLocal(v) {          if (v >= 1000000000) return 'Rp ' + parseFloat((v/1000000000).toFixed(2)).toLocaleString('id-ID') + ' M';
          if (v >= 1000000)    return 'Rp ' + parseFloat((v/1000000).toFixed(2)).toLocaleString('id-ID') + ' Jt';
          return new Intl.NumberFormat('id-ID', {style:'currency', currency:'IDR', maximumFractionDigits:0}).format(v);
        }

        // ===== DASHBOARD TABLE PAGINATION =====
        var DASH_PAGE_SIZE = 10;
        var dashPaginationState = {
          jenis: { page: 1, items: [] },
          nilai: { page: 1, items: [] },
          kondisi: { page: 1, items: [] }
        };

        function renderDashPagination(containerId, state, renderFn) {
          var el = document.getElementById(containerId);
          if (!el) return;
          var total = state.items.length;
          var totalPages = Math.ceil(total / DASH_PAGE_SIZE);
          
          if (total <= DASH_PAGE_SIZE) {
            el.style.display = 'none';
            return;
          }
          el.style.display = 'flex';
          
          var from = ((state.page - 1) * DASH_PAGE_SIZE) + 1;
          var to = Math.min(state.page * DASH_PAGE_SIZE, total);
          
          var html = '<div class="dash-pagination-info">Menampilkan <strong>' + from + '-' + to + '</strong> dari <strong>' + total + '</strong></div>';
          html += '<div class="dash-pagination-nav">';
          html += '<button class="dash-page-btn ' + (state.page <= 1 ? 'disabled' : '') + '" data-page="' + (state.page - 1) + '"><svg xmlns="http://www.w3.org/2000/svg" width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><polyline points="15 18 9 12 15 6"/></svg></button>';
          
          for (var i = 1; i <= totalPages; i++) {
            if (totalPages > 5) {
              if (i === 1 || i === totalPages || (i >= state.page - 1 && i <= state.page + 1)) {
                html += '<button class="dash-page-btn ' + (i === state.page ? 'active' : '') + '" data-page="' + i + '">' + i + '</button>';
              } else if (i === state.page - 2 || i === state.page + 2) {
                html += '<span style="padding:0 4px;color:#9ca3af;font-size:11px;">...</span>';
              }
            } else {
              html += '<button class="dash-page-btn ' + (i === state.page ? 'active' : '') + '" data-page="' + i + '">' + i + '</button>';
            }
          }
          
          html += '<button class="dash-page-btn ' + (state.page >= totalPages ? 'disabled' : '') + '" data-page="' + (state.page + 1) + '"><svg xmlns="http://www.w3.org/2000/svg" width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><polyline points="9 18 15 12 9 6"/></svg></button>';
          html += '</div>';
          
          el.innerHTML = html;
          el.querySelectorAll('.dash-page-btn:not(.disabled)').forEach(function(btn) {
            btn.addEventListener('click', function() {
              state.page = parseInt(this.dataset.page);
              renderFn();
            });
          });
        }

        // Populate tabel Nilai Perolehan Inventaris
        // Tampilkan SEMUA jenis dari availableJenis, yang tidak ada datanya = Rp 0
        function populateNilaiTable(data) {
          var tableEl = document.getElementById('nilai-inventory-table');
          var emptyEl = document.getElementById('nilai-table-empty-state');
          var totalPerolehanEl = document.getElementById('table-total-perolehan');
          if (!tableEl) return;

          var availableJenis = data.availableJenis || [];
          var nilaiMap = {};
          (data.nilaiInventaris || []).forEach(function(item) {
            nilaiMap[item.id] = item.nilai || 0;
          });

          if (availableJenis.length === 0) {
            if (emptyEl) emptyEl.style.display = 'flex';
            tableEl.innerHTML = '';
            if (totalPerolehanEl) totalPerolehanEl.textContent = 'Rp 0';
            return;
          }

          if (emptyEl) emptyEl.style.display = 'none';

          var items = availableJenis.map(function(jenis) {
            return { nama: jenis.nama, nilai: nilaiMap[jenis.id] || 0 };
          });
          items.sort(function(a, b) { return b.nilai - a.nilai; });

          var totalNilai = items.reduce(function(sum, item) { return sum + item.nilai; }, 0);
          if (totalPerolehanEl) {
            totalPerolehanEl.textContent = totalNilai > 0
              ? new Intl.NumberFormat('id-ID', {style:'currency', currency:'IDR', maximumFractionDigits:0}).format(totalNilai)
              : 'Rp 0';
          }

          dashPaginationState.nilai.items = items;
          dashPaginationState.nilai.page = 1;
          renderNilaiPage();
        }

        function renderNilaiPage() {
          var state = dashPaginationState.nilai;
          var tableEl = document.getElementById('nilai-inventory-table');
          if (!tableEl) return;
          var start = (state.page - 1) * DASH_PAGE_SIZE;
          var pageItems = state.items.slice(start, start + DASH_PAGE_SIZE);
          
          tableEl.innerHTML = pageItems.map(function(item, idx) {
            var nilaiFormat = item.nilai > 0
              ? new Intl.NumberFormat('id-ID', {style:'currency', currency:'IDR', maximumFractionDigits:0}).format(item.nilai)
              : 'Rp 0';
            return '<tr>' +
              '<td>' + (start + idx + 1) + '</td>' +
              '<td>' + (item.nama || '-') + '</td>' +
              '<td>' + nilaiFormat + '</td>' +
            '</tr>';
          }).join('');
          
          renderDashPagination('nilai-table-pagination', state, renderNilaiPage);
        }

        // Populate tabel Kondisi Inventaris
        // Tampilkan SEMUA jenis dari availableJenis, yang tidak ada datanya = 0
        function refreshKondisiInventarisDetailTable(data) {
          var tableEl = document.getElementById('kondisi-inventory-detail-table');
          var emptyEl = document.getElementById('kondisi-detail-table-empty-state');
          if (!tableEl) return;

          var availableJenis = data.availableJenis || [];
          var kondisiMap = {};
          (data.inventarisKondisi || []).forEach(function(item) {
            kondisiMap[item.id] = item;
          });

          if (availableJenis.length === 0) {
            if (emptyEl) emptyEl.style.display = 'flex';
            tableEl.innerHTML = '';
            return;
          }

          if (emptyEl) emptyEl.style.display = 'none';

          var items = availableJenis.map(function(jenis) {
            var k = kondisiMap[jenis.id] || {};
            return {
              nama: jenis.nama,
              baik: k.baik || 0,
              rusakRingan: k.rusakRingan || 0,
              rusakBerat: k.rusakBerat || 0
            };
          });
          items.sort(function(a, b) {
            return (b.baik + b.rusakRingan + b.rusakBerat) - (a.baik + a.rusakRingan + a.rusakBerat);
          });

          dashPaginationState.kondisi.items = items;
          dashPaginationState.kondisi.page = 1;
          renderKondisiPage();
        }

        function renderKondisiPage() {
          var state = dashPaginationState.kondisi;
          var tableEl = document.getElementById('kondisi-inventory-detail-table');
          if (!tableEl) return;
          var start = (state.page - 1) * DASH_PAGE_SIZE;
          var pageItems = state.items.slice(start, start + DASH_PAGE_SIZE);
          
          tableEl.innerHTML = pageItems.map(function(item, idx) {
            return '<tr>' +
              '<td>' + (start + idx + 1) + '</td>' +
              '<td>' + (item.nama || '-') + '</td>' +
              '<td>' + item.baik + '</td>' +
              '<td>' + item.rusakRingan + '</td>' +
              '<td>' + item.rusakBerat + '</td>' +
            '</tr>';
          }).join('');
          
          renderDashPagination('kondisi-table-pagination', state, renderKondisiPage);
        }

        // Stub — tidak dipakai lagi
        function updateKondisiTable(data) {}
        function refreshNilaiPerolehanDetailTable(data) {}
        function refreshKondisiInventarisSummaryTable(data) {}

        function populateJenisTable(data) {
          var tableEl = document.getElementById('jenis-inventory-table');
          var emptyEl = document.getElementById('jenis-table-empty-state');
          var totalAssetsEl = document.getElementById('table-total-assets');
          if (!tableEl) return;
          
          var availableJenis = data.availableJenis || [];
          var inventarisByJenis = data.inventarisByJenis || {};
          
          if (availableJenis.length === 0) {
            if (emptyEl) emptyEl.style.display = 'flex';
            tableEl.innerHTML = '';
            if (totalAssetsEl) totalAssetsEl.textContent = '0';
            return;
          }
          
          if (emptyEl) emptyEl.style.display = 'none';
          
          var items = availableJenis.map(function(jenis) {
            var jenisKey = jenis.nama.toLowerCase().replace(/ /g, '_');
            var count = 0;
            for (var key in inventarisByJenis) {
              if (key === jenisKey || key === 'inventaris_' + jenisKey) {
                count = inventarisByJenis[key];
                break;
              }
            }
            return { id: jenis.id, nama: jenis.nama, total: count || 0 };
          });
          
          items.sort(function(a, b) { return b.total - a.total; });
          
          var totalAssets = items.reduce(function(sum, item) { return sum + item.total; }, 0);
          if (totalAssetsEl) totalAssetsEl.textContent = totalAssets.toLocaleString('id-ID');

          dashPaginationState.jenis.items = items;
          dashPaginationState.jenis.page = 1;
          renderJenisPage();
        }

        function renderJenisPage() {
          var state = dashPaginationState.jenis;
          var tableEl = document.getElementById('jenis-inventory-table');
          if (!tableEl) return;
          var start = (state.page - 1) * DASH_PAGE_SIZE;
          var pageItems = state.items.slice(start, start + DASH_PAGE_SIZE);
          
          tableEl.innerHTML = pageItems.map(function(item, idx) {
            return '<tr>' +
              '<td>' + (start + idx + 1) + '</td>' +
              '<td style="text-align: left;">' + item.nama + '</td>' +
              '<td>' + item.total + '</td>' +
            '</tr>';
          }).join('');
          
          renderDashPagination('jenis-table-pagination', state, renderJenisPage);
        }

        function renderDonutChart(data) {
          var pieCtxEl = document.getElementById('chart-jenis-pie');
          if (!pieCtxEl) return;
          if (donutChartInst) { donutChartInst.destroy(); donutChartInst = null; }
          var donutValues = data.donutValues || [];
          var isNilai = (data.donutMetric || 'jumlah') === 'nilai';
          var hasData = donutValues.length > 0 && donutValues.some(function(v) { return v > 0; });
          if (!hasData) {
            pieCtxEl.style.display = 'none';
            document.getElementById('pie-empty-state').style.display = 'flex';
            return;
          }
          pieCtxEl.style.display = 'block';
          document.getElementById('pie-empty-state').style.display = 'none';
          donutChartInst = new Chart(pieCtxEl.getContext('2d'), {
            type: 'doughnut',
            data: {
              labels: data.donutLabels || [],
              datasets: [{ data: donutValues,
                backgroundColor: data.donutColors || ['rgba(99,102,241,0.85)','rgba(245,158,11,0.85)','rgba(16,185,129,0.85)',
                  'rgba(139,92,246,0.85)','rgba(6,182,212,0.85)','rgba(236,72,153,0.85)','rgba(239,68,68,0.85)'],
                borderWidth: 0, hoverOffset: 8 }]
            },
            options: {
              responsive: true, maintainAspectRatio: false, cutout: '60%',
              plugins: {
                legend: { position: 'right', labels: { boxWidth: 14, padding: 16, font: { size: 12 }, usePointStyle: true, pointStyle: 'circle' } },
                tooltip: {
                  backgroundColor: 'rgba(31, 41, 55, 0.95)', titleFont: { size: 13, weight: '600' },
                  bodyFont: { size: 12 }, padding: 12, cornerRadius: 8,
                  callbacks: { label: function(ctx2) {
                    return isNilai ? new Intl.NumberFormat('id-ID',{style:'currency',currency:'IDR',maximumFractionDigits:0}).format(ctx2.raw) : ctx2.raw + ' unit';
                  }}
                }
              }
            }
          });
        }

        function renderLineChart(data) {
          var lineCtxEl = document.getElementById('chart-month-trend');
          if (!lineCtxEl) return;
          if (lineChartInst) { lineChartInst.destroy(); lineChartInst = null; }
          var ctx = lineCtxEl.getContext('2d');
          var gradientFill = ctx.createLinearGradient(0, 0, 0, 200);
          gradientFill.addColorStop(0, 'rgba(81, 146, 89, 0.3)');
          gradientFill.addColorStop(1, 'rgba(81, 146, 89, 0.02)');
          lineChartInst = new Chart(ctx, {
            type: 'line',
            data: {
              labels: (data.monthlyTrend || []).map(function(m) { return m.month || ''; }),
              datasets: [{ label: 'Jumlah Aset',
                data: (data.monthlyTrend || []).map(function(m) { return m.count || 0; }),
                borderColor: '#519259', backgroundColor: gradientFill,
                borderWidth: 3, fill: true, tension: 0.4,
                pointBackgroundColor: '#fff', pointBorderColor: '#519259',
                pointBorderWidth: 2, pointRadius: 4, pointHoverRadius: 6 }]
            },
            options: {
              responsive: true, maintainAspectRatio: false,
              plugins: {
                legend: { display: false },
                tooltip: { backgroundColor: 'rgba(31,41,55,0.95)', titleFont: { size: 13, weight: '600' }, bodyFont: { size: 12 }, padding: 12, cornerRadius: 8 }
              },
              scales: {
                x: { grid: { display: false }, ticks: { font: { size: 10 }, color: '#9ca3af' } },
                y: { grid: { color: 'rgba(0,0,0,0.04)' }, ticks: { font: { size: 10 }, color: '#9ca3af' } }
              }
            }
          });
        }

        // Render the right-side bar chart using fetched chart data
        // right-side sample chart removed

        // ===== FETCH & RE-RENDER =====
        function fetchBarDonut() {
          apiGet(buildChartUrl(getBarDonutParams())).then(function(r) { return r.json(); }).then(function(data) {
            renderBarChart(data); renderDonutChart(data);
            populateNilaiTable(data);
            refreshKondisiInventarisDetailTable(data);
            if (typeof feather !== 'undefined') try { feather.replace(); } catch(e) {}
          });
        }
        function fetchLine() {
          apiGet(buildChartUrl(getLineParams())).then(function(r) { return r.json(); }).then(function(data) {
            renderLineChart(data);
          });
        }

        // Fetch chart data and render charts (include default filter params for sync)
        apiGet(buildChartUrl(Object.assign({}, getBarDonutParams(), getLineParams())))
          .then(r => r.json())
          .then(chartData => {
            // Populate filter dropdowns on initial load
            if (chartData.availableYears) {
              populateYearDropdowns(chartData.availableYears);
              populateJenisDropdown(chartData.availableJenis || []);
            }

            // Update Inventaris per Jenis Cards
            if (chartData.inventarisByJenis) {
              const invData = chartData.inventarisByJenis;
              
              // Hitung total untuk main card
              let totalInv = 0;
              Object.values(invData).forEach(count => totalInv += count);
              if (el('inv-total')) el('inv-total').textContent = totalInv || 0;
              
              // Update setiap jenis inventaris
              if (el('inv-kendaraan')) {
                el('inv-kendaraan').textContent = invData.inventaris_kendaraan_bermotor || invData['inventaris kendaraan bermotor'] || 0;
              }
              if (el('inv-peralatan')) {
                el('inv-peralatan').textContent = invData.inventaris_peralatan_dan_mesin || invData['inventaris peralatan dan mesin'] || 0;
              }
              if (el('inv-bangunan')) {
                el('inv-bangunan').textContent = invData.inventaris_bangunan || invData['inventaris bangunan'] || 0;
              }
              if (el('inv-tanah')) {
                el('inv-tanah').textContent = invData.inventaris_tanah || invData['inventaris tanah'] || 0;
              }
              if (el('inv-jalan')) {
                el('inv-jalan').textContent = invData['inventaris_jalan,_irigasi,_dan_jaringan'] || invData['inventaris jalan, irigasi, dan jaringan'] || 0;
              }
              if (el('inv-lainnya')) {
                el('inv-lainnya').textContent = invData.aset_tetap_lainnya || invData['aset tetap lainnya'] || 0;
              }
              if (el('inv-hilang')) {
                el('inv-hilang').textContent = invData.aset_tidak_ditemukan || invData['aset tidak ditemukan'] || 0;
              }
            }
            
            // Render all charts and populate table
            renderBarChart(chartData);
            renderDonutChart(chartData);
            renderLineChart(chartData);
            populateJenisTable(chartData);
            populateNilaiTable(chartData);
            updateKondisiTable(chartData);
            refreshNilaiPerolehanDetailTable(chartData);
            refreshKondisiInventarisDetailTable(chartData);
            refreshKondisiInventarisSummaryTable(chartData);
            if (typeof feather !== 'undefined') try { feather.replace(); } catch(e) {}

            bindFilterPanelToggles();

            // Init chart year pickers (pakem sistem)
            initChartYearPickers();

            ['filter-bar-metric','filter-bar-tahun','filter-bar-kondisi',
             'filter-donut-metric','filter-donut-tahun','filter-donut-kondisi'].forEach(function(id) {
              var el2 = document.getElementById(id);
              if (el2) el2.addEventListener('change', fetchBarDonut);
            });

            ['filter-line-rentang','filter-line-semester','filter-line-tahun','filter-line-jenis','filter-line-kondisi'].forEach(function(id) {
              var el2 = document.getElementById(id);
              if (el2) el2.addEventListener('change', fetchLine);
            });

            // Toggle semester sub-filter visibility saat rentang berubah
            var rentangEl = document.getElementById('filter-line-rentang');
            if (rentangEl) {
              rentangEl.addEventListener('change', toggleSemesterFilter);
              toggleSemesterFilter(); // set state awal
            }

            document.addEventListener('click', function(evt) {
              var insideAction = evt.target.closest('.chart-export-row');
              var insidePanel = evt.target.closest('.chart-filter-panel');
              if (!insideAction && !insidePanel) {
                closeAllFilterPanels();
              }
            });

            var exportBarBtn = document.getElementById('btn-export-chart-bar');
            if (exportBarBtn) {
              exportBarBtn.addEventListener('click', function() {
                triggerChartExport('bar');
              });
            }

            var exportDonutBtn = document.getElementById('btn-export-chart-donut');
            if (exportDonutBtn) {
              exportDonutBtn.addEventListener('click', function() {
                triggerChartExport('donut');
              });
            }

            var exportLineBtn = document.getElementById('btn-export-chart-line');
            if (exportLineBtn) {
              exportLineBtn.addEventListener('click', function() {
                triggerChartExport('line');
              });
            }


          })
          .catch(err => {
            console.error('Error loading chart data:', err);
            var be = document.getElementById('bar-empty-state'); if (be) be.style.display = 'flex';
            var pe = document.getElementById('pie-empty-state'); if (pe) pe.style.display = 'flex';
            var actEl = document.getElementById('recent-activities'); if (actEl) actEl.innerHTML = '<li style="text-align:center; padding:24px; color:#ef4444; font-style:italic; list-style:none;">Gagal memuat data</li>';
            var expEl = document.getElementById('export-log'); if (expEl) expEl.innerHTML = '<tr><td colspan="3" style="text-align:center; padding:24px; color:#ef4444; font-style:italic;">Gagal memuat data</td></tr>';
          });

        const dashboardAutoRefreshMs = 60000; // 60 detik — cukup untuk dashboard, hindari 429
        setInterval(function () {
          if (document.hidden) return;
          refreshKpiData();
          refreshRecentActivities();
          // pending approvals removed
          refreshExportLogs();
          fetchBarDonut();
          fetchLine();
        }, dashboardAutoRefreshMs);

        // Fetch initial chart data for new tables too — sudah ditangani di fetch utama di atas

      })();
    </script>
  </body>
  <!-- [Body] end -->
</html>
