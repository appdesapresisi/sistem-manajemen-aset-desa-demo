<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Dropdown extends Model
{
    use HasFactory;

    protected $table = 'dropdowns';

    protected $fillable = [
        'nama',
        'deskripsi',
    ];

    public function items()
    {
        return $this->hasMany(DropdownItem::class, 'dropdown_id')->orderBy('order');
    }

    /**
     * Jenis inventaris data yang menggunakan dropdown ini.
     */
    public function jenisInventarisData()
    {
        return $this->hasMany(JenisInventarisData::class, 'dropdown_id');
    }
}
