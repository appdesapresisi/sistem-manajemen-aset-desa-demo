# Reset Filter Button Standard & Icon Spin Animation

Standar desain dan interaksi untuk tombol **Reset Filter** di seluruh sistem SIMA (Sistem Manajemen Aset Desa).

## 1. Golden Rules
1. **Pill Kapsul Bentuk Tombol**:
   - Wajib menggunakan class `.btn-sima-secondary.btn-reset-filter`.
   - Wajib berbentuk pill kapsul (`border-radius: var(--radius-full)` / `9999px`).
2. **Icon Feather**:
   - Wajib menggunakan icon `rotate-ccw`: `<i data-feather="rotate-ccw"></i>`.
3. **Animasi Icon Saat Dipencet (Mandatory)**:
   - Saat tombol Reset Filter dipencet/diklik di tampilan manapun dalam sistem, icon `rotate-ccw` **WAJIB berputar penuh 360 derajat berlawanan arah jarum jam (counter-clockwise)** secara halus.
   - Sesuai arah panah icon `rotate-ccw`, rotasi bergerak dari `0deg` ke `-360deg`.
   - Durasi: `0.65s` dengan easing cubic-bezier responsif (`cubic-bezier(0.25, 1, 0.5, 1)`).
   - Titik rotasi wajib di tengah: `transform-origin: center center !important`.
4. **Micro-interaction Hover**:
   - Saat hover, icon berputar sedikit ke kiri (`transform: rotate(-20deg)`).

## 2. Implementasi CSS (`public/assets/css/design-system.css`)
```css
@keyframes sima-spin-reset {
  0% { transform: rotate(0deg); }
  100% { transform: rotate(-360deg); }
}

.btn-reset-filter,
button[id*="reset-filter"] {
  position: relative;
  transition: all 0.15s ease;
}

.btn-reset-filter i,
.btn-reset-filter svg,
button[id*="reset-filter"] i,
button[id*="reset-filter"] svg {
  transform-origin: center center !important;
  display: inline-block !important;
  transition: transform 0.25s cubic-bezier(0.2, 0.8, 0.2, 1);
}

.btn-reset-filter:hover i,
.btn-reset-filter:hover svg,
button[id*="reset-filter"]:hover i,
button[id*="reset-filter"]:hover svg {
  transform: rotate(-20deg);
}

.is-spinning-reset,
.btn-reset-filter.is-spinning i,
.btn-reset-filter.is-spinning svg,
button[id*="reset-filter"].is-spinning i,
button[id*="reset-filter"].is-spinning svg {
  animation: sima-spin-reset 0.65s cubic-bezier(0.25, 1, 0.5, 1) !important;
  transform-origin: center center !important;
}
```

## 3. Implementasi JS Global (`public/assets/js/sima-dropdown.js`)
Menggunakan capture-phase event delegation pada `document` sehingga seluruh tombol Reset Filter di seluruh sistem secara otomatis mendapatkan animasi tanpa perlu script inline manual di setiap halaman:
```javascript
(function () {
  'use strict';
  function triggerResetFilterAnimation(btn) {
    if (!btn) return;
    const icon = btn.querySelector('svg, i');
    if (!icon) return;
    icon.classList.remove('is-spinning-reset');
    btn.classList.remove('is-spinning');
    void icon.offsetWidth; // Force DOM reflow
    icon.classList.add('is-spinning-reset');
    btn.classList.add('is-spinning');
    setTimeout(function () {
      icon.classList.remove('is-spinning-reset');
      btn.classList.remove('is-spinning');
    }, 650);
  }

  document.addEventListener('click', function (e) {
    const btn = e.target.closest('button[id*="reset-filter"], .btn-reset-filter, button[onclick*="resetFilter"], button[onclick*="resetActivityFilter"]');
    if (btn) {
      triggerResetFilterAnimation(btn);
    }
  }, true);
})();
```

## 4. Titik Penerapan di Seluruh Sistem
- `resources/views/manajemen-aset/index.blade.php`
- `resources/views/penghapusan-aset/index.blade.php` (Tab Aktif & Riwayat)
- `resources/views/users/index.blade.php`
- `resources/views/pejabat/index.blade.php`
- `resources/views/ruangan/index.blade.php`
- `resources/views/bidangs/index.blade.php`
- `resources/views/jenis-inventaris/index.blade.php`
- `resources/views/dropdown/index.blade.php`
- `resources/views/export-data/history.blade.php`
- `resources/views/dashboard/activities.blade.php`
- `resources/views/notifications/index.blade.php`
