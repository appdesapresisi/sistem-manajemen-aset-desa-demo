<!doctype html>
<html lang="en" data-pc-preset="{{ $pc_preset_theme }}" data-pc-theme="{{ $pc_dark_layout }}">
  <head>
    @include('layouts.head-page-meta', ['title' => 'Detail Inventaris'])
    @include('layouts.head-css')
    <style>
      /* ===== QR LABEL MODAL & PREVIEWS (SIMA Palette) ===== */
      /* ===== QR LABEL MODAL & PREVIEWS (SIMA Palette) ===== */
      .qr-modal-overlay {
        position: fixed; inset: 0; background: rgba(31, 58, 42, 0.45);
        display: flex; align-items: center; justify-content: center;
        z-index: 9999; padding: 24px; backdrop-filter: blur(4px);
        opacity: 0; pointer-events: none; transition: opacity .2s ease;
      }
      .qr-modal-overlay.active {
        opacity: 1; pointer-events: auto;
      }
      .qr-modal-card {
        background: var(--color-surface, #f8faf7);
        border: 1px solid var(--color-border, #cfdcc8);
        border-radius: var(--radius-lg, 16px);
        width: 100%; max-width: 600px;
        box-shadow: var(--shadow-float, 0 10px 30px rgba(0,0,0,0.12));
        overflow: hidden;
        display: flex; flex-direction: column; max-height: 90vh;
        transform: scale(0.96); transition: transform .2s ease;
      }
      .qr-modal-overlay.active .qr-modal-card {
        transform: scale(1);
      }
      .qr-modal-header {
        padding: 20px 24px;
        border-bottom: none !important;
        display: flex; flex-direction: column; align-items: center; justify-content: center;
        position: relative;
        background: var(--color-surface, #f8faf7);
        text-align: center;
      }
      .qr-modal-title { font-size: 16px; font-weight: 700; color: var(--color-text, #16251b); margin: 0; text-align: center; }
      .qr-modal-sub { font-size: 12px; color: var(--color-text-muted, #546958); margin-top: 3px; text-align: center; }
      .qr-modal-close {
        position: absolute; right: 20px; top: 50%; transform: translateY(-50%);
        border: none !important; background: transparent !important; box-shadow: none !important;
        width: 32px; height: 32px; padding: 0; border-radius: 0;
        display: flex; align-items: center; justify-content: center;
        cursor: pointer; color: var(--color-text-muted, #546958); transition: color .15s ease, transform .15s ease;
      }
      .qr-modal-close:hover { background: transparent !important; border: none !important; color: var(--color-text, #16251b); transform: translateY(-50%) scale(1.1); }
      .qr-modal-body {
        padding: 20px 24px; overflow-y: auto; display: flex; flex-direction: column; gap: 18px;
        background: var(--color-surface, #f8faf7);
      }
      
      /* Option Format Selector */
      .format-options-grid {
        display: grid; grid-template-columns: 1fr 1fr; gap: 12px;
      }
      .format-option-btn {
        border: 1px solid var(--color-border, #cfdcc8);
        border-radius: var(--radius-sm, 10px);
        padding: 12px 14px;
        background: var(--color-bg, #eaf1e7);
        cursor: pointer; text-align: left; transition: all .2s;
        display: flex; flex-direction: column; gap: 4px; position: relative;
      }
      .format-option-btn:hover {
        border-color: var(--color-primary, #1f3a2a); background: var(--color-row-hover, #eff5ed);
      }
      .format-option-btn.active {
        border: 1.5px solid var(--color-primary, #1f3a2a);
        background: var(--color-primary-tint, #d5e4d7);
      }
      .format-opt-title {
        font-size: 13px; font-weight: 700; color: var(--color-text, #16251b); display: flex; align-items: center; gap: 6px;
      }
      .format-opt-desc {
        font-size: 11px; color: var(--color-text-muted, #546958); line-height: 1.4;
      }

      /* Preview Box */
      .preview-wrapper {
        background: var(--color-bg, #eaf1e7);
        border: 1px dashed var(--color-border, #cfdcc8);
        border-radius: var(--radius-md, 12px);
        padding: 20px; display: flex; flex-direction: column; align-items: center; justify-content: center;
        min-height: 200px;
      }
      .preview-heading {
        font-size: 11px; font-weight: 700; color: var(--color-text-muted, #546958); text-transform: uppercase;
        letter-spacing: .5px; margin-bottom: 10px;
      }

      /* Template 1: Sticker Official Desa Demo (3-Section Box) */
      .sticker-preview-box {
        width: 100%; max-width: 480px; background: #fff;
        border: 2px solid #111827; border-radius: 4px; overflow: hidden;
        box-shadow: none; font-family: 'Inter', sans-serif;
        box-sizing: border-box;
      }
      .sticker-preview-header {
        background: #c81e1e; color: #fff; text-align: center;
        padding: 7px 10px; font-weight: 800; font-size: 13px;
        letter-spacing: 1.2px; text-transform: uppercase; border-bottom: 2px solid #111827;
      }
      .sticker-preview-body {
        display: flex; background: #fff; height: 110px;
      }
      .sticker-preview-box-square {
        width: 110px; height: 110px; flex-shrink: 0;
        display: flex; align-items: center; justify-content: center;
        background: #ffffff;
      }
      .sticker-preview-box-square.left {
        border-right: 2px solid #111827;
      }
      .sticker-preview-box-square.right {
        border-left: 2px solid #111827;
      }
      .sticker-preview-logo {
        width: 60px; height: 60px; object-fit: contain;
      }
      .sticker-preview-qr {
        width: 78px; height: 78px; object-fit: contain;
      }
      .sticker-preview-box-center {
        flex: 1; min-width: 0; display: flex; flex-direction: column; justify-content: stretch; overflow: hidden;
      }
      .sticker-preview-cell {
        flex: 1; min-width: 0; width: 100%; padding: 4px 8px; display: flex; flex-direction: column; align-items: center; justify-content: center; gap: 2px; overflow: hidden;
      }
      .sticker-preview-cell.top {
        border-bottom: 2px solid #111827;
      }
      .sticker-cell-value {
        font-size: 14px; font-weight: 800; color: #000000; font-family: monospace, sans-serif; letter-spacing: 0.5px; text-align: center; line-height: 1.2;
        white-space: nowrap; overflow: hidden; text-overflow: ellipsis; max-width: 100%; display: block;
      }

      /* Template 2: Plain QR Box */
      .plain-preview-box {
        background: #fff; border: 1px solid var(--color-border, #cfdcc8); border-radius: 12px;
        padding: 20px; text-align: center; box-shadow: none;
        flex-direction: column; align-items: center; justify-content: center;
        width: 100%; max-width: 200px; margin: 0 auto; box-sizing: border-box;
      }
      .plain-preview-box img {
        width: 150px; height: 150px; border: none; border-radius: 6px; display: block; margin: 0 auto;
      }

      /* Modal Footer */
      .qr-modal-footer {
        padding: 16px 24px 24px; border-top: none !important;
        background: var(--color-surface, #f8faf7);
        display: flex; align-items: center; justify-content: center; gap: 12px;
        flex-wrap: wrap;
      }

      @media (max-width: 640px) {
        .qr-modal-overlay {
          padding: 12px;
        }
        .qr-modal-card {
          max-height: 94vh;
          border-radius: 14px;
        }
        .qr-modal-header {
          padding: 16px 18px;
        }
        .qr-modal-body {
          padding: 16px 18px;
          gap: 16px;
        }
        .format-options-grid {
          grid-template-columns: 1fr;
          gap: 10px;
        }
        .format-option-btn {
          padding: 12px 14px;
        }
        .preview-wrapper {
          padding: 16px 10px;
          min-height: 140px;
          overflow-x: hidden;
        }
        .sticker-preview-box {
          max-width: 100%;
        }
        .sticker-preview-header {
          font-size: 11px;
          padding: 6px 4px;
          letter-spacing: 1px;
          white-space: nowrap;
          overflow: hidden;
          text-overflow: ellipsis;
        }
        .sticker-preview-body {
          height: 80px;
        }
        .sticker-preview-box-square {
          width: 70px;
          height: 80px;
          min-width: 70px;
        }
        .sticker-preview-logo {
          width: 44px;
          height: 44px;
        }
        .sticker-preview-qr {
          width: 54px;
          height: 54px;
        }
        .sticker-preview-cell {
          padding: 2px 4px;
        }
        .sticker-cell-value {
          font-size: 11px;
          letter-spacing: 0.2px;
          max-width: 100%;
          white-space: nowrap;
          overflow: hidden;
          text-overflow: ellipsis;
        }
        .qr-modal-footer {
          padding: 14px 18px;
          flex-direction: column;
          gap: 8px;
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
        @include('layouts.breadcrumb', [
          'breadcrumbItemHome' => 'Inventaris Desa',
          'breadcrumbItem' => 'Buku Inventaris',
          'breadcrumbItemUrl' => route('manajemen-aset.index'),
          'breadcrumbItemActive' => 'Detail Aset'
        ])

        <!-- Back Link & Action Bar Top -->
        <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 20px; flex-wrap: wrap; gap: 12px;">
          <a href="{{ route('manajemen-aset.index') }}" class="btn-sima-secondary" style="border:none !important; padding-left: 0 !important; background-color: transparent !important;" onclick="try { sessionStorage.setItem('manajemen_aset_returning', '1'); } catch(e) {} if(window.history.length > 1 && document.referrer && document.referrer.includes('/manajemen-aset') && !document.referrer.includes('/edit') && !document.referrer.includes('/create')) { window.history.back(); return false; }">
            <i data-feather="arrow-left" style="width: 14px; height: 14px;"></i>
            <span>Kembali ke Daftar Aset</span>
          </a>
          <div style="display: flex; gap: 10px; align-items: center;">
            @can('update', $inventaris)
            <a href="{{ route('manajemen-aset.edit', $inventaris->id) }}?from=show" class="btn-sima-primary">
              <i data-feather="edit-2" style="width: 14px; height: 14px;"></i>
              <span>Edit Aset</span>
            </a>
            @endcan
            @if(auth()->user()->canDeleteAset())
              <button type="button" class="btn-sima-danger" data-nama="{{ $inventaris->display_name ?? '' }}" onclick="confirmDeleteInventaris(this.getAttribute('data-nama'), '{{ $inventaris->id }}')">
                <i data-feather="trash-2" style="width: 14px; height: 14px;"></i>
                <span>Hapus Aset</span>
              </button>
            @endif
          </div>
        </div>

        <!-- ===== MAIN PORTAL CARD (Spacious & Modern) ===== -->
        <div class="portal-card">

          <!-- Hero 2-Column: Left (Gallery) & Right (Overview Metrics + Documents) -->
          <div class="portal-hero-grid" style="padding-top: 32px; margin-bottom: 0;">

            <!-- KIRI: Foto Showcase -->
            <div class="portal-gallery">
              @php
                $fotos = $inventaris->fotos;
                $firstFoto = $fotos->first();
                $fotoUrls = $fotos->map(function($f) {
                    return Storage::url($f->path);
                })->values();
              @endphp
              <div class="portal-gallery-main" id="portal-main-foto-box">
                @if($firstFoto)
                  <div class="portal-main-foto-trigger" onclick="openSimaFotoViewer()" style="width:100%;height:100%;display:flex;align-items:center;justify-content:center;cursor:zoom-in;" title="Klik untuk memperbesar (Layar Penuh)">
                    <img src="{{ Storage::url($firstFoto->path) }}" id="portal-main-foto-img" alt="{{ $inventaris->display_name ?? 'Foto Aset' }}" style="cursor:zoom-in;">
                  </div>
                @else
                  <div class="portal-gallery-placeholder">
                    <i data-feather="camera"></i>
                    <div style="font-weight: 600; color: var(--color-text);">Belum Ada Foto Aset</div>
                    <div style="font-size: 12px; color: var(--color-text-muted);">Foto aset dapat diunggah melalui menu Edit Aset</div>
                  </div>
                @endif

                @if($fotos->count() > 1)
                  <div class="portal-gallery-thumbs">
                    @foreach($fotos as $idx => $f)
                      <div class="portal-gallery-thumb {{ $idx === 0 ? 'active' : '' }}" onclick="switchPortalFoto('{{ Storage::url($f->path) }}', {{ $idx }}, this)" title="Lihat Foto {{ $loop->iteration }}">
                        <img src="{{ Storage::url($f->path) }}" alt="Thumbnail {{ $loop->iteration }}">
                      </div>
                    @endforeach
                  </div>
                @endif
              </div>
            </div>

            <!-- KANAN: Ringkasan Nilai & Spesifikasi + Berkas -->
            <div class="portal-overview-column">
              <div>
                <div class="portal-overview-title"><span>Ringkasan Data Aset</span>
                </div>
                <div class="portal-stats-grid" style="grid-template-columns: repeat(2, 1fr); gap: 24px 36px;">
                  <div class="portal-stat-item">
                    <span class="portal-stat-label">Nama Aset</span>
                    <span class="portal-stat-value primary-highlight" style="font-size: 15px; font-weight: 700;">{{ $inventaris->display_name ?? '-' }}</span>
                  </div>

                  <div class="portal-stat-item">
                    <span class="portal-stat-label">Kondisi</span>
                    <span class="portal-stat-value">
                      @if($inventaris->kondisi === 'baik') Baik (B)
                      @elseif($inventaris->kondisi === 'rusak_ringan') Kurang Baik (KB)
                      @elseif($inventaris->kondisi === 'rusak_berat') Rusak Berat (RB)
                      @else -
                      @endif
                    </span>
                  </div>

                  @php $npVal = (float)($inventaris->nilai_perolehan ?? 0); @endphp
                  <div class="portal-stat-item">
                    <span class="portal-stat-label">Nilai Perolehan</span>
                    <span class="portal-stat-value primary-highlight">Rp {{ number_format($npVal, fmod($npVal, 1) != 0 ? 2 : 0, ',', '.') }}</span>
                  </div>

                  <div class="portal-stat-item">
                    <span class="portal-stat-label">Asal Usul</span>
                    <span class="portal-stat-value">
                      @if($inventaris->asal_usul === 'apbdes') APBDes
                      @elseif($inventaris->asal_usul === 'perolehan_lain') Perolehan Lain
                      @elseif($inventaris->asal_usul === 'kekayaan_asli') Kekayaan Asli
                      @else -
                      @endif
                    </span>
                  </div>

                  <div class="portal-stat-item">
                    <span class="portal-stat-label">Pengguna</span>
                    <span class="portal-stat-value">{{ $inventaris->pengguna ?: '-' }}</span>
                  </div>

                  <div class="portal-stat-item">
                    <span class="portal-stat-label">Satuan</span>
                    <span class="portal-stat-value">{{ $inventaris->satuan ?: '-' }}</span>
                  </div>
                </div>
              </div>

              <!-- Available Documents / Label Bar -->
              @if(!empty($qrUrl))
              <div class="portal-docs-section" style="border-top: none; padding-top: 24px;">
                <div style="font-size: 11px; font-weight: 700; text-transform: uppercase; letter-spacing: 0.5px; color: var(--color-text-muted); margin-bottom: 2px;">
                  Dokumen & Label QR Tersedia
                </div>

                <div class="portal-doc-card">
                  <div class="portal-doc-left">
                    <div class="portal-doc-icon">
                      <i data-feather="printer"></i>
                    </div>
                    <div class="portal-doc-info">
                      <span class="portal-doc-name">Stiker Resmi Label Aset</span>
                      <span class="portal-doc-sub">Format Stiker Fisik & QR Code (PNG)</span>
                    </div>
                  </div>
                  <button type="button" class="portal-doc-btn" onclick="openQrLabelModal('preview')">
                    <i data-feather="printer" style="width: 13px; height: 13px;"></i>
                    <span>Cetak</span>
                  </button>
                </div>

                <div class="portal-doc-card">
                  <div class="portal-doc-left">
                    <div class="portal-doc-icon">
                      <i data-feather="hash"></i>
                    </div>
                    <div class="portal-doc-info">
                      <span class="portal-doc-name">{{ $inventaris->barcode ?? 'Aset ID' }}</span>
                      <span class="portal-doc-sub">Barcode Sistem SIMA Desa</span>
                    </div>
                  </div>
                  <button type="button" class="portal-doc-btn" onclick="downloadSelectedLabel()">
                    <i data-feather="download" style="width: 13px; height: 13px;"></i>
                    <span>Unduh</span>
                  </button>
                </div>
              </div>
              @endif

            </div>
          </div>

          <!-- Section: Informasi Rincian Inventaris -->
          @php
            $skipFieldKeys = [
              'nama_barang',
              'nilai_perolehan', 'kondisi', 'asal_usul', 'pengguna', 'satuan'
            ];
            
            $detailFields = collect();
            if ($inventaris->jenis && $inventaris->jenis->fields) {
              $detailFields = $inventaris->jenis->fields->filter(function($f) use ($skipFieldKeys) {
                $fix = strtolower(trim($f->fixed_field_name ?? ''));
                $name = strtolower(trim($f->nama_field ?? ''));
                return !in_array($fix, $skipFieldKeys) && !in_array($name, $skipFieldKeys);
              });
            }
          @endphp

          <div class="portal-section" style="border-top: none; margin-top: 0; padding-top: 36px;">
            <div class="portal-section-header">
              <h2 class="portal-section-title">
                <span>Informasi Rincian Inventaris</span>
              
              </h2>
            </div>

            <div class="portal-kv-grid grid-4">
              <div class="portal-kv-item">
                <span class="portal-kv-label">Jenis Inventaris</span>
                <span class="portal-kv-value">{{ $inventaris->jenis->nama ?? '-' }}</span>
              </div>

              @if($detailFields->count() > 0)
                @foreach($detailFields as $field)
                  @php
                    $fieldVal = $field->getValueFromInventaris($inventaris);
                    $isEmpty  = ($fieldVal === '' || $fieldVal === null || $fieldVal === '-');
                  @endphp
                  <div class="portal-kv-item">
                    <span class="portal-kv-label">{{ $field->label_field }}</span>
                    <span class="portal-kv-value {{ $isEmpty ? 'empty' : '' }}">
                      @if($field->field_type === 'fixed' && $field->fixed_field_name === 'ruangan_id')
                        {{ $inventaris->ruangan?->nama_with_kode ?: '-' }}
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
                  </div>
                @endforeach
              @else
                <div class="portal-kv-item">
                  <span class="portal-kv-label">Kode Barang</span>
                  <span class="portal-kv-value">{{ $inventaris->kode_barang ?: '-' }}</span>
                </div>
                <div class="portal-kv-item">
                  <span class="portal-kv-label">NUP</span>
                  <span class="portal-kv-value">{{ $inventaris->nup ?: '-' }}</span>
                </div>
                <div class="portal-kv-item">
                  <span class="portal-kv-label">Tahun Perolehan</span>
                  <span class="portal-kv-value">{{ $inventaris->tahun_perolehan ?: '-' }}</span>
                </div>
                @if($inventaris->ruangan)
                <div class="portal-kv-item">
                  <span class="portal-kv-label">Ruangan</span>
                  <span class="portal-kv-value">{{ $inventaris->ruangan->nama_with_kode }}</span>
                </div>
                @endif
              @endif

              @if(!($inventaris->jenis?->isVirtual() ?? false))
                <div class="portal-kv-item">
                  <span class="portal-kv-label">Bukti Kepemilikan</span>
                  <span class="portal-kv-value {{ !$inventaris->bukti_kepemilikan ? 'empty' : '' }}">{{ $inventaris->bukti_kepemilikan ?: '-' }}</span>
                </div>
                <div class="portal-kv-item">
                  <span class="portal-kv-label">Bulan Perolehan</span>
                  @php
                    $bulanNames = [
                      1 => 'Januari', 2 => 'Februari', 3 => 'Maret', 4 => 'April',
                      5 => 'Mei', 6 => 'Juni', 7 => 'Juli', 8 => 'Agustus',
                      9 => 'September', 10 => 'Oktober', 11 => 'November', 12 => 'Desember'
                    ];
                    $bp = $inventaris->getBulanPerolehan();
                  @endphp
                  <span class="portal-kv-value {{ !$bp ? 'empty' : '' }}">{{ $bp ? ($bulanNames[(int)$bp] ?? $bp) : '-' }}</span>
                </div>
              @endif
            </div>
          </div>

          <!-- Section: Data KIB (Kartu Inventaris Barang) -->
          @if(!empty($kibConfig['fields']))
          <div class="portal-section" style="border-top: none; margin-top: 0; padding-top: 36px;">
            <div class="portal-section-header">
              <h2 class="portal-section-title">
                <span>Data Kartu Inventaris Barang (KIB {{ $inventaris->jenis->kib_type }} — {{ $kibConfig['nama'] ?? '' }})</span>
              
              </h2>
            </div>
            <div class="portal-kv-grid grid-4">
              @foreach($kibConfig['fields'] as $kibField)
                @php
                  $kibVal = $inventaris->getKibValue($kibField['key']);
                  $kibEmpty = ($kibVal === null || $kibVal === '');
                @endphp
                <div class="portal-kv-item">
                  <span class="portal-kv-label">{{ $kibField['label'] }}</span>
                  <span class="portal-kv-value {{ $kibEmpty ? 'empty' : '' }}">
                    @if($kibEmpty)
                      -
                    @elseif($kibField['type'] === 'decimal')
                      @php $dv = (float)$kibVal; @endphp
                      {{ number_format($dv, fmod($dv, 1) != 0 ? 2 : 0, ',', '.') }}
                    @else
                      {{ $kibVal }}
                    @endif
                  </span>
                </div>
              @endforeach
            </div>
          </div>
          @endif

          <!-- Section: Data KIR (Kartu Inventaris Ruangan) -->
          @if($inventaris->jenis && $inventaris->jenis->requires_ruangan)
          <div class="portal-section" style="border-top: none; margin-top: 0; padding-top: 36px;">
            <div class="portal-section-header">
              <h2 class="portal-section-title">
                <span>Data Kartu Inventaris Ruangan (KIR)</span>
              
              </h2>
            </div>
            <div class="portal-kv-grid grid-4">
              <div class="portal-kv-item">
                <span class="portal-kv-label">Ruangan</span>
                <span class="portal-kv-value {{ $inventaris->ruangan ? '' : 'empty' }}">
                  {{ $inventaris->ruangan?->nama_with_kode ?? '-' }}
                </span>
              </div>
              @php
                 $kirFieldsShow = [
                   ['label' => 'Konstruksi', 'val' => $inventaris->getKirValue('kir_konstruksi')],
                   ['label' => 'Luas', 'val' => $inventaris->getKirValue('kir_luas')],
                   ['label' => 'Merk / Tipe', 'val' => $inventaris->getKirValue('kir_merk_tipe')],
                   ['label' => 'Warna', 'val' => $inventaris->getKirValue('kir_warna')],
                   ['label' => 'Judul', 'val' => $inventaris->getKirValue('kir_judul')],
                   ['label' => 'Pencipta', 'val' => $inventaris->getKirValue('kir_pencipta')],
                   ['label' => 'No. Polisi', 'val' => $inventaris->getKirValue('kir_no_pol')],
                   ['label' => 'No. Seri Pabrik', 'val' => $inventaris->getKirValue('kir_no_seri_pabrik')],
                   ['label' => 'Panjang (P)', 'val' => $inventaris->getKirValue('kir_panjang')],
                   ['label' => 'Lebar (L)', 'val' => $inventaris->getKirValue('kir_lebar')],
                   ['label' => 'Tinggi (T)', 'val' => $inventaris->getKirValue('kir_tinggi')],
                   ['label' => 'Bahan', 'val' => $inventaris->getKirValue('kir_bahan')],
                 ];
               @endphp
              @foreach($kirFieldsShow as $kfs)
                @if(!is_null($kfs['val']) && trim((string)$kfs['val']) !== '')
                  <div class="portal-kv-item">
                    <span class="portal-kv-label">{{ $kfs['label'] }}</span>
                    <span class="portal-kv-value">{{ $kfs['val'] }}</span>
                  </div>
                @endif
              @endforeach
            </div>
          </div>
          @endif

          <!-- Section: Audit Log & Catatan Sistem -->
          <div class="portal-section" style="border-top: none; margin-top: 0; padding-top: 36px; margin-bottom: 32px;">
            <div class="portal-section-header">
              <h2 class="portal-section-title">
                <span>Informasi Sistem & Audit Data</span>
              </h2>
            </div>
            <div class="portal-kv-grid grid-4" style="font-size: 12.5px;">
              <div class="portal-kv-item">
                <span class="portal-kv-label">ID Barcode / UID</span>
                <span class="portal-kv-value" style="font-family: monospace;">{{ $inventaris->barcode ?? '-' }}</span>
              </div>
              <div class="portal-kv-item">
                <span class="portal-kv-label">Tanggal Dibuat</span>
                <span class="portal-kv-value">{{ $inventaris->created_at ? $inventaris->created_at->format('d M Y, H:i') : '-' }}</span>
              </div>
              <div class="portal-kv-item">
                <span class="portal-kv-label">Terakhir Diperbarui</span>
                <span class="portal-kv-value">{{ $inventaris->updated_at ? $inventaris->updated_at->format('d M Y, H:i') : '-' }}</span>
              </div>
              <div class="portal-kv-item">
                <span class="portal-kv-label">Keterangan Tambahan</span>
                <span class="portal-kv-value {{ !$inventaris->keterangan ? 'text-empty-fallback' : '' }}">{{ $inventaris->keterangan ?: '-' }}</span>
              </div>
            </div>
          </div>

        </div><!-- /.portal-card -->

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
          AppModal.show({
            customHtml: `
              <div class="modal-header">
                <div class="modal-icon danger">
                  <i data-feather="trash-2"></i>
                </div>
                <h3 class="modal-title">Hapus Aset Inventaris</h3>
                <p class="modal-message">Apakah Anda yakin ingin menghapus data aset <strong style="color: var(--color-text, #16251b);">"${nama || 'Aset'}"</strong>?</p>
              </div>
              <div class="modal-body">
                <div class="modal-opt-group">
                  <label class="modal-opt-card active" id="opt-card-mutasi" onclick="selectDeleteOpt('mutasi_berkurang')">
                    <input type="radio" name="delete_type_opt" value="mutasi_berkurang" checked>
                    <span class="modal-opt-indicator"></span>
                    <div>
                      <div class="modal-opt-card-title">Penghapusan Resmi (Mutasi)</div>
                      <div class="modal-opt-card-desc">Memerlukan Berita Acara / SK di modul Penghapusan</div>
                    </div>
                  </label>

                  <label class="modal-opt-card" id="opt-card-salah" onclick="selectDeleteOpt('salah_input')">
                    <input type="radio" name="delete_type_opt" value="salah_input">
                    <span class="modal-opt-indicator"></span>
                    <div>
                      <div class="modal-opt-card-title">Salah Input / Percobaan</div>
                      <div class="modal-opt-card-desc">Hapus data langsung secara permanen</div>
                    </div>
                  </label>
                </div>
              </div>
              <div class="modal-footer">
                <button type="button" class="modal-btn modal-btn-secondary" onclick="AppModal.handleCancel()">
                  Batal
                </button>
                <button type="button" id="btn-show-delete-confirm" class="modal-btn modal-btn-primary" onclick="AppModal.handleConfirm()">
                  Lanjutkan
                </button>
              </div>
            `,
            onConfirm: function() {
              const tipeHapus = document.querySelector('input[name="delete_type_opt"]:checked')?.value || 'mutasi_berkurang';
              
              if (tipeHapus === 'mutasi_berkurang') {
                AppModal.close();
                window.location.href = "{{ route('penghapusan-aset.index') }}?inventaris_id=" + id;
                return;
              }

              const token = document.querySelector('meta[name="csrf-token"]').getAttribute('content');
              const form = document.createElement('form');
              form.method = 'POST';
              form.action = '{{ url("manajemen-aset") }}/' + id;
              
              const inputToken = document.createElement('input');
              inputToken.type = 'hidden';
              inputToken.name = '_token';
              inputToken.value = token;
              form.appendChild(inputToken);

              const inputMethod = document.createElement('input');
              inputMethod.type = 'hidden';
              inputMethod.name = '_method';
              inputMethod.value = 'DELETE';
              form.appendChild(inputMethod);

              const inputTipe = document.createElement('input');
              inputTipe.type = 'hidden';
              inputTipe.name = 'tipe_hapus';
              inputTipe.value = 'salah_input';
              form.appendChild(inputTipe);

              document.body.appendChild(form);
              form.submit();
            },
            onCancel: function() { AppModal.close(); }
          });

          window.selectDeleteOpt = function(val) {
            const mutasiCard = document.getElementById('opt-card-mutasi');
            const salahCard = document.getElementById('opt-card-salah');
            const btn = document.getElementById('btn-show-delete-confirm');
            
            const radioMutasi = document.querySelector('input[value="mutasi_berkurang"]');
            const radioSalah = document.querySelector('input[value="salah_input"]');

            if (val === 'mutasi_berkurang') {
              if (radioMutasi) radioMutasi.checked = true;
              if (mutasiCard) mutasiCard.classList.add('active');
              if (salahCard) salahCard.classList.remove('active');
              if (btn) {
                btn.className = 'modal-btn modal-btn-primary';
                btn.textContent = 'Lanjutkan';
              }
            } else {
              if (radioSalah) radioSalah.checked = true;
              if (mutasiCard) mutasiCard.classList.remove('active');
              if (salahCard) salahCard.classList.add('active');
              if (btn) {
                btn.className = 'modal-btn modal-btn-danger';
                btn.textContent = 'Hapus';
              }
            }
          };
        }

        let currentLabelFormat = 'sticker'; // 'sticker' | 'plain'
        
        function openQrLabelModal(defaultAction) {
          const modal = document.getElementById('qrLabelModal');
          if (modal) {
            modal.classList.add('active');
            if (typeof feather !== 'undefined') feather.replace();
          }
        }

        function closeQrLabelModal() {
          const modal = document.getElementById('qrLabelModal');
          if (modal) modal.classList.remove('active');
        }

        function selectLabelFormat(fmt) {
          currentLabelFormat = fmt;
          const btnSticker = document.getElementById('opt-btn-sticker');
          const btnPlain   = document.getElementById('opt-btn-plain');
          const prevSticker = document.getElementById('preview-sticker-container');
          const prevPlain   = document.getElementById('preview-plain-container');

          if (fmt === 'sticker') {
            if (btnSticker) btnSticker.classList.add('active');
            if (btnPlain)   btnPlain.classList.remove('active');
            if (prevSticker) prevSticker.style.display = 'block';
            if (prevPlain)   prevPlain.style.display = 'none';
          } else {
            if (btnSticker) btnSticker.classList.remove('active');
            if (btnPlain)   btnPlain.classList.add('active');
            if (prevSticker) prevSticker.style.display = 'none';
            if (prevPlain)   prevPlain.style.display = 'flex';
          }
        }

        function renderLabelCanvas(fmt, callback) {
          const barcode = '{{ $inventaris->barcode ?? "aset" }}';
          const kodeBrg = '{{ $inventaris->kode_barang ?? "-" }}';
          const qrSrc   = '{{ $qrUrl }}';
          const logoSrc = '{{ asset("assets/images/logo/logo-sima-desa-demo.png") }}';
          const nupVal  = '{{ $inventaris->nup ?? "-" }}';
          const thnVal  = '{{ $inventaris->tahun_perolehan ?? "-" }}';
          const nupThn  = 'NUP : ' + nupVal + ' / ' + thnVal;

          if (fmt === 'plain') {
            const canvas = document.createElement('canvas');
            canvas.width = 400;
            canvas.height = 400;
            const ctx = canvas.getContext('2d');
            ctx.fillStyle = '#ffffff';
            ctx.fillRect(0, 0, canvas.width, canvas.height);

            const qrImg = new Image();
            qrImg.crossOrigin = 'anonymous';
            qrImg.onload = function() {
              ctx.drawImage(qrImg, 20, 20, 360, 360);
              callback(canvas);
            };
            qrImg.onerror = function() {
              const domQr = document.getElementById('plain-qr-img') || document.getElementById('sticker-qr-img');
              if (domQr && domQr.complete && domQr.naturalWidth > 0) {
                try { ctx.drawImage(domQr, 20, 20, 360, 360); } catch(e){}
              }
              callback(canvas);
            };
            qrImg.src = qrSrc;
          } else {
            // 3-Section Sticker Canvas (Logo Square - Center Codes - QR Square)
            const canvas = document.createElement('canvas');
            canvas.width = 920;
            canvas.height = 320;
            const ctx = canvas.getContext('2d');
            ctx.fillStyle = '#ffffff';
            ctx.fillRect(0, 0, canvas.width, canvas.height);

            // Red Header
            ctx.fillStyle = '#c81e1e';
            ctx.fillRect(0, 0, 920, 64);

            const instansiNama = @json(strtoupper(config('desa.upb') ?: ('PEMERINTAH DESA ' . (config('desa.desa_nama') ?: (config('desa.nama_lokasi') ?: 'DEMO')))));
            const kodeLokasiDesa = @json(config('desa.kode_lokasi') ?: '51.03.02.2009');

            ctx.fillStyle = '#ffffff';
            ctx.font = 'bold 28px Inter, sans-serif';
            ctx.textAlign = 'center';
            ctx.fillText(instansiNama, 460, 44);

            // Outer border
            ctx.strokeStyle = '#000000';
            ctx.lineWidth = 6;
            ctx.strokeRect(3, 3, 914, 314);
            ctx.beginPath();
            ctx.moveTo(0, 64);
            ctx.lineTo(920, 64);
            ctx.stroke();

            // Left Box divider (x = 230)
            ctx.beginPath();
            ctx.moveTo(230, 64);
            ctx.lineTo(230, 320);
            ctx.stroke();

            // Right Box divider (x = 690)
            ctx.beginPath();
            ctx.moveTo(690, 64);
            ctx.lineTo(690, 320);
            ctx.stroke();

            // Center Box Horizontal divider (y = 170)
            ctx.beginPath();
            ctx.moveTo(230, 170);
            ctx.lineTo(690, 170);
            ctx.stroke();

            // Center Column Texts (kode_lokasi & kode_barang & NUP / Tahun)
            ctx.textAlign = 'center';
            ctx.fillStyle = '#000000';
            ctx.font = '800 34px monospace, sans-serif';
            ctx.fillText(kodeLokasiDesa, 460, 132);

            ctx.font = '800 30px monospace, sans-serif';
            ctx.fillText(kodeBrg, 460, 224);
            ctx.fillText(nupThn, 460, 278);

            // Load images
            let loaded = 0;
            const logoImg = new Image();
            logoImg.crossOrigin = 'anonymous';
            const qrImg = new Image();
            qrImg.crossOrigin = 'anonymous';

            const onBothReady = function() {
              try { ctx.drawImage(logoImg, 45, 110, 140, 140); } catch(e){}
              try { ctx.drawImage(qrImg, 720, 95, 170, 170); } catch(e){}
              callback(canvas);
            };

            const markLoaded = function() {
              loaded++;
              if (loaded >= 2) onBothReady();
            };

            logoImg.onload = markLoaded;
            logoImg.onerror = function() {
              const domLogo = document.getElementById('sticker-logo-img');
              if (domLogo && domLogo.complete && domLogo.naturalWidth > 0) {
                try { ctx.drawImage(domLogo, 45, 110, 140, 140); } catch(e){}
              }
              markLoaded();
            };

            qrImg.onload = markLoaded;
            qrImg.onerror = function() {
              const domQr = document.getElementById('sticker-qr-img') || document.getElementById('plain-qr-img');
              if (domQr && domQr.complete && domQr.naturalWidth > 0) {
                try { ctx.drawImage(domQr, 720, 95, 170, 170); } catch(e){}
              }
              markLoaded();
            };

            logoImg.src = logoSrc;
            qrImg.src   = qrSrc;
          }
        }

        function printSelectedLabel() {
          const barcode = '{{ $inventaris->barcode ?? "" }}';
          renderLabelCanvas(currentLabelFormat, function(canvas) {
            const dataUrl = canvas.toDataURL('image/png');
            const win = window.open('', '_blank', 'width=640,height=520');
            if (!win) {
              AppModal.error('Gagal Membuka Jendela Cetak', 'Popup window diblokir oleh browser. Silakan izinkan pop-up pada browser Anda.');
              return;
            }
            win.document.write('<!DOCTYPE html><html><head><title>Print Label Aset - ' + barcode + '</title>');
            win.document.write('<style>');
            win.document.write('*,*::before,*::after{box-sizing:border-box;margin:0;padding:0;}');
            win.document.write('body{font-family:"Inter",system-ui,-apple-system,sans-serif;background:#fff;display:flex;align-items:center;justify-content:center;min-height:100vh;padding:20px;}');
            win.document.write('@media print{@page{margin:5mm;size:auto;}body{padding:0;min-height:auto;}}');
            win.document.write('.print-img-wrap{max-width:520px;width:100%;text-align:center;margin:0 auto;}');
            win.document.write('.print-img{max-width:100%;height:auto;display:block;margin:0 auto;}');
            win.document.write('</style></head><body>');
            win.document.write('<div class="print-img-wrap"><img src="' + dataUrl + '" class="print-img" alt="Label Aset" onload="setTimeout(function(){ window.focus(); window.print(); }, 150);"></div>');
            win.document.write('</body></html>');
            win.document.close();
          });
        }

        function downloadSelectedLabel() {
          const barcode = '{{ $inventaris->barcode ?? "aset" }}';
          const filename = (currentLabelFormat === 'plain' ? 'QR-' : 'Stiker-Resmi-') + barcode + '.png';
          renderLabelCanvas(currentLabelFormat, function(canvas) {
            triggerDownload(canvas, filename);
          });
        }

        function triggerDownload(canvas, filename) {
          const a = document.createElement('a');
          a.download = filename;
          a.href = canvas.toDataURL('image/png');
          document.body.appendChild(a);
          a.click();
          document.body.removeChild(a);
        }

        // Data foto aset untuk Penampil Layar Penuh (Google Photos/Drive style)
        const simaFotos = @json($fotoUrls ?? []);
        let currentSimaFotoIdx = 0;

        function switchPortalFoto(src, idx, thumbEl) {
          currentSimaFotoIdx = typeof idx === 'number' ? idx : 0;
          const mainImg = document.getElementById('portal-main-foto-img');
          if (mainImg) mainImg.src = src;
          document.querySelectorAll('.portal-gallery-thumb').forEach(t => t.classList.remove('active'));
          if (thumbEl) thumbEl.classList.add('active');
        }

        function openSimaFotoViewer(idx) {
          if (!simaFotos || simaFotos.length === 0) return;
          if (typeof idx === 'number') {
            currentSimaFotoIdx = idx;
          }
          if (typeof window.openSimaImageViewer === 'function') {
            window.openSimaImageViewer(simaFotos, currentSimaFotoIdx, '{{ addslashes($inventaris->display_name ?? 'Foto Aset') }}', function(src, i) {
              const mainImg = document.getElementById('portal-main-foto-img');
              if (mainImg) mainImg.src = src;
              document.querySelectorAll('.portal-gallery-thumb').forEach((t, index) => {
                if (index === i) t.classList.add('active');
                else t.classList.remove('active');
              });
              currentSimaFotoIdx = i;
            });
          }
        }

      // Initialize Feather Icons
      if (typeof feather !== 'undefined') {
        feather.replace();
      }
    </script>

    {{-- MODAL CETAK & UNDUH LABEL QR CODE --}}
    @if(!empty($qrUrl))
    <div class="qr-modal-overlay" id="qrLabelModal" onclick="if(event.target===this) closeQrLabelModal()">
      <div class="qr-modal-card">
        <div class="qr-modal-header">
          <div>
            <h5 class="qr-modal-title">Cetak & Unduh Label QR Code</h5>
          </div>
          <button type="button" class="qr-modal-close" onclick="closeQrLabelModal()" aria-label="Tutup">
            <i data-feather="x"></i>
          </button>
        </div>

        <div class="qr-modal-body">
          <!-- Format Selector -->
          <div style="font-size:11px;font-weight:700;color:var(--color-text-muted, #546958);text-transform:uppercase;letter-spacing:.5px;">
            PILIH FORMAT LABEL
          </div>
          <div class="format-options-grid">
            <!-- Option 1: Stiker Resmi Desa -->
            <button type="button" class="format-option-btn active" id="opt-btn-sticker" onclick="selectLabelFormat('sticker')">
              <div class="format-opt-title">
                Label Stiker Resmi
              </div>
              <div class="format-opt-desc">
                Format stiker fisik resmi (Header instansi, Logo, Kode Lokasi, Kode Barang, NUP/Tahun, & QR Code)
              </div>
            </button>

            <!-- Option 2: Plain QR -->
            <button type="button" class="format-option-btn" id="opt-btn-plain" onclick="selectLabelFormat('plain')">
              <div class="format-opt-title">
                QR Code & Barcode Saja
              </div>
              <div class="format-opt-desc">
                Hanya gambar QR Code tanpa frame stiker resmi
              </div>
            </button>
          </div>

          <!-- Live Preview Area -->
          <div>
            <div class="preview-heading">LIVE PREVIEW LABEL</div>
            <div class="preview-wrapper" style="display:flex; flex-direction:column; align-items:center; justify-content:center; text-align:center;">
              <!-- Preview Template 1: Sticker Official (Logo - Codes - QR) -->
              <div id="preview-sticker-container" class="sticker-preview-box" style="margin:0 auto;">
                <div class="sticker-preview-header">
                  {{ strtoupper(config('desa.upb') ?: ('PEMERINTAH DESA ' . (config('desa.desa_nama') ?: (config('desa.nama_lokasi') ?: 'DEMO')))) }}
                </div>
                <div class="sticker-preview-body">
                  <!-- Section Kiri: Logo Desa -->
                  <div class="sticker-preview-box-square left">
                    <img src="{{ asset('assets/images/logo/logo-sima-desa-demo.png') }}" class="sticker-preview-logo" alt="Logo Desa" id="sticker-logo-img">
                  </div>
                  <!-- Section Tengah: Kode Lokasi & Kode Barang + NUP/Tahun -->
                  <div class="sticker-preview-box-center">
                    <div class="sticker-preview-cell top">
                      <span class="sticker-cell-value">{{ config('desa.kode_lokasi') ?: '51.03.02.2009' }}</span>
                    </div>
                    <div class="sticker-preview-cell">
                      <span class="sticker-cell-value">{{ $inventaris->kode_barang ?? '-' }}</span>
                      <span class="sticker-cell-value">NUP : {{ $inventaris->nup ?? '-' }} / {{ $inventaris->tahun_perolehan ?? '-' }}</span>
                    </div>
                  </div>
                  <!-- Section Kanan: QR Code (Ukuran sama persis dengan Kotak Logo Kiri) -->
                  <div class="sticker-preview-box-square right">
                    <img src="{{ $qrUrl }}" class="sticker-preview-qr" alt="QR Code" id="sticker-qr-img">
                  </div>
                </div>
              </div>

              <!-- Preview Template 2: Plain QR -->
              <div id="preview-plain-container" class="plain-preview-box" style="display:none; margin:0 auto !important;">
                <img src="{{ $qrUrl }}" alt="QR Code" id="plain-qr-img" style="margin:0 auto !important;">
              </div>
            </div>
          </div>
        </div>

        <div class="qr-modal-footer">
          <button type="button" class="btn-sima-secondary" onclick="downloadSelectedLabel()" style="height: 38px; padding: 0 18px; font-size: 13px;">
            <i data-feather="download" style="width:14px;height:14px;"></i> <span>Unduh Gambar (PNG)</span>
          </button>
          <button type="button" class="btn-sima-primary" onclick="printSelectedLabel()" style="height: 38px; padding: 0 20px; font-size: 13px;">
            <i data-feather="printer" style="width:14px;height:14px;"></i> <span>Cetak (Print)</span>
          </button>
        </div>
      </div>
    </div>
    @endif
    <script>
      try { sessionStorage.setItem('manajemen_aset_returning', '1'); } catch(e) {}
    </script>
  </body>
</html>
