# Aturan Standar: Card Pembungkus Info Text & Helper Wajib Lebar Penuh (Full-Width)

Aturan ini berlaku untuk **seluruh file Blade, komponen, stylesheet, dan tampilan di proyek SIMA**.

---

## 1. Golden Reference: Pusat Ekspor (`export-data/index.blade.php`)

Seluruh kotak/card banner info text, petunjuk pengisian, flow helper, dan catatan peringatan (`.info-box`, `.doc-flow-helper`, `.jenis-flow-helper`, `.order-hint`, `.info-hint`, `.form-hint-box`, `.sima-info-box`) **WAJIB dibungkus dengan kartu yang membentang lebar penuh (100% full-width)** mengikuti lebar kontainer form/kartu induk di atas dan bawahnya.

### Karakteristik Visual Baku:
- **Display & Width**: `display: flex !important; width: 100% !important; box-sizing: border-box !important;`
- **Background**: `var(--color-primary-tint, #d5e4d7) !important;` (warm sage tint khas SIMA)
- **Border**: `1px solid var(--color-border, #cfdcc8) !important;`
- **Border Radius**: `var(--radius-sm, 8px) !important;`
- **Padding**: `8px 14px !important;` (atau `9px 14px`)
- **Spacing / Margin**: `margin-top: 12px !important; margin-bottom: 12px !important;` (atau disesuaikan konteks tata letak form)
- **Icon**: `<i data-feather="info"></i>` (ukuran `14px x 14px`, `color: var(--color-primary, #1f3a2a)`, `flex-shrink: 0`, posisi vertikal center)
- **Teks Deskripsi**: `font-size: 12px !important; color: var(--color-text, #16251b) !important; font-weight: 500; line-height: 1.45; flex: 1 !important;`

---

## 2. DILARANG KERAS: Lebar Gepeng / Menciut Mengikuti Panjang Teks (`width: fit-content`)

- **DILARANG KERAS** menggunakan `display: inline-flex` dengan `width: fit-content` pada card pembungkus info text/helper.
- Pola `width: fit-content` membuat kotak info text terpotong tanggung di tengah kanvas formulir, terlihat tidak seimbang (*asymmetric*) dengan input/dropdown di atas atau di bawahnya yang berukuran lebar penuh.
- Seluruh card pembungkus info text **wajib membentang 100% penuh** sehingga layout tampak lapang, teratur, dan simetris.

---

## 3. Template Kode Baku (HTML / Blade)

```blade
<div class="info-box">
    <i data-feather="info"></i>
    <div class="info-box-text">
        <strong>Informasi:</strong> Tuliskan keterangan atau petunjuk yang jelas bagi pengguna sistem di sini.
    </div>
</div>
```

Atau jika menggunakan `span`:

```blade
<div class="doc-flow-helper">
    <i data-feather="info"></i>
    <span>Tanggal dokumen digunakan untuk mengisi informasi tanggal pada hasil export. Default adalah hari ini.</span>
</div>
```

---

## 4. Standar Responsive Mobile (Layout Vertikal & Rata Tengah / Center-Aligned)

Pada layar mobile (`@media (max-width: 768px)`), layout kotak info dan kotak peringatan kuning (`.warning-box`, `.sima-warning-box`) bertransformasi menjadi:
- `flex-direction: column !important; align-items: center !important; justify-content: center !important; text-align: center !important; gap: 6px !important;`
- Ikon berada di atas rata tengah (`margin: 0 auto !important;`).
- Teks berada di bawah rata tengah dengan lebar 100% penuh (`width: 100% !important; text-align: center !important; flex: none !important;`).
- Pada desktop (`> 768px`), layout **tetap horizontal**.

---

## 5. Pengendalian Global Terpusat

Aturan ini dikunci secara global dengan `!important` pada:
1. `public/assets/css/design-system.css` (`/* INFO & HINT BOX (STANDAR TEXT INFO WARM PILL/CARD - FULL WIDTH) */`)
2. `resources/views/layouts/head-css.blade.php` (`/* Standardized Info & Hint Box (SIMA Warm Pill/Card Pattern - FULL WIDTH) */`)
