<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Itinerary extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'date',
        'working_hour_id',
        'routing_type',
        'stores',
        'is_first_visit_locked',
    ];

    protected $casts = [
        'date' => 'date',
        'stores' => 'array',
        'is_first_visit_locked' => 'boolean',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function workingHour()
    {
        return $this->belongsTo(WorkingHour::class);
    }
}
