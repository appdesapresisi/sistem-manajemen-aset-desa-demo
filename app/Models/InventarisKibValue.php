<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class InventarisKibValue extends Model
{
    use HasFactory;

    protected $table = 'inventaris_kib_values';

    protected $fillable = [
        'inventaris_id',
        'kib_field_key',
        'value',
    ];

    public function inventaris()
    {
        return $this->belongsTo(Inventaris::class, 'inventaris_id');
    }
}
