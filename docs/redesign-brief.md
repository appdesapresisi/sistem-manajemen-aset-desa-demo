# Briefing: Redesign Total UI

Dokumen ini adalah brief kerja untuk redesign tampilan sistem yang sudah jadi. Baca bersama `no-ai-slop.md` (palet dan aturan umum). Kalau ada konflik, `no-ai-slop.md` menang untuk warna dan larangan dekorasi, dokumen ini menang untuk layout dan struktur halaman.

## 1. Tujuan dan batasan

**Tujuan:** mengganti seluruh tampilan menjadi minimalis, rapi, dan konsisten, mengikuti dua gambar referensi:

- `docs/reference/ref-table.png`: pola halaman list/tabel (header halaman, stat card, tab status, tabel, bulk action, pagination).
- `docs/reference/ref-dashboard.png`: pola dashboard, sidebar, dan topbar.

**Yang berubah:** tampilan, layout, struktur komponen, urutan informasi.

**Yang TIDAK boleh berubah:**
- Logika bisnis, endpoint API, nama field database, validasi, hak akses.
- Fitur. Tidak ada fitur, kolom data, filter, atau aksi yang boleh hilang. Kalau tidak muat di tampilan utama, pindahkan ke tempat sekunder (lihat bagian 5), jangan dihapus.

**Referensi dipakai untuk struktur, bukan untuk ditiru mentah.** Jangan menyalin logo, nama brand, ilustrasi 3D, atau aset dari gambar referensi. Warna di referensi (hitam, ungu, biru) DIABAIKAN. Semua warna memakai token di `no-ai-slop.md`.

## 2. Apa yang diambil dan apa yang dilewati

### Dari ref-table (halaman list)
Ambil:
- Header halaman: judul di kiri, aksi di kanan (1 tombol primary, sisanya secondary).
- Baris stat card kecil di atas tabel (label + angka, titik warna kecil sebagai penanda).
- Tab status dengan garis bawah pada tab aktif.
- Tabel: checkbox, header bisa sort, avatar + nama, status berupa pill lembut, ikon aksi per baris, baris terpilih diberi background tint.
- Bulk action bar melayang di bawah saat ada baris terpilih ("2 dipilih | Duplikat | Cetak | Hapus | ×").
- Footer: "Menampilkan 1–9 dari 240 data" + pagination.
- Sub-menu bertingkat di sidebar (induk bisa expand, anak diberi indent dan background tint saat aktif).

Lewati: kartu "Upgrade to Premium", toggle dark mode (kecuali sistem sudah punya), switcher workspace (kecuali sistem memang multi-tenant).

### Dari ref-dashboard (dashboard, sidebar, topbar)
Ambil:
- Sidebar: logo di atas, grup menu dengan label kecil dan divider antar grup, item aktif berupa pill tint, profil pengguna (nama + peran + menu) di bawah.
- Topbar: search dengan shortcut ⌘K, date range picker, satu tombol primary (misalnya Ekspor).
- Baris KPI: card berisi label, angka besar, delta dibanding periode lalu (hijau naik / merah turun), dan sparkline kecil.
- Card dengan judul + filter kecil di pojok kanan ("Bulan ini ▾").
- Donut dengan total di tengah + legend berisi nilai dan persen.
- Daftar peringkat dengan bar horizontal tipis.
- Feed aktivitas terbaru.
- Tabel "perlu perhatian" di bagian bawah, dengan pill status, progress bar kecil, dan sparkline.

Lewati (bertentangan dengan aturan anti-slop): gradient, grafik titik-titik berderau, funnel bergradient, hexagon health score, gelembung (bubble), cincin 3D, background bercorak. Ganti dengan grafik datar satu warna dari token chart.

## 3. Token tambahan (tambahkan ke token di no-ai-slop.md)

```css
:root {
  --color-primary-tint: #d5e4d7;   /* item sidebar aktif, tab pill, baris terpilih */
  --color-row-hover: #eff5ed;      /* hover baris tabel */
  --color-bg-sidebar: #f8faf7;     /* sama dengan surface */
  --shadow-float: 0 8px 24px rgba(31, 58, 42, 0.12); /* HANYA bulk bar, dropdown, modal */
}
```

Warna chart berurutan: `#1f3a2a`, `#658c63`, `#C9A24A`, `#97b593`, `#3E6A7A`. Maksimal 5 seri.

Status pill (teks solid di atas background soft), radius penuh, teks 12px weight 500:
Success, Warning, Danger, Info sesuai token. Status netral (misalnya "Draft") pakai `--color-border` + teks muted.

## 4. Layout shell

```
+------------+--------------------------------------------+
| Sidebar    | Topbar: judul halaman | search ⌘K | aksi   |
| 248px      +--------------------------------------------+
|            | Konten (max-width 1440px, padding 24–32px)  |
| profil     |                                            |
+------------+--------------------------------------------+
```

- Background halaman `--color-bg`. Area konten utama berupa container `--color-surface` radius 20px dengan border (seperti ref-table), atau card terpisah (seperti ref-dashboard). Pilih satu gaya dan pakai konsisten di semua halaman: **list = satu container besar, dashboard = card terpisah.**
- Sidebar: `--color-bg-sidebar`, border kanan 1px. Grup menu dengan label kecil 11–12px muted (boleh uppercase karena ini label grup yang berfungsi). Maksimal 7 item per grup. Item: ikon 18px + teks 14px. Aktif: `--color-primary-tint`, teks `--color-primary`, weight 500.
- Sidebar bisa collapse jadi mode ikon. Di mobile menjadi drawer.
- Topbar: tinggi 64px. Search di tengah/kanan, notifikasi, avatar. Aksi halaman (tombol primary) ada di header halaman, bukan di topbar, kecuali dashboard.

## 5. Template halaman (setiap halaman WAJIB masuk ke salah satu)

Tidak ada halaman dengan layout unik. Kalau halaman tidak cocok, tanyakan dulu.

**A. List** (semua halaman yang berupa tabel data)
1. Header: judul + maksimal 3 aksi (1 primary).
2. Stat card: maksimal 4, hanya angka yang dipakai pengguna mengambil keputusan.
3. Tab status: maksimal 5. Filter lain di tombol "Filter" (popover) dan search.
4. Tabel.
5. Footer pagination.

**B. Dashboard**
Menjawab tiga pertanyaan, berurutan:
1. "Bagaimana kondisinya?" → baris KPI (4–6 card).
2. "Apa yang berubah?" → 1 grafik tren besar + 1 breakdown (donut atau daftar bar).
3. "Apa yang harus saya tindak?" → tabel perlu perhatian + feed aktivitas.

**C. Detail**
Header (judul + status + aksi) lalu konten dibagi dalam section/card. Informasi utama di kiri, informasi pendukung dan riwayat di kanan. Detail singkat dari tabel dibuka sebagai drawer samping, bukan pindah halaman.

**D. Form**
Dikelompokkan dalam section dengan judul, grid 2 kolom di desktop. Form panjang memakai section bertumpuk dengan bar simpan yang menempel di bawah (Simpan perubahan / Batal). Label di atas input, pesan error di bawah input.

**E. Pengaturan**
Navigasi tab vertikal atau horizontal di kiri/atas, konten berupa section form.

## 6. Cara menjaga isi sistem tetap relevan di desain minimalis

Ini bagian terpenting. Desain referensi sederhana karena datanya sedikit. Sistem kita punya data lebih banyak, jadi aturannya: **tampilkan yang sering dipakai, sembunyikan yang jarang dipakai, jangan hapus.**

1. **Kolom tabel.** Default maksimal 6 kolom data + checkbox + aksi. Kolom lain disediakan lewat tombol "Kolom" (toggle) atau ditampilkan di drawer detail baris. Pilih kolom default berdasarkan: apa yang dicari pengguna saat memindai daftar.
2. **Gabungkan kolom yang berkaitan.** Contoh: avatar + nama + email kecil di bawahnya dalam satu kolom. Tanggal dibuat + pembuat dalam satu kolom.
3. **Aksi per baris.** Maksimal 2 ikon terlihat (misalnya ubah dan hapus), sisanya masuk menu "⋯". Aksi massal muncul di bulk bar hanya saat ada baris terpilih.
4. **Filter.** Status utama jadi tab. Filter lain di popover. Filter yang sedang aktif tampil sebagai chip kecil yang bisa dihapus.
5. **Stat card.** Hanya angka yang bisa ditindaklanjuti. Kalau ragu, jangan ditambahkan. Card yang bisa diklik harus memfilter tabel di bawahnya.
6. **Dashboard.** Kalau sistem punya banyak laporan, dashboard hanya menampilkan ringkasan. Detail ada di halaman Laporan. Setiap card dashboard punya tautan "Lihat semua" ke halaman asalnya.
7. **Menu sidebar.** Kelompokkan berdasarkan alur kerja pengguna (misalnya Utama, Operasional, Laporan, Pengaturan). Fitur yang jarang dipakai masuk sub-menu atau Pengaturan.
8. **Pengungkapan bertahap.** Opsi lanjutan di form disimpan dalam section "Lanjutan" yang bisa dibuka, bukan dihapus.
9. **Data kosong dan panjang.** Setiap tabel punya empty state, loading skeleton, dan perilaku untuk teks panjang (truncate dengan tooltip).
10. **Angka dan tanggal.** Format mengikuti lokal Indonesia (Rp 1.250.000, 5 Okt 2026). Angka rata kanan di tabel, dengan tabular numbers.
11. **Jika tidak ada tempat untuk sebuah elemen,** jangan membuang atau menebak. Catat di laporan akhir "Elemen X dipindah ke Y karena Z".

## 7. Rencana kerja bertahap

Kerjakan **satu fase per sesi**. Setelah tiap fase, berhenti dan tunggu review sebelum lanjut. Jangan menggabungkan fase.

Persiapan sebelum mulai:
- Buat branch baru: `git checkout -b redesign-ui`.
- Simpan gambar referensi di `docs/reference/ref-table.png` dan `docs/reference/ref-dashboard.png`.
- Simpan file ini di `docs/redesign-brief.md` dan `no-ai-slop.md` di `.agent/rules/`.
- Ambil screenshot setiap halaman sebelum diubah (untuk perbandingan).

### Fase 0: Audit (tanpa mengubah kode)

> Baca `docs/redesign-brief.md`, `no-ai-slop.md`, dan kedua gambar di `docs/reference/`. Jangan ubah kode apa pun di fase ini.
>
> Buat `docs/ui-audit.md` berisi:
> 1. Stack frontend yang dipakai (framework, library UI, styling, router) dan daftar komponen UI yang sudah ada.
> 2. Tabel semua halaman/route: nama halaman, file, template yang cocok (A–E), dan apakah ada layout yang tidak cocok dengan template manapun.
> 3. Untuk setiap halaman tabel: daftar semua kolom, kolom mana yang diusulkan tampil default (maks 6), kolom mana yang disembunyikan, semua aksi per baris dan aksi massal, serta filter yang ada.
> 4. Untuk dashboard: daftar semua data/widget saat ini, dan usulan pemetaan ke 3 pertanyaan (kondisi, perubahan, tindakan).
> 5. Struktur menu sidebar saat ini dan usulan pengelompokan baru.
> 6. Daftar risiko (halaman kompleks, komponen yang dipakai di banyak tempat, hardcoded warna, dll.).
>
> Berhenti setelah selesai dan tunggu persetujuan saya.

Setelah file audit jadi, **baca dan koreksi sendiri**. Anda yang paling tahu kolom dan fitur mana yang penting bagi pengguna.

### Fase 1: Token dan komponen dasar

> Berdasarkan `docs/ui-audit.md` yang sudah disetujui, terapkan token warna (termasuk token tambahan di brief) ke konfigurasi styling proyek, lalu buat atau perbarui komponen dasar: Button, Input/Select/Checkbox, Card, Badge/StatusPill, Tabs, Avatar (fallback inisial), Table (header sortable, baris terpilih, hover), Pagination, StatCard (dengan delta dan sparkline opsional), EmptyState, Skeleton, Drawer, Modal, BulkActionBar.
>
> Pakai komponen yang sudah ada sebagai dasar bila memungkinkan. Jangan ubah halaman apa pun. Buat satu halaman sementara `/design-preview` yang menampilkan semua komponen dan statusnya (default, hover, fokus, disabled, error, loading, kosong). Berhenti dan tunggu review.

### Fase 2: Shell (sidebar dan topbar)

> Ganti layout utama: sidebar dan topbar sesuai bagian 4 brief dan referensi dashboard. Pakai struktur menu baru dari audit. Pastikan semua route lama tetap bisa diakses dan item aktif benar. Sidebar bisa collapse, drawer di mobile. Jangan ubah isi halaman. Verifikasi dengan browser dan tunjukkan screenshot desktop dan mobile.

### Fase 3: Satu halaman list sebagai pilot

> Terapkan template A (List) pada halaman **[nama halaman paling kompleks]** saja. Ikuti referensi tabel dan bagian 6 brief. Semua kolom, filter, dan aksi dari audit harus tetap bisa diakses. Tunjukkan screenshot sebelum dan sesudah, lalu daftar elemen yang dipindahkan beserta alasannya. Berhenti dan tunggu review.

Fase ini penting: perbaiki pola di sini sampai Anda puas, karena semua halaman list lain akan meniru hasilnya.

### Fase 4: Dashboard

> Terapkan template B (Dashboard) sesuai pemetaan di audit dan referensi dashboard. Grafik datar, tanpa gradient. Setiap card punya tautan ke halaman asalnya. Tunjukkan screenshot. Berhenti dan tunggu review.

### Fase 5: Sisa halaman, bertahap

> Terapkan template pada halaman berikut: **[2–3 halaman]**. Ikuti pola dari halaman pilot. Jangan membuat variasi layout baru. Laporkan elemen yang dipindahkan atau digabungkan.

Ulangi sampai semua halaman selesai. Kelompokkan per template (semua list, lalu semua form, dst.), bukan per modul.

### Fase 6: QA akhir

> Lakukan pemeriksaan: (1) cocokkan setiap halaman dengan checklist parity di `docs/ui-audit.md`, pastikan tidak ada fitur, kolom, atau aksi yang hilang; (2) cari hex warna yang ditulis langsung di luar token, warna di luar palet, gradient, dan emoji di UI; (3) cek responsif sampai lebar mobile; (4) cek fokus keyboard, kontras, label input; (5) hapus kode dan style lama yang tidak terpakai. Laporkan temuan dan perbaiki.

## 8. Checklist review per halaman (untuk Anda)

- [ ] Semua kolom dan aksi dari sistem lama masih bisa diakses
- [ ] Hanya satu tombol primary per halaman
- [ ] Halaman mengikuti salah satu template A–E
- [ ] Tidak ada warna di luar token, tidak ada gradient
- [ ] Spacing dan radius mengikuti skala
- [ ] Ada empty state, loading, dan error
- [ ] Tampilan mobile masih bisa dipakai
- [ ] Teks UI jelas, konsisten, berbahasa Indonesia, tanpa filler
- [ ] Logika dan data tidak berubah (bandingkan dengan screenshot dan perilaku lama)

## 9. Aturan kerja agent (berlaku di semua fase)

- Kerjakan hanya fase yang sedang diminta. Jangan lompat ke fase berikutnya.
- Kalau ada hal yang ambigu atau tidak cocok dengan template, tanya dulu. Jangan menebak.
- Jangan menghapus fitur, kolom, atau aksi. Pindahkan dan laporkan.
- Jangan menambah dependency baru tanpa izin.
- Verifikasi hasil di browser dan sertakan screenshot sebelum melapor selesai.
- Laporan akhir tiap fase: file yang diubah, elemen yang dipindah, hal yang perlu keputusan saya. Singkat.