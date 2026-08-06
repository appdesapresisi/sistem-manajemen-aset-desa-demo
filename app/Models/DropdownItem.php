<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class DropdownItem extends Model
{
    use HasFactory;

    protected $table = 'dropdown_items';

    protected $fillable = [
        'dropdown_id',
        'label',
        'order',
    ];

    public function dropdown()
    {
        return $this->belongsTo(Dropdown::class, 'dropdown_id');
    }
}
