<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Visit extends Model
{
    protected $fillable = [
        'attendance_id',
        'visit_type',
        'location_name',
        'visit_in',
        'visit_out',
        'latitude_in',
        'longitude_in',
        'photo_in',
        'latitude_out',
        'longitude_out',
        'photo_out',
        'status',
    ];
    public function attendance()
    {
        return $this->belongsTo(Attendance::class);
    }
}
