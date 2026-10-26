<!doctype html>
<html lang="id">
<head>
  @include('layouts.head-page-meta', ['title' => 'Design System Preview - SIMA Demo'])
  @include('layouts.head-css')
  <script src="https://cdn.jsdelivr.net/npm/feather-icons/dist/feather.min.js"></script>
  <style>
    .preview-section {
      margin-bottom: var(--space-8);
      padding-bottom: var(--space-8);
      border-bottom: 1px dashed var(--color-border);
    }
    .preview-section:last-child {
      border-bottom: none;
      margin-bottom: 0;
      padding-bottom: 0;
    }
    .preview-title {
      font-size: 18px;
      font-weight: 600;
      color: var(--color-primary);
      margin-bottom: var(--space-4);
      display: flex;
      align-items: center;
      gap: 8px;
    }
    .swatch-box {
      border-radius: var(--radius-sm);
      height: 48px;
      border: 1px solid var(--color-border);
      display: flex;
      align-items: center;
      justify-content: center;
      font-size: 11px;
      font-weight: 500;
    }
    .component-row {
      display: flex;
      flex-wrap: wrap;
      align-items: center;
      gap: var(--space-4);
      margin-bottom: var(--space-4);
    }
  </style>
</head>
<body style="padding: 40px 24px; max-width: 1200px; margin: 0 auto;">

  <!-- Header Preview -->
  <div style="margin-bottom: var(--space-8); display: flex; justify-content: space-between; align-items: flex-start; flex-wrap: wrap; gap: 16px;">
    <div>
      <div style="font-size: 12px; font-weight: 600; color: var(--color-primary); text-transform: uppercase; letter-spacing: 0.05em; margin-bottom: 4px;">
        Fase 1: Token & Komponen Dasar
      </div>
      <h1 style="font-size: 28px; margin: 0; font-weight: 600;">Katalog Desain Baru (SIMA Warm Minimalist)</h1>
      <p style="color: var(--color-text-muted); margin-top: 4px; font-size: 14px;">
        Diadaptasi dari pola <em>Prodex Table</em>, <em>Analytics Dashboard</em>, dan palet <em>Custora</em> (No AI Slop).
      </p>
    </div>
    <div style="display: flex; gap: 8px;">
      <a href="/dashboard" class="btn-sima-secondary">
        <i data-feather="arrow-left" style="width: 16px; height: 16px;"></i>
        <span>Kembali ke Dashboard</span>
      </a>
      <button class="btn-sima-primary" onclick="alert('Desain Token Aktif!')">
        <i data-feather="check" style="width: 16px; height: 16px;"></i>
        <span>Status: Siap Direview</span>
      </button>
    </div>
  </div>

  <div class="sima-container">

    <!-- 1. COLOR TOKENS -->
    <div class="preview-section">
      <div class="preview-title">
        <i data-feather="droplet" style="width: 18px; height: 18px;"></i>
        <span>1. Palet Warna (Wajib Token)</span>
      </div>
      <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(130px, 1fr)); gap: 12px;">
        <div>
          <div class="swatch-box" style="background: var(--color-primary); color: var(--color-text-on-primary);">#1f3a2a</div>
          <div style="font-size: 12px; font-weight: 500; margin-top: 4px;">Primary</div>
        </div>
        <div>
          <div class="swatch-box" style="background: var(--color-primary-soft); color: var(--color-text-on-primary);">#2e523b</div>
          <div style="font-size: 12px; font-weight: 500; margin-top: 4px;">Primary Soft</div>
        </div>
        <div>
          <div class="swatch-box" style="background: var(--color-primary-tint); color: var(--color-primary);">#d5e4d7</div>
          <div style="font-size: 12px; font-weight: 500; margin-top: 4px;">Primary Tint</div>
        </div>
        <div>
          <div class="swatch-box" style="background: var(--color-bg); color: var(--color-text);">#eaf1e7</div>
          <div style="font-size: 12px; font-weight: 500; margin-top: 4px;">Background</div>
        </div>
        <div>
          <div class="swatch-box" style="background: var(--color-surface); color: var(--color-text);">#f8faf7</div>
          <div style="font-size: 12px; font-weight: 500; margin-top: 4px;">Surface</div>
        </div>
        <div>
          <div class="swatch-box" style="background: var(--color-border); color: var(--color-text);">#cfdcc8</div>
          <div style="font-size: 12px; font-weight: 500; margin-top: 4px;">Border</div>
        </div>
        <div>
          <div class="swatch-box" style="background: var(--color-accent); color: #fff;">#C9A24A</div>
          <div style="font-size: 12px; font-weight: 500; margin-top: 4px;">Aksen Gold</div>
        </div>
      </div>
    </div>

    <!-- 2. BUTTONS -->
    <div class="preview-section">
      <div class="preview-title">
        <i data-feather="square" style="width: 18px; height: 18px;"></i>
        <span>2. Tombol (Pill Primary, Secondary Border, Icon Only)</span>
      </div>
      <div class="component-row">
        <button class="btn-sima-primary">
          <i data-feather="plus" style="width: 16px; height: 16px;"></i>
          <span>Tambah inventaris</span>
        </button>
        <button class="btn-sima-secondary">
          <i data-feather="download" style="width: 16px; height: 16px;"></i>
          <span>Ekspor berkas</span>
        </button>
        <button class="btn-sima-danger">
          <i data-feather="trash-2" style="width: 16px; height: 16px;"></i>
          <span>Hapus aset</span>
        </button>
        <button class="btn-sima-icon" title="Edit Data">
          <i data-feather="edit-2" style="width: 16px; height: 16px;"></i>
        </button>
        <button class="btn-sima-icon" title="Lainnya">
          <i data-feather="more-horizontal" style="width: 16px; height: 16px;"></i>
        </button>
        <button class="btn-sima-primary" disabled>
          <span>Nonaktif (Disabled)</span>
        </button>
      </div>
    </div>

    <!-- 3. STAT CARDS (Prodex Style) -->
    <div class="preview-section">
      <div class="preview-title">
        <i data-feather="activity" style="width: 18px; height: 18px;"></i>
        <span>3. Stat Cards (Label + Angka + Indikator Titik Warna)</span>
      </div>
      <div class="stat-card-row">
        <div class="stat-card-sima">
          <div class="stat-header">
            <span>Total Inventaris</span>
          </div>
          <div class="stat-number">1.240</div>
          <div class="stat-delta up">
            <i data-feather="trending-up" style="width: 14px; height: 14px;"></i>
            <span>+12 bulan ini</span>
          </div>
        </div>
        <div class="stat-card-sima">
          <div class="stat-header">
            <span>Kondisi Baik</span>
          </div>
          <div class="stat-number">1.180</div>
          <div class="stat-delta up">
            <span>95.1% terawat</span>
          </div>
        </div>
        <div class="stat-card-sima">
          <div class="stat-header">
            <span>Rusak Ringan</span>
          </div>
          <div class="stat-number">45</div>
          <div class="stat-delta">
            <span>Perlu servis</span>
          </div>
        </div>
        <div class="stat-card-sima">
          <div class="stat-header">
            <span>Rusak Berat</span>
          </div>
          <div class="stat-number">15</div>
          <div class="stat-delta down">
            <span>Siap dihapus</span>
          </div>
        </div>
      </div>
    </div>

    <!-- 4. STATUS PILLS & BADGES -->
    <div class="preview-section">
      <div class="preview-title">
        <i data-feather="tag" style="width: 18px; height: 18px;"></i>
        <span>4. Status Pill (Soft Background + Solid Text)</span>
      </div>
      <div class="component-row">
        <span class="status-pill status-pill-success">Baik</span>
        <span class="status-pill status-pill-warning">Rusak Ringan</span>
        <span class="status-pill status-pill-danger">Rusak Berat</span>
        <span class="status-pill status-pill-info">Mutasi</span>
        <span class="status-pill status-pill-neutral">Draft</span>
      </div>
    </div>

    <!-- 5. TABS & FILTER BAR -->
    <div class="preview-section">
      <div class="preview-title">
        <i data-feather="layers" style="width: 18px; height: 18px;"></i>
        <span>5. Status Tabs (Underline Active) & Filter Form</span>
      </div>
      
      <!-- Tabs -->
      <div class="tabs-sima">
        <a class="tab-item-sima active">
          <span>Semua inventaris</span>
          <span class="tab-badge-sima">1.240</span>
        </a>
        <a class="tab-item-sima">
          <span>Kondisi baik</span>
          <span class="tab-badge-sima">1.180</span>
        </a>
        <a class="tab-item-sima">
          <span>Rusak ringan</span>
          <span class="tab-badge-sima">45</span>
        </a>
        <a class="tab-item-sima">
          <span>Rusak berat</span>
          <span class="tab-badge-sima">15</span>
        </a>
      </div>

      <!-- Controls Row -->
      <div style="display: flex; justify-content: space-between; align-items: center; gap: 16px; margin-bottom: 16px; flex-wrap: wrap;">
        <div style="display: flex; gap: 12px; align-items: center; flex: 1; max-width: 480px;">
          <input type="text" class="form-control-sima" placeholder="Cari nama barang, kode, atau NUP... (Tekan ⌘K)">
        </div>
        <div style="display: flex; gap: 8px;">
          <select class="form-select-sima" style="width: auto;">
            <option>Semua Ruangan</option>
            <option>Ruang Sekretariat</option>
            <option>Ruang Rapat Utama</option>
          </select>
          <button class="btn-sima-secondary">
            <i data-feather="filter" style="width: 14px; height: 14px;"></i>
            <span>Filter</span>
          </button>
        </div>
      </div>

      <!-- 6. DATA TABLE (Prodex Pattern) -->
      <div class="table-sima-wrapper">
        <table class="table-sima">
          <thead>
            <tr>
              <th style="width: 36px;"><input type="checkbox" class="sima-checkbox"></th>
              <th>Nama Barang & Kode</th>
              <th>Kategori & Ruangan</th>
              <th>Tahun</th>
              <th class="num-col">Nilai Perolehan</th>
              <th>Kondisi</th>
              <th style="text-align: right;">Aksi</th>
            </tr>
          </thead>
          <tbody>
            <tr>
              <td><input type="checkbox" class="sima-checkbox"></td>
              <td>
                <div style="font-weight: 500; color: var(--color-text);">Laptop Lenovo ThinkPad L14</div>
                <div style="font-size: 12px; color: var(--color-text-muted);">02.06.01.02.001 • NUP 0001</div>
              </td>
              <td>
                <div>Peralatan & Mesin</div>
                <div style="font-size: 12px; color: var(--color-text-muted);">Ruang Sekretariat</div>
              </td>
              <td>2024</td>
              <td class="num-col" style="font-weight: 500;">Rp 14.500.000</td>
              <td><span class="status-pill status-pill-success">Baik</span></td>
              <td style="text-align: right;">
                <button class="btn-sima-icon"><i data-feather="edit-2" style="width: 14px; height: 14px;"></i></button>
                <button class="btn-sima-icon"><i data-feather="trash-2" style="width: 14px; height: 14px;"></i></button>
              </td>
            </tr>
            <tr class="selected">
              <td><input type="checkbox" class="sima-checkbox" checked></td>
              <td>
                <div style="font-weight: 500; color: var(--color-text);">Proyektor Epson EB-X500</div>
                <div style="font-size: 12px; color: var(--color-text-muted);">02.06.01.05.002 • NUP 0003</div>
              </td>
              <td>
                <div>Peralatan & Mesin</div>
                <div style="font-size: 12px; color: var(--color-text-muted);">Ruang Rapat Utama</div>
              </td>
              <td>2023</td>
              <td class="num-col" style="font-weight: 500;">Rp 6.800.000</td>
              <td><span class="status-pill status-pill-warning">Rusak Ringan</span></td>
              <td style="text-align: right;">
                <button class="btn-sima-icon"><i data-feather="edit-2" style="width: 14px; height: 14px;"></i></button>
                <button class="btn-sima-icon"><i data-feather="trash-2" style="width: 14px; height: 14px;"></i></button>
              </td>
            </tr>
          </tbody>
        </table>
      </div>

      <!-- Pagination -->
      <div class="pagination-sima-container">
        <div>Menampilkan 1–2 dari 1.240 data</div>
        <div class="pagination-sima">
          <button class="page-btn-sima"><i data-feather="chevron-left" style="width: 14px; height: 14px;"></i></button>
          <button class="page-btn-sima active">1</button>
          <button class="page-btn-sima">2</button>
          <button class="page-btn-sima">3</button>
          <button class="page-btn-sima"><i data-feather="chevron-right" style="width: 14px; height: 14px;"></i></button>
        </div>
      </div>
    </div>

    <!-- 7. FORM ELEMENTS -->
    <div class="preview-section">
      <div class="preview-title">
        <i data-feather="edit-3" style="width: 18px; height: 18px;"></i>
        <span>6. Form Controls & Validasi</span>
      </div>
      <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(280px, 1fr)); gap: 16px;">
        <div>
          <label class="form-label-sima">Nama barang inventaris</label>
          <input type="text" class="form-control-sima" value="Meja Kerja Kayu Jati">
          <div class="form-helper-sima">Sebutkan merk dan tipe bila ada.</div>
        </div>
        <div>
          <label class="form-label-sima">Kondisi barang</label>
          <select class="form-select-sima">
            <option>Baik</option>
            <option>Rusak Ringan</option>
            <option>Rusak Berat</option>
          </select>
        </div>
        <div>
          <label class="form-label-sima">Kode barang (Invalid State)</label>
          <input type="text" class="form-control-sima is-invalid" value="02.XX.INVALID">
          <div class="form-error-sima">Format kode barang tidak valid sesuai Permendagri.</div>
        </div>
      </div>
    </div>

    <!-- 8. EMPTY STATE & SKELETON -->
    <div class="preview-section">
      <div class="preview-title">
        <i data-feather="inbox" style="width: 18px; height: 18px;"></i>
        <span>7. State Kosong (Empty State) & Skeleton</span>
      </div>
      <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(300px, 1fr)); gap: 24px;">
        <div class="sima-card" style="display: flex; align-items: center; justify-content: center;">
          <div class="empty-state-sima">
            <i data-feather="inbox" style="width: 42px; height: 42px; stroke-width: 1.5; color: var(--color-border-dark);"></i>
            <p style="margin: 0; font-size: 13.5px; font-weight: 500;">Belum ada data barang yang sesuai</p>
          </div>
        </div>
        <div class="sima-card">
          <div style="font-weight: 500; font-size: 14px; margin-bottom: 16px;">Contoh Skeleton Shimmer</div>
          <div class="skeleton-sima" style="height: 20px; width: 60%; margin-bottom: 12px;"></div>
          <div class="skeleton-sima" style="height: 14px; width: 90%; margin-bottom: 8px;"></div>
          <div class="skeleton-sima" style="height: 14px; width: 75%; margin-bottom: 16px;"></div>
          <div class="skeleton-sima" style="height: 36px; width: 120px; border-radius: 9999px;"></div>
        </div>
      </div>
    </div>

  </div>

  <!-- FLOATING BULK ACTION BAR (PREVIEW) -->
  <div class="bulk-action-bar-sima show">
    <span class="bulk-count">1 Barang Terpilih</span>
    <div class="bulk-divider"></div>
    <button class="btn-sima-secondary" style="border: none; padding: 4px 8px;" onclick="alert('Duplikat!')">
      <i data-feather="copy" style="width: 14px; height: 14px;"></i>
      <span>Duplikat</span>
    </button>
    <button class="btn-sima-secondary" style="border: none; padding: 4px 8px;" onclick="alert('Cetak!')">
      <i data-feather="printer" style="width: 14px; height: 14px;"></i>
      <span>Cetak Label</span>
    </button>
    <button class="btn-sima-danger" style="padding: 4px 12px;" onclick="alert('Hapus!')">
      <i data-feather="trash-2" style="width: 14px; height: 14px;"></i>
      <span>Hapus</span>
    </button>
    <button class="btn-sima-icon" style="width: 28px; height: 28px;" title="Tutup">
      <i data-feather="x" style="width: 14px; height: 14px;"></i>
    </button>
  </div>

  <script>
    feather.replace();
  </script>
</body>
</html>
