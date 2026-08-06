<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class InventarisDeletedLog extends Model
{
    protected $table = 'inventaris_deleted_log';

    public $timestamps = false;

    protected $fillable = [
        'kode_barang',
        'nup',
        'nama_barang',
        'satuan',
        'nilai_perolehan',
        'tahun_perolehan',
        'deleted_at',
        'deleted_by',
    ];

    protected $casts = [
        'deleted_at'      => 'datetime',
        'nilai_perolehan' => 'decimal:2',
        'tahun_perolehan' => 'integer',
    ];
}
