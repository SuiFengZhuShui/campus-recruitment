<?php

namespace App\Models;

use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class User extends Authenticatable
{
    use Notifiable;

    protected $fillable = [
        'role',
        'username',
        'email',
        'college_id',
        'name',
        'phone',
        'password',
        'remember_token',
        'status',
    ];

    protected $hidden = [
        'password',
        'remember_token',
    ];

    public function isSchool(): bool
    {
        return $this->role === 'school';
    }

    public function isCollege(): bool
    {
        return $this->role === 'college';
    }

    public function isEnterprise(): bool
    {
        return $this->role === 'enterprise';
    }

    public function isStudent(): bool
    {
        return $this->role === 'student';
    }

    public function enterprise()
    {
        return $this->hasOne(\App\Models\Enterprise::class);
    }

    public function student()
    {
        return $this->hasOne(\App\Models\Student::class);
    }
}
