<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class VisitLog extends Model
{
    use HasFactory, \App\Models\Traits\HasTenantScope;

    protected $fillable = [
        'user_id',
        'store_name',
        'issue',
        'action',
        'target',
        'actual',
        'deadline',
        'notes',
        'status',
    ];

    // Relasi agar HRD tahu laporan ini milik siapa
    public function user()
    {
        return $this->belongsTo(User::class);
    }
}