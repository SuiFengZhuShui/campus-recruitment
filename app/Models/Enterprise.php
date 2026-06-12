<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Enterprise extends Model
{
    protected $fillable = [
        'user_id', 'name', 'credit_code', 'industry', 'scale', 'intro',
        'contact_name', 'contact_phone', 'email', 'college_id', 'status', 'audit_remark',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function college()
    {
        return $this->belongsTo(College::class);
    }

    public function docs()
    {
        return $this->hasMany(EnterpriseDoc::class);
    }

    public function jobs()
    {
        return $this->hasMany(Job::class);
    }

    public function isApproved(): bool
    {
        return $this->status === 'approved';
    }
}
