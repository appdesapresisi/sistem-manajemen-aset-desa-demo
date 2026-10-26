<?php

namespace App\Imports;

use App\Models\Pejabat;
use Illuminate\Support\Collection;
use Maatwebsite\Excel\Concerns\ToCollection;
use Maatwebsite\Excel\Concerns\WithHeadingRow;
use Maatwebsite\Excel\Concerns\WithMultipleSheets;

class PejabatImport implements ToCollection, WithHeadingRow, WithMultipleSheets
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
    protected Collection $existingPejabats;

    public function __construct()
    {
        $this->existingPejabats = Pejabat::all();
    }

    public function collection(Collection $rows): void
    {
        // 1. Strict Header Verification (Mencegah Salah Berkas / Mismatched Template)
        $firstRow = $rows->first();
        if ($firstRow) {
            $firstRowArr = $firstRow instanceof Collection ? $firstRow->toArray() : (array)$firstRow;
            $hasJabatanHeader = false;
            $hasOtherModuleSignature = false;

            foreach (array_keys($firstRowArr) as $colKey) {
                $cleanCol = strtolower(preg_replace('/[^a-zA-Z0-9]/', '', (string)$colKey));
                if (str_contains($cleanCol, 'jabatan') || str_contains($cleanCol, 'pejabat') || str_contains($cleanCol, 'pengurusbarang')) {
                    $hasJabatanHeader = true;
                }
                // Jika memiliki kolom khas modul lain, tolak
                if (str_contains($cleanCol, 'kodebarang') || str_contains($cleanCol, 'nup') || str_contains($cleanCol, 'username') || str_contains($cleanCol, 'namabidang')) {
                    $hasOtherModuleSignature = true;
                }
            }

            if (!$hasJabatanHeader || $hasOtherModuleSignature) {
                throw new \InvalidArgumentException('Format berkas tidak sesuai untuk modul Pejabat. Berkas yang diunggah tidak memiliki kolom "Jabatan". Pastikan Anda mengunggah Template Import Pejabat.');
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

            $nama = $this->getRowValue($row, ['nama_lengkap__gelar', 'nama_lengkap_gelar', 'nama_lengkap', 'nama', 'nama_pejabat', 'pejabat']);
            $jabatan = $this->getRowValue($row, ['jabatan', 'nama_jabatan', 'posisi']);
            $tugas = $this->getRowValue($row, ['tugas__wewenang', 'tugas_wewenang', 'tugas', 'wewenang', 'peran']);
            $alamat = $this->getRowValue($row, ['alamat', 'domisili', 'alamat_pejabat']);
            $petugasBarangRaw = $this->getRowValue($row, ['petugas_pengurus_barang_ya_tidak', 'petugas_pengurus_barang', 'pengurus_barang', 'petugas_barang', 'is_petugas_barang']);
            $statusAktifRaw = $this->getRowValue($row, ['status_aktif_aktif_nonaktif', 'status_aktif', 'status_keaktifan', 'status_pejabat', 'status', 'is_aktif', 'keaktifan']);

            // Validation
            if (empty($nama) || empty($jabatan)) {
                $this->failedCount++;
                $this->failedRows[] = [
                    'row' => $rowIndex,
                    'nama' => $nama ?: '-',
                    'jabatan' => $jabatan ?: '-',
                    'reason' => 'Nama Lengkap dan Jabatan wajib diisi',
                ];
                continue;
            }

            if (!preg_match('/[a-zA-Z]/', (string)$nama)) {
                $this->failedCount++;
                $this->failedRows[] = [
                    'row' => $rowIndex,
                    'nama' => $nama,
                    'jabatan' => $jabatan,
                    'reason' => 'Nama Pejabat tidak valid (harus mengandung huruf, tidak boleh hanya angka atau simbol)',
                ];
                continue;
            }

            if (!preg_match('/[a-zA-Z]/', (string)$jabatan)) {
                $this->failedCount++;
                $this->failedRows[] = [
                    'row' => $rowIndex,
                    'nama' => $nama,
                    'jabatan' => $jabatan,
                    'reason' => 'Jabatan tidak valid (harus mengandung huruf, tidak boleh hanya angka atau simbol)',
                ];
                continue;
            }

            // Check duplicate in file
            $fileKey = strtolower($nama) . '|' . strtolower($jabatan);
            if (isset($this->seenInFile[$fileKey])) {
                $this->skippedCount++;
                $this->skippedRows[] = [
                    'row' => $rowIndex,
                    'nama' => $nama,
                    'jabatan' => $jabatan,
                    'reason' => 'Duplikat dengan baris #' . $this->seenInFile[$fileKey] . ' pada berkas Excel ini',
                ];
                continue;
            }
            $this->seenInFile[$fileKey] = $rowIndex;

            // Check duplicate in database
            $isDuplicateInDb = $this->existingPejabats->contains(function ($p) use ($nama, $jabatan) {
                return strcasecmp($p->nama, $nama) === 0 && strcasecmp($p->jabatan, $jabatan) === 0;
            });

            if ($isDuplicateInDb) {
                $this->skippedCount++;
                $this->skippedRows[] = [
                    'row' => $rowIndex,
                    'nama' => $nama,
                    'jabatan' => $jabatan,
                    'reason' => 'Pejabat dengan Nama dan Jabatan ini sudah terdaftar di sistem',
                ];
                continue;
            }

            // Parse boolean flags
            $isPetugasBarang = false;
            if ($petugasBarangRaw !== null && $petugasBarangRaw !== '') {
                $clean = strtolower(trim((string)$petugasBarangRaw));
                $isPetugasBarang = in_array($clean, ['ya', '1', 'true', 'yes', 'y', 'pengurus']);
            }

            $isAktif = true;
            if ($statusAktifRaw !== null && $statusAktifRaw !== '') {
                $clean = strtolower(trim((string)$statusAktifRaw));
                if (in_array($clean, ['tidak', 'nonaktif', 'non-aktif', 'non_aktif', '0', 'false', 'no', 't', 'inaktif', 'inactive', 'off'])) {
                    $isAktif = false;
                } elseif (in_array($clean, ['ya', 'aktif', '1', 'true', 'yes', 'y', 'on'])) {
                    $isAktif = true;
                }
            }

            try {
                if ($isPetugasBarang) {
                    Pejabat::where('is_petugas_barang', 1)->update(['is_petugas_barang' => 0]);
                }

                $newPejabat = Pejabat::create([
                    'nama'              => $nama,
                    'jabatan'           => $jabatan,
                    'tugas'             => $tugas ?: null,
                    'alamat'            => $alamat ?: null,
                    'is_petugas_barang' => $isPetugasBarang,
                    'is_aktif'          => $isAktif,
                ]);

                $this->existingPejabats->push($newPejabat);
                $this->importedCount++;
                $this->importedRows[] = [
                    'row' => $rowIndex,
                    'id'  => $newPejabat->id,
                    'nama' => $nama,
                    'jabatan' => $jabatan,
                ];
            } catch (\Throwable $e) {
                $this->failedCount++;
                $this->failedRows[] = [
                    'row' => $rowIndex,
                    'nama' => $nama,
                    'jabatan' => $jabatan,
                    'reason' => 'Gagal menyimpan: ' . $e->getMessage(),
                ];
            }
        }
    }

    protected function getRowValue(Collection|array $row, array $keys): ?string
    {
        $arr = $row instanceof Collection ? $row->toArray() : $row;

        // 1. Direct array key match
        foreach ($keys as $k) {
            if (array_key_exists($k, $arr) && $arr[$k] !== null) {
                $val = $this->cleanValue((string)$arr[$k]);
                if ($val !== null) return $val;
            }
        }

        // 2. Exact match on cleaned key
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

        // 3. Substring / prefix match (menangani header seperti "Status Aktif (Aktif / Nonaktif)" -> "statusaktifaktifnonaktif")
        foreach ($arr as $k => $v) {
            $cleanKey = strtolower(preg_replace('/[^a-zA-Z0-9]/', '', (string)$k));
            if ($cleanKey === '') continue;

            foreach ($keys as $target) {
                $cleanTarget = strtolower(preg_replace('/[^a-zA-Z0-9]/', '', (string)$target));
                if ($cleanTarget === '') continue;

                if ((str_starts_with($cleanKey, $cleanTarget) || str_starts_with($cleanTarget, $cleanKey)) && $v !== null) {
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
