<?php

namespace App\Exports;

use App\Models\JenisInventaris;
use App\Models\Ruangan;
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

class RuanganExport extends StringValueBinder implements FromArray, WithTitle, WithEvents, WithCustomValueBinder
{
    protected $ruangans;
    protected $isEmpty = false;

    public function __construct($ruangans = null)
    {
        $this->ruangans = $ruangans;
    }

    public function title(): string
    {
        return 'Data Ruangan';
    }

    public function array(): array
    {
        $desaConfig = config('desa', []);
        $desaName   = strtoupper($desaConfig['nama_lokasi'] ?? 'DEMO');
        $kodeDesa   = $desaConfig['kode_lokasi'] ?? '111.03.02.2009';
        $kabupaten  = strtoupper($desaConfig['kabupaten_nama'] ?? 'KABUPATEN DEMO');
        $kecamatan  = strtoupper($desaConfig['kecamatan_nama'] ?? 'KECAMATAN DEMO');
        $provinsi   = strtoupper($desaConfig['provinsi_nama'] ?? 'BALI');
        $tglCetak   = now()->translatedFormat('d F Y, H:i');

        $rows = [
            ['REKAPITULASI MASTER RUANGAN & PENEMPATAN ASET DESA'],      // Row 1: Judul Utama
            ["PEMERINTAH {$kabupaten} - DESA {$desaName}"],             // Row 2: Subjudul
            [''],                                                       // Row 3: Spacer
            ['Provinsi',        ': ' . $provinsi, '', '', '', 'Waktu Cetak', ': ' . $tglCetak], // Row 4
            ['Kab./Kota',       ': ' . $kabupaten],                                            // Row 5
            ['Kecamatan',       ': ' . $kecamatan],                                            // Row 6
            ['Desa',            ': ' . $desaName],                                             // Row 7
            ['No. Kode Lokasi', ': ' . $kodeDesa],                                             // Row 8
            [''],                                                                              // Row 9: Spacer
            [                                                                                  // Row 10: Table Header
                'NO',
                'KODE RUANGAN',
                'NAMA RUANGAN',
                'PENANGGUNG JAWAB',
                'JUMLAH ASET',
                'TOTAL NILAI ASET (RP)',
                'KETERANGAN',
            ],
        ];

        $inventarisFilter = function ($q) {
            $q->whereHas('jenis', fn($j) => $j->where('nama', '!=', JenisInventaris::NAMA_TIDAK_DITEMUKAN));
        };

        $items = $this->ruangans ?? Ruangan::withCount(['inventaris' => $inventarisFilter])
            ->with('penanggungJawab')
            ->withSum(['inventaris' => $inventarisFilter], 'nilai_perolehan')
            ->orderBy('id', 'asc')
            ->get();

        $this->isEmpty = $items->isEmpty();

        if ($items->isEmpty()) {
            $rows[] = [
                'Tidak ada data ruangan yang tersedia.',
                '',
                '',
                '',
                '',
                '',
                '',
            ];
        } else {
            $totalAsetCount = 0;
            $totalNilaiSum = 0;

            foreach ($items as $idx => $item) {
                $pjName = $item->penanggungJawab?->nama ?? '-';
                $asetCount = (int) ($item->inventaris_count ?? 0);
                $nilaiSum = (float) ($item->inventaris_sum_nilai_perolehan ?? 0);

                $totalAsetCount += $asetCount;
                $totalNilaiSum += $nilaiSum;

                $rows[] = [
                    $idx + 1,
                    $item->kode ?: '-',
                    $item->nama ?? '-',
                    $pjName,
                    $asetCount,
                    $nilaiSum > 0 ? number_format($nilaiSum, fmod($nilaiSum, 1) != 0 ? 2 : 0, ',', '.') : '-',
                    $item->deskripsi ?: '-',
                ];
            }

            // Total row
            $rows[] = [
                'TOTAL KESELURUHAN',
                '',
                '',
                '',
                $totalAsetCount,
                number_format($totalNilaiSum, fmod($totalNilaiSum, 1) != 0 ? 2 : 0, ',', '.'),
                '',
            ];
        }

        return $rows;
    }

    public function registerEvents(): array
    {
        return [
            AfterSheet::class => function(AfterSheet $event) {
                $sheet = $event->sheet->getDelegate();
                $highestRow = $sheet->getHighestRow();

                // Merge Title Rows (Rows 1 & 2 across A to G)
                $sheet->mergeCells('A1:G1');
                $sheet->mergeCells('A2:G2');

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

                $sheet->getStyle('F4:G4')->getFont()->setName('Arial')->setSize(9);
                $sheet->getStyle('F4')->getFont()->setBold(false);
                $sheet->getStyle('G4')->getFont()->setBold(true);
                $sheet->getStyle('F4:G4')->getAlignment()->setHorizontal(Alignment::HORIZONTAL_LEFT);

                $sheet->getRowDimension(9)->setRowHeight(8);

                // Apply ALL BORDERS to the entire table from Header (Row 10) down to the last row
                $tableRange = "A10:G{$highestRow}";
                $sheet->getStyle($tableRange)->applyFromArray([
                    'borders' => [
                        'allBorders' => [
                            'borderStyle' => Border::BORDER_THIN,
                            'color' => ['argb' => 'FF000000'],
                        ],
                    ],
                ]);

                // Header Table (Row 10)
                $headerRange = 'A10:G10';
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

                // Data Rows (Row 11 to highestRow)
                if ($highestRow >= 11) {
                    $dataRange = "A11:G{$highestRow}";
                    $sheet->getStyle($dataRange)->applyFromArray([
                        'font' => ['name' => 'Arial', 'size' => 9, 'color' => ['argb' => 'FF000000']],
                        'alignment' => ['vertical' => Alignment::VERTICAL_CENTER],
                    ]);

                    // Dynamic row height for all data rows to prevent any text clipping
                    for ($r = 11; $r <= $highestRow; $r++) {
                        $sheet->getRowDimension($r)->setRowHeight(-1);
                    }

                    if ($this->isEmpty) {
                        $sheet->mergeCells("A11:G11");
                        $sheet->getStyle("A11")->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
                    } else {
                        // Data alignment
                        $sheet->getStyle("A11:A" . ($highestRow - 1))->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
                        $sheet->getStyle("B11:B" . ($highestRow - 1))->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
                        $sheet->getStyle("C11:D" . ($highestRow - 1))->getAlignment()->setHorizontal(Alignment::HORIZONTAL_LEFT);
                        $sheet->getStyle("E11:E" . ($highestRow - 1))->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
                        $sheet->getStyle("F11:F" . ($highestRow - 1))->getAlignment()->setHorizontal(Alignment::HORIZONTAL_RIGHT);
                        $sheet->getStyle("G11:G" . ($highestRow - 1))->getAlignment()->setHorizontal(Alignment::HORIZONTAL_LEFT);

                        // Column F: NumberFormat TEXT to preserve dots separator
                        $sheet->getStyle("F11:F{$highestRow}")->getNumberFormat()->setFormatCode(NumberFormat::FORMAT_TEXT);

                        // Total Row (highestRow)
                        $sheet->mergeCells("A{$highestRow}:D{$highestRow}");
                        $sheet->getStyle("A{$highestRow}:G{$highestRow}")->getFont()->setBold(true);
                        $sheet->getStyle("A{$highestRow}")->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
                        $sheet->getStyle("E{$highestRow}")->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
                        $sheet->getStyle("F{$highestRow}")->getAlignment()->setHorizontal(Alignment::HORIZONTAL_RIGHT);
                    }
                }

                // Dynamic column auto-size following the content width
                foreach (range('A', 'G') as $col) {
                    $sheet->getColumnDimension($col)->setAutoSize(true);
                }
            },
        ];
    }
}
