<?php

namespace App\Exports;

use App\Models\JenisInventaris;
use Maatwebsite\Excel\Concerns\WithMultipleSheets;

class AllInventarisExport implements WithMultipleSheets
{
    protected array $filters;
    protected bool $includeBukuInventaris;
    protected array $pejabatSnapshot;
    protected string $tanggalDokumen;
    protected string $keteranganDokumen;
    protected bool $includeSignatureImage;

    public function __construct(array $filters = [], bool $includeBukuInventaris = false, array $pejabatSnapshot = [], string $tanggalDokumen = '', string $keteranganDokumen = '', bool $includeSignatureImage = true)
    {
        $this->filters               = $filters;
        $this->includeBukuInventaris  = $includeBukuInventaris;
        $this->pejabatSnapshot       = $pejabatSnapshot;
        $this->tanggalDokumen        = $tanggalDokumen;
        $this->keteranganDokumen     = $keteranganDokumen;
        $this->includeSignatureImage = $includeSignatureImage;
    }

    public function sheets(): array
    {
        // Ambil semua jenis KECUALI Buku Inventaris Aset Desa (virtual — tidak punya inventaris sendiri)
        $allJenis = JenisInventaris::where('nama', '!=', 'Buku Inventaris Aset Desa')
            ->orderBy('id')
            ->get();

        $sheets = [];

        foreach ($allJenis as $jenis) {
            // Skip jenis yang tidak punya data — tidak buat sheet kosong
            $hasData = \App\Models\Inventaris::where('jenis_inventaris_id', $jenis->id)
                ->when(!empty($this->filters['tahun']), fn($q) => $q->where('tahun_perolehan', '<=', $this->filters['tahun']))
                ->exists();
            if (!$hasData) continue;

            $sheets[] = new InventarisExport($jenis->id, $this->filters, !empty($this->pejabatSnapshot) ? $this->pejabatSnapshot : null, $this->includeSignatureImage, $this->tanggalDokumen);
        }

        // PhpSpreadsheet mensyaratkan minimal 1 sheet dalam workbook.
        // Jika seluruh jenis kosong untuk filter yang dipilih, sertakan jenis pertama sebagai template kosong.
        if (empty($sheets) && $allJenis->isNotEmpty()) {
            $firstJenis = $allJenis->first();
            $sheets[] = new InventarisExport($firstJenis->id, $this->filters, !empty($this->pejabatSnapshot) ? $this->pejabatSnapshot : null, $this->includeSignatureImage, $this->tanggalDokumen);
        }

        return $sheets;
    }
}
