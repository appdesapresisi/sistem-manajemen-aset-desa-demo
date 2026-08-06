<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class JenisInventarisPejabat extends Model
{
    use HasFactory;

    protected $table = 'jenis_inventaris_pejabats';

    protected $fillable = [
        'jenis_inventaris_id',
        'pejabat_id',
        'order',
    ];

    public function jenis()
    {
        return $this->belongsTo(JenisInventaris::class, 'jenis_inventaris_id');
    }

    public function pejabat()
    {
        return $this->belongsTo(Pejabat::class, 'pejabat_id');
    }
}
