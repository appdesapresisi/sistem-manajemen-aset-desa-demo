<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class KbSubSubKelompok extends Model
{
    protected $table = 'kb_sub_sub_kelompok';
    public $timestamps = false;

    protected $fillable = ['sub_kelompok_id', 'kode', 'uraian', 'kode_lengkap'];

    public function subKelompok()
    {
        return $this->belongsTo(KbSubKelompok::class, 'sub_kelompok_id');
    }
}
