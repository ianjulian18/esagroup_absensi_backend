<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Store extends Model
{
    use HasFactory, \App\Models\Traits\HasTenantScope;

    // Izinkan pengisian massal untuk fitur Bulk Upload Excel nanti
    protected $fillable = [
        'region_id', 'area_id', 'sub_area_id', 'channel_id', 
        'name', 'account_name', 'timezone', 'latitude', 'longitude', 'address'
    ];

    // Relasi ke tabel master
    public function region() {
        return $this->belongsTo(Region::class);
    }

    public function area() {
        return $this->belongsTo(Area::class);
    }

    public function subArea() {
        return $this->belongsTo(SubArea::class);
    }

    public function channel() {
        return $this->belongsTo(Channel::class);
    }
}