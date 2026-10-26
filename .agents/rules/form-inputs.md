# Form Inputs & Validation Error UI Standard

## 1. Zero Shadow on Focus & Active Form Fields
- Semua form fields (`input`, `textarea`, `select`, `.form-control`, `.form-select`, `.kezak-input`, `.search-box`, dll.) saat di-klik, di-ketik, di-fokuskan, atau aktif:
  - **DILARANG menerapkan `box-shadow` apapun (glow/ring shadow).**
  - Harus selalu `box-shadow: none !important;` dan `outline: none !important;`.
  - Indikator fokus hanya berupa perubahan warna border (`border-color: var(--color-primary, #1f3a2a)`), tanpa efek bayangan/shadow di luar maupun di dalam input.

## 2. Input Field Error State Standard (Dribbble Slick UX Pattern)
Referensi: [Dribbble - Creating Perfect Input Field UI Design for a Slick UX](https://dribbble.com/shots/23079555-Creating-Perfect-Input-Field-UI-Design-for-a-Slick-UX)

- **DILARANG** menampilkan error alert banner besar/floating kotak merah muda di atas form/input field.
- **Wajib** menempatkan pesan error tepat di **bawah input field** masing-masing (`.invalid-feedback` atau `.kezak-field-error`).
- **Aturan Visual State Error Terintegrasi:**
  1. **Judul / Label Input Field Berubah Merah**:
     - Label field (`.form-label`, `.kezak-label`) dan tanda bintang required (`.req`, `*`) **WAJIB ikut berubah warna menjadi merah tegas (`#D9383A !important`)** saat field mengalami error atau kosong.
  2. **Border Input Field Berubah Merah**:
     - Border input (`input`, `textarea`, `select`, `.form-control`, `.kezak-input`) **WAJIB berubah menjadi merah tegas (`border: 1.5px solid #E53935 !important; border-color: #E53935 !important;`)**.
     - State focus saat error tetap mempertahankan border merah `#E53935` tanpa bayangan (`box-shadow: none !important; outline: none !important;`).
  3. **Background Input**:
     - Tetap menggunakan background surface netral (`var(--color-surface)` / `#f8faf7`), dilarang background pink pekat.
  4. **Pesan Error & Ikon Peringatan (Di Bawah Input)**:
     - Menggunakan ikon SVG segitiga peringatan merah (`⚠️`, `<path fill="#D9383A".../>`) di sisi kiri teks.
     - Tipografi: font-size `12.5px`, font-weight `500`, warna teks `#D9383A` (merah tegas dan rapi).
     - Margin-top: `6px - 8px`, layout `display: flex; align-items: flex-start; gap: 6px;`.
     - **DILARANG mengubah atau menghilangkan style icon dan teks warning ini**.
  5. **Mekanisme Pemulihan Otomatis Saat Mengetik (*Instant Typing Recovery*)**:
     - Begitu pengguna mulai mengetik di input (`input` event):
       - Pesan warning text **langsung hilang seketika**.
       - Border merah **langsung kembali normal** (atau hijau primary saat fokus).
       - Judul/label merah **langsung kembali ke warna teks normal**.
  6. **Validasi Klik Submit Saat Kosong (*Empty Submit Handling*)**:
     - Saat tombol submit diklik dalam kondisi field wajib belum terisi, sistem langsung mengaktifkan state error (judul merah, border merah, warning text muncul, dan fokus ke field pertama yang belum terisi).
     - DILARANG menampilkan modal konfirmasi `AppModal.confirm` jika field belum valid.

## 3. Form Sections, Dropzones & Detail Cards Standard (Dribbble Forms UI)
Referensi: [Dribbble - Input Fields Forms UI](https://dribbble.com/shots/22201515-Input-Fields-Forms-UI)

- **Form Section Card (`.form-section`, `.form-card`)**:
  - Background: `var(--color-surface, #f8faf7)`
  - Border: `1px solid var(--color-border, #cfdcc8)`
  - Border Radius: `var(--radius-lg, 16px)`
  - Padding: `24px`
- **Section Title (`.form-section-title`)**:
  - Font: `15px`, `font-weight: 600`, color `var(--color-text, #16251b)`.
  - Section Icon Box: `30px x 30px`, radius `8px`, background `var(--color-primary, #1f3a2a)`, icon putih.
- **Labels (`.form-label`)**:
  - Font: `13.5px`, `font-weight: 600`, color `var(--color-text, #16251b)`.
  - Required star: `color: #D9383A; font-weight: 700;`.
  - Optional badge: `color: var(--color-text-muted, #546958); font-weight: 400; font-size: 12px;`.
- **Inputs & Selects (`.form-control`, `.form-select`)**:
  - Height: `46px`, padding `0 16px`, border `1.5px solid var(--color-border, #cfdcc8)`, radius `10px`, background `#FFFFFF`.
  - Hover: `border-color: #B5BB9D`.
  - Focus: `border-color: var(--color-primary, #1f3a2a) !important`, `box-shadow: none !important`.
- **Segmented Radio Option Cards (`.option-card`)**:
  - Radius `10px`, border `1.5px solid var(--color-border)`, active state `background: var(--color-primary-tint, #d5e4d7)`, border `var(--color-primary)`.
- **Custom Dropzone (`.form-dropzone`, `.custom-dropzone`)**:
  - Border: `1.5px dashed var(--color-border)`, radius `12px`, padding `24px`, background `#FFFFFF`.
- **Detail / Show View Cards (`.detail-card`, `.info-card`)**:
  - Background `var(--color-surface)`, border `1px solid var(--color-border)`, radius `16px`.
  - Label: `font-size: 11.5px`, uppercase, `color: var(--color-text-muted)`.
  - Value: `font-size: 14.5px`, `font-weight: 500`, `color: var(--color-text)`.

## 4. Textarea Height & Proportion Standard (Panjang Kebawah / Lapang)
- **Kotak input bertipe textarea** (alamat, deskripsi, tugas pejabat, keterangan, catatan, KIB textarea) **DILARANG KERAS pendek atau gepeng** (misalnya `rows="2"`, `rows="3"`, atau tinggi kurang dari 100px).
- **Standar Baku Textarea**:
  - **Atribut HTML**: Wajib minimal `rows="4"`.
  - **CSS Token**: `min-height: 120px !important; line-height: 1.5 !important; resize: vertical !important;`.
  - **Fleksibilitas Pengguna**: Pengguna dapat memperbesar/menarik area textarea ke bawah secara vertikal dengan bebas.
  - Berlaku seragam di seluruh form sistem (Form Aset, Pengguna, Pejabat, Ruangan, Bidang, Dropdown, dan Jenis Inventaris).

## 5. Standar Lebar Field Bukti Kepemilikan & Grid Simetris Form Aset
- Field **Bukti Kepemilikan (Opsional)** **WAJIB berukuran lebar yang sama dengan field Asal Usul Barang / Pengguna** (lebar 50% / kolom grid standar dalam grid 2-kolom `.form-grid`).
- **DILARANG KERAS** menjadikan field `Bukti Kepemilikan` sebagai `form-full` (lebar 100%) yang merusak keseimbangan simetri grid input data umum aset.
- Pembungkus input: `<div class="form-group">` (bukan `form-group form-full`).

## 6. Standar Hierarki Validasi: Animasi Scroll Halus & Fokus ke Input Field Paling Atas (Topmost First)
- **Hierarki Navigasi Error**:
  - Saat pengguna menekan tombol Simpan / Submit dengan satu atau lebih input field wajib yang belum terisi/tidak valid:
  - Seluruh field yang tidak valid wajib ditandai (label merah, border merah, warning text muncul dengan 1 ikon segitiga).
  - Animasi smooth scroll (`scrollIntoView({ behavior: 'smooth', block: 'center' })`) dan fokus input **HARUS SELALU DITUJUKAN KE INPUT FIELD YANG PALING ATAS (TOPMOST/HIGHEST IN PAGE DOCUMENT POSITION)**, BUKAN yang paling bawah atau paling dekat dengan tombol simpan.
  - Elemen yang tidak valid diurutkan secara vertikal berdasarkan `getBoundingClientRect().top + window.scrollY`. Elemen dengan koordinat terkecil (paling atas) menjadi target animasi scroll dan fokus pertama.
  - Pemanggilan focus harus menggunakan opsi `{ preventScroll: true }` agar native focus browser Chromium/Safari/Firefox tidak menginterupsi animasi smooth scroll.
  - Jika elemen teratas adalah input bertipe `readonly` (seperti pemilihan kode barang atau trigger dropdown custom), fokus dan scroll diarahkan secara tepat ke wrapper/trigger field tersebut.
- **Standar Single Icon (Anti Double Icon)**:
  - Pesan error (`.invalid-feedback`, `.field-error-message`, `.invalid-feedback-custom`, `.kezak-field-error`) **HANYA BOLEH MENAMPILKAN TEPAT SATU IKON PERINGATAN**.
  - Dilarang keras menampilkan dua ikon segitiga berdampingan.
  - Pseudo-element CSS `::before` wajib otomatis dinonaktifkan (`content: none !important; display: none !important;`) bila di dalam container pesan error sudah terdapat elemen SVG, `<i>`, `.sima-error-icon`, atau class `.has-svg-icon`.

