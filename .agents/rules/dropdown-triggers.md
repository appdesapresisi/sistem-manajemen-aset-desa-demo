# Standar Komponen Dropdown & Trigger Dropdown (SIMA Design System)

Dokumen ini adalah acuan aturan baku untuk setiap implementasi elemen dropdown (custom searchable dropdown maupun form select) di seluruh tampilan Sistem Manajemen Aset Desa (SIMA).

## 1. Acuan Utama (Golden Reference)
- Mengacu pada komponen visual dropdown KIB di `resources/views/export-data/index.blade.php` (`#kib-dropdown-trigger`).
- Karakter visual: Box container warm surface tenang (`var(--color-surface)`), border halus (`var(--color-border)`), tinggi lapang (`min-height: 48px`), padding `12px 18px`, label placeholder muted (`Pilih ...`), serta chevron di sisi kanan yang berputar 180° secara halus saat dropdown aktif/terbuka.

---

## 2. Struktur Anatomi Trigger Dropdown (`.dropdown-trigger`)

```blade
<div class="searchable-dropdown" id="{nama}-dropdown-container">
    <button type="button" class="dropdown-trigger" id="{nama}-dropdown-trigger">
        <span id="{nama}-selected-label" style="color: var(--color-text-muted); font-size: 13px; font-weight: 500;">Pilih ...</span>
        <i data-feather="chevron-down" class="dropdown-trigger-caret" style="width: 16px; height: 16px;"></i>
    </button>

    <div class="dropdown-menu-panel" id="{nama}-dropdown-menu">
        <div class="dropdown-search-wrapper">
            <i data-feather="search"></i>
            <input type="text" id="{nama}-dropdown-search" class="dropdown-search-input" placeholder="Cari..." autocomplete="off">
        </div>
        <ul class="dropdown-options-list" id="{nama}-dropdown-options-list">
            <!-- Item opsi dirender disini -->
        </ul>
    </div>
</div>
```

---

## 3. Spesifikasi CSS & Token Baku

### Trigger Button (`.dropdown-trigger`)
- `width: 100% !important;`
- `min-height: 48px !important;`
- `padding: 12px 18px !important;`
- `display: flex !important; align-items: center !important; justify-content: space-between !important;`
- `background-color: var(--color-surface, #f8faf7) !important;`
- `border: 1px solid var(--color-border, #cfdcc8) !important;`
- `border-radius: var(--radius-sm, 8px) !important;`
- `color: var(--color-text, #16251b) !important;`
- `cursor: pointer !important;`
- `transition: all 0.2s ease !important;`
- `box-shadow: none !important; outline: none !important;`

### Interaksi State
- **Hover**:
  - `border-color: var(--color-primary, #1f3a2a) !important;`
  - `background-color: var(--color-row-hover, #eff5ed) !important;`
- **Focus / Active / Open (`.dropdown-trigger.open`, `.dropdown-trigger.active`, `.searchable-dropdown.open .dropdown-trigger`)**:
  - `border-color: var(--color-primary, #1f3a2a) !important;`
  - `background-color: var(--color-surface, #f8faf7) !important;`
  - `box-shadow: none !important; outline: none !important;`

### Icon Caret / Chevron (`.dropdown-trigger-caret`)
- `width: 16px !important; height: 16px !important;`
- `color: var(--color-text-muted, #546958) !important;`
- `transition: transform 0.2s ease !important;`
- Saat menu terbuka: `transform: rotate(180deg) !important;`

### Dropdown Menu Panel (`.dropdown-menu-panel`)
- `background-color: var(--color-surface, #f8faf7) !important;`
- `border: 1px solid var(--color-border, #cfdcc8) !important;`
- `border-radius: var(--radius-md, 12px) !important;`
- `padding: 8px !important;`
- `box-shadow: var(--shadow-float, 0 8px 24px rgba(31, 58, 42, 0.12)) !important;`

### Search Box (`.dropdown-search-wrapper`, `.dropdown-search-input`)
- Input background: `var(--color-surface, #f8faf7) !important;`
- Border: `1px solid var(--color-border, #cfdcc8) !important;`
- Border Radius: `var(--radius-sm, 8px) !important;`
- Padding: `8px 12px 8px 34px !important;`
- Icon search: `<i data-feather="search"></i>` di `left: 12px`, warna `var(--color-text-muted)`.
- Focus input: `border-color: var(--color-primary) !important; box-shadow: none !important;`

### Opsi Item (`.dropdown-options-list`, `.dropdown-option-item`)
- Padding item: `10px 12px;`
- Border bottom: `1px solid var(--color-border);` (kecuali `:last-child`)
- Hover: `background-color: var(--color-row-hover);`
- Selected: `background-color: var(--color-primary-tint); color: var(--color-primary); font-weight: 600;`
- Judul opsi: `font-size: 13px; font-weight: 600; color: var(--color-text);`
- Subtitle/keterangan: `font-size: 11px; color: var(--color-text-muted);`

---

## 4. Standar untuk Native Select (`.form-select`, `.form-select-sima`)
Ketika form menggunakan elemen bawaan `<select>`:
- **Tampilan wajib seragam dengan trigger**: tinggi `48px`, padding `12px 38px 12px 18px`, background `var(--color-surface)`, border `1px solid var(--color-border)`, radius `var(--radius-sm)`.
- **Ikon panah**: Menggunakan SVG chevron `#546958` (bukan panah default browser hitam kaku).
- **Hover**: border & latar berubah ke `var(--color-primary)` & `var(--color-row-hover)`. Focus: border `var(--color-primary)`, zero shadow.

---

## 5. Larangan Keras (Anti-AI-Slop)
1. **DILARANG** menambahkan efek glowing blue/cyan atau box-shadow tebal saat fokus.
2. **DILARANG** menggunakan background putih neon kontras `#FFFFFF` di dalam dropdown panel saat tema warm surface aktif.
3. **DILARANG** membuat trigger tombol kotak kaku dengan padding sempit dan sudut tajam.
4. **DILARANG** membiarkan chevron diam saat dropdown dibuka (wajib berputar 180°).
5. **DILARANG** menampilkan singkatan kode (B, KB, RB) sebagai subtitle di bawah label opsi kondisi aset. Tampilkan opsi kondisi secara to-the-point (*clean & direct*): `Baik`, `Kurang Baik`, `Rusak Berat`.
6. **DILARANG** menggunakan tanda dekoratif ganda `--` pada opsi default, placeholder filter, maupun trigger dropdown (seperti: `"-- Semua Ruangan --"`, `"-- Tanpa Ruangan --"`, `"-- Semua Tahun --"`, `"-- Pilih Ruangan --"`). Wajib menggunakan teks polos bersih dan profesional: `"Semua Ruangan"`, `"Tanpa Ruangan"`, `"Semua Tahun"`, `"Pilih Ruangan"`.
