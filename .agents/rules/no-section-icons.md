# Anti-AI-Slop: Dilarang Memakai Icon pada Judul Section

## Latar Belakang & Filosofi
Menyematkan icon kecil di sebelah kiri judul section (seperti ikon info, grid, box, file-text, layers, clipboard, home, image, copy, user, lock, shield) adalah **tanda visual AI slop klasik** yang membuat desain terasa dibuat-buat, penuh ornamen yang tidak berguna, dan kekanak-kanakan. Teks judul yang dipilih dengan baik sudah sangat jelas menjelaskan konteks section tersebut tanpa membutuhkan "kartun/ikon penjelas".

## Aturan Baku
1. **DILARANG KERAS** menyisipkan tag ikon (`<i data-feather="...">`, `<svg>`, atau `.section-icon-box`) di dalam elemen judul section:
   - `.form-section-title`
   - `.portal-section-title`
   - `.buku-inventaris-title`
   - `.portal-overview-title`
   - `.order-section-title`
   - Serta seluruh heading sub-seksi (`h2`, `h3`, `h4`, `span`) di halaman formulir dan detail.
2. **Cukup Teks Judul**:
   - Judul section hanya boleh berisi teks bersih:
     ```html
     <!-- BENAR (Clean & Professional) -->
     <div class="form-section-title">Informasi Akun</div>
     <span class="buku-inventaris-title">Pilih Jenis Inventaris</span>
     <h2 class="portal-section-title"><span>Informasi Rincian Inventaris</span></h2>

     <!-- SALAH (AI Slop) -->
     <div class="form-section-title"><i data-feather="user"></i> Informasi Akun</div>
     <span class="buku-inventaris-title"><i data-feather="grid"></i><span>Pilih Jenis Inventaris</span></span>
     ```
3. **Cakupan Penerapan**:
   - Berlaku untuk seluruh halaman:
     - Form Tambah Data (`create.blade.php`)
     - Form Edit Data (`edit.blade.php`)
     - Halaman Detail / Rincian (`show.blade.php`)
     - Form Ubah Profil & Ubah Sandi
4. **Safety Net CSS**:
   - Diatur secara global di `design-system.css` agar jika ada icon di dalam judul section, icon tersebut otomatis disembunyikan (`display: none !important;`).
