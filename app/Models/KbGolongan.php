<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class KbGolongan extends Model
{
    protected $table = 'kb_golongan';
    public $timestamps = false;

    protected $fillable = ['kode', 'uraian'];

    public function bidang()
    {
        return $this->hasMany(KbBidang::class, 'golongan_id');
    }
}
