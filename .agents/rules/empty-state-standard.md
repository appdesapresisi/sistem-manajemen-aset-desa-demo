# Standar Baku Tampilan Data Kosong (Empty State UI Standard)

## 1. Filosofi & Acuan Baku (Golden Reference)
Ketika sebuah halaman, tabel, atau tampilan memanggil data namun datanya belum ada atau kosong (karena belum ada input data atau tidak ada data yang cocok dengan kriteria filter), tampilan **Empty State** wajib mengacu pada standar resmi dari:
- `resources/views/export-data/history.blade.php`

Karakteristik visualnya:
- **Ikon bersih langsung (*bare icon*)**: Ikon feather (biasanya `inbox`) diletakkan langsung berdiri sendiri tanpa pembungkus.
- **Teks to-the-point**: Satu baris kalimat jelas dan deskriptif di bawah ikon (misal: *"Belum ada data inventaris yang sesuai"*).
- **Spasi proporsional**: Vertikal gap 8px, padding lapang (48px 20px).

## 2. Struktur Anatomi Empty State Baku

```blade
<div style="display: flex; flex-direction: column; align-items: center; justify-content: center; gap: 8px; padding: 48px 20px; color: var(--color-text-muted);">
    <i data-feather="inbox" style="width: 42px; height: 42px; stroke-width: 1.5; color: var(--color-border-dark);"></i>
    <p style="margin: 0; font-size: 13.5px; font-weight: 500;">Belum ada [data yang dimaksud] yang sesuai</p>
</div>
```

Jika berada di dalam tabel data (`<tr><td>`):
```blade
<tr>
    <td colspan="{total_kolom}" style="text-align: center; padding: 48px 20px; color: var(--color-text-muted);">
        <div style="display: flex; flex-direction: column; align-items: center; justify-content: center; gap: 8px;">
            <i data-feather="inbox" style="width: 42px; height: 42px; stroke-width: 1.5; color: var(--color-border-dark);"></i>
            <p style="margin: 0; font-size: 13.5px; font-weight: 500;">Belum ada data [nama modul] yang sesuai</p>
        </div>
    </td>
</tr>
```

## 3. Larangan Keras (Anti-AI-Slop Empty State)
1. **DILARANG KERAS menggunakan Card / Lingkaran / Kotak Pembungkus Ikon**:
   - Dilarang membungkus ikon di dalam lingkaran berwarna (seperti `.empty-icon-box` dengan `border-radius: 50%`, background hijau muda/abu-abu).
   - Dilarang membuat card/box putih terpisah di sekeliling ikon.
   - Ikon wajib berdiri bebas (*bare icon*) langsung di atas kanvas/permukaan tabel.
2. **DILARANG menggunakan heading ganda yang redundan**:
   - Dilarang menumpuk `<h6>Judul Kosong</h6>` lalu di bawahnya `<p>Penjelasan panjang lebar...</p>`. Cukup satu baris teks bersih `13.5px` dengan `font-weight: 500`.
3. **DILARANG ilustrasi AI-slop raksasa atau kartun berwarna**:
   - Cukup gunakan icon feather minimalis bergaris halus (`stroke-width: 1.5`) dengan warna netral muted (`var(--color-border-dark)` / `var(--color-text-muted)`).

## 4. Safety Net CSS Global (`design-system.css`)
```css
.empty-state-sima,
.empty-state,
.table-empty-state {
  display: flex !important;
  flex-direction: column !important;
  align-items: center !important;
  justify-content: center !important;
  text-align: center !important;
  padding: 48px 20px !important;
  color: var(--color-text-muted, #546958) !important;
  gap: 8px !important;
  background: transparent !important;
  border: none !important;
  box-shadow: none !important;
}

.empty-state-sima i,
.empty-state-sima svg,
.empty-state i,
.empty-state svg {
  width: 42px !important;
  height: 42px !important;
  stroke-width: 1.5 !important;
  color: var(--color-border-dark, #546958) !important;
  stroke: var(--color-border-dark, #546958) !important;
  margin-bottom: 0 !important;
  background: none !important;
  border: none !important;
  border-radius: 0 !important;
  box-shadow: none !important;
}

.empty-icon-box {
  display: contents !important;
  background: transparent !important;
  border: none !important;
}
```
