# Backup Desain Halaman Error Handling (Kartunopolis 3D Concept)

Folder ini berisi cadangan lengkap (backup) untuk desain halaman error handling bergaya **Kartunopolis** dengan maskot 3D, watermarking angka raksasa, palet sage green khas SIMA Desa, dan floating pill button.

---

## 📁 Berkas Cadangan

1. **`layout.blade.php`**: Master layout Blade Laravel untuk seluruh halaman HTTP error (500, 404, 403, dll).
2. **`500.blade.php`**: View Blade spesifik error 500 dengan judul *"Waduh! Server Kami Sedang Istirahat Sebentar"* dan aksi muat ulang + kembali ke dashboard.
3. **`404.blade.php`**: View Blade spesifik error 404 *"Waduh! Halaman yang Anda Cari Tidak Ditemukan"*.
4. **`error-500.html`**: File prototipe HTML mandiri (standalone) yang dapat dibuka langsung di browser.
5. **`assets/mascot-3d.png`**: Aset gambar maskot 3D monster lucu berbulu memegang buku dengan latar belakang transparan (RGBA PNG).

---

## 🎨 Token & Color Palette yang Digunakan

```css
:root {
  --bg-top: #cfdcc4;        /* Soft Sage Green */
  --bg-mid: #e8f0e2;        /* Light Cream Green */
  --bg-bottom: #ffffff;     /* Pure White */
  --big-num: rgba(255, 255, 255, 0.82); /* Translucent Watermark */
  --ink: #16251b;           /* Dark Forest Organic Text */
  --muted: #546958;         /* Muted Sage */
  --accent: #1f3a2a;        /* Signature SIMA Forest Green */
  --accent-tint: #dde5cc;   /* Border Tint */
}
```

---

## 🔄 Cara Mengembalikan (Restore) Kapan Saja

Jika di kemudian hari ingin mengaktifkan kembali desain ini:
1. Salin `layout.blade.php` ke `resources/views/errors/layout.blade.php`.
2. Salin `500.blade.php` ke `resources/views/errors/500.blade.php`.
3. Salin `404.blade.php` ke `resources/views/errors/404.blade.php`.
4. Pastikan aset `mascot-3d.png` ada di `public/assets/images/errors/mascot-3d.png`.
