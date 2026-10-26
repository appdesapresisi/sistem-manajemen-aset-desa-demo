# DOKUMENTASI API - SISTEM MANAJEMEN ASET DESA (SIMA DEMO)

Dokumentasi ini dibuat untuk membantu developer dan client (seperti aplikasi mobile, sistem eksternal, atau integrasi pihak ketiga) dalam menggunakan API dari Sistem Manajemen Aset Desa (SIMA Demo).

Sistem ini membagi API menjadi dua kelompok utama:
1. **EXTERNAL API (Bearer Token - Laravel Sanctum)**: Digunakan oleh sistem luar atau aplikasi mobile. API ini bersifat **Read-Only** (hanya membaca data) dan dilindungi menggunakan Bearer Token (token yang didapat setelah login).
2. **INTERNAL API (Session / Cookie Web)**: Digunakan oleh frontend web internal. API ini membutuhkan session login web aktif dan mendukung operasi penuh (baca, tambah, ubah, hapus).

---

## PETUNJUK PENGGUNAAN POSTMAN COLLECTION

Untuk mempermudah pengujian API secara cepat, Anda dapat mengimpor file Postman Collection yang tersedia di dalam folder ini:
*   **File Koleksi**: `docs/api/postman_collection.json`
*   **File Environment**: `docs/api/postman_environment.json`

### Langkah-langkah Penggunaan (Postman / Hoppscotch):
1.  **Impor File**:
    *   Buka aplikasi Postman atau Hoppscotch.
    *   Klik menu/tombol **Import** dan pilih file `docs/api/postman_collection.json` (dan `docs/api/postman_environment.json`).
2.  **Variabel Lingkungan (Environment / Variables)**:
    *   `base_url`: Default ke `http://localhost:8000` (sesuaikan port jika berbeda).
    *   `bearer_token`: Variabel ini akan **otomatis terisi** saat Anda menjalankan request *Auth - Generate Token*.
3.  **Generate Token Otomatis**:
    *   Buka folder **`1. External API (Bearer Auth)`** $\rightarrow$ pilih **`1. Auth - Generate Token`** (`POST`).
    *   Request Body sudah terisi default dengan akun Super Admin (`Admin123` / `Admin123!`).
    *   Klik **Send**.
    *   *Script Post-Request* pada koleksi akan **secara otomatis menangkap token dan menyimpannya ke variabel `{{bearer_token}}`**.
4.  **Mulai Pengujian API Lainnya**:
    *   Anda dapat langsung memanggil endpoint lain seperti `Get Jenis Inventaris`, `Get Daftar Inventaris`, dll. Token Bearer akan terkirim secara otomatis tanpa perlu copy-paste manual!
5.  **Revoke / Logout Token**:
    *   Jalankan `8. Auth - Revoke Token (Logout)` untuk menghapus token dari server sekaligus mengosongkan variabel `{{bearer_token}}`.

---

## BAGIAN 1: AUTENTIKASI EXTERNAL API
Sebelum mengakses data, client eksternal harus mendapatkan Token Akses terlebih dahulu.

### 1. Buat Token Akses (Login API)
*   **Endpoint**: `POST /api/external/token`
*   **Tujuan**: Menukarkan username dan password dengan Bearer Token untuk otentikasi.
*   **Rate Limit**: Maksimal 10 kali percobaan dalam 5 menit.
*   **Request Headers**:
    *   `Content-Type: application/json`
    *   `Accept: application/json`
*   **Request Body (JSON)**:
    ```json
    {
      "username": "admin_demo",
      "password": "password_rahasia"
    }
    ```
*   **Response (200 OK - Sukses)**:
    ```json
    {
      "success": true,
      "message": "Token berhasil dibuat. Gunakan di header: Authorization: Bearer {token}",
      "token": "1|abc123xyz789...",
      "user": {
        "id": 1,
        "nama": "Super Admin",
        "username": "admin_demo",
        "role": "Super Admin"
      }
    }
    ```
*   **Response (401 Unauthorized - Gagal)**:
    ```json
    {
      "success": false,
      "message": "Username atau password tidak valid."
    }
    ```

### 2. Hapus Token Akses (Logout API)
*   **Endpoint**: `DELETE /api/external/token`
*   **Tujuan**: Menghapus token yang sedang aktif agar tidak bisa digunakan lagi.
*   **Request Headers**:
    *   `Authorization: Bearer {masukkan_token_di_sini}`
    *   `Accept: application/json`
*   **Response (200 OK)**:
    ```json
    {
      "success": true,
      "message": "Token berhasil dihapus."
    }
    ```

---

## BAGIAN 2: DATA ENDPOINTS (EXTERNAL API - READ ONLY)
Semua endpoint di bagian ini **wajib** menyertakan header:
`Authorization: Bearer {token_anda}`

### 1. Daftar Semua Aset (Inventaris)
*   **Endpoint**: `GET /api/external/inventaris`
*   **Tujuan**: Mengambil daftar seluruh aset desa yang terdaftar.
*   **Query Parameters (Filter & Pencarian)**:
    *   `search` (string, opsional): Cari berdasarkan nama barang, kode barang, atau nomor NUP.
    *   `jenis_inventaris_id` (integer, opsional): Filter berdasarkan kategori jenis inventaris.
    *   `kondisi` (string, opsional): Filter kondisi (`baik`, `rusak_ringan`, `rusak_berat`).
    *   `tahun` (integer, opsional): Filter tahun perolehan aset.
    *   `ruangan_id` (integer, opsional): Filter berdasarkan penempatan ruangan.
    *   `page` (integer, opsional): Halaman data yang ingin dibuka (default: 1).
    *   `per_page` (integer, opsional): Jumlah data per halaman (1 s/d 100, default: 15).
*   **Response (200 OK)**:
    ```json
    {
      "success": true,
      "data": [
        {
          "id": 12,
          "jenis": {
            "id": 3,
            "nama": "Peralatan dan Mesin"
          },
          "nama_barang": "Laptop ASUS ExpertBook B1",
          "kode_barang": "1.3.2.05.003",
          "nup": "0001",
          "satuan": "Unit",
          "tahun_perolehan": "2024",
          "nilai_perolehan": 15000000.00,
          "asal_usul": "APBDes",
          "pengguna": "Sekretariat Desa",
          "kondisi": "baik",
          "keterangan": "Dipakai untuk operasional staf kaur umum",
          "ruangan": {
            "id": 2,
            "nama": "Ruang Tata Usaha",
            "kode": "R-TU"
          },
          "dynamic_values": [
            {
              "field_name": "merk_type",
              "label": "Merk / Type",
              "value": "ASUS ExpertBook B1400"
            },
            {
              "field_name": "ukuran_cc",
              "label": "Ukuran / CC",
              "value": "14 Inch"
            }
          ],
          "created_at": "2026-06-19T20:00:00+08:00",
          "updated_at": "2026-06-19T20:30:00+08:00"
        }
      ],
      "meta": {
        "current_page": 1,
        "per_page": 15,
        "total": 45,
        "last_page": 3
      }
    }
    ```

### 2. Detail Satu Aset
*   **Endpoint**: `GET /api/external/inventaris/{id}`
*   **Tujuan**: Mengambil informasi detail dari satu aset tertentu termasuk riwayat fotonya.
*   **Path Parameter**: `id` (integer): ID aset di database (contoh: `/api/external/inventaris/12`).
*   **Response (200 OK)**:
    ```json
    {
      "success": true,
      "data": {
        "id": 12,
        "jenis": {
          "id": 3,
          "nama": "Peralatan dan Mesin"
        },
        "nama_barang": "Laptop ASUS ExpertBook B1",
        "kode_barang": "1.3.2.05.003",
        "nup": "0001",
        "satuan": "Unit",
        "tahun_perolehan": "2024",
        "nilai_perolehan": 15000000.00,
        "asal_usul": "APBDes",
        "pengguna": "Sekretariat Desa",
        "kondisi": "baik",
        "keterangan": "Dipakai untuk operasional staf kaur umum",
        "ruangan": {
          "id": 2,
          "nama": "Ruang Tata Usaha",
          "kode": "R-TU"
        },
        "dynamic_values": [
          {
            "field_name": "merk_type",
            "label": "Merk / Type",
            "value": "ASUS ExpertBook B1400"
          }
        ],
        "fotos": [
          {
            "id": 45,
            "url": "http://localhost:8000/storage/fotos-aset/laptop_front.jpg",
            "urutan": 1
          }
        ],
        "created_at": "2026-06-19T20:00:00+08:00",
        "updated_at": "2026-06-19T20:30:00+08:00"
      }
    }
    ```
*   **Response (404 Not Found)**:
    ```json
    {
      "success": false,
      "message": "Inventaris tidak ditemukan."
    }
    ```

### 3. Daftar Kategori (Jenis Inventaris)
*   **Endpoint**: `GET /api/external/jenis-inventaris`
*   **Tujuan**: Mengambil semua jenis inventaris aset yang tersedia (contoh: KIB A Jalan, KIB B Peralatan, KIB C Bangunan, dll.) beserta jumlah total aset di dalamnya.
*   **Response (200 OK)**:
    ```json
    {
      "success": true,
      "data": [
        {
          "id": 3,
          "nama": "Peralatan dan Mesin",
          "deskripsi": "Aset peralatan elektronik, mesin, kendaraan, dan perkakas desa.",
          "icon": "cpu",
          "kib_type": "b",
          "jumlah_aset": 28
        }
      ]
    }
    ```

### 4. Daftar Ruangan
*   **Endpoint**: `GET /api/external/ruangan`
*   **Tujuan**: Mengambil daftar ruangan penempatan aset desa beserta jumlah aset di masing-masing ruangan.
*   **Response (200 OK)**:
    ```json
    {
      "success": true,
      "data": [
        {
          "id": 2,
          "nama": "Ruang Tata Usaha",
          "kode": "R-TU",
          "deskripsi": "Ruang kerja staf Kaur Umum dan administrasi.",
          "jumlah_aset": 15
        }
      ]
    }
    ```

### 5. Daftar Pejabat Desa
*   **Endpoint**: `GET /api/external/pejabat`
*   **Tujuan**: Mengambil data pejabat desa yang saat ini berstatus aktif.
*   **Response (200 OK)**:
    ```json
    {
      "success": true,
      "data": [
        {
          "id": 1,
          "nama": "I Made Demo, S.E.",
          "jabatan": "Perbekel (Kepala Desa)",
          "tugas": "Pimpinan tertinggi pemerintahan desa."
        }
      ]
    }
    ```

### 6. Statistik Aset Desa
*   **Endpoint**: `GET /api/external/statistik`
*   **Tujuan**: Mengambil ringkasan data statistik cepat untuk kebutuhan dashboard/visualisasi grafis di aplikasi client.
*   **Response (200 OK)**:
    ```json
    {
      "success": true,
      "data": {
        "total_aset": 145,
        "total_nilai": 482500000.00,
        "total_nilai_format": "Rp 482.500.000",
        "kondisi": {
          "baik": 120,
          "rusak_ringan": 20,
          "rusak_berat": 5
        },
        "total_jenis": 6,
        "total_ruangan": 12,
        "total_pejabat_aktif": 5
      }
    }
    ```

---

## BAGIAN 3: INTERNAL API (DASHBOARD & FORM BUILDER)
API ini dilindungi oleh session auth web internal. Dipakai langsung oleh browser yang sedang aktif masuk ke sistem.

### 1. Dashboard KPI Metrics
*   **Endpoint**: `GET /api/dashboard/kpi`
*   **Tujuan**: Data rangkuman angka dashboard.
*   **Response**:
    ```json
    {
      "totalAssets": 145,
      "totalValue": 482500000,
      "pejabatActive": 5,
      "usersActive": 10,
      "kondisiAset": {
        "baik": 120,
        "rusakRingan": 20,
        "rusakBerat": 5
      }
    }
    ```

### 2. Dashboard Charts Data
*   **Endpoint**: `GET /api/dashboard/charts`
*   **Tujuan**: Mengambil data kompilasi untuk merender grafik Chart.js (Bar chart, Donut chart, Line chart) beserta data filter tahun/semester.
*   **Response (200 OK)**:
    ```json
    {
      "barLabels": [
        "Peralatan dan Mesin",
        "Gedung dan Bangunan",
        "Jalan, Irigasi, dan Jaringan"
      ],
      "barValues": [
        150000000,
        750000000,
        1200000000
      ],
      "barColors": [
        "#10b981",
        "#3b82f6",
        "#f59e0b"
      ],
      "donutLabels": [
        "Peralatan dan Mesin",
        "Gedung dan Bangunan",
        "Jalan, Irigasi, dan Jaringan"
      ],
      "donutValues": [
        45,
        8,
        3
      ],
      "donutColors": [
        "#10b981",
        "#3b82f6",
        "#f59e0b"
      ],
      "monthlyTrend": [
        {
          "month": "Jan 2026",
          "count": 5
        },
        {
          "month": "Feb 2026",
          "count": 12
        }
      ],
      "inventarisByJenis": {
        "peralatan_dan_mesin": 45,
        "gedung_dan_bangunan": 8
      },
      "availableYears": [
        2026,
        2025,
        2024
      ],
      "availableJenis": [
        {
          "id": 2,
          "nama": "Peralatan dan Mesin"
        },
        {
          "id": 3,
          "nama": "Gedung dan Bangunan"
        }
      ],
      "barMetric": "nilai",
      "donutMetric": "jumlah",
      "nilaiInventaris": [
        {
          "id": 2,
          "nama": "Peralatan dan Mesin",
          "nilai": 150000000
        }
      ],
      "inventarisKondisi": [
        {
          "id": 2,
          "nama": "Peralatan dan Mesin",
          "baik": 40,
          "rusakRingan": 4,
          "rusakBerat": 1
        }
      ],
      "trendTahun": 2026
    }
    ```

### 3. Dynamic Fields Form Builder
*   **Endpoint**: `GET /api/jenis-inventaris/{id}/fields`
*   **Tujuan**: Mengambil input formulir tambahan secara dinamis sesuai kategori jenis inventaris yang dipilih ketika admin membuat/mengedit aset.
*   **Response (200 OK)**:
    ```json
    [
      {
        "id": 14,
        "jenis_inventaris_id": 3,
        "nama_field": "merk_type",
        "label_field": "Merk / Type",
        "tipe_field": "text",
        "is_required": false
      }
    ]
    ```

### 4. Pencarian Kode Barang Hierarki (5 Level)
*   **Endpoint**: `GET /api/kb/search?q={keyword}&level={1-5}`
*   **Tujuan**: Autocomplete kode barang berdasarkan Peraturan Kemendagri dari Level 1 (Golongan) sampai Level 5 (Sub-sub Kelompok).
*   **Query Parameters**:
    *   `q` (string, opsional): Kata kunci pencarian (nama kategori atau kode).
    *   `level` (integer, default 5): Level pencarian (1 s/d 5).
    *   `parent_id` (integer, opsional): ID induk dari level sebelumnya untuk filter hierarki.
*   **Response (200 OK)**:
    ```json
    [
      {
        "id": 125,
        "kode_lengkap": "1.3.2.05.003",
        "kode": "003",
        "uraian": "Laptop / Personal Computer",
        "label": "1.3.2.05.003 — Laptop / Personal Computer"
      },
      {
        "id": 126,
        "kode_lengkap": "1.3.2.05.004",
        "kode": "004",
        "uraian": "Notebook",
        "label": "1.3.2.05.004 — Notebook"
      }
    ]
    ```

### 5. Log Aktivitas Terbaru
*   **Endpoint**: `GET /api/dashboard/activities`
*   **Tujuan**: Mengambil daftar 10 aktivitas terbaru di sistem untuk dashboard.
*   **Response (200 OK)**:
    ```json
    [
      {
        "text": "Admin Demo menambahkan aset Laptop ASUS ExpertBook",
        "time": "5 menit yang lalu",
        "color": "#10b981"
      }
    ]
    ```

### 6. Riwayat Ekspor Data Terbaru
*   **Endpoint**: `GET /api/dashboard/exports`
*   **Tujuan**: Mengambil daftar 10 berkas riwayat ekspor data terbaru.
*   **Response (200 OK)**:
    ```json
    [
      {
        "user": "Super Admin",
        "jenis": "Peralatan dan Mesin",
        "tanggal": "19 Jun 2026, 20:30",
        "tipe": "PDF"
      }
    ]
    ```

### 7. Daftar Dropdown Ruangan (Internal)
*   **Endpoint**: `GET /api/ruangan`
*   **Tujuan**: Mengambil daftar seluruh ruangan untuk pilihan dropdown formulir internal.
*   **Response (200 OK)**:
    ```json
    [
      {
        "id": 2,
        "nama": "Ruang Tata Usaha",
        "kode": "R-TU"
      }
    ]
    ```

---

## BAGIAN 4: FORMAT ERROR & HTTP STATUS CODES
API ini menggunakan standard status HTTP untuk mempermudah deteksi kesalahan integrasi:
*   `200 OK`: Request berhasil dilakukan.
*   `201 Created`: Data baru berhasil dibuat (pada operasi POST).
*   `400 Bad Request`: Parameter atau input request yang dikirimkan tidak sesuai format.
*   `401 Unauthorized`: Token Bearer tidak dikirimkan, salah ketik, atau masa berlakunya sudah habis.
*   `403 Forbidden`: User tidak memiliki akses/privilese untuk data tersebut.
*   `404 Not Found`: Data, ID, atau Endpoint yang dituju tidak ditemukan.
*   `422 Unprocessable Entity`: Input validasi gagal (misalnya format data salah atau field wajib kosong).
*   `429 Too Many Requests`: Client diblokir sementara karena melebihi batas request (*rate limit*).
*   `500 Internal Server Error`: Terjadi kesalahan internal pada server aplikasi.

