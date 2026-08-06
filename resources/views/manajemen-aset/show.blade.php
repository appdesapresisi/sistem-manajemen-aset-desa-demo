<!doctype html>
<html lang="en" data-pc-preset="{{ $pc_preset_theme }}" data-pc-theme="{{ $pc_dark_layout }}">
  <head>
    @include('layouts.head-page-meta', ['title' => 'Detail Inventaris'])
    @include('layouts.head-css')
    <style>
      /* ===== LAYOUT UTAMA ===== */
      .detail-layout {
        display: flex;
        gap: 20px;
        align-items: flex-start;
      }
      .detail-main { flex: 1; min-width: 0; }
      .detail-sidebar { width: 240px; flex-shrink: 0; display: flex; flex-direction: column; gap: 14px; }

      /* ===== HERO HEADER ===== */
      .hero-header {
        background: linear-gradient(135deg, #519259 0%, #064635 100%);
        border-radius: 12px;
        padding: 24px;
        margin-bottom: 14px;
        color: #fff;
        position: relative;
        overflow: hidden;
      }
      .hero-header::before { content:''; position:absolute; top:-30px; right:-30px; width:160px; height:160px; background:rgba(81,146,89,.2); border-radius:50%; }
      .hero-header::after  { content:''; position:absolute; bottom:-50px; right:60px; width:120px; height:120px; background:rgba(81,146,89,.15); border-radius:50%; }
      .hero-title { font-size:21px; font-weight:700; margin:0 0 5px; position:relative; z-index:1; }
      .hero-subtitle { font-size:13px; opacity:.8; margin:0; position:relative; z-index:1; }
      .hero-nilai {
        margin-top:18px; padding-top:14px; border-top:1px solid rgba(255,255,255,.2);
        position:relative; z-index:1; display:flex; align-items:center; gap:12px;
      }
      .hero-nilai-icon { width:38px; height:38px; background:rgba(255,255,255,.15); border-radius:10px; display:flex; align-items:center; justify-content:center; }
      .hero-nilai-icon i { width:18px; height:18px; color:#fff; }
      .hero-nilai-label { font-size:11px; opacity:.75; margin-bottom:2px; }
      .hero-nilai-amount { font-size:19px; font-weight:700; }

      /* ===== CARD GENERIK ===== */
      .d-card { background:#fff; border-radius:12px; border:1px solid #e2e8f0; box-shadow:0 1px 3px rgba(0,0,0,.05); margin-bottom:14px; overflow:hidden; }
      .d-card-header {
        padding:13px 20px; border-bottom:1px solid #f1f5f9;
        display:flex; align-items:center; gap:8px;
        font-size:12px; font-weight:700; color:#064635;
        text-transform:uppercase; letter-spacing:.5px;
      }
      .d-card-header i { width:15px; height:15px; }

      /* ===== DAFTAR ITEM VERTIKAL ===== */
      .detail-list { list-style:none; margin:0; padding:0; }
      .detail-list li {
        display:flex; align-items:flex-start;
        padding:12px 20px; border-bottom:1px solid #f1f5f9; gap:12px;
      }
      .detail-list li:last-child { border-bottom:none; }
      .detail-list li:hover { background:#fafbfc; }
      .dl-label {
        width:165px; flex-shrink:0;
        font-size:11px; font-weight:700; color:#94a3b8;
        text-transform:uppercase; letter-spacing:.4px; padding-top:3px;
      }
      .dl-value { flex:1; font-size:14px; color:#1e293b; font-weight:500; word-break:break-word; }
      .dl-value.empty { color:#cbd5e1; font-style:italic; font-weight:400; }

      /* ===== BADGE ===== */
      .kondisi-badge { display:inline-flex; align-items:center; gap:6px; padding:4px 10px; border-radius:20px; font-size:12px; font-weight:600; }
      .kondisi-baik { background:#ecfdf5; color:#059669; }
      .kondisi-rusak-ringan { background:#fef3c7; color:#d97706; }
      .kondisi-rusak-berat { background:#fef2f2; color:#dc2626; }

      /* ===== SIDEBAR ===== */
      .sidebar-qr { background:#fff; border:1px solid #e2e8f0; border-radius:12px; padding:18px; text-align:center; box-shadow:0 1px 3px rgba(0,0,0,.05); }
      .sidebar-qr img { width:136px; height:136px; border:1px solid #e2e8f0; border-radius:8px; padding:6px; background:#fff; display:block; margin:0 auto; }
      .sidebar-qr-code { margin-top:8px; font-size:11px; font-weight:600; color:#64748b; letter-spacing:.5px; font-family:monospace; }
      .qr-actions { display:flex; flex-direction:column; gap:8px; align-items:stretch; margin-top:12px; }
      .btn-qr { display:inline-flex; align-items:center; justify-content:center; gap:5px; padding:7px 12px; border-radius:7px; font-size:11px; font-weight:600; cursor:pointer; text-decoration:none; border:none; transition:all .2s; white-space:nowrap; width:100%; box-sizing:border-box; }
      .btn-qr i { width:12px; height:12px; }
      .btn-qr-download { background:#f0fdf4; color:#16a34a; border:1px solid #bbf7d0; }
      .btn-qr-download:hover { background:#dcfce7; color:#16a34a; }
      .btn-qr-print { background:#f0f4ff; color:#064635; border:1px solid #c7d2e8; }
      .btn-qr-print:hover { background:#dce6f7; color:#064635; }
      .sidebar-info-card { background:#fff; border:1px solid #e2e8f0; border-radius:12px; overflow:hidden; box-shadow:0 1px 3px rgba(0,0,0,.05); }
      .sic-row { padding:12px 16px; border-bottom:1px solid #f1f5f9; }
      .sic-row:last-child { border-bottom:none; }
      .sic-label { font-size:10px; font-weight:700; color:#94a3b8; text-transform:uppercase; letter-spacing:.5px; margin-bottom:4px; }
      .sic-value { font-size:13px; font-weight:600; color:#1e293b; }

      /* ===== FOTO ===== */
      .foto-grid { display:flex; flex-wrap:wrap; gap:10px; padding:16px 20px; }
      .foto-item { width:100px; height:100px; border-radius:8px; overflow:hidden; border:1px solid #e2e8f0; box-shadow:0 1px 3px rgba(0,0,0,.08); }
      .foto-item img { width:100%; height:100%; object-fit:cover; transition:.2s; display:block; }
      .foto-item:hover img { transform:scale(1.05); }

      /* ===== TIMELINE ===== */
      .timeline-wrap { padding:18px 20px; }
      .timeline-item { display:flex; gap:14px; position:relative; padding-bottom:16px; }
      .timeline-item:last-child { padding-bottom:0; }
      .timeline-item:not(:last-child)::before { content:''; position:absolute; left:15px; top:32px; bottom:0; width:2px; background:#f1f5f9; }
      .timeline-dot { width:32px; height:32px; border-radius:50%; display:flex; align-items:center; justify-content:center; flex-shrink:0; }
      .timeline-dot i { width:14px; height:14px; }
      .timeline-dot.draft    { background:#f1f5f9; color:#94a3b8; }
      .timeline-content { flex:1; }
      .timeline-title { font-size:13px; font-weight:600; color:#1f2937; }
      .timeline-meta  { font-size:11px; color:#9ca3af; margin-top:2px; }
      .timeline-note  { font-size:12px; color:#6b7280; margin-top:6px; font-style:italic; padding:7px 10px; background:#f4faf5; border-radius:6px; border-left:3px solid #e2e8f0; }

      /* ===== ACTION BAR ===== */
      .action-bar {
        background:#fff; border:1px solid #e2e8f0; border-radius:12px;
        padding:14px 20px; display:flex; gap:10px; align-items:center;
        flex-wrap:wrap; margin-bottom:14px; box-shadow:0 1px 3px rgba(0,0,0,.05);
        justify-content: space-between;
      }
      .action-bar-right { display:flex; gap:10px; align-items:center; }
      .btn {
        padding:9px 18px; border-radius:8px; font-size:13px; font-weight:600;
        cursor:pointer; transition:all .2s; display:inline-flex; align-items:center;
        gap:7px; text-decoration:none; border:none; white-space:nowrap;
      }
      .btn i { width:15px; height:15px; }
      .btn-back { border:1px solid #d1d5db !important; background:#fff; color:#374151; }
      .btn-back:hover { background:#f4faf5; }
      .btn-edit { background:#064635; color:#fff; }
      .btn-edit:hover { background:#1c5240; }
      .btn-delete { background:#dc2626; color:#fff; }
      .btn-delete:hover { background:#b91c1c; }

      /* ===== MODAL ===== */
      .modal-overlay { display:none; position:fixed; inset:0; background:rgba(0,0,0,.4); z-index:9999; align-items:center; justify-content:center; }
      .modal-overlay.open { display:flex; }
      .modal-box { background:#fff; border-radius:16px; padding:28px; width:420px; box-shadow:0 20px 60px rgba(0,0,0,.2); }
      .modal-box h4 { font-size:16px; font-weight:700; color:#1f2937; margin:0 0 16px; }
      .modal-box textarea { width:100%; border:1px solid #e5e7eb; border-radius:8px; padding:10px 12px; font-size:13px; resize:vertical; outline:none; box-sizing:border-box; }
      .modal-box textarea:focus { border-color:#ef4444; }
      .modal-actions { display:flex; gap:10px; justify-content:flex-end; margin-top:16px; }
      .modal-cancel { background:#f3f4f6; color:#6b7280; border:none; padding:8px 16px; border-radius:8px; font-size:13px; font-weight:600; cursor:pointer; display:inline-flex; align-items:center; gap:6px; }

      @media (max-width: 900px) {
        .detail-layout { flex-direction:column; }
        .detail-main { width:100%; flex:none; }
        .detail-sidebar { width:100%; }
      }

      /* ===== DETAIL SECTION (legacy - keep for compat) ===== */
      .detail-section {
        background: #ffffff;
        border-radius: 12px;
        padding: 24px;
        margin-bottom: 20px;
        border: 1px solid #e2e8f0;
        box-shadow: 0 1px 3px rgba(0,0,0,0.05);
      }
      .detail-section-title {
        font-size: 14px;
        font-weight: 600;
        color: #064635;
        margin-bottom: 20px;
        display: flex;
        align-items: center;
        gap: 8px;
        padding-bottom: 12px;
        border-bottom: 2px solid #f1f5f9;
      }
      .detail-section-title i {
        width: 18px;
        height: 18px;
      }
      .detail-grid {
        display: grid;
        grid-template-columns: repeat(2, minmax(0, 1fr));
        gap: 20px;
      }
      .detail-item {
        display: flex;
        flex-direction: column;
        gap: 6px;
      }
      .detail-label {
        font-size: 12px;
        font-weight: 600;
        color: #64748b;
        text-transform: uppercase;
        letter-spacing: 0.5px;
      }
      .detail-value {
        font-size: 15px;
        color: #1e293b;
        font-weight: 500;
      }
      .detail-value.empty {
        color: #94a3b8;
        font-style: italic;
      }
      .detail-full {
        grid-column: 1 / -1;
      }

      /* Card Header */
      .card-header-custom {
        display: flex;
        align-items: center;
        margin-bottom: 24px;
        padding-bottom: 20px;
        border-bottom: 2px solid #f1f5f9;
      }
      .card-header-icon {
        display: flex;
        align-items: center;
        justify-content: center;
        width: 48px;
        height: 48px;
        border-radius: 12px;
        background: #519259;
        margin-right: 16px;
      }
      .card-header-icon i {
        color: #ffffff !important;
        fill: #ffffff !important;
        stroke: #ffffff !important;
        width: 24px;
        height: 24px;
      }
      .card-header-icon svg {
        color: #ffffff !important;
        fill: #ffffff !important;
        stroke: #ffffff !important;
      }
      .card-header-info h4 {
        margin: 0 0 4px 0;
        font-size: 20px;
        font-weight: 700;
        color: #1e293b;
      }
      .card-header-info p {
        margin: 0;
        font-size: 14px;
        color: #64748b;
      }

      /* Kondisi Badge */
      .kondisi-badge {
        display: inline-flex;
        align-items: center;
        gap: 6px;
        padding: 6px 12px;
        border-radius: 20px;
        font-size: 13px;
        font-weight: 600;
      }
      .kondisi-baik {
        background: #ecfdf5;
        color: #059669;
      }
      .kondisi-rusak-ringan {
        background: #fef3c7;
        color: #d97706;
      }
      .kondisi-rusak-berat {
        background: #fef2f2;
        color: #dc2626;
      }

      /* Dynamic Fields Table */
      .fields-table {
        width: 100%;
        border-collapse: collapse;
        margin-top: 12px;
      }
      .fields-table thead th {
        background: #f4faf5;
        padding: 12px 16px;
        text-align: left;
        font-size: 12px;
        font-weight: 600;
        color: #64748b;
        text-transform: uppercase;
        letter-spacing: 0.5px;
        border-bottom: 2px solid #e2e8f0;
      }
      .fields-table tbody td {
        padding: 12px 16px;
        border-bottom: 1px solid #f1f5f9;
        font-size: 14px;
        color: #1e293b;
      }
      .fields-table tbody tr:last-child td {
        border-bottom: none;
      }
      .fields-table tbody tr:hover {
        background: #f4faf5;
      }

      /* Action Buttons */
      .action-buttons {
        display: flex;
        gap: 12px;
        margin-top: 24px;
        align-items: center;
      }
      .action-buttons-left {
        display: flex;
        gap: 12px;
        align-items: center;
      }
      .action-buttons-right {
        display: flex;
        gap: 12px;
        align-items: center;
        margin-left: auto;
      }
      .btn {
        padding: 10px 20px;
        border-radius: 8px;
        font-size: 14px;
        font-weight: 500;
        cursor: pointer;
        transition: all 0.2s;
        display: flex;
        align-items: center;
        gap: 8px;
        text-decoration: none;
        border: none;
      }
      .btn i {
        width: 16px;
        height: 16px;
      }
      .btn-back {
        border: 1px solid #d1d5db !important;
        background: #fff;
        color: #374151;
      }
      .btn-back:hover {
        background: #f4faf5;
      }
      .btn-edit {
        background: #064635;
        color: #fff;
      }
      .btn-edit:hover {
        background: #1c5240;
      }
      .btn-delete {
        background: #dc2626;
        color: #fff;
      }
      .btn-delete:hover {
        background: #b91c1c;
      }

      /* Stats Card */
      .stats-card {
        background: linear-gradient(135deg, #064635 0%, #519259 100%);
        color: #fff;
        border-radius: 12px;
        padding: 20px;
        display: flex;
        align-items: center;
        gap: 16px;
        margin-bottom: 20px;
      }
      .stats-icon {
        width: 50px;
        height: 50px;
        background: rgba(255,255,255,0.2);
        border-radius: 10px;
        display: flex;
        align-items: center;
        justify-content: center;
      }
      .stats-icon i {
        width: 26px;
        height: 26px;
        color: #fff;
      }
      .stats-info {
        flex: 1;
      }
      .stats-label {
        font-size: 13px;
        opacity: 0.9;
        margin-bottom: 4px;
      }
      .stats-value {
        font-size: 28px;
        font-weight: 700;
        margin: 0;
      }

      @media (max-width: 768px) {
        .detail-grid {
          grid-template-columns: 1fr;
        }
        .action-buttons {
          flex-direction: column;
        }
        .btn {
          width: 100%;
          justify-content: center;
        }
      }
    </style>
  </head>

  <body data-pc-preset="{{ $pc_preset_theme }}" data-pc-sidebar-theme="{{ $pc_sidebar_theme }}">
    @include('layouts.notification')
    @include('layouts.modals')
    @include('layouts.layout-vertical')

    <div class="pc-container">
      <div class="pc-content">
        <!-- Breadcrumb -->
        <div class="page-header">
          <div class="page-block">
            <div class="row align-items-center">
              <div class="col-md-12">
                <ul class="breadcrumb">
                  <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">Home</a></li>
                  <li class="breadcrumb-item"><a href="{{ route('manajemen-aset.index') }}">Manajemen Aset</a></li>
                  <li class="breadcrumb-item" aria-current="page">Detail Aset</li>
                </ul>
              </div>
            </div>
          </div>
        </div>

        <!-- Action Bar -->
        <div class="action-bar">
          <a href="{{ route('manajemen-aset.index') }}" class="btn btn-back">
            <i data-feather="arrow-left"></i>
            Kembali
          </a>
          <div class="action-bar-right">
            @can('update', $inventaris)
            <a href="{{ route('manajemen-aset.edit', $inventaris->id) }}?from=show" class="btn btn-edit">
              <i data-feather="edit-2"></i>
              Edit Aset
            </a>
            @endcan
            @can('delete', $inventaris)
              <button type="button" class="btn btn-delete" data-nama="{{ $inventaris->display_name ?? '' }}" onclick="confirmDeleteInventaris(this.getAttribute('data-nama'), '{{ $inventaris->id }}')">
                <i data-feather="trash-2"></i>
                Hapus Aset
              </button>
            @endcan
            {{-- DISABLED: Approval workflow removed --}}
          </div>
        </div>

        <!-- Layout Utama: Main + Sidebar -->
        <div class="detail-layout">

          <!-- ===== KOLOM KIRI (MAIN) ===== -->
          <div class="detail-main">

            <!-- Hero Header -->
            <div class="hero-header">
              <div class="hero-title">{{ $inventaris->display_name ?? '-' }}</div>
              <div class="hero-subtitle">
                {{ $inventaris->jenis->nama ?? '-' }}
              </div>
              <div class="hero-nilai">
                <div>
                  <div class="hero-nilai-label">Nilai Perolehan</div>
                  @php $npHero = (float)($inventaris->nilai_perolehan ?? 0); @endphp
                  <div class="hero-nilai-amount">Rp {{ number_format($npHero, fmod($npHero, 1) != 0 ? 2 : 0, ',', '.') }}</div>
                </div>
              </div>
            </div>

            <!-- Informasi Aset — field sesuai definisi jenis_inventaris_data (ordered by order) -->
            <div class="d-card">
              <div class="d-card-header">
                <i data-feather="info"></i>
                Informasi Aset
              </div>
              @if($inventaris->jenis && $inventaris->jenis->fields->count() > 0)
              <ul class="detail-list">
                @foreach($inventaris->jenis->fields as $field)
                @php
                  $fieldVal = $field->getValueFromInventaris($inventaris);
                  $isEmpty  = ($fieldVal === '' || $fieldVal === null || $fieldVal === '-');
                @endphp
                <li>
                  <span class="dl-label">{{ $field->label_field }}</span>
                  <span class="dl-value {{ $isEmpty ? 'empty' : '' }}">
                    @if($field->field_type === 'fixed' && $field->fixed_field_name === 'kondisi')
                      @if($fieldVal === 'baik')
                        <span class="kondisi-badge kondisi-baik">✓ Baik</span>
                      @elseif($fieldVal === 'rusak_ringan')
                        <span class="kondisi-badge kondisi-rusak-ringan">⚠ Rusak Ringan</span>
                      @elseif($fieldVal === 'rusak_berat')
                        <span class="kondisi-badge kondisi-rusak-berat">✕ Rusak Berat</span>
                      @else
                        <span class="empty">-</span>
                      @endif
                    @elseif($field->field_type === 'fixed' && $field->fixed_field_name === 'nilai_perolehan')
                      @php $npFv = (float)$fieldVal; @endphp
                      {{ $isEmpty ? '-' : 'Rp ' . number_format($npFv, fmod($npFv, 1) != 0 ? 2 : 0, ',', '.') }}
                    @elseif(!$isEmpty && \App\Models\JenisInventarisData::isCurrencyField($field->fixed_field_name ?? null, $field->tipe ?? null, $field->nama_field ?? null, $field->label_field ?? null))
                      @php $npDv = (float)$fieldVal; @endphp
                      Rp {{ number_format($npDv, fmod($npDv, 1) != 0 ? 2 : 0, ',', '.') }}
                    @elseif(!$isEmpty && ($field->tipe ?? null) === 'decimal_m2')
                      @php $m2Val = (float)$fieldVal; @endphp
                      {{ number_format($m2Val, fmod($m2Val, 1) != 0 ? 2 : 0, ',', '.') }} m²
                    @elseif(!$isEmpty && ($field->tipe ?? null) === 'decimal_km')
                      @php $kmVal = (float)$fieldVal; @endphp
                      {{ number_format($kmVal, fmod($kmVal, 1) != 0 ? 2 : 0, ',', '.') }} km
                    @elseif(!$isEmpty && ($field->tipe ?? null) === 'decimal_m')
                      @php $mVal = (float)$fieldVal; @endphp
                      {{ number_format($mVal, fmod($mVal, 1) != 0 ? 2 : 0, ',', '.') }} m
                    @else
                      {{ $isEmpty ? '-' : $fieldVal }}
                    @endif
                  </span>
                </li>
                @endforeach
                @if(!($inventaris->jenis?->isVirtual() ?? false))
                <li>
                  <span class="dl-label">Asal Usul Barang</span>
                  <span class="dl-value">
                    @if($inventaris->asal_usul === 'apbdes')
                      APBDes
                    @elseif($inventaris->asal_usul === 'perolehan_lain')
                      Perolehan Lain yang Sah
                    @elseif($inventaris->asal_usul === 'kekayaan_asli')
                      Kekayaan Asli Desa
                    @else
                      <span style="color:#9ca3af;font-style:italic;">Belum diisi</span>
                    @endif
                  </span>
                </li>
                <li>
                  <span class="dl-label">Pengguna</span>
                  <span class="dl-value {{ !$inventaris->pengguna ? 'empty' : '' }}">{{ $inventaris->pengguna ?: '-' }}</span>
                </li>
                <li>
                  <span class="dl-label">Satuan</span>
                  <span class="dl-value {{ !$inventaris->satuan ? 'empty' : '' }}">{{ $inventaris->satuan ?: '-' }}</span>
                </li>
                @endif
                {{-- Ruangan: selalu paling bawah, hanya tampil jika jenis requires_ruangan --}}
                @if($inventaris->jenis && $inventaris->jenis->requires_ruangan)
                <li>
                  <span class="dl-label">Ruangan</span>
                  <span class="dl-value {{ $inventaris->ruangan ? '' : 'empty' }}">
                    {{ $inventaris->ruangan?->nama ?? '-' }}
                  </span>
                </li>
                @php
                   $kirFieldsShow = [
                     ['label' => 'Konstruksi', 'val' => $inventaris->getKirValue('kir_konstruksi')],
                     ['label' => 'Luas', 'val' => $inventaris->getKirValue('kir_luas')],
                     ['label' => 'Konstruksi / Luas', 'val' => $inventaris->getKirValue('kir_konstruksi_luas')], // legacy fallback
                     ['label' => 'Merk / Tipe', 'val' => $inventaris->getKirValue('kir_merk_tipe')],
                     ['label' => 'Warna', 'val' => $inventaris->getKirValue('kir_warna')],
                     ['label' => 'Judul', 'val' => $inventaris->getKirValue('kir_judul')],
                     ['label' => 'Pencipta', 'val' => $inventaris->getKirValue('kir_pencipta')],
                     ['label' => 'Warna / Judul / Pencipta', 'val' => $inventaris->getKirValue('kir_warna_judul_pencipta')], // legacy fallback
                     ['label' => 'No. Polisi', 'val' => $inventaris->getKirValue('kir_no_pol')],
                   ];
                 @endphp
                @foreach($kirFieldsShow as $kfs)
                  @if(!is_null($kfs['val']) && trim((string)$kfs['val']) !== '')
                    <li>
                      <span class="dl-label">{{ $kfs['label'] }}</span>
                      <span class="dl-value">{{ $kfs['val'] }}</span>
                    </li>
                  @endif
                @endforeach
                @endif
              </ul>
              @else
              {{-- Fallback: orphan inventaris atau jenis tanpa field definition --}}
              <ul class="detail-list">
                @if($inventaris->nama_barang)
                <li><span class="dl-label">Nama Barang</span><span class="dl-value">{{ $inventaris->nama_barang }}</span></li>
                @endif
                @if($inventaris->kode_barang)
                <li><span class="dl-label">Kode Barang</span><span class="dl-value">{{ $inventaris->kode_barang }}</span></li>
                @endif
                @if($inventaris->nup)
                <li><span class="dl-label">NUP</span><span class="dl-value">{{ $inventaris->nup }}</span></li>
                @endif
                @if($inventaris->tahun_perolehan)
                <li><span class="dl-label">Tahun Perolehan</span><span class="dl-value">{{ $inventaris->tahun_perolehan }}</span></li>
                @endif
                @if($inventaris->nilai_perolehan !== null)
                @php $npList = (float)$inventaris->nilai_perolehan; @endphp
                <li><span class="dl-label">Nilai Perolehan</span><span class="dl-value">Rp {{ number_format($npList, fmod($npList, 1) != 0 ? 2 : 0, ',', '.') }}</span></li>
                @endif
                @if($inventaris->kondisi)
                <li>
                  <span class="dl-label">Kondisi</span>
                  <span class="dl-value">
                    @if($inventaris->kondisi === 'baik') <span class="kondisi-badge kondisi-baik">✓ Baik</span>
                    @elseif($inventaris->kondisi === 'rusak_ringan') <span class="kondisi-badge kondisi-rusak-ringan">⚠ Rusak Ringan</span>
                    @elseif($inventaris->kondisi === 'rusak_berat') <span class="kondisi-badge kondisi-rusak-berat">✕ Rusak Berat</span>
                    @endif
                  </span>
                </li>
                @endif
                @if($inventaris->keterangan)
                <li><span class="dl-label">Keterangan</span><span class="dl-value">{{ $inventaris->keterangan }}</span></li>
                @endif
                <li>
                  <span class="dl-label">Asal Usul Barang</span>
                  <span class="dl-value">
                    @if($inventaris->asal_usul === 'apbdes')
                      APBDes
                    @elseif($inventaris->asal_usul === 'perolehan_lain')
                      Perolehan Lain yang Sah
                    @elseif($inventaris->asal_usul === 'kekayaan_asli')
                      Kekayaan Asli Desa
                    @else
                      <span style="color:#9ca3af;font-style:italic;">Belum diisi</span>
                    @endif
                  </span>
                </li>
                <li><span class="dl-label">Pengguna</span><span class="dl-value">{{ $inventaris->pengguna ?: '-' }}</span></li>
                <li><span class="dl-label">Satuan</span><span class="dl-value">{{ $inventaris->satuan ?: '-' }}</span></li>
              </ul>
              @endif
            </div>

            <!-- Data KIB (Kartu Inventaris Barang) -->
            @if(!empty($kibConfig['fields']))
            <div class="d-card">
              <div class="d-card-header" style="border-bottom:1px solid #f1f5f9;">
                <i data-feather="clipboard" style="color:#064635;"></i>
                <span style="color:#064635;font-weight:600;">Data KIB (KIB {{ $inventaris->jenis->kib_type }} - {{ $kibConfig['nama'] ?? '' }})</span>
              </div>
              <ul class="detail-list">
                @foreach($kibConfig['fields'] as $kibField)
                  @php
                    $kibVal = $inventaris->getKibValue($kibField['key']);
                    $kibEmpty = ($kibVal === null || $kibVal === '');
                  @endphp
                  <li>
                    <span class="dl-label">{{ $kibField['label'] }}</span>
                    <span class="dl-value {{ $kibEmpty ? 'empty' : '' }}">
                      @if($kibEmpty)
                        -
                      @elseif($kibField['type'] === 'decimal')
                        @php $dv = (float)$kibVal; @endphp
                        {{ number_format($dv, fmod($dv, 1) != 0 ? 2 : 0, ',', '.') }}
                      @else
                        {{ $kibVal }}
                      @endif
                    </span>
                  </li>
                @endforeach
              </ul>
            </div>
            @endif

            <!-- Foto Aset -->
            @if($inventaris->fotos->isNotEmpty())
            <div class="d-card">
              <div class="d-card-header">
                <i data-feather="image"></i>
                Foto Aset
              </div>
              <div class="foto-grid">
                @foreach($inventaris->fotos as $foto)
                <a href="{{ Storage::url($foto->path) }}" target="_blank" class="foto-item">
                  <img src="{{ Storage::url($foto->path) }}" alt="Foto {{ $loop->iteration }}" loading="lazy">
                </a>
                @endforeach
              </div>
            </div>
            @endif

            {{-- DISABLED: Riwayat Persetujuan removed — approval history hidden --}}

          </div><!-- /.detail-main -->

          <!-- ===== KOLOM KANAN (SIDEBAR) ===== -->
          <div class="detail-sidebar">

            {{-- QR Code --}}
            @if(!empty($qrUrl))
            <div class="sidebar-qr">
              <div style="font-size:11px;font-weight:700;color:#94a3b8;text-transform:uppercase;letter-spacing:.5px;margin-bottom:12px;">QR Code</div>
              <img src="{{ $qrUrl }}" alt="QR Barcode" id="qr-img">
              <div class="sidebar-qr-code">{{ $inventaris->barcode ?? '-' }}</div>
              <div class="qr-actions">
                <a href="{{ $qrUrl }}" download="QR-{{ $inventaris->barcode ?? 'aset' }}.png" class="btn-qr btn-qr-download">
                  <i data-feather="download"></i> Download
                </a>
                <button type="button" class="btn-qr btn-qr-print" onclick="printQRCode()">
                  <i data-feather="printer"></i> Print
                </button>
              </div>
            </div>
            @endif

            {{-- Info Ringkas --}}
            <div class="sidebar-info-card">
              @if($inventaris->created_at)
              <div class="sic-row">
                <div class="sic-label">Dibuat</div>
                <div class="sic-value" style="font-weight:500;font-size:12px;">{{ $inventaris->created_at->format('d M Y') }}</div>
              </div>
              @endif
              @if($inventaris->updated_at)
              <div class="sic-row">
                <div class="sic-label">Diperbarui</div>
                <div class="sic-value" style="font-weight:500;font-size:12px;">{{ $inventaris->updated_at->format('d M Y') }}</div>
              </div>
              @endif
            </div>

          </div><!-- /.detail-sidebar -->

        </div><!-- /.detail-layout -->

        <!-- Hidden Forms -->
        @can('delete', $inventaris)
          <form id="deleteForm{{ $inventaris->id }}" action="{{ route('manajemen-aset.destroy', $inventaris->id) }}" method="POST" style="display:none;">
            @csrf
            @method('DELETE')
          </form>
        @endcan
      </div>
    </div>

    @include('layouts.footer-block')
    @include('layouts.footer-js')

    <script>
        function confirmDeleteInventaris(nama, id) {
          AppModal.confirmDelete(nama, function() {
            document.getElementById('deleteForm' + id).submit();
          });
        }

        function printQRCode() {
          var imgSrc = '{{ $qrUrl }}';
          var barcode = '{{ $inventaris->barcode ?? '' }}';
          var nama = '{{ addslashes($inventaris->display_name ?? '') }}';
          var win = window.open('', '_blank', 'width=420,height=520');
          win.document.write('<html><head><title>Print QR Code</title>');
          win.document.write('<style>');
          win.document.write('body{margin:0;display:flex;flex-direction:column;align-items:center;justify-content:center;min-height:100vh;font-family:sans-serif;background:#fff;}');
          win.document.write('img{width:220px;height:220px;border:1px solid #ccc;padding:12px;border-radius:8px;background:#fff;}');
          win.document.write('.code{margin-top:14px;font-size:13px;font-weight:700;color:#064635;letter-spacing:1px;font-family:monospace;}');
          win.document.write('.name{margin-top:5px;font-size:11px;color:#64748b;}');
          win.document.write('@media print{@page{margin:10mm;}}');
          win.document.write('</style></head><body>');
          win.document.write('<img src="' + imgSrc + '" alt="QR Code">');
          win.document.write('<div class="code">' + barcode + '</div>');
          win.document.write('<div class="name">' + nama + '</div>');
          win.document.write('</body></html>');
          win.document.close();
          win.focus();
          setTimeout(function(){ win.print(); }, 600);
        }

      // Initialize Feather Icons
      if (typeof feather !== 'undefined') {
        feather.replace();
      }
    </script>
  </body>
</html>
