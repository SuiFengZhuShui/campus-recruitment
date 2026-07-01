<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Application extends Model
{
    protected $fillable = ['job_id', 'student_id', 'status', 'remark'];

    public function statusLabel(): string
    {
        $map = ['pending' => '待审核', 'reviewed' => '已查看', 'interviewed' => '面试中', 'accepted' => '已录用', 'rejected' => '未通过'];
        return $map[$this->status] ?? '未知';
    }

    public function job()
    {
        return $this->belongsTo(Job::class);
    }

    public function student()
    {
        return $this->belongsTo(Student::class);
    }

    public function interview()
    {
        return $this->hasOne(Interview::class);
    }
}
