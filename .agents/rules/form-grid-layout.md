# Standar Tata Letak Form Create & Edit (Grid Kombinasi Horizontal & Vertikal)

## 1. Filosofi & Latar Belakang
Tata letak formulir (*form layout*) di seluruh sistem SIMA tidak boleh semata-mata menumpuk satu kolom secara vertikal ke bawah (*single vertical stack*) yang membuang banyak ruang horizontal pada monitor desktop, dan juga tidak boleh membagi seluruh field secara seragam 2 kolom tanpa memperhatikan tipe input.

Layout formulir standar SIMA mengadopsi **kombinasi modular horizontal & vertikal** yang mengacu pada golden reference:
- `resources/views/manajemen-aset/create.blade.php`
- `resources/views/manajemen-aset/edit.blade.php`

## 2. Struktur Grid & Token CSS Baku
Formulir dibungkus di dalam kartu full-width standar grid 12-kolom:
```blade
<div class="grid grid-cols-12 gap-x-6">
  <div class="col-span-12 w-full">
    <div class="card">
      <div class="card-header">...</div>
      <div class="card-body pt-4">
        <form ...>
          <div class="form-section">
            <div class="form-section-title">Nama Seksi</div>
            <div class="form-grid">
              <!-- Field 1 & 2: Baris Horizontal (50% - 50%) -->
              <div class="form-group">...</div>
              <div class="form-group">...</div>

              <!-- Field 3: Baris Vertikal Full-Width (100%) -->
              <div class="form-group form-full">...</div>
            </div>
          </div>
        </form>
      </div>
    </div>
  </div>
</div>
```

### Spesifikasi CSS Global (`public/assets/css/design-system.css`):
```css
.form-grid,
.form-grid-2 {
  display: grid !important;
  grid-template-columns: repeat(2, minmax(0, 1fr)) !important;
  gap: 20px 24px !important;
  align-items: start !important;
  width: 100% !important;
}

.form-full,
.form-group.form-full,
.form-col-full,
.col-span-2 {
  grid-column: 1 / -1 !important;
  width: 100% !important;
}

@media (max-width: 768px) {
  .form-grid,
  .form-grid-2 {
    grid-template-columns: 1fr !important;
    gap: 16px !important;
  }
}
```

## 3. Aturan Kombinasi Horizontal & Vertikal
1. **Field 1-Baris Pendek / Berpasangan (Horizontal 50% - 50%)**:
   - Field yang memiliki tinggi setara (seperti text input tunggal, dropdown select, number input, checkbox/toggle) disusun berpasangan 2 kolom secara horizontal:
     - Contoh: `nama` & `username` (Users)
     - Contoh: `nama` & `jabatan` (Pejabat)
     - Contoh: `kode` & `nama` (Ruangan)
     - Contoh: `nama` & `requires_ruangan` (Jenis Inventaris)
     - Contoh: `password` & `password_confirmation` (Users / Profile)
     - Contoh: `asal_usul` & `bukti_kepemilikan` (Manajemen Aset)
2. **Field Panjang / Textarea (Vertikal 100% Full-Width)**:
   - Field textarea seperti `alamat`, `deskripsi`, `tugas`, `catatan`, dan spesifikasi KIB **WAJIB** menggunakan kelas `.form-full` (`grid-column: 1 / -1`).
   - **DILARANG KERAS** meletakkan textarea berdampingan dalam satu baris dengan input teks 1-baris pendek karena merusak simetri dan baseline grid form.
3. **Lebar Kontainer Form Utama**:
   - Kontainer form create dan edit wajib menggunakan `<div class="col-span-12 w-full">`.
   - **DILARANG KERAS** membatasi lebar form dengan `col-span-8 lg:col-start-3` atau `col-span-10 lg:col-start-2` yang membuat halaman menyempit dan inkonsisten antar modul.
4. **Header Seksi Bersih (Anti-AI-Slop)**:
   - `.form-section-title` hanya berisi teks judul polos tanpa icon pendamping.
5. **Tombol Form Aksi**:
   - Tombol Batal (`.btn-cancel`) dan Tombol Simpan (`.btn-submit`) selalu berpenampilan **Pill Kapsul (`border-radius: var(--radius-full)` / `9999px`)**.
