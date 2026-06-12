<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class College extends Model
{
    protected $fillable = ['school_id', 'name'];

    public function school()
    {
        return $this->belongsTo(School::class);
    }

    public function enterprises()
    {
        return $this->hasMany(Enterprise::class);
    }

    public function students()
    {
        return $this->hasMany(Student::class);
    }
}
