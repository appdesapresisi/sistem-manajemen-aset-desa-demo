# Standar Desain Sistem Manajemen Aset Desa (SIMA) — Panduan Anti-AI-Slop

Dokumen ini adalah **sumber kebenaran tunggal (*single source of truth*)** untuk seluruh antarmuka (UI), komponen visual, tata letak, palet warna, dan kode tampilan pada proyek Sistem Manajemen Aset Desa (SIMA).

Aturan ini wajib ditaati setiap kali membuat, memodifikasi, atau merevisi file UI di seluruh proyek. Jika ada konflik antara aturan ini dengan kebiasaan umum AI, aturan di dokumen ini yang berlaku mutlak.

---

## 1. Prinsip Utama (Filosofi Desain)

1. **Aplikasi Manajemen Nyata, Bukan Landing Page**: Ini adalah sistem informasi penatausahaan aset pemerintah desa yang fungsional. Hindari elemen dekoratif pameran, statistik bombastis palsu, atau hero banner ala agensi.
2. **Kebenaran Data (*Truthful & Grounded Data*)**: Setiap angka, persentase, dan status harus dihitung riil dari database. Dilarang melakukan hardcode serba "100%", serba "Prima", atau kata-kata template AI yang menutupi kondisi data sebenarnya.
3. **Setiap Elemen Punya Alasan**: Jika sebuah garis, warna, border, atau badge tidak memiliki alasan fungsi nyata bagi pengguna, **jangan tambahkan**. Lebih baik sedikit tapi rapi dan teratur.
4. **Desain Konsisten, Bersih, dan Tenang**: Menggunakan palet warm organic (krem, ivory, hijau tua hutan) yang nyaman di mata untuk pemakaian kerja harian yang intensif.

---

## 2. Palet Warna (Color Palette Tokens)

Semua warna **WAJIB** merujuk pada token CSS variabel di bawah ini. Dilarang menulis nilai hex bebas secara sembarangan di komponen.

```css
:root {
  /* --- Brand Tokens --- */
  --color-primary: #1f3a2a;         /* Hijau tua: tombol utama, sidebar, aksen penting */
  --color-primary-hover: #162c1f;   /* State hover/pressed tombol primary */
  --color-primary-soft: #2e523b;    /* State aktif menu sidebar */
  --color-primary-tint: #d5e4d7;    /* Highlight lembut elemen aktif */

  /* --- Netral Hangat (Warm Neutrals) --- */
  --color-bg: #eaf1e7;              /* Background kanvas utama halaman */
  --color-surface: #f8faf7;         /* Card, modal, dropdown container, input background */
  --color-border: #cfdcc8;          /* Garis tepi card, divider, separator */
  --color-row-hover: #eff5ed;       /* Background baris tabel saat di-hover */

  /* --- Tipografi & Teks --- */
  --color-text: #16251b;            /* Teks judul, label tebal, angka utama */
  --color-text-muted: #546958;      /* Deskripsi, label sekunder, placeholder */
  --color-text-on-primary: #eaf1e7; /* Teks di atas latar belakang hijau gelap */

  /* --- Aksen (Maksimal 10% Layar) --- */
  --color-accent: #C9A24A;          /* Emas/Ochre: highlight dokumen KIB, icon penting */

  /* --- Status Semantik (Solid & Soft Badge) --- */
  --color-success: #3F7D3A;  --color-success-soft: #dce8d5; /* Kondisi Baik / Lengkap */
  --color-warning: #B7791F;  --color-warning-soft: #F4E7C3; /* Rusak Ringan / Peringatan */
  --color-danger:  #A63D2F;  --color-danger-soft:  #F3DAD3; /* Rusak Berat / Hapus / Error */
  --color-info:    #3E6A7A;  --color-info-soft:    #DCE8EA; /* Mutasi / Informasi Teknis */

  /* --- Fallback Data Kosong --- */
  --color-fallback-empty: #B7791F;  /* Warm Golden Amber: teks data null/kosong/belum ditentukan (sama dengan --color-warning) */
}
```

### Aturan Penerapan Warna:
- **Proporsi Visual**: ±60% Cream/Ivory (`--color-bg`, `--color-surface`), ±30% Hijau Tua Hutan (`--color-primary`), maksimal 10% Aksen Emas/Status.
- **Eksklusivitas Warna Kuning / Amber**: Warna kuning atau palet warna kuning (`var(--color-warning)` / `#B7791F`) **HANYA DAN EKSKLUSIF** difungsikan untuk data yang bernilai kosong / belum ditentukan (`.text-empty-fallback`). **DILARANG KERAS** menggunakan warna kuning atau teks kuning untuk tombol navigasi, teks link pengarah berkas/halaman (*"Pusat Ekspor"*, *"Lihat"*, *"Buka"*, *"Unduh"*, *"Cetak"*), teks CTA, maupun ikon navigasi.
- **Dilarang Warna Ekstrem**: Dilarang menggunakan `#FFFFFF` murni atau `#000000` murni sebagai latar belakang atau teks utama.
- **Dilarang Gradien**: Dilarang menggunakan gradien warna-warni (ungu-biru, neon, pink), glow effect, atau glassmorphism dekoratif.

---

## 3. Tipografi & Skala Spacing

- **Font Family**: Satu keluarga font sans-serif konsisten: **Inter** / **Plus Jakarta Sans**. Dilarang menambah font display atau dekoratif baru.
- **Hierarki Font**:
  - Judul Halaman: `20px - 22px`, font-weight `700`, letter-spacing `-0.02em`.
  - Judul Card / Section: `14px - 15px`, font-weight `600`, color `var(--color-text)`.
  - Teks Isi / Nilai Tabel: `13px - 14px`, line-height `1.5`, color `var(--color-text)`.
  - Teks Keterangan / Muted: `11.5px - 12.5px`, color `var(--color-text-muted)`.
- **Kapitalisasi**: Gunakan **Sentence case** ("Lihat semua jenis", "Tambah aset baru"), bukan Title Case dan bukan ALL CAPS.
- **Skala Spacing Tetap**: `4px`, `8px`, `12px`, `16px`, `24px`, `32px`, `48px`. Hindari angka arbitrer.

---

## 4. Standar Card & Container (Anti-AI-Slop Khusus)

1. **Card Standar (`.sima-card`, `.card`)**:
   - Background: `var(--color-surface, #f8faf7)`.
   - Border: `1px solid var(--color-border, #cfdcc8)`.
   - Radius: `var(--radius-lg, 16px)`.
   - Shadow: `none !important` (tanpa bayangan tebal melayang).
2. **DILARANG Garis Aksen Samping di Pojok Kiri Card (`border-left` Accent)**:
   - **DILARANG KERAS** menambahkan garis warna di pinggir kiri card/sub-box (misalnya: `border-left: 3px solid var(--color-primary);` atau `border-left: 3px solid var(--color-accent);`).
   - Garis warna vertikal di kiri card adalah **tanda visual AI slop klasik** yang tidak diizinkan di sistem ini.
3. **Standar Garis Pemisah (Normal Solid Line untuk Tabel & Dashed Line Sistem untuk Non-Tabel)**:
   - **Tabel & Halaman Index Tabel (Wajib Garis Normal Lurus / Solid Line)**: Pada setiap komponen tabel, seluruh garis pembatas (toolbar kontrol/pencarian atas tabel, header `thead th`, baris data `tbody tr td`, dan pagination footer bar) **WAJIB menggunakan garis normal yang lurus (*solid line*)**: `border-bottom: 1px solid var(--color-border);` dan `border-top: 1px solid var(--color-border);`. Ini menjamin keseragaman visual seluruh garis di tabel.
   - **Selain Tabel / Non-Table (Wajib Garis Standar Sistem Putus-Putus / Dashed Line)**: Untuk pemisah di luar tabel (seperti di bawah setiap rincian data halaman detail/show, pemisah section form/modal non-tabel, serta menu dropdown), **WAJIB menggunakan garis putus-putus (*dashed line*)**: `border-bottom: 1px dashed var(--color-border);` atau `border-top: 1px dashed var(--color-border);`.
   - Aturan teknis lengkap tercatat di `.agents/rules/dashed-dividers.md`.
4. **DILARANG Header Tunggal Berulang / Duplikasi Informasi (Anti-AI-Slop Detail Page)**:
   - **DILARANG KERAS** membuat section/banner/header mandiri di bagian atas kartu detail yang hanya menampilkan informasi tunggal (misalnya hanya *"Nama Aset"*, *"Nama Pengguna"*, *"Judul Entitas"*) yang kemudian informasinya diulang kembali pada section data terstruktur / key-value grid di bawahnya.
   - Pola meletakkan judul tunggal di header terpisah yang hanya mengulang data di bawahnya adalah **tanda visual AI-slop** yang membuang ruang layar dan membuat informasi terlihat berulang secara janggal.
   - **Solusi Bersih**: Hilangkan header tunggal redundan tersebut. Tempatkan informasi identitas utama (misalnya Nama Aset atau Nama Pengguna) langsung menyatu ke dalam section terstruktur / grid ringkasan data yang relevan di bawahnya.
   - Aturan teknis lengkap tercatat di `.agents/rules/no-redundant-single-headers.md`.
5. **Standar Kartu Detail / Show (*Standardized Show Page Layout & Typography*)**:
   - Berlaku untuk **seluruh halaman detail (*show*)** di seluruh sistem (`/manajemen-aset/{id}`, `/profile`, `/users/{id}`, `/ruangan/{id}`, `/pejabat/{id}`, `/jenis-inventaris/{id}`, dsb.).
   - **Ukuran Teks (Font Size Standard — Mengacu pada Manajemen Aset)**:
     - Label field (`.portal-kv-label`): `11px`, `font-weight: 600`, `text-transform: uppercase`, `letter-spacing: 0.5px`, `color: var(--color-text-muted)`.
     - Value/isi field (`.portal-kv-value`): `14px`, `font-weight: 600`, `color: var(--color-text)`, `line-height: 1.4`.
     - Identitas Utama / Nama Entitas: `15px` - `16px`, `font-weight: 700`, `color: var(--color-primary)` atau `var(--color-text)`.
     - Badge/status pill: `12px`, `font-weight: 600`, padding `3px 10px`.
   - **Tata Letak & Spacing (Spacing Standard — Mengacu pada Kombinasi Lapang & Panjang)**:
     - Padding kartu (.portal-card): 44px 50px (lapang, kokoh, tidak mepet ke tepi).
     - Tinggi minimal kartu (.portal-card): min-height: calc(100vh - 220px); (desktop min-height: 580px;, display flex column) agar kartu tampil panjang, megah, dan seluruh konten tidak terasa sesak maupun menyisakan ruang kosong bolong di layar.
     - Grid gap (.portal-kv-grid): gap: 28px 48px; (gap baris vertikal 28px, gap kolom horizontal 48px).
     - Jarak vertikal per item & divider baku (.portal-kv-item, .profile-field-item, .portal-stat-item): gap: 7px; padding-bottom: 18px; dan **WAJIB menerapkan garis pembatas putus-putus (*dashed divider*) di bawah setiap data**: order-bottom: 1px dashed var(--color-border, #cfdcc8);.
     - Pemisahan antar-seksi (.portal-section): padding-top: 36px; margin-top: 36px; border-top: none; (murni mengandalkan whitespace alami, **DILARANG ada garis divider putus-putus antar-baris maupun antar-seksi**). Seksi terakhir otomatis mengikat ke dasar kartu (margin-top: auto; padding-top: 36px;).
     - **Grid Ringkasan Data (.portal-stats-grid)**: Wajib 2-kolom simetris seimbang (grid-template-columns: repeat(2, 1fr); gap: 24px 36px;). Dilarang mencampuradukkan span 2 dengan kolom bolong/kosong. Field disusun berpasangan logis: Baris 1 (*Nama Aset* & *Kondisi*), Baris 2 (*Nilai Perolehan* & *Asal Usul*), Baris 3 (*Pengguna* & *Satuan*).
     - **Seksi Dokumen & Label QR (.portal-docs-section)**: order-top: none; padding-top: 28px; (**DILARANG ada garis divider dashed/solid** di atas seksi dokumen/label QR, murni whitespace lapang alami).
   - Aturan teknis lengkap tercatat di .agents/rules/portal-detail-standard.md.
   - **DILARANG KERAS** menambahkan ikon kecil dekoratif (seperti `<i data-feather="...">`, `<svg>`, atau `.section-icon-box`) di samping judul section di mana pun, baik di formulir tambah/create, edit, maupun halaman rincian/detail (*show*).
   - Menyisipkan ikon generik di sebelah judul section (seperti ikon kotak/grid di sebelah *"Pilih Jenis Inventaris"*, ikon box di *"Data Inventaris"*, ikon user di *"Informasi Akun"*, ikon kunci di *"Keamanan Akun"*, ikon shield, dsb.) adalah **tanda visual AI slop klasik** yang tidak perlu dan membuat antarmuka terlihat amatir.
   - **Solusi Bersih**: Cukup teks judul polos dengan tipografi berbobot tegas (`font-size: 14.5px` - `15px`, `font-weight: 700`, warna `var(--color-primary)` atau `var(--color-text)`). Teks judul sudah sangat jelas dan merefleksikan hierarki informasi secara elegan.
   - Aturan teknis lengkap tercatat di `.agents/rules/no-section-icons.md`.
7. **DILARANG Membungkus Data Jenis Inventaris dengan Card/Badge/Pill (Clean Plain Text Standard)**:
   - **DILARANG KERAS** membungkus data jenis inventaris (seperti *"Inventaris Peralatan dan Mesin"*, *"Inventaris Gedung dan Bangunan"*, dsb.) di dalam kotak card pembungkus, status pill, tag, atau badge (misalnya `.status-pill.status-pill-neutral` atau `.badge`).
   - Jenis inventaris adalah data atribut kategori informatif, bukan status kondisi (*Baik/Rusak*) dan bukan status approval (*Pending/Disetujui*). Membungkusnya dengan card/pill abu-abu berulang secara vertikal membuat baris tabel terasa berantakan dan sesak.
   - **Standar Baku**: Wajib ditampilkan sebagai **teks biasa bersih (*plain text*)**: `font-size: 13px; font-weight: 500; color: var(--color-text, #16251b);`.
   - Berlaku di seluruh tampilan sistem (tabel manajemen aset SSR & live search AJAX, detail ruangan, daftar mutasi, dsb.).
   - Aturan teknis lengkap tercatat di `.agents/rules/no-category-cards.md`.
8. **DILARANG Membungkus Teks Trend/Delta KPI dengan Card/Pill (Clean Plain Text Metric Standard)**:
   - Teks tren metrik dan keterangan sub-stat pada kartu KPI (seperti *"0% (0) vs. bulan lalu"*, *"Mutasi Berkurang Buku Aset Desa"*, *"Tahun 2026 periode berjalan"*, *"0 Unit akumulasi aset"*) **DILARANG KERAS dibungkus dalam card atau pill bulat** dengan background warna / padding kapsul (`.kpi-trend-pill`, `.stat-delta-pill`).
   - Elemen tersebut **WAJIB ditampilkan sebagai teks polos bersih (*plain text*)**: `font-size: 11.5px; font-weight: 600; line-height: 1.3;` tanpa background (`background: transparent !important`), tanpa padding kapsul (`padding: 0 !important`), dan tanpa border radius. Indikator naik/turun/netral hanya diwakili oleh warna teks yang bersih atau ikon panah kecil alami.

9. **Standar Penyelarasan & Konsistensi Margin Kartu KPI (Symmetric KPI Card Baseline Standard)**:
   - Seluruh kartu metrik KPI (`.stat-card-sima`, `.stat-card`) di seluruh modul sistem (Dashboard, Manajemen Aset, Jenis Inventaris, Ruangan, Pejabat, Users, Bidang, Dropdown, Penghapusan Aset) **WAJIB menerapkan struktur 3-tier vertikal dengan margin dan slot ketinggian baku**:
     1. **Tier 1 (Header/Label Atas)**: `height: 14px; margin-bottom: 8px !important;` (jarak ke nilai utama selalu tetap 8px).
     2. **Tier 2 (Nilai Utama/Metrik)**: Wajib memiliki **slot ketinggian tetap terkunci** `height: 32px; min-height: 32px; max-height: 32px; display: flex; align-items: center; margin-bottom: 10px !important;`. Ini menjamin angka 1 digit (`8`), angka 3 digit (`442`), nominal rupiah, maupun teks nama kategori panjang memiliki slot tinggi yang identik dan tidak menggeser layout kartu lain.
     3. **Tier 3 (Keterangan/Trend Bawah)**: Wajib menerapkan **`margin-top: auto !important;`** sehingga teks tren bawah (`0% (0) vs. bulan lalu`, dominan, periode berjalan) selalu terkunci rata di dasar kartu pada posisi baseline yang **100% rata sejajar horizontal** di semua 4 kartu.
     4. **No Wrap pada Metrik & Ikon Trend**: Trend pill dan label pendamping wajib `flex-wrap: nowrap !important; white-space: nowrap !important;` dengan ikon SVG `vertical-align: middle; flex-shrink: 0; width: 12px; height: 12px;` agar ikon tidak pernah patah ke baris baru.
   - Aturan teknis lengkap tercatat di `.agents/rules/kpi-cards-baseline.md`.

10. **Standar Identifikasi & Filter Kelengkapan Data Aset (*Data Completeness Standard*)**:
    - **Definisi Lengkap**: Seluruh kolom wajib (*required fields*) telah terisi lengkap. Meliputi field inti (`nama_barang`, `kode_barang`, `nup`, `tahun_perolehan`, `asal_usul`, `kondisi`), `ruangan_id` jika jenis inventaris mewajibkan ruangan (`requires_ruangan == 1`), serta seluruh atribut kustom yang ditandai `is_required == 1`. Kolom opsional (`foto fisik`, `pengguna`, `keterangan`, `bukti_kepemilikan`) diabaikan dan aset tetap diidentifikasi berstatus **Lengkap**.
    - **Definisi Tidak Lengkap / Perlu Dilengkapi**: Aset yang memiliki minimal 1 kolom wajib yang masih bernilai NULL/kosong (biasa terjadi ketika developer melakukan migrasi/injeksi massal via SQL query langsung tanpa validasi form).
    - **Penyajian di Tabel**: Baris aset yang tidak lengkap menampilkan badge pill peringatan halus `.status-pill.status-pill-warning` bertuliskan *"Perlu Dilengkapi"* di bawah baris NUP/Kode Barang dengan atribut `title` yang mencantumkan rincian field wajib apa saja yang belum terisi.
    - **Filter & Navigasi**: Popover Filter Manajemen Aset menyediakan opsi radio *"Semua Status Kelengkapan"*, *"Data Lengkap"*, dan *"Perlu Dilengkapi (Tidak Lengkap)"* yang tersinkronisasi dua arah via parameter URL query `?kelengkapan=...` dan terhubung langsung dari kartu Dashboard (*"Lihat aset yang belum lengkap"*).

---

## 5. Standar Form Inputs & Validasi Error

1. **Zero Shadow pada Input & Dropdown**:
   - Semua form input (`input`, `select`, `textarea`, `.form-control`, `.form-select`, search box):
   - **DILARANG menerapkan `box-shadow` apapun saat fokus/aktif**.
   - Wajib `box-shadow: none !important;` dan `outline: none !important;`.
   - Indikator fokus hanya berupa perubahan warna border (`border-color: var(--color-primary)`).
2. **Standar Baku Validasi Input & State Error (Dribbble Slick UX Standard)**:
   - **Prinsip Utama**: Respons visual error harus informatif, anggun, tegas, dan tidak merusak tata letak form (dilarang menggunakan alert banner mengambang besar warna pink di atas form).
   - **Tiga Unsur Visual State Error Terintegrasi**:
     1. **Warna Judul / Label Field Ikut Merah**:
        - Label field (`.form-label`, `.kezak-label`) dan tanda bintang required (`.req`, `*`) **WAJIB berubah menjadi warna merah tegas (`#D9383A !important`)** saat field mengalami error.
        - Selector CSS: `.has-error .form-label`, `.has-error .kezak-label`, `.form-label.error`, `.kezak-label.error`.
     2. **Warna Border Input Field Ikut Merah**:
        - Border input field (`input`, `textarea`, `select`, `.form-control`, `.kezak-input`) **WAJIB berubah menjadi merah tegas (`border: 1.5px solid #E53935 !important; border-color: #E53935 !important;`)**.
        - State focus/aktif saat error: Tetap mempertahankan border merah (`#E53935 !important`) tanpa glow/shadow (`box-shadow: none !important; outline: none !important;`).
        - Background input tetap netral hangat (`var(--color-surface, #f8faf7)`), dilarang diubah menjadi latar belakang pink pekat.
     3. **Warning Message & Ikon Peringatan (Di Bawah Input)**:
        - Pesan error diletakkan tepat di bawah input (`margin-top: 6px - 8px;`).
        - Format: Ikon SVG segitiga peringatan merah (`⚠️`, `<path fill="#D9383A".../>`) di sebelah kiri + teks pesan warna merah tegas (`#D9383A`, font-size `12.5px`, font-weight `500`, line-height `1.4`).
        - **DILARANG mengubah atau menghilangkan ikon dan tipografi warning text ini** karena sudah menjadi standar baku yang disukai pengguna.
   - **Mekanisme Interaksi: Pemulihan Seketika Saat Mengetik (*Instant Typing Recovery*)**:
     - Begitu pengguna mulai mengetik di dalam field input (`addEventListener('input', ...)`):
       a. Pesan warning text di bawah input **langsung hilang seketika**.
       b. Warna border input **langsung kembali normal** (atau hijau primary jika sedang aktif berfokus).
       c. Warna judul/label input **langsung kembali ke warna normal**.
   - **Mekanisme Validasi Saat Submit Kosong (*Empty Submit Handling*)**:
     - Jika pengguna mengklik tombol submit/masuk saat field wajib masih kosong, sistem langsung mengaktifkan state error (judul merah, border merah, warning text muncul, dan auto-focus ke field pertama yang belum terisi).
     - DILARANG memunculkan dialog konfirmasi `AppModal.confirm` atau mengirimkan request form jika validasi field wajib belum terpenuhi.
3. **Standar Tinggi & Proporsi Textarea (Panjang ke Bawah / Lapang)**:
   - Kotak input bertipe textarea (`textarea`, `.form-control`, `alamat`, `deskripsi`, `tugas`, `keterangan`, `catatan`, spesifikasi KIB) **DILARANG KERAS berukuran pendek/gepeng/sempit** (seperti `rows="2"`, `rows="3"`, atau tinggi kurang dari 100px).
   - **WAJIB berukuran panjang ke bawah dan lapang**:
     - Atribut HTML standar: minimal `rows="4"`.
     - Styling CSS standar: `min-height: 120px !important; line-height: 1.5 !important; resize: vertical !important;`.
     - Fleksibilitas interaksi: Pengguna dapat menarik handle resize ke bawah secara vertikal dengan bebas jika membutuhkan area input yang lebih luas.
     - Berlaku seragam di **seluruh formulir sistem**: Tambah/Edit Aset, Tambah/Edit Pengguna, Tambah/Edit Pejabat, Tambah/Edit Ruangan, Tambah/Edit Bidang, Tambah/Edit Dropdown, dan Tambah/Edit Jenis Inventaris.
4. **Standar Lebar Field Bukti Kepemilikan & Grid Simetris Form Aset**:
   - Field `Bukti Kepemilikan (Opsional)` **WAJIB memiliki lebar yang sama dengan field Asal Usul Barang / Pengguna** (yaitu 50% lebar kontainer atau 1 kolom standar di dalam grid 2-kolom `.form-grid`).
   - **DILARANG KERAS** menjadikan field `Bukti Kepemilikan` sebagai full-width (`form-full` / 100%) yang merusak keseimbangan simetri grid input data umum aset.
   - Pembungkus input: `<div class="form-group">` (bukan `<div class="form-group form-full">`).
5. **Standar Hierarki Validasi: Animasi Scroll Halus & Fokus ke Input Field Paling Atas (Topmost First)**:
   - **Hierarki Navigasi Error**: Saat tombol Simpan / Submit ditekan dalam kondisi satu atau beberapa field wajib belum diisi/invalid, seluruh field invalid ditandai, dan animasi smooth scroll (`scrollIntoView({ behavior: 'smooth', block: 'center' })`) serta fokus input **HARUS SELALU DITUJUKAN KE FIELD PALING ATAS (TOPMOST ELEMENT)** dalam tata letak dokumen vertikal, BUKAN field paling bawah yang dekat dengan tombol simpan.
   - Elemen invalid diurutkan secara vertikal berdasarkan `getBoundingClientRect().top + window.scrollY`.
   - Pemanggilan focus wajib menggunakan `{ preventScroll: true }` agar native focus browser Chromium tidak menginterupsi animasi smooth scroll.
   - **Standar Single Icon (Anti Double Icon)**: Kontainer pesan error (`.invalid-feedback`, `.field-error-message`, `.invalid-feedback-custom`, `.kezak-field-error`) **HANYA BOLEH MENAMPILKAN TEPAT 1 IKON PERINGATAN**. Pseudo-element CSS `::before` otomatis dinonaktifkan (`content: none !important; display: none !important;`) jika di dalam kontainer terdapat elemen SVG atau class `.has-svg-icon`.

5. **Standar Tata Letak Form Create & Edit (Grid Kombinasi Horizontal & Vertikal - Mengacu pada Manajemen Aset)**:
   - Seluruh formulir create (`create.blade.php`) dan edit (`edit.blade.php`) di seluruh sistem (Aset, Pengguna, Pejabat, Ruangan, Bidang, Dropdown, Jenis Inventaris, Ubah Password) **WAJIB mengadopsi standar tata letak grid kombinasi horizontal & vertikal**:
     - **Lebar Kontainer**: Seluruh kartu formulir menggunakan lebar penuh `<div class="col-span-12 w-full">`. DILARANG menggunakan `col-span-8` atau `col-span-10` yang menyempitkan tata letak form.
     - **Field 1-Baris Pendek / Berpasangan (Horizontal 50% - 50%)**: Field input bertipe teks singkat, dropdown select, number, status checkbox/toggle diletakkan bersandingan 2 kolom secara horizontal (`.form-group` di dalam `.form-grid`). Contoh: `nama` & `username`, `nama` & `jabatan`, `kode` & `nama`, `nama` & `requires_ruangan`, `password` & `password_confirmation`.
     - **Field Panjang / Textarea (Vertikal 100% Full-Width)**: Field textarea seperti `alamat`, `deskripsi`, `tugas`, `catatan` **WAJIB** membentang penuh 1 baris (`.form-group.form-full` / `grid-column: 1 / -1`). Dilarang meletakkan textarea berdampingan dengan 1-line text input yang membuat tinggi baris tidak simetris.
     - **Responsif Mobile**: Pada resolusi layar ponsel (`max-width: 768px`), grid otomatis bertransformasi menjadi 1 kolom (`grid-template-columns: 1fr`).
6. **Larangan Penggunaan Ikon pada Judul Section (Anti-AI-Slop Header)**:
   - Judul seksi formulir (`.form-section-title`), ringkasan detail (`.portal-section-title`), maupun subseksi **DILARANG KERAS** menggunakan ikon dekoratif (seperti Feather Icons). Cukup teks judul bersih yang lugas dan informatif.

---

## 5.1 Standar Dropdown, Trigger Dropdown, & Searchable Select (Single Source of Truth)

Seluruh komponen dropdown di seluruh halaman form dan tampilan sistem SIMA **WAJIB** diimplementasikan menggunakan komponen **Searchable Dropdown** dengan acuan desain resmi dari **Pusat Ekspor** (`#kib-dropdown-trigger` dan `#buku-inventaris-dropdown-container`).

> [!IMPORTANT]
> **Pengecualian Resmi Standar Dropdown**:
> 1. **Dropdown Kode Barang**: Menggunakan hierarki tree selector / modal pemilihan kode barang khusus.
> 2. **Field Khusus Tanggal, Bulan, dan Tahun**: Menggunakan Flatpickr / picker kalender / month selector khusus.
> Selain dua pengecualian di atas, **SELURUH** field dropdown di formulir (Asal Usul, Bukti Kepemilikan, Ruangan, Pengguna, Role, Bidang, Dinas, Kondisi, custom field dropdown) **WAJIB** membuka panel pencarian dan list opsi bergaya Pusat Ekspor saat di-trigger.

### 1. Acuan Visual (Golden Reference)
- Mengacu pada komponen visual dropdown di `resources/views/export-data/index.blade.php` (`#kib-dropdown-trigger` & `#buku-inventaris-dropdown-trigger`).
- Karakteristik visual: Container warm surface yang tenang, border halus, tinggi mantap (`min-height: 48px`), padding lapang (`12px 18px`), teks placeholder muted yang rapi di kiri, dan icon chevron di kanan yang berputar 180° secara halus saat dropdown terbuka.
- Saat di-trigger: Membuka panel dropdown (`.dropdown-menu-panel`) yang memiliki kolom pencarian (`.dropdown-search-wrapper` dengan input pencarian) dan list opsi (`.dropdown-options-list`) di mana opsi terpilih memiliki background hijau lembut (`var(--color-primary-tint)` / `#d5e4d7`), teks tebal, dan opsi dapat memiliki title + subtitle deskripsi.

### 2. Anatomi & Token Komponen Searchable Dropdown
```blade
<div class="searchable-dropdown" id="contoh-dropdown-container">
    <button type="button" class="dropdown-trigger" id="contoh-dropdown-trigger">
        <span class="dropdown-selected-label" style="color: var(--color-text-muted); font-size: 13px; font-weight: 500;">-- Pilih Opsi --</span>
        <i data-feather="chevron-down" class="dropdown-trigger-caret" style="width: 16px; height: 16px;"></i>
    </button>
    <div class="dropdown-menu-panel" id="contoh-dropdown-menu">
        <div class="dropdown-search-wrapper">
            <i data-feather="search"></i>
            <input type="text" class="dropdown-search-input" placeholder="Cari..." autocomplete="off">
        </div>
        <ul class="dropdown-options-list">
            <li class="dropdown-option-item selected" data-value="val1">
                <div class="dropdown-option-text">
                    <div class="dropdown-option-name">Judul Opsi</div>
                    <div class="dropdown-option-desc">Keterangan / Subtitle</div>
                </div>
            </li>
        </ul>
    </div>
</div>
```

- **Width**: `100%` (atau sesuai lebar kontainer form/toolbar)
- **Min-Height**: `48px !important;` (tinggi yang proporsional untuk interaksi desktop & mobile)
- **Padding**: `12px 18px !important;`
- **Display**: `flex; align-items: center; justify-content: space-between;`
- **Background**: `var(--color-surface, #f8faf7) !important;`
- **Border**: `1px solid var(--color-border, #cfdcc8) !important;`
- **Border Radius**: `var(--radius-sm, 8px) !important;`
- **Cursor**: `pointer !important;`
- **Placeholder Label**: `color: var(--color-text-muted, #546958); font-size: 13px; font-weight: 500;`
- **Selected Label**: `color: var(--color-text, #16251b); font-size: 13.5px; font-weight: 600;`
- **Caret Icon**: `<i data-feather="chevron-down" class="dropdown-trigger-caret"></i>` (ukuran 16px - 18px, warna `var(--color-text-muted)`, transition `transform 0.2s ease`).

### 3. State & Perilaku Interaktif
- **Default State**: Background `var(--color-surface)`, Border `var(--color-border)`, Caret menghadap ke bawah.
- **Hover State**:
  - `border-color: var(--color-primary, #1f3a2a) !important;`
  - `background-color: var(--color-row-hover, #eff5ed) !important;`
- **Open / Active / Focus State**:
  - `border-color: var(--color-primary, #1f3a2a) !important;`
  - `background-color: var(--color-surface, #f8faf7) !important;`
  - `outline: none !important; box-shadow: none !important;` (Zero Shadow Standard)
  - **Rotasi Caret**: Icon chevron otomatis berputar 180 derajat menghadap ke atas (`transform: rotate(180deg);`).
- **Pilihan Terpilih**: Teks berubah menjadi gelap tegas (`var(--color-text)`), font-weight `600`.
- **Search Filtering**: Pencarian live real-time memfilter opsi berdasarkan teks atau keterangan opsi.

### 4. Spesifikasi Panel Menu & Search Input (`.dropdown-menu-panel`)
- **Container Panel**: Background `var(--color-surface)`, Border `1px solid var(--color-border)`, Radius `var(--radius-md, 12px)`, Padding `8px`, Box-shadow `var(--shadow-float)`.
- **Search Box (`.dropdown-search-wrapper`)**:
  - Ikon search `<i data-feather="search"></i>` di posisi kiri (`left: 12px`, warna `var(--color-text-muted)`).
  - Input (`.dropdown-search-input`): Background `var(--color-surface)`, border `1px solid var(--color-border)`, radius `var(--radius-sm)`, padding `8px 12px 8px 34px`, font-size `13px`, fokus `border-color: var(--color-primary)`, zero shadow.
- **List Opsi (`.dropdown-options-list`)**:
  - Item (`.dropdown-option-item`): Padding `10px 12px`, border-radius `var(--radius-sm)`, border-bottom `1px solid var(--color-border)` (item terakhir tanpa border-bottom).
  - Hover Item: `background: var(--color-row-hover);`
  - Selected Item: `background: var(--color-primary-tint, #d5e4d7) !important; color: var(--color-primary, #1f3a2a) !important; font-weight: 600;`
  - Judul Item: Font-size `13px`, font-weight `600`, color `var(--color-text)`.
  - Subtitle / Deskripsi: Font-size `11px`, color `var(--color-text-muted)`.

### 5. Mesin Sinkronisasi Otomatis (`sima-dropdown.js`)
Sistem dilengkapi skrip universal `public/assets/js/sima-dropdown.js` yang secara otomatis meng-upgrade seluruh elemen `<select>` formulir di aplikasi menjadi komponen Searchable Dropdown Pusat Ekspor tanpa merusak validasi HTML5 dan pengiriman form POST Laravel.

### 6. Ketentuan Khusus Dropdown Kondisi Aset
Opsi kondisi aset disajikan to-the-point (*clean & direct*): **`Baik`**, **`Kurang Baik`**, **`Rusak Berat`** tanpa subtitle/keterangan singkatan kode (seperti *B*, *KB*, *RB*) di bawahnya agar tampilan dropdown bersih, padat, dan tidak berlebihan.

### 7. Standar Teks Opsi Default & Trigger Dropdown (Anti-Decorated Double Hyphens)
Seluruh opsi default, placeholder input filter, dan trigger label pada seluruh dropdown (baik komponen filter popover maupun formulir input tambah/edit) **WAJIB ditulis bersih dan profesional tanpa tanda strip ganda `--`** (contoh baku: `"Semua Ruangan"`, `"Tanpa Ruangan"`, `"Semua Tahun"`, `"Pilih Ruangan"`, `"Pilih Asal Usul Barang"`).
**DILARANG KERAS** menggunakan format dekoratif kuno seperti `"-- Semua Ruangan --"`, `"-- Tanpa Ruangan --"`, `"-- Semua Tahun --"`, atau `"-- Pilih ... --"`.

---

## 5.2 Standar Card Pembungkus Info Text & Helper (Wajib Lebar Penuh / Full-Width)

Seluruh kotak/card banner info text, petunjuk pengisian, flow helper, dan catatan peringatan (`.info-box`, `.doc-flow-helper`, `.jenis-flow-helper`, `.order-hint`, `.info-hint`, `.form-hint-box`, `.sima-info-box`) di seluruh tampilan sistem **WAJIB dibungkus dengan kartu yang membentang lebar penuh (100% full-width)** mengikuti lebar kontainer induk/formulir:

1. **Acuan Desain Resmi (Golden Reference)**: Mengacu pada komponen info helper di Pusat Ekspor (`resources/views/export-data/index.blade.php`).
2. **Karakteristik Visual Baku**:
   - `display: flex !important; width: 100% !important; box-sizing: border-box !important;`
   - Background: `var(--color-primary-tint, #d5e4d7) !important;` (warm sage tint khas SIMA).
   - Border: `1px solid var(--color-border, #cfdcc8) !important;`
   - Radius: `var(--radius-sm, 8px) !important;`
   - Padding: `8px 14px !important;`
   - Ikon Info: `<i data-feather="info"></i>` (14px, `color: var(--color-primary)`, `flex-shrink: 0`).
   - Teks Deskripsi: `font-size: 12px; font-weight: 500; color: var(--color-text); line-height: 1.45; flex: 1 !important;`.
3. **DILARANG KERAS Lebar Menciut (`width: fit-content`)**: Dilarang menggunakan `display: inline-flex` dengan `width: fit-content` yang memotong kartu info text di tengah form dan merusak simetri visual antarmuka.
4. **Standar Responsive Mobile (Layout Vertikal & Rata Tengah / Center-Aligned)**:
   - Pada layar mobile (`@media (max-width: 768px)`), seluruh box info (`.info-box`, `.doc-flow-helper`, `.jenis-flow-helper`, `.order-hint`, `.info-hint`, `.info-hint-box`, `.form-hint-box`, `.sima-info-box`) dan box peringatan kuning (`.warning-box`, `.sima-warning-box`) **WAJIB bertransformasi menjadi layout vertikal rata tengah**:
     - `flex-direction: column !important; align-items: center !important; justify-content: center !important; text-align: center !important;`
     - Ikon info/warning berada di atas secara horizontal di tengah (`margin: 0 auto !important;`).
     - Teks deskripsi berada di bawah ikon dengan lebar 100% penuh dan rata tengah (`text-align: center !important; width: 100% !important;`).
   - **Tujuan / Rasional**: Mencegah pemenuhan ruang horizontal berlebihan oleh ikon di sebelah kiri pada layar mobile sempit yang memaksa teks tertekan dan melorot turun menjadi terlalu banyak baris vertikal. Dengan ikon di atas dan teks di bawah, teks mendapatkan 100% lebar kontainer secara proporsional.
   - Pada desktop (> 768px), layout **tetap horizontal** (ikon di kiri dan teks di kanan).
5. Aturan teknis lengkap tercatat di `.agents/rules/full-width-info-boxes.md`.

---

## 5.3 Standar Area Unggah Berkas & Ikon Upload (Clean upload-cloud Icon Polos, Dilarang Card/Badge/Background Box)

Seluruh area unggah berkas (*file upload area*, dropzone, modal import data, unggah foto inventaris, unggah tanda tangan elektronik, dan unggah dokumen bukti) di seluruh antarmuka SIMA:

1. **Wajib Ikon `upload-cloud` Polos (*Clean Naked Icon*)**:
   - Seluruh ikon upload menggunakan ikon **`upload-cloud`** (Feather Icons) dengan ukuran standar `28px`, `stroke-width: 1.5`, dan warna `var(--color-text-muted)`.
2. **DILARANG KERAS Card / Badge / Kotak Pembungkus Ikon**:
   - Dilarang membungkus ikon upload dengan elemen kotak berlatar belakang gradien/warna (`.file-upload-icon` dengan background gradien hijau, border-radius kotak, dsb.).
   - Ikon tampil langsung (*naked/clean icon*) di tengah dropzone tanpa badge atau card pembungkus.
3. **Pertahankan Teks Label & Hint Asli**:
   - Teks instruksi utama dan catatan hint batas ukuran/format file tetap dipertahankan sesuai kebutuhan masing-masing halaman.
4. Aturan teknis lengkap tercatat di `.agents/rules/clean-upload-icons.md`.

---

## 6. Standar Tautan & Tombol Aksi (Buttons & Links)

1. **STANDAR BENTUK TOMBOL WAJIB PILL-ROUNDED (`border-radius: 9999px`)**:
   - **SEMUA tombol aksi** di seluruh sistem (Primary, Secondary, Danger/Hapus, Warning, Success, Submit Form, Cancel/Batal, Modal Konfirmasi/Batal, dan Bulk Actions) **WAJIB berbentuk Pill Rounded (`border-radius: var(--radius-full)` / `9999px`)**.
   - **DILARANG KERAS tombol aksi berbentuk KOTAK** (sudut tajam atau lengkungan kecil kaku seperti `border-radius: 4px`, `6px`, `8px`, `10px`, `12px`, `var(--radius-sm)`, maupun `var(--radius-md)`).
   - Jangan pernah menampilkan tombol pill kapsul (misal *Tandai Semua Dibaca*) berdampingan dengan tombol kotak kaku (misal *Hapus Sudah Dibaca*). Seluruh jajaran tombol aksi dalam satu kesatuan wajib seragam berpenampilan pill kapsul elegan.

2. **Varian Tombol Sistem**:
   - **Primary (`btn-sima-primary`, `btn-primary`, `btn-submit`)**:
     - Background: `var(--color-primary)` (Dark Forest Green `#1f3a2a`)
     - Teks: `var(--color-text-on-primary)` (#FFFFFF / #f8faf7), font-weight: `500`/`600`
     - Border: `1px solid var(--color-primary)`
     - Shape: `border-radius: var(--radius-full)` (Pill kapsul)
     - Padding: `8px 20px` (atau `8px 18px`), font-size: `14px`
     - **Maksimal satu tombol primary per tampilan layar.**
   - **Secondary (`btn-sima-secondary`, `btn-secondary`, `btn-cancel`)**:
     - Background: transparan (atau `var(--color-surface)`)
     - Teks: `var(--color-text)` (`#16251b`), font-weight: `500`
     - Border: `1px solid var(--color-border)` (`#cfdcc8`)
     - Shape: `border-radius: var(--radius-full)` (Pill kapsul)
     - Padding: `8px 18px`
   - **Danger / Destructive (`btn-sima-danger`, `btn-danger`, `bulk-btn-delete`)**:
     - Background: `var(--color-danger-soft)` (`#FBE8E6`)
     - Teks: `var(--color-danger)` (`#A63D2F` / `#991b1b`)
     - Border: `1px solid var(--color-danger-soft)`
     - Shape: `border-radius: var(--radius-full)` (Pill kapsul)
     - Padding: `8px 18px`
     - Hover: background `#ebd0c8` dengan teks tetap tajam
   - **Modal Buttons (`.modal-btn`, `.modal-btn-primary`, `.modal-btn-secondary`, `.modal-btn-danger`, `.modal-btn-warning`)**:
     - Shape: `border-radius: var(--radius-full)` (Pill kapsul)
   - **Floating Bulk Action Bar Buttons (`.bulk-btn-delete`, `.bulk-btn-restore`, `.bulk-btn-cancel`)**:
     - Shape: `border-radius: var(--radius-full)` (Pill kapsul)

3. **DILARANG Menggunakan Banner Tombol Kotak Kuning/Alert untuk Tautan Biasa**:
   - Dilarang membungkus tautan navigasi dengan kotak tombol berwarna kuning alert (seperti `background: #fffbeb; border: 1px solid #fef3c7; color: #b45309;`).
   - Tautan navigasi harus dibuat sebagai **clean text link** yang menyatu rapi dengan card.

4. **Standar Ikon Navigasi ("Lihat Lebih Banyak / Detail")**:
   - Teks yang mengajak pengguna melihat rincian (*"Lihat semua jenis"*, *"Lihat semua aktivitas"*, *"Lihat ... aset yang belum lengkap"*, *"Buka"*, *"Detail"*):
   - **WAJIB menggunakan ikon `chevron-right`**: `<i data-feather="chevron-right" style="width: 14px; height: 14px;"></i>`.
   - **DILARANG menggunakan `arrow-right`** maupun karakter entitas panah teks seperti `&rarr;`, `&gt;`, `→`, atau `>`.
   - Layout:
     ```blade
     <a href="..." style="font-size: 12px; color: var(--color-primary); font-weight: 600; text-decoration: none; display: inline-flex; align-items: center; gap: 4px;">
       <span>Lihat semua jenis</span>
       <i data-feather="chevron-right" style="width: 14px; height: 14px;"></i>
     </a>
     ```

5. **DILARANG KERAS Teks atau Tombol Berwarna Kuning/Amber untuk Navigasi**:
   - Dilarang membuat link, teks pengarah, atau button apapun dengan warna kuning/amber (`#C9A24A`, `#B7791F`, `#F59E0B`, `var(--color-accent)`).
   - Warna kuning **HANYA DAN EKSKLUSIF** difungsikan untuk data kosong/belum ditentukan (`.text-empty-fallback`).
   - Seluruh teks dan tautan navigasi (seperti *"Pusat Ekspor"*, *"Buka"*, *"Lihat"*, *"Cetak KIB"*) **WAJIB** menggunakan warna brand hijau primer `var(--color-primary)`.

6. **Standar Tombol Reset Filter & Animasi Icon Putar Halus (Counter-Clockwise Spin)**:
   - **Bentuk & Class Tombol**: Seluruh tombol Reset Filter di sistem **WAJIB** menggunakan class `.btn-sima-secondary.btn-reset-filter` (berbentuk Pill Kapsul `border-radius: var(--radius-full)`).
   - **Icon Standar**: Menggunakan ikon Feather `rotate-ccw` (`<i data-feather="rotate-ccw"></i>`).
   - **Perilaku Animasi Saat Dipencet**:
     - Saat tombol Reset Filter dipencet/diklik di tampilan manapun dalam sistem, icon `rotate-ccw` **WAJIB berputar penuh 360 derajat berlawanan jarum jam (counter-clockwise)** secara halus (`animation: sima-spin-reset 0.65s cubic-bezier(0.25, 1, 0.5, 1)`).
     - Titik pusat rotasi wajib di tengah (`transform-origin: center center !important`).
     - Terdapat micro-interaction tactile feedback saat hover berupa sedikit rotasi -20° (`transform: rotate(-20deg)`).
   - **Implementasi Universal (Zero Redundancy)**:
     - Logika animasi ditangani otomatis secara global pada level capture-phase di `public/assets/js/sima-dropdown.js` (menangkap klik pada `button[id*="reset-filter"]`, `.btn-reset-filter`, dan `button[onclick*="resetFilter"]`).
     - Keyframe dan styling terdaftar secara global di `public/assets/css/design-system.css` (`@keyframes sima-spin-reset` dan class `.is-spinning-reset`).
     - Diterapkan di seluruh 12 titik kontrol sistem: Manajemen Aset (Buku Inventaris), Penghapusan Aset (Tab Aktif & Riwayat), Pengguna (Users), Pejabat, Ruangan, Bidang, Jenis Inventaris, Dropdown Dinamis, Riwayat Ekspor, Log Aktivitas Dashboard, dan Notifikasi.

7. **Standar Mutlak Interaksi Hover (Zero Shadow on Hover Across All Elements)**:
   - **KONSEP MUTLAK SISTEM**: Seluruh elemen yang ada di sistem ini (tombol primary, secondary, submit, aksi tabel, link navigasi, kartu/card, baris tabel, status badge/pill, gambar/foto, avatar, widget, input, dan kontainer) **DILARANG KERAS memunculkan, mempertebal, atau mengubah ox-shadow saat state :hover**.
   - **Bentuk Efek Hover yang Diizinkan**: Interaksi hover hanya boleh berupa animasi halus berupa:
     1. Perubahan warna latar belakang (ackground-color / background tint).
     2. Perubahan warna border (order-color).
     3. Perubahan warna teks atau ikon (color).
     4. Translasi fisik mikro yang halus (seperti 	ransform: translateY(-1px) atau 	ransform: scale(1.02)).
   - Efek glow, bayangan elevation, penambahan shadow baru, atau pelebaran shadow saat hover adalah **pelanggaran standar visual SIMA Desa** dan dilarang di seluruh modul sistem.

---

## 7. Mekanisme Fallback Data Kosong (Empty Data Fallback)

1. **DILARANG Membungkus Data Kosong dengan Card, Pill, atau Badge**:
   - Jika data kolom/relasi bernilai `null`, kosong, atau belum ditentukan (misal: Ruangan, Tanggal, Pengguna):
   - Dilarang memberi kotak background badge (`#fffbeb`, padding box, border badge).
2. **Wajib Ditampilkan sebagai Plain Inline Text**:
   - Tampilkan sebagai teks polos langsung di dalam cell tabel/elemen:
   - Warna teks: Warm Golden Amber **`#B7791F`** / `var(--color-warning)` (font-weight `500`, font-size `13px`).
   - Teks: `"Belum ditentukan"` (atau tanda `"-"` jika kolom sangat sempit).
3. **Standar Tampilan Halaman / Tabel Data Kosong (Empty State - Acuan Resmi Ekspor Riwayat)**:
   - Ketika sebuah halaman, tabel, atau tampilan memanggil data namun datanya belum ada atau kosong, tampilan empty state **WAJIB mengacu pada golden reference `resources/views/export-data/history.blade.php`**:
     ```blade
     <div style="display: flex; flex-direction: column; align-items: center; justify-content: center; gap: 8px;">
         <i data-feather="inbox" style="width: 42px; height: 42px; stroke-width: 1.5; color: var(--color-border-dark);"></i>
         <p style="margin: 0; font-size: 13.5px; font-weight: 500;">Belum ada [data yang dimaksud] yang sesuai</p>
     </div>
     ```
   - **DILARANG KERAS MEMAKAI CARD / LINGKARAN PEMBUNGKUS IKON**:
     - Dilarang membungkus ikon dalam wadah bulat/persegi berwarna (`.empty-icon-box`, background hijau muda, border bulat).
     - Dilarang membuat card pembungkus tambahan di sekeliling icon.
     - Ikon diletakkan **langsung (bare icon)** berdiri bebas di atas kanvas/tabel dengan ukuran mantap `42px x 42px`, garis halus `stroke-width: 1.5`, dan warna `var(--color-border-dark)` / `var(--color-text-muted)` (`#546958`).
     - Teks keterangan di bawah ikon berukuran `13.5px`, `font-weight: 500`, warna `var(--color-text-muted)`. Cukup 1 baris *to the point* tanpa menumpuk heading tebal dan paragraf redundan.

---

## 8. Standar Filter & Tab Baris

- Garis indikator filter hijau di bawah tab hanya muncul pada tab/indikator yang **sedang aktif**.
- **DILARANG menampilkan garis panjang default abu-abu/hijau** di bawah seluruh deretan filter yang tidak aktif.
- **DILARANG Menggunakan Card / Box Pembungkus Luar pada Tab**: Tab navigasi wajib berdiri bebas langsung di atas permukaan halaman/kanvas (`.tabs-sima`, `.tab-item-sima`, `.tab-badge-sima`), bukan dibungkus dalam card box bergaris tepi (seperti acuan pada modul Ruangan). Garis indikator bawah hijau (`border-bottom: 2px solid var(--color-primary)`) hanya ada pada tab aktif.
- **Card Angka / Badge pada Tab (Aktif vs Inaktif)**:
  - Saat tab **aktif** ("Semua", "Belum Dibaca", dsb.), badge angka **WAJIB menggunakan warna hijau** (`background: var(--color-primary-tint, #d5e4d7); color: var(--color-primary, #1f3a2a); border-color: transparent; font-weight: 600;`).
  - Dilarang menggunakan badge kotak abu-abu surface/border kusam pada tab aktif.
  - Tab "Belum Dibaca" saat **tidak aktif** menggunakan warna soft amber warning (`background: var(--color-warning-soft, #F4E7C3); color: var(--color-warning, #B7791F); border-color: transparent;`). Ketika diklik/aktif, badge otomatis bertransformasi menjadi hijau soft standar aktif.

---

## 9. Kejujuran Data (*Data Integrity Standards*)

1. **Mata Uang & Angka (Standar Aset Desa Demo — DILARANG Singkatan)**:
   - Sesuai standar nilai yang diacu pada proyek Sistem Manajemen Aset Desa Demo, **SEMUA data yang berhubungan dengan angka dan nilai rupiah DILARANG KERAS disingkat** (seperti *"miliar"*, *"juta"*, *"M"*, *"jt"*, *"k"*, dsb.).
   - Setiap nilai rupiah pada dashboard, Donut Chart (label tengah & tooltip), KPI, breakdown list, dan tabel **WAJIB dijabarkan spesifik dan sejelas-jelasnya secara penuh** dengan format mata uang standar Indonesia (`Rp` diikuti pemisah ribuan titik `.`, contoh: `Rp 19.327.989.966`, `Rp 8.293.450.000`).
   - Aturan lengkap tercatat di `.agents/rules/unabbreviated-numbers.md`.
2. **Kondisi Fisik**:
   - Angka kondisi baik, rusak ringan, dan rusak berat dihitung langsung dari kolom `kondisi` di database (`baik`, `rusak_ringan`, `rusak_berat`).
   - Jangan menampilkan angka 0 jika data sebenarnya ada, dan jangan memalsukan angka 100% jika data belum lengkap.
3. **Data Pejabat**:
   - Status pejabat diverifikasi dari kolom `is_aktif`. Dilarang mengarang kata "SK" jika tidak ada field SK pada entitas pejabat desa.
4. **Kategori & Nomenklatur**:
   - Gunakan istilah Kartu Inventaris Barang (**KIB**) dan **Jenis Inventaris** sesuai regulasi penatausahaan aset desa.

---

## 10. Daftar Tanda "AI-Slop" yang DILARANG KERAS

| Tanda AI Slop yang Dilarang | Pengganti yang Benar & Bersih |
| :--- | :--- |
| **Garis warna di pinggir kiri card (`border-left: 3px solid ...`)** | Container bersih netral dengan background surface polos |
| **Garis pembatas solid (`border-bottom: 1px solid ...`) pada toolbar/footer** | Garis putus-putus halus (*dashed line* `1px dashed var(--color-border)`) |
| **Kotak tombol banner kuning alert (`#fffbeb`) untuk navigasi** | Clean text link warna `var(--color-primary)` dengan ikon `chevron-right` |
| **Teks link / tombol navigasi berwarna kuning atau aksen emas** | Teks link standar warna `var(--color-primary)` (warna kuning HANYA untuk data kosong) |
| **Badge / Pill pembungkus untuk data kosong ("Belum ditentukan")** | Teks polos (*plain text*) warna `#B7791F` (`var(--color-warning)`) tanpa background/border |
| **Ikon panah lurus `arrow-right` atau karakter `&rarr;` pada link rincian** | Ikon `chevron-right` rapi |
| **Box-shadow tebal / glowing ring pada input form saat aktif** | Zero shadow (`box-shadow: none !important`), fokus hanya border tipis |
| **Gradien ungu-biru, neon, glow, blur kaca (glassmorphism)** | Flat clean warm ivory & forest green (`--color-surface`, `--color-primary`) |
| **Kata-kata abstrak promosi ("Prima", "Tertib Arsip", "Sinkron")** | Teks faktual, deskriptif, dan operasional |
| **Hardcoded "100% Beres" di semua card** | Hitungan dinamis riil dari database |
| **Eyebrow ALL CAPS di atas setiap heading tanpa informasi** | Hierarki heading natural tanpa teks pengisi |
| **Trigger dropdown kaku / panah default browser hitam** | Trigger `.dropdown-trigger` warm surface, padding 12px 18px, chevron halus rotasi 180° |
| **Header tunggal terpisah yang informasinya diulang di section bawahnya (AI-slop header redundan)** | Tempatkan informasi langsung menyatu di dalam section terstruktur/ringkasan data tanpa header banner duplikat |
| **Ikon dekoratif di samping judul section (`<i data-feather="...">`)** | Teks judul polos (*clean plain heading*) tanpa ikon AI-slop |
| **Form satu kolom gepeng atau card sempit 8-kolom di desktop** | Standar grid kombinasi horizontal (50%-50%) & vertikal (100% textarea) pada card 12-kolom w-full |
| **Card / Lingkaran pembungkus icon pada tampilan data kosong (`.empty-icon-box`)** | Bare icon langsung (42px) di atas teks tanpa card/lingkaran pembungkus |
| **Pill / Badge pembungkus aksi pada kolom Aktivitas** | Teks polos bersih (*plain text*) langsung di dalam sel `<td>` tanpa badge pill pembungkus |
| **Card / Box pembungkus luar pada deretan tab navigasi** | Underline tab standar (`.tabs-sima`) langsung di atas kanvas dengan garis bawah aktif hijau |
| **Kotak / Card info text menciut terpotong (`width: fit-content`)** | Card pembungkus info text membentang lebar penuh 100% (*full-width*) simetris dengan kontainer |
| **Card / Kotak gradien pembungkus ikon upload berkas (`.file-upload-icon`)** | Ikon `upload-cloud` polos (*clean naked icon*) tanpa card/badge pembungkus |
| **Garis divider di bawah judul modal atau di atas tombol footer modal** | Tanpa garis (`border: none !important`), divider hanya pemisah internal antar-section |
| **Icon "X" di pojok modal saat footer sudah memiliki tombol "Batal"** | Hilangkan icon "X", gunakan tombol "Batal" di footer sebagai aksi penutup |

---

## 11. Standar Pop-up & Modal Dialog (Divider & Close Button Rules)

Berlaku untuk **seluruh modal, dialog, dan pop-up** di seluruh sistem (modal import, formulir penghapusan aset resmi, restore aset, konfirmasi duplikasi, export preview, QR code, dsb.):

### 1. Aturan Garis Divider (Pemisah Konten Internal Saja)
- **HANYA Sebagai Pemisah Antar-Section**: Garis divider (`1px dashed var(--color-border)`) hanya diizinkan sebagai pemisah internal antar-bagian (*section-to-section*) di dalam body modal jika diperlukan.
- **DILARANG Garis di Bawah Judul / Header Modal**: Dilarang meletakkan garis divider (baik border solid maupun dashed) langsung di bawah judul atau header modal (`.modal-header`, `.modal-sima-header`, `.qr-modal-header` wajib `border-bottom: none !important;`).
- **DILARANG Garis di Atas Footer Tombol Aksi**: Dilarang meletakkan garis divider antara section konten terakhir dan footer tombol aksi (`.modal-footer`, `.modal-sima-footer`, `.qr-modal-footer` wajib `border-top: none !important;`).

### 2. Aturan Tombol Tutup "X" di Pojok Kanan Atas Modal
- **JIKA Ada Tombol "Batal" di Footer**: Jika modal memiliki tombol "Batal" (atau cancel button sejenis) di footer bawah (biasanya 2 tombol: Batal dan tombol aksi misalnya Tambah / Simpan / Hapus), maka **DILARANG KERAS** menampilkan tombol icon "X" di pojok kanan atas header.
- **JIKA TIDAK Ada Tombol "Batal" di Footer**: Tombol icon "X" di pojok kanan atas header **HANYA** diimplementasikan jika footer modal tidak menyediakan tombol "Batal" (misalnya modal yang footernya hanya berisi tombol "Unduh" dan "Cetak").

---

## 13. Standar Tata Letak Responsif Mobile Pagination (Acuan Baku: Penghapusan Aset)

Seluruh komponen pagination di seluruh modul tabel dan tampilan data SIMA (Penghapusan Aset, Manajemen Aset, Manajemen Pengguna, Manajemen Pejabat, Manajemen Ruangan, Manajemen Bidang, Dropdown Dinamis, Jenis Inventaris, Riwayat Ekspor, Log Aktivitas, Notifikasi) **WAJIB** menerapkan tata letak responsif mobile terpusat dengan struktur **4-Tier Vertikal** mengacu pada desain baku **Penghapusan Aset**:

### 1. Struktur 4-Tier Vertikal Terpusat (`@media (max-width: 768px)`)
```
┌────────────────────────────────────────────────────────┐
│                Tampilkan [ 10 ˅ ] data                 │  <-- Tier 1: Per-page dropdown ringkas
│                                                        │
│           [ < ] [ 1 ] [ 2 ] [ 3 ] [ 4 ] [ 5 ] [ > ]    │  <-- Tier 2: Nomor halaman pill button
│                                                        │
│                       Ke hal. [ 1 ]                    │  <-- Tier 3: Lompat ke halaman mandiri
│                                                        │
│              Menampilkan 1-10 dari 441 data            │  <-- Tier 4: Keterangan rentang data
└────────────────────────────────────────────────────────┘
```

1. **Tier 1 (Per-Page Selector - Baris 1)**:
   - Format teks: `Tampilkan [ 10 ˅ ] data` (`order: 1 !important;`).
   - **DILARANG** menggunakan teks panjang seperti *"data per halaman"* atau *"Baris per hal:"* yang memakan ruang horizontal layar ponsel.
   - Posisi: Terpusat secara horizontal (`justify-content: center; margin: 0 auto;`).
2. **Tier 2 (Page Numbers Navigation - Baris 2)**:
   - Deretan tombol navigasi halaman (`< 1 2 3 4 5 >`) diletakkan pada barisnya sendiri secara mandiri (`order: 2 !important; width: 100% !important;`).
   - Tombol halaman berukuran kompak (`32px x 32px`, font-size `12.5px`, radius kapsul/pill) dengan jarak antar-tombol `gap: 4px`.
   - Terpusat secara horizontal di tengah kontainer kartu.
3. **Tier 3 (Jump to Page Input - Baris 3)**:
   - Komponen *"Ke hal. [ 1 ]"* diletakkan pada baris tersendiri langsung di bawah tombol nomor halaman (`order: 3 !important; width: auto !important;`).
   - **DILARANG** diletakkan berdampingan secara horizontal dengan tombol navigasi nomor halaman pada mobile karena menyebabkan desak-desakan dan overflow horizontal.
   - Input kotak lompat halaman berukuran proporsional: `width: 52px; height: 34px; text-align: center;`.
4. **Tier 4 (Range Info Text - Baris 4)**:
   - Teks ringkasan data (*"Menampilkan 1-10 dari 441 data"*) diletakkan di dasar kartu (`order: 4 !important;`).
   - Tipografi: `font-size: 12.5px; color: var(--color-text-muted); text-align: center; width: 100%;`.

### 2. Aturan Teknis CSS Global (`resources/views/layouts/head-css.blade.php`)
- **Container Utama**: `.pagination-bar` dan `.pagination-sima-container` wajib `display: flex !important; flex-direction: column !important; align-items: center !important; justify-content: center !important; text-align: center !important; gap: 12px 14px !important; padding: 16px 12px 20px 12px !important; width: 100% !important; overflow-x: hidden !important;`.
- **Unpack Sub-Containers**: `.pagination-left` dan `.pagination-center` menerapkan `display: contents !important;` sehingga keempat elemen tier dapat disusun rapi menggunakan `order: 1` s/d `order: 4`.
- **Zero Horizontal Overflow**: Seluruh baris dijamin tidak memiliki horizontal scrollbar atau clipping di tepi layar ponsel.

---

## 14. Standar Isolasi Dropdown & Pencegahan Invisible Phantom Overlay pada Layar Mobile

### 1. Masalah & Akar Masalah (Root Cause)
Pada template Bootstrap/Tailwind bawaan, menu dropdown seperti User Profile (`.dropdown-user-profile`) dan Notifikasi (`.dropdown-notification`) pada keadaan tertutup (`:not(.drp-show):not(.show)`) hanya disembunyikan menggunakan manipulasi `opacity: 0` dan transform `scale(0)`.
Ketika aturan CSS responsif mobile menerapkan override `transform: none !important`, skala nol tersebut terhapus. Akibatnya:
- Kontainer dropdown menjadi berukuran penuh (lebar 280–360px dan tinggi ~200px) mengambang di bawah topbar dengan status tidak kasat mata (`opacity: 0`).
- Karena `z-index` header tinggi (`1020+`), link di dalam menu (seperti *Ganti Kata Sandi* / `/profile/change-password`, *Profil Pengguna*, atau link notifikasi) secara tidak sengaja **menindih tombol aksi halaman** yang berada di bawahnya (seperti tombol *Ekspor Data* pada Penghapusan Aset dan tombol *Import CSV* pada Manajemen Aset).
- Pengguna yang berniat mengklik tombol *Ekspor Data* atau *Import CSV* pada layar ponsel malah mengeksekusi klik pada link tidak kasat mata tersebut dan terlempar ke halaman lain.

### 2. Aturan Baku Pencegahan (Universal Dropdown Safety)
1. **Keadaan Tertutup (`:not(.drp-show):not(.show)`)**:
   - Wajib menerapkan isolasi penuh:
     ```css
     .pc-header .pc-h-item.dropdown:not(.drp-show):not(.show) > .dropdown-menu,
     .dropdown:not(.drp-show):not(.show) > .dropdown-menu,
     .dropdown-menu:not(.show):not(.drp-show),
     .dropdown-user-profile:not(.show):not(.drp-show),
     .dropdown-notification:not(.show):not(.drp-show) {
       display: none !important;
       visibility: hidden !important;
       pointer-events: none !important;
       opacity: 0 !important;
     }
     ```
   - **DILARANG KERAS** hanya mengandalkan `opacity: 0` atau `transform: scale(0)` untuk menyembunyikan elemen dropdown.
2. **Keadaan Terbuka (`.drp-show` atau `.show`)**:
   - Ditampilkan dengan `display: block !important; visibility: visible !important; pointer-events: auto !important; opacity: 1 !important; z-index: 1060 !important;`.
   - Bounding rect dipastikan berada di dalam batas viewport layar ponsel (`max-width: calc(100vw - 24px)`).

---

## 15. Standar Dropzone & Live Counter Unggah Multi-Foto (Multi-Image Dynamic Dropzone & Counter Standard)

Pada setiap form input unggah foto atau berkas yang dapat menampung lebih dari 1 file (contoh: Foto Aset pada modul Manajemen Aset `create` dan `edit` dengan batas maksimal 5 foto):

### 1. Keadaan Awal (0 Foto Tersimpan / Dipilih)
- Teks utama pada area dropzone (`.file-upload-text`) menampilkan instruksi ajakan awal standar:
  - Pada halaman Tambah: `"Klik atau drag & drop untuk upload foto"`
  - Pada halaman Edit: `"Klik atau drag & drop untuk tambah foto baru"`
- Teks petunjuk (`.file-upload-hint`) mencantumkan format dan batas ukuran file yang didukung:
  `"Format: JPG, PNG, WEBP — Maks. 5 foto, setiap foto ≤5MB (total ≤25MB)"`

### 2. Keadaan Dinamis Live Counter (Ketika Ada ≥ 1 Foto)
- Begitu pengguna menambahkan foto baru (atau ketika membuka halaman edit yang sudah memiliki foto lama yang tersimpan), teks utama `.file-upload-text` secara otomatis dan real-time berubah menjadi counter dinamis:
  - Format: `Tambahkan foto lagi (X/MAX)` (contoh: `Tambahkan foto lagi (1/5)`, `Tambahkan foto lagi (2/5)`).
- Nilai `X` menghitung total akumulasi foto aktif saat ini:
  - Pada mode Create: `selectedFiles.length`.
  - Pada mode Edit: `existingCount() + newSelectedFiles.length`.

### 3. Keadaan Batas Maksimal (X = MAX)
- Jika jumlah foto telah mencapai batas maksimum (5/5):
  - Teks berubah menjadi: `Batas maksimal foto tercapai (5/5)`.
  - Mengklik area dropzone tidak akan membuka dialog file baru dan secara ramah menampilkan dialog notifikasi batas maksimal upload (`AppModal.error`).

### 4. Reversible State (Ketika Foto Dihapus)
- Jika pengguna menghapus foto satu per satu (melalui tombol silang merah `×` pada thumbnail):
  - Counter langsung berkurang secara live (misalnya dari `2/5` kembali menjadi `1/5`).
  - Jika seluruh foto sementara atau foto yang ada dihapus hingga tersisa 0 foto (`X === 0`):
    - Teks label `.file-upload-text` **wajib kembali secara instan** ke kalimat instruksi awal:
      - Create: `"Klik atau drag & drop untuk upload foto"`
      - Edit: `"Klik atau drag & drop untuk tambah foto baru"`

---

## 16. Standar Dimensi & Proporsi Showcase Foto Aset (Standardized Asset Photo Showcase & Contain Sizing)

Pada kartu detail/show aset (Hero Showcase 2-Kolom):

### 1. Ketinggian Baku Terkunci Rata Ujung Bawah Card (Symmetric Height & Bottom Line Alignment)
- Bingkai foto utama showcase (`.portal-gallery-main`) memiliki dimensi dan ketinggian yang **selalu identik 100% dan garis batas bawahnya rata sempurna sejajar dengan ujung bawah kartu Barcode Sistem SIMA Desa** pada kolom kanan, baik saat aset belum memiliki foto (placeholder), memiliki 1 foto, maupun memiliki 2+ foto.
- **Konsistensi Visual Mutlak (Ukuran Frame Tidak Pernah Menyusut)**:
  - Saat aset **belum memiliki foto** (placeholder): Kotak `.portal-gallery-main` membentang penuh hingga ujung bawah kartu Barcode Sistem SIMA Desa.
  - Saat aset memiliki **1 foto**: Bingkai `.portal-gallery-main` membentang penuh dengan ukuran dan garis batas bawah yang identik presisi.
  - Saat aset memiliki **2+ foto**: Bingkai `.portal-gallery-main` **TETAP membentang penuh 100% dengan ukuran dan garis batas bawah yang sama persis (tidak boleh menyusut)**. Deretan thumbnail (`.portal-gallery-thumbs`) ditempatkan sebagai *floating overlay* di pojok kiri bawah bingkai foto utama dengan styling glassmorphism (`position: absolute; bottom: 12px; left: 12px; z-index: 5;`). Hal ini menjamin garis bawah bingkai foto selalu simetris dan sejajar tanpa celah dengan kartu Barcode di sebelah kanan.

### 2. Standar Penskalaan Foto (Wajib `object-fit: contain`)
- **DILARANG KERAS** menggunakan `object-fit: cover` pada foto utama showcase aset karena akan memotong tepi foto secara paksa (membuat foto lanskap terpotong kiri-kanan dan foto persegi/portrait terpotong atas-bawah).
- **WAJIB menggunakan `object-fit: contain !important;`** dengan padding internal `12px` dan latar belakang kanvas natural (`var(--color-bg, #eaf1e7)`).
- Foto dengan rasio apapun (16:9 lanskap, 1:1 bujur sangkar, 3:4 portrait, maupun foto resolusi tinggi) dijamin **100% utuh, tajam, terpusat simetris, dan tidak mengalami distorsi maupun pemotongan bagian gambar**.
- Aturan teknis lengkap tercatat di `.agents/rules/portal-detail-standard.md`.

---

## 17. Referensi Desain Resmi

- **Dashboard & Analitika**: [Custora Customer Success Platform (Dribbble)](https://dribbble.com/shots/27696220-Custora-Customer-Success-Platform)
- **Komponen Form & Validasi Input**: [Creating Perfect Input Field UI Design for a Slick UX (Dribbble)](https://dribbble.com/shots/23079555-Creating-Perfect-Input-Field-UI-Design-for-a-Slick-UX)
- **Form Sections & Inputs Hierarchy**: [Input Fields Forms UI (Dribbble)](https://dribbble.com/shots/22201515-Input-Fields-Forms-UI)