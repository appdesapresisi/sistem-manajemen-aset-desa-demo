<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ExportLog extends Model
{
    use HasFactory;

    protected $table = 'export_logs';

    protected $fillable = [
        'user_id',
        'inventaris_id',
        'jenis_inventaris_id',
        'pejabat_id',
        'tipe_export',
        'nama_file',
        'pejabat_override',
        'filters',
    ];

    // Timestamps NOW ENABLED (database sudah punya created_at & updated_at)
    // public $timestamps = false; // ← REMOVED

    protected $casts = [
        'pejabat_override' => 'array',
        'filters' => 'array',
    ];

    public function user()
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    public function inventaris()
    {
        return $this->belongsTo(Inventaris::class, 'inventaris_id');
    }

    public function jenisInventaris()
    {
        return $this->belongsTo(JenisInventaris::class, 'jenis_inventaris_id');
    }

    public function pejabat()
    {
        return $this->belongsTo(Pejabat::class, 'pejabat_id');
    }
}
