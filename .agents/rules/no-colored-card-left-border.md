# Aturan Standar: Larangan Garis Aksen Samping pada Card (No Left Border Color Accent)

Aturan ini wajib ditaati dan berlaku untuk **seluruh file dan view di proyek ini**.

---

## 1. Prinsip Larangan
- **DILARANG KERAS** menambahkan aksen warna di tepi/sudut kiri card maupun sub-box (misalnya: `border-left: 3px solid var(--color-primary);`, `border-left: 3px solid var(--color-info);`, `border-left: 4px solid #...;`, dsb.).
- Desain strip/garis warna vertikal di kiri card adalah **tanda AI slop** yang merusak estetika clean editorial dan tidak diizinkan di sistem ini.

---

## 2. Standar Tampilan Card & Sub-Box
- Gunakan card/sub-box bersih:
  - Background netral: `var(--color-surface, #f8faf7)` atau `var(--color-bg, #eaf1e7)`.
  - Border: Seragam 1px border tipis `1px solid var(--color-border, #cfdcc8)` di seluruh sisi, atau tanpa border samping tambahan.
  - Radius: Sesuai skala (`var(--radius-sm, 8px)` untuk sub-box, `var(--radius-lg, 16px)` untuk card utama).
- Jika ingin membedakan tipe item:
  - Gunakan badge status tipis atau teks label yang jelas.
  - Jangan menandai perbedaan jenis card dengan mewarnai garis pinggir kirinya.
