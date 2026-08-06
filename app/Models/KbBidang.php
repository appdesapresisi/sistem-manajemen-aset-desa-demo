<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class KbBidang extends Model
{
    protected $table = 'kb_bidang';
    public $timestamps = false;

    protected $fillable = ['golongan_id', 'kode', 'uraian'];

    public function golongan()
    {
        return $this->belongsTo(KbGolongan::class, 'golongan_id');
    }

    public function kelompok()
    {
        return $this->hasMany(KbKelompok::class, 'bidang_id');
    }
}
