# Aturan Standar: Larangan Singkatan Angka & Nilai Rupiah Penuh Spesifik (Standar Sistem Manajemen Aset Desa Mengwi)

Aturan ini wajib ditaati dan berlaku untuk **seluruh file, view, komponen, grafik, dan tabel di proyek ini**.

---

## 1. Prinsip Utama (Standar Aset Desa Mengwi)
Dalam sistem penatausahaan aset desa, data audit dan nilai perolehan harus **akurat, transparan, dan dapat dipertanggungjawabkan secara hukum/LPJ**. 
- **DILARANG KERAS menyingkat nilai angka maupun nilai rupiah** (misalnya menggunakan kata `"miliar"`, `"juta"`, `"M"`, `"jt"`, `"k"`, dsb.).
- **Setiap nilai nominal WAJIB dijabarkan secara spesifik dan penuh** menggunakan format mata uang standar Indonesia (`Rp` dengan pemisah ribuan titik `.` tanpa pembulatan singkatan desimal).

---

## 2. Contoh Standar Format Angka:

### ✅ Benar (Dijabarkan Penuh & Spesifik):
- `Rp 19.327.989.966`
- `Rp 8.293.450.000`
- `Rp 6.012.300.000`
- `Rp 275.000.000`
- `Rp 150.000.000`
- Jumlah unit: `442 unit`, `10 unit`

### ❌ Salah (Dilarang Keras):
- `Rp 19,33 miliar` ❌
- `Rp 8,3 miliar` ❌
- `Rp 275 juta` ❌
- `Rp 150 jt` ❌
- `19.3M` ❌

---

## 3. Cakupan Area Penerapan
Aturan ini berlaku mutlak pada:
1. **Semua KPI Cards**: Nilai perolehan, total aset, dsb.
2. **Semua Grafik & Chart**: Label tengah Donut Chart, label sumbu/axis, dan tooltip grafik.
3. **Semua Structured Breakdown List & Legenda**: Daftar rincian kategori KIB di samping grafik.
4. **Semua Tabel Data**: Kolom Harga/Nilai Perolehan di tabel inventaris.
5. **Ekspor & Cetak Dokumen**: KIB A–F, Buku Inventaris, dan Rekapitulasi LPJ.
