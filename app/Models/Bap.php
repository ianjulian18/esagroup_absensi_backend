<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Bap extends Model
{
    use HasFactory, \App\Models\Traits\HasTenantScope;

    protected $fillable = [
        'user_id',
        'date',
        'type',
        'time',
        'reason',
        'proof_path',
        'status',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}