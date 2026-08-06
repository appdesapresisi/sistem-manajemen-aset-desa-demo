<!DOCTYPE html>
<html lang="id">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <title>Informasi Aset — {{ $inventaris->display_name ?? $inventaris->kode_barang }}</title>
  <link rel="preconnect" href="https://fonts.googleapis.com">
  <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">
  <style>
    *, *::before, *::after { box-sizing: border-box; margin: 0; padding: 0; }

    body {
      font-family: 'Inter', sans-serif;
      background: linear-gradient(180deg, #064635 0%, #064635 300px, #f4faf5 240px, #f4faf5 100%);
      min-height: 100vh;
      min-height: 100vh;
    }

    /* ── PAGE WRAP ── */
    .page-wrap {
      max-width: 720px;
      margin: 0 auto;
      padding-bottom: 40px;
      position: relative;
      z-index: 1;
    }

    /* ── TOP BAR ── */
    .top-bar {
      width: 100%;
      padding: 32px 20px 24px;
      display: flex;
      flex-direction: column;
      align-items: center;
      justify-content: center;
      gap: 10px;
      max-width: 720px;
      margin: 0 auto;
      text-align: center;
    }
    .top-bar-logo {
      width: 64px; height: 64px;
      display: flex; align-items: center; justify-content: center;
      flex-shrink: 0;
    }
    .top-bar-logo img { width: 64px; height: 64px; object-fit: contain; filter: drop-shadow(0 2px 6px rgba(0,0,0,0.25)); }
    .top-bar-title { font-size: 20px; font-weight: 700; color: #fff; margin-top: 4px; }
    .top-bar-sub { font-size: 13px; color: rgba(255,255,255,0.75); margin-top: 2px; }

    /* ── IDENTITY CARD (status + nama + jenis + kode) ── */
    .identity-card {
      background: #fff;
      border-radius: 12px;
      border: 1px solid #e2e8f0;
      box-shadow: 0 1px 4px rgba(0,0,0,0.06);
      margin: 16px 16px 0;
      padding: 24px;
    }
    .status-pill {
      display: inline-flex; align-items: center; gap: 6px;
      padding: 4px 12px; border-radius: 20px; font-size: 11px; font-weight: 700;
      margin-bottom: 14px;
    }
    .status-pill .dot { width: 6px; height: 6px; border-radius: 50%; background: currentColor; }
    .identity-name {
      font-size: 22px; font-weight: 700; color: #1e293b;
      line-height: 1.3; margin-bottom: 10px;
    }
    .identity-meta-row {
      display: flex; align-items: center; gap: 8px;
      padding: 8px 0;
      border-top: 1px solid #f1f5f9;
      border-bottom: 1px solid #f1f5f9;
      margin-bottom: -1px;
    }
    .identity-meta-label {
      font-size: 11px; font-weight: 700; color: #94a3b8;
      text-transform: uppercase; letter-spacing: .4px;
      width: 150px; flex-shrink: 0;
      line-height: 1;
      padding: 0;
      margin: 0;
    }
    .identity-meta-value {
      font-size: 14px; font-weight: 600; color: #1e293b;
      line-height: 1;
    }

    /* ── CARD ── */
    .card {
      background: #fff;
      border-radius: 14px;
      border: 1px solid #e2e8f0;
      box-shadow: 0 1px 4px rgba(0,0,0,0.06);
      margin: 20px 16px 0;
      overflow: hidden;
    }
    .card-header {
      display: flex; align-items: center; gap: 8px;
      font-size: 12px; font-weight: 700; color: #064635;
      text-transform: uppercase; letter-spacing: .5px;
      padding: 16px 24px;
      border-bottom: 1px solid #f1f5f9;
    }
    .card-header svg { width: 14px; height: 14px; stroke: #064635; fill: none; stroke-width: 2.5; flex-shrink: 0; }

    /* ── DATA ROW (label kiri dekat nilai, vertikal) ── */
    .dl-row {
      display: flex; align-items: flex-start; gap: 12px;
      padding: 15px 24px;
      border-bottom: 1px solid #f4faf5;
    }
    .dl-row:last-child { border-bottom: none; }
    .dl-row:hover { background: #fafbfc; }
    .dl-label {
      width: 150px; flex-shrink: 0;
      font-size: 11px; font-weight: 700; color: #94a3b8;
      text-transform: uppercase; letter-spacing: .4px;
      padding-top: 2px;
    }
    .dl-value {
      flex: 1; font-size: 14px; color: #1e293b; font-weight: 500;
    }
    .dl-value.empty { color: #cbd5e1; font-style: italic; font-weight: 400; }

    /* ── BADGE ── */
    .kondisi-badge { display: inline-flex; align-items: center; gap: 5px; padding: 4px 10px; border-radius: 20px; font-size: 12px; font-weight: 600; }
    .kondisi-baik         { background: #dcfce7; color: #16a34a; }
    .kondisi-rusak-ringan { background: #fef3c7; color: #d97706; }
    .kondisi-rusak-berat  { background: #fee2e2; color: #dc2626; }

    /* ── FOTO ── */
    .foto-grid { display: flex; flex-wrap: wrap; gap: 10px; padding: 14px 20px 18px; }
    .foto-item { width: 90px; height: 90px; border-radius: 10px; overflow: hidden; border: 1px solid #e2e8f0; cursor: pointer; }
    .foto-item img { width: 100%; height: 100%; object-fit: cover; }

    /* ── QR ── */
    .qr-wrap { padding: 20px; text-align: center; }
    .qr-wrap img { width: 180px; height: 180px; border: 1px solid #e2e8f0; border-radius: 10px; padding: 8px; background: #fff; display: block; margin: 0 auto 12px; }
    .qr-code  { font-size: 12px; font-weight: 700; color: #475569; font-family: monospace; letter-spacing: 1px; margin-bottom: 4px; }
    .qr-hint  { font-size: 11px; color: #94a3b8; }

    /* ── FOOTER ── */
    .footer { 
      text-align: center; 
      font-size: 11px; 
      color: rgba(255,255,255,0.75); 
      line-height: 1.8; 
      background: #064635;
      width: 100%;
      padding: 80px 16px 32px;
      margin-top: -80px;
    }
    .footer a { color: #fff; font-weight: 600; text-decoration: none; }

    /* ── LIGHTBOX ── */
    .lightbox { display: none; position: fixed; inset: 0; background: rgba(0,0,0,0.85); z-index: 999; align-items: center; justify-content: center; }
    .lightbox.active { display: flex; }
    .lightbox img { max-width: 90vw; max-height: 85vh; border-radius: 10px; }
    .lightbox-close { position: fixed; top: 16px; right: 20px; color: #fff; font-size: 28px; cursor: pointer; line-height: 1; }

    /* ── SECTION DIVIDER & SUBHEADER (untuk 1 card gabungan) ── */
    .section-divider {
      height: 1px;
      background: #f3f4f6;
      margin: 0;
    }
    .section-subheader {
      display: flex; align-items: center; gap: 8px;
      font-size: 12px; font-weight: 700; color: #064635;
      text-transform: uppercase; letter-spacing: .4px;
      padding: 14px 24px 8px;
    }
    .section-subheader svg { width: 13px; height: 13px; stroke: #064635; fill: none; stroke-width: 2.5; flex-shrink: 0; }
    .not-found { margin: 40px 16px; background: #fff; border-radius: 12px; padding: 40px 24px; text-align: center; box-shadow: 0 1px 4px rgba(0,0,0,0.06); }
    .not-found svg { width: 48px; height: 48px; stroke: #cbd5e1; fill: none; stroke-width: 1.5; margin-bottom: 16px; }
    .not-found h3 { font-size: 18px; font-weight: 700; color: #1e293b; margin-bottom: 8px; }
    .not-found p  { font-size: 13px; color: #94a3b8; }

    /* ── RESPONSIVE MOBILE ── */
    @media (max-width: 480px) {
      .dl-label {
        width: 110px;
        font-size: 10px;
      }
      .identity-meta-label {
        width: 110px;
        font-size: 10px;
      }
    }
  </style>
</head>
<body>

  {{-- Top bar (full width, di luar page-wrap) --}}
  <div class="top-bar">
    <div class="top-bar-logo">
      <img src="{{ asset('assets/images/logo/logo-demo.png') }}" alt="Logo Mengwi" />
    </div>
    <div class="top-bar-title">Sistem Manajemen Aset</div>
    <div class="top-bar-sub">Desa Demo</div>
  </div>

<div class="page-wrap">

  @if($inventaris)
  @php
    $dynamicRows = collect();
    if ($inventaris->values && $inventaris->values->count() > 0) {
      $dynamicRows = $inventaris->values->filter(function($v) {
        return $v->field !== null && !empty($v->field->label_field) && !empty($v->value);
      })->sortBy(fn($v) => $v->field->order ?? 999);
    }
  @endphp

  {{-- === SATU CARD GABUNGAN (mulai dari identity) === --}}
  <div class="card">

    {{-- Identity: nama, jenis, kode --}}
    <div style="padding: 24px 24px 0;">
      <div class="identity-name">{{ $inventaris->display_name ?? $inventaris->kode_barang }}</div>
      @if($inventaris->jenis)
      <div class="identity-meta-row">
        <span class="identity-meta-label">Jenis Inventaris</span>
        <span class="identity-meta-value">{{ $inventaris->jenis->nama }}</span>
      </div>
      @endif
      @if($inventaris->kode_barang)
      <div class="identity-meta-row" style="border-bottom: none;">
        <span class="identity-meta-label">Kode Barang</span>
        <span class="identity-meta-value">{{ $inventaris->kode_barang }}</span>
      </div>
      @endif
    </div>

    <div class="section-divider"></div>

    {{-- Sub-section: Informasi Aset --}}
    <div class="card-header">
      <svg viewBox="0 0 24 24"><circle cx="12" cy="12" r="10"/><line x1="12" y1="8" x2="12" y2="12"/><line x1="12" y1="16" x2="12.01" y2="16"/></svg>
      Informasi Aset
    </div>
    @if($inventaris->nup)
    <div class="dl-row">
      <span class="dl-label">NUP</span>
      <span class="dl-value">{{ $inventaris->nup }}</span>
    </div>
    @endif
    @if($inventaris->jenis_barang)
    <div class="dl-row">
      <span class="dl-label">Jenis Barang</span>
      <span class="dl-value">{{ $inventaris->jenis_barang }}</span>
    </div>
    @endif
    @if($inventaris->tahun_perolehan)
    <div class="dl-row">
      <span class="dl-label">Tahun Perolehan</span>
      <span class="dl-value">{{ $inventaris->tahun_perolehan }}</span>
    </div>
    @endif
    @if($inventaris->nilai_perolehan)
    <div class="dl-row">
      <span class="dl-label">Nilai Perolehan</span>
      @php $npScan = (float)$inventaris->nilai_perolehan; @endphp
      <span class="dl-value">Rp {{ number_format($npScan, fmod($npScan, 1) != 0 ? 2 : 0, ',', '.') }}</span>
    </div>
    @endif
    @if($inventaris->kondisi)
    <div class="dl-row">
      <span class="dl-label">Kondisi</span>
      <span class="dl-value">
        @if($inventaris->kondisi === 'baik')
          <span class="kondisi-badge kondisi-baik">✓ {{ $inventaris->kondisi_label }}</span>
        @elseif($inventaris->kondisi === 'rusak_ringan')
          <span class="kondisi-badge kondisi-rusak-ringan">⚠ {{ $inventaris->kondisi_label }}</span>
        @elseif($inventaris->kondisi === 'rusak_berat')
          <span class="kondisi-badge kondisi-rusak-berat">✕ {{ $inventaris->kondisi_label }}</span>
        @endif
      </span>
    </div>
    @endif
    @if($inventaris->keterangan)
    <div class="dl-row">
      <span class="dl-label">Keterangan</span>
      <span class="dl-value">{{ $inventaris->keterangan }}</span>
    </div>
    @endif
    @if(!empty($inventaris->asal_usul))
    <div class="dl-row">
      <span class="dl-label">Asal Usul Barang</span>
      <span class="dl-value">
        @if($inventaris->asal_usul === 'apbdes') APBDes
        @elseif($inventaris->asal_usul === 'perolehan_lain') Perolehan Lain yang Sah
        @elseif($inventaris->asal_usul === 'kekayaan_asli') Kekayaan Asli Desa
        @else {{ $inventaris->asal_usul }}
        @endif
      </span>
    </div>
    @endif
    @if(!empty($inventaris->pengguna))
    <div class="dl-row">
      <span class="dl-label">Pengguna</span>
      <span class="dl-value">{{ $inventaris->pengguna }}</span>
    </div>
    @endif
    @foreach($dynamicRows as $v)
    <div class="dl-row">
      <span class="dl-label">{{ $v->field->label_field }}</span>
      <span class="dl-value">
        @if(\App\Models\JenisInventarisData::isCurrencyField($v->field->fixed_field_name ?? null, $v->field->tipe ?? null, $v->field->nama_field ?? null, $v->field->label_field ?? null))
          @php $scanNum = (float) $v->value; @endphp
          Rp {{ number_format($scanNum, fmod($scanNum, 1) != 0 ? 2 : 0, ',', '.') }}
        @elseif(($v->field->tipe ?? null) === 'decimal_m2')
          @php $scanM2 = (float) $v->value; @endphp
          {{ number_format($scanM2, fmod($scanM2, 1) != 0 ? 2 : 0, ',', '.') }} m²
        @elseif(($v->field->tipe ?? null) === 'decimal_km')
          @php $scanKm = (float) $v->value; @endphp
          {{ number_format($scanKm, fmod($scanKm, 1) != 0 ? 2 : 0, ',', '.') }} km
        @elseif(($v->field->tipe ?? null) === 'decimal_m')
          @php $scanM = (float) $v->value; @endphp
          {{ number_format($scanM, fmod($scanM, 1) != 0 ? 2 : 0, ',', '.') }} m
        @else
          {{ $v->value }}
        @endif
      </span>
    </div>
    @endforeach

    {{-- Sub-section: Data KIB --}}
    @if($inventaris->jenis && $inventaris->jenis->kib_type)
    @php
      $kibType = $inventaris->jenis->kib_type;
      $kibConf = config("kib.{$kibType}");
      $kibFields = $kibConf['fields'] ?? [];
    @endphp
    @if(!empty($kibFields))
    <div class="section-divider"></div>
    <div class="section-subheader">
      <svg viewBox="0 0 24 24"><path d="M4 19.5A2.5 2.5 0 0 1 6.5 17H20"/><path d="M6.5 2H20v20H6.5A2.5 2.5 0 0 1 4 19.5v-15A2.5 2.5 0 0 1 6.5 2z"/></svg>
      Data KIB {{ $kibType }} — {{ $kibConf['nama'] ?? '' }}
    </div>
    @foreach($kibFields as $kf)
    @php $kibVal = $inventaris->getKibValue($kf['key']); @endphp
    <div class="dl-row">
      <span class="dl-label">{{ $kf['label'] }}</span>
      <span class="dl-value {{ empty($kibVal) ? 'empty' : '' }}">
        @if(!empty($kibVal))
          @if(($kf['type'] ?? 'text') === 'decimal')
            @php $dv = (float)$kibVal; @endphp
            {{ number_format($dv, fmod($dv, 1) != 0 ? 2 : 0, ',', '.') }}
          @else {{ $kibVal }}
          @endif
        @else -
        @endif
      </span>
    </div>
    @endforeach
    @endif
    @endif

    {{-- Sub-section: Data KIR --}}
    @if($inventaris->jenis && $inventaris->jenis->requires_ruangan)
    @php
      $kirFields = [
        ['label' => 'Ruangan', 'key' => null],
        ['label' => 'Konstruksi', 'key' => 'kir_konstruksi'],
        ['label' => 'Luas', 'key' => 'kir_luas'],
        ['label' => 'Merk / Tipe', 'key' => 'kir_merk_tipe'],
        ['label' => 'Warna', 'key' => 'kir_warna'],
        ['label' => 'Judul', 'key' => 'kir_judul'],
        ['label' => 'Pencipta', 'key' => 'kir_pencipta'],
        ['label' => 'No. Polisi', 'key' => 'kir_no_pol'],
      ];
    @endphp
    <div class="section-divider"></div>
    <div class="section-subheader">
      <svg viewBox="0 0 24 24"><rect x="3" y="3" width="7" height="7"/><rect x="14" y="3" width="7" height="7"/><rect x="14" y="14" width="7" height="7"/><rect x="3" y="14" width="7" height="7"/></svg>
      Data Kartu Inventaris Ruangan (KIR)
    </div>
    <div class="dl-row">
      <span class="dl-label">Ruangan</span>
      <span class="dl-value {{ $inventaris->ruangan ? '' : 'empty' }}">{{ $inventaris->ruangan?->nama ?? '-' }}</span>
    </div>
    @foreach($kirFields as $kir)
    @if($kir['key'] === null) @continue @endif
    @php $kirVal = $inventaris->getKirValue($kir['key']); @endphp
    <div class="dl-row">
      <span class="dl-label">{{ $kir['label'] }}</span>
      <span class="dl-value {{ empty($kirVal) ? 'empty' : '' }}">{{ !empty($kirVal) ? $kirVal : '-' }}</span>
    </div>
    @endforeach
    @endif

    {{-- Sub-section: Data Laporan Aset --}}
    @if(!empty($inventaris->satuan))
    <div class="section-divider"></div>
    <div class="section-subheader">
      <svg viewBox="0 0 24 24"><line x1="18" y1="20" x2="18" y2="10"/><line x1="12" y1="20" x2="12" y2="4"/><line x1="6" y1="20" x2="6" y2="14"/></svg>
      Data Laporan Aset
    </div>
    <div class="dl-row">
      <span class="dl-label">Satuan</span>
      <span class="dl-value">{{ $inventaris->satuan }}</span>
    </div>
    @endif

  </div>{{-- end card gabungan --}}

  {{-- QR Code — card terpisah --}}
  @if(!empty($qrUrl))
  <div class="card">
    <div class="card-header">
      <svg viewBox="0 0 24 24"><rect x="3" y="3" width="7" height="7"/><rect x="14" y="3" width="7" height="7"/><rect x="3" y="14" width="7" height="7"/><rect x="14" y="14" width="3" height="3"/><rect x="18" y="18" width="3" height="3"/></svg>
      QR Code
    </div>
    <div class="qr-wrap">
      <img src="{{ $qrUrl }}" alt="QR Code" />
      <div class="qr-code">{{ $inventaris->barcode }}</div>
      <div class="qr-hint">Scan untuk akses informasi aset ini</div>
    </div>
  </div>
  @endif

  {{-- Foto Aset — paling bawah --}}
  @if($inventaris->fotos && $inventaris->fotos->count() > 0)
  <div class="card">
    <div class="card-header">
      <svg viewBox="0 0 24 24"><rect x="3" y="3" width="18" height="18" rx="2"/><circle cx="8.5" cy="8.5" r="1.5"/><polyline points="21 15 16 10 5 21"/></svg>
      Foto Aset
    </div>
    <div class="foto-grid">
      @foreach($inventaris->fotos as $foto)
        <div class="foto-item" onclick="openLightbox('{{ asset('storage/' . $foto->path) }}')">
          <img src="{{ asset('storage/' . $foto->path) }}" alt="Foto Aset" loading="lazy" />
        </div>
      @endforeach
    </div>
  </div>
  @endif

  @else
  {{-- Inventaris tidak ditemukan --}}
  <div class="not-found">
    <svg viewBox="0 0 24 24"><circle cx="12" cy="12" r="10"/><line x1="15" y1="9" x2="9" y2="15"/><line x1="9" y1="9" x2="15" y2="15"/></svg>
    <h3>Aset Tidak Ditemukan</h3>
    <p>QR code ini tidak terhubung dengan data inventaris manapun.</p>
  </div>
  @endif
  
</div>{{-- /.page-wrap --}}

  {{-- Footer — di luar page-wrap, full width --}}
  <div class="footer">
    Sistem Dikelola Oleh Desa Dinas &nbsp;•&nbsp; Informasi Aset ini Bersifat Publik<br>
    <a href="#">Sistem Manajemen Aset Desa Demo</a>
  </div>

  {{-- Lightbox --}}
  <div class="lightbox" id="lightbox" onclick="closeLightbox()">
    <span class="lightbox-close">&times;</span>
    <img id="lightbox-img" src="" alt="Foto" />
  </div>

  <script>
    function openLightbox(src) {
      document.getElementById('lightbox-img').src = src;
      document.getElementById('lightbox').classList.add('active');
    }
    function closeLightbox() {
      document.getElementById('lightbox').classList.remove('active');
    }
    document.addEventListener('keydown', function(e) {
      if (e.key === 'Escape') closeLightbox();
    });
  </script>
</body>
</html>
