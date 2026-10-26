# Standar Mutlak: Dilarang Shadow pada State Hover (Zero Shadow on Hover Across All Elements)

## Aturan Inti
Di seluruh sistem Sistem Manajemen Aset Desa (SIMA):
1. **DILARANG KERAS** menambahkan, memunculkan, memperbesar, atau mengubah properti `box-shadow` pada elemen apa pun ketika berada pada status `:hover`!
2. Konsep ini berlaku untuk **SELURUH ELEMEN** tanpa terkecuali:
   - Tombol (tombol submit, tombol primary, secondary, cancel, reset, tombol aksi di tabel, tombol modal, dsb.)
   - Link teks dan tautan navigasi
   - Kartu (`.card`, `.sima-card`, `.portal-card`, widget)
   - Baris tabel (`tr:hover`, `.table-row:hover`)
   - Badge dan status pill
   - Input formulir, textarea, dropdown, dan search box
   - Frame gambar, foto aset, atau 3D stage

## Bentuk Efek Hover yang Diizinkan
Efek interaksi saat elemen disentuh/di-hover murni mengandalkan feedback taktil dan visual yang tenang:
1. **Perubahan Warna Latar Belakang (`background-color`)**:
   - Contoh: tombol primary berubah ke `var(--color-primary-hover)` (`#162c1f`).
   - Contoh: tombol secondary berubah ke `var(--color-primary-tint)` (`#d5e4d7`) atau warna hover lembut.
2. **Perubahan Warna Border (`border-color`)**:
   - Contoh: border berubah menjadi lebih tegas atau sesuai warna primary.
3. **Perubahan Warna Teks / Ikon (`color`)**:
   - Contoh: teks link menjadi lebih pekat atau menyesuaikan warna brand.
4. **Translasi Fisik Mikro yang Halus (`transform`)**:
   - Contoh: tombol sedikit terangkat halus: `transform: translateY(-1px);`
   - Contoh: foto/aset mengalami zoom halus: `transform: scale(1.02);`

## Larangan Khusus
- Dilarang `box-shadow: 0 4px ...` atau `0 6px ...` saat hover.
- Dilarang efek glow warna-warni saat hover.
- Selalu pastikan `box-shadow: none !important;` pada definisi hover elemen interaktif.
