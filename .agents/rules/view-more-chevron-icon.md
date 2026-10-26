# Aturan Standar: Ikon Navigasi "Lihat Lebih Banyak / Detail" (Chevron Right Standard)

Aturan ini wajib ditaati dan berlaku untuk **seluruh file dan view di proyek ini**.

---

## 1. Prinsip Utama
Setiap kali ada tautan (link `<a>`) atau tombol yang teksnya mengindikasikan navigasi untuk **melihat data lebih banyak, rincian, atau detail**:
- Contoh teks:
  - *"Lihat semua jenis"*
  - *"Lihat semua aktivitas"*
  - *"Lihat semua aset"*
  - *"Lihat rincian"* / *"Lihat detail"*
  - *"Buka selengkapnya"* / *"Buka"*
  - *"Lihat ... aset yang belum lengkap"*
  - *"Cetak KIB A–F"* / link aksi rincian terkait

## 2. Standar Ikon
1. **Wajib menggunakan ikon Chevron Right**:
   - Feather icon: `<i data-feather="chevron-right" style="width: 13px; height: 13px;"></i>` (atau ukuran `12px` - `14px` sesuai ukuran font).
   - Tabler / Phosphor icon jika digunakan: `ti ti-chevron-right` / `ph ph-caret-right`.
2. **DILARANG menggunakan**:
   - Ikon panah lurus: `arrow-right` (`data-feather="arrow-right"`).
   - Karakter HTML entity panah: `&rarr;`, `&gt;`, `→`, atau `>`.

## 3. Format Penulisan & Layout
- Tautan harus dibungkus dengan layout inline yang rapi:
```blade
<a href="{{ route('...') }}" style="display: inline-flex; align-items: center; gap: 4px; font-weight: 600; text-decoration: none;">
  <span>Lihat semua jenis</span>
  <i data-feather="chevron-right" style="width: 14px; height: 14px;"></i>
</a>
```
- Ikon diletakkan di sebelah kanan teks teks deskriptif dengan gap seimbang (`3px - 4px`).

## 4. Dilarang Membungkus Link Navigasi dengan Kotak/Button Banner Kuning
- **DILARANG** membuat tautan navigasi berupa tombol kotak/banner dengan background kuning/oranye alert (seperti `background: #fffbeb`, `border: 1px solid #fef3c7`, dsb.).
- Tautan navigasi harus selalu berupa **teks link bersih** (`color: var(--color-primary); font-weight: 600; text-decoration: none;`) yang menyatu dengan desain card, baik diposisikan di header card maupun di bagian bawah dengan garis pemisah tipis (`border-top: 1px dashed var(--color-border)`).

