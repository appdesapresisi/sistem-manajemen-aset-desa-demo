# PANDUAN LENGKAP PENGUJIAN BLACK BOX (BLACK BOX TESTING GUIDE)
## SISTEM MANAJEMEN ASET DESA (SIMA DESA DEMO)

> **Dokumen Pengujian Fungsional Mandiri (Self-Verification Matrix)**  
> **Target Pengguna**: Quality Assurance (QA), Tester, Developer, Administrator Sistem  
> **Versi Dokumen**: v1.0.0  
> **Tanggal Rilis**: 2026-10-21  

---

## 1. PENDAHULUAN & PETUNJUK PENGGUNAAN

### 1.1 Tujuan Dokumen
Dokumen ini disusun sebagai panduan operasional pengujian *Black Box* (Black Box Testing) menyeluruh pada **Sistem Manajemen Aset Desa**. Pengujian berfokus pada fungsionalitas antarmuka (*front-end*), logika bisnis (*business rules*), penanganan kesalahan (*error handling* & validasi input), serta alur positif (*positive flow*) dan negatif (*negative flow*) di seluruh modul aplikasi.

### 1.2 Cara Melakukan Pengujian
1. Gunakan checkbox `[ ]` di setiap test case untuk menandai progres pengujian Anda.
   - Tandai `[x] Lolos` jika respon sistem sesuai dengan kolom **Respon yang Diharapkan (Expected Result)**.
   - Berikan catatan jika terdapat bug/ketidaksesuaian tampilan pada kolom **Status / Catatan**.
2. Akun Uji Standar:
   - **Super Admin**: Username `superadmin` | Password `password` (Akses Penuh)
   - **Admin Aset**: Username `admin` | Password `password`
   - **Operator**: Username `operator` | Password `password`
   - **Viewer**: Username `viewer` | Password `password` (Hanya Baca)

---

## DAFTAR ISI MODUL PENGUJIAN
1. [Modul 1: Autentikasi & Hak Akses (Login, Session, Logout)](#modul-1-autentikasi--hak-akses)
2. [Modul 2: Profil Pengguna & Ganti Password](#modul-2-profil-pengguna--ganti-password)
3. [Modul 3: Dashboard & Log Aktivitas Sistem](#modul-3-dashboard--log-aktivitas-sistem)
4. [Modul 4: Buku Inventaris (Manajemen Aset)](#modul-4-buku-inventaris-manajemen-aset)
5. [Modul 5: Penghapusan Aset (Mutasi Berkurang)](#modul-5-penghapusan-aset-mutasi-berkurang)
6. [Modul 6: Pusat Ekspor Data (Laporan Resmi Desa)](#modul-6-pusat-ekspor-data-laporan-resmi-desa)
7. [Modul 7: Master Data - Ruangan](#modul-7-master-data---ruangan)
8. [Modul 8: Master Data - Pejabat Penandatangan](#modul-8-master-data---pejabat-penandatangan)
9. [Modul 9: Master Data - Manajemen Pengguna (Users)](#modul-9-master-data---manajemen-pengguna-users)
10. [Modul 10: Master Data - Bidang](#modul-10-master-data---bidang)
11. [Modul 11: Master Data - Jenis Inventaris & Custom Field Builder](#modul-11-master-data---jenis-inventaris--custom-field-builder)
12. [Modul 12: Master Data - Dropdown Dinamis](#modul-12-master-data---dropdown-dinamis)
13. [Modul 13: Sistem Notifikasi](#modul-13-sistem-notifikasi)
14. [Modul 14: Cadangan Basis Data (Database Backup)](#modul-14-cadangan-basis-data-database-backup)
15. [Modul 15: Fitur Publik (Scan Barcode / QR Code Publik)](#modul-15-fitur-publik-scan-barcode--qr-code-publik)
16. [Modul 16: Error Handling & Keamanan Sistem (403, 404, 419, XSS Protection)](#modul-16-error-handling--keamanan-sistem)

---

## MODUL 1: AUTENTIKASI & HAK AKSES

| ID Test Case | Fitur / Komponen | Skenario Uji | Data Masukan (Input) | Respon yang Diharapkan (Expected Result) | Status |
| :--- | :--- | :--- | :--- | :--- | :--- |
| **TC-AUTH-001** | Form Login | Submit form dengan field kosong | Username: *(Kosong)*<br>Password: *(Kosong)* | Form tidak tersubmit; browser menampilkan tooltip HTML5 "*Please fill out this field*" atau validasi error di bawah input. | [ ] |
| **TC-AUTH-002** | Form Login | Login dengan username yang tidak terdaftar | Username: `userngawur`<br>Password: `password` | Muncul alert/pesan error berwarna merah: "*Kredensial tersebut tidak cocok dengan data kami.*" Pengguna tetap di halaman login. | [ ] |
| **TC-AUTH-003** | Form Login | Login dengan password salah | Username: `superadmin`<br>Password: `passwordsalah123` | Muncul pesan error kredensial tidak cocok. Field password otomatis dikosongkan. | [ ] |
| **TC-AUTH-004** | Form Login | Proteksi Brute-force (Rate Limiting) | Input password salah sebanyak 5 kali berturut-turut | Sistem mengunci form login sementara (HTTP 429) dengan pesan: "*Terlalu banyak upaya masuk. Silakan coba lagi dalam X detik.*" | [ ] |
| **TC-AUTH-005** | Form Login | Toggle lihat password (Show/Hide) | Klik ikon mata di sisi kanan field password | Karakter password berganti tipe dari dot pelindung (`type="password"`) menjadi teks terlihat (`type="text"`), dan sebaliknya saat diklik ulang. | [ ] |
| **TC-AUTH-006** | Form Login | Login berhasil (Positive Case) | Username: `superadmin`<br>Password: `password` | Berhasil login, dialihkan (*redirect*) ke halaman `/dashboard`, muncul toast selamat datang / nama user aktif di header. | [ ] |
| **TC-AUTH-007** | Session Proteksi | Akses URL dilindungi tanpa login | Buka tab Incognito baru, ketik URL `http://127.0.0.1:8000/dashboard` | Sistem otomatis menolak akses dan mengalihkan ke `http://127.0.0.1:8000/login`. | [ ] |
| **TC-AUTH-008** | Tombol Logout | Logout dari sistem | Klik avatar di pojok kanan atas > Klik tombol **Logout** | Sesi pengguna dihapus dari server, dialihkan kembali ke halaman `/login`. Menekan tombol *Back* pada browser tidak membuka kembali dashboard tanpa login ulang. | [ ] |

---

## MODUL 2: PROFIL PENGGUNA & GANTI PASSWORD

| ID Test Case | Fitur / Komponen | Skenario Uji | Data Masukan (Input) | Respon yang Diharapkan (Expected Result) | Status |
| :--- | :--- | :--- | :--- | :--- | :--- |
| **TC-PROF-001** | Edit Profil | Nama lengkap dikosongkan | Nama: *(Kosong)* | Sistem memvalidasi field wajib diisi: "*Nama lengkap wajib diisi*". Form tidak tersimpan. | [ ] |
| **TC-PROF-001b** | Edit Profil | Nama lengkap hanya berisi angka | Nama: `12345678` | HTML5 pattern / validasi error menolak: "*Nama lengkap hanya boleh berisi huruf dan tanda baca nama (tidak boleh mengandung angka).*" | [ ] |
| **TC-PROF-002** | Edit Profil | Format email salah | Email: `bukan-email-valid` | Sistem menampilkan pesan validasi: "*Format email tidak valid*". Border input berwarna merah. | [ ] |
| **TC-PROF-003** | Edit Profil | Username duplikat dengan akun lain | Username: *(isi username user lain yang sudah ada)* | Muncul validasi: "*Username sudah digunakan oleh pengguna lain*". | [ ] |
| **TC-PROF-004** | Edit Profil | Update profil valid (Positive Case) | Nama: `Super Admin Demo`<br>Email: `admin@demo.desa.id`<br>No Telp: `08123456789` | Data berhasil diupdate, muncul notifikasi sukses, nama di pojok kanan atas berubah secara realtime. | [ ] |
| **TC-PROF-004b** | Edit Profil | Edit profil pengguna yang terdaftar sebagai pejabat desa | User ber-role Perbekel/Kaur mengubah nama lengkap dari `I Wayan Lama` menjadi `I Wayan Baru` | Nama di tabel `users` dan tabel `pejabats` keduanya tersinkronisasi menjadi `I Wayan Baru` secara otomatis; dokumen ekspor resmi menampilkan nama baru. | [ ] |
| **TC-PASS-001** | Ganti Password | Password saat ini salah | Password Saat Ini: `salahrahasia`<br>Password Baru: `Admin1234!`<br>Konfirmasi: `Admin1234!` | Muncul pesan error merah: "*Password saat ini tidak sesuai*". Password tidak berubah. | [ ] |
| **TC-PASS-002** | Ganti Password | Password baru kurang dari 8 karakter | Password Saat Ini: `password`<br>Password Baru: `12345` (5 karakter)<br>Konfirmasi: `12345` | Muncul validasi: "*Password baru minimal terdiri dari 8 karakter*". | [ ] |
| **TC-PASS-003** | Ganti Password | Konfirmasi password tidak cocok | Password Saat Ini: `password`<br>Password Baru: `RahasiaBaru2026`<br>Konfirmasi: `RahasiaBeda2026` | Muncul validasi: "*Konfirmasi password tidak cocok dengan password baru*". | [ ] |
| **TC-PASS-003b** | Ganti Password | Password baru sama persis dengan password saat ini | Password Saat Ini: `password`<br>Password Baru: `password`<br>Konfirmasi: `password` | Muncul pesan validasi: "*Password baru tidak boleh sama dengan password saat ini.*" | [ ] |
| **TC-PASS-004** | Ganti Password | Update password sukses (Positive Case) | Password Saat Ini: `password`<br>Password Baru: `PasswordAman2026!`<br>Konfirmasi: `PasswordAman2026!` | Password berhasil diupdate, muncul modal/toast sukses, pengguna dapat login menggunakan password baru tersebut. | [ ] |

---

## MODUL 3: DASHBOARD & LOG AKTIVITAS SISTEM

| ID Test Case | Fitur / Komponen | Skenario Uji | Data Masukan (Input) | Respon yang Diharapkan (Expected Result) | Status |
| :--- | :--- | :--- | :--- | :--- | :--- |
| **TC-DASH-001** | KPI Cards | Sinkronisasi total aset dan nilai aset | Buka halaman `/dashboard` | Menampilkan 4 kartu ringkasan (Total Aset, Nilai Aset Rupiah, Kondisi Baik/Rusak, Kategori). Nilai sesuai dengan data aktif di database. | [ ] |
| **TC-DASH-002** | Grafik Aset | Interaksi hover dan filter grafik | Arahkan kursor (*hover*) pada diagram batang / pie chart | Muncul tooltip interaktif berisi detail angka aset tanpa distorsi bayangan (*shadow* berlebih). | [ ] |
| **TC-DASH-003** | Tombol Export Chart | Unduh grafik dalam format dokumen PDF | Klik tombol icon Download / Export pada pojok card grafik | File grafik PDF terunduh otomatis dengan tata letak bersih dan proporsional. | [ ] |
| **TC-LOG-001** | Log Aktivitas | Pencarian log aktivitas | Ketik kata kunci pada kotak search (misal: nama user atau kata "*tambah*") | Tabel menyaring riwayat aktivitas secara cepat sesuai kata kunci pencarian. | [ ] |
| **TC-LOG-002** | Log Aktivitas | Hapus satu baris log aktivitas | Klik tombol hapus (icon tempat sampah) pada salah satu log | Muncul modal konfirmasi: "*Apakah Anda yakin ingin menghapus log ini?*". Jika klik konfirmasi, log terhapus dan list terupdate. | [ ] |
| **TC-LOG-003** | Log Aktivitas | Hapus seluruh log aktivitas (Super Admin) | Klik tombol **Hapus Semua Log** | Muncul modal peringatan keras (*danger modal*). Jika disetujui, seluruh histori log dikosongkan dan tabel menampilkan *empty state*. | [ ] |

---

## MODUL 4: BUKU INVENTARIS (MANAJEMEN ASET)

### 4.1 Filter, Pencarian, & Tabel Inventaris
| ID Test Case | Fitur / Komponen | Skenario Uji | Data Masukan (Input) | Respon yang Diharapkan (Expected Result) | Status |
| :--- | :--- | :--- | :--- | :--- | :--- |
| **TC-ASET-001** | Search Box | Cari aset dengan kata kunci yang tidak ada | Ketik: `xyz999tidakmungkinada` | Tabel menampilkan *empty state*: "*Tidak ada data inventaris yang ditemukan*" dengan ilustrasi ramah. | [ ] |
| **TC-ASET-002** | Filter Kategori | Saring aset berdasarkan kategori tertentu | Pilih: misal `Peralatan dan Mesin` | Tabel hanya menampilkan aset yang termasuk dalam kategori tersebut. | [ ] |
| **TC-ASET-003** | Filter Kondisi | Saring aset berdasarkan kondisi fisik | Pilih: `Rusak Berat` | Tabel hanya menampilkan aset berstatus rusak berat. | [ ] |
| **TC-ASET-004** | Reset Filter | Mengembalikan seluruh filter ke awal | Klik tombol **Reset Filter** | Semua dropdown filter kembali ke opsi default (*Semua*) dan menampilkan list inventaris lengkap. | [ ] |

### 4.2 Tambah Aset Baru (Create Form)
| ID Test Case | Fitur / Komponen | Skenario Uji | Data Masukan (Input) | Respon yang Diharapkan (Expected Result) | Status |
| :--- | :--- | :--- | :--- | :--- | :--- |
| **TC-ASET-005** | Form Tambah | Submit tanpa memilih jenis inventaris | Klik Simpan tanpa pilih Jenis Inventaris | Muncul pesan error/warning: "*Silakan pilih Jenis Inventaris terlebih dahulu*". Form otomatis scroll ke field jenis. | [ ] |
| **TC-ASET-006** | Form Tambah | Validasi field wajib (Required fields) | Jenis terpilih, tapi Kode Barang, NUP, atau Nama Barang dikosongkan | Input yang belum diisi mendapat outline merah dan kursor otomatis difokuskan ke field error tersebut. | [ ] |
| **TC-ASET-006b** | Form Tambah | Nama barang hanya berisi angka tanpa huruf | Nama Barang: `12345678` | Form & backend menolak input: "*Nama barang harus memuat huruf deskriptif (tidak boleh hanya angka).*" | [ ] |
| **TC-ASET-006c** | Form Tambah | Nama pengguna/pemegang hanya berisi angka | Pengguna: `12345678` | Validasi error: "*Nama pengguna/pemegang hanya boleh berisi huruf dan tanda baca nama.*" | [ ] |
| **TC-ASET-007** | Form Tambah | NUP bernilai 0 atau negatif | NUP: `0` atau `-5` | Muncul modal error: "*Nomor NUP minimal harus bernilai 1 (tidak boleh 0 atau negatif)*". | [ ] |
| **TC-ASET-008** | Form Tambah | NUP duplikat pada kode & tahun yang sama | Kode: `02.01.01`, Tahun: `2024`, NUP: `1` *(sudah ada di database)* | Muncul modal error peringatan: "*NUP 1 sudah terdaftar untuk kode barang ini pada tahun 2024. Silakan gunakan nomor NUP berbeda.*" | [ ] |
| **TC-ASET-009** | Form Tambah | Format Nilai Perolehan (Rupiah Masking) | Ketik angka: `15000000` | Input otomatis terformat dengan separator ribuan menjadi `Rp 15.000.000` tanpa perlu mengetik titik manual. | [ ] |
| **TC-ASET-009b** | Form Tambah | Nilai perolehan melebihi batas sistem (> 999 Miliar) | Nilai: `1000000000000` | Validasi error: "*Nilai perolehan maksimal adalah Rp 999.999.999.999 (999 Miliar).*" Sistem terlindungi dan tidak mengalami crash HTTP 500. | [ ] |
| **TC-ASET-009c** | Form Tambah | Tahun perolehan di masa depan atau sebelum 1900 | Tahun: `2099` atau `0000` | Validasi error: "*Tahun perolehan minimal tahun 1900 / tidak boleh lebih dari tahun depan.*" | [ ] |
| **TC-ASET-010** | Form Tambah | Upload Foto: File bukan format gambar | Upload file ekstensi `.pdf` atau `.exe` | Dropzone menolak file dan menampilkan pesan: "*Format file tidak didukung. Harap upload gambar JPG, PNG, atau WEBP.*" | [ ] |
| **TC-ASET-011** | Form Tambah | Upload Foto: Ukuran per foto > 5 MB | Upload foto dengan ukuran 6.5 MB | Sistem menampilkan modal alert: "*Foto [nama file] melebihi batas 5 MB per foto.*" File tidak dimasukkan ke antrean. | [ ] |
| **TC-ASET-012** | Form Tambah | Upload Foto: Jumlah lebih dari 5 foto | Pilih 6 foto sekaligus | Sistem menampilkan modal alert: "*Maksimal upload adalah 5 foto.*" File ke-6 dibatalkan otomatis. | [ ] |
| **TC-ASET-013** | Form Tambah | Salinan Ganda (Multi Copy): Input jumlah salah | Centang "Simpan beberapa salinan", isi jumlah: `1` atau `99` | Muncul validasi: "*Jumlah salinan harus berada di antara 2 hingga 50 salinan.*" | [ ] |
| **TC-ASET-014** | Form Tambah | Tombol Batal | Klik tombol **Batal** setelah mengubah data | Muncul modal dialog konfirmasi: "*Batalkan pengisian? Data yang belum disimpan akan hilang.*" Jika pilih Batal, kembali ke index. | [ ] |
| **TC-ASET-015** | Form Tambah | Simpan Aset Sukses (Positive Case) | Data lengkap & valid, foto 2 buah, salinan 1 | Muncul modal konfirmasi simpan > Klik Konfirmasi > Aset tersimpan > Toast sukses muncul > Redirect ke buku inventaris dengan highlight data baru. | [ ] |

### 4.3 Edit, Duplikasi, Detail, & Hapus Aset
| ID Test Case | Fitur / Komponen | Skenario Uji | Data Masukan (Input) | Respon yang Diharapkan (Expected Result) | Status |
| :--- | :--- | :--- | :--- | :--- | :--- |
| **TC-ASET-016** | Edit Aset | Ubah data aset & pertahankan foto lama | Ubah nama aset & kondisi dari Baik ke Rusak Ringan | Data berhasil diperbarui, foto lama tidak hilang/tertimpa jika tidak dihapus, status kondisi terupdate di tabel. | [ ] |
| **TC-ASET-017** | Duplikasi Aset | Duplikasi aset dari tombol aksi tabel | Klik icon **Duplikat** pada salah satu baris aset | Muncul modal konfirmasi duplikasi dengan auto-generate nomor NUP berikutnya yang belum dipakai. Data terduplikasi sempurna. | [ ] |
| **TC-ASET-017b** | Duplikasi Aset | Duplikasi aset saat urutan NUP memiliki celah atau sudah terisi aset lain | Duplikasi aset sebanyak 3 salinan | Sistem secara dinamis mencari nomor NUP bebas berikutnya yang belum terpakai pada setiap iterasi duplikasi sehingga tidak terjadi crash database (*SQLSTATE 1062 Duplicate entry*). | [ ] |
| **TC-ASET-018** | Cetak Label QR | Cetak label QR tunggal | Klik tombol **Cetak QR** di menu aksi aset | Membuka jendela print preview browser dengan label standar (berisi Logo Desa, Nama Aset, Kode Barang, NUP, dan QR Code presisi). | [ ] |
| **TC-ASET-019** | Cetak QR Massal | Cetak QR untuk banyak aset sekaligus | Centang 3 aset di tabel > Klik **Cetak Barcode Terpilih** | Membuka preview PDF/print sheet berisi 3 label QR yang tersusun rapi dalam format grid kertas siap cetak. | [ ] |
| **TC-ASET-020** | Hapus Aset Tunggal | Hapus 1 aset dari tombol aksi | Klik tombol icon sampah pada baris aset | Muncul modal konfirmasi hapus: "*Apakah Anda yakin ingin menghapus aset [Nama Aset]?*". Klik Hapus > Aset hilang dari list. | [ ] |
| **TC-ASET-021** | Bulk Delete Aset | Hapus massal tanpa memilih aset | Klik tombol **Hapus Terpilih** tanpa centang checkbox | Tombol tidak aktif (disabled) atau muncul toast peringatan: "*Pilih minimal satu aset untuk dihapus.*" | [ ] |
| **TC-ASET-022** | Bulk Delete Aset | Hapus massal dengan centang 2 aset | Centang 2 aset > Klik **Hapus Terpilih** | Muncul modal konfirmasi jumlah item (2 item). Setelah dikonfirmasi, kedua aset terhapus dan tabel me-refresh datanya. | [ ] |

### 4.4 Import Data Aset (Excel)
| ID Test Case | Fitur / Komponen | Skenario Uji | Data Masukan (Input) | Respon yang Diharapkan (Expected Result) | Status |
| :--- | :--- | :--- | :--- | :--- | :--- |
| **TC-IMP-001** | Template Import | Unduh file template Excel inventaris | Klik tombol **Unduh Template** | File `.xlsx` terunduh dengan struktur header kolom resmi desa (Kode, NUP, Nama, Tahun, Nilai, dll). | [ ] |
| **TC-IMP-002** | Upload File | Upload format file non-excel | Upload file `.txt` atau `.pdf` | Dropzone menolak file dengan notifikasi: "*Format file harus .xlsx atau .xls*". | [ ] |
| **TC-IMP-003** | Validasi Baris | Upload Excel dengan baris error (NUP duplikat/kolom kosong) | File Excel dengan 3 baris benar dan 1 baris kolom nama kosong | Modal review import menandai baris yang salah dengan warna merah dan keterangan spesifik errornya sebelum dieksekusi. | [ ] |
| **TC-IMP-003b** | Validasi Baris | Upload Excel dengan nama barang hanya angka atau nilai perolehan bernilai negatif | Kolom nama barang: `12345678` atau nilai perolehan: `-50000` | Baris import ditolak dengan indikator error jelas: "*Nama Barang tidak valid (harus mengandung huruf)*" atau "*Nilai Perolehan tidak boleh bernilai negatif*". Database terlindungi dari data korup. | [ ] |
| **TC-IMP-004** | Eksekusi Import | Import data valid (Positive Case) | File Excel yang seluruh barisnya valid | Sistem memproses baris data, progress bar berjalan hingga 100%, muncul toast sukses bertuliskan jumlah aset yang berhasil diimpor. | [ ] |
| **TC-IMP-005** | Master Module Import | Upload Excel master data (Pejabat/User/Ruangan/Bidang/Dropdown) dengan nama hanya angka | File Excel dengan kolom nama: `12345678` | Sistem importer menandai baris sebagai gagal (*failed row*) dengan pesan error spesifik: "*Nama tidak valid (harus mengandung huruf)*", menjaga integritas master data. | [ ] |

---

## MODUL 5: PENGHAPUSAN ASET (MUTASI BERKURANG)

| ID Test Case | Fitur / Komponen | Skenario Uji | Data Masukan (Input) | Respon yang Diharapkan (Expected Result) | Status |
| :--- | :--- | :--- | :--- | :--- | :--- |
| **TC-MUT-001** | Form Hapus Aset | Submit form tanpa memilih sebab penghapusan | Tanggal: `21/10/2026`<br>Sebab: *(Kosong)* | Muncul validasi: "*Sebab penghapusan wajib dipilih (Rusak Berat, Hilang, Dijual, Dihibahkan, dll)*". | [ ] |
| **TC-MUT-002** | Form Hapus Aset | Submit tanpa nomor SK / dasar hukum | Nomor SK: *(Kosong)* | Field mendapat tanda merah wajib diisi. Form tidak diproses. | [ ] |
| **TC-MUT-002b** | Form Hapus Aset | Tanggal penghapusan diisi tanggal masa depan | Tanggal: `31/12/2035` | Kalender datepicker membatasi hingga hari ini; jika diketik manual, validasi error menolak: "*Tanggal penghapusan tidak boleh di masa depan (maksimal hari ini).*" | [ ] |
| **TC-MUT-003** | Form Hapus Aset | Konfirmasi resmi penghapusan aset | Data lengkap + upload scan Berita Acara PDF | Klik tombol konfirmasi resmi di tengah > Aset dipindahkan dari daftar inventaris aktif ke tab **Riwayat Penghapusan**. | [ ] |
| **TC-MUT-004** | Unduh Berita Acara | Download dokumen Berita Acara PDF | Klik tombol **Unduh PDF** pada riwayat penghapusan | File PDF Berita Acara Resmi terunduh dengan kop surat desa, rincian aset terhapus, dan tanda tangan pejabat terkait. | [ ] |
| **TC-MUT-005** | Batalkan Penghapusan | Restore / Batalkan penghapusan aset | Klik tombol **Pulihkan Aset** di tab riwayat | Muncul modal konfirmasi pemulihan aset. Setelah disetujui, aset kembali aktif ke Buku Inventaris dengan kondisi aslinya. | [ ] |
| **TC-MUT-005b** | Batalkan Penghapusan | Pulihkan aset saat nomor NUP aslinya telah dipakai oleh aset baru | Klik Pulihkan Aset pada riwayat penghapusan di mana NUP aslinya telah dialokasikan ke aset aktif lain | Sistem secara cerdas mendeteksi konflik NUP dan mengalokasikan nomor NUP baru (NUP tertinggi + 1) untuk aset yang dipulihkan tanpa menimbulkan crash database (*1062 Duplicate entry*). | [ ] |
| **TC-MUT-005c** | Batalkan Penghapusan | Pulihkan aset yang memiliki data custom field dan bulan perolehan semester | Klik Pulihkan Aset pada riwayat penghapusan | Seluruh nilai custom field dinamis (spesifikasi kustom) serta bulan perolehan semester terpulihkan secara lengkap ke database tanpa kehilangan data kustom. | [ ] |
| **TC-MUT-006** | Proteksi Tata Kelola Aset | Pilih "Mutasi Berkurang" pada modal hapus aset di Buku Inventaris | Pada halaman Buku Inventaris, klik Hapus Aset > Pilih opsi radio "Mutasi Berkurang (Penghapusan Resmi)" > Lanjutkan | Sistem secara otomatis mengarahkan (redirect) ke formulir resmi Modul Penghapusan Aset dengan melampirkan Dokumen Berita Acara / SK, mencegah penghapusan resmi tanpa dokumen audit. | [ ] |

---

## MODUL 6: PUSAT EKSPOR DATA (LAPORAN RESMI DESA)

| ID Test Case | Fitur / Komponen | Skenario Uji | Data Masukan (Input) | Respon yang Diharapkan (Expected Result) | Status |
| :--- | :--- | :--- | :--- | :--- | :--- |
| **TC-EXP-001** | Ekspor KIR | Pilih Kartu Inventaris Ruangan tanpa pilih ruangan | Jenis: KIR<br>Ruangan: *(Belum dipilih)* | Tombol export tidak dapat diklik atau muncul alert: "*Silakan pilih ruangan tujuan untuk laporan KIR.*" | [ ] |
| **TC-EXP-002** | Counter Preview | Perubahan counter saat memilih parameter | Ganti filter tahun atau ruangan | Indikator counter "*X data ditemukan*" langsung terupdate secara dinamis via AJAX sebelum tombol download ditekan. | [ ] |
| **TC-EXP-003** | Override Pejabat | Ganti pejabat penandatangan sebelum cetak | Buka panel tanda tangan > Pilih nama Kepala Desa / Sekdes pengganti | Pejabat penandatangan di pratinjau dan hasil cetak PDF/Excel berubah sesuai pejabat yang dipilih. | [ ] |
| **TC-EXP-004** | Ekspor Excel & PDF | Generate laporan Buku Inventaris | Klik **Export ke Excel** atau **Export ke PDF** | File laporan terunduh sempurna dengan format tabel rapi, penomoran berurutan, dan footer tanda tangan pejabat desa. | [ ] |
| **TC-EXP-005** | Riwayat Ekspor | Unduh ulang dan hapus riwayat ekspor | Klik icon download pada tabel riwayat | File lama dapat diunduh ulang langsung dari server tanpa perlu generate ulang; tombol hapus log riwayat berfungsi normal. | [ ] |

---

## MODUL 7: MASTER DATA - RUANGAN

| ID Test Case | Fitur / Komponen | Skenario Uji | Data Masukan (Input) | Respon yang Diharapkan (Expected Result) | Status |
| :--- | :--- | :--- | :--- | :--- | :--- |
| **TC-RNG-001** | Tambah Ruangan | Kode ruangan kosong atau huruf | Kode: *(Kosong)* atau `KODE-ABC` | Validasi error: "*Kode ruangan harus berupa angka dan wajib diisi.*" | [ ] |
| **TC-RNG-001b** | Tambah Ruangan | Nama ruangan hanya berisi angka tanpa huruf | Nama: `12345678` | HTML5 pattern / validasi menolak: "*Nama ruangan harus memuat huruf (tidak boleh hanya angka).*" | [ ] |
| **TC-RNG-001c** | Tambah Ruangan | Kode ruangan melebihi batas 99999 | Kode: `1234567` (7 digit) | HTML5 input maxlength="5" dan validasi error: "*Kode ruangan maksimal bernilai 99999 (maksimal 5 digit angka).*" | [ ] |
| **TC-RNG-002** | Tambah Ruangan | Kode ruangan duplikat dengan yang ada | Kode: `001` *(sudah dipakai)* | Muncul validasi error: "*Kode ruangan sudah digunakan oleh ruangan lain.*" | [ ] |
| **TC-RNG-003** | Tambah Ruangan | Simpan data valid (Positive Case) | Kode: `999`<br>Nama: `Ruang Server & Multimedia`<br>Penanggung Jawab: Pilih User | Data berhasil disimpan, muncul di tabel ruangan dengan badge penanggung jawab yang sesuai. | [ ] |
| **TC-RNG-004** | Detail Ruangan | Lihat inventaris di dalam ruangan | Klik nama ruangan atau icon detail | Menampilkan halaman detail ruangan berisi kartu ringkasan dan tabel aset yang berlokasi di ruangan tersebut. | [ ] |
| **TC-RNG-005** | Hapus Ruangan | Hapus ruangan yang masih memiliki aset | Klik Hapus pada ruangan yang berisi 5 aset | Sistem menolak penghapusan dengan modal peringatan: "*Ruangan tidak dapat dihapus karena masih menampung aset.*" | [ ] |

---

## MODUL 8: MASTER DATA - PEJABAT PENANDATANGAN

| ID Test Case | Fitur / Komponen | Skenario Uji | Data Masukan (Input) | Respon yang Diharapkan (Expected Result) | Status |
| :--- | :--- | :--- | :--- | :--- | :--- |
| **TC-PJB-001** | Tambah Pejabat | Nama pejabat atau jabatan kosong | Nama: *(Kosong)*<br>Jabatan: *(Kosong)* | Validasi error: "*Nama lengkap dan jabatan wajib diisi.*" | [ ] |
| **TC-PJB-001b** | Tambah Pejabat | Nama pejabat hanya berisi angka | Nama: `123434` | HTML5 pattern / validasi menolak: "*Nama pejabat hanya boleh berisi huruf dan tanda baca nama/gelar (tidak boleh mengandung angka).*" | [ ] |
| **TC-PJB-001c** | Tambah Pejabat | Jabatan pejabat hanya berisi angka tanpa huruf | Jabatan: `121234323` | HTML5 pattern / validasi menolak: "*Jabatan harus memuat huruf (tidak boleh hanya angka).*" | [ ] |
| **TC-PJB-002** | Tanda Tangan Digital | Canvas TTD: Bersihkan dan tanda tangan | Klik **Bersihkan Gambar** > Buat coretan TTD | Tombol bersihkan menghapus canvas; coretan baru tersimpan dalam format base64 transparan saat form disubmit. | [ ] |
| **TC-PJB-003** | Tanda Tangan Digital | Upload gambar TTD: File bukan gambar (misal file PDF) | Upload file `.pdf` | Sistem menampilkan pesan validasi error: "*File tanda tangan harus berupa gambar (PNG, JPG, JPEG, WEBP).*", mencegah kegagalan render gambar HTML dan crash saat cetak PDF (DomPDF). | [ ] |
| **TC-PJB-004** | Status Pejabat | Toggle status aktif / tidak aktif | Klik switch status pejabat | Pejabat berstatus tidak aktif tidak akan muncul di opsi penandatangan laporan ekspor. | [ ] |
| **TC-PJB-005** | Hapus Massal Pejabat | Hapus massal di mana salah satu pejabat masih dipakai sebagai default penandatangan | Centang 2 pejabat (1 bebas, 1 default penandatangan jenis inventaris) > Klik Hapus Terpilih | Sistem tidak crash; pejabat bebas berhasil terhapus, pejabat yang terikat dilewati dengan pesan: "*1 pejabat berhasil dihapus. Dilewati (1 pejabat masih digunakan sebagai penandatangan default jenis inventaris)*". | [ ] |

---

## MODUL 9: MASTER DATA - MANAJEMEN PENGGUNA (USERS)

| ID Test Case | Fitur / Komponen | Skenario Uji | Data Masukan (Input) | Respon yang Diharapkan (Expected Result) | Status |
| :--- | :--- | :--- | :--- | :--- | :--- |
| **TC-USR-001** | Tambah Pengguna | Cek realtime ketersediaan username | Ketik username yang sudah ada di sistem | Muncul indikator teks merah di bawah input: "*Username sudah digunakan*". | [ ] |
| **TC-USR-001b** | Tambah Pengguna | Nama lengkap hanya berisi angka | Nama: `123434` | HTML5 pattern / validasi error menolak: "*Nama hanya boleh berisi huruf dan spasi atau tanda baca nama.*" | [ ] |
| **TC-USR-002** | Tambah Pengguna | Password baru kurang dari 8 karakter | Password: `admin` (5 karakter) | Validasi error: "*Password minimal 8 karakter.*" | [ ] |
| **TC-USR-003** | Tambah Pengguna | Konfirmasi password tidak cocok | Password: `Password123`<br>Konfirmasi: `Password456` | Validasi error: "*Konfirmasi password tidak cocok.*" | [ ] |
| **TC-USR-004** | Status Pengguna | Toggle status aktif/nonaktif via AJAX | Klik tombol switch status pada baris pengguna | Status berubah seketika tanpa reload halaman penuh; user yang dinonaktifkan tidak dapat login ke sistem. | [ ] |
| **TC-USR-004b** | Form Pengguna | Upload berkas TTD pejabat bertipe PDF | Upload file `.pdf` pada field tanda tangan pejabat | Sistem menolak berkas dengan validasi error: "*File tanda tangan harus berupa gambar (PNG, JPG, JPEG, WEBP).*" | [ ] |
| **TC-USR-004c** | Status Pengguna | Toggle nonaktifkan akun ber-role Super Admin | Klik switch nonaktif pada akun Super Admin | Sistem menolak dengan alert error: "*Akun Super Admin tidak dapat dinonaktifkan dari sistem.*", mencegah lockout administratif. | [ ] |
| **TC-USR-004d** | Keamanan Akun | Ubah username menjadi username Super Developer yang dicadangkan sistem | Pada edit akun user atau halaman profil (`/profile`), ubah username menjadi `developer`, `owner`, atau `sima_superdeveloper` | Sistem menolak dengan validasi error: "*Username tersebut dicadangkan untuk sistem dan tidak dapat digunakan.*", mencegah eskalasi hak akses Super Developer secara tidak sah. | [ ] |
| **TC-USR-004e** | Tambah Pengguna | Registrasi akun baru menggunakan username Super Developer yang dicadangkan sistem | Masukkan username `developer`, `owner`, atau `sima_superdeveloper` pada form tambah user atau API `/api/users` | Validasi error menolak: "*Username tersebut dicadangkan untuk sistem dan tidak dapat digunakan.*", mencegah pembuatan akun dengan privilege developer. | [ ] |
| **TC-USR-004f** | Impor Pengguna CSV | Impor berkas CSV berisi peran tunggal ganda (Perbekel / Sekretaris Desa / Super Admin) | Unggah berkas CSV yang berisi lebih dari 1 Perbekel atau mengimpor Perbekel saat sudah ada Perbekel aktif | Baris kedua dan baris yang melebihi kuota 1 pejabat ditolak dan masuk ke daftar baris gagal dengan pesan: "*Sudah ada user dengan role Perbekel. Hanya 1 user yang diperbolehkan.*" | [ ] |
| **TC-USR-005** | Hapus Pengguna | Proteksi hapus akun sendiri (Self-delete) | Super Admin mencoba menghapus akun `superadmin` yang sedang aktif | Tombol hapus tidak tersedia / sistem menolak: "*Anda tidak dapat menghapus akun yang sedang Anda gunakan.*" | [ ] |
| **TC-USR-005b** | Hapus Pengguna | Hapus akun sendiri melalui aksi single delete | User aktif memanggil route hapus untuk user_id miliknya | Sistem menolak dengan alert error: "*Anda tidak dapat menghapus akun yang sedang Anda gunakan.*" Sesi akun tetap aktif. | [ ] |

---

## MODUL 10: MASTER DATA - BIDANG

| ID Test Case | Fitur / Komponen | Skenario Uji | Data Masukan (Input) | Respon yang Diharapkan (Expected Result) | Status |
| :--- | :--- | :--- | :--- | :--- | :--- |
| **TC-BDG-001** | Tambah Bidang | Kode bidang atau nama bidang kosong | Kode: *(Kosong)* | Muncul validasi: "*Kode bidang dan nama bidang wajib diisi.*" | [ ] |
| **TC-BDG-001b** | Tambah Bidang | Nama bidang hanya berisi angka tanpa huruf | Nama: `1213123231` | HTML5 pattern / validasi error menolak: "*Nama bidang harus memuat huruf (tidak boleh hanya angka).*" | [ ] |
| **TC-BDG-002** | Tambah Bidang | Kode bidang duplikat | Kode: `1.01` *(sudah terdaftar)* | Muncul pesan error: "*Kode bidang sudah digunakan.*" | [ ] |
| **TC-BDG-003** | Tambah Bidang | Simpan bidang valid | Kode: `9.99`<br>Nama: `Bidang Pemberdayaan Inovasi Desa` | Bidang berhasil ditambahkan dan langsung tersedia pada dropdown pilihan bidang. | [ ] |

---

## MODUL 11: MASTER DATA - JENIS INVENTARIS & CUSTOM FIELD BUILDER

| ID Test Case | Fitur / Komponen | Skenario Uji | Data Masukan (Input) | Respon yang Diharapkan (Expected Result) | Status |
| :--- | :--- | :--- | :--- | :--- | :--- |
| **TC-JNS-001** | Form Tambah Jenis | Simpan tanpa mengisi nama jenis | Nama Jenis: *(Kosong)* | Validasi error: "*Nama jenis inventaris wajib diisi.*" | [ ] |
| **TC-JNS-001b** | Form Tambah Jenis | Nama jenis inventaris hanya berisi angka tanpa huruf | Nama: `12345678` | Validasi error backend / form menolak: "*Nama jenis inventaris harus memuat huruf (tidak boleh hanya angka).*" | [ ] |
| **TC-JNS-001c** | Form Tambah Jenis | Nama jenis inventaris sudah ada / duplikat | Nama: `Peralatan dan Mesin` *(sudah ada)* | Validasi error menolak: "*Nama jenis inventaris sudah terdaftar, silakan gunakan nama lain.*" | [ ] |
| **TC-JNS-002** | Form Tambah Jenis | Pilih tipe KIB (Kartu Inventaris Barang) | Pilih tipe: `KIB B (Peralatan dan Mesin)` | Jenis baru otomatis terikat dengan skema KIB B saat export data laporan. | [ ] |
| **TC-JNS-003** | Custom Field Builder | Tambah field spesifikasi kustom | Klik **Tambah Field** > Isi nama: `Nomor Mesin`, Tipe: `Teks`, Wajib: `Ya` | Baris field bertambah di form builder. Saat form Tambah Aset dibuka dengan jenis ini, input "Nomor Mesin" otomatis muncul. | [ ] |
| **TC-JNS-004** | Hapus Jenis | Hapus jenis yang sudah digunakan banyak aset | Klik Hapus pada jenis yang memiliki aset | Sistem memblokir penghapusan: "*Jenis inventaris tidak dapat dihapus karena sedang digunakan oleh data aset.*" | [ ] |
| **TC-JNS-005** | Proteksi Sistem Pakem | Hapus jenis template LHI "Inventaris yang tidak ditemukan" atau virtual "Buku Inventaris" | Hapus jenis template LHI via web maupun API `DELETE /api/jenis-inventaris/{id}` | Sistem menolak dengan pesan: "*Jenis template LHI 'Inventaris yang tidak ditemukan' adalah master sistem pakem dan tidak boleh dihapus.*" (atau HTTP 422 JSON pada API). | [ ] |

---

## MODUL 12: MASTER DATA - DROPDOWN DINAMIS

| ID Test Case | Fitur / Komponen | Skenario Uji | Data Masukan (Input) | Respon yang Diharapkan (Expected Result) | Status |
| :--- | :--- | :--- | :--- | :--- | :--- |
| **TC-DRP-001** | Opsi Dropdown | Tambah opsi nilai baru pada kategori | Kategori: `Asal Usul`<br>Nilai: `Sumbangan Pihak Ketiga` | Opsi tersimpan dan otomatis muncul sebagai pilihan radio/select di form input aset. | [ ] |
| **TC-DRP-001b** | Form Dropdown | Nama dropdown hanya berisi angka tanpa huruf | Nama: `12345678` | HTML5 pattern / validasi error menolak: "*Nama dropdown harus memuat huruf (tidak boleh hanya angka).*" | [ ] |
| **TC-DRP-002** | Hapus Opsi | Hapus opsi yang tidak terpakai | Klik icon hapus pada salah satu opsi | Opsi terhapus dari master dropdown dan tidak muncul lagi di form input. | [ ] |
| **TC-DRP-003** | Index & Statistik Dropdown | Buka halaman master dropdown untuk memuat statistik | Buka halaman `/dropdown` | Halaman terbuka tanpa peringatan PHP (*Undefined variable $topDropdown*); kartu statistik menampilkan nama dan jumlah item dropdown teratas dengan tepat. | [ ] |

---

## MODUL 13: SISTEM NOTIFIKASI

| ID Test Case | Fitur / Komponen | Skenario Uji | Data Masukan (Input) | Respon yang Diharapkan (Expected Result) | Status |
| :--- | :--- | :--- | :--- | :--- | :--- |
| **TC-NOTIF-001** | Navbar Bell Icon | Klik ikon lonceng notifikasi | Klik ikon lonceng di navbar atas | Muncul dropdown daftar 5 notifikasi terbaru dengan penanda belum dibaca (*unread indicator*). | [ ] |
| **TC-NOTIF-002** | Tandai Dibaca | Tandai semua notifikasi telah dibaca | Klik tombol **Tandai Semua Dibaca** | Badge angka notifikasi merah di lonceng hilang/menjadi 0; warna background notifikasi berubah menjadi *read*. | [ ] |
| **TC-NOTIF-003** | Hapus Notifikasi | Hapus notifikasi yang sudah dibaca | Klik **Hapus Yang Sudah Dibaca** | Semua item notifikasi yang berstatus terbaca dibersihkan dari daftar. | [ ] |
| **TC-NOTIF-004** | Hapus Tunggal via AJAX | Hapus satu item notifikasi melalui pemanggilan AJAX/JSON | Kirim request `DELETE /notifications/{id}` dengan header `Accept: application/json` | Sistem mengembalikan respons JSON `{ "success": true, "message": "Notifikasi dihapus." }` dengan status HTTP 200 tanpa redirect paksa. | [ ] |

---

## MODUL 14: CADANGAN BASIS DATA (DATABASE BACKUP)

| ID Test Case | Fitur / Komponen | Skenario Uji | Data Masukan (Input) | Respon yang Diharapkan (Expected Result) | Status |
| :--- | :--- | :--- | :--- | :--- | :--- |
| **TC-BCK-001** | Hak Akses | Akses backup dengan role non-Super Admin | Login sebagai `operator` > Buka `/backup` | Sistem menolak akses dengan halaman **403 Forbidden** (*Akses Ditolak*). | [ ] |
| **TC-BCK-002** | Buat Backup | Eksekusi pembuatan cadangan database baru | Klik tombol **Buat Cadangan Baru** | Tombol menampilkan animasi proses/spinner, setelah selesai file backup baru bertambah di tabel dengan ukuran dan tanggal realtime. | [ ] |
| **TC-BCK-003** | Unduh Backup | Download file cadangan `.sql` / `.zip` | Klik tombol **Unduh** pada baris backup | File arsip database terunduh ke komputer lokal pengguna. | [ ] |
| **TC-BCK-004** | Hapus Backup | Hapus file cadangan lama | Klik tombol **Hapus** pada baris cadangan | Muncul modal konfirmasi hapus; file fisik cadangan di server berhasil dimusnahkan. | [ ] |
| **TC-BCK-005** | Preservasi Leading Zero | Verifikasi dump SQL terhadap kolom teks berisi angka diawali nol (nomor HP, NIK, kode pos) | Buat cadangan database, periksa isi baris `INSERT INTO` pada file `.sql` | Nilai string berupa nomor berawalan nol (misal `'08123456789'`) tetap dikutip dengan tanda petik `'...'` sehingga angka nol depan tidak terhapus saat direstore. | [ ] |

---

## MODUL 15: FITUR PUBLIK (SCAN BARCODE / QR CODE PUBLIK)

| ID Test Case | Fitur / Komponen | Skenario Uji | Data Masukan (Input) | Respon yang Diharapkan (Expected Result) | Status |
| :--- | :--- | :--- | :--- | :--- | :--- |
| **TC-SCAN-001** | Akses Valid | Scan QR Code asli aset dengan smartphone | Buka URL resmi dari QR Code: `/scan/{kode}?signature=...` | Halaman informasi publik aset terbuka **tanpa login**, menampilkan identitas desa Demo, spesifikasi aset, ruangan, dan status resmi aset desa. | [ ] |
| **TC-SCAN-002** | Manipulasi URL | Buka URL scan tanpa parameter signature | Ketik manual di browser: `http://127.0.0.1:8000/scan/02.01.01-0001` | Sistem menolak dengan halaman **403 Forbidden** (*Tautan Verifikasi Tidak Sah / Kedaluwarsa*). | [ ] |
| **TC-SCAN-003** | Aset Tidak Ada / Terhapus | URL signed tapi barcode fiktif atau aset telah dihapus | Signature valid tapi barcode tidak terdaftar atau aset telah dihapus dari sistem | Sistem menampilkan halaman informasi ramah "*Aset Tidak Ditemukan*" dengan judul halaman bersih tanpa mengalami fatal crash HTTP 500 (*Attempt to read property on null*). | [ ] |

---

## MODUL 16: ERROR HANDLING & KEAMANAN SISTEM

| ID Test Case | Fitur / Komponen | Skenario Uji | Data Masukan (Input) | Respon yang Diharapkan (Expected Result) | Status |
| :--- | :--- | :--- | :--- | :--- | :--- |
| **TC-ERR-001** | Halaman 404 | Akses rute URL yang tidak terdaftar | Ketik URL: `http://127.0.0.1:8000/halaman-ngawur-tidak-ada` | Menampilkan custom error page **404 Halaman Tidak Ditemukan** dengan tema SIMA dan tombol kembali ke dashboard. | [ ] |
| **TC-ERR-002** | Proteksi CSRF (419) | Submit form setelah sesi login kedaluwarsa | Diamkan halaman form lebih dari 120 menit, lalu klik Simpan | Menampilkan halaman custom **419 Halaman Kedaluwarsa** dengan tombol Segarkan / Kembali ke Login. | [ ] |
| **TC-ERR-003** | Proteksi XSS | Uji celah Cross-Site Scripting pada field teks | Input pada nama aset / ruangan: `<script>alert('XSS-TEST')</script>` | Skrip tidak tereksekusi sama sekali; karakter di-escape secara aman oleh Blade engine (`&lt;script&gt;...`). | [ ] |
| **TC-ERR-004** | Proteksi SQL Injection | Uji injection karakter kutip dan boolean SQL | Input pada kolom search: `' OR '1'='1` | Sistem melakukan parameter binding PDO secara aman; tidak terjadi error sintaks SQL atau pembocoran data. | [ ] |

---

## CATATAN AKHIR & KESIMPULAN PENGUJIAN
- **Total Test Cases**: 81 Skenario Pengujian
- **Status Lolos**: ______ / 81
- **Status Gagal**: ______ / 81
- **Catatan / Temuan Penting Penguji**:
  ```text
  (Tuliskan catatan khusus, saran perbaikan, atau nomor test case yang memerlukan perhatian di sini)
  ```
- **Tanggal Selesai Uji**: _______________________
- **Nama & Tanda Tangan Penguji**: _______________________
