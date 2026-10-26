<?php

namespace App\Imports;

use App\Models\Dropdown;
use App\Models\DropdownItem;
use Illuminate\Support\Collection;
use Maatwebsite\Excel\Concerns\ToCollection;
use Maatwebsite\Excel\Concerns\WithHeadingRow;
use Maatwebsite\Excel\Concerns\WithMultipleSheets;

class DropdownImport implements ToCollection, WithHeadingRow, WithMultipleSheets
{
    public function sheets(): array
    {
        return [
            0 => $this,
        ];
    }
    public int $totalRows = 0;
    public int $importedCount = 0;
    public int $skippedCount = 0;
    public int $failedCount = 0;

    public array $importedRows = [];
    public array $skippedRows = [];
    public array $failedRows = [];

    protected array $seenInFile = [];

    public function collection(Collection $rows): void
    {
        // 1. Strict Header Verification (Mencegah Salah Berkas / Mismatched Template)
        $firstRow = $rows->first();
        if ($firstRow) {
            $firstRowArr = $firstRow instanceof Collection ? $firstRow->toArray() : (array)$firstRow;
            $hasDropdownHeader = false;
            $hasOtherModuleSignature = false;

            foreach (array_keys($firstRowArr) as $colKey) {
                $cleanCol = strtolower(preg_replace('/[^a-zA-Z0-9]/', '', (string)$colKey));
                if (str_contains($cleanCol, 'dropdown') || str_contains($cleanCol, 'pilihanitem') || str_contains($cleanCol, 'itemlabel')) {
                    $hasDropdownHeader = true;
                }
                // Jika memiliki kolom khas modul lain, tolak
                if (str_contains($cleanCol, 'kodebarang') || str_contains($cleanCol, 'nup') || str_contains($cleanCol, 'username') || str_contains($cleanCol, 'namabidang')) {
                    $hasOtherModuleSignature = true;
                }
            }

            if (!$hasDropdownHeader || $hasOtherModuleSignature) {
                throw new \InvalidArgumentException('Format berkas tidak sesuai untuk modul Dropdown. Berkas yang diunggah tidak memiliki kolom "Nama Dropdown" atau "Pilihan Item". Pastikan Anda mengunggah Template Import Dropdown.');
            }
        }

        $rowIndex = 1;

        foreach ($rows as $row) {
            $rowIndex++;

            $rowArr = $row instanceof Collection ? $row->toArray() : (array)$row;

            $hasAnyValue = false;
            foreach ($rowArr as $val) {
                if ($val !== null && trim((string)$val) !== '') {
                    $hasAnyValue = true;
                    break;
                }
            }
            if (!$hasAnyValue) {
                continue;
            }

            // Abaikan baris komentar / petunjuk format CSV
            $firstVal = trim((string)($rowArr[0] ?? array_values($rowArr)[0] ?? ''));
            if (str_starts_with($firstVal, '#') || str_starts_with($firstVal, '[') || str_contains(strtoupper($firstVal), 'PETUNJUK') || str_contains(strtoupper($firstVal), 'PANDUAN')) {
                continue;
            }

            $this->totalRows++;

            $nama = $this->getRowValue($rowArr, [
                'nama_kategori_dropdown', 'nama_dropdown', 'kategori_dropdown', 'nama', 'kategori', 'dropdown'
            ]);

            $deskripsi = $this->getRowValue($rowArr, [
                'deskripsi_kategori', 'deskripsi', 'keterangan'
            ]);

            $itemRaw = $this->getRowValue($rowArr, [
                'pilihan_item_label', 'pilihan_item', 'item_label', 'item', 'label', 'nilai', 'opsi', 'pilihan'
            ]);

            $urutanRaw = $this->getRowValue($rowArr, [
                'urutan_tampil_angka', 'urutan_tampil', 'urutan', 'urutan_angka'
            ]);

            if (empty($nama) || empty($itemRaw)) {
                $this->failedCount++;
                $this->failedRows[] = [
                    'row' => $rowIndex,
                    'nama' => $nama ?: '-',
                    'item' => $itemRaw ?: '-',
                    'reason' => 'Nama Dropdown dan Pilihan Item wajib diisi',
                ];
                continue;
            }

            if (!preg_match('/[a-zA-Z]/', (string)$nama)) {
                $this->failedCount++;
                $this->failedRows[] = [
                    'row' => $rowIndex,
                    'nama' => $nama,
                    'item' => $itemRaw ?: '-',
                    'reason' => 'Nama Dropdown tidak valid (harus mengandung huruf, tidak boleh hanya angka atau simbol)',
                ];
                continue;
            }

            // Split if comma separated in single cell
            $items = array_map('trim', explode(',', (string)$itemRaw));

            foreach ($items as $itemLabel) {
                if (empty($itemLabel)) continue;

                $fileKey = strtolower($nama) . '|' . strtolower($itemLabel);
                if (isset($this->seenInFile[$fileKey])) {
                    $this->skippedCount++;
                    $this->skippedRows[] = [
                        'row' => $rowIndex,
                        'nama' => $nama,
                        'item' => $itemLabel,
                        'reason' => 'Duplikat dengan baris #' . $this->seenInFile[$fileKey] . ' pada berkas Excel ini',
                    ];
                    continue;
                }
                $this->seenInFile[$fileKey] = $rowIndex;

                try {
                    $dropdown = Dropdown::firstOrCreate(
                        ['nama' => $nama],
                        ['deskripsi' => $deskripsi ?: null]
                    );

                    if ($deskripsi && empty($dropdown->deskripsi)) {
                        $dropdown->update(['deskripsi' => $deskripsi]);
                    }

                    $exists = DropdownItem::where('dropdown_id', $dropdown->id)
                        ->whereRaw('LOWER(label) = ?', [strtolower($itemLabel)])
                        ->exists();

                    if ($exists) {
                        $this->skippedCount++;
                        $this->skippedRows[] = [
                            'row' => $rowIndex,
                            'nama' => $nama,
                            'item' => $itemLabel,
                            'reason' => 'Pilihan item sudah ada di sistem',
                        ];
                        continue;
                    }

                    $order = is_numeric($urutanRaw) ? (int)$urutanRaw : ((int)DropdownItem::where('dropdown_id', $dropdown->id)->max('order') + 1);

                    DropdownItem::create([
                        'dropdown_id' => $dropdown->id,
                        'label' => $itemLabel,
                        'order' => $order,
                    ]);

                    $this->importedCount++;
                    $this->importedRows[] = [
                        'row' => $rowIndex,
                        'nama' => $nama,
                        'item' => $itemLabel,
                    ];
                } catch (\Throwable $e) {
                    $this->failedCount++;
                    $this->failedRows[] = [
                        'row' => $rowIndex,
                        'nama' => $nama,
                        'item' => $itemLabel,
                        'reason' => 'Gagal menyimpan: ' . $e->getMessage(),
                    ];
                }
            }
        }
    }

    protected function getRowValue(array $arr, array $keys): ?string
    {
        // 1. Exact key match
        foreach ($keys as $k) {
            if (array_key_exists($k, $arr) && $arr[$k] !== null) {
                $val = $this->cleanValue((string)$arr[$k]);
                if ($val !== null) return $val;
            }
        }

        // 2. Cleaned alphanumeric match
        foreach ($arr as $k => $v) {
            $cleanKey = strtolower(preg_replace('/[^a-zA-Z0-9]/', '', (string)$k));
            foreach ($keys as $target) {
                $cleanTarget = strtolower(preg_replace('/[^a-zA-Z0-9]/', '', (string)$target));
                if ($cleanKey === $cleanTarget && $v !== null) {
                    $val = $this->cleanValue((string)$v);
                    if ($val !== null) return $val;
                }
            }
        }

        // 3. Substring contains match
        foreach ($arr as $k => $v) {
            $cleanKey = strtolower(preg_replace('/[^a-zA-Z0-9]/', '', (string)$k));
            foreach ($keys as $target) {
                $cleanTarget = strtolower(preg_replace('/[^a-zA-Z0-9]/', '', (string)$target));
                if (strlen($cleanTarget) >= 4 && (str_contains($cleanKey, $cleanTarget) || str_contains($cleanTarget, $cleanKey)) && $v !== null) {
                    $val = $this->cleanValue((string)$v);
                    if ($val !== null) return $val;
                }
            }
        }

        return null;
    }

    /**
     * Bersihkan nilai sel dari UTF-8 BOM, null bytes, karakter kontrol tak terlihat, dan formula injection.
     */
    protected function cleanValue(?string $val): ?string
    {
        if ($val === null) {
            return null;
        }

        // Hapus UTF-8 BOM jika ada di awal string
        $val = preg_replace('/^\xEF\xBB\xBF/', '', $val);

        // Hapus null-byte
        $val = str_replace("\0", '', $val);

        // Hapus karakter kontrol non-printable tak terlihat
        $val = preg_replace('/[\x00-\x08\x0B\x0C\x0E-\x1F\x7F]/u', '', $val);

        $val = trim($val);

        // Netralkan potensi formula injection jika diawali '=' atau '@'
        if (str_starts_with($val, '=') || str_starts_with($val, '@')) {
            $val = ltrim($val, '=@');
            $val = trim($val);
        }

        return $val !== '' ? $val : null;
    }
}
