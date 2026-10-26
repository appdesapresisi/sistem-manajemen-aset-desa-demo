<?php

namespace App\Exports;

use App\Models\Inventaris;
use Illuminate\Support\Collection;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithTitle;
use Maatwebsite\Excel\Concerns\WithEvents;
use Maatwebsite\Excel\Concerns\WithColumnWidths;
use Maatwebsite\Excel\Events\AfterSheet;
use PhpOffice\PhpSpreadsheet\Style\Alignment;
use PhpOffice\PhpSpreadsheet\Style\Border;
use PhpOffice\PhpSpreadsheet\Style\Fill;
use PhpOffice\PhpSpreadsheet\Style\NumberFormat;
use PhpOffice\PhpSpreadsheet\Worksheet\Drawing;
use Maatwebsite\Excel\Concerns\WithCustomValueBinder;
use PhpOffice\PhpSpreadsheet\Cell\StringValueBinder;

class BukuInventarisSheet extends StringValueBinder implements
    FromCollection,
    WithTitle,
    WithColumnWidths,
    WithEvents,
    WithCustomValueBinder
{
    protected array $filters;
    protected array $pejabatSnapshot;
    protected string $tanggalFormatted = '';
    protected bool $includeSignatureImage = true;
    protected int $rowCounter = 0;
    protected float $nilaiTotal = 0.0;
    protected string $docLampiran = '';
    protected string $docBaNomor = '';
    protected string $docBaTanggal = '';

    // Total rows of data (set after collection is built)
    protected Collection $rows;

    public function __construct(array $filters = [], array $pejabatSnapshot = [], string $tanggalDokumen = '', string $keteranganDokumen = '', bool $includeSignatureImage = true, string $docLampiran = '', string $docBaNomor = '', string $docBaTanggal = '')
    {
        $this->filters               = $filters;
        $activePejabats = [];
        if (is_array($pejabatSnapshot)) {
            foreach ($pejabatSnapshot as $p) {
                if (!$p) continue;
                $nama = trim((string)($p['nama'] ?? ($p['nama_pejabat'] ?? '')));
                if ($nama !== '') {
                    $activePejabats[] = $p;
                }
            }
        }
        $this->pejabatSnapshot       = $activePejabats;
        $this->includeSignatureImage = $includeSignatureImage;
        $this->docLampiran           = $docLampiran;
        $this->docBaNomor            = $docBaNomor;
        $this->docBaTanggal          = $docBaTanggal;
        $this->tanggalFormatted      = $this->formatTanggal(
            $tanggalDokumen,
            $keteranganDokumen ?: config('desa.nama_lokasi', '')
        );
    }

    /**
     * Format tanggal ke bahasa Indonesia: "Demo, 31 Desember 2026"
     */
    private function formatTanggal(string $tanggal, string $keterangan): string
    {
        if (empty($tanggal)) {
            return '';
        }
        try {
            $dt = \Carbon\Carbon::parse($tanggal);
            $bulan = [
                1 => 'Januari', 2 => 'Februari', 3 => 'Maret',    4 => 'April',
                5 => 'Mei',     6 => 'Juni',      7 => 'Juli',     8 => 'Agustus',
                9 => 'September', 10 => 'Oktober', 11 => 'November', 12 => 'Desember',
            ];
            $tanggalStr = $dt->day . ' ' . $bulan[$dt->month] . ' ' . $dt->year;
            return $keterangan !== '' ? $keterangan . ', ' . $tanggalStr : $tanggalStr;
        } catch (\Throwable $e) {
            return $keterangan !== '' ? $keterangan . ', -' : '-';
        }
    }

    public function title(): string
    {
        return 'Buku Inventaris Aset Desa';
    }

    public function collection(): Collection
    {
        $query = Inventaris::with('jenis')
            ->orderBy('jenis_inventaris_id')
            ->orderBy('kode_barang')
            ->orderBy('tahun_perolehan')
            ->orderBy('nama_barang')
            ->orderByRaw('CAST(nup AS UNSIGNED) ASC')
            ->orderBy('id');

        if (!empty($this->filters['tahun'])) {
            $query->where('tahun_perolehan', '<=', $this->filters['tahun']);
        }

        $data = $query->get();

        $rows = collect();
        foreach ($data as $inv) {
            $nilai = (float) ($inv->nilai_perolehan ?? 0);
            $this->nilaiTotal += $nilai;

            $rows->push([
                ++$this->rowCounter,
                $inv->jenis->nama ?? '-',
                $inv->kode_barang ?? '-',
                $inv->nama_barang ?? '-',
                ($inv->asal_usul === 'apbdes')         ? '✓' : '',
                ($inv->asal_usul === 'perolehan_lain') ? '✓' : '',
                ($inv->asal_usul === 'kekayaan_asli')  ? '✓' : '',
                $inv->tahun_perolehan ?? '-',
                $nilai > 0 ? number_format($nilai, fmod($nilai, 1) != 0 ? 2 : 0, ',', '.') : '',
                $inv->pengguna ?? '-',
                $inv->keterangan ?? '',
            ]);
        }

        $this->rows = $rows;
        return $rows;
    }

    public function columnWidths(): array
    {
        return [
            'A' => 5,    // No
            'B' => 24,   // Jenis Barang
            'C' => 16,   // Kode Barang
            'D' => 28,   // Nama/Identitas Barang
            'E' => 12,   // APBDes
            'F' => 22,   // Perolehan Lain yang Sah
            'G' => 20,   // Kekayaan Asli Desa
            'H' => 12,   // Tahun Perolehan
            'I' => 18,   // Nilai Aset
            'J' => 22,   // Pengguna
            'K' => 36,   // Keterangan
        ];
    }

    public function registerEvents(): array
    {
        return [
            AfterSheet::class => function (AfterSheet $event) {
                $sheet    = $event->sheet->getDelegate();
                $lastCol  = 'K';
                $totalCols = 11;

                // ── 1. Insert title rows at top (shift everything down 8 rows) ────────
                $sheet->insertNewRowBefore(1, 8);

                // Row 1: Main title
                $sheet->setCellValue('A1', 'BUKU INVENTARIS ASET DESA');
                $sheet->mergeCells("A1:{$lastCol}1");
                $sheet->getStyle('A1')->applyFromArray([
                    'font'      => ['bold' => true, 'size' => 14],
                    'alignment' => ['horizontal' => Alignment::HORIZONTAL_CENTER, 'vertical' => Alignment::VERTICAL_CENTER],
                ]);
                $sheet->getRowDimension(1)->setRowHeight(28);

                // Row 2: PEMERINTAH DESA [NAMA]
                $namaDesa = config('desa.nama_lokasi', '');
                if ($namaDesa !== '') {
                    $sheet->setCellValue('A2', 'PEMERINTAH DESA ' . strtoupper($namaDesa));
                    $sheet->mergeCells("A2:{$lastCol}2");
                    $sheet->getStyle('A2')->applyFromArray([
                        'font'      => ['bold' => true, 'size' => 14],
                        'alignment' => ['horizontal' => Alignment::HORIZONTAL_CENTER, 'vertical' => Alignment::VERTICAL_CENTER],
                    ]);
                }
                $sheet->getRowDimension(2)->setRowHeight(28);

                // Row 3: TAHUN [TAHUN]
                $tahun = $this->filters['tahun'] ?? date('Y');
                $sheet->setCellValue('A3', 'TAHUN ' . $tahun);
                $sheet->mergeCells("A3:{$lastCol}3");
                $sheet->getStyle('A3')->applyFromArray([
                    'font'      => ['bold' => true, 'size' => 14],
                    'alignment' => ['horizontal' => Alignment::HORIZONTAL_CENTER, 'vertical' => Alignment::VERTICAL_CENTER],
                ]);
                $sheet->getRowDimension(3)->setRowHeight(28);

                // Row 4: empty spacer
                $sheet->getRowDimension(4)->setRowHeight(6);

                // Row 5: Kode Lokasi Desa
                $kodeLokasi = config('desa.kode_lokasi', '');
                if ($kodeLokasi !== '') {
                    $sheet->setCellValue('A5', 'KODE LOKASI DESA : ' . $kodeLokasi);
                    $sheet->mergeCells("A5:{$lastCol}5");
                    $sheet->getStyle('A5')->applyFromArray([
                        'font'      => ['bold' => true, 'size' => 11],
                        'alignment' => ['horizontal' => Alignment::HORIZONTAL_LEFT, 'vertical' => Alignment::VERTICAL_CENTER],
                    ]);
                }
                $sheet->getRowDimension(5)->setRowHeight(20);

                // Row 6: empty spacer
                $sheet->getRowDimension(6)->setRowHeight(6);

                // Row 7: column headers (top row — span for Asal Usul)
                $sheet->setCellValue('A7', 'No');
                $sheet->setCellValue('B7', 'Jenis Barang');
                $sheet->setCellValue('C7', 'Kode Barang');
                $sheet->setCellValue('D7', 'Nama / Identitas Barang');
                $sheet->setCellValue('E7', 'Asal Usul Barang');
                $sheet->mergeCells('E7:G7');
                $sheet->setCellValue('H7', 'Tahun Perolehan');
                $sheet->setCellValue('I7', 'Nilai Aset (Rp)');
                $sheet->setCellValue('J7', 'Pengguna');
                $sheet->setCellValue('K7', 'Keterangan');

                // Merge single-column headers vertically (rows 7-8)
                foreach (['A', 'B', 'C', 'D', 'H', 'I', 'J', 'K'] as $col) {
                    $sheet->mergeCells("{$col}7:{$col}8");
                }

                // Row 8: sub-headers for Asal Usul
                $sheet->setCellValue('E8', 'APBDes');
                $sheet->setCellValue('F8', 'Perolehan Lain yang Sah');
                $sheet->setCellValue('G8', 'Kekayaan Asli Desa');

                // Style rows 7 & 8 (header)
                $sheet->getStyle("A7:{$lastCol}8")->applyFromArray([
                    'font'      => ['bold' => true, 'size' => 10],
                    'alignment' => ['horizontal' => Alignment::HORIZONTAL_CENTER, 'vertical' => Alignment::VERTICAL_CENTER, 'wrapText' => true],
                    'fill'      => ['fillType' => Fill::FILL_SOLID, 'startColor' => ['rgb' => 'FFFFFF']],
                    'borders'   => ['allBorders' => ['borderStyle' => Border::BORDER_THIN, 'color' => ['rgb' => '000000']]],
                ]);
                $sheet->getRowDimension(7)->setRowHeight(32);
                $sheet->getRowDimension(8)->setRowHeight(28);

                // ── 2. Style data rows ────────────────────────────────────────────────
                $dataStart = 9;
                $lastRow   = $sheet->getHighestRow();

                if ($lastRow >= $dataStart) {
                    $sheet->getStyle("A{$dataStart}:{$lastCol}{$lastRow}")->applyFromArray([
                        'borders' => ['allBorders' => ['borderStyle' => Border::BORDER_THIN, 'color' => ['rgb' => '000000']]],
                        'font'    => ['size' => 10],
                    ]);
                    $sheet->getStyle("A{$dataStart}:{$lastCol}{$lastRow}")
                        ->getAlignment()->setHorizontal(Alignment::HORIZONTAL_LEFT);

                    for ($r = $dataStart; $r <= $lastRow; $r++) {
                        $sheet->getStyle("A{$r}:{$lastCol}{$r}")->getFill()
                            ->setFillType(Fill::FILL_SOLID)->getStartColor()->setRGB('FFFFFF');
                        $sheet->getStyle("A{$r}:{$lastCol}{$r}")->getAlignment()
                            ->setVertical(Alignment::VERTICAL_TOP);
                        $sheet->getRowDimension($r)->setRowHeight(-1); // auto height for wrapped text
                    }

                    // Wrap text for Keterangan column
                    $sheet->getStyle("K{$dataStart}:K{$lastRow}")->getAlignment()
                        ->setWrapText(true)
                        ->setVertical(Alignment::VERTICAL_TOP);

                    // Center: No, asal usul checkmarks, tahun
                    $sheet->getStyle("A{$dataStart}:A{$lastRow}")->getAlignment()
                        ->setHorizontal(Alignment::HORIZONTAL_CENTER);
                    $sheet->getStyle("E{$dataStart}:G{$lastRow}")->getAlignment()
                        ->setHorizontal(Alignment::HORIZONTAL_CENTER);
                    $sheet->getStyle("H{$dataStart}:H{$lastRow}")->getAlignment()
                        ->setHorizontal(Alignment::HORIZONTAL_CENTER);

                    // Format kolom nilai
                    $sheet->getStyle("I{$dataStart}:I{$lastRow}")
                        ->getNumberFormat()->setFormatCode(NumberFormat::FORMAT_TEXT);
                    $sheet->getStyle("I{$dataStart}:I{$lastRow}")
                        ->getAlignment()->setHorizontal(Alignment::HORIZONTAL_RIGHT);
                }

                // ── 3. Jumlah row ─────────────────────────────────────────────────────
                $jumlahRow = ($lastRow >= $dataStart) ? $lastRow + 1 : $dataStart + 1;
                $sheet->mergeCells("A{$jumlahRow}:H{$jumlahRow}");
                $sheet->setCellValue("A{$jumlahRow}", 'Jumlah ' . str_repeat('-', 60));
                // Tampilkan jumlah dengan format number
                $sheet->setCellValue("I{$jumlahRow}", number_format($this->nilaiTotal, fmod($this->nilaiTotal, 1) != 0 ? 2 : 0, ',', '.'));
                $sheet->getStyle("I{$jumlahRow}")->getAlignment()->setHorizontal(Alignment::HORIZONTAL_RIGHT);
                $sheet->getStyle("A{$jumlahRow}:{$lastCol}{$jumlahRow}")->applyFromArray([
                    'borders' => ['allBorders' => ['borderStyle' => Border::BORDER_THIN, 'color' => ['rgb' => '000000']]],
                    'font'    => ['bold' => true, 'size' => 10],
                    'alignment' => ['vertical' => Alignment::VERTICAL_CENTER],
                ]);
                $sheet->getRowDimension($jumlahRow)->setRowHeight(22);

                // ── 4. Tim Inventarisasi — layout adaptif sesuai jumlah pejabat ──
                if (!empty($this->pejabatSnapshot)) {
                    $rowCursor = $jumlahRow + 2;

                    // Tanggal dokumen — hanya sekali di kanan atas TTD section (kolom I:K)
                    if (!empty($this->tanggalFormatted)) {
                        $sheet->mergeCells("I{$rowCursor}:K{$rowCursor}");
                        $sheet->setCellValue("I{$rowCursor}", $this->tanggalFormatted);
                        $sheet->getStyle("I{$rowCursor}")->applyFromArray([
                            'font'      => ['size' => 10],
                            'alignment' => ['horizontal' => Alignment::HORIZONTAL_RIGHT, 'vertical' => Alignment::VERTICAL_CENTER],
                        ]);
                        $sheet->getRowDimension($rowCursor)->setRowHeight(16);
                        $rowCursor++;
                    }

                    // Gap sebelum blok TTD tetap seperti semula
                    $sheet->getRowDimension($rowCursor)->setRowHeight(8);
                    $rowCursor++;

                    // Layout columns
                    $ranges3 = [['A', 'C'], ['E', 'G'], ['I', 'K']]; // 3 kolom
                    $ranges2 = [['A', 'E'], ['G', 'K']];             // 2 kolom
                    $rangeCenter = [['C', 'I']];                     // 1 kolom (center)

                    $signers = array_values($this->pejabatSnapshot);
                    $signerCount = count($signers);

                    $renderGroup = function(array $group, array $ranges) use (&$rowCursor, $sheet) {
                        // Pad group agar sama dengan jumlah kolom
                        $group = array_pad($group, count($ranges), null);

                        // Baris jabatan
                        foreach ($ranges as $i => $range) {
                            [$cs, $ce] = $range;
                            $p = $group[$i] ?? null;
                            $jabatan = $p ? trim((string) ($p['jabatan'] ?? '')) : '';
                            $sheet->mergeCells("{$cs}{$rowCursor}:{$ce}{$rowCursor}");
                            $sheet->setCellValue("{$cs}{$rowCursor}", $jabatan);
                            $sheet->getStyle("{$cs}{$rowCursor}:{$ce}{$rowCursor}")->applyFromArray([
                                'font'      => ['size' => 10],
                                'alignment' => ['horizontal' => Alignment::HORIZONTAL_CENTER, 'vertical' => Alignment::VERTICAL_CENTER, 'wrapText' => true],
                            ]);
                        }
                        $sheet->getRowDimension($rowCursor)->setRowHeight(-1);
                        $rowCursor++;

                        // Baris tugas
                        foreach ($ranges as $i => $range) {
                            [$cs, $ce] = $range;
                            $p = $group[$i] ?? null;
                            $tugas = $p ? trim((string) ($p['tugas'] ?? '')) : '';
                            $sheet->mergeCells("{$cs}{$rowCursor}:{$ce}{$rowCursor}");
                            $sheet->setCellValue("{$cs}{$rowCursor}", $tugas);
                            $sheet->getStyle("{$cs}{$rowCursor}:{$ce}{$rowCursor}")->applyFromArray([
                                'font'      => ['size' => 10],
                                'alignment' => ['horizontal' => Alignment::HORIZONTAL_CENTER, 'vertical' => Alignment::VERTICAL_CENTER, 'wrapText' => true],
                            ]);
                        }
                        $sheet->getRowDimension($rowCursor)->setRowHeight(-1);
                        $rowCursor++;

                        // Baris TTD: 1 baris tinggi untuk gambar floating
                        $ttdRowH = 55; // pt — cukup untuk gambar TTD
                        $sheet->getRowDimension($rowCursor)->setRowHeight($ttdRowH);

                        foreach ($ranges as $i => $range) {
                            [$cs, $ce] = $range;
                            if ($cs !== $ce) {
                                $sheet->mergeCells("{$cs}{$rowCursor}:{$ce}{$rowCursor}");
                            }

                            $p = $group[$i] ?? null;
                            if (!$p) {
                                continue;
                            }

                            $rawPath = trim((string) ($p['ttd_path'] ?? ''));
                            $ttdFull = $rawPath !== '' ? storage_path('app/public/' . ltrim($rawPath, '/')) : '';

                            if ($this->includeSignatureImage && $ttdFull !== '' && file_exists($ttdFull)) {
                                $ttdType      = trim((string) ($p['ttd_type'] ?? 'gambar'));
                                $isElektronik = ($ttdType === 'elektronik');
                                $maxH = $isElektronik ? 50 : 48;
                                $maxW = $isElektronik ? 190 : 200;

                                $targetH = $maxH;
                                $targetW = (int) round($maxW * 0.75);
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

                                // Hitung total pixel lebar slot
                                $startColN   = \PhpOffice\PhpSpreadsheet\Cell\Coordinate::columnIndexFromString($cs);
                                $endColN     = \PhpOffice\PhpSpreadsheet\Cell\Coordinate::columnIndexFromString($ce);
                                $totalSlotPx = 0;
                                for ($cn = $startColN; $cn <= $endColN; $cn++) {
                                    $cl  = \PhpOffice\PhpSpreadsheet\Cell\Coordinate::stringFromColumnIndex($cn);
                                    $cw  = $sheet->getColumnDimension($cl)->getWidth();
                                    if ($cw < 0) $cw = 8;
                                    $totalSlotPx += (int) round($cw * 7);
                                }

                                // Posisi kiri gambar agar ter-center dalam slot
                                $targetStartPx = max(0, (int)(($totalSlotPx - $targetW) / 2));

                                // Cari anchor column: kolom yang MENGANDUNG targetStartPx
                                $cumPx      = 0;
                                $anchorCol  = $cs;
                                $anchorOffX = $targetStartPx;
                                for ($cn = $startColN; $cn <= $endColN; $cn++) {
                                    $cl   = \PhpOffice\PhpSpreadsheet\Cell\Coordinate::stringFromColumnIndex($cn);
                                    $cw   = $sheet->getColumnDimension($cl)->getWidth();
                                    if ($cw < 0) $cw = 8;
                                    $cwPx = (int) round($cw * 7);
                                    if ($cumPx + $cwPx > $targetStartPx || $cn === $endColN) {
                                        $anchorCol  = $cl;
                                        $anchorOffX = max(0, $targetStartPx - $cumPx);
                                        break;
                                    }
                                    $cumPx += $cwPx;
                                }

                                $cellHeightPx = (int) round($ttdRowH * 1.333);
                                $offsetY      = max(2, (int)(($cellHeightPx - $targetH) / 2));

                                $drawing = new Drawing();
                                $drawing->setName('TTD_buku_' . $i . '_' . $rowCursor);
                                $drawing->setDescription('Tanda Tangan ' . ($p['nama'] ?? ''));
                                $drawing->setPath($ttdFull);
                                $drawing->setCoordinates("{$anchorCol}{$rowCursor}");
                                $drawing->setOffsetX($anchorOffX);
                                $drawing->setOffsetY($offsetY);
                                $drawing->setResizeProportional(false);
                                $drawing->setWidth($targetW);
                                $drawing->setHeight($targetH);
                                $drawing->setWorksheet($sheet);
                            }
                        }
                        $rowCursor++;

                        // Baris nama (dengan underline pada text)
                        foreach ($ranges as $i => $range) {
                            [$cs, $ce] = $range;
                            $p = $group[$i] ?? null;
                            $nama = $p ? trim((string) ($p['nama'] ?? '')) : '';
                            $sheet->mergeCells("{$cs}{$rowCursor}:{$ce}{$rowCursor}");
                            $sheet->setCellValue("{$cs}{$rowCursor}", $nama);
                            $sheet->getStyle("{$cs}{$rowCursor}:{$ce}{$rowCursor}")->applyFromArray([
                                'font'      => ['size' => 10, 'underline' => Font::UNDERLINE_SINGLE],
                                'alignment' => ['horizontal' => Alignment::HORIZONTAL_CENTER, 'vertical' => Alignment::VERTICAL_CENTER],
                            ]);
                        }
                        $sheet->getRowDimension($rowCursor)->setRowHeight(20);
                        $rowCursor++;

                        // Gap antar kelompok pejabat (jarak antar data pejabat)
                        $sheet->getRowDimension($rowCursor)->setRowHeight(22);
                        $rowCursor++;
                        $sheet->getRowDimension($rowCursor)->setRowHeight(22);
                        $rowCursor++;
                    };

                    if ($signerCount <= 3) {
                        $renderGroup($signers, $ranges3);
                    } elseif ($signerCount === 4) {
                        $renderGroup(array_slice($signers, 0, 2), $ranges2);
                        $renderGroup(array_slice($signers, 2, 2), $ranges2);
                    } elseif ($signerCount === 5) {
                        $renderGroup(array_slice($signers, 0, 2), $ranges2);
                        $renderGroup(array_slice($signers, 2, 2), $ranges2);
                        $renderGroup(array_slice($signers, 4, 1), $rangeCenter);
                    } else {
                        // 6+ pejabat: layout 3 per baris (kiri ke kanan)
                        $groups = array_chunk($signers, 3);
                        foreach ($groups as $group) {
                            $renderGroup($group, $ranges3);
                        }
                    }
                }

                // ── 5. Print settings ─────────────────────────────────────────────────
                $sheet->getPageSetup()
                    ->setOrientation(\PhpOffice\PhpSpreadsheet\Worksheet\PageSetup::ORIENTATION_LANDSCAPE)
                    ->setPaperSize(\PhpOffice\PhpSpreadsheet\Worksheet\PageSetup::PAPERSIZE_A4)
                    ->setFitToPage(true)->setFitToWidth(1)->setFitToHeight(0);
                $sheet->getPageMargins()->setTop(0.5)->setBottom(0.5)->setLeft(0.5)->setRight(0.5);
            },
        ];
    }
}
