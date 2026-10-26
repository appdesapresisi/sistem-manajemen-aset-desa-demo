<?php

namespace App\Imports;

use App\Models\Bidang;
use Illuminate\Support\Collection;
use Maatwebsite\Excel\Concerns\ToCollection;
use Maatwebsite\Excel\Concerns\WithHeadingRow;
use Maatwebsite\Excel\Concerns\WithMultipleSheets;

class BidangImport implements ToCollection, WithHeadingRow, WithMultipleSheets
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
    protected Collection $existingBidangs;

    public function __construct()
    {
        $this->existingBidangs = Bidang::all();
    }

    public function collection(Collection $rows): void
    {
        // 1. Strict Header Verification (Mencegah Salah Berkas / Mismatched Template)
        $firstRow = $rows->first();
        if ($firstRow) {
            $firstRowArr = $firstRow instanceof Collection ? $firstRow->toArray() : (array)$firstRow;
            $hasBidangHeader = false;
            $hasOtherModuleSignature = false;

            foreach (array_keys($firstRowArr) as $colKey) {
                $cleanCol = strtolower(preg_replace('/[^a-zA-Z0-9]/', '', (string)$colKey));
                if (str_contains($cleanCol, 'namabidang') || $cleanCol === 'bidang') {
                    $hasBidangHeader = true;
                }
                // Jika memiliki kolom khas modul lain (misal username/role milik Pengguna, kode barang milik Inventaris), tolak
                if (str_contains($cleanCol, 'username') || str_contains($cleanCol, 'peranrole') || str_contains($cleanCol, 'kodebarang') || str_contains($cleanCol, 'nup')) {
                    $hasOtherModuleSignature = true;
                }
            }

            if (!$hasBidangHeader || $hasOtherModuleSignature) {
                throw new \InvalidArgumentException('Format berkas tidak sesuai untuk modul Bidang. Berkas yang diunggah tidak memiliki kolom "Nama Bidang". Pastikan Anda mengunggah Template Import Bidang.');
            }
        }

        $rowIndex = 1;

        foreach ($rows as $row) {
            $rowIndex++;

            $hasAnyValue = false;
            foreach ($row as $val) {
                if ($val !== null && trim((string)$val) !== '') {
                    $hasAnyValue = true;
                    break;
                }
            }
            if (!$hasAnyValue) {
                continue;
            }

            // Abaikan baris komentar / petunjuk format CSV
            $firstVal = trim((string)($row instanceof Collection ? $row->first() : (array_values((array)$row)[0] ?? '')));
            if (str_starts_with($firstVal, '#') || str_starts_with($firstVal, '[') || str_contains(strtoupper($firstVal), 'PETUNJUK') || str_contains(strtoupper($firstVal), 'PANDUAN')) {
                continue;
            }

            $this->totalRows++;

            $nama = $this->getRowValue($row, ['nama_bidang', 'nama', 'bidang']);
            $deskripsi = $this->getRowValue($row, ['deskripsi', 'keterangan']);

            if (empty($nama)) {
                $this->failedCount++;
                $this->failedRows[] = [
                    'row' => $rowIndex,
                    'nama' => '-',
                    'reason' => 'Nama Bidang wajib diisi',
                ];
                continue;
            }

            if (!preg_match('/[a-zA-Z]/', (string)$nama)) {
                $this->failedCount++;
                $this->failedRows[] = [
                    'row' => $rowIndex,
                    'nama' => $nama,
                    'reason' => 'Nama Bidang tidak valid (harus mengandung huruf, tidak boleh hanya angka atau simbol)',
                ];
                continue;
            }

            $fileKey = strtolower($nama);
            if (isset($this->seenInFile[$fileKey])) {
                $this->skippedCount++;
                $this->skippedRows[] = [
                    'row' => $rowIndex,
                    'nama' => $nama,
                    'reason' => 'Duplikat dengan baris #' . $this->seenInFile[$fileKey] . ' pada berkas Excel ini',
                ];
                continue;
            }
            $this->seenInFile[$fileKey] = $rowIndex;

            $isDuplicateInDb = $this->existingBidangs->contains(function ($b) use ($nama) {
                return strcasecmp($b->nama, $nama) === 0;
            });

            if ($isDuplicateInDb) {
                $this->skippedCount++;
                $this->skippedRows[] = [
                    'row' => $rowIndex,
                    'nama' => $nama,
                    'reason' => 'Nama Bidang sudah terdaftar di sistem',
                ];
                continue;
            }

            try {
                $newBidang = Bidang::create([
                    'nama'      => $nama,
                    'deskripsi' => $deskripsi ?: null,
                ]);

                $this->existingBidangs->push($newBidang);
                $this->importedCount++;
                $this->importedRows[] = [
                    'row' => $rowIndex,
                    'id'  => $newBidang->id,
                    'nama' => $nama,
                ];
            } catch (\Throwable $e) {
                $this->failedCount++;
                $this->failedRows[] = [
                    'row' => $rowIndex,
                    'nama' => $nama,
                    'reason' => 'Gagal menyimpan: ' . $e->getMessage(),
                ];
            }
        }
    }

    protected function getRowValue(Collection|array $row, array $keys): ?string
    {
        $arr = $row instanceof Collection ? $row->toArray() : $row;

        foreach ($keys as $k) {
            if (array_key_exists($k, $arr) && $arr[$k] !== null) {
                $val = $this->cleanValue((string)$arr[$k]);
                if ($val !== null) return $val;
            }
        }

        foreach ($arr as $k => $v) {
            $cleanKey = strtolower(str_replace([' ', '-', '_', '*', '(', ')', '/'], '', (string)$k));
            foreach ($keys as $target) {
                $cleanTarget = strtolower(str_replace([' ', '-', '_', '*', '(', ')', '/'], '', $target));
                if ($cleanKey === $cleanTarget && $v !== null) {
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
