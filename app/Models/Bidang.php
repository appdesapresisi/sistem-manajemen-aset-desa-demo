<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Bidang extends Model
{
    use HasFactory;

    protected $table = 'bidangs';

    protected $fillable = [
        'nama',
        'deskripsi',
    ];

    /**
     * Relasi: Bidang memiliki banyak users (Kaur yang assign ke bidang ini)
     */
    public function users()
    {
        return $this->hasMany(User::class, 'bidang_id');
    }
}
