<?php

namespace App\Exports;

use App\Models\InventarisDeletedLog;
use Maatwebsite\Excel\Concerns\FromArray;
use Maatwebsite\Excel\Concerns\WithCustomValueBinder;
use Maatwebsite\Excel\Concerns\WithTitle;
use Maatwebsite\Excel\Concerns\WithEvents;
use Maatwebsite\Excel\Events\AfterSheet;
use PhpOffice\PhpSpreadsheet\Cell\StringValueBinder;
use PhpOffice\PhpSpreadsheet\Style\Alignment;
use PhpOffice\PhpSpreadsheet\Style\Border;
use PhpOffice\PhpSpreadsheet\Style\Fill;
use PhpOffice\PhpSpreadsheet\Style\NumberFormat;

class PenghapusanAsetExport extends StringValueBinder implements FromArray, WithTitle, WithEvents, WithCustomValueBinder
{
    protected $logs;
    protected $search;
    protected $isEmpty = false;

    public function __construct($logs = null, string $search = '')
    {
        $this->logs = $logs;
        $this->search = $search;
    }

    public function title(): string
    {
        return 'Penghapusan Aset';
    }

    public function array(): array
    {
        $desaConfig = config('desa', []);
        $desaName   = strtoupper($desaConfig['nama_lokasi'] ?? 'DEMO');
        $kodeDesa   = $desaConfig['kode_lokasi'] ?? '111.3.2.2009';
        $kabupaten  = strtoupper($desaConfig['kabupaten_nama'] ?? 'KABUPATEN DEMO');
        $kecamatan  = strtoupper($desaConfig['kecamatan_nama'] ?? 'KECAMATAN DEMO');
        $provinsi   = strtoupper($desaConfig['provinsi_nama'] ?? 'BALI');
        $tglCetak   = now()->translatedFormat('d F Y, H:i');

        $rows = [
            ['REKAPITULASI PENGHAPUSAN ASET DESA (MUTASI BERKURANG)'], // Row 1: Judul Utama
            ["PEMERINTAH {$kabupaten} - DESA {$desaName}"],             // Row 2: Subjudul
            [''],                                                       // Row 3: Spacer
            ['Provinsi',        ': ' . $provinsi, '', '', '', '', '', '', 'Waktu Cetak', ': ' . $tglCetak, ''], // Row 4
            ['Kab./Kota',       ': ' . $kabupaten],                                                              // Row 5
            ['Kecamatan',       ': ' . $kecamatan],                                                              // Row 6
            ['Desa',            ': ' . $desaName],                                                               // Row 7
            ['No. Kode Lokasi', ': ' . $kodeDesa],                                                               // Row 8
            [''],                                                                                                // Row 9: Spacer
            [                                                                                                    // Row 10: Table Header
                'NO',
                'TANGGAL DIHAPUS',
                'KODE BARANG',
                'NUP',
                'NAMA BARANG',
                'MERK / TIPE',
                'JENIS INVENTARIS',
                'TAHUN PEROLEHAN',
                'NILAI PEROLEHAN (RP)',
                'ALASAN PENGHAPUSAN',
                'DIHAPUS OLEH',
            ],
        ];

        $items = $this->logs ?? InventarisDeletedLog::with('user:id,nama')->orderByDesc('deleted_at')->get();
        $this->isEmpty = $items->isEmpty();
        $totalNilai = 0;

        if ($items->isEmpty()) {
            $rows[] = [
                'Tidak ada data penghapusan aset yang tersedia.',
                '',
                '',
                '',
                '',
                '',
                '',
                '',
                0,
                '',
                '',
            ];
        } else {
            foreach ($items as $idx => $item) {
                $nilai = (float)($item->nilai_perolehan ?? 0);
                $totalNilai += $nilai;

                $rows[] = array_map([$this, 'sanitizeFormulaInjection'], [
                    $idx + 1,
                    $item->deleted_at ? $item->deleted_at->translatedFormat('d/m/Y H:i') : '-',
                    $item->kode_barang ?? '-',
                    $item->nup ?? 1,
                    $item->nama_barang ?? '-',
                    $item->merk_type ?? ($item->merk ?? '-'),
                    $item->jenis_nama ?? '-',
                    $item->tahun_perolehan ?? '-',
                    $nilai > 0 ? number_format($nilai, fmod($nilai, 1) != 0 ? 2 : 0, ',', '.') : '-',
                    $item->alasan_penghapusan ?? '-',
                    $item->user?->nama ?? 'Sistem',
                ]);
            }
        }

        // Baris Total
        $rows[] = [
            'TOTAL',
            '',
            '',
            '',
            '',
            '',
            '',
            '',
            number_format($totalNilai, fmod($totalNilai, 1) != 0 ? 2 : 0, ',', '.'),
            '',
            '',
        ];

        return $rows;
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

    public function registerEvents(): array
    {
        return [
            AfterSheet::class => function(AfterSheet $event) {
                $sheet = $event->sheet->getDelegate();
                $highestRow = $sheet->getHighestRow();

                // Merge Title Rows (Rows 1 & 2)
                $sheet->mergeCells('A1:K1');
                $sheet->mergeCells('A2:K2');

                $sheet->getStyle('A1')->getFont()->setName('Arial')->setSize(13)->setBold(true);
                $sheet->getStyle('A2')->getFont()->setName('Arial')->setSize(11)->setBold(true);
                $sheet->getStyle('A1:A2')->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER)->setVertical(Alignment::VERTICAL_CENTER);
                $sheet->getRowDimension(1)->setRowHeight(24);
                $sheet->getRowDimension(2)->setRowHeight(20);
                $sheet->getRowDimension(3)->setRowHeight(8);

                // Info Desa Block (Rows 4 - 8)
                $sheet->getStyle('A4:B8')->getFont()->setName('Arial')->setSize(9);
                $sheet->getStyle('A4:A8')->getFont()->setBold(false);
                $sheet->getStyle('B4:B8')->getFont()->setBold(true);
                $sheet->getStyle('A4:B8')->getAlignment()->setHorizontal(Alignment::HORIZONTAL_LEFT);

                $sheet->getStyle('I4:J4')->getFont()->setName('Arial')->setSize(9);
                $sheet->getStyle('I4')->getFont()->setBold(false);
                $sheet->getStyle('J4')->getFont()->setBold(true);
                $sheet->getStyle('I4:J4')->getAlignment()->setHorizontal(Alignment::HORIZONTAL_LEFT);

                $sheet->getRowDimension(9)->setRowHeight(8);

                // Apply ALL BORDERS to the entire table from Header (Row 10) down to the last row
                $tableRange = "A10:K{$highestRow}";
                $sheet->getStyle($tableRange)->applyFromArray([
                    'borders' => [
                        'allBorders' => [
                            'borderStyle' => Border::BORDER_THIN,
                            'color' => ['argb' => 'FF000000'],
                        ],
                    ],
                ]);

                // Header Table (Row 10)
                $headerRange = 'A10:K10';
                $sheet->getStyle($headerRange)->applyFromArray([
                    'font' => [
                        'name' => 'Arial',
                        'bold' => true,
                        'size' => 9,
                        'color' => ['argb' => 'FF000000'],
                    ],
                    'alignment' => [
                        'horizontal' => Alignment::HORIZONTAL_CENTER,
                        'vertical'   => Alignment::VERTICAL_CENTER,
                        'wrapText'   => true,
                    ],
                    'fill' => [
                        'fillType' => Fill::FILL_SOLID,
                        'startColor' => ['rgb' => 'FFFFFF'],
                    ],
                ]);
                $sheet->getRowDimension(10)->setRowHeight(26);

                // Data Rows (Row 11 to $highestRow - 1)
                if ($highestRow >= 11) {
                    $dataRange = "A11:K" . ($highestRow - 1);
                    $sheet->getStyle($dataRange)->applyFromArray([
                        'font' => ['name' => 'Arial', 'size' => 9, 'color' => ['argb' => 'FF000000']],
                        'alignment' => ['vertical' => Alignment::VERTICAL_CENTER],
                    ]);

                    // Dynamic row height for all data rows to prevent any text clipping
                    for ($r = 11; $r <= $highestRow; $r++) {
                        $sheet->getRowDimension($r)->setRowHeight(-1);
                    }

                    if ($this->isEmpty) {
                        $sheet->mergeCells("A11:H11");
                        $sheet->getStyle('A11')->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
                        $sheet->getStyle('A11')->getFont()->setItalic(true);
                    } else {
                        // Format Alignments
                        $sheet->getStyle("A11:A" . ($highestRow - 1))->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
                        $sheet->getStyle("B11:B" . ($highestRow - 1))->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
                        $sheet->getStyle("C11:D" . ($highestRow - 1))->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
                        $sheet->getStyle("H11:H" . ($highestRow - 1))->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
                        $sheet->getStyle("I11:I" . ($highestRow - 1))->getAlignment()->setHorizontal(Alignment::HORIZONTAL_RIGHT);
                    }
                    $sheet->getStyle("I11:I{$highestRow}")->getNumberFormat()->setFormatCode(NumberFormat::FORMAT_TEXT);

                    // Style Total Row (last row)
                    $totalRange = "A{$highestRow}:K{$highestRow}";
                    $sheet->mergeCells("A{$highestRow}:H{$highestRow}");
                    $sheet->getStyle($totalRange)->applyFromArray([
                        'font' => ['bold' => true, 'color' => ['argb' => 'FF000000']],
                        'borders' => [
                            'top' => ['borderStyle' => Border::BORDER_THIN, 'color' => ['argb' => 'FF000000']],
                            'bottom' => ['borderStyle' => Border::BORDER_DOUBLE, 'color' => ['argb' => 'FF000000']],
                        ],
                    ]);
                    $sheet->getStyle("A{$highestRow}")->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
                    $sheet->getStyle("I{$highestRow}")->getAlignment()->setHorizontal(Alignment::HORIZONTAL_RIGHT);
                }

                // Auto-fit columns
                foreach (range('A', 'K') as $col) {
                    $sheet->getColumnDimension($col)->setAutoSize(true);
                }
            },
        ];
    }
}
