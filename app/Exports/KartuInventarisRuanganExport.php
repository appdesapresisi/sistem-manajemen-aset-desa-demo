<?php

namespace App\Exports;

use Maatwebsite\Excel\Concerns\WithMultipleSheets;

class KartuInventarisRuanganExport implements WithMultipleSheets
{
    public function __construct(
        protected array  $ruanganData,
        protected float  $totalNilai,
        protected ?array $sekretarisPejabat,
        protected array  $petugasPejabat,
        protected bool   $includeImg,
        protected array  $filters = [],
        protected string $tanggalDokumen = '',
        protected string $keteranganDokumen = ''
    ) {}

    public function sheets(): array
    {
        return [
            new KartuInventarisRuanganSheet(
                $this->ruanganData,
                $this->totalNilai,
                $this->sekretarisPejabat,
                $this->petugasPejabat,
                $this->includeImg,
                $this->filters,
                $this->tanggalDokumen,
                $this->keteranganDokumen
            ),
        ];
    }
}
