<?php

namespace App\Exports;

use Maatwebsite\Excel\Concerns\WithMultipleSheets;

class KartuInventarisRuanganFormat2Export implements WithMultipleSheets
{
    public function __construct(
        protected array  $ruanganData,
        protected float  $totalNilai,
        protected ?array $perbekelPejabat,
        protected ?array $kasiKesejahteraan,
        protected array  $petugasPejabat,
        protected bool   $includeImg,
        protected array  $filters = [],
        protected string $tanggalDokumen = '',
        protected string $keteranganDokumen = ''
    ) {}

    public function sheets(): array
    {
        $sheets = [];
        foreach ($this->ruanganData as $rd) {
            if (empty($rd['rows'])) continue;

            // Resolve penanggung jawab for this specific room
            $resolvedPj = null;
            $ruangan = $rd['ruangan'];
            if ($ruangan->penanggungJawab) {
                $pjUser = $ruangan->penanggungJawab;
                if ($pjUser->pejabat) {
                    $resolvedPj = $pjUser->pejabat->toArray();
                } else {
                    $resolvedPj = [
                        'id' => null,
                        'nama' => $pjUser->nama,
                        'jabatan' => $pjUser->role_name_display ?: 'Penanggungjawab Ruangan',
                        'ttd_path' => null,
                        'ttd_type' => 'gambar',
                    ];
                }
            }
            $roomSigner = $resolvedPj ?? $this->kasiKesejahteraan;

            $sheets[] = new KartuInventarisRuanganFormat2Sheet(
                [$rd],
                $this->totalNilai,
                $this->perbekelPejabat,
                $roomSigner,
                $this->petugasPejabat,
                $this->includeImg,
                $this->filters,
                $this->tanggalDokumen,
                $this->keteranganDokumen
            );
        }

        if (empty($sheets)) {
            $sheets[] = new KartuInventarisRuanganFormat2Sheet(
                $this->ruanganData,
                $this->totalNilai,
                $this->perbekelPejabat,
                $this->kasiKesejahteraan,
                $this->petugasPejabat,
                $this->includeImg,
                $this->filters,
                $this->tanggalDokumen,
                $this->keteranganDokumen
            );
        }

        return $sheets;
    }
}
