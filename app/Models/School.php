<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class School extends Model
{
    protected $fillable = ['name'];

    public function colleges()
    {
        return $this->hasMany(College::class);
    }

    public function studentIdRules()
    {
        return $this->hasMany(StudentIdRule::class);
    }
}
