# Aturan Standar: Larangan Header Tunggal Redundan / Duplikasi Informasi (No Redundant Single-Info Headers)

Aturan ini wajib ditaati dan berlaku untuk **seluruh file dan view detail di proyek ini**.

---

## 1. Prinsip Larangan
- **DILARANG KERAS** membuat section/banner/header mandiri di bagian atas kartu detail yang hanya menampilkan satu informasi tunggal (seperti *"Nama Aset"*, *"Nama Pengguna"*, *"Judul Entitas"*) yang kemudian informasinya diulang kembali pada section data terstruktur / key-value grid di bawahnya.
- Pola meletakkan judul tunggal di header terpisah yang hanya mengulang data di bawahnya adalah **tanda visual AI-slop** yang membuang ruang layar dan membuat informasi terlihat berulang secara janggal.

---

## 2. Standar Tampilan yang Benar
- **Eliminasi Header Redundan**: Hilangkan elemen header/banner atas yang redundan tersebut.
- **Integrasi ke Konten Terstruktur**: Tempatkan informasi identitas utama (misalnya Nama Aset, Nama Pengguna, dsb.) langsung menyatu ke dalam section terstruktur / grid ringkasan data yang relevan di bawahnya.
- **Zero Redundancy**: Setiap data hanya disajikan satu kali secara jelas dan fungsional di tempat yang paling tepat.
