<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Offer extends Model
{
    protected $fillable = [
        'application_id', 'position', 'salary', 'start_date', 'note', 'status',
    ];

    public function application()
    {
        return $this->belongsTo(Application::class);
    }

    public function statusLabel(): string
    {
        $map = [
            'draft' => '草稿',
            'sent' => '已发送',
            'accepted' => '已接受',
            'declined' => '已拒绝',
        ];
        return $map[$this->status] ?? '未知';
    }
}
