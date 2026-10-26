<?php

namespace App\Exports;

use App\Models\Bidang;
use Maatwebsite\Excel\Concerns\FromArray;
use Maatwebsite\Excel\Concerns\WithTitle;
use Maatwebsite\Excel\Concerns\WithEvents;
use Maatwebsite\Excel\Events\AfterSheet;
use PhpOffice\PhpSpreadsheet\Style\Alignment;
use PhpOffice\PhpSpreadsheet\Style\Border;
use PhpOffice\PhpSpreadsheet\Style\Fill;

class BidangExport implements FromArray, WithTitle, WithEvents
{
    protected $bidangs;
    protected $isEmpty = false;

    public function __construct($bidangs = null)
    {
        $this->bidangs = $bidangs;
    }

    public function title(): string
    {
        return 'Data Bidang';
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
            ['REKAPITULASI MASTER BIDANG PEMERINTAHAN DESA'],           // Row 1: Judul Utama
            ["PEMERINTAH {$kabupaten} - DESA {$desaName}"],             // Row 2: Subjudul
            [''],                                                       // Row 3: Spacer
            ['Provinsi',        ': ' . $provinsi, 'Waktu Cetak', ': ' . $tglCetak], // Row 4
            ['Kab./Kota',       ': ' . $kabupaten],                                 // Row 5
            ['Kecamatan',       ': ' . $kecamatan],                                 // Row 6
            ['Desa',            ': ' . $desaName],                                  // Row 7
            ['No. Kode Lokasi', ': ' . $kodeDesa],                                  // Row 8
            [''],                                                                   // Row 9: Spacer
            [                                                                       // Row 10: Table Header
                'NO',
                'NAMA BIDANG',
                'DESKRIPSI',
                'JUMLAH PENGGUNA / STAF',
            ],
        ];

        $items = $this->bidangs ?? Bidang::withCount('users')->orderBy('id', 'asc')->get();
        $this->isEmpty = $items->isEmpty();

        if ($items->isEmpty()) {
            $rows[] = [
                'Tidak ada data bidang yang tersedia.',
                '',
                '',
                '',
            ];
        } else {
            foreach ($items as $idx => $item) {
                $rows[] = [
                    $idx + 1,
                    $item->nama ?? '-',
                    $item->deskripsi ?: '-',
                    $item->users_count ?? 0,
                ];
            }
        }

        return $rows;
    }

    public function registerEvents(): array
    {
        return [
            AfterSheet::class => function(AfterSheet $event) {
                $sheet = $event->sheet->getDelegate();
                $highestRow = $sheet->getHighestRow();

                // Merge Title Rows (Rows 1 & 2)
                $sheet->mergeCells('A1:D1');
                $sheet->mergeCells('A2:D2');

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

                $sheet->getStyle('C4:D4')->getFont()->setName('Arial')->setSize(9);
                $sheet->getStyle('C4')->getFont()->setBold(false);
                $sheet->getStyle('D4')->getFont()->setBold(true);
                $sheet->getStyle('C4:D4')->getAlignment()->setHorizontal(Alignment::HORIZONTAL_LEFT);

                $sheet->getRowDimension(9)->setRowHeight(8);

                // Apply ALL BORDERS to the entire table from Header (Row 10) down to the last row
                $tableRange = "A10:D{$highestRow}";
                $sheet->getStyle($tableRange)->applyFromArray([
                    'borders' => [
                        'allBorders' => [
                            'borderStyle' => Border::BORDER_THIN,
                            'color' => ['argb' => 'FF000000'],
                        ],
                    ],
                ]);

                // Header Table (Row 10)
                $headerRange = 'A10:D10';
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
                    $dataRange = "A11:D{$highestRow}";
                    $sheet->getStyle($dataRange)->applyFromArray([
                        'font' => ['name' => 'Arial', 'size' => 9, 'color' => ['argb' => 'FF000000']],
                        'alignment' => ['vertical' => Alignment::VERTICAL_CENTER],
                    ]);

                    // Dynamic row height for all data rows to prevent any text clipping
                    for ($r = 11; $r <= $highestRow; $r++) {
                        $sheet->getRowDimension($r)->setRowHeight(-1);
                    }

                    if ($this->isEmpty) {
                        $sheet->mergeCells("A11:D11");
                        $sheet->getStyle('A11')->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
                        $sheet->getStyle('A11')->getFont()->setItalic(true);
                    } else {
                        $sheet->getStyle("A11:A{$highestRow}")->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
                        $sheet->getStyle("D11:D{$highestRow}")->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
                    }
                }

                foreach (range('A', 'D') as $col) {
                    $sheet->getColumnDimension($col)->setAutoSize(true);
                }
            },
        ];
    }
}
