<?php

namespace App\Notifications;

use App\Models\Enterprise;
use Illuminate\Notifications\Notification;
use Illuminate\Notifications\Messages\MailMessage;

class EnterpriseRejected extends Notification
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
            ->subject('企业资质审核未通过')
            ->line('您的企业「' . $this->enterprise->name . '」未通过审核。')
            ->line('原因：' . ($this->enterprise->audit_remark ?: '未提供'))
            ->line('请修改资质材料后重新提交。');
    }
}
