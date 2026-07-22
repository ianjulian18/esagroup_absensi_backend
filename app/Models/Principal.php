<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Principal extends Model
{
    use HasFactory;

    protected $fillable = ['entity_id', 'name', 'description'];

    public function entity()
    {
        return $this->belongsTo(Entity::class);
    }

    public function users()
    {
        return $this->belongsToMany(User::class, 'principal_user');
    }

    public function stores()
    {
        return $this->hasMany(Store::class);
    }
}
