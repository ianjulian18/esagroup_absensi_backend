<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class WorkingGroupSchedule extends Model
{
    use HasFactory;

    protected $fillable = [
        'working_group_id',
        'day_of_week',
        'working_hour_id',
        'late_tolerance',
        'routing_type',
        'stores',
    ];

    // Beritahu Laravel kalau kolom 'stores' formatnya JSON (Array)
    protected $casts = [
        'stores' => 'array',
    ];

    public function workingGroup()
    {
        return $this->belongsTo(WorkingGroup::class);
    }

    // Relasi ke tabel Jam Kerja (Shift)
    public function workingHour()
    {
        return $this->belongsTo(WorkingHour::class);
    }
}