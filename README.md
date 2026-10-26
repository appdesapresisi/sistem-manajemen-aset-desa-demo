# Sistem Manajemen Aset Desa Demo

Aplikasi web untuk manajemen dan inventarisasi aset milik Desa Demo. Sistem ini membantu pengelolaan data aset desa secara digital, termasuk pencatatan, pelacakan, pelaporan, dan ekspor data inventaris.

## Fitur Utama

- Manajemen data inventaris aset desa
- Import data inventaris massal dari Excel (.xlsx) dengan validasi presisi, auto-mapping foreign key, proteksi duplikasi, dan unduh template resmi
- Pencatatan berdasarkan jenis inventaris (KIB A - KIB F)
- QR Code & Barcode untuk pelacakan aset
- Kartu Inventaris Ruangan (KIR)
- Ekspor data ke Excel & PDF
- Manajemen pengguna & hak akses (role-based)
- Manajemen bidang & ruangan
- Manajemen data pejabat
- Notifikasi sistem
- Activity log & riwayat perubahan
- Dashboard statistik aset

## Tech Stack

- **Backend:** Laravel 10 (PHP 8.1+)
- **Frontend:** Blade Templates, Tailwind CSS 4, Vite
- **Database:** MySQL
- **Libraries:**
  - Maatwebsite Excel (export spreadsheet)
  - DomPDF (export PDF)
  - Endroid QR Code (generate QR)
  - Laravel Sanctum (API authentication)

## Persyaratan Sistem

- PHP >= 8.1
- Composer
- Node.js & NPM
- MySQL
- Extension PHP: BCMath, Ctype, Fileinfo, JSON, Mbstring, OpenSSL, PDO, Tokenizer, XML, GD

## Instalasi

### 1. Clone repository

```bash
git clone https://github.com/appdesapresisi/sistem-manajemen-aset-desa-demo.git
cd sistem-manajemen-aset-desa-demo
```

### 2. Install dependencies

```bash
composer install
npm install
```

### 3. Konfigurasi environment

```bash
cp .env.example .env
php artisan key:generate
```

Edit file `.env` dan sesuaikan konfigurasi database:

```
DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=nama_database
DB_USERNAME=root
DB_PASSWORD=
```

### 4. Setup database

Import SQL script yang diberikan oleh pembuat sistem ke database yang sudah dibuat:

```bash
mysql -u root -p nama_database < database_script.sql
```

Atau import melalui phpMyAdmin.

Untuk Migrate bisa saja di running namun disarankan untuk Import SQL Script.

### 5. Generate QR Code / Barcode

Setelah import database, jalankan command berikut untuk generate ulang file barcode (QR Code) yang tidak ikut ter-push ke repository:

```bash
php artisan app:generate-missing-barcodes --force
```

Command ini akan:
- Mengecek semua data aset yang sudah memiliki kode barcode di database
- Generate ulang file PNG QR Code yang belum ada
- Menyimpan file ke folder `public/barcodes/`

> **Catatan:** Langkah ini wajib dilakukan setelah setup awal agar fitur scan QR Code berfungsi dengan benar.

### 6. Build assets

```bash
npm run build
```

### 7. Jalankan aplikasi

```bash
php artisan serve
```

Akses aplikasi di `http://localhost:8000`

## Deployment (Shared Hosting)

1. Upload semua file (kecuali `public/`) ke folder di luar document root
2. Upload isi folder `public/` ke document root (`htdocs` / `public_html`)
3. Edit `index.php` di document root, sesuaikan path ke folder Laravel
4. Pastikan folder `storage/` dan `bootstrap/cache/` memiliki permission 755/775
5. Jalankan `php artisan storage:link` untuk symlink storage

## Struktur Folder Penting

```
├── app/
│   ├── Console/Commands/    # Artisan commands (cleanup, barcode generator)
│   ├── Exports/             # Excel & PDF export classes
│   ├── Imports/             # Excel import & bulk data processing classes
│   ├── Http/Controllers/    # Controller aplikasi
│   └── Models/              # Eloquent models
├── database/
│   ├── migrations/          # Database migrations
│   └── seeders/             # Database seeders
├── resources/views/         # Blade templates
├── routes/                  # Route definitions
├── public/                  # Public assets
├── storage/                 # File uploads, logs, cache
└── docs/                    # Seluruh dokumentasi proyek, panduan teknis, & API
```

## Dokumentasi Proyek

Seluruh dokumentasi teknis dan panduan operasional telah dirapikan ke dalam folder **`docs/`**:

- 📖 **[Panduan Sistem & Manual Pengguna](docs/panduan-sistem.txt)** — Panduan lengkap alur kerja, peran pengguna, dan fitur SIMA.
- 🧪 **[Panduan Pengujian Blackbox (Blackbox Testing Guide)](docs/blackbox-testing-guide.md)** — Rincian 63+ skenario pengujian fungsional dan verifikasi manual.
- 🎨 **[Standar Desain & Design System](docs/design-system.md)** — Token warna, tipografi, dan aturan konsistensi UI.
- 🔌 **[Dokumentasi API & Postman Collection](docs/api/api_documentation.md)** — Spesifikasi endpoint internal/eksternal dan petunjuk import Postman.
- 🛠️ **[Catatan Audit & Perbaikan Bug](docs/audit-and-bug-fixes.md)** — Rekapitulasi histori perbaikan bug dan audit kode.

## Standar Desain & Pedoman UI (Anti-AI-Slop)

Sistem ini memiliki standar UI ketat yang wajib ditaati untuk menjaga konsistensi dan mencegah tampilan generik "AI-slop". Panduan lengkap dan seluruh token warna tercatat di **[docs/design-system.md](docs/design-system.md)**:
- **Palet Warna Netral Hangat**: Menggunakan Ivory/Cream (`#EDEFDC`, `#F7F8EC`) dan Forest Green (`#2E4A2D`), maksimal 10% aksen. Dilarang warna ekstrem `#FFF`/`#000` murni dan gradien ungu-biru.
- **Card & Container**: Dilarang keras menggunakan aksen strip warna di sisi kiri card (`border-left: 3px solid ...`).
- **Form Inputs**: Zero shadow saat aktif/fokus (`box-shadow: none !important`). Pesan error diletakkan tepat di bawah input dengan ikon segitiga peringatan (`⚠️`).
- **Tautan Navigasi**: Navigasi detail wajib menggunakan tautan teks bersih (`color: var(--color-primary)`) berdampingan dengan ikon `chevron-right` (`>`). Dilarang panah lurus `arrow-right` atau tombol kotak banner kuning alert.
- **Standar Tombol Aksi (Wajib Pill-Rounded)**: Seluruh tombol aksi (Primary, Secondary, Danger/Hapus, Submit, Cancel/Batal, Modal Konfirmasi, dan Bulk Bar) **WAJIB berbentuk Pill Rounded (`border-radius: var(--radius-full)` / `9999px`)**. DILARANG KERAS menggunakan tombol berbentuk kotak kaku (`border-radius: 4px`-`12px`). Dilarang menampilkan tombol pill berdampingan dengan tombol kotak.
- **Standar Trigger Dropdown & Form Searchable Select**: Seluruh komponen field dropdown pada formulir (tambah/edit aset, pengguna, pejabat, ruangan, dll.) **WAJIB diimplementasikan menggunakan UI Searchable Dropdown standar Pusat Ekspor** (`.searchable-dropdown` dengan trigger, panel pencarian, dan highlight opsi terpilih). Pengecualian resmi hanya untuk dropdown pemilih Kode Barang dan field khusus tanggal, bulan, dan tahun (Flatpickr/kalender). Menggunakan tinggi `min-height: 48px`, padding `12px 18px`, background `var(--color-surface, #F7F8EC)`, border halus `1px solid var(--color-border, #D8DCC3)`, radius `var(--radius-sm, 8px)`, font 13.5px / 500, icon chevron muted (`#5E6C5A`, 16px) dengan rotasi 180° saat terbuka, hover `var(--color-row-hover)`, dan focus border primary dengan Zero Shadow (`box-shadow: none !important`). Didukung mesin otomatis via `public/assets/js/sima-dropdown.js`.
- **Standar Label Opsi Default Dropdown (Tanpa Tanda `--`)**: Seluruh teks default pilihan dropdown, placeholder input filter, serta trigger label di seluruh sistem (baik pada filter indeks/riwayat maupun form create/edit) **WAJIB menggunakan teks bersih dan profesional tanpa tanda strip dekoratif `--`** (contoh baku: `"Semua Ruangan"`, `"Tanpa Ruangan"`, `"Semua Tahun"`, `"Pilih Ruangan"`, `"Pilih Role"`). **DILARANG KERAS** menggunakan format kuno berpenghias seperti `"-- Semua Ruangan --"`, `"-- Tanpa Ruangan --"`, `"-- Semua Tahun --"`, atau `"-- Pilih ... --"`.
- **Standar Kartu Detail / Show (`.portal-card`)**: Seluruh kartu pembungkus utama pada halaman rincian/detail (*show*) di seluruh sistem **WAJIB berformat lapang dan extended** dengan tinggi minimal `min-height: calc(100vh - 220px)` (desktop `min-height: 580px;`, `display: flex; flex-direction: column;`) serta padding `44px 50px` dan gap `24px 36px`. DILARANG KERAS membuat kartu tampil pendek, gepeng, atau bantet yang menyebabkan konten terasa sesak.
- **Standar Pop-up & Modal Dialog (Divider & Close Button Rules)**: Garis divider (`1px dashed var(--color-border)`) **HANYA** difungsikan sebagai pemisah internal antar-section konten, **DILARANG** ada garis divider di bawah judul/header modal maupun di atas tombol footer modal. Selain itu, **DILARANG** menampilkan tombol icon "X" di pojok kanan atas modal jika pada barisan footer bawah sudah terdapat tombol "Batal" (navigasi batal/tutup cukup 1 pintu via tombol Batal). Icon "X" di pojok atas **HANYA** diimplementasikan jika footer modal tidak memiliki tombol "Batal".
- **Fallback Data Kosong**: Teks polos berwarna Warm Golden Amber (`#B7791F` / `var(--color-warning)`) tanpa card, pill, atau badge pembungkus. Warna kuning/amber eksklusif hanya untuk data kosong (dilarang untuk tombol/link navigasi).

## Lisensi

Hak cipta © 2024-2026 Desa Presisi. All rights reserved.
