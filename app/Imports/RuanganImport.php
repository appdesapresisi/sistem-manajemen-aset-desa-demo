<?php

namespace App\Imports;

use App\Models\Ruangan;
use App\Models\User;
use Illuminate\Support\Collection;
use Maatwebsite\Excel\Concerns\ToCollection;
use Maatwebsite\Excel\Concerns\WithHeadingRow;
use Maatwebsite\Excel\Concerns\WithMultipleSheets;

class RuanganImport implements ToCollection, WithHeadingRow, WithMultipleSheets
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
    protected Collection $existingRuangans;
    protected Collection $users;

    public function __construct()
    {
        $this->existingRuangans = Ruangan::all();
        $this->users = User::all();
    }

    public function collection(Collection $rows): void
    {
        // 1. Strict Header Verification (Mencegah Salah Berkas / Mismatched Template)
        $firstRow = $rows->first();
        if ($firstRow) {
            $firstRowArr = $firstRow instanceof Collection ? $firstRow->toArray() : (array)$firstRow;
            $hasRuanganHeader = false;
            $hasOtherModuleSignature = false;

            foreach (array_keys($firstRowArr) as $colKey) {
                $cleanCol = strtolower(preg_replace('/[^a-zA-Z0-9]/', '', (string)$colKey));
                if (str_contains($cleanCol, 'namaruang') || str_contains($cleanCol, 'koderuang') || $cleanCol === 'ruang' || $cleanCol === 'ruangan') {
                    $hasRuanganHeader = true;
                }
                // Jika memiliki kolom khas modul lain (misal kodebarang / nup milik Inventaris, username milik Pengguna), tolak
                if (str_contains($cleanCol, 'kodebarang') || str_contains($cleanCol, 'nup') || str_contains($cleanCol, 'username') || str_contains($cleanCol, 'peranrole')) {
                    $hasOtherModuleSignature = true;
                }
            }

            if (!$hasRuanganHeader || $hasOtherModuleSignature) {
                throw new \InvalidArgumentException('Format berkas tidak sesuai untuk modul Ruangan. Berkas yang diunggah tidak memiliki kolom "Nama Ruangan". Pastikan Anda mengunggah Template Import Ruangan.');
            }
        }

        $rowIndex = 1;

        foreach ($rows as $row) {
            $rowIndex++;

            // Check if entire row is empty
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

            $nama = $this->getRowValue($row, ['nama_ruangan', 'nama', 'nama_ruang', 'ruangan']);
            $kode = $this->getRowValue($row, ['kode_ruangan', 'kode', 'kode_ruang']);
            $deskripsi = $this->getRowValue($row, ['deskripsi_lokasi', 'deskripsi', 'lokasi']);
            $penanggungJawabRaw = $this->getRowValue($row, ['penanggung_jawab', 'penanggungjawab', 'pj']);

            // Validation
            if (empty($nama)) {
                $this->failedCount++;
                $this->failedRows[] = [
                    'row' => $rowIndex,
                    'nama' => '-',
                    'kode' => $kode ?? '-',
                    'reason' => 'Nama Ruangan wajib diisi',
                ];
                continue;
            }

            if (!preg_match('/[a-zA-Z]/', (string)$nama)) {
                $this->failedCount++;
                $this->failedRows[] = [
                    'row' => $rowIndex,
                    'nama' => $nama,
                    'kode' => $kode ?? '-',
                    'reason' => 'Nama Ruangan tidak valid (harus mengandung huruf, tidak boleh hanya angka atau simbol)',
                ];
                continue;
            }

            if (!empty($kode)) {
                $cleanKode = trim((string)$kode);
                if (strlen($cleanKode) > 50) {
                    $this->failedCount++;
                    $this->failedRows[] = [
                        'row' => $rowIndex,
                        'nama' => $nama,
                        'kode' => $kode,
                        'reason' => 'Kode Ruangan terlalu panjang (maksimal 50 karakter)',
                    ];
                    continue;
                }
                if (is_numeric($cleanKode) && ((int)$cleanKode < 1 || (int)$cleanKode > 99999)) {
                    $this->failedCount++;
                    $this->failedRows[] = [
                        'row' => $rowIndex,
                        'nama' => $nama,
                        'kode' => $kode,
                        'reason' => 'Kode Ruangan numerik harus bernilai antara 1 s/d 99999',
                    ];
                    continue;
                }
            }

            // Check duplicate in file
            $fileKey = strtolower($nama) . '|' . strtolower($kode ?? '');
            if (isset($this->seenInFile[$fileKey])) {
                $this->skippedCount++;
                $this->skippedRows[] = [
                    'row' => $rowIndex,
                    'nama' => $nama,
                    'kode' => $kode ?? '-',
                    'reason' => 'Duplikat dengan baris #' . $this->seenInFile[$fileKey] . ' pada berkas Excel ini',
                ];
                continue;
            }
            $this->seenInFile[$fileKey] = $rowIndex;

            // Check duplicate in database
            $isDuplicateInDb = $this->existingRuangans->contains(function ($r) use ($nama, $kode) {
                $namaMatch = strcasecmp($r->nama, $nama) === 0;
                $kodeMatch = !empty($kode) && !empty($r->kode) && strcasecmp($r->kode, $kode) === 0;
                return $namaMatch || $kodeMatch;
            });

            if ($isDuplicateInDb) {
                $this->skippedCount++;
                $this->skippedRows[] = [
                    'row' => $rowIndex,
                    'nama' => $nama,
                    'kode' => $kode ?? '-',
                    'reason' => 'Nama atau Kode Ruangan sudah terdaftar di sistem',
                ];
                continue;
            }

            // Match penanggung jawab user if provided
            $pjId = null;
            if (!empty($penanggungJawabRaw)) {
                $matchedUser = $this->users->first(function ($u) use ($penanggungJawabRaw) {
                    return strcasecmp($u->nama, $penanggungJawabRaw) === 0
                        || strcasecmp($u->username, $penanggungJawabRaw) === 0;
                });
                if ($matchedUser) {
                    $pjId = $matchedUser->id;
                }
            }

            try {
                $newRuangan = Ruangan::create([
                    'nama'                => $nama,
                    'kode'                => $kode ?: null,
                    'deskripsi'           => $deskripsi ?: null,
                    'penanggung_jawab_id' => $pjId,
                ]);

                $this->existingRuangans->push($newRuangan);
                $this->importedCount++;
                $this->importedRows[] = [
                    'row' => $rowIndex,
                    'id'  => $newRuangan->id,
                    'nama' => $nama,
                    'kode' => $kode ?? '-',
                ];
            } catch (\Throwable $e) {
                $this->failedCount++;
                $this->failedRows[] = [
                    'row' => $rowIndex,
                    'nama' => $nama,
                    'kode' => $kode ?? '-',
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

        // Fuzzy search for snake_cased or clean keys
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
