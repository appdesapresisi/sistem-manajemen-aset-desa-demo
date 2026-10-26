---
description: Standar Desain Pop-up & Modal Dialog (Divider & Close Button Rules)
globs: resources/views/**/*.blade.php, public/assets/css/**/*.css
---

# Standar Pop-up & Modal Dialog (Divider & Close Button Rules)

Aturan ini berlaku mutlak untuk **seluruh modal, dialog, dan pop-up** di seluruh sistem (modal import CSV/Excel, formulir penghapusan aset resmi, pulihkan aset/restore, konfirmasi duplikasi, preview export data, cetak/unduh QR code, dsb.):

## 1. Aturan Garis Divider (Pemisah Konten Internal Saja)
1. **Hanya Pemisah Section Internal**: Garis divider (`1px dashed var(--color-border)`) hanya difungsikan sebagai pemisah internal antar-bagian (*section-to-section*) di dalam body modal jika diperlukan.
2. **DILARANG Garis di Bawah Judul / Header Modal**:
   - Dilarang meletakkan garis divider (baik border solid maupun dashed) langsung di bawah judul atau header modal.
   - `.modal-header`, `.modal-sima-header`, dan `.qr-modal-header` wajib memiliki `border-bottom: none !important;` (atau `border-bottom-width: 0 !important;`).
3. **DILARANG Garis di Atas Footer Tombol Aksi**:
   - Dilarang meletakkan garis divider antara section konten terakhir dan footer tombol aksi.
   - `.modal-footer`, `.modal-sima-footer`, dan `.qr-modal-footer` wajib memiliki `border-top: none !important;` (atau `border-top-width: 0 !important;`).

## 2. Aturan Tombol Tutup "X" di Pojok Kanan Atas Modal
1. **Jika Footer Memiliki Tombol "Batal"**:
   - Jika modal memiliki tombol "Batal" (atau cancel/dismiss button sejenis) pada footer bagian bawah (misalnya 2 tombol standar: 1 Batal dan 1 untuk aksi seperti Simpan/Hapus/Impor), maka **DILARANG KERAS** menampilkan tombol icon "X" di pojok kanan atas header.
   - Pengguna diarahkan untuk membatalkan dialog secara jelas dan eksplisit melalui tombol "Batal" di bawah.
2. **Jika Footer TIDAK Memiliki Tombol "Batal"**:
   - Tombol icon "X" di pojok kanan atas header **HANYA** diimplementasikan jika footer modal tidak menyediakan tombol "Batal" (misalnya modal pop-up pratinjau yang footernya hanya berisi tombol aksi "Unduh Gambar" dan "Cetak").
