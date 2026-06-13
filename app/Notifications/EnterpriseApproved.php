<?php

namespace App\Notifications;

use App\Models\Enterprise;
use Illuminate\Notifications\Notification;
use Illuminate\Notifications\Messages\MailMessage;

class EnterpriseApproved extends Notification
{
    protected $enterprise;

    public function __construct(Enterprise $enterprise)
    {
        $this->enterprise = $enterprise;
    }

    public function via($notifiable)
    {
        return ['mail'];
    }

    public function toMail($notifiable)
    {
        return (new MailMessage)
            ->subject('企业资质审核通过')
            ->line('您的企业「' . $this->enterprise->name . '」已通过审核。')
            ->line('现在可以登录平台发布招聘岗位了。')
            ->action('前往管理', url('/enterprise/jobs'));
    }
}
