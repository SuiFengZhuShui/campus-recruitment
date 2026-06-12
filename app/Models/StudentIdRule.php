<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class StudentIdRule extends Model
{
    protected $table = 'student_id_rules';

    protected $fillable = ['school_id', 'prefix', 'college_id'];

    public function school()
    {
        return $this->belongsTo(School::class);
    }

    public function college()
    {
        return $this->belongsTo(College::class);
    }
}
