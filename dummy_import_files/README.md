# Data Dummy Siap Import - Sistem Manajemen Aset Desa (SIMA)

Direktori ini berisi berkas data dummy siap pakai dalam **dua format**:
1. **Format `.csv`** (Standar format yang saat ini diterima oleh validasi import SIMA)
2. **Format `.xlsx`** (Format Microsoft Excel spreadsheet)

Semua data telah diuji dan terverifikasi **100% Berhasil Diimpor (0 Gagal, 0 Duplikat)** tanpa bentrok dengan data bawaan sistem.

---

## Daftar Berkas Dummy Import

| No | Modul | Berkas CSV (Standar Sistem) | Berkas Excel | Menu Navigasi di Web | Data Baris |
|---|---|---|---|---|---|
| 1 | **Bidang** | [`01_dummy_import_bidang.csv`](./01_dummy_import_bidang.csv) | [`01_dummy_import_bidang.xlsx`](./01_dummy_import_bidang.xlsx) | Master Data > Bidang > *Import CSV* | 5 bidang urusan baru |
| 2 | **Ruangan** | [`02_dummy_import_ruangan.csv`](./02_dummy_import_ruangan.csv) | [`02_dummy_import_ruangan.xlsx`](./02_dummy_import_ruangan.xlsx) | Master Data > Ruangan > *Import CSV* | 5 ruangan kantor/fasilitas (`R-011` s/d `R-015`) |
| 3 | **Pengguna** | [`03_dummy_import_pengguna.csv`](./03_dummy_import_pengguna.csv) | [`03_dummy_import_pengguna.xlsx`](./03_dummy_import_pengguna.xlsx) | Master Data > Pengguna > *Import CSV* | 5 akun pengguna baru (password: `desa12345`) |
| 4 | **Pejabat** | [`04_dummy_import_pejabat.csv`](./04_dummy_import_pejabat.csv) | [`04_dummy_import_pejabat.xlsx`](./04_dummy_import_pejabat.xlsx) | Master Data > Pejabat > *Import CSV* | 5 pejabat & pengurus barang pembantu |
| 5 | **Dropdown** | [`05_dummy_import_dropdown.csv`](./05_dummy_import_dropdown.csv) | [`05_dummy_import_dropdown.xlsx`](./05_dummy_import_dropdown.xlsx) | Master Data > Dropdown > *Import CSV* | 10 item opsi & kategori dropdown baru |
| 6 | **Inventaris Aset** | [`06_dummy_import_inventaris.csv`](./06_dummy_import_inventaris.csv) | [`06_dummy_import_inventaris.xlsx`](./06_dummy_import_inventaris.xlsx) | Manajemen Aset > *Import CSV* | 6 data aset lintas KIB (kode & NUP unik) |

---

## Catatan Format & Validasi Import di SIMA:
- **Status Validasi Saat Ini**: Seluruh controller import di SIMA memvalidasi berkas dengan `'mimes:csv,txt'`. Oleh karena itu, gunakan berkas berakhiran **`.csv`** saat mengunggah melalui formulir web.
- **Dukungan Format Excel (.xlsx)**: Jika Anda ingin agar sistem juga menerima file `.xlsx` secara langsung bersamaan dengan `.csv`, validasi di controller dapat diperluas menjadi `'mimes:csv,txt,xlsx,xls'`.
