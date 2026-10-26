# Aturan Standar: Mekanisme Fallback Data Kosong (Empty Data Fallback UI Standard)

Aturan ini wajib ditaati dan berlaku untuk **seluruh file dan view di proyek ini** (tabel data, halaman rincian/detail, profil, dashboard, modal, dan komponen list).

---

## 1. Prinsip Utama Fallback Data Kosong
Ketika sebuah nilai, relasi data, atau properti bernilai kosong (`null`, string kosong, `0`, atau belum dipilih/diisi):
1. **DILARANG menggunakan Card, Pill, Badge, atau Kotak Pembungkus** sebagai indikator data kosong:
   - Dilarang membungkus dengan background box (seperti `background: #fffbeb`, background abu-abu, dsb.).
   - Dilarang memberi garis tepi (seperti `border: 1px solid #fef3c7`, border rounded box, dsb.).
   - Dilarang memberi padding kotak (seperti `padding: 2px 8px`).
   - Jangan membuat teks kosong tampak seperti tombol atau status badge aktif.
2. **Wajib ditampilkan sebagai Plain Inline Text**:
   - Ditampilkan langsung sebagai teks polos bersih (*clean inline text*) di dalam elemen penampung (misal: cell `<td>`, paragraph `<p>`, atau `<span>`).

---

## 2. Palet Warna & Tipografi Teks Fallback
- **Warna Teks (Color Palette)**:
  - Gunakan warna Warm Golden Amber sesuai palet sistem: **`#B7791F`** / **`var(--color-warning)`** (atau class `.text-empty-fallback` / `.empty-fallback`).
  - Warna ini memberikan sinyal perhatian yang jelas dan hangat bahwa data belum lengkap tanpa mengotori tata letak dengan kotak/badge berulang.
- **Tipografi**:
  - `font-size`: `13px` (atau menyesuaikan ukuran teks sel tabel/elemen sekitarnya).
  - `font-weight`: `500`.
  - Format teks: Gunakan kalimat informatif seperti **`Belum ditentukan`**, atau tanda **`-`** jika kolom sangat sempit.

---

## 3. Contoh Implementasi

### ✅ Benar (Sesuai Aturan):
```blade
<!-- Contoh pada tabel data -->
<td>
  @if($aset->ruangan && $aset->ruangan->nama)
    <span style="color: var(--color-text); font-size: 13px;">{{ $aset->ruangan->nama }}</span>
  @else
    <span class="text-empty-fallback" style="color: var(--color-warning); font-size: 13px; font-weight: 500;">
      Belum ditentukan
    </span>
  @endif
</td>
```

### ❌ Salah (Dilarang):
```blade
<!-- JANGAN membungkus dengan badge / card / background box -->
<td>
  <span style="color: #b7791f; background: #fffbeb; border: 1px solid #fef3c7; padding: 2px 8px; border-radius: 4px;">
    Belum ditentukan
  </span>
</td>
```

---

## 4. Class Helper Global
Untuk memudahkan konsistensi di seluruh Blade view:
```css
.text-empty-fallback,
.empty-fallback {
  color: var(--color-warning, #B7791F) !important;
  font-weight: 500 !important;
  font-size: 13px;
  background: transparent !important;
  border: none !important;
  padding: 0 !important;
  display: inline !important;
}
```

---

## 5. Eksklusivitas Warna Kuning / Amber (Dilarang untuk Tombol & Tautan Navigasi)
- **Warna Kuning / Amber (`#B7791F`, `var(--color-warning)`) HANYA difungsikan secara eksklusif untuk menandai data yang kosong / belum ditentukan.**
- **DILARANG KERAS** menggunakan warna kuning atau palet warna kuning untuk tombol aksi (*action button*), tautan berkas/halaman (*navigation link* seperti *"Pusat Ekspor"*, *"Lihat"*, *"Unduh"*, *"Cetak"*), teks CTA, maupun ikon navigasi.
- Seluruh tombol dan teks navigasi yang mengarah ke file atau modul lain **WAJIB** menggunakan warna utama hijau brand (`var(--color-primary)`) dengan teks yang rapi dan ikon `chevron-right`.

---

## 6. Standar Warna Ikon Indikator Data Kosong (Empty State Icon)
- Ikon yang mengindikasikan ketiadaan data pada tabel, daftar, atau kartu (misalnya ikon `inbox`, `clock`, dsb. saat belum ada rekaman data):
- **WAJIB menggunakan warna `var(--color-text-muted)` (`#546958`)** dengan opasitas penuh (`opacity: 1` / `stroke: var(--color-text-muted)`).
- **DILARANG** membuat ikon data kosong terlalu redup atau abu-abu mati pudar (seperti `opacity: 0.4`, `#9ca3af`, `#ccc`, atau border pudar).
- Layout baku:
  ```blade
  <i data-feather="inbox" style="width: 40px; height: 40px; color: var(--color-text-muted); stroke: var(--color-text-muted);"></i>
  ```

