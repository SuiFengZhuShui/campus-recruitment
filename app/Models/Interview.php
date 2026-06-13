<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Interview extends Model
{
    protected $fillable = [
        'application_id', 'scheduled_at', 'location', 'type', 'contact', 'note', 'status',
    ];

    public function application()
    {
        return $this->belongsTo(Application::class);
    }

    public function statusLabel(): string
    {
        $map = [
            'invited' => '待确认',
            'accepted' => '已接受',
            'declined' => '已拒绝',
            'completed' => '已完成',
        ];
        return $map[$this->status] ?? '未知';
    }
}
