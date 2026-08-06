<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class UserDeviceSession extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'device_id',
        'session_id',
        'ip_address',
        'user_agent',
        'last_seen_at',
    ];

    protected $casts = [
        'last_seen_at' => 'datetime',
    ];
}
