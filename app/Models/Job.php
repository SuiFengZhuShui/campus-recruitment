<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Job extends Model
{
    protected $table = 'jobs';

    protected $fillable = [
        'enterprise_id', 'title', 'count', 'city', 'salary_min', 'salary_max',
        'education', 'major', 'skills', 'type', 'duty', 'requirement', 'welfare', 'status',
    ];

    public function enterprise()
    {
        return $this->belongsTo(Enterprise::class);
    }

    public function applications()
    {
        return $this->hasMany(Application::class);
    }
}
