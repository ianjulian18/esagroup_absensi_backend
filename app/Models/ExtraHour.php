<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ExtraHour extends Model
{
    use HasFactory, \App\Models\Traits\HasTenantScope;

    protected $fillable = [
        'user_id',
        'date',
        'start_time',
        'end_time',
        'reason',
        'is_cross_day',
        'status',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}