<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class WorkingGroup extends Model
{
    use HasFactory, \App\Models\Traits\HasTenantScope;

    protected $fillable = [
        'name', 
        'region', 
        'area', 
        'sub_area', 
        'date_applied',
        'default_working_hour_id', 
        'default_late_tolerance', 
        'default_stores', 
        'is_first_visit_locked'
    ];

    // Relasi ke jadwal harian
    public function schedules()
    {
        return $this->hasMany(WorkingGroupSchedule::class);
    }
    // Relasi User ke Working Group
    public function workingGroup()
    {
        return $this->belongsTo(WorkingGroup::class);
    }
    protected $casts = [
        'default_stores' => 'array',
        'is_first_visit_locked' => 'boolean',
    ];

    // Relasi ke tabel Jam Kerja (Default)
    public function defaultWorkingHour()
    {
        return $this->belongsTo(WorkingHour::class, 'default_working_hour_id');
    }

    // Relasi ke Jadwal Harian (Override

    // Relasi List Nama (Karyawan di grup ini)
    public function users()
    {
        return $this->hasMany(User::class);
    }
}