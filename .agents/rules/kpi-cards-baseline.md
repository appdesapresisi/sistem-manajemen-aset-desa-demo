# Standar Penyelarasan & Konsistensi Margin Kartu KPI (Symmetric KPI Card Baseline Standard)

## Latar Belakang & Filosofi
Kartu metrik/KPI di dashboard dan toolbar halaman daftar sering kali memiliki variasi isi nilai utama (misal Card 1 berisi 1 digit `8`, Card 2 berisi 3 digit `442`, Card 3 berisi teks nama panjang, Card 4 memiliki icon tren panah naik/turun). Jika tidak memiliki standarisasi slot ketinggian dan margin baku:
- Ketinggian vertikal teks tren bawah (`0% (0) vs. bulan lalu`) akan melayang di posisi yang acak dan tidak sejajar horizontal.
- Ikon SVG panah dapat patah ke baris baru (*line break*) jika tidak terkunci dalam `white-space: nowrap`, membuat satu kartu memiliki 2 baris teks bawah sementara kartu lainnya hanya 1 baris.
- Tampilan antarmuka terlihat berantakan, tidak simetris, dan amatir.

## Aturan Baku Struktur 3-Tier Vertikal KPI Card
1. **Container Kartu (`.stat-card-sima`, `.stat-card`)**:
   - `padding: 16px 20px;`
   - `display: flex; flex-direction: column; justify-content: flex-start;`
   - `min-height: 118px;` seragam di semua kartu KPI dalam grid 4-kolom.

2. **Tier 1: Label Atas (`.stat-header`, `.stat-label`, `.stat-card-label`)**:
   - Tipografi: `font-size: 11.5px; font-weight: 700; color: var(--color-text-muted); text-transform: uppercase; letter-spacing: 0.05em;`
   - Spacing: `height: 14px; margin-bottom: 8px !important;` (jarak ke nilai utama selalu tetap 8px).

3. **Tier 2: Nilai Utama / Metrik (`.stat-number`, `.stat-card-value`)**:
   - **Slot Ketinggian Tetap Terstandar**:
     `height: 32px; min-height: 32px; max-height: 32px; display: flex; align-items: center; margin-bottom: 10px !important;`
   - Angka: `font-size: 26px; font-weight: 700; line-height: 1.1; font-variant-numeric: tabular-nums;`
   - Teks Kategori (jika berupa nama entitas): `font-size: 16px; font-weight: 600; line-height: 1.2; white-space: nowrap; overflow: hidden; text-overflow: ellipsis;`
   - Dengan slot ketinggian 32px terkunci, perbedaan jenis isi tidak akan menggeser posisi elemen di bawahnya.

4. **Tier 3: Keterangan / Trend Delta Bawah (`.kpi-trend-wrapper`, `.stat-delta`)**:
   - **Kunci Penyelarasan**: Wajib **`margin-top: auto !important;`** sehingga baris bawah selalu duduk di posisi baseline yang persis sama dan 100% rata sejajar di seluruh kartu dalam satu baris.
   - Layout: `display: flex; align-items: center; gap: 5px; flex-wrap: nowrap; white-space: nowrap; height: 18px;`
   - Trend value (`.stat-delta-pill`, `.kpi-trend-pill`): `display: inline-flex; align-items: center; gap: 3px; flex-shrink: 0; white-space: nowrap;`
   - Ikon Trend SVG: `display: inline-block; width: 12px; height: 12px; vertical-align: middle; flex-shrink: 0; margin: 0;` (DILARANG patah/wrap ke baris baru).
   - Label Pendamping (`.stat-delta-label`, `.kpi-trend-label`): `font-size: 11.5px; font-weight: 500; color: var(--color-text-muted); white-space: nowrap;`
