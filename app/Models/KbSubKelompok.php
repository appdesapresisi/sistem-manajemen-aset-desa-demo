<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class KbSubKelompok extends Model
{
    protected $table = 'kb_sub_kelompok';
    public $timestamps = false;

    protected $fillable = ['kelompok_id', 'kode', 'uraian'];

    public function kelompok()
    {
        return $this->belongsTo(KbKelompok::class, 'kelompok_id');
    }

    public function subSubKelompok()
    {
        return $this->hasMany(KbSubSubKelompok::class, 'sub_kelompok_id');
    }
}
