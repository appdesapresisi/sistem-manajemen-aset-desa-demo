<?php

namespace App\Exports;

use App\Models\Inventaris;
use App\Models\JenisInventaris;
use Illuminate\Support\Collection;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Concerns\WithColumnWidths;
use Maatwebsite\Excel\Concerns\WithEvents;
use Maatwebsite\Excel\Concerns\WithTitle;
use Maatwebsite\Excel\Events\AfterSheet;
use Illuminate\Support\Facades\Log;
use PhpOffice\PhpSpreadsheet\Style\Alignment;
use PhpOffice\PhpSpreadsheet\Style\Border;
use PhpOffice\PhpSpreadsheet\Style\Fill;
use PhpOffice\PhpSpreadsheet\Style\NumberFormat;
use Maatwebsite\Excel\Concerns\WithCustomValueBinder;
use PhpOffice\PhpSpreadsheet\Cell\StringValueBinder;

class InventarisExport extends StringValueBinder implements
    FromCollection,
    WithHeadings,
    WithMapping,
    WithColumnWidths,
    WithEvents,
    WithTitle,
    WithCustomValueBinder
{
    protected int $jenisId;
    protected array $filters;
    protected Collection $fields;
    protected $jenis;
    protected bool $hasKondisi = false;
    protected int $kondisiColIndex = -1;
    protected string $nilaiColLetter = '';
    protected array $currencyColLetters = []; // semua kolom mata uang (subset dari fields)
    protected int $totalColumns = 0;
    protected int $rowCounter = 0;
    protected array $petunjuk = [];
    protected array $pejabatSnapshot = [];
    protected ?string $sumTargetFieldKey = null;
    protected float $sumTargetTotal = 0.0;
    protected bool $includeSignatureImage = true;
    protected array $textColLetters = []; // kolom yang harus diformat @Text agar tidak muncul warning
    protected array $centerColLetters = []; // kolom yang di-center: kode_barang, nup, tahun_perolehan
    protected bool $hasUkuranGroup = false; // Jalan Irigasi: kolom UKURAN (panjang/lebar/luas) digroup
    protected array $ukuranColNums = []; // [colNum => 'km'|'m'|'m2']
    protected bool $hasNomorIdentitas = false; // Kendaraan: 4 kolom nomor identitas digabung jadi 1
    protected int $nomorIdentitasColStart = -1; // colNum (1-based) kolom pertama dari grup
    protected array $nomorIdentitasColNums = []; // [colNum1, colNum2, colNum3, colNum4]
    protected array $nomorIdentitasLabels = []; // label ke-4 field
    protected string $tanggalDokumen = '';
    protected string $docLampiran = '';
    protected string $docBaNomor = '';
    protected string $docBaTanggal = '';
    protected array $desa = [];

    public function __construct(int $jenisId, array $filters = [], ?array $pejabatSnapshot = null, bool $includeSignatureImage = true, string $tanggalDokumen = '', string $docLampiran = '', string $docBaNomor = '', string $docBaTanggal = '')
    {
        $this->jenisId = $jenisId;
        $this->filters = $filters ?? [];
        $this->rowCounter = 0;
        $this->sumTargetTotal = 0.0;
        $this->includeSignatureImage = $includeSignatureImage;
        $this->tanggalDokumen = $tanggalDokumen;
        $this->docLampiran = $docLampiran;
        $this->docBaNomor = $docBaNomor;
        $this->docBaTanggal = $docBaTanggal;
        $this->desa = config('desa', []);

        $this->jenis = JenisInventaris::with([
            'fields' => function ($q) {
                $q->orderBy('order', 'asc');
            },
            'pejabatDefaults.pejabat'
        ])->findOrFail($jenisId);

        // Filter field yang include_in_export = false (user memilih tidak masuk export)
        // Field dengan include_in_export NULL atau true tetap ditampilkan (default include)
        $this->fields = $this->jenis->fields->filter(function ($field) {
            return $field->include_in_export !== false;
        })->values();
        $this->sumTargetFieldKey = $this->resolveSumTargetFieldKey();

        // Deteksi grup NOMOR IDENTITAS lebih awal (agar kolom tracking menghitung sebagai 1 kolom)
        $niKeywords = ['polisi', 'mesin', 'rangka', 'bpkb'];
        $fieldsArrEarly = $this->fields->values();
        $niFieldStartEarly = -1;
        for ($fi = 0; $fi <= $fieldsArrEarly->count() - 4; $fi++) {
            $found = [];
            for ($fj = $fi; $fj < $fi + 4; $fj++) {
                $lbl = mb_strtolower(trim((string)($fieldsArrEarly[$fj]->label_field ?? '')));
                foreach ($niKeywords as $kw) {
                    if (str_contains($lbl, $kw)) { $found[$kw] = $fj; break; }
                }
            }
            if (count($found) === 4) { $niFieldStartEarly = $fi; break; }
        }

        $colIdx = 1;
        $actualColNum = 2; // A(1)=No, B(2)=first field
        foreach ($this->fields as $fIndex => $field) {
            if ($field->field_type === 'fixed' && $field->fixed_field_name === 'kondisi') {
                $this->hasKondisi = true;
                $this->kondisiColIndex = $actualColNum - 1; // colNum-based, konsisten dengan NI merge
                $actualColNum += 3;
            } else {
                // NOMOR IDENTITAS: field ke-2,3,4 grup tidak menambah kolom (digabung jadi 1)
                if ($niFieldStartEarly >= 0 && $fIndex > $niFieldStartEarly && $fIndex <= $niFieldStartEarly + 3) {
                    $colIdx++;
                    continue; // jangan naikkan actualColNum
                }
                if ($this->nilaiColLetter === '' && $this->isSumTargetField($field)) {
                    $this->nilaiColLetter = $this->numToColLetter($actualColNum);
                }
                // Track semua kolom mata uang termasuk yang bukan sum-target
                if ($this->isCurrencyField($field)) {
                    $this->currencyColLetters[] = $this->numToColLetter($actualColNum);
                }
                // Tandai kolom yang nilainya teks (bisa punya leading zero) agar diformat @Text
                $fixedName = $field->fixed_field_name ?? '';
                if (in_array($fixedName, ['nup', 'kode_barang'])) {
                    $this->textColLetters[] = $this->numToColLetter($actualColNum);
                }
                // Kolom yang di-center: kode_barang, nup, tahun_perolehan + semua tipe angka kecuali currency
                $isNumericTipe = in_array($field->tipe ?? null, ['number', 'decimal_km', 'decimal_m', 'decimal_m2', 'year'], true);
                $isCurrencyCol = $this->isCurrencyField($field);
                if (
                    in_array($fixedName, ['kode_barang', 'nup', 'tahun_perolehan'])
                    || ($isNumericTipe && !$isCurrencyCol)
                ) {
                    $this->centerColLetters[] = $this->numToColLetter($actualColNum);
                }
                // Deteksi kolom UKURAN untuk Jalan Irigasi (kib_type D)
                if (($this->jenis->kib_type ?? null) === 'D') {
                    $tipe = $field->tipe ?? null;
                    if ($tipe === 'decimal_km')  $this->ukuranColNums[$actualColNum] = 'km';
                    elseif ($tipe === 'decimal_m')  $this->ukuranColNums[$actualColNum] = 'm';
                    elseif ($tipe === 'decimal_m2') $this->ukuranColNums[$actualColNum] = 'm2';
                }
                $actualColNum++;
            }
            $colIdx++;
        }

        if ($this->nilaiColLetter === '') {
            Log::warning('Inventaris export SUM target not found; jumlah row rendered without formula', [
                'jenis_inventaris_id' => $this->jenisId,
                'jenis_nama' => $this->jenis->nama ?? null,
                'field_keys' => $this->fields->map(fn($f) => $this->makeFieldKey($f))->values()->all(),
            ]);
        } else {
            Log::info('Inventaris export SUM target selected', [
                'jenis_inventaris_id' => $this->jenisId,
                'jenis_nama' => $this->jenis->nama ?? null,
                'sum_target_key' => $this->sumTargetFieldKey,
                'sum_col' => $this->nilaiColLetter,
            ]);
        }

        $this->totalColumns = 1 + $this->fields->count() + ($this->hasKondisi ? 2 : 0);
        $this->hasUkuranGroup = count($this->ukuranColNums) === 3;

        // Deteksi grup NOMOR IDENTITAS (kendaraan): 4 field berurutan polisi/mesin/rangka/bpkb
        $keywords = ['polisi', 'mesin', 'rangka', 'bpkb'];
        $fieldArr = $this->fields->values();
        for ($fi = 0; $fi <= $fieldArr->count() - 4; $fi++) {
            $found = [];
            for ($fj = $fi; $fj < $fi + 4; $fj++) {
                $lbl = mb_strtolower(trim((string)($fieldArr[$fj]->label_field ?? '')));
                foreach ($keywords as $kw) {
                    if (str_contains($lbl, $kw)) { $found[$kw] = $fj; break; }
                }
            }
            if (count($found) === 4) {
                $this->hasNomorIdentitas = true;
                $colNum = 2; // A=1=No, B=2=first field
                for ($k = 0; $k < $fi; $k++) {
                    $f = $fieldArr[$k];
                    if ($f->field_type === 'fixed' && $f->fixed_field_name === 'kondisi') {
                        $colNum += 3;
                    } else {
                        $colNum++;
                    }
                }
                $this->nomorIdentitasColStart = $colNum;
                foreach (array_values($found) as $fj) {
                    $this->nomorIdentitasColNums[] = $colNum + ($fj - $fi);
                    $this->nomorIdentitasLabels[]  = trim((string)($fieldArr[$fj]->label_field ?? ''));
                }
                // Kurangi totalColumns: 4 kolom → 1 kolom
                $this->totalColumns -= 3;
                break;
            }
        }

        $this->buildPetunjuk();

        if (is_array($pejabatSnapshot) && count($pejabatSnapshot) > 0) {
            $this->pejabatSnapshot = array_values(array_filter($pejabatSnapshot));
        } else {
            $defaults = $this->jenis->pejabatDefaults ?? collect();
            $this->pejabatSnapshot = $defaults
                ->sortBy('order')
                ->map(function ($d) {
                    return $d->pejabat ? $d->pejabat->toArray() : null;
                })
                ->filter()
                ->values()
                ->all();
        }
    }

    protected function buildPetunjuk(): void
    {
        $no = 1;
        $this->petunjuk[] = [$no++, 'Diisi Nomor Urut'];
        foreach ($this->fields as $field) {
            if ($field->field_type === 'fixed' && $field->fixed_field_name === 'kondisi') {
                $this->petunjuk[] = [$no++, 'Diisi Kondisi Barang : X pada kolom Baik, Rusak Ringan, atau Rusak Berat'];
            } else {
                $this->petunjuk[] = [$no++, 'Diisi ' . $this->exportLabelForField($field)];
            }
        }
    }

    protected function exportLabelForField($field): string
    {
        $label = trim((string) ($field->label_field ?? ''));
        
        // Tambah suffix untuk currency field - label saja yang punya (Rp), value tidak
        if ($this->isCurrencyField($field)) {
            $normalized = strtoupper(str_replace([' ', '(', ')'], '', $label));
            if (str_contains($normalized, 'RP')) {
                return $label; // Sudah ada (Rp), jangan duplikasi
            }
            return $label !== '' ? $label . ' (Rp)' : '(Rp)';
        }

        $tipe = $field->tipe ?? null;

        if ($tipe === 'decimal_m2') {
            $normalized = strtoupper(str_replace([' ', '²'], '', $label));
            if (str_contains($normalized, 'M2')) {
                $standardized = preg_replace('/[mM]\s*(?:2|²)/u', 'M²', $label);
                return $standardized ?? $label;
            }
            return $label !== '' ? $label . ' (M²)' : 'M²';
        }

        if ($tipe === 'decimal_km') {
            $upper = strtoupper($label);
            if (str_contains($upper, '(KM)') || str_contains($upper, ' KM')) {
                return $label;
            }
            return $label !== '' ? $label . ' (Km)' : 'Km';
        }

        if ($tipe === 'decimal_m') {
            if (preg_match('/\(M\)/i', $label) && !str_contains($label, '²') && !str_contains(strtoupper($label), 'M2')) {
                return $label;
            }
            return $label !== '' ? $label . ' (M)' : 'M';
        }

        return $label;
    }

    public function collection(): Collection
    {
        $query = Inventaris::where('jenis_inventaris_id', $this->jenisId)
            ->with(['jenis', 'values.field'])
            ->orderBy('kode_barang')
            ->orderBy('tahun_perolehan')
            ->orderBy('nama_barang')
            ->orderByRaw('CAST(nup AS UNSIGNED) ASC')
            ->orderBy('id');

        if (!empty($this->filters['tahun'])) {
            $query->where('tahun_perolehan', '<=', $this->filters['tahun']);
        }

        return $query->get();
    }

    public function headings(): array
    {
        $headers = ['No'];
        $niFieldIndices = $this->hasNomorIdentitas
            ? array_flip(array_map(fn($c) => $c - 2, $this->nomorIdentitasColNums)) // colNum-2 = field index
            : [];
        $niAdded = false;
        $fieldIdx = 0;
        foreach ($this->fields as $field) {
            if ($field->field_type === 'fixed' && ($field->fixed_field_name ?? null) === 'jenis_barang') { $fieldIdx++; continue; }
            if (($field->nama_field ?? null) === 'jenis_barang') { $fieldIdx++; continue; }

            if ($this->hasNomorIdentitas && isset($niFieldIndices[$fieldIdx])) {
                if (!$niAdded) {
                    $headers[] = 'NOMOR IDENTITAS'; // 1 kolom untuk semua 4 field
                    $niAdded = true;
                }
                // Skip 3 field berikutnya (sudah digabung)
                $fieldIdx++;
                continue;
            }

            if ($field->field_type === 'fixed' && $field->fixed_field_name === 'kondisi') {
                $headers[] = 'KONDISI BARANG';
                $headers[] = '';
                $headers[] = '';
            } elseif ($field->field_type === 'fixed' && ($field->fixed_field_name ?? null) === 'nama_barang') {
                $headers[] = 'JENIS BARANG / NAMA BARANG';
            } else {
                $headers[] = mb_strtoupper($this->exportLabelForField($field));
            }
            $fieldIdx++;
        }
        return $headers;
    }

    public function title(): string
    {
        $safe = preg_replace('/[\/\\\\?\*\[\]:]+/', '', $this->jenis->nama ?? 'Sheet');
        return mb_substr(trim($safe), 0, 31) ?: 'Sheet';
    }

    public function map($inventaris): array
    {
        $row = [++$this->rowCounter];
        // Kumpulkan nilai NOMOR IDENTITAS dulu jika ada
        $niValues = [];
        if ($this->hasNomorIdentitas) {
            $niFieldStart = $this->nomorIdentitasColStart - 2; // field index (0-based)
            $fieldArr = $this->fields->values();
            for ($k = 0; $k < 4; $k++) {
                $f = $fieldArr[$niFieldStart + $k] ?? null;
                $niValues[] = $f ? trim((string)$f->getValueFromInventaris($inventaris)) : '-';
            }
        }
        $niAdded = false;
        $fieldIdx = 0;
        foreach ($this->fields as $field) {
            if ($field->field_type === 'fixed' && ($field->fixed_field_name ?? null) === 'jenis_barang') { $fieldIdx++; continue; }
            if (($field->nama_field ?? null) === 'jenis_barang') { $fieldIdx++; continue; }

            // Nomor Identitas: gabungkan 4 nilai jadi 1 cell dengan newline
            if ($this->hasNomorIdentitas) {
                $niStart = $this->nomorIdentitasColStart - 2;
                $niEnd   = $niStart + 3;
                if ($fieldIdx >= $niStart && $fieldIdx <= $niEnd) {
                    if (!$niAdded) {
                        $row[] = implode("\n", $niValues);
                        $niAdded = true;
                    }
                    $fieldIdx++;
                    continue;
                }
            }

            $value = $field->getValueFromInventaris($inventaris);
            if ($field->field_type === 'fixed' && $field->fixed_field_name === 'kondisi') {
                $kondisi = strtolower(trim((string) $value));
                $kondisi = str_replace(' ', '_', $kondisi);
                $row[] = ($kondisi === 'baik' || $kondisi === 'b') ? 'X' : '';
                $row[] = ($kondisi === 'rusak_ringan' || $kondisi === 'rr') ? 'X' : '';
                $row[] = ($kondisi === 'rusak_berat' || $kondisi === 'rb') ? 'X' : '';
            } elseif ($this->isSumTargetField($field)) {
                $num = $this->parseNumberForExcel($value);
                $this->sumTargetTotal += $num;
                $isBlank = ($value === null || $value === '' || $value === '-');
                $row[] = $isBlank ? '' : number_format($num, fmod($num, 1) != 0 ? 2 : 0, ',', '.');
            } elseif ($this->isCurrencyField($field)) {
                $num = $this->parseNumberForExcel($value);
                $isBlank = ($value === null || $value === '' || $value === '-');
                $row[] = $isBlank ? '' : number_format($num, fmod($num, 1) != 0 ? 2 : 0, ',', '.');
            } elseif (in_array($field->tipe ?? null, ['decimal_m2', 'decimal_km', 'decimal_m'], true)) {
                $num = $this->parseNumberForExcel($value);
                $isBlank = ($value === null || $value === '' || $value === '-');
                $row[] = $isBlank ? '' : number_format($num, fmod($num, 1) != 0 ? 2 : 0, ',', '.');
            } else {
                $row[] = $value;
            }
            $fieldIdx++;
        }
        $row = array_map([$this, 'sanitizeFormulaInjection'], $row);
        return $row;
    }

    public function sanitizeFormulaInjection($value)
    {
        if (!is_string($value)) {
            return $value;
        }

        $trimmed = trim($value);
        if ($trimmed === '' || $trimmed === '-') {
            return $value;
        }

        $firstChar = $trimmed[0];
        if (in_array($firstChar, ['=', '+', '-', '@'], true)) {
            if ($firstChar === '-' && is_numeric($trimmed)) {
                return $value;
            }
            return "'" . $value;
        }

        return $value;
    }

    public function columnWidths(): array
    {
        $widths = ['A' => 20];
        $colLetters = array_merge(range('B', 'Z'));
        $idx = 0;
        $niStart = $this->hasNomorIdentitas ? ($this->nomorIdentitasColStart - 2) : -1; // field index 0-based
        $niSkip  = 0; // counter untuk skip 3 field berikutnya
        foreach ($this->fields as $fieldIdx => $field) {
            $letter = $colLetters[$idx] ?? null;
            if (!$letter) break;
            $fixedName = $field->fixed_field_name ?? '';

            // NOMOR IDENTITAS: 4 field → 1 kolom lebar
            if ($this->hasNomorIdentitas && $fieldIdx >= $niStart && $fieldIdx < $niStart + 4) {
                if ($fieldIdx === $niStart) {
                    $widths[$letter] = 32; // diperlebar untuk 4 label/nilai vertikal
                    $idx++;
                }
                // Skip field 2-4 (tidak ada kolom di output)
                continue;
            }

            if ($field->field_type === 'fixed' && $fixedName === 'kondisi') {
                // Kondisi dipersempit saat kombinasi dengan NOMOR IDENTITAS (kendaraan)
                $kondisiW = $this->hasNomorIdentitas ? 9 : 16;
                $widths[$letter]                      = $kondisiW;
                $widths[$colLetters[$idx + 1] ?? 'Z'] = $kondisiW;
                $widths[$colLetters[$idx + 2] ?? 'Z'] = $kondisiW;
                $idx += 3;
            } elseif (in_array($fixedName, ['nama_barang', 'keterangan'])) {
                $widths[$letter] = 32; $idx++;
            } elseif ($fixedName === 'nilai_perolehan') {
                $widths[$letter] = 22; $idx++;
            } elseif (in_array($fixedName, ['nup', 'kode_barang', 'tahun_perolehan'])) {
                $widths[$letter] = $fixedName === 'nup' ? 20 : 16; $idx++;
            } else {
                $widths[$letter] = 22; $idx++;
            }
        }
        return $widths;
    }

    public function registerEvents(): array
    {
        return [
            AfterSheet::class => function (AfterSheet $event) {
                $sheet     = $event->sheet->getDelegate();
                $totalCols = $this->totalColumns;
                $lastCol   = $this->numToColLetter($totalCols);

                // 1. Insert title + periode + info desa block (11 rows). Heading di row 12.
                $sheet->insertNewRowBefore(1, 11);
                $headRow = 12;

                $tahunFilter = (int)($this->filters['tahun'] ?? date('Y'));
                if (!$tahunFilter) $tahunFilter = (int)date('Y');
                $periodeStr = 'Periode Tanggal, 31 Desember ' . $tahunFilter;

                $bulanMap = [1=>'Januari',2=>'Februari',3=>'Maret',4=>'April',5=>'Mei',6=>'Juni',
                             7=>'Juli',8=>'Agustus',9=>'September',10=>'Oktober',11=>'November',12=>'Desember'];
                $tglBeritaAcara = '';
                if ($this->tanggalDokumen) {
                    try {
                        $dt = \Carbon\Carbon::parse($this->tanggalDokumen);
                        $tglBeritaAcara = $dt->day . ' ' . $bulanMap[$dt->month] . ' ' . $dt->year;
                    } catch (\Throwable $e) {}
                }
                if (!$tglBeritaAcara) {
                    $now = now();
                    $tglBeritaAcara = $now->day . ' ' . $bulanMap[$now->month] . ' ' . $now->year;
                }

                $sheet->setCellValue('A1', 'LAPORAN HASIL INVENTARIS ASET DESA');
                $sheet->mergeCells("A1:{$lastCol}1");
                $sheet->getStyle('A1')->applyFromArray([
                    'font'      => ['bold' => true, 'size' => 14],
                    'alignment' => ['horizontal' => Alignment::HORIZONTAL_CENTER, 'vertical' => Alignment::VERTICAL_CENTER],
                ]);
                $sheet->getRowDimension(1)->setRowHeight(22);

                $sheet->setCellValue('A2', 'BERUPA ' . strtoupper($this->jenis->nama));
                $sheet->mergeCells("A2:{$lastCol}2");
                $sheet->getStyle('A2')->applyFromArray([
                    'font'      => ['bold' => true, 'size' => 14],
                    'alignment' => ['horizontal' => Alignment::HORIZONTAL_CENTER, 'vertical' => Alignment::VERTICAL_CENTER],
                ]);
                $sheet->getRowDimension(2)->setRowHeight(20);

                $sheet->setCellValue('A3', $periodeStr);
                $sheet->mergeCells("A3:{$lastCol}3");
                $sheet->getStyle('A3')->applyFromArray([
                    'font'      => ['bold' => false, 'size' => 11],
                    'alignment' => ['horizontal' => Alignment::HORIZONTAL_CENTER, 'vertical' => Alignment::VERTICAL_CENTER],
                ]);
                $sheet->getRowDimension(3)->setRowHeight(18);
                $sheet->getRowDimension(4)->setRowHeight(10);

                $d = $this->desa;
                $infoKiri = [
                    ['Provinsi',        ($d['provinsi_kode'] ?? '') . '. ' . ($d['provinsi_nama'] ?? '')],
                    ['Kab./Kota',       ($d['kabupaten_kode'] ?? '') . '. ' . ($d['kabupaten_nama'] ?? '')],
                    ['Kecamatan',       ($d['kecamatan_kode'] ?? '') . '. ' . ($d['kecamatan_nama'] ?? '')],
                    ['Desa',            ($d['kode_lokasi'] ?? '') . '. ' . ($d['desa_nama'] ?? '')],
                    ['UPB',             $d['upb'] ?? ''],
                    ['No. Kode Lokasi', ($d['kode_lokasi'] ?? '') . '.'],
                ];
                $rr = 5;
                // Courier New (monospace) + str_pad(15) → titik dua sejajar sempurna.
                $pad = 15; // "No. Kode Lokasi" = 15 char
                foreach ($infoKiri as [$lbl, $val]) {
                    $sheet->setCellValue("A{$rr}", str_pad($lbl, $pad) . ' :');
                    $sheet->setCellValue("B{$rr}", $val);
                    $sheet->getStyle("A{$rr}")->getFont()->setName('Courier New')->setSize(10);
                    $sheet->getStyle("B{$rr}")->getFont()->setBold(true)->setSize(11);
                    $sheet->getStyle("A{$rr}")->getAlignment()->setHorizontal(Alignment::HORIZONTAL_LEFT);
                    $sheet->getStyle("B{$rr}")->getAlignment()->setHorizontal(Alignment::HORIZONTAL_LEFT);
                    $rr++;
                }
                // Lebar kolom A mengikuti label terpanjang "No. Kode Lokasi :"
                $sheet->getColumnDimension('A')->setWidth(20);

                $rightColNum = max(5, $totalCols - 1);
                $rightLbl    = $this->numToColLetter($rightColNum);
                $rightSep    = $this->numToColLetter(min($totalCols, $rightColNum + 1));
                $rightVal    = $this->numToColLetter(min($totalCols, $rightColNum + 2));
                
                // Format Tanggal BA dari input (untuk ditampilkan di header)
                $tglBaFormatted = '';
                if ($this->docBaTanggal) {
                    try {
                        $dt = \Carbon\Carbon::parse($this->docBaTanggal);
                        $tglBaFormatted = $dt->day . ' ' . $bulanMap[$dt->month] . ' ' . $dt->year;
                    } catch (\Throwable $e) {}
                }
                
                $sheet->setCellValue("{$rightLbl}5", 'Lampiran');
                $sheet->setCellValue("{$rightSep}5", ':');
                $sheet->setCellValue("{$rightVal}5", $this->docLampiran ?: '-');
                $sheet->setCellValue("{$rightLbl}7", 'Berita Acara Inventarisasi Aset Desa');
                $sheet->setCellValue("{$rightLbl}8", 'Nomor');
                $sheet->setCellValue("{$rightSep}8", ':');
                $sheet->setCellValue("{$rightVal}8", $this->docBaNomor ?: '-');
                $sheet->setCellValue("{$rightLbl}9", 'Tanggal');
                $sheet->setCellValue("{$rightSep}9", ':');
                $sheet->setCellValue("{$rightVal}9", $tglBaFormatted ?: $tglBeritaAcara);
                $sheet->getStyle("{$rightLbl}5:{$rightVal}9")->applyFromArray(['font' => ['bold' => true, 'size' => 11]]);
                $sheet->getRowDimension(11)->setRowHeight(10);

                // 2. Handle kondisi sub-header OR UKURAN group (Jalan Irigasi)
                if ($this->hasUkuranGroup) {
                    // Baris 2: sub-kolom UKURAN
                    $subRow = $headRow + 1;
                    $sheet->insertNewRowBefore($subRow, 1);

                    $ukuranColNums = array_keys($this->ukuranColNums);
                    $firstUkuranCol = $this->numToColLetter($ukuranColNums[0]);
                    $lastUkuranCol  = $this->numToColLetter($ukuranColNums[count($ukuranColNums) - 1]);

                    // Merge 3 kolom UKURAN di baris heading
                    $sheet->mergeCells("{$firstUkuranCol}{$headRow}:{$lastUkuranCol}{$headRow}");
                    $sheet->setCellValue("{$firstUkuranCol}{$headRow}", 'U K U R A N');

                    // Sub-kolom di baris 2
                    foreach ($this->ukuranColNums as $colNum => $type) {
                        $cl = $this->numToColLetter($colNum);
                        $headers = $this->headings();
                        $headerIdx = $colNum - 1; // headings() index
                        $label = isset($headers[$headerIdx]) ? mb_strtoupper($headers[$headerIdx]) : mb_strtoupper($type);
                        $sheet->setCellValue("{$cl}{$subRow}", $label);
                    }

                    // Kolom non-UKURAN: rowspan (merge headRow:subRow)
                    for ($c = 1; $c <= $totalCols; $c++) {
                        if (!isset($this->ukuranColNums[$c])) {
                            $cl = $this->numToColLetter($c);
                            $sheet->mergeCells("{$cl}{$headRow}:{$cl}{$subRow}");
                        }
                    }

                    $sheet->getStyle("A{$headRow}:{$lastCol}{$subRow}")->applyFromArray([
                        'font'      => ['bold' => true, 'size' => 12],
                        'alignment' => ['horizontal' => Alignment::HORIZONTAL_CENTER, 'vertical' => Alignment::VERTICAL_CENTER, 'wrapText' => true],
                        'fill'      => ['fillType' => Fill::FILL_SOLID, 'startColor' => ['rgb' => 'FFFFFF']],
                        'borders'   => ['allBorders' => ['borderStyle' => Border::BORDER_THIN, 'color' => ['rgb' => '000000']]],
                    ]);
                    $sheet->getRowDimension($headRow)->setRowHeight(60);
                    $sheet->getRowDimension($subRow)->setRowHeight(36);
                    $headEnd = $subRow;
                } elseif ($this->hasNomorIdentitas) {
                    // Header 2 baris: NOMOR IDENTITAS + KONDISI BARANG (jika ada)
                    $subRow = $headRow + 1;
                    $sheet->insertNewRowBefore($subRow, 1);

                    $niFirstCol = $this->numToColLetter($this->nomorIdentitasColStart);
                    $sheet->setCellValue("{$niFirstCol}{$headRow}", 'NOMOR IDENTITAS');
                    $subLabel = implode("\n", array_map(fn($l) => mb_strtoupper($l) . ' :', $this->nomorIdentitasLabels));
                    $sheet->setCellValue("{$niFirstCol}{$subRow}", $subLabel);
                    $sheet->getStyle("{$niFirstCol}{$subRow}")->getAlignment()
                        ->setHorizontal(Alignment::HORIZONTAL_LEFT)
                        ->setVertical(Alignment::VERTICAL_CENTER)
                        ->setWrapText(true);

                    // Handle KONDISI BARANG jika ada
                    if ($this->hasKondisi) {
                        $kL = $this->numToColLetter($this->kondisiColIndex + 1);
                        $rL = $this->numToColLetter($this->kondisiColIndex + 2);
                        $bL = $this->numToColLetter($this->kondisiColIndex + 3);
                        $sheet->mergeCells("{$kL}{$headRow}:{$bL}{$headRow}");
                        $sheet->setCellValue("{$kL}{$subRow}", 'Baik');
                        $sheet->setCellValue("{$rL}{$subRow}", 'Rusak Ringan');
                        $sheet->setCellValue("{$bL}{$subRow}", 'Rusak Berat');
                    }

                    // Kolom lain (bukan NI dan bukan kondisi sub): merge headRow:subRow
                    for ($c = 1; $c <= $totalCols; $c++) {
                        $isNiCol     = ($c === $this->nomorIdentitasColStart);
                        $isKondisiSub = $this->hasKondisi
                            && ($c >= ($this->kondisiColIndex + 1)) && ($c <= ($this->kondisiColIndex + 3));
                        if (!$isNiCol && !$isKondisiSub) {
                            $cl = $this->numToColLetter($c);
                            $sheet->mergeCells("{$cl}{$headRow}:{$cl}{$subRow}");
                        }
                    }

                    $sheet->getStyle("A{$headRow}:{$lastCol}{$subRow}")->applyFromArray([
                        'font'      => ['bold' => true, 'size' => 12],
                        'alignment' => ['horizontal' => Alignment::HORIZONTAL_CENTER, 'vertical' => Alignment::VERTICAL_CENTER, 'wrapText' => true],
                        'fill'      => ['fillType' => Fill::FILL_SOLID, 'startColor' => ['rgb' => 'FFFFFF']],
                        'borders'   => ['allBorders' => ['borderStyle' => Border::BORDER_THIN, 'color' => ['rgb' => '000000']]],
                    ]);
                    $sheet->getRowDimension($headRow)->setRowHeight(30);
                    $sheet->getRowDimension($subRow)->setRowHeight(80);
                    $headEnd = $subRow;
                } elseif ($this->hasKondisi) {
                    $subRow = $headRow + 1;
                    $sheet->insertNewRowBefore($subRow, 1);
                    $kL = $this->numToColLetter($this->kondisiColIndex + 1);
                    $rL = $this->numToColLetter($this->kondisiColIndex + 2);
                    $bL = $this->numToColLetter($this->kondisiColIndex + 3);

                    $sheet->mergeCells("{$kL}{$headRow}:{$bL}{$headRow}");
                    $sheet->setCellValue("{$kL}{$subRow}", 'Baik');
                    $sheet->setCellValue("{$rL}{$subRow}", 'Rusak Ringan');
                    $sheet->setCellValue("{$bL}{$subRow}", 'Rusak Berat');

                    for ($c = 1; $c <= $totalCols; $c++) {
                        $isKondisi = ($c >= ($this->kondisiColIndex + 1)) && ($c <= ($this->kondisiColIndex + 3));
                        if (!$isKondisi) {
                            $cl = $this->numToColLetter($c);
                            $sheet->mergeCells("{$cl}{$headRow}:{$cl}{$subRow}");
                        }
                    }

                    $sheet->getStyle("A{$headRow}:{$lastCol}{$subRow}")->applyFromArray([
                        'font'      => ['bold' => true, 'size' => 12],
                        'alignment' => ['horizontal' => Alignment::HORIZONTAL_CENTER, 'vertical' => Alignment::VERTICAL_CENTER, 'wrapText' => true],
                        'fill'      => ['fillType' => Fill::FILL_SOLID, 'startColor' => ['rgb' => 'FFFFFF']],
                        'borders'   => ['allBorders' => ['borderStyle' => Border::BORDER_THIN, 'color' => ['rgb' => '000000']]],
                    ]);
                    $sheet->getRowDimension($headRow)->setRowHeight(60);
                    $sheet->getRowDimension($subRow)->setRowHeight(36);
                    $headEnd = $subRow;
                } else {
                    $sheet->getStyle("A{$headRow}:{$lastCol}{$headRow}")->applyFromArray([
                        'font'      => ['bold' => true, 'size' => 12],
                        'alignment' => ['horizontal' => Alignment::HORIZONTAL_CENTER, 'vertical' => Alignment::VERTICAL_CENTER, 'wrapText' => true],
                        'fill'      => ['fillType' => Fill::FILL_SOLID, 'startColor' => ['rgb' => 'FFFFFF']],
                        'borders'   => ['allBorders' => ['borderStyle' => Border::BORDER_THIN, 'color' => ['rgb' => '000000']]],
                    ]);
                    $sheet->getRowDimension($headRow)->setRowHeight(60);
                    $headEnd = $headRow;
                }

                // 2b. Baris nomor kolom (1,2,3,...)
                $numRow = $headEnd + 1;
                $sheet->insertNewRowBefore($numRow, 1);
                $colNum = 1;
                for ($c = 1; $c <= $totalCols; $c++) {
                    $isKondisiSub = $this->hasKondisi
                        && ($c > ($this->kondisiColIndex + 1)) && ($c <= ($this->kondisiColIndex + 3));
                    if ($isKondisiSub) continue;
                    $cl = $this->numToColLetter($c);
                    if ($this->hasKondisi && $c === ($this->kondisiColIndex + 1)) {
                        $bL = $this->numToColLetter($this->kondisiColIndex + 3);
                        $sheet->mergeCells("{$cl}{$numRow}:{$bL}{$numRow}");
                    }
                    $sheet->setCellValue("{$cl}{$numRow}", $colNum);
                    $colNum++;
                }
                $sheet->getStyle("A{$numRow}:{$lastCol}{$numRow}")->applyFromArray([
                    'font'      => ['bold' => true, 'size' => 11],
                    'alignment' => ['horizontal' => Alignment::HORIZONTAL_CENTER, 'vertical' => Alignment::VERTICAL_CENTER],
                    'borders'   => ['allBorders' => ['borderStyle' => Border::BORDER_THIN, 'color' => ['rgb' => '000000']]],
                ]);
                $sheet->getRowDimension($numRow)->setRowHeight(16);
                $dataStart = $numRow + 1;

                // 3. Style data rows
                $lastRow = $sheet->getHighestRow();
                if ($lastRow >= $dataStart) {
                    $sheet->getStyle("A{$dataStart}:{$lastCol}{$lastRow}")->applyFromArray([
                        'borders'   => ['allBorders' => ['borderStyle' => Border::BORDER_THIN, 'color' => ['rgb' => '000000']]],
                        'font'      => ['size' => 10],
                        'alignment' => ['wrapText' => true],
                    ]);
                    // Paksa semua sel data rata kiri agar kolom numerik tidak otomatis ke kanan.
                    $sheet->getStyle("A{$dataStart}:{$lastCol}{$lastRow}")->getAlignment()->setHorizontal(Alignment::HORIZONTAL_LEFT);
                    for ($r = $dataStart; $r <= $lastRow; $r++) {
                        $bg = 'FFFFFF';
                        $sheet->getStyle("A{$r}:{$lastCol}{$r}")->getFill()
                            ->setFillType(Fill::FILL_SOLID)->getStartColor()->setRGB($bg);
                        $sheet->getStyle("A{$r}:{$lastCol}{$r}")->getAlignment()->setVertical(Alignment::VERTICAL_TOP);
                        $sheet->getRowDimension($r)->setRowHeight(-1); // auto-height mengikuti konten
                    }
                    $sheet->getStyle("A{$dataStart}:A{$lastRow}")->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
                    // Format @Text untuk kolom yang berisi teks dengan leading zero (nup, kode_barang)
                    // agar Excel tidak tampilkan warning "number stored as text"
                    if (!empty($this->textColLetters)) {
                        foreach ($this->textColLetters as $tCol) {
                            $sheet->getStyle("{$tCol}{$dataStart}:{$tCol}{$lastRow}")
                                ->getNumberFormat()
                                ->setFormatCode(\PhpOffice\PhpSpreadsheet\Style\NumberFormat::FORMAT_TEXT);
                        }
                    }
                    if ($this->hasKondisi) {
                        $kL = $this->numToColLetter($this->kondisiColIndex + 1);
                        $bL = $this->numToColLetter($this->kondisiColIndex + 3);
                        $sheet->getStyle("{$kL}{$dataStart}:{$bL}{$lastRow}")->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
                    }
                    // NOMOR IDENTITAS: left align + wrapText untuk nilai vertikal
                    if ($this->hasNomorIdentitas) {
                        $niCol = $this->numToColLetter($this->nomorIdentitasColStart);
                        $sheet->getStyle("{$niCol}{$dataStart}:{$niCol}{$lastRow}")->getAlignment()
                            ->setHorizontal(Alignment::HORIZONTAL_LEFT)
                            ->setVertical(Alignment::VERTICAL_CENTER)
                            ->setWrapText(true);
                    }
                    // Center untuk kolom kode_barang, nup, tahun_perolehan
                    foreach ($this->centerColLetters as $ctrCol) {
                        $sheet->getStyle("{$ctrCol}{$dataStart}:{$ctrCol}{$lastRow}")->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
                    }
                    // Nilai perolehan (currency) → kanan
                    foreach ($this->currencyColLetters as $curCol) {
                        $sheet->getStyle("{$curCol}{$dataStart}:{$curCol}{$lastRow}")->getAlignment()->setHorizontal(Alignment::HORIZONTAL_RIGHT);
                    }
                    // Format kolom mata uang dengan number format text agar separator titik Indonesia tidak terkonversi
                    foreach ($this->currencyColLetters as $cLetter) {
                        $sheet->getStyle("{$cLetter}{$dataStart}:{$cLetter}{$lastRow}")
                              ->getNumberFormat()
                              ->setFormatCode(NumberFormat::FORMAT_TEXT);
                    }
                }

                // 4. Jumlah row → "JUMLAH HARGA" (center), nilai (right)
                $jumlahRow = ($lastRow >= $dataStart) ? $lastRow + 1 : $dataStart + 1;
                $sheet->getRowDimension($jumlahRow)->setRowHeight(22);
                if ($this->nilaiColLetter !== '') {
                    $nilaiColNum = $this->colLetterToNum($this->nilaiColLetter);
                    if ($nilaiColNum > 2) {
                        $mergeEnd = $this->numToColLetter($nilaiColNum - 1);
                        $sheet->mergeCells("A{$jumlahRow}:{$mergeEnd}{$jumlahRow}");
                    }
                    $sheet->setCellValue("A{$jumlahRow}", 'JUMLAH HARGA');
                    // Write total as Indonesian formatted string
                    $sheet->setCellValue("{$this->nilaiColLetter}{$jumlahRow}", number_format($this->sumTargetTotal, fmod($this->sumTargetTotal, 1) != 0 ? 2 : 0, ',', '.'));
                    // Nilai → kanan; label JUMLAH HARGA → center
                    $sheet->getStyle("{$this->nilaiColLetter}{$jumlahRow}")->getAlignment()->setHorizontal(Alignment::HORIZONTAL_RIGHT);
                    $sheet->getStyle("A{$jumlahRow}")->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
                } else {
                    $sheet->mergeCells("A{$jumlahRow}:{$lastCol}{$jumlahRow}");
                    $sheet->setCellValue("A{$jumlahRow}", 'JUMLAH HARGA');
                    $sheet->getStyle("A{$jumlahRow}")->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
                }
                $sheet->getStyle("A{$jumlahRow}:{$lastCol}{$jumlahRow}")->applyFromArray([
                    'borders' => ['allBorders' => ['borderStyle' => Border::BORDER_THIN, 'color' => ['rgb' => '000000']]],
                    'font'    => ['bold' => true, 'size' => 11],
                    'alignment' => ['vertical' => Alignment::VERTICAL_CENTER],
                ]);

                // 5. Petunjuk Pengisian — DIHAPUS (tidak digunakan lagi)
                $pRow = $jumlahRow + 2;

                $activePejabats = [];
                if (is_array($this->pejabatSnapshot)) {
                    foreach ($this->pejabatSnapshot as $p) {
                        if (!$p) continue;
                        $nama = trim((string)($p['nama'] ?? ($p['nama_pejabat'] ?? '')));
                        if ($nama !== '') {
                            $activePejabats[] = $p;
                        }
                    }
                }

                if (count($activePejabats) > 0) {
                    // Untuk jenis dengan kolom sangat sedikit (orphan: 1 kolom), 
                    // alokasikan full width untuk TTD. Untuk normal jenis, split 70% nama / 30% TTD
                    if ($totalCols <= 2) {
                        // Very small table (orphan): TTD area full width, no nama area
                        $signColNum = 1;
                        $nameCols = 0;
                        $ttdCols = $totalCols;
                    } else {
                        // Normal table: ~70% untuk nama, ~30% untuk TTD
                        $signColNum = (int) floor($totalCols * 0.7);
                        $signColNum = max(1, $signColNum);
                        $signColsTotal = $totalCols - $signColNum + 1;
                        $nameCols = max(1, (int) ceil($signColsTotal * 0.6));
                        $ttdCols = max(2, $signColsTotal - $nameCols); // Min 2 for TTD area
                    }
                    
                    $signCol = $this->numToColLetter($signColNum);
                    $signEndCol = $lastCol;
                    $signRow = $jumlahRow + 2;

                    $sheet->mergeCells("{$signCol}{$signRow}:{$signEndCol}{$signRow}");
                    $sheet->setCellValue("{$signCol}{$signRow}", 'TIM INVENTARISASI');
                    $sheet->getStyle("{$signCol}{$signRow}")->applyFromArray([
                        'font' => ['bold' => true, 'size' => 12],
                        'alignment' => ['horizontal' => Alignment::HORIZONTAL_CENTER, 'vertical' => Alignment::VERTICAL_CENTER],
                    ]);

                    $nameEndColNum = $signColNum + $nameCols - 1;
                    $nameEndCol    = $this->numToColLetter($nameEndColNum);
                    $ttdStartCol   = $ttdCols > 0 ? $this->numToColLetter($nameEndColNum + 1) : null;

                    $line = $signRow + 2;
                    $n = 1;
                    foreach ($activePejabats as $p) {
                        $nama = trim((string) ($p['nama'] ?? ($p['nama_pejabat'] ?? '')));
                        if ($nama === '') {
                            continue;
                        }

                        // Kolom nama (kiri)
                        if ($nameEndCol !== $signCol) {
                            $sheet->mergeCells("{$signCol}{$line}:{$nameEndCol}{$line}");
                        }
                        $sheet->setCellValue("{$signCol}{$line}", $n . '. ' . $nama);
                        $sheet->getStyle("{$signCol}{$line}:{$nameEndCol}{$line}")->applyFromArray([
                            'font'      => ['size' => 12],
                            'alignment' => ['horizontal' => Alignment::HORIZONTAL_LEFT, 'vertical' => Alignment::VERTICAL_CENTER],
                        ]);
                        // Cell height tetap kecil & konsisten — gambar floating di atasnya (tidak mendorong cell)
                        $sheet->getRowDimension($line)->setRowHeight(32);

                        // Kolom TTD (kanan) — gambar TTD floating di atas cell, border bottom selalu muncul
                        if ($ttdStartCol !== null) {
                            if ($ttdStartCol !== $signEndCol) {
                                $sheet->mergeCells("{$ttdStartCol}{$line}:{$signEndCol}{$line}");
                            }

                            $ttdFull = '';
                            $rawPath = trim((string) ($p['ttd_path'] ?? ''));
                            if ($rawPath !== '') {
                                $ttdFull = storage_path('app/public/' . ltrim($rawPath, '/'));
                            }

                            // Tanda kurung "(        )" — bukan garis bawah.
                            // Gambar TTD (jika ada) floating di tengah antara kurung.
                            $sheet->setCellValue("{$ttdStartCol}{$line}", '(                                   )');
                            $sheet->getStyle("{$ttdStartCol}{$line}:{$signEndCol}{$line}")->applyFromArray([
                                'borders' => [
                                    'bottom' => ['borderStyle' => Border::BORDER_NONE],
                                    'top'    => ['borderStyle' => Border::BORDER_NONE],
                                    'left'   => ['borderStyle' => Border::BORDER_NONE],
                                    'right'  => ['borderStyle' => Border::BORDER_NONE],
                                ],
                                'font'      => ['size' => 12],
                                'alignment' => ['vertical' => Alignment::VERTICAL_CENTER, 'horizontal' => Alignment::HORIZONTAL_CENTER],
                            ]);

                            if ($this->includeSignatureImage && $ttdFull !== '' && file_exists($ttdFull)) {
                                $ttdType      = trim((string) ($p['ttd_type'] ?? 'gambar'));
                                $isElektronik = ($ttdType === 'elektronik');

                                // Batas dimensi berbeda per tipe:
                                // - gambar: 38px tinggi, row dinaikkan ke 44pt agar proporsional dan bisa di-center
                                // - elektronik: lebih besar, cell mengikuti gambar
                                $maxH = $isElektronik ? 50 : 55;
                                $maxW = $isElektronik ? 200 : 220;

                                $targetH = $maxH;
                                $targetW = (int) round($maxW * 0.75); // fallback proporsional
                                $imgSize = @getimagesize($ttdFull);
                                if ($imgSize && $imgSize[0] > 0 && $imgSize[1] > 0) {
                                    $targetW = (int) round($imgSize[0] * ($targetH / $imgSize[1]));
                                    if ($targetW > $maxW) {
                                        $targetH = (int) round($imgSize[1] * ($maxW / $imgSize[0]));
                                        $targetW = $maxW;
                                    }
                                    $targetH = max(12, $targetH);
                                    $targetW = max(20, $targetW);
                                }

                                // Row height: gambar → 48pt (cell lebih kecil, image 55px tetap muat), elektronik → ikuti gambar
                                $rowHeightPts = $isElektronik ? ($targetH + 10) : 48;
                                $sheet->getRowDimension($line)->setRowHeight($rowHeightPts);

                                // Hitung pixel lebar kolom TTD untuk horizontal center
                                $ttdStartColIdx = $this->colLetterToNum($ttdStartCol);
                                $ttdEndColIdx   = $this->colLetterToNum($signEndCol);
                                $cellTtdWidthPx = 0;
                                for ($ci = $ttdStartColIdx; $ci <= $ttdEndColIdx; $ci++) {
                                    $cl   = $this->numToColLetter($ci);
                                    $colW = $sheet->getColumnDimension($cl)->getWidth();
                                    if ($colW < 0) $colW = 8;
                                    $cellTtdWidthPx += (int) round($colW * 7);
                                }

                                // Row height px (1pt ≈ 1.333px)
                                $cellHeightPx = (int) round($rowHeightPts * 1.333);

                                // Scale down targetW jika terlalu besar untuk TTD area (untuk jenis dengan kolom banyak)
                                $maxTtdWidthPx = (int) round($cellTtdWidthPx * 0.85); // Leave 15% margin
                                if ($targetW > $maxTtdWidthPx && $maxTtdWidthPx > 0) {
                                    $scaleFactor = $maxTtdWidthPx / $targetW;
                                    $targetW = $maxTtdWidthPx;
                                    $targetH = (int) round($targetH * $scaleFactor);
                                }

                                // Center offset — gunakan anchor column approach (seperti BukuInventarisSheet)
                                // untuk handling jenis dengan kolom banyak dengan TTD area terbatas
                                $targetStartPx = max(0, (int) (($cellTtdWidthPx - $targetW) / 2));
                                
                                // Cari anchor column: kolom yang MENGANDUNG targetStartPx
                                $cumPx = 0;
                                $anchorCol = $ttdStartCol;
                                $offsetX = $targetStartPx;
                                for ($ci = $ttdStartColIdx; $ci <= $ttdEndColIdx; $ci++) {
                                    $cl = $this->numToColLetter($ci);
                                    $colW = $sheet->getColumnDimension($cl)->getWidth();
                                    if ($colW < 0) $colW = 8;
                                    $cwPx = (int) round($colW * 7);
                                    if ($cumPx + $cwPx > $targetStartPx || $ci === $ttdEndColIdx) {
                                        $anchorCol = $cl;
                                        $offsetX = max(0, $targetStartPx - $cumPx);
                                        break;
                                    }
                                    $cumPx += $cwPx;
                                }
                                
                                $offsetY = max(2, (int) (($cellHeightPx - $targetH) / 2));

                                $drawing = new \PhpOffice\PhpSpreadsheet\Worksheet\Drawing();
                                $drawing->setName('TTD_' . $n);
                                $drawing->setDescription('Tanda Tangan ' . $nama);
                                $drawing->setPath($ttdFull);
                                $drawing->setCoordinates("{$anchorCol}{$line}");
                                $drawing->setOffsetX($offsetX);
                                $drawing->setOffsetY($offsetY);
                                $drawing->setResizeProportional(false);
                                $drawing->setWidth($targetW);
                                $drawing->setHeight($targetH);
                                $drawing->setWorksheet($sheet);
                            }
                        }

                        $line += 1; // langsung ke pejabat berikutnya tanpa baris kosong
                        $n++;
                    }

                    // Petunjuk rows dihapus — tidak ada re-apply alignment yang diperlukan
                }

                // 7. Print settings
                $sheet->getPageSetup()
                    ->setOrientation(\PhpOffice\PhpSpreadsheet\Worksheet\PageSetup::ORIENTATION_LANDSCAPE)
                    ->setPaperSize(\PhpOffice\PhpSpreadsheet\Worksheet\PageSetup::PAPERSIZE_A4)
                    ->setFitToPage(true)->setFitToWidth(1)->setFitToHeight(0);
                $sheet->getPageMargins()->setTop(0.5)->setBottom(0.5)->setLeft(0.5)->setRight(0.5);
            },
        ];
    }

    protected function colLetterToNum(string $letter): int
    {
        $result = 0;
        foreach (str_split(strtoupper($letter)) as $char) {
            $result = $result * 26 + (ord($char) - 64);
        }
        return $result;
    }

    protected function isSumTargetField($field): bool
    {
        if ($this->sumTargetFieldKey === null) {
            return false;
        }
        return $this->makeFieldKey($field) === $this->sumTargetFieldKey;
    }

    /**
     * Cek apakah field ini adalah field mata uang (format Rp).
     * Mendelegasikan ke JenisInventarisData::isCurrencyField() sebagai single source of truth.
     */
    protected function isCurrencyField($field): bool
    {
        return \App\Models\JenisInventarisData::isCurrencyField(
            $field->fixed_field_name ?? null,
            $field->tipe ?? null,
            $field->nama_field ?? null,
            $field->label_field ?? null
        );
    }

    protected function resolveSumTargetFieldKey(): ?string
    {
        $scored = [];

        foreach ($this->fields as $field) {
            if ($field->field_type === 'fixed' && $field->fixed_field_name === 'kondisi') {
                continue;
            }

            $score = $this->sumTargetScore($field);
            if ($score <= 0) {
                continue;
            }

            $scored[] = [
                'key' => $this->makeFieldKey($field),
                'score' => $score,
                'order' => (int) ($field->order ?? 999),
                'id' => (int) ($field->id ?? 0),
                'label' => (string) ($field->label_field ?? ''),
            ];
        }

        if (empty($scored)) {
            return null;
        }

        usort($scored, function ($a, $b) {
            if ($a['score'] !== $b['score']) {
                return $b['score'] <=> $a['score'];
            }
            if ($a['order'] !== $b['order']) {
                return $a['order'] <=> $b['order'];
            }
            return $a['id'] <=> $b['id'];
        });

        if (count($scored) > 1 && $scored[0]['score'] === $scored[1]['score']) {
            Log::warning('Inventaris export SUM target ambiguous; using deterministic fallback', [
                'jenis_inventaris_id' => $this->jenisId,
                'selected' => $scored[0],
                'alternative' => $scored[1],
            ]);
        }

        return $scored[0]['key'];
    }

    protected function sumTargetScore($field): int
    {
        if ($field->field_type === 'fixed' && $field->fixed_field_name === 'nilai_perolehan') {
            return 100000;
        }

        $tipe = strtolower((string) ($field->tipe ?? ''));

        $label = $this->normalizeIdentifier((string) ($field->label_field ?? ''));
        $name = $this->normalizeIdentifier((string) ($field->nama_field ?? ''));
        $fixed = $this->normalizeIdentifier((string) ($field->fixed_field_name ?? ''));
        $haystack = trim($label . ' ' . $name . ' ' . $fixed);

        $exactAliases = [
            'nilai',
            'nilai perolehan',
            'nilaiperolehan',
            'nilai aset',
            'nilai rp',
            'nilai rupiah',
            'harga perolehan',
        ];

        $score = 0;
        if (in_array($label, $exactAliases, true) || in_array($name, $exactAliases, true) || in_array($fixed, $exactAliases, true)) {
            $score += 50000;
        }

        // Prefer number field, but still allow strong alias match on non-number field
        // to handle legacy/badly-configured schemas where nilai is stored as text.
        if ($tipe === 'number') {
            $score += 4000;
        } else {
            $score -= 1200;
        }

        if (str_contains($haystack, 'nilai')) $score += 6000;
        if (str_contains($haystack, 'harga')) $score += 4500;
        if (str_contains($haystack, 'perolehan')) $score += 3000;
        if (str_contains($haystack, 'rupiah') || str_contains($haystack, ' rp ')) $score += 1500;
        if (str_contains($haystack, 'aset')) $score += 800;

        // Penalize numeric fields likely not monetary totals.
        if (str_contains($haystack, 'luas')) $score -= 4000;
        if (str_contains($haystack, 'tahun')) $score -= 3500;
        if (str_contains($haystack, 'jumlah unit')) $score -= 3000;

        return max(0, $score);
    }

    protected function normalizeIdentifier(string $text): string
    {
        $text = mb_strtolower(trim($text));
        $text = preg_replace('/[^a-z0-9\s]+/', ' ', $text) ?? '';
        $text = preg_replace('/\s+/', ' ', $text) ?? '';
        return trim($text);
    }

    protected function makeFieldKey($field): string
    {
        if ($field->field_type === 'fixed') {
            return 'fixed:' . (string) ($field->fixed_field_name ?? '');
        }
        return 'id:' . (string) ($field->id ?? '0');
    }

    protected function parseNumberForExcel($value): float
    {
        if ($value === null || $value === '' || $value === '-') {
            return 0;
        }

        if (is_numeric($value)) {
            return (float) $value;
        }

        $s = str_replace(["\xc2\xa0", ' '], '', (string) $value);
        $s = preg_replace('/[^0-9,\.\-]/', '', $s) ?? '';
        if ($s === '') {
            return 0;
        }

        $hasComma = str_contains($s, ',');
        $hasDot = str_contains($s, '.');

        if ($hasComma && $hasDot) {
            if (strrpos($s, ',') > strrpos($s, '.')) {
                $s = str_replace('.', '', $s);
                $s = str_replace(',', '.', $s);
            } else {
                $s = str_replace(',', '', $s);
            }
        } elseif ($hasComma) {
            if (substr_count($s, ',') > 1) {
                $s = str_replace(',', '', $s);
            } else {
                $parts = explode(',', $s);
                $s = (count($parts) === 2 && strlen($parts[1]) <= 2)
                    ? str_replace(',', '.', $s)
                    : str_replace(',', '', $s);
            }
        } elseif ($hasDot) {
            if (substr_count($s, '.') > 1) {
                $s = str_replace('.', '', $s);
            } else {
                $parts = explode('.', $s);
                if (count($parts) === 2 && strlen($parts[1]) > 2) {
                    $s = str_replace('.', '', $s);
                }
            }
        }

        return is_numeric($s) ? (float) $s : 0;
    }

    protected function numToColLetter(int $num): string
    {
        $result = '';
        while ($num > 0) {
            $num--;
            $result = chr(65 + ($num % 26)) . $result;
            $num    = (int) ($num / 26);
        }
        return $result;
    }

    /**
     * Perkiraan lebar teks pakai metrik Arial (unit per-1000 em).
     */
    protected function approxTextWidth(string $text): int
    {
        // Metrik Arial (advance width per 1000 unit em)
        $m = [
            ' ' => 278, '.' => 278, ',' => 278, ':' => 278, '/' => 278, '!' => 278, 'i' => 222, 'j' => 222, 'l' => 222, 'I' => 278,
            'f' => 278, 't' => 278, 'r' => 333, 'A' => 667, 'B' => 667, 'C' => 722, 'D' => 722, 'E' => 667, 'F' => 611,
            'G' => 778, 'H' => 722, 'J' => 500, 'K' => 667, 'L' => 556, 'M' => 833, 'N' => 722, 'O' => 778, 'P' => 667,
            'Q' => 778, 'R' => 722, 'S' => 667, 'T' => 611, 'U' => 722, 'V' => 667, 'W' => 944, 'X' => 667, 'Y' => 667, 'Z' => 611,
            'a' => 556, 'b' => 556, 'c' => 500, 'd' => 556, 'e' => 556, 'g' => 556, 'h' => 556, 'k' => 500, 'm' => 833,
            'n' => 556, 'o' => 556, 'p' => 556, 'q' => 556, 's' => 500, 'u' => 556, 'v' => 500, 'w' => 722, 'x' => 500,
            'y' => 500, 'z' => 500,
        ];
        $w = 0;
        foreach (mb_str_split($text) as $ch) {
            if (isset($m[$ch])) $w += $m[$ch];
            elseif (ctype_upper($ch)) $w += 700;
            elseif (ctype_digit($ch)) $w += 556;
            else $w += 540;
        }
        return $w;
    }

    /**
     * Pad label dengan spasi agar titik dua mendekati sejajar dengan label terpanjang.
     */
    protected function padLabelProportional(string $label, int $targetWidth): string
    {
        $spaceW = 278; // lebar spasi Arial (unit per-1000 em)
        $cur = $this->approxTextWidth($label);
        $deficit = $targetWidth - $cur;
        if ($deficit <= 0) return $label;
        $spaces = (int) round($deficit / $spaceW);
        return $label . str_repeat(' ', max(0, $spaces));
    }
}