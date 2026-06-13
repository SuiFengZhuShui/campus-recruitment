<?php

namespace App\Notifications;

use App\Models\Application;
use Illuminate\Notifications\Notification;
use Illuminate\Notifications\Messages\MailMessage;

class ApplicationSubmitted extends Notification
{
    protected $application;

    public function __construct(Application $application)
    {
        $this->application = $application;
    }

    public function via($notifiable)
    {
        return ['mail'];
    }

    public function toMail($notifiable)
    {
        $job = $this->application->job;
        $student = $this->application->student;

        return (new MailMessage)
            ->subject('新简历投递通知')
            ->line($student->user->name . ' 投递了「' . $job->title . '」岗位。')
            ->line('学生信息：' . $student->student_no . ' | ' . $student->class_name)
            ->action('查看投递列表', url('/enterprise/jobs/' . $job->id . '/applications'));
    }
}
