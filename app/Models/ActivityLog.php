<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ActivityLog extends Model
{
    protected $table = 'activity_logs';

    protected $fillable = [
        'user_id',
        'module',
        'action',
        'detail',
        'subject_id',
        'subject_name',
    ];

    public function user()
    {
        return $this->belongsTo(User::class, 'user_id');
    }
}
