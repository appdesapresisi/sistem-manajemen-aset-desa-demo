<?php

namespace App\Exports;

use App\Models\ActivityLog;
use Maatwebsite\Excel\Concerns\FromArray;
use Maatwebsite\Excel\Concerns\WithTitle;
use Maatwebsite\Excel\Concerns\WithEvents;
use Maatwebsite\Excel\Events\AfterSheet;
use PhpOffice\PhpSpreadsheet\Style\Alignment;
use PhpOffice\PhpSpreadsheet\Style\Border;
use PhpOffice\PhpSpreadsheet\Style\Fill;

class ActivityLogExport implements FromArray, WithTitle, WithEvents
{
    protected $logs;
    protected $isEmpty = false;

    public function __construct($logs = null)
    {
        $this->logs = $logs;
    }

    public function title(): string
    {
        return 'Log Aktivitas';
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
            ['REKAPITULASI LOG AUDIT AKTIVITAS SISTEM'],                // Row 1: Judul Utama
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
                'TANGGAL & WAKTU',
                'PENGGUNA',
                'PERAN / JABATAN',
                'MODUL / FITUR',
                'AKTIVITAS',
                'RINCIAN AKTIVITAS',
            ],
        ];

        $items = $this->logs ?? ActivityLog::excludeSuperDevelopers()->with(['user.role'])->orderByDesc('created_at')->get();
        $this->isEmpty = $items->isEmpty();

        if ($items->isEmpty()) {
            $rows[] = [
                'Tidak ada data log aktivitas yang tersedia.',
                '',
                '',
                '',
                '',
                '',
                '',
            ];
        } else {
            foreach ($items as $idx => $item) {
                $userNama = $item->user?->nama ?? 'Sistem';
                $userRole = $item->user?->role_name_display ?: ($item->user ? ($item->user->role?->nama ? ucwords(str_replace('_', ' ', $item->user->role->nama)) : '-') : 'Sistem');
                $module   = $item->module_label ?? ucfirst((string)$item->module);
                $action   = $item->action_label ?? (ucfirst((string)$item->action) . ' Data');

                $rows[] = [
                    $idx + 1,
                    $item->created_at ? $item->created_at->translatedFormat('d/m/Y H:i:s') : '-',
                    $userNama,
                    $userRole,
                    $module,
                    $action,
                    $item->detail ?? '-',
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
                        $sheet->getStyle('A11')->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
                        $sheet->getStyle('A11')->getFont()->setItalic(true);
                    } else {
                        $sheet->getStyle("A11:A{$highestRow}")->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
                        $sheet->getStyle("B11:B{$highestRow}")->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
                        $sheet->getStyle("E11:F{$highestRow}")->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
                    }
                }

                foreach (range('A', 'G') as $col) {
                    $sheet->getColumnDimension($col)->setAutoSize(true);
                }
            },
        ];
    }
}
