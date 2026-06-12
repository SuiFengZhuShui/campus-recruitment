<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Student extends Model
{
    protected $fillable = [
        'user_id', 'student_no', 'class_name', 'grade', 'college_id', 'resume_path',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function college()
    {
        return $this->belongsTo(College::class);
    }

    public function applications()
    {
        return $this->hasMany(Application::class);
    }
}
