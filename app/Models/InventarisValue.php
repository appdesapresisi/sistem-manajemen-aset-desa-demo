<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class InventarisValue extends Model
{
    use HasFactory;

    protected $table = 'inventaris_values';

    protected $fillable = [
        'inventaris_id',
        'jenis_inventaris_data_id',
        'value',
    ];

    public function inventaris()
    {
        return $this->belongsTo(Inventaris::class, 'inventaris_id');
    }

    public function field()
    {
        return $this->belongsTo(JenisInventarisData::class, 'jenis_inventaris_data_id');
    }
}
