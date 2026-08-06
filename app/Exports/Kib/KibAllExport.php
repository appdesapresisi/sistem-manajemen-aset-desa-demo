<?php

namespace App\Exports\Kib;

use Maatwebsite\Excel\Concerns\WithMultipleSheets;

class KibAllExport implements WithMultipleSheets
{
    protected array $sheets;

    public function __construct(array $sheets)
    {
        $this->sheets = $sheets;
    }

    public function sheets(): array
    {
        return $this->sheets;
    }
}
