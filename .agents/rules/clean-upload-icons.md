# Standar Area Unggah Berkas & Ikon Upload (Clean Upload-Cloud Icon)

## Aturan Utama (Universal & Mandatory)

Seluruh area unggah berkas (*file upload area*, dropzone, modal import data, unggah foto inventaris, unggah tanda tangan elektronik, dan unggah dokumen bukti) di seluruh antarmuka SIMA **WAJIB menggunakan ikon `upload-cloud` (Feather Icons) polos tanpa card, kotak, badge, atau wadah latar belakang pembungkus apapun**.

---

## 1. Larangan Keras (Anti-AI-Slop Visual)

1. **DILARANG Kotak / Card / Badge Pembungkus Ikon**:
   - Dilarang membungkus ikon upload dengan elemen berlatar belakang kotak warna/gradien (`background: linear-gradient(...)`, `background: var(--color-primary)`, `border-radius: 12px`, dsb.).
   - Kotak gradien hijau atau badge kotak pembungkus ikon upload adalah **tanda visual AI-slop** yang membuat area dropzone terkesan berat dan ramai.

2. **DILARANG Menggunakan Ikon Upload Lainnya**:
   - Dilarang menggunakan ikon `upload` (panah lurus dalam tray), `image`, `file`, atau ikon pihak ketiga lainnya untuk area dropzone upload berkas.
   - Semua area upload diseragamkan hanya memakai ikon: **`upload-cloud`**.

3. **Pertahankan Teks Label & Hint Asli**:
   - Jangan mengubah atau menghapus teks instruksi label dan batas ukuran berkas (*hint*) yang sudah disesuaikan pada masing-masing fitur (misal: keterangan maksimal 5MB, 10MB, tipe JPG/PNG/PDF/Excel, dsb.).

---

## 2. Acuan Implementasi Baku (Golden Reference)

Mengacu pada modal import data excel di halaman index (`users`, `ruangan`, `pejabat`, `manajemen-aset`, `dropdown`, `bidangs`):

```html
<div class="file-upload-area" id="upload-drop-area">
    <div style="display: flex; justify-content: center; align-items: center; width: 100%; margin-bottom: 8px;">
        <i data-feather="upload-cloud" style="width: 28px; height: 28px; stroke-width: 1.5; color: var(--color-text-muted); display: block; margin: 0 auto;"></i>
    </div>
    <div class="file-upload-text">Pilih berkas atau seret ke area ini</div>
    <div class="file-upload-hint">Maksimal ukuran berkas 10 MB</div>
</div>
```

Atau jika `.file-upload-area` sudah memiliki tata letak flexbox (`display: flex; flex-direction: column; align-items: center; gap: 8px;`):

```html
<div class="file-upload-area" id="upload-drop-area">
    <i data-feather="upload-cloud" style="width: 28px; height: 28px; stroke-width: 1.5; color: var(--color-text-muted); display: block; margin: 0 auto 8px auto;"></i>
    <div class="file-upload-text">Klik atau drag &amp; drop untuk upload</div>
    <div class="file-upload-hint">Format: PNG dan JPG (Max 5MB)</div>
</div>
```

---

## 3. Netralisasi CSS Global (`.file-upload-icon`)

Jika selector `.file-upload-icon` masih ada di stylesheet lama, wajib dinetralkan ke format transparan dan polos:

```css
.file-upload-icon {
    background: transparent !important;
    width: auto !important;
    height: auto !important;
    border-radius: 0 !important;
    box-shadow: none !important;
    margin: 0 auto 8px auto !important;
    display: block !important;
}
.file-upload-icon i,
.file-upload-icon svg {
    width: 28px !important;
    height: 28px !important;
    stroke-width: 1.5 !important;
    color: var(--color-text-muted, #546958) !important;
    stroke: var(--color-text-muted, #546958) !important;
}
```

---

## 4. Standar Live Counter Dropzone Multi-Foto (`.file-upload-text`)

Untuk input unggah file yang mendukung multi-file (maksimal 5 foto pada Foto Aset di Manajemen Aset `create` & `edit`):
1. **0 Foto**:
   - Create: `"Klik atau drag & drop untuk upload foto"`
   - Edit: `"Klik atau drag & drop untuk tambah foto baru"`
2. **1 s/d 4 Foto**:
   - Teks dinamis: `"Tambahkan foto lagi (X/5)"`
   - Nilai X menghitung akumulasi total foto aktif saat ini (termasuk foto existing di mode edit + file baru yang dipilih).
3. **5 Foto (Maksimal)**:
   - Teks: `"Batas maksimal foto tercapai (5/5)"`
   - Klik dropzone dicegat dengan modal/peringatan ramah tanpa membuka dialog file picker berulang kali.
4. **Hapus Foto (Reversible)**:
   - Ketika thumbnail foto dihapus melalui tombol `×`, counter langsung berkurang. Jika seluruh foto dihapus (kembali ke 0), teks wajib kembali instan ke kalimat ajakan awal.
