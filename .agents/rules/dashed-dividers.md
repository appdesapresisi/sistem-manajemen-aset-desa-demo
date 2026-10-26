# Aturan Standar: Pemisahan Garis — Garis Normal untuk Tabel & Garis Dashed Sistem untuk Non-Tabel

Aturan ini berlaku untuk **seluruh file Blade, komponen, stylesheet, dan tampilan di proyek SIMA**.

---

## 1. Aturan Tabel & Halaman Index: Gunakan Garis Normal (Garis Lurus / Solid Line)

Pada setiap **tabel atau halaman index yang memuat tabel**, seluruh garis pembatas **WAJIB menggunakan garis normal yang lurus (*solid line*)** `1px solid var(--color-border)` agar seluruh garis di komponen tabel tampil seragam dan tidak belang-belang:

1. **Toolbar / Header Kontrol Tabel Atas**:
   - Pembatas antara bar pencarian/filter dengan tabel di bawahnya:
   ```blade
   <!-- WAJIB: border-bottom: 1px solid var(--color-border) -->
   <div style="padding: 16px 20px; border-bottom: 1px solid var(--color-border); display: flex; justify-content: space-between; align-items: center;">
   ```

2. **Header Tabel (`thead th`)**:
   - Garis pemisah header tabel dengan isi data:
   ```css
   /* WAJIB: border-bottom: 1px solid var(--color-border) */
   .table-sima th, .modern-table thead th {
       border-bottom: 1px solid var(--color-border);
   }
   ```

3. **Baris Data Tabel (`tbody tr td`)**:
   - Garis pemisah antar-baris data:
   ```css
   /* WAJIB: border-bottom: 1px solid var(--color-border) */
   .table-sima td, .modern-table tbody td, .modern-table tbody tr {
       border-bottom: 1px solid var(--color-border);
   }
   ```

4. **Footer / Pagination Bar Bawah**:
   - Pembatas antara tabel dengan pagination bar di bawahnya:
   ```blade
   <!-- WAJIB: border-top: 1px solid var(--color-border) -->
   <div class="pagination-bar" style="border-top: 1px solid var(--color-border);">
   <div class="pagination-sima-container" style="border-top: 1px solid var(--color-border);">
   ```

---

## 2. Aturan Selain Tabel (Non-Table): Gunakan Garis Standar Sistem (Garis Putus-Putus / Dashed Line)

Untuk pemisah di luar area tabel, **WAJIB menggunakan garis standar sistem berupa garis putus-putus (*dashed line*)**:
- `border-top: 1px dashed var(--color-border);`
- `border-bottom: 1px dashed var(--color-border);`

1. **Di Bawah Setiap Data pada Seluruh Halaman Detail / Show (`show.blade.php`)**:
   - Setiap item/baris data pada kartu detail (`.portal-kv-item`, `.profile-field-item`, `.portal-stat-item`) wajib memiliki garis pembatas putus-putus halus di bawahnya:
   ```css
   border-bottom: 1px dashed var(--color-border, #cfdcc8);
   padding-bottom: 16px;
   ```

2. **Garis Pemisah Antar Bagian Formulir / Menu Dropdown**:
   ```blade
   <!-- WAJIB: hr dashed -->
   <hr style="border: none; border-top: 1px dashed var(--color-border); margin: 16px 0;" />
   ```

3. **Pemisah Antar Section Formulir (`.form-section + .form-section`)**:
   ```css
   .form-section + .form-section {
       border-top: 1px dashed var(--color-border, #cfdcc8) !important;
   }
   ```

4. **Modal Dialog Non-Tabel**:
   - Header dan footer modal formulir (misal ganti password, modal konfirmasi non-tabel):
   ```css
   border-bottom: 1px dashed var(--color-border);
   border-top: 1px dashed var(--color-border);
   ```

5. **DILARANG Garis Divider di Atas Tombol Aksi Formulir (`.form-actions`)**:
   - Seluruh formulir create, edit, maupun formulir lainnya **DILARANG KERAS** memiliki garis pemisah (divider) di atas deretan tombol aksi (*Batal* & *Simpan/Update*).
   - Wajib `border-top: none !important;` pada `.form-actions`, `.card-footer`, dan kontainer tombol formulir.
   - **Divider di Bawah Judul Kartu Tetap Wajib Ada**: Garis putus-putus (*dashed line*) di bawah judul header kartu (`.card-header > div`, `.card-header-inner`) tetap dipertahankan sebagai pemisah resmi judul dengan isi formulir.

---

## 3. Penegakan Otomatis di `head-css.blade.php`

```css
/* Table & Table Card Separator Standards: Normal Straight Lines (Solid Line) */
.table-sima th,
.table-sima td,
.modern-table thead th,
.modern-table tbody td,
.modern-table tbody tr,
.table th,
.table td {
  border-bottom: 1px solid var(--color-border, #cfdcc8) !important;
}

.sima-card > div[style*="border-bottom"],
.sima-card > form[style*="border-bottom"] {
  border-bottom: 1px solid var(--color-border, #cfdcc8) !important;
}

.pagination-bar,
.pagination-sima-container {
  border-top: 1px solid var(--color-border, #cfdcc8) !important;
}
```
