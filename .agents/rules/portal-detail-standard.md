# Aturan Standar: Kombinasi Tipografi & Spacing Halaman Detail / Show

Aturan ini wajib ditaati dan berlaku untuk **seluruh halaman detail / show (`show.blade.php`) di seluruh sistem**.

---

## 1. Standar Ukuran Teks (Font Size — Mengacu pada Manajemen Aset)
- **Label Field (`.portal-kv-label`)**:
  - `font-size: 11px;`
  - `font-weight: 600;`
  - `text-transform: uppercase;`
  - `letter-spacing: 0.5px;`
  - `color: var(--color-text-muted, #546958);`
- **Nilai Field (`.portal-kv-value`)**:
  - `font-size: 14px;`
  - `font-weight: 600;`
  - `color: var(--color-text, #16251b);`
  - `line-height: 1.4;`
- **Nilai Identitas Utama / Nama Entitas**:
  - `font-size: 15px;` – `16px;`
  - `font-weight: 700;`
  - `color: var(--color-primary, #1f3a2a);`
- **Badge / Status Pill**:
  - `font-size: 12px;`
  - `font-weight: 600;`
  - `padding: 3px 10px;`

---

## 2. Standar Spacing & Tata Letak (Spacing — Mengacu pada Kombinasi Lapang & Panjang)
- **Padding Kartu (`.portal-card`)**:
  - `padding: 44px 50px;` (memberikan ruang napas yang lega dan kokoh di layar desktop/tablet).
- **Tinggi Minimal Kartu (`.portal-card`)**:
  - `min-height: calc(100vh - 220px);` (desktop `min-height: 580px;`, display flex column) agar kartu tampil panjang, megah, dan seluruh konten tidak terasa sesak maupun menyisakan ruang kosong bolong di layar.
- **Grid Gap (`.portal-kv-grid`)**:
  - `gap: 28px 48px;` (gap baris vertikal 28px, gap kolom horizontal 48px).
- **Jarak Vertikal per Item & Divider Baku di Bawah Data (`.portal-kv-item`, `.profile-field-item`)**:
  - `display: flex; flex-direction: column;`
  - `gap: 7px;` (jarak antara label dan value).
  - `padding-bottom: 18px;` (memberikan ruang napas yang elegan sebelum garis divider).
  - **WAJIB menerapkan garis pembatas putus-putus (*dashed divider*) di bawah setiap data**:
    `border-bottom: 1px dashed var(--color-border, #cfdcc8);`
  - Hal ini memberikan struktur pembatas yang jelas antar-baris data namun tetap lembut dan menyatu dengan palet natural SIMA.
- **Pemisahan Antar-Seksi (`.portal-section`)**:
  - `padding-top: 36px;`
  - `margin-top: 36px;`
  - `border-top: none;` (pemisahan antar-blok seksi murni mengandalkan whitespace alami, **DILARANG ada garis divider antar-seksi**).
  - Seksi terakhir otomatis mengikat ke dasar kartu (`margin-top: auto; padding-top: 36px;`).
  - Header seksi (`.portal-section-header`): `margin-bottom: 24px;`.

---

## 3. Standar Ringkasan Data & Tindakan Cepat (Hero Overview Grid)
- **Grid Ringkasan Data (`.portal-stats-grid`)**:
  - `grid-template-columns: repeat(2, 1fr);` (Wajib grid 2-kolom simetris seimbang 50/50).
  - `gap: 24px 36px;` (gap baris vertikal 24px, gap kolom horizontal 36px).
  - Setiap item data ringkasan (`.portal-stat-item`) memiliki `padding-bottom: 18px;` dan `border-bottom: 1px dashed var(--color-border, #cfdcc8);` di bawah setiap data.
  - **DILARANG membuat layout asimetris / acak-acakan** (misal baris 1 berisi 1 item bolong kanan, baris 2 pakai span 2 dengan spasi bolong di tengah, baris 3 berjejer 3 kolom sempit).
  - Susunan field wajib berpasangan logis per baris:
    - **Baris 1**: Identitas Dasar & Status Fisik (contoh: *Nama Aset* | *Kondisi*).
    - **Baris 2**: Keuangan & Sumber Dana (contoh: *Nilai Perolehan* | *Asal Usul*).
    - **Baris 3**: Operasional / Penanggung Jawab (contoh: *Pengguna* | *Satuan*).
- **Seksi Dokumen & Label QR (`.portal-docs-section`)**:
  - `border-top: none;` (**DILARANG ada garis divider putus-putus maupun solid** di atas seksi dokumen/label QR).
  - `padding-top: 28px;` dan `margin-top: auto;` untuk pemisahan berbasis whitespace alami.

---

## 4. Standar Galeri Showcase Foto Aset (`.portal-gallery` & `.portal-gallery-main`)
- **Ketinggian & Garis Batas Bawah Baku Terkunci Simetris**:
  - Bingkai foto utama showcase (`.portal-gallery-main`) selalu memiliki ketinggian dan garis batas bawah yang **100% sejajar rata sempurna dengan ujung bawah kartu Barcode Sistem SIMA Desa** pada kolom kanan.
  - Ukuran bingkai `.portal-gallery-main` **TIDAK PERNAH menyusut** baik saat aset belum memiliki foto (placeholder), memiliki 1 foto, maupun memiliki banyak foto (2+ foto).
  - Saat ada lebih dari 1 foto, deretan thumbnail (`.portal-gallery-thumbs`) ditempatkan sebagai **floating overlay chip** di pojok kiri bawah di dalam bingkai foto utama (`position: absolute; bottom: 12px; left: 12px; z-index: 5;`) dengan backdrop filter glassmorphism. Hal ini menjamin bingkai foto tidak terpotong atau tertekan ke atas, dan garis batas bawahnya selalu sama persis dengan kartu Barcode di kanan.
- **Standar Penskalaan (`object-fit: contain !important;`)**:
  - **DILARANG KERAS** menggunakan `object-fit: cover` pada foto utama karena memotong foto aset.
  - **WAJIB `object-fit: contain !important;`** dengan `padding: 12px;` di dalam `.portal-gallery-main` agar seluruh bagian foto fisik aset (baik persegi, lanskap lebar 16:9, maupun foto tegak portrait) tampil 100% utuh, rapi, dan terpusat di tengah kanvas natural (`#eaf1e7`).
