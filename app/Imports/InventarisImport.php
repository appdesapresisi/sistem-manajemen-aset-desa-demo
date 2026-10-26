<?php

namespace App\Imports;

use App\Models\Inventaris;
use App\Models\JenisInventaris;
use App\Models\Ruangan;
use App\Models\InventarisLaporanAsetValue;
use Illuminate\Support\Collection;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\DB;
use Maatwebsite\Excel\Concerns\ToCollection;
use Maatwebsite\Excel\Concerns\WithHeadingRow;
use Maatwebsite\Excel\Concerns\WithMultipleSheets;

class InventarisImport implements ToCollection, WithHeadingRow, WithMultipleSheets
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
    protected array $generatedBarcodes = [];
    protected Collection $jenisCollection;
    protected Collection $ruanganCollection;

    public function __construct()
    {
        $this->jenisCollection = JenisInventaris::where('nama', '!=', 'Buku Inventaris Aset Desa')->get();
        $this->ruanganCollection = Ruangan::all();
    }

    public function collection(Collection $rows): void
    {
        // 1. Strict Header Verification (Mencegah Salah Berkas / Mismatched Template)
        // Berkas Inventaris WAJIB memiliki header 'kode_barang', 'nama_barang', atau 'nup'.
        $firstRow = $rows->first();
        if ($firstRow) {
            $firstRowArr = $firstRow instanceof Collection ? $firstRow->toArray() : (array)$firstRow;
            $hasInventarisHeader = false;
            foreach (array_keys($firstRowArr) as $colKey) {
                $cleanCol = strtolower(preg_replace('/[^a-zA-Z0-9]/', '', (string)$colKey));
                if (str_contains($cleanCol, 'kodebarang') || str_contains($cleanCol, 'nup') || str_contains($cleanCol, 'namabarang')) {
                    $hasInventarisHeader = true;
                    break;
                }
            }

            if (!$hasInventarisHeader) {
                throw new \InvalidArgumentException('Format berkas tidak sesuai untuk modul Inventaris Aset. Berkas yang diunggah tidak memiliki kolom "Kode Barang" atau "NUP". Pastikan Anda mengunggah Template Import Inventaris.');
            }
        }

        $rowIndex = 1; // Row 1 is header in Excel, so data rows start from row 2

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

            // 1. Extract values flexibly
            $kodeBarang       = $this->getRowValue($row, ['kode_barang', 'kode', 'kode_barang_']);
            $namaBarang       = $this->getRowValue($row, ['nama_barang', 'nama', 'nama_barang_']);
            $nupRaw           = $this->getRowValue($row, ['nup', 'nomor_urut_pendaftaran', 'no_nup', 'nup_']);
            $jenisRaw         = $this->getRowValue($row, ['jenis_inventaris', 'jenis', 'jenis_aset', 'kib', 'jenis_inventaris_']);
            $tahunRaw         = $this->getRowValue($row, ['tahun_perolehan', 'tahun', 'tahun_perolehan_']);
            $bulanRaw         = $this->getRowValue($row, ['bulan_perolehan', 'bulan']);
            $nilaiRaw         = $this->getRowValue($row, ['nilai_perolehan', 'nilai', 'harga', 'harga_perolehan', 'nilai_perolehan_']);
            $kondisiRaw       = $this->getRowValue($row, ['kondisi', 'kondisi_barang', 'kondisi_']);
            $asalUsulRaw      = $this->getRowValue($row, ['asal_usul', 'asal', 'sumber', 'asal_usul_']);
            $satuan           = $this->getRowValue($row, ['satuan', 'satuan_barang']);
            $ruanganRaw       = $this->getRowValue($row, ['ruangan', 'ruang', 'lokasi', 'nama_ruangan', 'kode_ruangan']);
            $pengguna         = $this->getRowValue($row, ['pengguna', 'pemakai', 'penanggung_jawab']);
            $buktiKepemilikan = $this->getRowValue($row, ['bukti_kepemilikan', 'bukti', 'dokumen', 'no_bukti', 'nomor_bukti']);
            $keterangan       = $this->getRowValue($row, ['keterangan', 'catatan', 'deskripsi']);

            // 2. Validate required fields
            $errors = [];

            if (empty($kodeBarang)) {
                $errors[] = 'Kode Barang wajib diisi';
            }

            if (empty($namaBarang)) {
                $errors[] = 'Nama Barang wajib diisi';
            } elseif (!preg_match('/[a-zA-Z]/', (string)$namaBarang)) {
                $errors[] = 'Nama Barang tidak valid (harus mengandung huruf, tidak boleh hanya angka atau simbol)';
            }

            if ($nilaiRaw !== null && trim((string)$nilaiRaw) !== '') {
                if (str_contains((string)$nilaiRaw, '-')) {
                    $errors[] = 'Nilai Perolehan tidak boleh bernilai negatif';
                }
            }

            if ($nupRaw === null || !is_numeric($nupRaw) || (int)$nupRaw < 1) {
                $errors[] = 'NUP wajib berupa angka bulat positif (>= 1)';
            }
            $nup = (int)$nupRaw;

            // Match Jenis Inventaris
            $jenis = null;
            if (empty($jenisRaw)) {
                $errors[] = 'Jenis Inventaris wajib diisi';
            } else {
                $jenis = $this->resolveJenisInventaris($jenisRaw);
                if (!$jenis) {
                    $errors[] = "Jenis Inventaris '{$jenisRaw}' tidak ditemukan di sistem";
                }
            }

            // Validate Tahun Perolehan
            $tahun = (int)$tahunRaw;
            if (empty($tahunRaw) || !is_numeric($tahunRaw) || $tahun < 1900 || $tahun > 2100) {
                $errors[] = "Tahun Perolehan '{$tahunRaw}' tidak valid (harus 4 digit angka, misal 2023)";
            }

            // Normalize Bulan Perolehan
            $bulan = $this->normalizeBulanPerolehan($bulanRaw);

            // Normalize Kondisi
            $kondisi = $this->normalizeKondisi($kondisiRaw);
            if (empty($kondisiRaw)) {
                $errors[] = 'Kondisi wajib diisi';
            } elseif (!$kondisi) {
                $errors[] = "Kondisi '{$kondisiRaw}' tidak valid (pilih: Baik, Rusak Ringan, atau Rusak Berat)";
            }

            // Normalize Asal Usul
            $asalUsul = $this->normalizeAsalUsul($asalUsulRaw);
            if (empty($asalUsulRaw)) {
                $errors[] = 'Asal Usul wajib diisi';
            } elseif (!$asalUsul) {
                $errors[] = "Asal Usul '{$asalUsulRaw}' tidak valid (pilih: APBDes, Kekayaan Asli, atau Perolehan Lain)";
            }

            // If there are validation errors, mark row as failed
            if (!empty($errors)) {
                $this->failedRows[] = [
                    'row'         => $rowIndex,
                    'kode_barang' => $kodeBarang ?? '-',
                    'nup'         => $nupRaw ?? '-',
                    'nama_barang' => $namaBarang ?? '-',
                    'reason'      => implode('; ', $errors),
                ];
                $this->failedCount++;
                continue;
            }

            // 3. Duplicate checks
            $dedupKey = $kodeBarang . '||' . $nup;
            if (isset($this->seenInFile[$dedupKey])) {
                $this->skippedRows[] = [
                    'row'         => $rowIndex,
                    'kode_barang' => $kodeBarang,
                    'nup'         => $nup,
                    'nama_barang' => $namaBarang,
                    'reason'      => 'Duplikat dengan baris sebelumnya di dalam file Excel yang sama',
                ];
                $this->skippedCount++;
                continue;
            }
            $this->seenInFile[$dedupKey] = true;

            $existsInDb = Inventaris::where('kode_barang', $kodeBarang)->where('nup', $nup)->exists();
            if ($existsInDb) {
                $this->skippedRows[] = [
                    'row'         => $rowIndex,
                    'kode_barang' => $kodeBarang,
                    'nup'         => $nup,
                    'nama_barang' => $namaBarang,
                    'reason'      => 'Kode Barang dan NUP sudah terdaftar di sistem',
                ];
                $this->skippedCount++;
                continue;
            }

            // 4. Resolve Ruangan
            $ruanganId = $this->resolveRuangan($ruanganRaw);

            // 5. Sanitize Nilai Perolehan
            $nilaiPerolehan = $this->sanitizeNilaiPerolehan($nilaiRaw);

            // 6. Generate Barcode
            $barcode = $this->generateUniqueBarcode();

            // 7. Insert to Database
            DB::beginTransaction();
            try {
                $inventaris = Inventaris::create([
                    'jenis_inventaris_id' => $jenis->id,
                    'nama_barang'         => $namaBarang,
                    'satuan'              => $satuan,
                    'kode_barang'         => $kodeBarang,
                    'nup'                 => $nup,
                    'tahun_perolehan'     => $tahun,
                    'nilai_perolehan'     => $nilaiPerolehan,
                    'keterangan'          => $keterangan,
                    'asal_usul'           => $asalUsul,
                    'bukti_kepemilikan'   => $buktiKepemilikan,
                    'pengguna'            => $pengguna,
                    'ruangan_id'          => $ruanganId,
                    'kondisi'             => $kondisi,
                    'status'              => 'draft',
                    'barcode'             => $barcode,
                    'created_by'          => auth()->id() ?? \App\Models\User::first()?->id ?? 1,
                ]);

                // Simpan bulan perolehan ke laporan aset
                InventarisLaporanAsetValue::updateOrCreate(
                    ['inventaris_id' => $inventaris->id],
                    ['bulan_perolehan' => $bulan]
                );

                DB::commit();

                $this->importedRows[] = [
                    'row'         => $rowIndex,
                    'id'          => $inventaris->id,
                    'kode_barang' => $kodeBarang,
                    'nup'         => $nup,
                    'nama_barang' => $namaBarang,
                ];
                $this->importedCount++;
            } catch (\Throwable $e) {
                DB::rollBack();
                $this->failedRows[] = [
                    'row'         => $rowIndex,
                    'kode_barang' => $kodeBarang,
                    'nup'         => $nup,
                    'nama_barang' => $namaBarang,
                    'reason'      => 'Gagal menyimpan ke database: ' . $e->getMessage(),
                ];
                $this->failedCount++;
            }
        }
    }

    /**
     * Ambil nilai dari baris Excel dengan beberapa kemungkinan nama kolom.
     */
    protected function getRowValue(Collection|array $row, array $aliases): ?string
    {
        // 1. Direct match
        foreach ($aliases as $alias) {
            if (isset($row[$alias]) && $row[$alias] !== null) {
                $val = $this->cleanValue((string)$row[$alias]);
                if ($val !== null) {
                    return $val;
                }
            }
        }

        // 2. Normalized match (strip non-alphanumeric)
        foreach ($row as $k => $v) {
            $cleanKey = strtolower(preg_replace('/[^a-zA-Z0-9]/', '', (string)$k));
            foreach ($aliases as $alias) {
                $cleanAlias = strtolower(preg_replace('/[^a-zA-Z0-9]/', '', $alias));
                if ($cleanKey === $cleanAlias && $v !== null) {
                    $val = $this->cleanValue((string)$v);
                    if ($val !== null) {
                        return $val;
                    }
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

    /**
     * Cari JenisInventaris berdasarkan nama, kode KIB, atau ID.
     */
    protected function resolveJenisInventaris(string $raw): ?JenisInventaris
    {
        $rawTrim = trim($raw);
        $rawLower = strtolower($rawTrim);

        // 1. Direct ID match
        if (is_numeric($rawTrim)) {
            $found = $this->jenisCollection->firstWhere('id', (int)$rawTrim);
            if ($found) return $found;
        }

        // 2. Exact name match (case-insensitive)
        $found = $this->jenisCollection->first(fn($j) => strtolower($j->nama) === $rawLower);
        if ($found) return $found;

        // 3. KIB type match (A, B, C, D, E)
        if (strlen($rawTrim) <= 5) {
            $kibCandidate = strtoupper(preg_replace('/[^a-zA-Z]/', '', $rawTrim));
            if (in_array($kibCandidate, ['A', 'B', 'C', 'D', 'E'])) {
                $found = $this->jenisCollection->firstWhere('kib_type', $kibCandidate);
                if ($found) return $found;
            }
        }

        // 4. Substring contains match
        $found = $this->jenisCollection->first(fn($j) => str_contains(strtolower($j->nama), $rawLower) || str_contains($rawLower, strtolower($j->nama)));
        if ($found) return $found;

        // 5. Common abbreviations & aliases
        $aliasMap = [
            'peralatan' => 'Inventaris Peralatan dan Mesin',
            'mesin'     => 'Inventaris Peralatan dan Mesin',
            'kendaraan' => 'Inventaris Kendaraan',
            'gedung'    => 'Inventaris Gedung dan Bangunan',
            'bangunan'  => 'Inventaris Gedung dan Bangunan',
            'tanah'     => 'Inventaris Tanah',
            'jalan'     => 'Jalan, Irigasi & Jaringan',
            'irigasi'   => 'Jalan, Irigasi & Jaringan',
            'jaringan'  => 'Jalan, Irigasi & Jaringan',
            'lainnya'   => 'Aset Tetap Lainnya',
        ];

        foreach ($aliasMap as $kw => $targetNama) {
            if (str_contains($rawLower, $kw)) {
                $found = $this->jenisCollection->first(fn($j) => strtolower($j->nama) === strtolower($targetNama));
                if ($found) return $found;
            }
        }

        return null;
    }

    /**
     * Cari Ruangan berdasarkan ID, kode, atau nama.
     */
    protected function resolveRuangan(?string $raw): ?int
    {
        if (empty($raw)) return null;

        $rawTrim = trim($raw);
        $rawLower = strtolower($rawTrim);

        // 1. Direct numeric ID
        if (is_numeric($rawTrim)) {
            $found = $this->ruanganCollection->firstWhere('id', (int)$rawTrim);
            if ($found) return $found->id;
        }

        // 2. Match by Kode (e.g. R-001)
        $found = $this->ruanganCollection->first(fn($r) => strtolower($r->kode) === $rawLower);
        if ($found) return $found->id;

        // 3. Match by Nama (case-insensitive)
        $found = $this->ruanganCollection->first(fn($r) => strtolower($r->nama) === $rawLower);
        if ($found) return $found->id;

        // 4. Match partial name
        $found = $this->ruanganCollection->first(fn($r) => str_contains(strtolower($r->nama), $rawLower) || str_contains($rawLower, strtolower($r->nama)));
        if ($found) return $found->id;

        return null;
    }

    /**
     * Normalisasi nilai kondisi.
     */
    protected function normalizeKondisi(?string $raw): ?string
    {
        if (empty($raw)) return null;
        $k = strtolower(trim($raw));
        $k = str_replace(['_', '-'], ' ', $k);

        if (in_array($k, ['baik', 'b', 'good'])) {
            return 'baik';
        }
        if (in_array($k, ['rusak ringan', 'kurang baik', 'sedang', 'rr'])) {
            return 'rusak_ringan';
        }
        if (in_array($k, ['rusak berat', 'rusak parah', 'rb', 'hancur'])) {
            return 'rusak_berat';
        }

        return null;
    }

    /**
     * Normalisasi nilai asal usul.
     */
    protected function normalizeAsalUsul(?string $raw): ?string
    {
        if (empty($raw)) return null;
        $a = strtolower(trim($raw));
        $a = str_replace(['_', '-'], ' ', $a);

        if (in_array($a, ['apbdes', 'apb des', 'apbdesa', 'apbd', 'apbn', 'dana desa'])) {
            return 'apbdes';
        }
        if (in_array($a, ['kekayaan asli', 'asli desa', 'tanah desa', 'asli'])) {
            return 'kekayaan_asli';
        }
        if (in_array($a, ['perolehan lain', 'lain lain', 'lainnya', 'hibah', 'sumbangan'])) {
            return 'perolehan_lain';
        }

        return null;
    }

    /**
     * Normalisasi bulan perolehan (1-12).
     */
    protected function normalizeBulanPerolehan(?string $raw): int
    {
        if (empty($raw)) return 1;

        $rawTrim = trim($raw);
        if (is_numeric($rawTrim)) {
            $num = (int)$rawTrim;
            return ($num >= 1 && $num <= 12) ? $num : 1;
        }

        $monthMap = [
            'januari' => 1, 'jan' => 1,
            'februari' => 2, 'feb' => 2,
            'maret' => 3, 'mar' => 3,
            'april' => 4, 'apr' => 4,
            'mei' => 5, 'may' => 5,
            'juni' => 6, 'jun' => 6,
            'juli' => 7, 'jul' => 7,
            'agustus' => 8, 'agu' => 8, 'agt' => 8,
            'september' => 9, 'sep' => 9,
            'oktober' => 10, 'okt' => 10,
            'november' => 11, 'nov' => 11,
            'desember' => 12, 'des' => 12,
        ];

        $lower = strtolower($rawTrim);
        return $monthMap[$lower] ?? 1;
    }

    /**
     * Membersihkan angka nominal rupiah.
     */
    protected function sanitizeNilaiPerolehan(?string $raw): float
    {
        if (empty($raw)) return 0.0;

        $clean = preg_replace('/[^0-9\.,\-]/u', '', (string)$raw);
        if ($clean === '') return 0.0;

        if (strpos($clean, ',') !== false && strpos($clean, '.') !== false) {
            $clean = str_replace('.', '', $clean);
            $clean = str_replace(',', '.', $clean);
        } elseif (strpos($clean, ',') !== false) {
            $clean = str_replace(',', '.', $clean);
        } else {
            if (preg_match('/^\d+\.\d{1,2}$/', $clean)) {
                // Biarkan titik sebagai desimal standar jika berupa angka tunggal berpresisi 1-2 desimal (misal 1500000.00 atau 1250.5)
            } else {
                $clean = str_replace('.', '', $clean);
            }
        }

        $val = is_numeric($clean) ? (float)$clean : 0.0;
        if ($val < 0) {
            return 0.0;
        }
        if ($val > 999999999999.99) {
            return 999999999999.99;
        }
        return $val;
    }

    /**
     * Generate barcode unik.
     */
    protected function generateUniqueBarcode(): string
    {
        $attempt = 0;
        do {
            $candidate = 'INV-' . date('Y') . '-' . strtoupper(Str::random(6));
            $exists = Inventaris::where('barcode', $candidate)->exists() || in_array($candidate, $this->generatedBarcodes);
            $attempt++;
        } while ($exists && $attempt < 15);

        if ($exists) {
            $candidate = 'INV-' . date('Y') . '-' . strtoupper(substr(str_replace('-', '', (string) Str::uuid()), 0, 8));
        }

        $this->generatedBarcodes[] = $candidate;
        return $candidate;
    }
}
