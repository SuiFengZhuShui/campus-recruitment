<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class EnterpriseDoc extends Model
{
    protected $fillable = [
        'enterprise_id', 'type', 'file_path', 'file_name', 'status', 'reject_reason',
    ];

    public function enterprise()
    {
        return $this->belongsTo(Enterprise::class);
    }
}
