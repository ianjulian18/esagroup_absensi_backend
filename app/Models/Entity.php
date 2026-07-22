<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Entity extends Model
{
    use HasFactory;

    protected $fillable = ['name', 'description'];

    public function principals()
    {
        return $this->hasMany(Principal::class);
    }

    public function users()
    {
        return $this->hasMany(User::class);
    }

    public function stores()
    {
        return $this->hasMany(Store::class);
    }

    public function workingGroups()
    {
        return $this->hasMany(WorkingGroup::class);
    }
}
