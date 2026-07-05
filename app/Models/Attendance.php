<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Attendance extends Model
{
    use HasFactory;

    // Mengizinkan kolom-kolom ini diisi data secara otomatis
    protected $fillable = [
        'user_id',
        'date',
        'clock_in',
        'clock_out',
        'status',
        'latitude',  
        'longitude',
        'photo_in', 
        'photo_out', 
    ];

    // Memberitahu sistem bahwa data absen ini milik satu User
    public function user()
    {
        return $this->belongsTo(User::class);
    }
    public function visits()
    {
        return $this->hasMany(Visit::class);
    }

}
