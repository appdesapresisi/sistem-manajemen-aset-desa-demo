# Standar Penampilan Data Kategori / Jenis Inventaris (No Card/Pill Wrapper)

## Latar Belakang & Filosofi
Di dalam tabel maupun tampilan ringkasan data, membungkus data kategori informatif (seperti Jenis Inventaris) ke dalam komponen badge/pill/card (misalnya `<span class="status-pill status-pill-neutral">` atau background rounded grey/green box) adalah anti-pattern yang mengacaukan hierarki visual tabel.

Komponen status pill/badge hanya diperuntukkan bagi status yang memiliki implikasi alur kerja (seperti kondisi *Baik/Rusak Ringan/Rusak Berat*, status approval *Disetujui/Ditolak/Pending*, atau akun *Aktif/Tidak Aktif*). Kategori data seperti Jenis Inventaris adalah data teks informatif murni.

## Aturan Baku
1. **DILARANG KERAS** membungkus nilai **Jenis Inventaris** dengan card, badge, pill, tag, atau chip di tampilan manapun (tabel daftar aset, tabel ruangan, popover, detail page, dsb.):
   ```html
   <!-- SALAH (Ada Card/Pill Pembungkus) -->
   <td>
     <span class="status-pill status-pill-neutral">Inventaris Peralatan dan Mesin</span>
   </td>

   <!-- BENAR (Clean Plain Text) -->
   <td>
     <span style="font-size: 13px; color: var(--color-text); font-weight: 500;">
       Inventaris Peralatan dan Mesin
     </span>
   </td>
   ```
2. **Spesifikasi Teks**:
   - Tipe: Teks murni (*plain text*).
   - Tipografi: `font-size: 13px`, `font-weight: 500`, `color: var(--color-text, #16251b)`.
   - Tanpa background, tanpa border, tanpa padding pill, tanpa border-radius.
3. **Cakupan Penerapan**:
   - Berlaku untuk seluruh halaman:
     - Tabel Manajemen Aset (`manajemen-aset/index.blade.php`) baik SSR loop maupun AJAX live search.
     - Tabel Ruangan (`ruangan/show.blade.php`).
     - Halaman Rekapitulasi / Mutasi / Penghapusan.
     - Seluruh view di sistem SIMA.
