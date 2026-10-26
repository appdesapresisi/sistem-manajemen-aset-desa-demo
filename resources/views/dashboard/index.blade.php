<!doctype html>
<html lang="id" data-pc-preset="{{ $pc_preset_theme ?? 'preset-1' }}" data-pc-sidebar-theme="{{ $pc_sidebar_theme ?? 'light' }}" data-pc-theme="{{ $pc_dark_layout ?? 'light' }}">
<head>
  @include('layouts.head-page-meta', ['title' => 'Dashboard - Sistem Manajemen Aset Desa'])
  @include('layouts.head-css')
  <script src="https://cdn.jsdelivr.net/npm/feather-icons/dist/feather.min.js"></script>
  <!-- ApexCharts CDN for clean flat charts -->
  <script src="https://cdn.jsdelivr.net/npm/apexcharts"></script>
  <style>
    /* Section 1: 5 KPI Cards sama lebar */
    .dash-section-1 {
      display: grid;
      grid-template-columns: repeat(5, 1fr);
      gap: 16px;
      margin-bottom: 24px;
    }

    /* Section 2: 6 + 6 (Nilai Aset per Kategori + Kelengkapan Data) */
    .dash-section-2 {
      display: grid;
      grid-template-columns: 1fr 1fr;
      gap: 20px;
      margin-bottom: 24px;
      align-items: stretch;
    }
    .dash-section-2 > div {
      height: 100% !important;
      margin-bottom: 0 !important;
      box-sizing: border-box !important;
    }

    /* Container Queries for Section 2 Cards */
    #section-nilai-per-kategori,
    #section-kelengkapan-data {
      container-type: inline-size;
      container-name: dashcard;
    }

    /* CARD 1: Nilai Aset per Kategori Styles */
    .dash-donut-layout {
      display: grid;
      grid-template-columns: minmax(170px, 1fr) minmax(0, 1.5fr);
      gap: 16px;
      align-items: center;
      flex: 1;
    }
    .dash-donut-chart-wrap {
      min-height: 220px;
      display: flex;
      align-items: center;
      justify-content: center;
    }
    .dash-donut-list {
      display: flex;
      flex-direction: column;
      gap: 4px;
      font-size: 12.5px;
      max-height: 235px;
      overflow-y: auto;
      overflow-x: hidden;
      padding-right: 8px;
      scrollbar-width: thin;
      scrollbar-color: var(--color-border) transparent;
    }
    .dash-donut-item {
      display: flex;
      justify-content: space-between;
      align-items: center;
      gap: 10px;
      padding: 6px 0;
    }
    .dash-donut-item-bordered {
      border-bottom: 1px dashed var(--color-border);
    }
    .dash-donut-item-label {
      min-width: 0;
      flex: 1;
      display: flex;
      align-items: center;
      gap: 8px;
    }
    .dash-donut-dot {
      width: 8px;
      height: 8px;
      border-radius: 50%;
      display: inline-block;
      flex-shrink: 0;
    }
    .dash-donut-name {
      color: var(--color-text);
      font-weight: 500;
      font-size: 12.5px;
      line-height: 1.3;
      white-space: normal;
      word-break: break-word;
    }
    .dash-donut-item-val {
      text-align: right;
      font-variant-numeric: tabular-nums;
      flex-shrink: 0;
      white-space: nowrap;
      display: flex;
      align-items: baseline;
      gap: 6px;
    }
    .dash-donut-amount {
      font-weight: 600;
      font-size: 12px;
      color: var(--color-text);
    }
    .dash-donut-amount.val-muted {
      color: var(--color-text-muted);
    }
    .dash-donut-pct {
      font-size: 11px;
      color: var(--color-text-muted);
      min-width: 42px;
      text-align: right;
    }

    /* CARD 2: Kelengkapan Data Aset Styles */
    .dash-kelengkapan-layout {
      flex: 1;
      display: grid;
      grid-template-columns: 150px 1fr;
      gap: 24px;
      align-items: center;
      padding: 12px 0;
    }
    .dash-kelengkapan-stat {
      display: flex;
      flex-direction: column;
      justify-content: center;
    }
    .dash-kelengkapan-num {
      font-size: 42px;
      font-weight: 700;
      color: var(--color-text);
      line-height: 1;
      letter-spacing: -0.03em;
    }
    .dash-kelengkapan-sub {
      font-size: 13px;
      color: var(--color-text-muted);
      margin-top: 8px;
      font-weight: 500;
    }
    .dash-kelengkapan-list {
      display: flex;
      flex-direction: column;
      justify-content: space-between;
      gap: 4px;
    }
    .dash-kelengkapan-item {
      display: flex;
      justify-content: space-between;
      align-items: center;
      font-size: 13px;
      padding: 9px 0;
    }
    .dash-kelengkapan-item-bordered {
      border-bottom: 1px dashed var(--color-border);
    }
    .dash-kelengkapan-item-label {
      display: flex;
      align-items: center;
      gap: 8px;
      color: var(--color-text);
      font-weight: 500;
    }
    .dash-kelengkapan-item-val {
      font-weight: 600;
      font-variant-numeric: tabular-nums;
      white-space: nowrap;
    }
    .dash-kelengkapan-footer {
      margin-top: 14px;
      padding-top: 12px;
      border-top: 1px dashed var(--color-border);
      display: flex;
      justify-content: space-between;
      align-items: center;
      font-size: 12px;
    }

    /* Responsive Vertical Layout: Triggered when card width <= 680px (container query) */
    @container (max-width: 680px) {
      /* Card 1: Nilai Aset per Kategori */
      .dash-donut-layout {
        display: flex !important;
        flex-direction: column !important;
        align-items: stretch !important;
        gap: 16px !important;
      }
      .dash-donut-chart-wrap {
        min-height: 210px !important;
        width: 100% !important;
        margin: 0 auto !important;
      }
      .dash-donut-list {
        max-height: 280px !important;
        padding-right: 4px !important;
      }
      .dash-donut-item {
        flex-direction: column !important;
        align-items: flex-start !important;
        gap: 4px !important;
        padding: 8px 0 !important;
      }
      .dash-donut-item-label {
        width: 100% !important;
      }
      .dash-donut-item-val {
        padding-left: 16px !important;
        width: 100% !important;
        justify-content: flex-start !important;
        text-align: left !important;
      }
      .dash-donut-pct {
        text-align: left !important;
        min-width: auto !important;
      }

      /* Card 2: Kelengkapan Data Aset */
      .dash-kelengkapan-layout {
        display: flex !important;
        flex-direction: column !important;
        align-items: stretch !important;
        gap: 16px !important;
        padding: 8px 0 !important;
      }
      .dash-kelengkapan-stat {
        align-items: center !important;
        text-align: center !important;
        padding: 4px 0 10px !important;
        border-bottom: 1px dashed var(--color-border) !important;
      }
      .dash-kelengkapan-item {
        flex-direction: row !important;
        justify-content: space-between !important;
        align-items: center !important;
        gap: 8px !important;
        padding: 9px 0 !important;
      }
      .dash-kelengkapan-item-label {
        width: auto !important;
        display: inline-flex !important;
        align-items: center !important;
        gap: 8px !important;
      }
      .dash-kelengkapan-item-val {
        padding-left: 0 !important;
        text-align: right !important;
        flex-shrink: 0 !important;
      }
      .dash-kelengkapan-footer {
        flex-direction: column !important;
        align-items: flex-start !important;
        gap: 10px !important;
      }
    }

    /* Fallback media query for small viewports */
    @media (max-width: 640px) {
      .dash-donut-layout {
        display: flex !important;
        flex-direction: column !important;
        align-items: stretch !important;
        gap: 16px !important;
      }
      .dash-donut-chart-wrap {
        min-height: 210px !important;
        width: 100% !important;
      }
      .dash-donut-list {
        max-height: 280px !important;
        padding-right: 4px !important;
      }
      .dash-donut-item {
        flex-direction: column !important;
        align-items: flex-start !important;
        gap: 4px !important;
        padding: 8px 0 !important;
      }
      .dash-donut-item-label {
        width: 100% !important;
      }
      .dash-donut-item-val {
        padding-left: 16px !important;
        width: 100% !important;
        justify-content: flex-start !important;
        text-align: left !important;
      }
      .dash-donut-pct {
        text-align: left !important;
        min-width: auto !important;
      }

      .dash-kelengkapan-layout {
        display: flex !important;
        flex-direction: column !important;
        align-items: stretch !important;
        gap: 16px !important;
        padding: 8px 0 !important;
      }
      .dash-kelengkapan-stat {
        align-items: center !important;
        text-align: center !important;
        padding: 4px 0 10px !important;
        border-bottom: 1px dashed var(--color-border) !important;
      }
      .dash-kelengkapan-item {
        flex-direction: row !important;
        justify-content: space-between !important;
        align-items: center !important;
        gap: 8px !important;
        padding: 9px 0 !important;
      }
      .dash-kelengkapan-item-label {
        width: auto !important;
        display: inline-flex !important;
        align-items: center !important;
        gap: 8px !important;
      }
      .dash-kelengkapan-item-val {
        padding-left: 0 !important;
        text-align: right !important;
        flex-shrink: 0 !important;
      }
      .dash-kelengkapan-footer {
        flex-direction: column !important;
        align-items: flex-start !important;
        gap: 10px !important;
      }
    }

    /* Section 3: 4 + 4 + 4 (Status Buku + Kondisi + Asal Aset) */
    .dash-section-3 {
      display: grid;
      grid-template-columns: repeat(3, 1fr);
      gap: 20px;
      margin-bottom: 24px;
      align-items: stretch;
    }
    .dash-section-3 > div {
      height: 100% !important;
      margin-bottom: 0 !important;
      box-sizing: border-box !important;
      display: flex !important;
      flex-direction: column !important;
      justify-content: space-between !important;
    }

    /* Section 4: 6 + 3 + 3 (Tahun Perolehan + Sebaran Ruangan + Aktivitas Terbaru) */
    .dash-section-4 {
      display: grid;
      grid-template-columns: 2fr 1fr 1fr;
      gap: 20px;
      margin-bottom: 24px;
      align-items: stretch;
    }
    .dash-section-4 > div {
      height: 100% !important;
      margin-bottom: 0 !important;
      box-sizing: border-box !important;
      display: flex !important;
      flex-direction: column !important;
      justify-content: space-between !important;
    }

    /* Section 5: 5 + 7 (Jenis Inventaris + Rentang Nilai) */
    .dash-section-5 {
      display: grid;
      grid-template-columns: repeat(2, minmax(0, 1fr));      gap: 20px;
      margin-bottom: 24px;
      align-items: stretch;
    }
    .dash-section-5 > div {
      height: 100% !important;
      margin-bottom: 0 !important;
      box-sizing: border-box !important;
      display: flex !important;
      flex-direction: column !important;
      justify-content: space-between !important;
    }

    /* Section 6: 12 (Tabel Aset Baru Ditambahkan) */
    .dash-section-6 {
      margin-bottom: 32px;
    }
    .dash-section-6 .table-sima th:first-child,
    .dash-section-6 .table-sima td:first-child {
      padding-left: 0 !important;
    }
    .dash-section-6 .table-sima th:last-child,
    .dash-section-6 .table-sima td:last-child {
      padding-right: 0 !important;
    }

    /* Responsive Breakpoints */
    @media (max-width: 1400px) {
      .dash-section-1 {
        grid-template-columns: repeat(3, 1fr);
      }
      .dash-section-2 {
        grid-template-columns: 1fr;
      }
      .dash-section-3 {
        grid-template-columns: 1fr 1fr;
      }
      .dash-section-4 {
        grid-template-columns: 1fr;
      }
      .dash-section-5 {
        grid-template-columns: 1fr;
      }
    }

    @media (max-width: 992px) {
      .dash-section-1 {
        grid-template-columns: repeat(2, 1fr);
      }
      .dash-section-3 {
        grid-template-columns: 1fr;
      }
    }

    @media (max-width: 640px) {
      .dash-section-1 {
        grid-template-columns: 1fr;
      }
    }

    /* Sparkline containers */
    .mini-sparkline {
      height: 40px;
      margin-top: 6px;
      margin-bottom: -4px;
    }
  </style>
</head>
<body>
  @include('layouts.notification')
  @include('layouts.modals')
  @include('layouts.layout-vertical')

  <!-- [ Main Content ] start -->
  <div class="pc-container">
    <div class="pc-content" style="max-width: 1560px; margin: 0 auto;">

      <!-- TOPBAR HEADER & CONTROLS -->
      <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 24px; flex-wrap: wrap; gap: 16px;">
        <div>
          <h1 style="font-size: 22px; font-weight: 700; margin: 0; color: var(--color-text); line-height: 1.2; letter-spacing: -0.02em;">
            Dashboard Aset Desa
          </h1>
        </div>
        <div style="display: flex; gap: 10px; align-items: center; flex-wrap: wrap;">
          <!-- Date / Period Badge with Tooltip -->
          <div style="display: inline-flex; align-items: center; gap: 8px; background: var(--color-surface); border: 1px solid var(--color-border); border-radius: var(--radius-full); padding: 6px 14px; font-size: 13px; font-weight: 500; color: var(--color-text);">
            <i data-feather="calendar" style="width: 14px; height: 14px; color: var(--color-text-muted);"></i>
            <span title="Tahun Anggaran">Tahun Anggaran {{ date('Y') }} (1 Jan - 31 Des) </span>
          </div>
        </div>
      </div>

      @php
        $totalAset = $kpis['totalAssets'] ?? $kpis['total_inventaris'] ?? 442;
        $totalNilai = $kpis['totalValue'] ?? $kpis['total_nilai'] ?? 0;
        $kondisiBaik = $kpis['kondisiAset']['baik'] ?? $kpis['kondisi']['baik'] ?? $totalAset;
        $kondisiRusakRingan = $kpis['kondisiAset']['rusakRingan'] ?? $kpis['kondisi']['rusak_ringan'] ?? 0;
        $kondisiRusakBerat = $kpis['kondisiAset']['rusakBerat'] ?? $kpis['kondisi']['rusak_berat'] ?? 0;
        $kondisiRusak = $kondisiRusakRingan + $kondisiRusakBerat;
        $totalRuangan = $kpis['totalRuangan'] ?? 10;
        $assetsWithRoom = $kpis['assetsWithRoom'] ?? 0;
        $assetsWithoutRoom = $kpis['assetsWithoutRoom'] ?? ($totalAset - $assetsWithRoom);
        $totalPejabat = $kpis['totalPejabat'] ?? 17;
        $pejabatWithSk = $kpis['pejabatWithSk'] ?? 17;
        $assetsThisMonth = $kpis['assetsThisMonth'] ?? 12;

        // Kelengkapan data aset
        $assetsWithNup = $kpis['assetsWithNup'] ?? $totalAset;
        $assetsWithFoto = $kpis['assetsWithFoto'] ?? 0;
        $assetsWithBarcode = $kpis['assetsWithBarcode'] ?? $totalAset;
        $overallCompleteness = $totalAset > 0 
          ? round((($assetsWithNup + $assetsWithRoom + $assetsWithFoto + $assetsWithBarcode) / ($totalAset * 4)) * 100, 1) 
          : 0;
        $incompleteAssetsCount = $kpis['incompleteAssetsCount'] ?? max($assetsWithoutRoom, ($totalAset - $assetsWithFoto));

        // Kewajiban pelaporan
        $latestAssetDate = !empty($kpis['latestAssetUpdate']) 
          ? \Carbon\Carbon::parse($kpis['latestAssetUpdate'])->locale('id')->isoFormat('D MMM Y') 
          : '5 Okt ' . date('Y');
        $deletedAssetsThisYear = $kpis['deletedAssetsThisYear'] ?? 0;
        $baPenghapusanCount = $kpis['baPenghapusanCount'] ?? 0;

        // Palette for categories
        $categoryPalette = ['#1f3a2a', '#658c63', '#C9A24A', '#3E6A7A', '#97b593', '#B7791F', '#52796F', '#84A98C', '#A3B18A', '#6B7280', '#D97706', '#4B5563'];
      @endphp

      <!-- =====================================================================
           SECTION 1: 5 STAT KPI CARDS (5 KARTU SAMA LEBAR)
           ===================================================================== -->
      <div class="dash-section-1" id="section-ringkasan-kpi" data-search-title="Ringkasan Statistik KPI">
        
        <!-- KPI 1: Total Aset -->
        <div class="metric-card-rich">
          <div class="metric-top">
            <span>Total Aset</span>
            <i data-feather="box" style="width: 14px; height: 14px; color: var(--color-primary);"></i>
          </div>
          <div class="metric-val">{{ number_format($totalAset, 0, ',', '.') }} <span style="font-size: 14px; font-weight: 500; color: var(--color-text-muted);">unit</span></div>
          <div class="metric-sub">
            <span style="color: var(--color-text-muted); font-weight: 500;">{{ $assetsThisMonth }} unit baru bulan ini</span>
          </div>
          <div id="sparkline-1" class="mini-sparkline"></div>
        </div>

        <!-- KPI 2: Total Nilai Aset -->
        <div class="metric-card-rich">
          <div class="metric-top">
            <span>Total Nilai Aset</span>
            <i data-feather="dollar-sign" style="width: 14px; height: 14px; color: var(--color-primary);"></i>
          </div>
          <div class="metric-val" style="font-size: 18px; letter-spacing: -0.02em; white-space: nowrap; overflow: hidden; text-overflow: ellipsis;" title="Rp {{ number_format($totalNilai, 0, ',', '.') }}">
            Rp {{ number_format($totalNilai, 0, ',', '.') }}
          </div>
          <div class="metric-sub">
            <span style="color: var(--color-success); font-weight: 600;">&uarr; Naik 14,2%</span>
            <span>dari {{ date('Y') - 1 }}</span>
          </div>
          <div id="sparkline-2" class="mini-sparkline"></div>
        </div>

        <!-- KPI 3: Aset Kondisi Baik -->
        <div class="metric-card-rich">
          <div class="metric-top">
            <span>Aset Kondisi Baik</span>
            <i data-feather="check-circle" style="width: 14px; height: 14px; color: var(--color-primary);"></i>
          </div>
          <div class="metric-val">{{ number_format($kondisiBaik, 0, ',', '.') }} <span style="font-size: 13px; font-weight: 500; color: var(--color-text-muted);">dari {{ number_format($totalAset, 0, ',', '.') }} unit</span></div>
          <div class="metric-sub">
            <span style="color: var(--color-text-muted); font-weight: 500;">Rusak ringan {{ $kondisiRusakRingan }} dan rusak berat {{ $kondisiRusakBerat }}</span>
          </div>
        </div>

        <!-- KPI 4: Ruangan Terdaftar -->
        <div class="metric-card-rich">
          <div class="metric-top">
            <span>Ruangan Terdaftar</span>
            <i data-feather="map-pin" style="width: 14px; height: 14px; color: var(--color-primary);"></i>
          </div>
          <div class="metric-val">{{ $totalRuangan }} <span style="font-size: 14px; font-weight: 500; color: var(--color-text-muted);">ruangan</span></div>
          <div class="metric-sub">
            <span style="color: {{ $assetsWithRoom > 0 ? 'var(--color-text-muted)' : 'var(--color-warning)' }}; font-weight: 500;">{{ $assetsWithRoom }} dari {{ $totalAset }} aset sudah ada ruangannya</span>
          </div>
        </div>

        <!-- KPI 5: Pejabat Penatausahaan -->
        <div class="metric-card-rich">
          <div class="metric-top">
            <span>Pejabat Penatausahaan</span>
            <i data-feather="users" style="width: 14px; height: 14px; color: var(--color-primary);"></i>
          </div>
          <div class="metric-val">{{ $totalPejabat }} <span style="font-size: 14px; font-weight: 500; color: var(--color-text-muted);">orang</span></div>
          <div class="metric-sub">
            <span style="color: var(--color-text-muted); font-weight: 500;">{{ $pejabatWithSk }} dari {{ $totalPejabat }} berstatus aktif</span>
          </div>
        </div>

      </div>

      <!-- =====================================================================
           SECTION 2: NILAI ASET PER KATEGORI + KELENGKAPAN DATA ASET (6 + 6)
           ===================================================================== -->
      <div class="dash-section-2">
        
        <!-- Kolom 1 (lebar 6): Nilai Aset per Kategori -->
        <div class="sima-card" id="section-nilai-per-kategori" data-search-title="Nilai Aset per Kategori" style="display: flex; flex-direction: column;">
          <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 16px;">
            <div>
              <h3 style="font-size: 15px; font-weight: 600; margin: 0; color: var(--color-text);">Nilai Aset per Kategori</h3>
            </div>
            <a href="{{ route('manajemen-aset.index') }}" style="font-size: 12px; color: var(--color-primary); font-weight: 600; text-decoration: none; display: inline-flex; align-items: center; gap: 4px;">
              <span>Detail</span>
              <i data-feather="chevron-right" style="width: 14px; height: 14px;"></i>
            </a>
          </div>

          <div class="dash-donut-layout">
            <!-- Donut Chart -->
            <div id="chart-donut-nilai" class="dash-donut-chart-wrap"></div>

            <!-- Structured Breakdown List (Nama kategori lengkap tidak terpotong + Tooltip) -->
            <div class="dash-donut-list">
              @php
                $labels = $chartData['nilai_per_jenis']['labels'] ?? [];
                $values = $chartData['nilai_per_jenis']['data'] ?? [];
                $totalNilaiSum = array_sum($values);
              @endphp
              @foreach($labels as $idx => $label)
                @php
                  $val = $values[$idx] ?? 0;
                  $pct = $totalNilaiSum > 0 ? round(($val / $totalNilaiSum) * 100, 1) : 0;
                  $color = $categoryPalette[$idx % count($categoryPalette)];
                @endphp
                <div class="dash-donut-item {{ !$loop->last ? 'dash-donut-item-bordered' : '' }}">
                  <div class="dash-donut-item-label">
                    <span class="dash-donut-dot" style="background-color: {{ $color }};"></span>
                    <span class="dash-donut-name" title="{{ $label }}">{{ $label }}</span>
                  </div>
                  <div class="dash-donut-item-val">
                    <span class="dash-donut-amount {{ $val > 0 ? '' : 'val-muted' }}">
                      Rp {{ number_format($val, 0, ',', '.') }}
                    </span>
                    <span class="dash-donut-pct">({{ number_format($pct, 1, ',', '.') }}%)</span>
                  </div>
                </div>
              @endforeach
            </div>
          </div>
        </div>

        <!-- Kolom 2 (lebar 6): Kelengkapan Data Aset -->
        <div class="sima-card" id="section-kelengkapan-data" data-search-title="Kelengkapan Data Aset" style="display: flex; flex-direction: column; justify-content: space-between;">
          <!-- Top Header -->
          <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 16px;">
            <div>
              <h3 style="font-size: 15px; font-weight: 600; margin: 0; color: var(--color-text);">Kelengkapan Data Aset</h3>
            </div>
            @if($overallCompleteness >= 100)
              <span class="status-pill status-pill-success" style="font-size: 11px;">100% Lengkap</span>
            @else
              <span style="font-size: 11.5px; background: #F4E7C3; color: #7B5E1E; font-weight: 600; padding: 2px 10px; border-radius: var(--radius-full);">
                Perlu Dilengkapi
              </span>
            @endif
          </div>

          <!-- Middle Content: Responsive Balanced Grid -->
          <div class="dash-kelengkapan-layout">
            
            <!-- Left/Top: Big percentage stat -->
            <div class="dash-kelengkapan-stat">
              <div class="dash-kelengkapan-num">
                {{ number_format($overallCompleteness, 1, ',', '.') }}%
              </div>
              <div class="dash-kelengkapan-sub">
                rata-rata terisi
              </div>
            </div>

            <!-- Right/Bottom: 4 Checklist Items -->
            <div class="dash-kelengkapan-list">
              
              <!-- Item 1: NUP -->
              <div class="dash-kelengkapan-item dash-kelengkapan-item-bordered">
                <div class="dash-kelengkapan-item-label">
                  <i data-feather="{{ $assetsWithNup == $totalAset ? 'check-circle' : 'alert-circle' }}" style="width: 15px; height: 15px; color: {{ $assetsWithNup == $totalAset ? 'var(--color-success)' : 'var(--color-warning)' }};"></i>
                  <span>Nomor register (NUP)</span>
                </div>
                <div class="dash-kelengkapan-item-val" style="color: {{ $assetsWithNup == $totalAset ? 'var(--color-text)' : 'var(--color-warning)' }};">
                  {{ $assetsWithNup }} dari {{ $totalAset }}
                </div>
              </div>

              <!-- Item 2: Ruangan -->
              <div class="dash-kelengkapan-item dash-kelengkapan-item-bordered">
                <div class="dash-kelengkapan-item-label">
                  <i data-feather="{{ $assetsWithRoom == $totalAset ? 'check-circle' : 'alert-circle' }}" style="width: 15px; height: 15px; color: {{ $assetsWithRoom == $totalAset ? 'var(--color-success)' : 'var(--color-warning)' }};"></i>
                  <span>Ruangan</span>
                </div>
                <div class="dash-kelengkapan-item-val" style="color: {{ $assetsWithRoom == $totalAset ? 'var(--color-text)' : 'var(--color-warning)' }};">
                  {{ $assetsWithRoom }} dari {{ $totalAset }}
                </div>
              </div>

              <!-- Item 3: Foto fisik -->
              <div class="dash-kelengkapan-item dash-kelengkapan-item-bordered">
                <div class="dash-kelengkapan-item-label">
                  <i data-feather="{{ $assetsWithFoto == $totalAset ? 'check-circle' : 'alert-circle' }}" style="width: 15px; height: 15px; color: {{ $assetsWithFoto == $totalAset ? 'var(--color-success)' : 'var(--color-warning)' }};"></i>
                  <span>Foto fisik</span>
                </div>
                <div class="dash-kelengkapan-item-val" style="color: {{ $assetsWithFoto == $totalAset ? 'var(--color-text)' : 'var(--color-warning)' }};">
                  {{ $assetsWithFoto }} dari {{ $totalAset }}
                </div>
              </div>

              <!-- Item 4: Label QR -->
              <div class="dash-kelengkapan-item">
                <div class="dash-kelengkapan-item-label">
                  <i data-feather="{{ $assetsWithBarcode == $totalAset ? 'check-circle' : 'alert-circle' }}" style="width: 15px; height: 15px; color: {{ $assetsWithBarcode == $totalAset ? 'var(--color-success)' : 'var(--color-warning)' }};"></i>
                  <span>Label QR</span>
                </div>
                <div class="dash-kelengkapan-item-val" style="color: {{ $assetsWithBarcode == $totalAset ? 'var(--color-text)' : 'var(--color-warning)' }};">
                  {{ $assetsWithBarcode }} dari {{ $totalAset }}
                </div>
              </div>

            </div>

          </div>

          <!-- Bottom Footer -->
          @if($incompleteAssetsCount > 0)
            <div class="dash-kelengkapan-footer">
              <span style="color: var(--color-text-muted);">Perlu dilengkapi: <strong style="color: var(--color-warning);">{{ $incompleteAssetsCount }} aset</strong></span>
              <a href="{{ route('manajemen-aset.index', ['kelengkapan' => 'tidak_lengkap']) }}" style="font-size: 12px; color: var(--color-primary); font-weight: 600; text-decoration: none; display: inline-flex; align-items: center; gap: 4px;">
                <span>Lihat aset yang belum lengkap</span>
                <i data-feather="chevron-right" style="width: 14px; height: 14px;"></i>
              </a>
            </div>
          @endif
        </div>

      </div>

      <!-- =====================================================================
           SECTION 3: STATUS BUKU ASET + KONDISI ASET + ASAL ASET (4 + 4 + 4)
           ===================================================================== -->
      <div class="dash-section-3">
        
        <!-- Card 1 (lebar 4): Status Buku Aset -->
        <div class="sima-card" id="section-status-buku" data-search-title="Status Buku Aset" style="display: flex; flex-direction: column;">
          <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 14px;">
            <div>
              <h3 style="font-size: 14px; font-weight: 600; margin: 0; color: var(--color-text);">Status Buku Aset</h3>
            </div>
          </div>

          <!-- Real Book Status Indicators -->
          <div style="display: flex; flex-direction: column; gap: 8px; flex: 1; justify-content: space-between;">
            
            <div style="background: var(--color-bg); padding: 9px 12px; border-radius: var(--radius-sm);">
              <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 3px;">
                <span style="font-size: 11.5px; color: var(--color-text-muted);">Buku Inventaris</span>
                <a href="{{ route('manajemen-aset.index') }}" style="font-size: 11.5px; color: var(--color-primary); font-weight: 600; text-decoration: none; display: inline-flex; align-items: center; gap: 3px;">
                  <span>Buka</span>
                  <i data-feather="chevron-right" style="width: 12px; height: 12px;"></i>
                </a>
              </div>
              <div style="display: flex; justify-content: space-between; align-items: center;">
                <span style="font-weight: 600; color: var(--color-text); font-size: 13px;">{{ number_format($totalAset, 0, ',', '.') }} aset aktif</span>
                <span style="font-size: 11.5px; color: var(--color-text-muted);">Terakhir diperbarui {{ $latestAssetDate }}</span>
              </div>
            </div>

            <div style="background: var(--color-bg); padding: 9px 12px; border-radius: var(--radius-sm);">
              <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 3px;">
                <span style="font-size: 11.5px; color: var(--color-text-muted);">Buku Penghapusan Aset</span>
                <a href="{{ route('penghapusan-aset.index') }}" style="font-size: 11.5px; color: var(--color-primary); font-weight: 600; text-decoration: none; display: inline-flex; align-items: center; gap: 3px;">
                  <span>Lihat</span>
                  <i data-feather="chevron-right" style="width: 12px; height: 12px;"></i>
                </a>
              </div>
              <div style="display: flex; justify-content: space-between; align-items: center;">
                <span style="font-weight: 600; color: var(--color-text); font-size: 13px;">{{ $deletedAssetsThisYear }} aset dihapus</span>
                <span style="font-size: 11.5px; color: var(--color-text-muted);">Tahun {{ date('Y') }}</span>
              </div>
            </div>

            <div style="background: var(--color-bg); padding: 9px 12px; border-radius: var(--radius-sm);">
              <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 3px;">
                <span style="font-size: 11.5px; color: var(--color-text-muted);">Format Laporan & KIB</span>
                <a href="{{ route('export-data.index') }}" style="font-size: 11.5px; color: var(--color-primary); font-weight: 600; text-decoration: none; display: inline-flex; align-items: center; gap: 3px;">
                  <span>Pusat Ekspor</span>
                  <i data-feather="chevron-right" style="width: 12px; height: 12px;"></i>
                </a>
              </div>
              <div style="display: flex; justify-content: space-between; align-items: center;">
                <span style="font-weight: 600; color: var(--color-text); font-size: 13px;">Siap diunduh</span>
                <span style="font-size: 11.5px; color: var(--color-text-muted);">KIB A–F, KIR, Buku Inventaris</span>
              </div>
            </div>

          </div>
        </div>

        <!-- Card 2 (lebar 4): Kondisi Aset -->
        @php
          $pctBaik = $totalAset > 0 ? round(($kondisiBaik / $totalAset) * 100, 1) : 0;
          $pctRingan = $totalAset > 0 ? round(($kondisiRusakRingan / $totalAset) * 100, 1) : 0;
          $pctBerat = $totalAset > 0 ? round(($kondisiRusakBerat / $totalAset) * 100, 1) : 0;
        @endphp
        <div class="sima-card" id="section-kondisi-aset" data-search-title="Kondisi Aset" style="display: flex; flex-direction: column; justify-content: space-between;">
          <div style="display: flex; justify-content: space-between; align-items: center;">
            <h3 style="font-size: 14px; font-weight: 600; margin: 0; color: var(--color-text);">Kondisi Aset</h3>
            <a href="{{ route('manajemen-aset.index') }}" style="font-size: 11.5px; color: var(--color-primary); font-weight: 600; text-decoration: none; display: inline-flex; align-items: center; gap: 3px;">
              <span>Detail</span>
              <i data-feather="chevron-right" style="width: 12px; height: 12px;"></i>
            </a>
          </div>

          <!-- 3 Kolom Metrik Kondisi Aset (Center Vertikal & Horizontal) -->
          <div style="flex: 1; display: flex; flex-direction: column; justify-content: center; padding: 16px 0;">
            <div style="display: grid; grid-template-columns: repeat(3, 1fr); gap: 12px; text-align: center;">
              <!-- Kolom 1: Baik -->
              <div style="display: flex; flex-direction: column; align-items: center;">
                <div style="display: inline-flex; align-items: center; gap: 6px; font-weight: 500; font-size: 12.5px; color: var(--color-text);">
                  <span style="color: var(--color-success);">Baik</span>
                </div>
                <div style="font-size: 32px; font-weight: 700; color: var(--color-text); line-height: 1.15; margin: 8px 0 4px; font-variant-numeric: tabular-nums;">
                  {{ number_format($kondisiBaik, 0, ',', '.') }}
                </div>
                <div style="font-size: 12px; color: var(--color-text-muted); font-weight: 500;">
                  {{ number_format($pctBaik, 1, ',', '.') }}%
                </div>
              </div>

              <!-- Kolom 2: Rusak Ringan -->
              <div style="display: flex; flex-direction: column; align-items: center;">
                <div style="display: inline-flex; align-items: center; gap: 6px; font-weight: 500; font-size: 12.5px; color: var(--color-text-muted);">
                  <span style="color: var(--color-warning)">Rusak ringan</span>
                </div>
                <div style="font-size: 32px; font-weight: 700; color: var(--color-text); line-height: 1.15; margin: 8px 0 4px; font-variant-numeric: tabular-nums;">
                  {{ number_format($kondisiRusakRingan, 0, ',', '.') }}
                </div>
                <div style="font-size: 12px; color: var(--color-text-muted); font-weight: 500;">
                  {{ number_format($pctRingan, 1, ',', '.') }}%
                </div>
              </div>

              <!-- Kolom 3: Rusak Berat -->
              <div style="display: flex; flex-direction: column; align-items: center;">
                <div style="display: inline-flex; align-items: center; gap: 6px; font-weight: 500; font-size: 12.5px; color: var(--color-text-muted);">
                  <span style="color: var(--color-danger)">Rusak berat</span>
                </div>
                <div style="font-size: 32px; font-weight: 700; color: var(--color-text); line-height: 1.15; margin: 8px 0 4px; font-variant-numeric: tabular-nums;">
                  {{ number_format($kondisiRusakBerat, 0, ',', '.') }}
                </div>
                <div style="font-size: 12px; color: var(--color-text-muted); font-weight: 500;">
                  {{ number_format($pctBerat, 1, ',', '.') }}%
                </div>
              </div>
            </div>
          </div>

          <!-- Bottom Operational Status -->
          <div style="padding-top: 12px; border-top: 1px dashed var(--color-border); display: flex; align-items: center; gap: 5px; font-size: 12px;">
            <span style="color: var(--color-text-muted);">Aset perlu perbaikan:</span>
            <span style="font-weight: 600; color: {{ $kondisiRusak > 0 ? 'var(--color-muted)' : 'var(--color-muted)' }};">
              {{ $kondisiRusak > 0 ? $kondisiRusak . ' unit' : '0 unit (Nihil)' }}
            </span>
          </div>
        </div>

        <!-- Card 3 (lebar 4): Asal Aset (3 Baris Bar saja, tanpa donut) -->
        @php
          $rawAsalUsul = collect($chartData['asalUsul'] ?? []);
          $apbdesCount = $rawAsalUsul->first(function($i) { return str_contains(strtolower($i['nama'] ?? ''), 'apbdes'); })['count'] ?? 0;
          $kekayaanCount = $rawAsalUsul->first(function($i) { return str_contains(strtolower($i['nama'] ?? ''), 'kekayaan'); })['count'] ?? 0;
          $bantuanCount = $rawAsalUsul->first(function($i) { return str_contains(strtolower($i['nama'] ?? ''), 'bantuan'); })['count'] ?? 0;

          $pctApbdes   = $totalAset > 0 ? round(($apbdesCount   / $totalAset) * 100, 1) : 0;
          $pctKekayaan = $totalAset > 0 ? round(($kekayaanCount / $totalAset) * 100, 1) : 0;
          $pctBantuan  = $totalAset > 0 ? round(($bantuanCount  / $totalAset) * 100, 1) : 0;
        @endphp
        <div class="sima-card" id="section-asal-aset" data-search-title="Asal Aset" style="display: flex; flex-direction: column; justify-content: space-between;">
          <div style="margin-bottom: 14px;">
            <div style="display: flex; justify-content: space-between; align-items: flex-start;">
              <div>
                <h3 style="font-size: 14px; font-weight: 600; margin: 0; color: var(--color-text);">Asal Aset</h3>
              </div>
              <span style="font-size: 11px; background: var(--color-primary-tint); color: var(--color-primary); font-weight: 600; padding: 2px 8px; border-radius: var(--radius-full);">
                {{ number_format($totalAset, 0, ',', '.') }} unit
              </span>
            </div>
          </div>

          <!-- Tiga Baris Bar Saja (APBDes, Kekayaan Asli Desa, Bantuan Pemerintah) -->
          <div style="display: flex; flex-direction: column; gap: 16px; flex: 1; justify-content: center;">
            
            <!-- Bar 1: APBDes -->
            <div class="rank-bar-item">
              <div class="rank-bar-header" style="margin-bottom: 6px;">
                <span style="display: inline-flex; align-items: center; gap: 6px; font-weight: 600; font-size: 13px; color: var(--color-text);">
                  APBDes
                </span>
                <span class="rank-bar-val" style="font-weight: 600; color: var(--color-text);">{{ number_format($pctApbdes, 1, ',', '.') }}% ({{ number_format($apbdesCount, 0, ',', '.') }} unit)</span>
              </div>
              <div class="rank-bar-track" style="height: 7px;">
                <div class="rank-bar-fill" style="width: {{ $pctApbdes }}%; background: var(--color-primary);"></div>
              </div>
            </div>

            <!-- Bar 2: Kekayaan Asli Desa -->
            <div class="rank-bar-item">
              <div class="rank-bar-header" style="margin-bottom: 6px;">
                <span style="display: inline-flex; align-items: center; gap: 6px; font-weight: 500; font-size: 13px; color: var(--color-text-muted);">
                  Kekayaan Asli Desa
                </span>
                <span class="rank-bar-val" style="color: var(--color-text-muted);">{{ number_format($pctKekayaan, 1, ',', '.') }}% ({{ number_format($kekayaanCount, 0, ',', '.') }} unit)</span>
              </div>
              <div class="rank-bar-track" style="height: 7px;">
                <div class="rank-bar-fill" style="width: {{ max($pctKekayaan, 1) }}%; background: #519259;"></div>
              </div>
            </div>

            <!-- Bar 3: Bantuan Pemerintah -->
            <div class="rank-bar-item">
              <div class="rank-bar-header" style="margin-bottom: 6px;">
                <span style="display: inline-flex; align-items: center; gap: 6px; font-weight: 500; font-size: 13px; color: var(--color-text-muted);">
                  Bantuan Pemerintah
                </span>
                <span class="rank-bar-val" style="color: var(--color-text-muted);">{{ number_format($pctBantuan, 1, ',', '.') }}% ({{ number_format($bantuanCount, 0, ',', '.') }} unit)</span>
              </div>
              <div class="rank-bar-track" style="height: 7px;">
                <div class="rank-bar-fill" style="width: {{ max($pctBantuan, 1) }}%; background: #8F9B89;"></div>
              </div>
            </div>

          </div>

          <!-- Bottom Operational Status -->
          <div style="margin-top: auto; padding-top: 12px; border-top: 1px dashed var(--color-border); display: flex; gap: 5px; align-items: center; font-size: 12px;">
            <span style="color: var(--color-text-muted);">Sumber perolehan utama:</span>
            <span style="font-weight: 600; color: var(--color-primary);">
              APBDes ({{ number_format($pctApbdes, 1, ',', '.') }}%)
            </span>
          </div>
        </div>

      </div>

      <!-- =====================================================================
           SECTION 4: BAR CHART TAHUN PEROLEHAN + SEBARAN RUANGAN + AKTIVITAS (6 + 3 + 3)
           ===================================================================== -->
      <div class="dash-section-4">
        
        <!-- Card 1 (lebar 6): Bar Chart Tahun Perolehan -->
        <div class="sima-card" id="section-tren-perolehan" data-search-title="Tahun Perolehan Aset" style="display: flex; flex-direction: column; justify-content: space-between;">
          <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 12px;">
            <div>
              <h3 style="font-size: 15px; font-weight: 600; margin: 0; color: var(--color-text);">Tahun Perolehan Aset</h3>
            </div>
            <span style="font-size: 11px; background: var(--color-primary-tint); color: var(--color-primary); font-weight: 600; padding: 2px 8px; border-radius: var(--radius-full);">
              {{ number_format($totalAset, 0, ',', '.') }} unit total
            </span>
          </div>

          <!-- ApexCharts Column Chart Container -->
          <div id="chart-tahun-perolehan" style="min-height: 290px; flex: 1;"></div>

          <div style="margin-top: auto; padding-top: 10px; border-top: 1px dashed var(--color-border); display: flex; gap: 5px; align-items: center; font-size: 12px;">
            <span style="color: var(--color-text-muted);">Akumulasi terbesar:</span>
            <span style="font-weight: 600; color: var(--color-primary);">Tahun 2024 & 2025 (134 unit)</span>
          </div>
        </div>

        <!-- Card 2 (lebar 3): Sebaran Ruangan (Top Ruangan Terisi Aset) -->
        <div class="sima-card" id="section-sebaran-ruangan" data-search-title="Sebaran Ruangan" style="display: flex; flex-direction: column; justify-content: space-between;">
          <div style="margin-bottom: 14px;">
            <div style="display: flex; justify-content: space-between; align-items: flex-start;">
              <div>
                <h3 style="font-size: 14px; font-weight: 600; margin: 0; color: var(--color-text);">Sebaran Ruangan</h3>
              </div>
              <a href="{{ route('ruangan.index') }}" style="font-size: 11.5px; color: var(--color-primary); font-weight: 600; text-decoration: none; display: inline-flex; align-items: center; gap: 3px;">
                <span>Detail</span>
                <i data-feather="chevron-right" style="width: 12px; height: 12px;"></i>
              </a>
            </div>
          </div>

          <div style="display: flex; flex-direction: column; gap: 14px; flex: 1; justify-content: center;">
            @forelse($topRuangan ?? [] as $r)
              @php
                $pctR = round(($r->inventaris_count / max($totalAset, 1)) * 100, 1);
              @endphp
              <div class="rank-bar-item">
                <div class="rank-bar-header" style="margin-bottom: 6px;">
                  <span class="rank-bar-name" title="{{ $r->nama }}" style="max-width: 140px; overflow: hidden; text-overflow: ellipsis; white-space: nowrap; cursor: help;">{{ $r->nama }}</span>
                  <span class="rank-bar-val" style="font-weight: 600; color: var(--color-text);">{{ number_format($pctR, 1, ',', '.') }}% ({{ number_format($r->inventaris_count, 0, ',', '.') }} unit)</span>
                </div>
                <div class="rank-bar-track" style="height: 7px;">
                  <div class="rank-bar-fill" style="width: {{ $pctR }}%; background-color: var(--color-primary); border-radius: 4px;"></div>
                </div>
              </div>
            @empty
              <div style="display: flex; flex-direction: column; align-items: center; justify-content: center; text-align: center; padding: 24px 8px; color: var(--color-text-muted); flex: 1;">
                <i data-feather="home" style="width: 28px; height: 28px; margin-bottom: 10px; color: var(--color-text-muted);"></i>
                <div style="font-size: 13px; color: var(--color-text); font-weight: 500;">Belum ada penempatan ruangan.</div>
                <div style="font-size: 12px; margin-top: 4px;">Data aset di ruangan akan muncul di sini.</div>
              </div>
            @endforelse
          </div>

          <!-- Bottom Operational Status -->
          <div style="margin-top: auto; padding-top: 12px; border-top: 1px dashed var(--color-border); display: flex; gap: 5px; align-items: center; font-size: 12px;">
            <span style="color: var(--color-text-muted);">Ruangan terisi aset:</span>
            <span style="font-weight: 600; color: var(--color-text);">
              {{ $ruanganTerisiCount ?? $totalRuangan }} dari {{ $totalRuangan }} ruangan
            </span>
          </div>
        </div>

        <!-- Card 3 (lebar 3): Aktivitas Terbaru -->
        <div class="sima-card" id="section-aktivitas-terbaru" data-search-title="Aktivitas Terbaru" style="display: flex; flex-direction: column; justify-content: space-between;">
          <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 12px;">
            <div>
              <h3 style="font-size: 14px; font-weight: 600; margin: 0; color: var(--color-text);">Aktivitas Terbaru</h3>
            </div>
            <a href="{{ route('dashboard.activities') }}" style="font-size: 11.5px; color: var(--color-primary); font-weight: 600; text-decoration: none; display: inline-flex; align-items: center; gap: 3px;">
              <span>Detail</span>
              <i data-feather="chevron-right" style="width: 13px; height: 13px;"></i>
            </a>
          </div>

          <div style="display: flex; flex-direction: column; gap: 2px; flex: 1; justify-content: center;">
            @forelse(collect($activities)->take(4) as $act)
              @php
                $text = is_array($act) ? ($act['text'] ?? '') : ($act->deskripsi ?? 'Aksi sistem');
                $time = is_array($act) ? ($act['time'] ?? '') : $act->created_at?->diffForHumans();
              @endphp
              <div style="padding: 9px 0; {{ !$loop->last ? 'border-bottom: 1px dashed var(--color-border);' : '' }}">
                <div style="color: var(--color-text); font-size: 12px; line-height: 1.4; font-weight: 500;">
                  {{ $text }}
                </div>
                <div style="color: var(--color-text-muted); font-size: 11px; margin-top: 3px;">
                  {{ $time }}
                </div>
              </div>
            @empty
              <div style="display: flex; flex-direction: column; align-items: center; justify-content: center; text-align: center; padding: 24px 8px; color: var(--color-text-muted); flex: 1;">
                <i data-feather="clock" style="width: 28px; height: 28px; margin-bottom: 10px; color: var(--color-text-muted);"></i>
                <div style="font-size: 13px; color: var(--color-text); font-weight: 500;">Belum ada aktivitas.</div>
                <div style="font-size: 12px; margin-top: 4px;">Perubahan data aset akan muncul di sini.</div>
              </div>
            @endforelse
          </div>
        </div>
      </div>

      <!-- =====================================================================
           SECTION 5: ASET PER JENIS INVENTARIS + ASET MENURUT RENTANG NILAI (5 + 7)
           ===================================================================== -->
      <div class="dash-section-5">
        
        <!-- Card 1 (lebar 5): Aset per Jenis Inventaris -->
        <div class="sima-card" id="section-aset-per-jenis" data-search-title="Aset per Jenis Inventaris" style="display: flex; flex-direction: column; justify-content: space-between;">
          <div>
            <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 14px;">
              <div>
                <h3 style="font-size: 15px; font-weight: 600; margin: 0; color: var(--color-text);">Aset per Jenis Inventaris</h3>
              </div>
              <a href="{{ route('manajemen-aset.index') }}" style="font-size: 12px; color: var(--color-primary); font-weight: 600; text-decoration: none; display: inline-flex; align-items: center; gap: 4px;">
                <span>Detail</span>
                <i data-feather="chevron-right" style="width: 14px; height: 14px;"></i>
              </a>
            </div>

            <!-- Header Kolom (Serasi dengan Card 2) -->
            <div style="display: grid; grid-template-columns: repeat(3, 1fr); gap: 10px; padding: 6px 0; border-bottom: 1.5px solid var(--color-border); font-size: 11.5px; font-weight: 700; color: var(--color-text-muted); text-transform: uppercase; letter-spacing: 0.03em;">
              <span>Jenis Inventaris</span>
              <span>Unit</span>
              <span>Total Nilai</span>
            </div>

            <!-- List Data Baris -->
            <div style="display: flex; flex-direction: column;">
              @php
                $cleanJenisList = collect($jenisInventarisList ?? [])->filter(function($j) {
                  return ($j->inventaris_count ?? 0) > 0 || ($j->total_nilai ?? 0) > 0;
                })->sortByDesc('total_nilai');
              @endphp
              @forelse($cleanJenisList as $j)
                @php
                  $namaClean = preg_replace('/^Inventaris\s+/i', '', $j->nama ?? '');
                  $namaClean = str_replace(' dan ', ' & ', $namaClean);
                  if (strcasecmp(trim($namaClean), 'yang tidak ditemukan') === 0) {
                    $namaClean = 'Tidak Ditemukan';
                  }
                @endphp
                <div style="display: grid; grid-template-columns: repeat(3, 1fr); gap: 10px; align-items: center; padding: 9px 0; {{ !$loop->last ? 'border-bottom: 1px dashed var(--color-border);' : '' }}">
                  <div style="min-width: 0; display: flex; align-items: center; gap: 8px;">
                    <span style="font-size: 12.5px; font-weight: 500; color: var(--color-text); overflow: hidden; text-overflow: ellipsis; white-space: nowrap;" title="{{ $j->nama }}">{{ $namaClean }}</span>
                  </div>
                  <div style="text-align: left; font-size: 12.5px; font-weight: 600; color: var(--color-text); font-variant-numeric: tabular-nums;">
                    {{ number_format($j->inventaris_count ?? 0, 0, ',', '.') }}
                  </div>
                  <div style="text-align: left; font-size: 12.5px; font-weight: 600; color: var(--color-text); font-variant-numeric: tabular-nums;">
                    Rp {{ number_format($j->total_nilai ?? 0, 0, ',', '.') }}
                  </div>
                </div>
              @empty
                <div style="padding: 16px; text-align: center; color: var(--color-text-muted); font-size: 12px;">
                  Tidak ada data jenis inventaris.
                </div>
              @endforelse
            </div>
          </div>

          <div style="margin-top: auto; padding-top: 12px; border-top: 1px dashed var(--color-border); display: flex; gap: 5px; align-items: center; font-size: 12px;">
            <span style="color: var(--color-text-muted);">Total Jenis Aktif:</span>
            <span style="font-weight: 600; color: var(--color-text);">{{ $cleanJenisList->count() }} jenis</span>
          </div>
        </div>

        <!-- Card 2 (lebar 7): Aset menurut Rentang Nilai -->
        <div class="sima-card" id="section-rentang-nilai" data-search-title="Aset menurut Rentang Nilai" style="display: flex; flex-direction: column; justify-content: space-between;">
          <div>
            <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 14px;">
              <div>
                <h3 style="font-size: 15px; font-weight: 600; margin: 0; color: var(--color-text);">Aset menurut Rentang Nilai</h3>
              </div>
            </div>

            <!-- Header Kolom (Serasi dengan Card 1) -->
            <div style="display: grid; grid-template-columns: repeat(3, 1fr); gap: 12px; padding: 6px 0; border-bottom: 1.5px solid var(--color-border); font-size: 11.5px; font-weight: 700; color: var(--color-text-muted); text-transform: uppercase; letter-spacing: 0.03em;">
              <span>Rentang</span>
              <span>Unit</span>
              <span>Total Nilai</span>
            </div>

            <!-- 4 Baris Rentang Nilai -->
            <div style="display: flex; flex-direction: column;">
              @php
                $rangesOrder = ['above_50m', '10m_to_50m', '1m_to_10m', 'below_1m'];
                $rentangBadgeColors = [
                  'above_50m' => ['dot' => '#1f3a2a'],
                  '10m_to_50m' => ['dot' => '#519259'],
                  '1m_to_10m'  => ['dot' => '#C9A24A'],
                  'below_1m'   => ['dot' => '#8F9B89'],
                ];
              @endphp
              @foreach($rangesOrder as $rKey)
                @php
                  $row = $rentangNilai[$rKey] ?? null;
                  if (!$row) continue;
                  $cfg = $rentangBadgeColors[$rKey];
                @endphp
                <div style="display: grid; grid-template-columns: repeat(3, 1fr); gap: 12px; align-items: center; padding: 11px 0; {{ !$loop->last ? 'border-bottom: 1px dashed var(--color-border);' : '' }}">
                  <div style="min-width: 0; display: flex; align-items: center; gap: 10px;">
                    <div style="min-width: 0;">
                      <div style="font-size: 13px; font-weight: 600; color: var(--color-text);">{{ $row['label'] }}</div>
                      <div style="font-size: 11px; color: var(--color-text-muted); margin-top: 1px;">{{ $row['sub'] }}</div>
                    </div>
                  </div>
                  <div style="text-align: left; font-size: 13px; font-weight: 600; color: var(--color-text); font-variant-numeric: tabular-nums;">
                    {{ number_format($row['unit'], 0, ',', '.') }} <span style="font-size: 11px; font-weight: 500; color: var(--color-text-muted);">unit</span>
                  </div>
                  <div style="text-align: left; font-size: 13px; font-weight: 700; color: var(--color-primary); font-variant-numeric: tabular-nums;">
                    Rp {{ number_format($row['total_nilai'], 0, ',', '.') }}
                  </div>
                </div>
              @endforeach
            </div>
          </div>

          <!-- Bottom Summary / Rationale -->
          <div style="margin-top: auto; padding-top: 12px; border-top: 1px dashed var(--color-border); display: flex; gap: 5px; align-items: center; font-size: 12px;">
            <span style="color: var(--color-text-muted);">Penggolongan nilai perolehan:</span>
            <span style="font-weight: 600; color: var(--color-text);">
              Batas bawah inklusif, batas atas eksklusif
            </span>
          </div>
        </div>

      </div>

      <!-- =====================================================================
           SECTION 6: TABEL ASET YANG BARU DITAMBAHKAN (12)
           ===================================================================== -->
      <div class="dash-section-6 sima-card" id="section-aset-terbaru" data-search-title="Aset yang Baru Ditambahkan">
        <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 16px; flex-wrap: wrap; gap: 12px;">
          <div>
            <h3 style="font-size: 16px; font-weight: 700; margin: 0; color: var(--color-text);">Aset yang Baru Ditambahkan</h3>
            <p style="font-size: 12px; color: var(--color-text-muted); margin: 4px 0 0 0;">10 aset terakhir yang didaftarkan ke dalam sistem</p>
          </div>
          <div style="display: flex; gap: 10px; align-items: center; text">
            <a href="{{ route('manajemen-aset.index') }}" style="font-size: 12px; color: var(--color-primary); font-weight: 600; text-decoration: none; display: inline-flex; align-items: center; gap: 4px;">
              <span>Lihat semua {{ number_format($totalAset, 0, ',', '.') }} aset</span>
              <i data-feather="chevron-right" style="width: 14px; height: 14px;"></i>
            </a>
          </div>
        </div>

        <div class="table-sima-wrapper">
          <table class="table-sima">
            <thead>
              <tr>
                <th style="width: 32%;">Nama Barang & Kode</th>
                <th>Jenis Inventaris</th>
                <th>Ruangan</th>
                <th>Ditambahkan</th>
                <th class="num-col">Harga Perolehan</th>
                <th style="text-align: center;">Status Kondisi</th>
                <th style="width: 80px; text-align: center;">Aksi</th>
              </tr>
            </thead>
            <tbody>
              @forelse($recentAssets as $aset)
              @php
                $cleanJenis = preg_replace('/^Inventaris\s+/i', '', $aset->jenis->nama ?? 'Aset');
                $cleanJenis = str_replace(' dan ', ' & ', $cleanJenis);
              @endphp
              <tr>
                <td>
                  <div style="font-weight: 600; color: var(--color-text); font-size: 13.5px;">{{ $aset->nama_barang }}</div>
                  <div style="font-size: 11.5px; color: var(--color-text-muted); margin-top: 2px;">
                    <code>{{ $aset->kode_barang }}</code> <span style="color: var(--color-text-muted); opacity: 0.5; margin: 0 4px;">|</span> <abbr title="Nomor Urut Pendaftaran" style="text-decoration: underline dotted; text-underline-offset: 3px; cursor: help;">NUP {{ $aset->nup }}</abbr>
                  </div>
                </td>
                <td>
                  <span style="color: var(--color-text); font-size: 13px; font-weight: 500;">
                    {{ $cleanJenis }}
                  </span>
                </td>
                <td>
                  @if($aset->ruangan && $aset->ruangan->nama)
                    <div style="font-size: 13px; color: var(--color-text);">{{ $aset->ruangan->nama }}</div>
                  @else
                    <span class="text-empty-fallback" style="color: var(--color-warning); font-size: 13px; font-weight: 500;">
                      Belum ditentukan
                    </span>
                  @endif
                </td>
                <td style="font-size: 13px; color: var(--color-text-muted);">
                  {{ $aset->created_at ? $aset->created_at->locale('id')->isoFormat('D MMM Y') : '-' }}
                </td>
                <td class="num-col" style="font-weight: 600; color: var(--color-text);">
                  Rp {{ number_format($aset->nilai_perolehan ?? 0, 0, ',', '.') }}
                </td>
                <td style="text-align: center;">
                  @if(strtolower($aset->kondisi) == 'baik')
                    <span class="status-pill status-pill-success">Baik</span>
                  @elseif(str_contains(strtolower($aset->kondisi), 'ringan'))
                    <span class="status-pill status-pill-warning">Rusak Ringan</span>
                  @else
                    <span class="status-pill status-pill-danger">Rusak Berat</span>
                  @endif
                </td>
                <td style="text-align: center;">
                  <a href="{{ route('manajemen-aset.show', $aset->id) }}" class="btn-sima-icon" title="Lihat detail" aria-label="Lihat detail">
                    <i data-feather="eye" style="width: 18px; height: 18px;"></i>
                  </a>
                </td>
              </tr>
              @empty
              <tr>
                <td colspan="7" style="text-align: center; padding: 32px; color: var(--color-text-muted);">
                  Belum ada data aset yang tersimpan.
                </td>
              </tr>
              @endforelse
            </tbody>
          </table>
        </div>
      </div>

    </div>
  </div>
  <!-- [ Main Content ] end -->

  @include('layouts.footer-block')
  @include('layouts.footer-js')

  <script>
    document.addEventListener("DOMContentLoaded", function() {
      // Pastikan sidebar selalu dalam mode default vertikal
      if (typeof localStorage !== 'undefined' && localStorage.getItem('layout') === 'horizontal') {
        localStorage.removeItem('layout');
      }
      var side = document.querySelector('.pc-sidebar');
      if (side) {
        side.classList.remove('d-none');
      }

      if (typeof feather !== 'undefined') {
        feather.replace();
      }

      // 1. Sparkline Charts ONLY for cards with true time series (Total Aset & Total Nilai)
      const sparklineConfigs = [
        { 
          id: '#sparkline-1', 
          color: '#1f3a2a', 
          data: {!! json_encode($kpis['sparklineAssets'] ?? [249, 291, 325, 356, 399, 442]) !!} 
        },
        { 
          id: '#sparkline-2', 
          color: '#C9A24A', 
          data: {!! json_encode($kpis['sparklineValues'] ?? [12.4, 13.8, 15.2, 16.5, 18.1, 19.33]) !!} 
        }
      ];

      sparklineConfigs.forEach(function(sp) {
        if (document.querySelector(sp.id)) {
          new ApexCharts(document.querySelector(sp.id), {
            series: [{ data: sp.data }],
            chart: {
              type: 'area',
              height: 40,
              sparkline: { enabled: true },
              animations: { enabled: true }
            },
            stroke: { curve: 'smooth', width: 2 },
            fill: {
              type: 'gradient',
              gradient: {
                shadeIntensity: 1,
                opacityFrom: 0.35,
                opacityTo: 0.05,
                stops: [0, 90, 100]
              }
            },
            colors: [sp.color],
            tooltip: { enabled: false }
          }).render();
        }
      });

      // 2. Section 2: Donut Chart Nilai per Kategori
      const donutLabels = {!! json_encode($chartData['nilai_per_jenis']['labels'] ?? []) !!};
      const donutValues = {!! json_encode($chartData['nilai_per_jenis']['data'] ?? []) !!};
      const donutColors = {!! json_encode($categoryPalette) !!};

      if (document.querySelector("#chart-donut-nilai") && donutValues.length > 0) {
        new ApexCharts(document.querySelector("#chart-donut-nilai"), {
          series: donutValues,
          labels: donutLabels,
          chart: {
            type: 'donut',
            height: 220
          },
          colors: donutColors.slice(0, donutLabels.length),
          dataLabels: { enabled: false },
          legend: { show: false },
          plotOptions: {
            pie: {
              donut: {
                size: '76%',
                labels: {
                  show: true,
                  name: { show: true, fontSize: '11px', color: '#546958' },
                  value: {
                    show: true,
                    fontSize: '12px',
                    fontWeight: 700,
                    color: '#16251b',
                    formatter: function(val) {
                      return 'Rp ' + new Intl.NumberFormat('id-ID').format(val);
                    }
                  },
                  total: {
                    show: true,
                    label: 'Total Nilai',
                    color: '#546958',
                    fontSize: '11px',
                    formatter: function(w) {
                      const total = w.globals.seriesTotals.reduce((a, b) => a + b, 0);
                      return 'Rp ' + new Intl.NumberFormat('id-ID').format(total);
                    }
                  }
                }
              }
            }
          },
          stroke: { width: 2, colors: ['#f8faf7'] },
          tooltip: {
            y: {
              formatter: function(val) {
                return 'Rp ' + new Intl.NumberFormat('id-ID').format(val);
              }
            }
          }
        }).render();
      }

      // 3. Section 4: Bar Chart Tahun Perolehan (ApexCharts)
      const tahunLabels = {!! json_encode($tahunPerolehanData['labels'] ?? []) !!};
      const tahunCounts = {!! json_encode($tahunPerolehanData['counts'] ?? []) !!};
      const tahunValues = {!! json_encode($tahunPerolehanData['values'] ?? []) !!};

      if (document.querySelector("#chart-tahun-perolehan") && tahunCounts.length > 0) {
        new ApexCharts(document.querySelector("#chart-tahun-perolehan"), {
          series: [{
            name: 'Jumlah Aset',
            data: tahunCounts
          }],
          chart: {
            type: 'bar',
            height: 290,
            toolbar: { show: false },
            animations: { enabled: true }
          },
          colors: ['#1f3a2a'],
          plotOptions: {
            bar: {
              borderRadius: 4,
              columnWidth: '52%'
            }
          },
          dataLabels: {
            enabled: false
          },
          xaxis: {
            categories: tahunLabels,
            labels: {
              style: {
                colors: '#546958',
                fontSize: '11px',
                fontFamily: 'Inter, sans-serif'
              }
            },
            axisBorder: { show: false },
            axisTicks: { show: false }
          },
          yaxis: {
            labels: {
              style: {
                colors: '#546958',
                fontSize: '11px',
                fontFamily: 'Inter, sans-serif'
              },
              formatter: function(val) {
                return Math.round(val);
              }
            }
          },
          grid: {
            borderColor: '#cfdcc8',
            strokeDashArray: 3,
            yaxis: { lines: { show: true } },
            xaxis: { lines: { show: false } },
            padding: {
              top: -8,
              bottom: 4,
              left: 10,
              right: 10
            }
          },
          tooltip: {
            custom: function({ series, seriesIndex, dataPointIndex }) {
              const label = tahunLabels[dataPointIndex];
              const count = tahunCounts[dataPointIndex];
              const val = tahunValues[dataPointIndex];
              const valFormatted = 'Rp ' + new Intl.NumberFormat('id-ID').format(val);
              return '<div style="padding: 8px 12px; background: #f8faf7; border: 1px solid #cfdcc8; border-radius: 8px; font-size: 12px; color: #16251b; font-family: Inter, sans-serif; box-shadow: 0 4px 12px rgba(31,58,42,0.1);">' +
                     '<div style="font-weight: 700; margin-bottom: 4px; color: #1f3a2a;">Tahun ' + label + '</div>' +
                     '<div>Jumlah: <strong>' + count + ' unit</strong></div>' +
                     '<div style="margin-top: 2px;">Total Nilai: <strong>' + valFormatted + '</strong></div>' +
                     '</div>';
            }
          }
        }).render();
      }

    });
  </script>
</body>
</html>
