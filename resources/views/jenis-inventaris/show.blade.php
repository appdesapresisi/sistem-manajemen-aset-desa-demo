<!doctype html>
<html lang="en" data-pc-preset="{{ $pc_preset_theme }}" data-pc-theme="{{ $pc_dark_layout }}">
  <head>
    @include('layouts.head-page-meta', ['title' => 'Detail Jenis Inventaris — ' . ($jenis->nama ?? 'Jenis Inventaris')])
    @include('layouts.head-css')
    <style>
      /* SIMA Portal Specific Enhancements */
      .pejabat-grid {
        display: grid;
        grid-template-columns: repeat(auto-fill, minmax(280px, 1fr));
        gap: 12px;
      }
      .pejabat-card-item {
        background: var(--color-surface, #f8faf7);
        border: 1px solid var(--color-border, #cfdcc8);
        border-radius: var(--radius-sm, 10px);
        padding: 12px 16px;
        display: flex;
        align-items: center;
        gap: 12px;
        transition: background-color 0.15s ease;
      }
      .pejabat-card-item:hover {
        background: var(--color-row-hover, #eff5ed);
      }
      .pejabat-order-num {
        width: 26px;
        height: 26px;
        border-radius: 6px;
        background: var(--color-primary-tint, #d5e4d7);
        color: var(--color-primary, #1f3a2a);
        font-size: 12px;
        font-weight: 700;
        font-variant-numeric: tabular-nums;
        display: flex;
        align-items: center;
        justify-content: center;
        flex-shrink: 0;
      }
      .pejabat-item-details {
        flex: 1;
        min-width: 0;
      }
      .pejabat-item-name {
        font-size: 13.5px;
        font-weight: 600;
        color: var(--color-text, #16251b);
        white-space: nowrap;
        overflow: hidden;
        text-overflow: ellipsis;
      }
      .pejabat-item-sub {
        font-size: 12px;
        color: var(--color-text-muted, #546958);
        margin-top: 2px;
      }
    </style>
  </head>

  <body data-pc-preset="{{ $pc_preset_theme }}" data-pc-sidebar-theme="{{ $pc_sidebar_theme ?? 'light' }}">
    @include('layouts.notification')
    @include('layouts.modals')
    @include('layouts.layout-vertical')

    <div class="pc-container">
      <div class="pc-content">
        @include('layouts.breadcrumb', [
          'breadcrumbItemHome' => 'Master Data',
          'breadcrumbItem' => 'Jenis Inventaris',
          'breadcrumbItemUrl' => route('jenis-inventaris.index'),
          'breadcrumbItemActive' => 'Detail Jenis Inventaris'
        ])

        <!-- Top Action Bar -->
        <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 20px; flex-wrap: wrap; gap: 12px;">
          <a href="{{ route('jenis-inventaris.index') }}" class="btn-sima-secondary" style="border: none !important; padding-left: 0 !important; background-color: transparent !important;" onclick="try { sessionStorage.setItem('jenis_inventaris_returning', '1'); } catch(e) {} if (window.history.length > 1 && document.referrer && document.referrer.includes('/jenis-inventaris')) { window.history.back(); return false; } return true;">
            <i data-feather="arrow-left" style="width: 14px; height: 14px;"></i>
            <span>Kembali ke Daftar Jenis Inventaris</span>
          </a>
          @canany(['update', 'delete'], $jenis)
          <div style="display: flex; gap: 10px; align-items: center;">
            @can('update', $jenis)
            <a href="{{ route('jenis-inventaris.edit', $jenis->id) }}" class="btn-sima-primary">
              <i data-feather="edit-2" style="width: 14px; height: 14px;"></i>
              <span>Edit Jenis</span>
            </a>
            @endcan
            @can('delete', $jenis)
            @if(!($isVirtual ?? false))
            <button type="button" onclick="confirmDelete()" class="btn-sima-danger">
              <i data-feather="trash-2" style="width: 14px; height: 14px;"></i>
              <span>Hapus Jenis</span>
            </button>
            @endif
            @endcan
          </div>
          @endcanany
        </div>

        <!-- ===== MAIN PORTAL CARD ===== -->
        <div class="portal-card">

          <!-- Section: Informasi & Spesifikasi Jenis Inventaris -->
          <div class="portal-section" style="padding-top: 0; border-top: none; margin-top: 32px;">
            <div class="portal-section-header">
              <h2 class="portal-section-title">
                <span>Informasi & Spesifikasi Jenis Inventaris</span>
              </h2>
            </div>

            <div class="portal-stats-grid" style="grid-template-columns: repeat(2, 1fr); gap: 24px 36px;">
              <div class="portal-stat-item">
                <span class="portal-stat-label">Nama Jenis Inventaris</span>
                <span class="portal-stat-value">{{ $jenis->nama ?? '-' }}</span>
              </div>

              <div class="portal-stat-item">
                <span class="portal-stat-label">Format Dokumen KIB</span>
                <span class="portal-stat-value">
                  @if(!($isVirtual ?? false) && $jenis->kib_type && ($kibConfig = config('kib.' . $jenis->kib_type)))
                    KIB {{ $jenis->kib_type }} ({{ $kibConfig['nama'] ?? 'Standar Permendagri' }})
                  @else
                    Tidak Menggunakan KIB
                  @endif
                </span>
              </div>

              <div class="portal-stat-item">
                <span class="portal-stat-label">Konfigurasi Ruangan</span>
                <span class="portal-stat-value">
                  @if($jenis->requires_ruangan)
                    Wajib dialokasikan ke ruangan (KIR)
                  @else
                    Tidak memerlukan ruangan
                  @endif
                </span>
              </div>

              <div class="portal-stat-item">
                <span class="portal-stat-label">Total Aset Terdaftar</span>
                <span class="portal-stat-value">{{ $approvedCount ?? 0 }} aset</span>
              </div>

              <div class="portal-stat-item" style="grid-column: 1 / -1;">
                <span class="portal-stat-label">Deskripsi</span>
                <span class="portal-stat-value {{ !($jenis->deskripsi ?? null) ? 'text-empty-fallback' : '' }}">
                  {{ $jenis->deskripsi ?: 'Tidak ada deskripsi untuk jenis inventaris ini.' }}
                </span>
              </div>
            </div>
          </div>

          <!-- Section: Pejabat yang Bertanda Tangan Default -->
          <div class="portal-section" style="border-top: none; margin-top: 0; padding-top: 36px;">
            <div class="portal-section-header">
              <h2 class="portal-section-title">
                <span>Pejabat Penandatangan Default ({{ $jenis->pejabatDefaults ? $jenis->pejabatDefaults->count() : 0 }})</span>
              
              </h2>
              @if($jenis->pejabatDefaults && $jenis->pejabatDefaults->count() > 0)
              <span style="font-size: 12px; color: var(--color-text-muted); font-weight: 500;">
                Otomatis mengisi lembar cetak KIB/Laporan
              </span>
              @endif
            </div>

            @if($jenis->pejabatDefaults && $jenis->pejabatDefaults->count() > 0)
              <div class="pejabat-grid">
                @foreach(($jenis->pejabatDefaults ? $jenis->pejabatDefaults->sortBy('order') : collect()) as $default)
                <div class="pejabat-card-item">
                  <div class="pejabat-order-num">{{ $default->order }}</div>
                  <div class="pejabat-item-details">
                    <div class="pejabat-item-name">{{ optional($default->pejabat)->nama ?? 'Pejabat tidak ditemukan' }}</div>
                    <div class="pejabat-item-sub">{{ optional($default->pejabat)->jabatan ?? '-' }}</div>
                  </div>
                </div>
                @endforeach
              </div>
            @else
              <div class="empty-state-sima">
                <i data-feather="user-x" style="width: 42px; height: 42px; stroke-width: 1.5; color: var(--color-border-dark);"></i>
                <p>Belum ada pejabat penandatangan default yang ditentukan untuk jenis ini.</p>
              </div>
            @endif
          </div>

          <!-- Section: Struktur Field Data Umum Inventaris -->
          <div class="portal-section" style="border-top: none; margin-top: 0; padding-top: 36px;">
            <div class="portal-section-header">
              <h2 class="portal-section-title">
                <span>Struktur Field Data Umum Inventaris</span>              
              </h2>
            </div>

            @if((($fields ?? collect())->count() === 0 && ($isVirtual ?? false)))
              <div class="empty-state-sima">
                <i data-feather="inbox" style="width: 42px; height: 42px; stroke-width: 1.5; color: var(--color-border-dark);"></i>
                <p>Belum ada field yang dikonfigurasi pada jenis inventaris ini.</p>
              </div>
            @else
              <div style="display: flex; flex-direction: column; margin-top: 6px;">
                @php
                  $counter = 1;
                @endphp
                @foreach(($fields ?? collect())->sortBy('order') as $field)
                <div style="display: flex; align-items: flex-start; font-size: 13.5px; line-height: 1.5; color: var(--color-text); padding: 12px 0; border-bottom: 1px dashed var(--color-border, #cfdcc8);">
                  <span style="color: var(--color-text-muted); font-variant-numeric: tabular-nums; min-width: 24px; font-weight: 500; flex-shrink: 0;">
                    {{ $counter++ }}.
                  </span>
                  <div style="min-width: 0;">
                    <div>
                      <span style="font-weight: 600; color: var(--color-text);">{{ $field->label_field }}</span>
                      <span style="color: var(--color-text-muted); font-size: 13px; font-weight: 500; margin-left: 6px;">(Data umum)</span>
                    </div>
                    @if(isset($field->description) && $field->description)
                    <div style="font-size: 12px; color: var(--color-text-muted); margin-top: 2px;">{{ $field->description }}</div>
                    @endif
                  </div>
                </div>
                @endforeach

                @if(!($isVirtual ?? false))
                <div style="display: flex; align-items: flex-start; font-size: 13.5px; line-height: 1.5; color: var(--color-text); padding: 12px 0; border-bottom: 1px dashed var(--color-border, #cfdcc8);">
                  <span style="color: var(--color-text-muted); font-variant-numeric: tabular-nums; min-width: 24px; font-weight: 500; flex-shrink: 0;">
                    {{ $counter++ }}.
                  </span>
                  <div style="min-width: 0;">
                    <div>
                      <span style="font-weight: 600; color: var(--color-text);">Asal Usul Barang</span>
                      <span style="color: var(--color-text-muted); font-size: 13px; font-weight: 500; margin-left: 6px;">(Data khusus (template sistem))</span>
                    </div>
                    <div style="font-size: 12px; color: var(--color-text-muted); margin-top: 2px;">APBDes / Perolehan Lain yang Sah / Kekayaan Asli Desa</div>
                  </div>
                </div>

                <div style="display: flex; align-items: flex-start; font-size: 13.5px; line-height: 1.5; color: var(--color-text); padding: 12px 0; border-bottom: 1px dashed var(--color-border, #cfdcc8);">
                  <span style="color: var(--color-text-muted); font-variant-numeric: tabular-nums; min-width: 24px; font-weight: 500; flex-shrink: 0;">
                    {{ $counter++ }}.
                  </span>
                  <div style="min-width: 0;">
                    <div>
                      <span style="font-weight: 600; color: var(--color-text);">Pengguna</span>
                      <span style="color: var(--color-text-muted); font-size: 13px; font-weight: 500; margin-left: 6px;">(Data khusus (template sistem))</span>
                    </div>
                    <div style="font-size: 12px; color: var(--color-text-muted); margin-top: 2px;">Nama orang atau bidang yang menggunakan aset ini</div>
                  </div>
                </div>

                <div style="display: flex; align-items: flex-start; font-size: 13.5px; line-height: 1.5; color: var(--color-text); padding: 12px 0; border-bottom: 1px dashed var(--color-border, #cfdcc8);">
                  <span style="color: var(--color-text-muted); font-variant-numeric: tabular-nums; min-width: 24px; font-weight: 500; flex-shrink: 0;">
                    {{ $counter++ }}.
                  </span>
                  <div style="min-width: 0;">
                    <div>
                      <span style="font-weight: 600; color: var(--color-text);">Satuan</span>
                      <span style="color: var(--color-text-muted); font-size: 13px; font-weight: 500; margin-left: 6px;">(Data laporan aset pertahun)</span>
                    </div>
                    <div style="font-size: 12px; color: var(--color-text-muted); margin-top: 2px;">Satuan pengukur barang (misal: Unit, Buah, Set, dll.)</div>
                  </div>
                </div>
                @endif
              </div>
            @endif
          </div>

          <!-- Section: Data KIB (Kartu Inventaris Barang) -->
          @if(!($isVirtual ?? false) && $jenis->kib_type && ($kibConfig = config('kib.' . $jenis->kib_type)))
          <div class="portal-section" style="margin-top: 20px; padding-top: 0;">
            <div class="portal-section-header">
              <h2 class="portal-section-title">
                <span>Data Kartu Inventaris Barang (KIB {{ $jenis->kib_type }} — {{ $kibConfig['nama'] ?? '' }})</span>
              
              </h2>
            </div>
            
            <div class="info-box" style="margin-top: 0; margin-bottom: 16px;">
              <i data-feather="info"></i>
              <div class="info-box-text">
                Atribut di bawah ini adalah kolom standar Permendagri yang dikelola secara otomatis oleh sistem untuk pembuatan format cetak KIB {{ $jenis->kib_type }}.
              </div>
            </div>

            <div style="display: flex; flex-direction: column; margin-top: 6px;">
              @foreach(($kibConfig['fields'] ?? []) as $index => $kibField)
              <div style="display: flex; align-items: center; font-size: 13.5px; line-height: 1.5; color: var(--color-text); padding: 12px 0; border-bottom: 1px dashed var(--color-border, #cfdcc8);">
                <span style="color: var(--color-text-muted); font-variant-numeric: tabular-nums; min-width: 24px; font-weight: 500; flex-shrink: 0;">
                  {{ $index + 1 }}.
                </span>
                <span style="font-weight: 600; color: var(--color-text);">
                  {{ $kibField['label'] }}
                </span>
                <span style="color: var(--color-text-muted); font-size: 13px; font-weight: 500; margin-left: 6px;">
                  (KIB {{ $jenis->kib_type }})
                </span>
              </div>
              @endforeach
            </div>
          </div>
          @endif

          <!-- Section: Data KIR (Kartu Inventaris Ruangan) -->
          @if($jenis->requires_ruangan)
          <div class="portal-section" style="border-top: none; margin-top: 0; padding-top: 36px;">
            <div class="portal-section-header">
              <h2 class="portal-section-title">
                <span>Data Kartu Inventaris Ruangan (KIR)</span>
              </h2>
            </div>

            <div class="info-box" style="margin-top: 0; margin-bottom: 16px;">
              <i data-feather="info"></i>
              <div class="info-box-text">
                Atribut di bawah ini wajib diisi saat input aset jenis ini agar dapat dialokasikan ke ruangan terkait dan dicetak pada lembar KIR.
              </div>
            </div>

            <div style="display: flex; flex-direction: column; margin-top: 6px;">
              @php
                $kirFieldsShow = [
                  'Ruangan',
                  'Konstruksi',
                  'Luas',
                  'Merk / Tipe',
                  'Warna',
                  'Judul',
                  'Pencipta',
                  'No. Polisi'
                ];
              @endphp
              @foreach($kirFieldsShow as $index => $label)
              <div style="display: flex; align-items: center; font-size: 13.5px; line-height: 1.5; color: var(--color-text); padding: 12px 0; border-bottom: 1px dashed var(--color-border, #cfdcc8);">
                <span style="color: var(--color-text-muted); font-variant-numeric: tabular-nums; min-width: 24px; font-weight: 500; flex-shrink: 0;">
                  {{ $index + 1 }}.
                </span>
                <span style="font-weight: 600; color: var(--color-text);">
                  {{ $label }}
                </span>
                <span style="color: var(--color-text-muted); font-size: 13px; font-weight: 500; margin-left: 6px;">
                  (KIR (Ruangan))
                </span>
              </div>
              @endforeach
            </div>
          </div>
          @endif

          <!-- Section: Audit Data -->
          <div class="portal-section" style="border-top: none; margin-top: 0; padding-top: 36px; margin-bottom: 32px;">
            <div class="portal-section-header">
              <h2 class="portal-section-title">
                <span>Informasi Sistem & Audit Data</span>
              </h2>
            </div>
            <div style="display: flex; justify-content: space-evenly; gap: 36px; font-size: 12.5px; flex-wrap: wrap;">
              <div class="portal-kv-item" style="flex: 1; min-width: 180px;">
                <span class="portal-kv-label">ID Jenis</span>
                <span class="portal-kv-value" style="font-family: monospace;">#{{ $jenis->id }}</span>
              </div>
              <div class="portal-kv-item" style="flex: 1; min-width: 180px;">
                <span class="portal-kv-label">Tanggal Dibuat</span>
                <span class="portal-kv-value">{{ $jenis->created_at ? $jenis->created_at->format('d M Y, H:i') : '-' }}</span>
              </div>
              <div class="portal-kv-item" style="flex: 1; min-width: 180px;">
                <span class="portal-kv-label">Terakhir Diperbarui</span>
                <span class="portal-kv-value">{{ $jenis->updated_at ? $jenis->updated_at->format('d M Y, H:i') : '-' }}</span>
              </div>
            </div>
          </div>

        </div><!-- /.portal-card -->

        <!-- Delete Form (hidden) -->
        @can('delete', $jenis)
        @if(!($isVirtual ?? false))
        <form id="deleteForm" action="{{ route('jenis-inventaris.destroy', $jenis->id) }}" method="POST" style="display:none;">
          @csrf
          @method('DELETE')
        </form>
        @endif
        @endcan

      </div>
    </div>

    @include('layouts.footer-block')
    @include('layouts.footer-js')

    <script>
      function confirmDelete() {
        if (!document.getElementById('deleteForm')) return;
        AppModal.confirmDelete('jenis inventaris "{{ $jenis->nama }}" (semua data terkait akan ikut terhapus)', function() {
          document.getElementById('deleteForm').submit();
        });
      }
      try { sessionStorage.setItem('jenis_inventaris_returning', '1'); } catch(e) {}
    </script>
  </body>
</html>
