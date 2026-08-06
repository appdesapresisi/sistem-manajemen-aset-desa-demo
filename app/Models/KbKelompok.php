<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class KbKelompok extends Model
{
    protected $table = 'kb_kelompok';
    public $timestamps = false;

    protected $fillable = ['bidang_id', 'kode', 'uraian'];

    public function bidang()
    {
        return $this->belongsTo(KbBidang::class, 'bidang_id');
    }

    public function subKelompok()
    {
        return $this->hasMany(KbSubKelompok::class, 'kelompok_id');
    }
}
