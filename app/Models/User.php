<?php

namespace App\Models;

use Database\Factories\UserFactory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class User extends Authenticatable
{
    /** @use HasFactory<UserFactory> */
    use HasFactory, Notifiable;

    protected $fillable = [
        'role',
        'college_id',
        'name',
        'phone',
        'password',
        'status',
    ];

    protected $hidden = [
        'password',
        'remember_token',
    ];

    protected function casts(): array
    {
        return [
            'password' => 'hashed',
        ];
    }

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
}
