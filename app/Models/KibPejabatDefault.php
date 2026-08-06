<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class KibPejabatDefault extends Model
{
    use HasFactory;

    protected $table = 'kib_pejabat_defaults';

    protected $fillable = [
        'kib_type',
        'pejabat_id',
        'posisi',
    ];

    public function pejabat()
    {
        return $this->belongsTo(Pejabat::class, 'pejabat_id');
    }
}
