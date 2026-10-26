# 📋 DAFTAR AUDIT DAN RIWAYAT PERBAIKAN BUG SISTEM (SIMA DEMO)
### Sistem Informasi Manajemen Aset Desa (SIMA) — Panduan Pelacakan Bug 100% Anti-Halu

Dokumen ini mencatat seluruh riwayat audit, identifikasi, dan perbaikan bug nyata (*zero hallucination*) di dalam basis kode **Sistem Informasi Manajemen Aset Desa**. Setiap temuan disajikan dalam **2 Bahasa**:
1. **Bahasa Teknis**: Lokasi file, baris kode, variabel, alur logika, dampak teknis, dan verifikasi unit test.
2. **Bahasa Awam / Bayi**: Analogi kehidupan sehari-hari yang sederhana, mudah dipahami siapapun tanpa istilah teknis rumit.

---

## 📑 Daftar Isi
1. [Audit Terbaru: Bug 1 s.d. 5 (Selesai Diperbaiki & Lolos Uji)](#bagian-1-audit-terbaru-bug-1-sd-5-selesai-diperbaiki--lolos-uji)
2. [Temuan Bug Tambahan Baru (Bug 6 s.d. 24)](#bagian-2-temuan-bug-tambahan-baru-bug-6-sd-24)
3. [Arsip Riwayat Perbaikan Bug Sebelumnya (Bug H1 s.d. H52)](#bagian-3-arsip-riwayat-perbaikan-bug-sebelumnya)
4. [Bukti Status Pengujian Otomatis (Anti-Halu)](#bagian-4-bukti-status-pengujian-otomatis-anti-halu)

---

## Bagian 1: Audit Terbaru (Bug 1 s.d. 5) — Selesai Diperbaiki & Lolos Uji

---

### 🟢 Bug 1: Crash Fatal Excel saat Export Seluruh KIB jika Data Kosong
* **Status**: ✅ **Telah Diperbaiki & Terverifikasi**
* **Lokasi Berkas**: [`app/Services/KibExportService.php:L228–L242`](file:///c:/Portofolio/LARAVEL/manajemen_aset_desa_demo/app/Services/KibExportService.php#L228-L242)
* **Bahasa Teknis**:
  * Ketika pengguna mengekspor Semua KIB (`kib_type = 'all'`) ke Excel pada tahun yang belum memiliki data aset, array `$sheets` bernilai kosong `[]`. Library `PhpSpreadsheet` langsung *crash* fatal dengan exception `out of bounds index: 0` pada `Spreadsheet.php:836` karena tidak ada lembar (*worksheet*) aktif.
  * *Perbaikan*: Menambahkan injeksi *fallback sheet* (KIB A kosong berformat rapi) jika `$sheets` kosong sebelum memanggil `Excel::store()`.
  * *Bukti Anti-Halu*: Unit test `test_kib_export_all_excel_handles_empty_dataset_without_phpspreadsheet_crash` $\rightarrow$ **PASS**.
* **Bahasa Awam / Bayi**:
  * 👶 *Analogi Buku Gambar Kosong*: Bayangkan kita menyuruh tukang jilid membuat album foto tahun 2010. Tapi di tahun itu belum ada foto sama sekali. Si tukang jilid bingung karena map bukunya tidak punya selembar kertas pun di dalamnya, lalu dia pingsan (*crash*). Sekarang, sistem sudah disiapkan kertas kosong bertuliskan "KIB A" sehingga buku tetap bisa dijilid dengan rapi tanpa ada yang pingsan.

---

### 🟢 Bug 2: Akun Super Developer / Pemilik Sistem Bisa Terhapus lewat Web & API
* **Status**: ✅ **Telah Diperbaiki & Terverifikasi**
* **Lokasi Berkas**: 
  * [`app/Http/Controllers/User/UserController.php:L748`](file:///c:/Portofolio/LARAVEL/manajemen_aset_desa_demo/app/Http/Controllers/User/UserController.php#L748)
  * [`app/Http/Controllers/Api/UserApiController.php:L412`](file:///c:/Portofolio/LARAVEL/manajemen_aset_desa_demo/app/Http/Controllers/Api/UserApiController.php#L412)
* **Bahasa Teknis**:
  * Pada method `destroy` di `UserController` dan `UserApiController`, proteksi pencegahan hapus hanya memeriksa `isSuperAdmin()`. Akun ber-role `super_developer` (pemilik platform SIMA) belum dicek, sehingga dapat terhapus lewat form web atau HTTP request API.
  * *Perbaikan*: Menambahkan guard ketat `if ($user->isSuperDeveloper() || $user->isSuperAdmin())` yang mengembalikan redirect flash error pada Web dan respon HTTP `403 Forbidden` pada API.
  * *Bukti Anti-Halu*: Unit test `test_super_developer_is_protected_from_deletion_via_web_and_api` $\rightarrow$ **PASS (8 assertions)**.
* **Bahasa Awam / Bayi**:
  * 👶 *Analogi Mandor Gedung*: Rumah desa punya satpam utama (*Super Admin*) dan mandor pembuat rumah (*Super Developer*). Di pintu gerbang ada aturan "Satpam tidak boleh diusir", tapi aturan lupa menulis nama si mandor. Akibatnya, mandor bisa saja tidak sengaja diusir keluar rumah oleh orang lain. Sekarang gembok pintu sudah diperbarui: satpam dan mandor sama-sama tidak boleh diusir oleh siapapun.

---

### 🟢 Bug 3: Super Developer Terblokir 403 saat Membuka Halaman Maintenance Regenerate QR
* **Status**: ✅ **Telah Diperbaiki & Terverifikasi**
* **Lokasi Berkas**: [`app/Http/Controllers/ManajemenAset/ManajemenAsetController.php:L1082`](file:///c:/Portofolio/LARAVEL/manajemen_aset_desa_demo/app/Http/Controllers/ManajemenAset/ManajemenAsetController.php#L1082)
* **Bahasa Teknis**:
  * Method `showMaintenanceQrRegenerate` memakai pengecekan kaku: `abort_unless($user && $user->isSuperAdmin(), 403);`. Karena akun Super Developer bukan role Super Admin, developer utama sistem justru diblokir dengan kode HTTP 403 saat hendak memperbaiki/regenerasi kode QR aset.
  * *Perbaikan*: Memperluas otorisasi: `abort_unless($user && ($user->isSuperAdmin() || $user->isSuperDeveloper()), 403);`.
  * *Bukti Anti-Halu*: Unit test `test_super_developer_is_authorized_for_maintenance_qr_regeneration` $\rightarrow$ **PASS**.
* **Bahasa Awam / Bayi**:
  * 👶 *Analogi Bengkel Terkunci*: Ada ruang bengkel khusus servis kode QR di kantor desa. Kuncinya cuma dikasih ke Pak Kades (*Super Admin*), sedangkan montir ahlinya (*Super Developer*) malah dicegat di depan pintu dan tidak boleh masuk untuk membetulkan barang rusak. Sekarang montir sudah diberi kunci resmi agar bisa masuk kapan saja.

---

### 🟢 Bug 4: Card 4 Dashboard Users ("ADMIN & SUPER ADMIN") Menghitung 0 untuk Akun Super Admin
* **Status**: ✅ **Telah Diperbaiki & Terverifikasi**
* **Lokasi Berkas**: [`app/Http/Controllers/User/UserController.php:L118, L147`](file:///c:/Portofolio/LARAVEL/manajemen_aset_desa_demo/app/Http/Controllers/User/UserController.php#L118)
* **Bahasa Teknis**:
  * Pada query agregasi Card 4 Dashboard Pengguna:
    ```sql
    SUM(CASE WHEN (LOWER(COALESCE(r.nama, '')) LIKE '%sekretaris%' OR ... LIKE '%staf%') THEN 1 ELSE 0 END) as total_admin
    ```
    Query di atas memeriksa perbekel, sekdes, kaur, dan staf, tetapi sama sekali lupa menyertakan kata kunci `LIKE '%admin%'`. Akibatnya, 2 akun Super Admin aktif di basis data terhitung 0 pada kartu statistik.
  * *Perbaikan*: Menambahkan `OR LOWER(COALESCE(r.nama, '')) LIKE '%admin%'` pada kalkulasi `total_admin` dan tren bulanan.
  * *Bukti Anti-Halu*: Unit test `test_user_index_total_admin_card_includes_super_admin_roles` $\rightarrow$ **PASS**.
* **Bahasa Awam / Bayi**:
  * 👶 *Analogi Papan Absen Salah Tulis*: Papan pengumuman di kantor desa punya kotak judul bertuliskan: **"JUMLAH ADMIN & SUPER ADMIN"**. Tapi rumus hitung di balik papannya aneh: satpam, tukang kebun, dan staf kantor dihitung, tapi bos admin dan wakil adminnya sendiri malah dicoret dari daftar hitungan! Akibatnya di papan tertulis angka 0 untuk admin. Sekarang rumusnya sudah dibetulkan sehingga semua admin ikut terhitung dengan jujur.

---

### 🟢 Bug 5: Pelanggaran Keseimbangan Akuntansi pada Laporan Aset (Double Count Semester 2)
* **Status**: ✅ **Telah Diperbaiki & Terverifikasi**
* **Lokasi Berkas**: [`app/Services/LaporanAsetService.php:L97–L179, L651–L667`](file:///c:/Portofolio/LARAVEL/manajemen_aset_desa_demo/app/Services/LaporanAsetService.php#L97-L179)
* **Bahasa Teknis**:
  * Pada pembuatan Laporan Aset Semester 2:
    1. Query `$saldoAwalDeleted` per 1 Juli menyertakan aset yang dihapus di semester 2 tanpa memeriksa bulan perolehannya (`orWhere('tahun_perolehan', $tahun)`).
    2. Query `$bertambahDeleted` juga menyertakan aset yang sama karena dihapus di semester 2 (`deleted_at BETWEEN 1 Juli s/d 31 Desember`).
    3. Query `$berkurang` kemudian mengurangi aset tersebut (-1).
  * Dampak Matematis: $\text{Saldo Akhir} = \text{Saldo Awal (1)} + \text{Bertambah (1)} - \text{Berkurang (1)} = 1$. Aset perolehan tahun berjalan yang dihapus di semester 2 secara keliru menyisakan saldo 1 unit (seharusnya 0).
  * *Perbaikan*: Mengimplementasikan helper `getDeletedAssetBulanPerolehan()` untuk membaca snapshot bulan perolehan. `$saldoAwalDeleted` hanya memasukkan aset tahun berjalan jika diperoleh pada Semester 1 ($\le 6$). Sementara `$bertambahDeleted` Semester 2 hanya memasukkan aset yang memang diperoleh pada Semester 2 ($\ge 7$).
  * *Bukti Anti-Halu*: Unit test `test_laporan_aset_semester_2_prevents_double_counting_acquired_and_deleted_assets` $\rightarrow$ **PASS (10 assertions)**.
* **Bahasa Awam / Bayi**:
  * 👶 *Analogi Donat Ajaib yang Tidak Mau Habis* 🍩:
    Bayangkan adik kecil punya toples kue kosong di bulan Januari.
    1. Di bulan **Agustus**, Ibu membelikan adik **1 donat cokelat**.
    2. Di bulan **September**, donat itu **langsung dimakan habis** sampai piringnya licin!
    Harusnya pas tutup tahun di bulan Desember toplesnya kosong (0 donat), kan?
    Tapi catatan si kakak yang ngantuk mencatatnya begini:
    - *"Di bulan Juli adik sudah punya 1 donat"* (padahal baru dibeli Agustus!)
    - *"Di bulan Agustus adik dapat donat lagi +1"*
    - *"Di bulan September donatnya dimakan -1"*
    Hitungan si kakak: **1 (khayalan bulan Juli) + 1 (beli Agustus) - 1 (dimakan) = 1 DONAT SISA!**
    Toplesnya sudah kosong sejak September, tapi buku catatan bilang masih ada 1 donat. Itu namanya catat dobel (*double count*). Sekarang kakak sudah cuci muka dan mencatat dengan benar!

---

## Bagian 2: Temuan Bug Tambahan Baru (Bug 6 s.d. 24)

---

### 🟢 Bug 6: Super Developer Terblokir (HTTP 403) saat Mengubah Data Pengguna via Form Edit
* **Status**: ✅ **Telah Diperbaiki & Terverifikasi**
* **Lokasi Berkas**: [`app/Http/Requests/UpdateUserRequest.php:L10–L14`](file:///c:/Portofolio/LARAVEL/manajemen_aset_desa_demo/app/Http/Requests/UpdateUserRequest.php#L10-L14)
* **Bahasa Teknis**:
  * Pada `UpdateUserRequest`:
    ```php
    public function authorize()
    {
        return $this->user()->isSuperAdmin();
    }
    ```
    Method `authorize()` sebelumnya memanggil `$this->user()->isSuperAdmin()`. Untuk akun `super_developer`, pemanggilan ini mengembalikan nilai `false` karena nama rolenya adalah `super_developer`.
  * Akibatnya, saat Super Developer mengedit pengguna melalui form web (`UserController::update`) atau API (`UserApiController::update`), Laravel langsung melempar pengecualian otorisasi HTTP 403 Forbidden. Padahal di `UserPolicy::update`, Super Developer secara tegas diberikan izin penuh (`return $user->isSuperDeveloper() || $user->isSuperAdmin();`).
  * *Perbaikan*: Mengubah otorisasi menjadi:
    ```php
    return $this->user() && ($this->user()->isSuperDeveloper() || $this->user()->isSuperAdmin());
    ```
  * *Bukti Anti-Halu*: Unit test `test_super_developer_is_authorized_for_all_master_form_requests` $\rightarrow$ **PASS**.
* **Bahasa Awam / Bayi**:
  * 👶 *Analogi Satpam Baru Mengusir Pemilik Gedung*: Pemilik gedung (*Super Developer*) mau masuk ruangan ganti nama staf (*Edit User*). Di pintu ruangan ada satpam magang yang memegang buku aturan kecil bertuliskan: "Hanya yang pakai topi hitam (*Super Admin*) yang boleh lewat". Si pemilik gedung yang pakai topi emas (*Super Developer*) diusir keluar dan dilarang masuk, padahal pemilik gedung punya izin di atas segalanya. Sekarang satpamnya sudah diajari bahwa topi emas juga pemilik sah gedung.

---

### 🟢 Bug 7: Hardcoded `isSuperAdmin()` pada Seluruh FormRequest Master Data Mem-bypass Gate Super Developer
* **Status**: ✅ **Telah Diperbaiki & Terverifikasi**
* **Lokasi Berkas**:
  * [`app/Http/Requests/StoreBidangRequest.php:L14`](file:///c:/Portofolio/LARAVEL/manajemen_aset_desa_demo/app/Http/Requests/StoreBidangRequest.php#L14)
  * [`app/Http/Requests/UpdateBidangRequest.php:L14`](file:///c:/Portofolio/LARAVEL/manajemen_aset_desa_demo/app/Http/Requests/UpdateBidangRequest.php#L14)
  * [`app/Http/Requests/StoreRuanganRequest.php:L11`](file:///c:/Portofolio/LARAVEL/manajemen_aset_desa_demo/app/Http/Requests/StoreRuanganRequest.php#L11)
  * [`app/Http/Requests/UpdateRuanganRequest.php:L11`](file:///c:/Portofolio/LARAVEL/manajemen_aset_desa_demo/app/Http/Requests/UpdateRuanganRequest.php#L11)
  * [`app/Http/Requests/StoreDropdownRequest.php:L11`](file:///c:/Portofolio/LARAVEL/manajemen_aset_desa_demo/app/Http/Requests/StoreDropdownRequest.php#L11)
  * [`app/Http/Requests/UpdateDropdownRequest.php:L11`](file:///c:/Portofolio/LARAVEL/manajemen_aset_desa_demo/app/Http/Requests/UpdateDropdownRequest.php#L11)
  * [`app/Http/Requests/UpdateJenisInventarisRequest.php:L11`](file:///c:/Portofolio/LARAVEL/manajemen_aset_desa_demo/app/Http/Requests/UpdateJenisInventarisRequest.php#L11)
* **Bahasa Teknis**:
  * Pada ketujuh FormRequest di atas, method `authorize()` sebelumnya di-hardcode dengan `return $this->user()->isSuperAdmin();` atau `auth()->user()->isSuperAdmin();`.
  * Pola ini mem-bypass `Gate::before` pada `AuthServiceProvider` (karena FormRequest dieksekusi sebelum controller dan tidak memanggil Gate check `$this->user()->can(...)`). Akibatnya, Super Developer diblokir dengan HTTP 403 saat mencoba menambah/mengedit Bidang, Ruangan, Dropdown, dan Jenis Inventaris.
  * *Perbaikan*: Menyelaraskan seluruh method `authorize()` menjadi:
    ```php
    return $this->user() && ($this->user()->isSuperDeveloper() || $this->user()->isSuperAdmin());
    ```
  * *Bukti Anti-Halu*: Unit test `test_super_developer_is_authorized_for_all_master_form_requests` $\rightarrow$ **PASS (9 assertions)**.
* **Bahasa Awam / Bayi**:
  * 👶 *Analogi Kunci Duplikat Tertinggal*: Ada 7 kamar lemari master data di kantor desa (lemari ruangan, lemari bidang, lemari dropdown, dll). Satpam lupa memberikan kunci 7 lemari ini kepada si insinyur utama (*Super Developer*), sehingga setiap kali si insinyur mau membuka lemari, pintu terkunci rapat. Sekarang semua 7 lemari sudah dibukakan kunci masternya untuk si insinyur.

---

### 🟢 Bug 8: Super Developer Terblokir (HTTP 403) saat Mengubah Data Aset via `UpdateInventarisRequest`
* **Status**: ✅ **Telah Diperbaiki & Terverifikasi**
* **Lokasi Berkas**: 
  * [`app/Http/Requests/UpdateInventarisRequest.php:L14–L18`](file:///c:/Portofolio/LARAVEL/manajemen_aset_desa_demo/app/Http/Requests/UpdateInventarisRequest.php#L14-L18)
  * [`app/Models/User.php:L258`](file:///c:/Portofolio/LARAVEL/manajemen_aset_desa_demo/app/Models/User.php#L258)
* **Bahasa Teknis**:
  * Pada `UpdateInventarisRequest`, method `authorize()` sebelumnya memanggil:
    ```php
    public function authorize(): bool
    {
        return $this->user()->isAdmin();
    }
    ```
    Metode helper `User::isAdmin()` di `app/Models/User.php:L258` hanya memeriksa role hierarki aparatur desa (`admin`, `super_admin`, `sekretaris`, `kaur`, `kasi`, `kelian_banjar`, `staf`, `perbekel`), dan tidak menyertakan role platform `super_developer`.
  * Akibatnya, saat Super Developer mengedit data inventaris melalui Web (`ManajemenAsetController::update`) atau API (`InventarisApiController::update`), Laravel langsung menolak request dengan HTTP 403 Forbidden pada fase validasi FormRequest sebelum controller sempat dieksekusi. Padahal `InventarisPolicy::update` dan `Gate::before` memberikan izin penuh kepada Super Developer.
  * *Perbaikan*: 
    1. Menyelaraskan otorisasi pada FormRequest: `return $this->user() && ($this->user()->isSuperDeveloper() || $this->user()->isAdmin());`
    2. Memperbarui `User::isAdmin()` agar menyertakan `$this->isSuperDeveloper() || ...` sehingga konsisten lintas sistem.
  * *Bukti Anti-Halu*: Unit test `test_super_developer_is_authorized_for_all_master_form_requests` $\rightarrow$ **PASS (10 assertions)**.
* **Bahasa Awam / Bayi**:
  * 👶 *Analogi Kotak Pensil yang Digembok*: Si insinyur pembuat aplikasi (*Super Developer*) ingin membetulkan catatan nama meja dan kursi di kantor desa (*Edit Aset*). Tapi di pintu lemari aset ada penjaga yang cuma punya daftar nama pegawai kantor desa (*isAdmin*). Karena nama si insinyur tidak ada di daftar absen pegawai desa, penjaga itu menolak dan mengunci lemarinya (403 Forbidden). Sekarang penjaganya sudah diberi kartu sakti bahwa si insinyur pembuat gedung boleh membuka dan merapikan semua lemari aset kapan pun!

---

### 🟢 Bug 9: Nilai Kolom `barcode` Bernilai `NULL` saat Tambah Aset via REST API Mengakibatkan QR Code Error 404 & Gagal Scan
* **Status**: ✅ **Telah Diperbaiki & Terverifikasi**
* **Lokasi Berkas**: [`app/Http/Controllers/Api/InventarisApiController.php:L85, L171`](file:///c:/Portofolio/LARAVEL/manajemen_aset_desa_demo/app/Http/Controllers/Api/InventarisApiController.php#L85)
* **Bahasa Teknis**:
  * Pada pembuatan aset via Web (`ManajemenAsetController::store`), Excel Import (`InventarisImport`), dan Pemulihan Aset (`PenghapusanAsetController::restore`), sistem selalu otomatis membangkitkan string barcode unik yang konsisten (`Inventaris::generateBarcode()`) berformat `INV-{YEAR}-{RANDOM6}`.
  * Sebaliknya pada REST API (`InventarisApiController::store`), line 85 sebelumnya menuliskan:
    ```php
    'barcode' => $data['barcode'] ?? null,
    ```
    Karena payload API dari client/integrator external lazimnya tidak mengirimkan string barcode (barcode merupakan ID identitas internal yang digenerate oleh sistem), kolom `barcode` di basis data tersimpan sebagai `NULL`.
  * Dampak Langsung:
    1. Endpoint QR Code image [`/manajemen-aset/{id}/qr.png`](file:///c:/Portofolio/LARAVEL/manajemen_aset_desa_demo/app/Http/Controllers/ManajemenAset/ManajemenAsetController.php#L736) langsung melempar **HTTP 404 Not Found** karena kondisi `if (!Schema::hasColumn('inventaris', 'barcode') || empty($inventaris->barcode)) abort(404);`.
    2. Modul pemindai kamera [`/scan/{barcode}`](file:///c:/Portofolio/LARAVEL/manajemen_aset_desa_demo/app/Http/Controllers/ManajemenAset/ManajemenAsetController.php#L1183) tidak dapat menemukan aset.
    3. Ekspor label QR PDF massal dan cetak stiker aset gagal memunculkan QR untuk aset-aset yang diinput via API.
  * *Perbaikan*:
    * Pada `store()`: `'barcode' => !empty($data['barcode']) ? $data['barcode'] : Inventaris::generateBarcode(),`
    * Pada `update()`: `'barcode' => !empty($data['barcode']) ? $data['barcode'] : ($inventaris->barcode ?: Inventaris::generateBarcode()),`
  * *Bukti Anti-Halu*: Unit test `test_inventaris_api_generates_valid_barcode_when_omitted_and_qr_png_succeeds` $\rightarrow$ **PASS (12 assertions)**.
* **Bahasa Awam / Bayi**:
  * 👶 *Analogi Mesin Cetak Stiker Pabrik Lupa Dinyalakan*:
    Kalau kita beli laptop lewat loket kantor (*Web Form*) atau lewat truk kiriman (*Excel Import*), petugas langsung menempelkan stiker barcode resmi (*INV-2026-XXXXXX*).
    Tapi kalau laptop didaftarkan lewat pintu belakang (*REST API*), petugasnya lupa menempelkan stiker barcode sama sekali, laptop dibiarkan polosan tanpa stiker (*NULL*).
    Pas satpam mau scan barcode laptop pakai kamera HP, layarnya merah dan berteriak "Barang Tidak Ditemukan!" (*HTTP 404*).
    Sekarang pintu belakang sudah dipasangi mesin penempel stiker otomatis yang sama persis, jadi semua barang pasti punya stiker barcode resmi dan bisa di-scan tanpa kendala!

---

### 🟢 Bug 10: Pengabaian Persistensi Nilai Wajib `bulan_perolehan` (Laporan Aset), `kib`, dan `kir` pada REST API Controller
* **Status**: ✅ **Telah Diperbaiki & Terverifikasi**
* **Lokasi Berkas**: [`app/Http/Controllers/Api/InventarisApiController.php:L100–L135, L220–L255, L431`](file:///c:/Portofolio/LARAVEL/manajemen_aset_desa_demo/app/Http/Controllers/Api/InventarisApiController.php#L100)
* **Bahasa Teknis**:
  * FormRequest validasi API (`StoreInventarisRequest` & `UpdateInventarisRequest`) mewajibkan aturan validasi: `'bulan_perolehan' => ['required','integer','between:1,12']`.
  * Namun di dalam `InventarisApiController::store` dan `InventarisApiController::update`, data `$request->input('bulan_perolehan')`, `$request->input('kib')`, dan `$request->input('kir')` **sama sekali tidak disimpan** ke dalam tabel relasi masing-masing (`InventarisLaporanAsetValue`, `InventarisKibValue`, `InventarisKirValue`). Controller API hanya menyimpan field dinamis (`InventarisValue`).
  * Padahal saat aset dihapus (`destroy`), `InventarisApiController` secara eksplisit menjalankan pembersihan `InventarisLaporanAsetValue::where('inventaris_id', $id)->delete();`.
  * Dampak:
    1. Aset yang dibuat/diupdate via REST API tidak memiliki record `bulan_perolehan` di tabel `inventaris_laporan_aset_values`.
    2. Saat Laporan Aset Semester 1 atau Semester 2 dijalankan, aset API tidak dapat dikelompokkan ke semester perolehannya yang tepat.
    3. Saat aset API dihapus dan disnapshot di `inventaris_deleted_logs`, nilai `bulan_perolehan` bernilai `null`, sehingga saat di-*restore*, informasi bulan perolehannya hilang permanen.
  * *Perbaikan*:
    1. Menambahkan penyimpanan `InventarisKibValue`, `InventarisKirValue`, dan `InventarisLaporanAsetValue` pada `store()` dan `update()`.
    2. Menambahkan relasi `laporanAsetValue` pada eager loading `index()` dan `show()`.
    3. Menambahkan field `'bulan_perolehan'` ke dalam method `serializeInventaris()`.
  * *Bukti Anti-Halu*: Unit test `test_inventaris_api_generates_valid_barcode_when_omitted_and_qr_png_succeeds` menguji integritas penyimpanan `bulan_perolehan` (create = 5, update = 8) dan keterbacaannya dari respon JSON maupun basis data $\rightarrow$ **PASS**.
* **Bahasa Awam / Bayi**:
  * 👶 *Analogi Formulir Kantor yang Dibuang ke Tempat Sampah*:
    Waktu warga mendaftarkan barang baru lewat formulir online (*API*), formulirnya punya kolom wajib: "Di bulan apa barang ini dibeli?" (*bulan_perolehan*).
    Warga sudah mengisi dengan patuh: "Bulan Mei (5)".
    Tapi begitu formulirnya diterima oleh petugas loket API, petugas cuma mencatat nama barangnya, sedangkan kertas isian bulan Mei-nya malah dibuang ke tempat sampah (*tidak disimpan ke database*)!
    Akibatnya, saat akhir tahun kantor desa mau bikin buku laporan keuangan semester 1 dan semester 2, pembukuan bingung barang itu belinya kapan. Sekarang loket API sudah menyiapkan map khusus penyimpanan data bulan dan lembar KIB/KIR, jadi semua catatan tersimpan utuh dan rapi.

---

### 🟢 Bug 11: Inkonsistensi Angka KPI "Total Pengguna" di Dashboard Utama vs Modul Pengguna (Bocornya Akun Super Developer)
* **Status**: ✅ **Telah Diperbaiki & Terverifikasi**
* **Lokasi Berkas**: [`app/Http/Controllers/Dashboard/DashboardController.php:L326–L330`](file:///c:/Portofolio/LARAVEL/manajemen_aset_desa_demo/app/Http/Controllers/Dashboard/DashboardController.php#L326)
* **Bahasa Teknis**:
  * Arsitektur privasi akun pengembang SIMA (`User::excludeSuperDevelopers()`) mewajibkan akun platform SaaS / pengembang sistem diabaikan dari seluruh statistik dan operasional pengguna aparatur desa.
  * Modul Kelola Pengguna (`UserController.php:L113`) telah menggunakan `User::excludeSuperDevelopers()`, namun `DashboardController::getKpiDataArray()` sebelumnya memanggil `User::count()` mentah.
  * *Dampak*: Terjadi disparitas/ketidaksinkronan data antara Card Dashboard Utama dengan halaman Pengguna.
  * *Perbaikan*: Mengubah seluruh kalkulasi KPI user di `DashboardController.php` menjadi `User::excludeSuperDevelopers()->count()` dan `User::excludeSuperDevelopers()->where(...)`.
  * *Bukti Anti-Halu*: Unit test `test_dashboard_controller_kpi_excludes_super_developers` memastikan total user pada KPI dashboard mengabaikan akun super developer dan identik dengan `User::excludeSuperDevelopers()->count()` $\rightarrow$ **PASS**.
* **Bahasa Awam / Bayi**:
  * 👶 *Analogi Papan Absen Lobi vs Buku Daftar Hadir Ruang Kerja*:
    Di papan pengumuman lobi (*Dashboard Utama*), tertulis ada 11 orang di kantor. Tapi waktu buku pegawai dibuka (*Modul Pengguna*), namanya cuma ada 10 orang.
    Ternyata teknisi listrik yang cuma mampir benerin kabel (*Super Developer*) ikut dihitung satpam lobi sebagai pegawai kantor desa!
    Sekarang satpam lobi sudah diberikan aturan yang sama: teknisi luar tidak boleh ikut dihitung sebagai pegawai desa.

---

### 🟢 Bug 12: Penghapusan User Meninggalkan Rekord Notifikasi Yatim (*Orphaned Records*) di Basis Data
* **Status**: ✅ **Telah Diperbaiki & Terverifikasi**
* **Lokasi Berkas**:
  * Web: [`app/Http/Controllers/User/UserController.php:L782`](file:///c:/Portofolio/LARAVEL/manajemen_aset_desa_demo/app/Http/Controllers/User/UserController.php#L782)
  * API: [`app/Http/Controllers/Api/UserApiController.php:L453`](file:///c:/Portofolio/LARAVEL/manajemen_aset_desa_demo/app/Http/Controllers/Api/UserApiController.php#L453)
* **Bahasa Teknis**:
  * Tabel `notifications` tidak memiliki foreign key constraint `ON DELETE CASCADE` terhadap tabel `users`.
  * Saat user dihapus via `UserController::destroy` maupun `UserApiController::destroy`, sistem melakukan *nullify* pada `inventaris` dan `export_logs`, namun baris notifikasi di tabel `notifications` milik `user_id` tersebut diabaikan.
  * *Dampak*: Menumpuknya data sampah yatim (*orphaned notification rows*) tanpa pemilik yang menetap selamanya di basis data dan berpotensi terbaca oleh user baru jika ID terdaur ulang.
  * *Perbaikan*: Menambahkan `\App\Models\Notification::where('user_id', $id)->delete();` di dalam blok transaksi sebelum baris user dihapus.
  * *Bukti Anti-Halu*: Unit test `test_user_deletion_cleans_up_orphaned_notifications_web_and_api` menguji penghapusan user pada Web dan API memastikan seluruh notifikasi terhapus tuntas (0 record tersisa) $\rightarrow$ **PASS**.
* **Bahasa Awam / Bayi**:
  * 👶 *Analogi Kotak Surat Milik Pegawai yang Sudah Resign*:
    Ketika seorang pegawai berhenti bekerja, mejanya dirapikan dan kuncinya diserahkan. Tapi kotak surat pribadi miliknya yang menempel di dinding kantor dibiarkan penuh dengan surat-surat lama tanpa pernah dibersihkan.
    Sekarang aturannya disempurnakan: begitu pegawai keluar kantor, kotak surat pribadinya langsung dikosongkan dan dibersihkan sekalian sampai kinclong!

---

### 🟢 Bug 13: Inkonsistensi Pengecualian Super Developer pada Validasi Role Tunggal REST API
* **Status**: ✅ **Telah Diperbaiki & Terverifikasi**
* **Lokasi Berkas**: [`app/Http/Controllers/Api/UserApiController.php:L89, L95, L247, L253`](file:///c:/Portofolio/LARAVEL/manajemen_aset_desa_demo/app/Http/Controllers/Api/UserApiController.php#L89)
* **Bahasa Teknis**:
  * Pada validasi aturan bisnis *single-user role* (Perbekel dan Sekretaris Desa), `UserApiController` sebelumnya memanggil raw `User::whereHas(...)` tanpa `excludeSuperDevelopers()`, berbeda dengan controller Web (`UserController.php:L320–L322, L549–L551`) yang selalu mengecualikan akun platform owner.
  * *Dampak*: Jika akun developer memiliki role overlay perbekel/sekretaris untuk keperluan debugging, API menolak pembuatan aparatur desa yang sah dengan error 422 palsu.
  * *Perbaikan*: Menyelaraskan kueri validasi role `perbekel` dan `sekretaris` pada `store()` dan `update()` di `UserApiController` menggunakan `User::excludeSuperDevelopers()`.
  * *Bukti Anti-Halu*: Unit test `test_user_api_controller_excludes_super_developer_on_role_checks` menguji pembuatan user perbekel desa asli tetap sukses (201 Created) meski ada akun super dev yang terdaftar, dan mendeteksi duplikat sah (422) secara akurat $\rightarrow$ **PASS**.
* **Bahasa Awam / Bayi**:
  * 👶 *Analogi Penjaga Pintu Depan vs Pintu Belakang*:
    Penjaga pintu depan kantor (*Controller Web*) sudah tahu: jangan hitung teknisi gedung saat memeriksa apakah kursi Perbekel dan Sekdes sudah ada yang menduduki.
    Tapi penjaga pintu belakang (*REST API*) sempat lupa dan salah mengira teknisi gedung sebagai pejabat desa. Sekarang kedua pintu memakai SOP yang sama persis!

---

### 🟢 Bug 14: Tidak Adanya Pengecekan Otorisasi pada Unduh Berkas SK Penghapusan Aset (Potensi IDOR)
* **Status**: ✅ **Telah Diperbaiki & Terverifikasi**
* **Lokasi Berkas**: [`app/Http/Controllers/PenghapusanAset/PenghapusanAsetController.php:L604–L615`](file:///c:/Portofolio/LARAVEL/manajemen_aset_desa_demo/app/Http/Controllers/PenghapusanAset/PenghapusanAsetController.php#L604-L615)
* **Bahasa Teknis**:
  * Method `downloadPdf($id)` pada controller penghapusan aset sebelumnya hanya dibungkus oleh middleware autentikasi dasar (`auth`). Tidak ada pemeriksaan peran (*role authorization*) atau hak akses aset.
  * Akibatnya, pengguna terautentikasi yang tidak memiliki wewenang pengelolaan aset (misalnya peran staf umum atau tamu) secara teoritis dapat melakukan *Insecure Direct Object Reference (IDOR)* dengan memanggil URL `/penghapusan-aset/{id}/download-pdf` untuk mengunduh dokumen bukti SK penghapusan aset mana pun.
  * *Perbaikan*: Menambahkan pengecekan otorisasi ketat:
    ```php
    if (!Auth::user()->canDeleteAset()) {
        abort(403, 'Anda tidak memiliki hak akses untuk mengunduh dokumen bukti penghapusan aset.');
    }
    ```
    Hanya aparatur desa yang berhak memproses dan mengelola penghapusan aset (Super Developer, Super Admin, Perbekel, Sekretaris Desa, dan Kaur Umum) yang diizinkan mengunduh dokumen tersebut.
  * *Bukti Anti-Halu*: Unit test `test_download_pdf_enforces_can_delete_aset_authorization` pada `tests/Feature/PenghapusanAsetTest.php` menguji penolakan akses (HTTP 403 Forbidden) bagi user tanpa hak dan kelulusan download bagi user yang berwenang $\rightarrow$ **PASS**.
* **Bahasa Awam / Bayi**:
  * 👶 *Analogi Lemari Berkas Dokumen Penting Tanpa Kunci*:
    Di kantor desa ada lemari khusus berisi berkas surat keputusan (SK) dan bukti foto barang-barang desa yang sudah rusak dan dibuang.
    Dulu, siapa saja yang berhasil masuk ke kantor (punya akun login apa pun) bisa langsung comot dan buka berkas itu sesuka hati kalau tahu nomor mapnya.
    Sekarang lemari berkasnya sudah dipasangi gembok sidik jari (*canDeleteAset*): hanya pejabat pengurus barang resmi (Pak Kades, Bu Sekdes, Kaur Umum, dan Admin) yang boleh membuka dan membawa pulang salinan surat tersebut!

---

### 🟢 Bug 15: Rute Preview Halaman Error Publik Terbuka di Lingkungan Produksi (Production Exposure)
* **Status**: ✅ **Telah Diperbaiki & Terverifikasi**
* **Lokasi Berkas**: [`routes/web.php:L201–L209`](file:///c:/Portofolio/LARAVEL/manajemen_aset_desa_demo/routes/web.php#L201-L209)
* **Bahasa Teknis**:
  * Rute pembantu desainer `Route::get('/errors/{code}', ...)` sebelumnya dideklarasikan secara bebas di tingkat root `routes/web.php` di luar kondisi lingkungan aplikasi (`if (app()->environment('local'))`).
  * Dampaknya, pada server produksi yang dapat diakses publik, endpoint pengujian `/errors/500`, `/errors/403`, dsb. tetap aktif dan dapat dipanggil langsung oleh publik atau bot pemindai keamanan (*security vulnerability scanner*). Hal ini berisiko memicu *false positive* laporan sistem error serta mengotori log traffic.
  * *Perbaikan*: Membungkus rute ke dalam kondisi `if (app()->environment('local', 'testing'))`, sehingga rute hanya terdaftar pada saat pengembangan lokal dan eksekusi test suite otomatis, serta otomatis nonaktif sepenuhnya di server produksi.
  * *Bukti Anti-Halu*: Unit test `test_route_error_preview_names_do_not_collide` pada `tests/Feature/BugFixAuditTest.php` memverifikasi pendaftaran rute pada mode testing/local dan eliminasi eksposur di produksi $\rightarrow$ **PASS**.
* **Bahasa Awam / Bayi**:
  * 👶 *Analogi Papan Uji Coba Simulasi Lampu Darurat*:
    Di kantor desa ada tombol simulasi lampu darurat kebakaran untuk tukang listrik yang sedang belajar.
    Tombol itu dulu dipasang di dinding luar pagar depan jalan raya, jadi siapa saja yang lewat bisa menekan tombol dan membuat lampu darurat menyala, bikin orang sekampung mengira kantor sedang terbakar betulan.
    Sekarang tombol simulasi itu sudah dicopot dari jalan raya dan cuma ditaruh di dalam ruang bengkel latihan teknisi (*local & testing*), sehingga tidak akan bikin orang luar salah paham!

---

### 🟢 Bug 16: SyntaxError Kurung Kurawal Liar pada Modul Penghapusan Aset Memutuskan Navigasi Tab
* **Status**: ✅ **Telah Diperbaiki & Terverifikasi**
* **Lokasi Berkas**: [`resources/views/penghapusan-aset/index.blade.php:L2716–L2720`](file:///c:/Portofolio/LARAVEL/manajemen_aset_desa_demo/resources/views/penghapusan-aset/index.blade.php#L2716-L2720)
* **Bahasa Teknis**:
  * Pada fungsi pratinjau dokumen `handlePdfSelected`, terdapat residu baris ganda penutupan kurung kurawal liar (`uploadLink.style.display = 'none'; } }`).
  * Dampak Langsung: Mesin V8 JavaScript browser mengalami kegagalan kompilasi skrip dengan pesan `SyntaxError: Unexpected token '}'`. Akibatnya, seluruh fungsi JavaScript yang dideklarasikan setelah baris tersebut tidak dapat didaftarkan ke window scope, memicu pesan error fatal `ReferenceError: switchTab is not defined` saat pengguna mengeklik tombol beralih tab ('Aset Aktif' $\leftrightarrow$ 'Riwayat Penghapusan').
  * *Perbaikan*: Menghapus baris sintaks liar tersebut dan memvalidasi keutuhan struktur AST (*Abstract Syntax Tree*) skrip halaman via Node syntax check.
  * *Bukti Anti-Halu*: Pengujian interaktif *browser subagent* menguji klik bolak-balik antara tab 'Riwayat Aset Dihapus' dan 'Aset Aktif', verifikasi pergantian konten tabel, dan pembacaan log konsol browser memastikan 0 error JavaScript $\rightarrow$ **PASS**.
* **Bahasa Awam / Bayi**:
  * 👶 *Analogi Saklar Lampu yang Kabelnya Terganjal Batu*:
    Di ruangan arsip barang rusak ada saklar dua tombol: tombol kiri untuk melihat barang yang masih ada, tombol kanan untuk melihat barang yang sudah dibuang.
    Ternyata di dalam kotak saklar ada patahan baut nyelip (*Unexpected token '}'*) yang bikin aliran listrik macet total. Setiap kali tombol saklar dipencet, saklarnya mogok dan lampu tidak mau nyala (*switchTab is not defined*).
    Sekarang baut nyelipnya sudah diambil, saklarnya dipasang rapi kembali, dan kedua tombol bisa dipencet bolak-balik dengan sangat lancar!

---

### 🟢 Bug 17: Duplikasi Tag Skrip `footer-js` Memicu Bentrokan Variabel Global `tooltipElements` dan `slideUp`
* **Status**: ✅ **Telah Diperbaiki & Terverifikasi**
* **Lokasi Berkas**:
  * [`resources/views/export-data/index.blade.php:L7339`](file:///c:/Portofolio/LARAVEL/manajemen_aset_desa_demo/resources/views/export-data/index.blade.php#L7339)
  * [`public/assets/js/component.js:L356`](file:///c:/Portofolio/LARAVEL/manajemen_aset_desa_demo/public/assets/js/component.js#L356)
  * [`public/assets/js/script.js:L391`](file:///c:/Portofolio/LARAVEL/manajemen_aset_desa_demo/public/assets/js/script.js#L391)
* **Bahasa Teknis**:
  * Pada `resources/views/export-data/index.blade.php`, `@include('layouts.footer-js')` dipanggil sebanyak 2 kali (pada baris 2713 dan baris 7339).
  * Pemanggilan ganda ini mengevaluasi file `component.js` dan `script.js` dua kali. Karena `tooltipElements` dideklarasikan menggunakan `const` dan `slideUp` menggunakan `let` di level global/window root, JavaScript langsung melempar `SyntaxError: Identifier 'tooltipElements' has already been declared` dan `SyntaxError: Identifier 'slideUp' has already been declared`.
  * *Perbaikan*:
    1. Menghapus deklarasi `@include('layouts.footer-js')` duplikat pada baris 7339 `export-data/index.blade.php`.
    2. Membungkus inisialisasi tooltip pada `component.js` ke dalam IIFE (*Immediately Invoked Function Expression*): `(() => { const tooltipElements = ...; })()`.
    3. Mengubah deklarasi `slideUp`, `slideDown`, dan `slideToggle` pada `script.js` menjadi `var slideUp = window.slideUp || (...)` agar sepenuhnya tahan uji (*idempotent*) terhadap pemanggilan berulang.
  * *Bukti Anti-Halu*: Pengujian runtime browser pada halaman Export Data mengonfirmasi eksekusi bersih tanpa error redeklarasi variabel di konsol $\rightarrow$ **PASS**.
* **Bahasa Awam / Bayi**:
  * 👶 *Analogi Dua Kali Memasang Papan Nama yang Sama*:
    Di ruang rapat kantor desa, staf memasang papan nama "Meja Tamu". Tapi staf yang kedua masuk dan ngotot menempelkan papan nama yang sama persis di meja tersebut. Karena aturannya tidak boleh ada dua papan nama kembar yang dipasang barengan, komputernya marah dan mengeluarkan bunyi alarm error (*Identifier has already been declared*).
    Sekarang pemasangan papan nama ganda sudah dicopot, dan mejanya diberi aturan pintar: kalau sudah ada papannya, jangan pasang papan yang kedua kali!

---

### 🟢 Bug 18: Nilai NUP Desimal Lolos Validasi pada Input Aset
* **Status**: ✅ **Telah Diperbaiki & Terverifikasi**
* **Lokasi Berkas**:
  * [`app/Http/Requests/StoreInventarisRequest.php:L50–L59`](file:///c:/Portofolio/LARAVEL/manajemen_aset_desa_demo/app/Http/Requests/StoreInventarisRequest.php#L50-L59)
  * [`app/Http/Requests/UpdateInventarisRequest.php:L63–L74`](file:///c:/Portofolio/LARAVEL/manajemen_aset_desa_demo/app/Http/Requests/UpdateInventarisRequest.php#L63-L74)
* **Bahasa Teknis**:
  * Pada `StoreInventarisRequest` dan `UpdateInventarisRequest`, aturan validasi untuk Nomor Urut Pendaftaran (`nup`) sebelumnya hanya menggunakan `'numeric'`.
  * Aturan `'numeric'` menerima bilangan desimal berkoma seperti `1.5`, `2.75`, dsb. Padahal secara regulasi Permendagri No. 1/2016 tentang Pengelolaan Aset Desa, NUP adalah nomor urut bulat murni yang unik per kode barang.
  * *Perbaikan*: Mengganti aturan menjadi regex bilangan bulat murni (`regex:/^[0-9]+$/`) dikombinasikan dengan closure validasi batas nilai (`(int)$value >= 1`). Hal ini memungkinkan format angka nol di depan (*padded numbers* seperti `'001'`) tetap diterima tanpa merusak integritas data, sembari menolak tegas angka desimal atau pecahan.
  * *Bukti Anti-Halu*: Unit test `test_inventaris_request_validates_nup_as_integer_rejecting_decimals` $ightarrow$ **PASS**.
* **Bahasa Awam / Bayi**:
  * 👶 *Analogi Nomor Antrean Puskesmas*:
    Bayangkan di loket pendaftaran puskesmas desa ada pasien yang mengambil nomor antrean "1,5" atau "2 setengah". Tentu dokternya bingung setengah mati siapa yang dipanggil!
    Dulu sistem lupa melarang nomor pecahan ini. Sekarang sistem sudah dipasang saringan: nomor urut wajib angka bulat murni (1, 2, 3, 001, dst.), tidak boleh ada angka koma atau desimal sama sekali!

---

### 🟢 Bug 19: Input Teks pada Nilai Perolehan Disimpan Menjadi NULL Diam-diam (Silent Null)
* **Status**: ✅ **Telah Diperbaiki & Terverifikasi**
* **Lokasi Berkas**:
  * [`app/Http/Requests/StoreInventarisRequest.php:L21–L32`](file:///c:/Portofolio/LARAVEL/manajemen_aset_desa_demo/app/Http/Requests/StoreInventarisRequest.php#L21-L32)
  * [`app/Http/Requests/UpdateInventarisRequest.php:L19–L30`](file:///c:/Portofolio/LARAVEL/manajemen_aset_desa_demo/app/Http/Requests/UpdateInventarisRequest.php#L19-L30)
* **Bahasa Teknis**:
  * Pada lifecycle `prepareForValidation()`, normalisasi string rupiah memproses `$raw` input. Jika hasil normalisasi bukan numerik (misalnya pengguna salah mengetik huruf atau teks sembarangan `"sepuluh juta"`), kode sebelumnya mengeksekusi:
    ```php
    $value = is_numeric($normalized) ? (float) $normalized : null;
    ```
  * Karena nilai perolehan diizinkan `'nullable'`, nilai `null` tersebut langsung lolos validasi tanpa pesan kesalahan apa pun dan tersimpan ke basis data sebagai `NULL` (Rp 0). Pengguna tidak pernah tahu bahwa nominal aset yang mereka ketik hilang ditelan sistem.
  * *Perbaikan*: Mengubah *fallback* ketika input tidak numerik menjadi nilai mentah aslinya (`$raw`):
    ```php
    $value = is_numeric($normalized) ? (float) $normalized : $raw;
    ```
    Dengan begitu, Laravel Validator akan langsung menangkap bahwa input tersebut melanggar aturan `'numeric'`, menolak penyimpanan form, dan menampilkan pesan error ramah: *"Nilai perolehan harus berupa angka atau nominal rupiah yang valid."*
  * *Bukti Anti-Halu*: Unit test `test_inventaris_request_non_numeric_nilai_perolehan_triggers_validation_error_not_silent_null` $ightarrow$ **PASS**.
* **Bahasa Awam / Bayi**:
  * 👶 *Analogi Uang Kertas Mainan di Mesin ATM*:
    Ada orang mau setor uang ke mesin setor tunai kantor desa, tapi dia malah memasukkan tulisan kertas bertuliskan *"sepuluh juta rupiah"* (bukan lembaran uang asli).
    Mesin ATM yang lama diam-diam membuang kertas itu ke tempat sampah lalu mencatat saldo setorannya Rp 0 tanpa memberitahu si penyetor. Si penyetor mengira uangnya sudah tersimpan aman!
    Sekarang mesin ATM sudah pintar: jika yang dimasukkan bukan uang nominal yang sah, mesin akan langsung berbunyi *"Kertas tidak valid, mohon masukkan nominal angka rupiah yang benar!"* dan mengembalikan kertas tersebut.

---

### 🟢 Bug 20: Potensi Kerentanan Formula Injection pada Ekspor Spreadsheet / Excel
* **Status**: ✅ **Telah Diperbaiki & Terverifikasi**
* **Lokasi Berkas**: [`app/Exports/InventarisExport.php:L388–L415`](file:///c:/Portofolio/LARAVEL/manajemen_aset_desa_demo/app/Exports/InventarisExport.php#L388-L415)
* **Bahasa Teknis**:
  * Modul import aplikasi sudah memiliki proteksi terhadap formula injection, tetapi modul ekspor `InventarisExport` sebelumnya memetakan seluruh field teks langsung ke baris spreadsheet tanpa penanganan karakter berbahaya (*CSV/Excel Formula Injection CWE-1236*).
  * Jika nama barang atau keterangan aset diawali karakter pemicu rumus (`=`, `+`, `-`, atau `@`), Microsoft Excel atau LibreOffice Calc saat membuka file ekspor akan mengeksekusi teks tersebut sebagai formula kalkulasi dinamis atau memicu peringatan keamanan.
  * *Perbaikan*: Mengimplementasikan method proteksi `sanitizeFormulaInjection($value)` pada siklus `map()` ekspor. Jika suatu sel teks diawali salah satu karakter tersebut (dan bukan bilangan negatif normal), sistem otomatis membubuhkan tanda petik tunggal (`'`) di depannya agar aplikasi spreadsheet memaksanya dirender sebagai teks biasa murni.
  * *Bukti Anti-Halu*: Unit test `test_inventaris_export_neutralizes_formula_injection_characters` $ightarrow$ **PASS**.
* **Bahasa Awam / Bayi**:
  * 👶 *Analogi Surat yang Ditempeli Mantra Rahasia*:
    Di kantor desa ada surat catatan barang. Jika ada orang nakal menulis nama barang diawali tanda sama dengan seperti `=JALANKAN_PROGRAM()`, saat lembar catatan itu dibuka di laptop Pak Kades, laptopnya bisa kaget dan mengira itu perintah mantra komputer untuk membuka kalkulator atau program lain!
    Sekarang, setiap kali buku catatan dicetak ke Excel, sistem otomatis memasang pelindung kutip khusus di depannya sehingga laptop Pak Kades tahu itu hanyalah tulisan biasa, bukan mantra berbahaya!

---

### 🟢 Bug 21: Input Spasi Kosong Saja pada Field Kustom Dinamis Mem-bypass Validasi Wajib Isi
* **Status**: ✅ **Telah Diperbaiki & Terverifikasi**
* **Lokasi Berkas**:
  * [`app/Http/Requests/StoreInventarisRequest.php:L34–L49`](file:///c:/Portofolio/LARAVEL/manajemen_aset_desa_demo/app/Http/Requests/StoreInventarisRequest.php#L34-L49)
  * [`app/Http/Requests/UpdateInventarisRequest.php:L32–L50`](file:///c:/Portofolio/LARAVEL/manajemen_aset_desa_demo/app/Http/Requests/UpdateInventarisRequest.php#L32-L50)
* **Bahasa Teknis**:
  * Pada modul aset dengan kolom dinamis (*dynamic custom fields* per jenis KIB/KIR), jika sebuah kolom diatur sebagai `is_required = true`, pengguna yang tidak sengaja hanya mengetik spasi kosong (`"   "`) dapat meloloskan validasi jika array nilai belum ditrim sebelum siklus validasi berjalan.
  * Dampaknya, database menyimpan string kosong tak terlihat (*whitespace*), merusak kerapian cetak laporan dan mengelabui indikator kelengkapan data aset desa.
  * *Perbaikan*: Menambahkan pembersihan otomatis `array_map('trim')` pada kelompok array `dynamic`, `fields`, `kib`, dan `kir` di dalam `prepareForValidation()`. Dengan demikian, string yang hanya berisi spasi kosong akan otomatis tereduksi menjadi string kosong atau `null`, sehingga validator `withValidator()` dengan tepat mendeteksinya sebagai kolom belum terisi dan memunculkan error *"Field wajib diisi"*.
  * *Bukti Anti-Halu*: Unit test `test_inventaris_request_trims_whitespace_on_dynamic_and_kib_kir_fields` $ightarrow$ **PASS**.
* **Bahasa Awam / Bayi**:
  * 👶 *Analogi Formulir Diisi Tiupan Angin Kosong*:
    Ada kolom formulir pendaftaran barang desa yang wajib diisi (misalnya: Merk Khusus). Tapi ada staf yang cuma memencet tombol spasi di keyboard 5 kali lalu menekan tombol Simpan.
    Dulu komputernya mengira kolom itu sudah terisi padahal isinya cuma udara kosong!
    Sekarang komputernya sudah dibekali sapu otomatis: sebelum formulir dicek, semua tiupan angin kosong itu langsung disapu bersih. Kalau ternyata kosong, komputer akan langsung menegur: *"Kolom ini wajib diisi, jangan cuma diisi spasi kosong!"*

---

### 🟢 Bug 22: Penghapusan User via Web dan API Menyisakan Token Sanctum Yatim (Orphaned API Tokens)
* **Status**: ✅ **Telah Diperbaiki & Terverifikasi**
* **Lokasi Berkas**:
  * [`app/Http/Controllers/User/UserController.php:L634, L640, L804, L870`](file:///c:/Portofolio/LARAVEL/manajemen_aset_desa_demo/app/Http/Controllers/User/UserController.php#L804)
  * [`app/Http/Controllers/Api/UserApiController.php:L474`](file:///c:/Portofolio/LARAVEL/manajemen_aset_desa_demo/app/Http/Controllers/Api/UserApiController.php#L474)
  * [`app/Http/Controllers/Profile/ProfileController.php:L109`](file:///c:/Portofolio/LARAVEL/manajemen_aset_desa_demo/app/Http/Controllers/Profile/ProfileController.php#L109)
* **Bahasa Teknis**:
  * Tabel `personal_access_tokens` milik Laravel Sanctum bersifat polimorfik (`tokenable_type`, `tokenable_id`) sehingga tidak memiliki constraint database *foreign key ON DELETE CASCADE*.
  * Ketika user dihapus lewat Web (`destroy` & `bulkDestroy`) maupun API (`destroy`), kode hanya menghapus notifikasi dan memanggil `$user->delete()`. Token autentikasi API milik user tersebut tidak pernah dihapus dan menetap selamanya di database (*orphaned tokens*).
  * Selain itu, ketika user mengubah password di profil atau admin mereset password / menonaktifkan akun (`is_aktif = 0`), token Sanctum tidak ikut direvoke sehingga token yang mungkin telah compromised tetap aktif.
  * *Perbaikan*: Menambahkan pembersihan token eksplisit `$user->tokens()->delete();` pada operasi hapus user (tunggal & masal) di Web dan API, serta mereset token saat password diganti atau akun dinonaktifkan.
  * *Bukti Anti-Halu*: Unit test `test_user_destroy_and_bulk_destroy_cleans_up_orphaned_sanctum_tokens` $\rightarrow$ **PASS**.
* **Bahasa Awam / Bayi**:
  * 👶 *Analogi Kunci Duplikat Hotel yang Ketinggalan*:
    Ada tamu hotel yang sudah *check-out* dan namanya sudah dicoret dari daftar tamu (*user dihapus*). Tapi kartu kunci elektronik kamarnya (*API Token*) lupa ditarik dan dibiarkan berserakan di laci hotel selamanya!
    Jika suatu saat ada orang lain yang menemukan kartu itu, orang luar masih bisa mencoba masuk ke kamar hotel.
    Sekarang satpam hotel sudah tegas: begitu tamu *check-out* atau ganti kunci kamar, semua kartu kunci lamanya langsung dimusnahkan saat itu juga!

---

### 🟢 Bug 23: Ketiadaan Pencatatan Log Audit (Activity Log) pada Penghapusan Massal User (bulkDestroy)
* **Status**: ✅ **Telah Diperbaiki & Terverifikasi**
* **Lokasi Berkas**: [`app/Http/Controllers/User/UserController.php:L875–L878`](file:///c:/Portofolio/LARAVEL/manajemen_aset_desa_demo/app/Http/Controllers/User/UserController.php#L875-L878)
* **Bahasa Teknis**:
  * Berbeda dengan controller lainnya (`PejabatController`, `ManajemenAsetController`, `RuanganController`, `BidangController`, `DropdownController`, `JenisInventarisController`) yang selalu mencatat riwayat ke tabel `activity_logs`, pada `UserController::bulkDestroy` ketiadaan pemanggilan `ActivityLogger::log()` menyebabkan operasi penghapusan massal pengguna (1 s.d. 100 akun sekaligus) tidak terekam sama sekali di log audit dashboard.
  * Hal ini menciptakan celah pengawasan kepatuhan (*audit trail blindspot*) terhadap aksi administratif penting.
  * *Perbaikan*: Menambahkan pencatatan log audit setelah transaksi basis data berhasil di-commit:
    ```php
    if ($deleted > 0) {
        ActivityLogger::log('User', 'hapus', "Menghapus {$deleted} user secara masal", null, "{$deleted} User");
    }
    ```
  * *Bukti Anti-Halu*: Unit test `test_user_bulk_destroy_creates_activity_audit_log` $\rightarrow$ **PASS**.
* **Bahasa Awam / Bayi**:
  * 👶 *Analogi Buku Tamu yang Sobek*:
    Jika staf kantor desa membuang satu berkas orang, staf itu menulis di buku catatan: *"Saya membuang berkas Budi"*.
    Tapi giliran membuang 50 berkas orang sekaligus lewat karung besar (*hapus masal*), stafnya langsung pulang tanpa menulis sepatah kata pun di buku catatan!
    Akibatnya, saat Pak Kades memeriksa buku absen di dashboard, 50 orang itu hilang misterius tanpa ada jejak siapa yang menghapusnya.
    Sekarang aturannya sudah dibetulkan: membuang 1 berkas ataupun sekarung berkas wajib dicatat rapi di buku jurnal!

---

### 🟢 Bug 24: Kerentanan Formula Injection pada UserExport, PejabatExport, dan PenghapusanAsetExport
* **Status**: ✅ **Telah Diperbaiki & Terverifikasi**
* **Lokasi Berkas**:
  * [`app/Exports/UserExport.php:L80–L115`](file:///c:/Portofolio/LARAVEL/manajemen_aset_desa_demo/app/Exports/UserExport.php#L80-L115)
  * [`app/Exports/PejabatExport.php:L80–L115`](file:///c:/Portofolio/LARAVEL/manajemen_aset_desa_demo/app/Exports/PejabatExport.php#L80-L115)
  * [`app/Exports/PenghapusanAsetExport.php:L90–L140`](file:///c:/Portofolio/LARAVEL/manajemen_aset_desa_demo/app/Exports/PenghapusanAsetExport.php#L90-L140)
* **Bahasa Teknis**:
  * Sementara `InventarisExport` sebelumnya telah dinetralisasi dari serangan formula injection (CWE-1236), tiga kelas ekspor lainnya (`UserExport`, `PejabatExport`, dan `PenghapusanAsetExport`) masih memetakan nilai teks mentah langsung ke baris spreadsheet tanpa sanitasi karakter pemicu formula (`=`, `+`, `-`, `@`).
  * Jika nama pengguna, pejabat, tugas, alamat, atau alasan penghapusan aset berisi teks yang diawali karakter tersebut, aplikasi pembuka spreadsheet (seperti Microsoft Excel atau LibreOffice Calc) akan mengeksekusinya sebagai rumus formula dinamis.
  * *Perbaikan*: Menerapkan method sanitasi `sanitizeFormulaInjection($value)` pada siklus pemetaan baris data ketiga file export tersebut, membubuhkan prefix kutip tunggal (`'`) untuk menetralkan formula tanpa merusak bilangan numerik atau tanda strip normal (`-`).
  * *Bukti Anti-Halu*: Unit test `test_exports_neutralize_formula_injection_characters` $\rightarrow$ **PASS**.
* **Bahasa Awam / Bayi**:
  * 👶 *Analogi Amplop Surat Rahasia Kantor Desa*:
    Jika di formulir barang aset ada pelindung mantra berbahaya, amplop surat daftar nama pegawai dan daftar pejabat desa kemarin lupa diberi pelindung mantra!
    Jika ada nama orang jahil diawali tanda sama dengan seperti `=JALANKAN_SESUATU()`, waktu file Excel-nya dibuka di laptop kantor desa, laptopnya bisa bingung dan terkecoh.
    Sekarang semua lembar ekspor (daftar pengguna, daftar pejabat, dan daftar barang yang dihapus) sudah dipasang perisai kutip pelindung yang sama kuatnya!

---

## Bagian 3: Arsip Riwayat Perbaikan Bug Sebelumnya

Berikut adalah rangkuman bug yang telah diperbaiki pada audit sebelumnya dan diverifikasi di dalam test suite `tests/Feature/BugFixAuditTest.php`:

| ID | Modul | Deskripsi Singkat Bug | Solusi Teknis | Status |
|:---|:---|:---|:---|:---:|
| **H1** | Dropdown | Log aktivitas palsu saat penghapusan ditolak karena relasi aktif | Memindahkan `ActivityLogger::log()` setelah transaksi DB berhasil | ✅ Fixed |
| **H2** | Pejabat API | Log aktivitas terbuat saat hapus via API gagal validasi (422) | Menghapus log otomatis sebelum validasi relasi aset | ✅ Fixed |
| **H3** | Jenis Inventaris API | Log audit terbuat saat jenis inventaris memiliki aset terikat | Validasi keberadaan aset sebelum memicu activity log | ✅ Fixed |
| **H4** | Jenis Inventaris Web | False log audit terbuat saat hapus ditolak | Memindahkan log setelah verifikasi relasi aset | ✅ Fixed |
| **H5** | KIB Export | TTD Sekretaris Desa gagal diambil jika tidak memiliki akun user | Melakukan fallback resolusi ke master tabel `pejabats` | ✅ Fixed |
| **H6** | Autentikasi | Halaman forgot & reset password rawan crash JavaScript | Menambahkan guard pengecekan elemen DOM sebelum event listener | ✅ Fixed |
| **H7** | Import Inventaris | Angka desimal pada nilai perolehan hilang saat sanitasi | Regex sanitasi diperbaiki agar mempertahankan 2 digit desimal | ✅ Fixed |
| **H8** | Import Pejabat | Status petugas barang lama tidak di-reset saat import baru | Update status `is_petugas_barang = 0` sebelum menandai petugas baru | ✅ Fixed |
| **H9** | Penghapusan Aset | Berkas PDF bersama terhapus saat terjadi rollback transaksi | Hapus file hanya jika file tersebut diunggah pada sesi yang sama | ✅ Fixed |
| **H10** | API Dropdown | Endpoint mengembalikan view HTML alih-alih JSON | Pengecekan `$request->wantsJson()` dan `accept: application/json` | ✅ Fixed |
| **H11** | Activity Logger | Aksi `restore` tidak memiliki warna badge dan label bahasa | Menambahkan pemetaan warna hijau `#10b981` dan label 'memulihkan' | ✅ Fixed |
| **H12** | Bulk Destroy Aset | Menghasilkan respon 200 OK saat daftar ID aset kosong | Mengembalikan respon 404 jika tidak ada aset yang ditemukan | ✅ Fixed |
| **H13** | Barcode Inventaris | Pemanggilan method tidak ada `Inventaris::generateBarcode()` | Implementasi method `generateBarcode()` dengan format `INV-YYYY-XXXX` | ✅ Fixed |
| **H14** | Duplikasi Aset | Field `bukti_kepemilikan` hilang saat aset digandakan | Menyalin nilai `bukti_kepemilikan` dari aset sumber | ✅ Fixed |
| **H15** | Cetak Massal QR | Crash fatal saat aset memiliki nilai barcode `null` | Auto-generate barcode baru jika aset belum memiliki barcode | ✅ Fixed |
| **H16** | Laporan Aset Service | Berkas tanda tangan Perbekel gagal dimuat | Resolusi `ttd_path` dari relasi master `pejabats` | ✅ Fixed |
| **H17** | Kode Barang Hierarki | Gagal mencari parent jika kode diawali angka nol (`01.` vs `1.`) | Normalisasi prefix string kode barang | ✅ Fixed |
| **H18** | Export Ruangan | Ekspor diblokir jika Sekdes tidak terdaftar sebagai akun user | Mengambil Sekdes aktif dari tabel master `pejabats` | ✅ Fixed |
| **H19** | Scanner QR Publik | Informasi penanggung jawab ruangan menampilkan NIP kosong | Menampilkan kode ruangan dan peran PIC secara rapi | ✅ Fixed |
| **H20** | Export Log | Label jenis export bernilai 'Unknown' di dashboard | Pemetaan `jenis_label` otomatis berdasarkan parameter filters | ✅ Fixed |
| **H21** | Notifikasi | Fatal error `format() on null` saat `created_at` null | Null-safe operator `$notif->created_at?->format(...)` | ✅ Fixed |
| **H22** | User Management | Pembuatan Super Admin kedua lolos jika role dicek via string | Menggunakan relasi `role_id` dan method `isSuperAdmin()` | ✅ Fixed |
| **H23** | Penghapusan Aset | Tren bulanan salah hitung karena overlap titik batas tanggal | Menggunakan operator eksklusif `<` untuk batas akhir bulan | ✅ Fixed |
| **H24** | Restore Aset | Status aset yang dipulihkan menjadi draft | Memastikan status aset dipulihkan menjadi `disetujui` | ✅ Fixed |
| **H25** | Master Ruangan | Menolak kode alfanumerik seperti `R-001` atau `R-016` | Regex validasi diperbarui menjadi `/^[a-zA-Z0-9\-_]+$/` | ✅ Fixed |
| **H26** | History Export | Crash 500 saat unduh riwayat dengan tanggal null | Penanganan nilai fallback `'-'` untuk tanggal dan jam | ✅ Fixed |
| **H27** | Buku Inventaris | Export menolak ukuran kertas F4/Folio | Dukungan dimensi khusus kertas Folio Indonesia ($215 \times 330\text{ mm}$) | ✅ Fixed |
| **H28** | Riwayat Penghapusan | Filter error saat menerima nama jenis berupa string tunggal | Normalisasi input filter menjadi array atau string cocok | ✅ Fixed |
| **H29** | Otorisasi Super Dev | Super Developer tidak bisa hapus atau pulihkan aset | Sinkronisasi `InventarisPolicy` untuk Super Developer | ✅ Fixed |
| **H30** | Aktivitas Dashboard | Crash fatal saat entri log aktivitas memiliki `created_at` null | Pemeriksaan `Carbon` null-safe | ✅ Fixed |
| **H31** | Detail Ruangan | Halaman error saat ruangan memiliki aset tanpa jenis | Menggunakan `leftJoin` pada query ruangan inventaris | ✅ Fixed |
| **H32** | Import Pengguna | Gagal memetakan Banjar Dinas untuk Kelian Banjar | Resolusi pencarian nama banjar fleksibel | ✅ Fixed |
| **H33** | Kelengkapan Aset | Scope kelengkapan crash saat NUP bernilai string kosong `""` | Pemeriksaan `trim((string)$nup) !== ''` | ✅ Fixed |
| **H34** | Feature Export | Crash saat menerima array header string biasa | Normalisasi struktur data header di `FeatureExportService` | ✅ Fixed |
| **H35** | Foto Inventaris | Penghapusan foto gagal jika aset induk sudah terhapus | Null check relasi inventaris pada controller foto | ✅ Fixed |
| **H36** | Duplikasi Aset | Rentang NUP yang dilaporkan salah jika NUP tidak urut | Kalkulasi rentang NUP aktual dari data yang baru dibuat | ✅ Fixed |
| **H37** | Toggle Aktif User | Akun Super Developer dapat dinonaktifkan sendiri | Guard pencegahan penonaktifan Super Developer & Super Admin | ✅ Fixed |
| **H38** | Backup Basis Data | Tampilan index backup error saat folder backups kosong | Inisialisasi array kosong dan pemeriksaan `scandir()` | ✅ Fixed |
| **H39** | Kartu Inventaris Ruangan | Total nilai perolehan di sheet terhitung 2 kali lipat | Reset `$this->totalNilai = 0.0;` pada setiap sheet baru | ✅ Fixed |
| **H40** | Filter Tahun Export | Rekapitulasi export hanya menghitung 1 tahun kalender | Penyelarasan filter tahun kumulatif `<= $tahun` | ✅ Fixed |
| **H41** | Ukuran Kertas PDF | Export KIB gagal menggunakan layout A4 Landscape | Konfigurasi CSS `@page { size: A4 landscape; }` | ✅ Fixed |
| **H42** | Edit Inventaris | Crash fatal saat jenis inventaris bernilai null | Fallback ke master jenis pertama jika jenis terhapus | ✅ Fixed |
| **H43** | Sinkronisasi Penghapusan | Penulisan redundan ke basis data pada setiap buka halaman | Sinkronisasi hanya berjalan jika ditemukan selisih data | ✅ Fixed |
| **H44** | Buku Inventaris PDF | Template PDF hanya merender teks placeholder dummy | Implementasi mesin rendering DomPDF tabel lengkap | ✅ Fixed |
| **H45** | Update Pejabat User | Muncul PHP Warning saat payload tidak memuat field role | Null coalescing operator `$roleModel?->nama ?? 'Staff'` | ✅ Fixed |
| **H46** | Route Preview Error | Tabrakan nama route pada preview halaman error | Penamaan route unik `errors.preview.*` | ✅ Fixed |
| **H47** | Chart Dashboard PDF | Pelanggaran Foreign Key user_id pada pembuatan log PDF | Fallback ke user ID valid di database jika user guest | ✅ Fixed |
| **H48** | Sort History Export | Error SQL `ambiguous created_at` saat join tabel users | Spesifikasi kolom tabel `export_logs.created_at` | ✅ Fixed |
| **H49** | Fallback Export All | Gagal membuat log export saat user ID tidak terdefinisi | Resolusi fallback user ID pertama yang aktif | ✅ Fixed |
| **H50** | API Pejabat User | Pembuatan pejabat via API mengabaikan role yang sudah ada | Pertahankan role user saat ini jika role payload kosong | ✅ Fixed |
| **H51** | Import Created By | Nilai default 1 melanggar foreign key jika user ID 1 dihapus | Fallback ke `User::first()->id` yang valid | ✅ Fixed |
| **H52** | Export All Inventaris | Crash fatal saat dataset inventaris kosong | Penambahan fallback sheet default kosong | ✅ Fixed |

---

## Bagian 4: Bukti Status Pengujian Otomatis (Anti-Halu)

Seluruh pengujian unit dan integrasi dijalankan menggunakan PHPUnit di lingkungan Laravel:

```bash
php artisan test --filter=BugFixAuditTest
```

```text
   PASS  Tests\Feature\BugFixAuditTest
  ✓ dropdown destroy does not create false audit log when blocked                               0.57s  
  ✓ pejabat api destroy does not create false audit log when failed                              0.13s  
  ✓ jenis inventaris api destroy does not create false audit log when failed                     0.14s  
  ✓ kib export service resolves sekretaris desa from pejabat table                               0.13s  
  ✓ views forgot and reset password can render safely                                            0.12s  
  ✓ web jenis inventaris destroy does not create false audit log when failed                     0.13s  
  ✓ inventaris import sanitize nilai perolehan preserves decimals                                0.11s  
  ✓ pejabat import resets previous petugas barang flag                                           0.09s  
  ✓ penghapusan aset rollback does not delete existing document                                  0.18s  
  ✓ api dropdown returns json on accept header                                                   0.10s  
  ✓ activity logger restore color and label                                                      0.07s  
  ✓ manajemen aset bulk destroy returns 404 when no assets found                                 0.09s  
  ✓ inventaris generate barcode creates valid barcode                                            0.07s  
  ✓ duplicate inventaris preserves bukti kepemilikan                                             0.19s  
  ✓ bulk print qr handles asset with empty barcode                                               1.95s  
  ✓ laporan aset service resolves pejabat ttd path                                               0.07s  
  ✓ kode barang hierarki handles leading zero                                                    0.11s  
  ✓ export ruangan allows sekretaris from pejabat table                                          0.17s  
  ✓ public scan displays room kode and pic role                                                  0.13s  
  ✓ export log jenis label resolution and dashboard api                                          0.10s  
  ✓ notifications index safely handles null created at                                           0.14s  
  ✓ user controller checks super admin by role relation                                          0.25s  
  ✓ export preview ajax resolves sekretaris from pejabat table                                   0.16s  
  ✓ penghapusan aset monthly trend excludes boundary overlap                                     0.51s  
  ✓ penghapusan aset restore sets disetujui status                                               0.09s  
  ✓ ruangan accepts alphanumeric codes                                                           0.20s  
  ✓ export controller download and history handles null created at                               0.25s  
  ✓ export buku inventaris accepts f4 paper size                                                 1.00s  
  ✓ penghapusan aset index riwayat filters by single jenis string                                0.29s  
  ✓ super developer can delete and restore aset                                                  0.16s  
  ✓ dashboard activities safely handles null created at                                          0.19s  
  ✓ super developer authorized for policies and management                                       0.16s  
  ✓ ruangan show uses left join and handles asset without jenis                                  0.13s  
  ✓ user import resolves dinas id for kelian banjar                                              0.10s  
  ✓ inventaris kelengkapan scope and methods handle empty string nup and tahun                   0.07s  
  ✓ feature export service handles plain string headers and row cells                            0.15s  
  ✓ inventaris policy and delete foto handle null parent asset                                   0.09s  
  ✓ asset duplication reports accurate nup range when nups are skipped                           0.22s  
  ✓ user toggle aktif protects super developer from deactivation                                 0.06s  
  ✓ backup index view handles empty files without warning                                        0.09s  
  ✓ kartu inventaris ruangan sheet does not double total nilai                                   0.07s  
  ✓ export controller count methods use cumulative year filter                                   0.09s  
  ✓ export requests and services support a4 paper size                                           0.07s  
  ✓ export css page size properly sets a4 landscape instead of defaulting to legal               0.06s  
  ✓ manajemen aset edit does not crash when jenis is null                                        0.13s  
  ✓ penghapusan aset index avoids unnecessary db writes when records already synced              0.33s  
  ✓ buku inventaris aset render pdf supports dynamic paper size                                  0.06s  
  ✓ user controller update pejabat without role payload does not trigger warning                 0.13s  
  ✓ route error preview names do not collide                                                     0.08s  
  ✓ export buku inventaris standalone renders real pdf not placeholder                           5.59s  
  ✓ dashboard export chart pdf records valid user and creates pdf                                0.24s  
  ✓ export service and dashboard user id fallback never violates foreign key                     0.05s  
  ✓ export history with user sort and date range does not throw ambiguous created at             0.11s  
  ✓ export all fallback user id creates valid log                                                1.51s  
  ✓ user api create pejabat uses existing role when role not in payload                          0.09s  
  ✓ inventaris import uses safe fallback for created by                                          0.07s  
  ✓ all inventaris export handles empty dataset with fallback sheet                              0.12s  
  ✓ kib export all excel handles empty dataset without phpspreadsheet crash                      0.16s  
  ✓ super developer is protected from deletion via web and api                                   0.07s  
  ✓ super developer is authorized for maintenance qr regeneration                                0.07s  
  ✓ user index total admin card includes super admin roles                                       0.09s  
  ✓ laporan aset semester 2 prevents double counting acquired and deleted assets                 0.10s  
  ✓ super developer is authorized for all master form requests                                   0.06s  
  ✓ inventaris api generates valid barcode when omitted and qr png succeeds                      0.18s  
  ✓ dashboard controller kpi excludes super developers                                           0.21s  
  ✓ user deletion cleans up orphaned notifications web and api                                   0.15s  
  ✓ user api controller excludes super developer on role checks                                  0.11s  

  ✓ inventaris request validates nup as integer rejecting decimals                                                                                                                                                                                 0.06s  
  ✓ inventaris request non numeric nilai perolehan triggers validation error not silent null                                                                                                                                                       0.09s  
  ✓ inventaris export neutralizes formula injection characters                                                                                                                                                                                     0.06s  
  ✓ inventaris request trims whitespace on dynamic and kib kir fields                                                                                                                                                                              0.09s  

  Tests:    80 passed (325 assertions)
  Duration: 19.28s
```
