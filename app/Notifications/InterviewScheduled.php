<?php

namespace App\Notifications;

use App\Models\Interview;
use Illuminate\Notifications\Notification;
use Illuminate\Notifications\Messages\MailMessage;

class InterviewScheduled extends Notification
{
    protected $interview;

    public function __construct(Interview $interview)
    {
        $this->interview = $interview;
    }

    public function via($notifiable)
    {
        return ['mail'];
    }

    public function toMail($notifiable)
    {
        $interview = $this->interview;
        $job = $interview->application->job;
        $enterprise = $job->enterprise;

        $typeLabel = $interview->type === 'online' ? '线上' : '线下';

        return (new MailMessage)
            ->subject('面试邀请 — ' . $enterprise->name)
            ->line('「' . $enterprise->name . '」邀请您参加面试。')
            ->line('岗位：' . $job->title)
            ->line('时间：' . $interview->scheduled_at)
            ->line('地点：' . $interview->location . '（' . $typeLabel . '）')
            ->line('联系人：' . ($interview->contact ?: '-'))
            ->line('备注：' . ($interview->note ?: '-'))
            ->action('查看面试详情', url('/student/interviews'));
    }
}
