<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Payslip extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'period',
        'basic_salary',
        'allowances',
        'deductions',
        'overtime_pay',
        'net_salary',
        'status',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}