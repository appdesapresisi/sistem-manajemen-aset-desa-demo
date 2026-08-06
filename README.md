# Sistem Manajemen Aset Desa Demo

Aplikasi web untuk manajemen dan inventarisasi aset milik Desa Demo. Sistem ini membantu pengelolaan data aset desa secara digital, termasuk pencatatan, pelacakan, pelaporan, dan ekspor data inventaris.

## Fitur Utama

- Manajemen data inventaris aset desa
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
│   ├── Http/Controllers/    # Controller aplikasi
│   └── Models/              # Eloquent models
├── database/
│   ├── migrations/          # Database migrations
│   └── seeders/             # Database seeders
├── resources/views/         # Blade templates
├── routes/                  # Route definitions
├── public/                  # Public assets
└── storage/                 # File uploads, logs, cache
```

## Lisensi

Hak cipta © 2024-2026 Desa Presisi. All rights reserved.
