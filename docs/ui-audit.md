# UI Audit & Redesign Mapping

Dokumen ini merupakan hasil audit menyeluruh pada seluruh antarmuka sistem **Sistem Manajemen Aset Desa (SIMA Demo)** sebelum memulai eksekusi penulisan kode komponen. Disusun berdasarkan panduan [design.md](file:///c:/Portofolio/LARAVEL/manajemen_aset_desa_demo/design.md) dan [redesign-brief.md](file:///c:/Portofolio/LARAVEL/manajemen_aset_desa_demo/redesign-brief.md) dengan mengadopsi struktur visual dari referensi *Prodex Table*, *Dashboard Analytics*, dan palet hangat *Custora*.

---

## 1. Stack Frontend & Komponen Saat Ini

- **Core Engine:** Laravel 10 (Blade Templating Engine)
- **CSS Architecture:** Vanilla CSS Tokenized Design System (diinjeksi via `resources/views/layouts/head-css.blade.php`), Bootstrap 5 Grid / Modals / Dropdowns dasar.
- **Ikonografi:** Feather Icons SVG (`feather.replace()`)
- **Visualisasi & Interaktivitas:**
  - ApexCharts / Chart.js untuk visualisasi dashboard
  - Signature Pad untuk tanda tangan digital pejabat
  - Vanilla JS + AJAX (`api-helper.js`, `script.js`) untuk dynamic search, filter, pagination, dan barcode scanner.
- **Komponen UI Eksisting:**
  - `layouts/sidebar.blade.php` & `layouts/header-content.blade.php` (Shell navigasi)
  - `layouts/modals.blade.php` (Modal hapus, restore, import, preview)
  - `layouts/notification.blade.php` (Toast & alert trigger)

---

## 2. Pemetaan Seluruh Halaman ke 5 Template Baku (A–E)

Semua halaman sistem terpetakan dengan tepat ke dalam 5 template tanpa ada layout liar/aneh:

| Nama Halaman | Route Name | File Blade | Template | Keterangan Layout |
| :--- | :--- | :--- | :---: | :--- |
| **Dashboard** | `dashboard.index` | `dashboard/index.blade.php` | **B** (Dashboard) | KPI baris atas, grafik breakdown, tabel perhatian & aktivitas |
| **Aktivitas Sistem** | `dashboard.activities` | `dashboard/activities.blade.php` | **A** (List) | Log riwayat aktivitas pengguna sistem |
| **Manajemen Aset** | `manajemen-aset.index` | `manajemen-aset/index.blade.php` | **A** (List) | Tabel inventaris induk, stat card kondisi, bulk action |
| **Detail Aset** | `manajemen-aset.show` | `manajemen-aset/show.blade.php` | **C** (Detail) | Header status aset, tab spesifikasi dinamis, foto, barcode |
| **Tambah Aset** | `manajemen-aset.create` | `manajemen-aset/create.blade.php` | **D** (Form) | Section identitas, kategori dinamis, foto, ruangan |
| **Ubah Aset** | `manajemen-aset.edit` | `manajemen-aset/edit.blade.php` | **D** (Form) | Section update data aset & nilai perolehan |
| **Penghapusan Aset** | `penghapusan-aset.index` | `penghapusan-aset/index.blade.php` | **A** (List) | Tab ganda: Aset Aktif Siap Dihapus & Riwayat Mutasi |
| **Pusat Export Data** | `export-data.index` | `export-data/index.blade.php` | **A** (List / Hub) | Tab format export: KIB A-F, KIR, Laporan Aset, Buku Inv |
| **Riwayat Export** | `export-data.history` | `export-data/history.blade.php` | **A** (List) | Log unduhan berkas PDF & Excel |
| **Jenis Inventaris** | `jenis-inventaris.index` | `jenis-inventaris/index.blade.php` | **A** (List) | Daftar kategori/klasifikasi barang |
| **Detail Jenis** | `jenis-inventaris.show` | `jenis-inventaris/show.blade.php` | **C** (Detail) | Struktur custom dynamic fields & pejabat default |
| **Form Jenis** | `jenis-inventaris.create/edit`| `jenis-inventaris/*.blade.php` | **D** (Form) | Form nama, mode ikon, builder custom fields |
| **Ruangan** | `ruangan.index` | `ruangan/index.blade.php` | **A** (List) | Daftar ruangan dan penanggung jawab |
| **Detail Ruangan** | `ruangan.show` | `ruangan/show.blade.php` | **C** (Detail) | Info ruangan + daftar aset di dalam ruangan |
| **Form Ruangan** | `ruangan.create/edit` | `ruangan/*.blade.php` | **D** (Form) | Form kode ruangan, lokasi, penanggung jawab |
| **Pejabat** | `pejabat.index` | `pejabat/index.blade.php` | **A** (List) | Daftar perangkat desa / penandatangan resmi |
| **Detail Pejabat** | `pejabat.show` | `pejabat/show.blade.php` | **C** (Detail) | Profil jabatan & pratinjau tanda tangan |
| **Form Pejabat** | `pejabat.create/edit` | `pejabat/*.blade.php` | **D** (Form) | Data diri, NIP/Jabatan, canvas tanda tangan |
| **Pengguna** | `users.index` | `users/index.blade.php` | **A** (List) | Manajemen akun operator & hak akses role |
| **Detail Pengguna** | `users.show` | `users/show.blade.php` | **C** (Detail) | Profil user, status aktif, riwayat login sesi |
| **Form Pengguna** | `users.create/edit` | `users/*.blade.php` | **D** (Form) | Akun, password, role, bidang |
| **Bidang** | `bidangs.index` | `bidangs/index.blade.php` | **A** (List) | Daftar bidang pemerintahan desa |
| **Dropdown Dinamis**| `dropdown.index` | `dropdown/index.blade.php` | **A** (List) | Kategori opsi pilihan custom |
| **Notifikasi** | `notifications.index` | `notifications/index.blade.php`| **A** (List) | Kotak masuk notifikasi & alert sistem |
| **Profil Saya** | `profile.show` | `profile/show.blade.php` | **E** (Pengaturan) | Pengaturan akun dan biodata |
| **Ganti Sandi** | `profile.change-password` | `profile/change-password.blade.php`| **E** (Pengaturan) | Keamanan akun |
| **Scan Publik** | `inventaris.scan` | `scan/show.blade.php` | **C** (Detail) | Tampilan pratinjau mobile saat scan QR Code |

---

## 3. Audit Tabel Data: Kolom Default, Secondary, Aksi & Filter

Mengacu pada aturan minimalis (maksimal 6 kolom data utama + checkbox + aksi per baris):

### A. Tabel Manajemen Aset (`manajemen-aset.index`)
- **Stat Cards (4 Card Atas):**
  1. Total Inventaris (angka + titik biru/primer)
  2. Kondisi Baik (angka + titik hijau sukses)
  3. Rusak Ringan (angka + titik kuning warning)
  4. Rusak Berat (angka + titik merah danger)
- **Status Tabs:** `Semua Aset` | `Kondisi Baik` | `Rusak Ringan` | `Rusak Berat`
- **Kolom Default Tampil (6 Kolom):**
  1. `[Checkbox]`
  2. **Barang & Kode:** Nama barang + Kode Barang & NUP kecil di bawahnya (digabung).
  3. **Kategori & Ruangan:** Jenis inventaris + badge ruangan (digabung).
  4. **Tahun & Asal:** Tahun perolehan + asal-usul barang.
  5. **Nilai Perolehan:** Nominal (format Rp, tabular numbers, rata kanan).
  6. **Kondisi:** Status pill lembut (`Baik`, `Rusak Ringan`, `Rusak Berat`).
  7. `[Aksi]`: Ikon Edit, Hapus, dan Menu `⋯` (Detail, Duplikat, Cetak Label QR).
- **Kolom Sekunder (Dapat dilihat via Drawer Detail / Modal):**
  - Bukti Kepemilikan, Pengguna/Penanggung Jawab, Spesifikasi KIB Tambahan, Keterangan Lengkap.
- **Bulk Action Bar (Floating melayang di bawah):**
  - `[N] Terpilih` | `Duplikat` | `Cetak Barcode / Label` | `Hapus / Mutasi` | `Batal (×)`
- **Filter Popover:** Filter Bidang, Filter Ruangan, Filter Tahun Perolehan.

### B. Tabel Penghapusan Aset (`penghapusan-aset.index`)
- **Stat Cards:** Total Aset Dihapus, Nilai Total Terhapus, Penghapusan Tahun Ini.
- **Status Tabs:** `Semua Riwayat` | `Rusak Total` | `Dilelang` | `Hilang` | `Dihibahkan`
- **Kolom Default (6 Kolom):**
  1. `[Checkbox]`
  2. **Barang & NUP:** Nama barang + Kode & NUP.
  3. **Alasan & No. SK:** Alasan penghapusan + No. SK Penetapan.
  4. **Tanggal Hapus:** Tanggal mutasi + pencatat.
  5. **Nilai Aset:** Nilai perolehan awal.
  6. **Dokumen:** Link download Berita Acara PDF resmi.
  7. `[Aksi]`: Tombol Restore (Kembalikan Aset) & Detail Snapshot.

### C. Tabel Pengguna (`users.index`)
- **Status Tabs:** `Semua Pengguna` | `Aktif` | `Nonaktif`
- **Kolom Default (5 Kolom):**
  1. `[Checkbox]`
  2. **Pengguna:** Avatar inisial + Nama lengkap + Username di bawah.
  3. **Peran & Bidang:** Badge Role (Super Admin, Sekretaris, Kaur) + Nama Bidang.
  4. **Status:** Pill Aktif / Nonaktif (dapat di-toggle).
  5. **Login Terakhir:** Waktu sesi terakhir.
  6. `[Aksi]`: Edit, Hapus, Toggle Status.

### D. Tabel Pejabat, Ruangan, Bidang, Jenis Inventaris
- Disusun seragam dengan format: Kolom Utama (Avatar/Ikon + Nama), Kolom Info Kunci, Status Aktif / Jumlah Terkait, dan Kolom Aksi terstandarisasi.

---

## 4. Pemetaan Dashboard ke 3 Pertanyaan Kunci

Dashboard (`dashboard/index.blade.php`) direstrukturisasi agar menjawab 3 pertanyaan operasional perangkat desa:

```
+-----------------------------------------------------------------------------------+
| 1. BAGAIMANA KONDISINYA? (Baris KPI Metrics)                                      |
| [ Total Nilai Aset ]  [ Total Barang ]  [ Kondisi Baik ]  [ Perlu Perbaikan ]    |
+---------------------------------------------------+-------------------------------+
| 2. APA YANG BERUBAH?                              | 2. BREAKDOWN DISTRIBUSI       |
| [ Grafik Tren Pengadaan Aset per Tahun/Bulan ]    | [ Donut Komposisi Jenis Aset] |
| (Grafik Datar 1 Warna #2E4A2D + Line #7A9A5C)     | (Legenda Nilai & Persentase)  |
+---------------------------------------------------+-------------------------------+
| 3. APA YANG HARUS DITINDAK? (Tabel Perhatian)     | 3. AKTIVITAS TERKINI (Feed)   |
| [ Tabel Aset Rusak Berat / Perlu Penghapusan ]    | [ Stream Log Operator Desa ]  |
+-----------------------------------------------------------------------------------+
```

- **Elemen Dihilangkan:** Efek gradient ungu-biru, 3D chart, confetti statistik, widget marketing.
- **Tautan Balik:** Setiap card widget memiliki link `"Lihat semua →"` menuju modul terkait.

---

## 5. Struktur Menu Sidebar

Struktur sidebar disederhanakan ke dalam kelompok alur kerja yang jelas:

- **UTAMA**
  - Dashboard (`/dashboard`)
- **INVENTARIS DESA**
  - Buku Inventaris (`/manajemen-aset`)
  - Penghapusan Aset (`/penghapusan-aset`)
  - Ruangan & Lokasi (`/ruangan`)
- **PELAPORAN & DOKUMEN**
  - Pusat Cetak & Export (`/export-data`)
  - Riwayat Unduhan (`/export-data/history`)
- **PENGATURAN & MASTER**
  - Jenis & Format Barang (`/jenis-inventaris`)
  - Perangkat / Pejabat (`/pejabat`)
  - Manajemen Pengguna (`/users`)
  - Bidang & Dropdown (`/bidangs`, `/dropdown`)
  - Aktivitas & Log (`/dashboard/activities`)
- **PROFIL PENGGUNA (Bawah Sidebar)**
  - Avatar inisial, Nama pengguna, Peran, Tombol Profil & Logout.

---

## 6. Daftar Risiko & Mitigasi

1. **Kompleksitas Dynamic Fields pada Manajemen Aset:**
   - *Risiko:* Setiap jenis barang (Tanah, Bangunan, Kendaraan) memiliki kolom custom KIB yang berbeda.
   - *Mitigasi:* Tabel utama menampilkan kolom standar universal (Nama, Kode, Ruangan, Nilai, Kondisi), sedangkan kolom KIB spesifik ditampilkan di side-drawer detail atau modal tanpa merusak lebar tabel.
2. **Ketergantungan Script AJAX & Selector DOM:**
   - *Risiko:* Perubahan class Bootstrap lama dapat memutus event handler JavaScript.
   - *Mitigasi:* Pertahankan selector data attribute atau ID fungsional (`#bulk-delete-btn`, `[data-id]`, form triggers) saat memperbarui HTML/CSS.
3. **Hardcoded Color di Template Lama:**
   - *Risiko:* Masih ada sisa inline color atau class utility seperti `bg-primary`, `text-success`.
   - *Mitigasi:* Semua styling diarahkan ke CSS Custom Properties (`var(--color-primary)`, `var(--color-surface)`, dll.) yang didefinisikan di satu tempat.

---

### Status Fase 0: **SELESAI (Menunggu Persetujuan Pengguna)**
File ini siap menjadi acuan resmi untuk melangkah ke **Fase 1: Token dan Komponen Dasar**.
