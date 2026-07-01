<?php

namespace App\Notifications;

use Illuminate\Notifications\Notification;
use Illuminate\Notifications\Messages\MailMessage;

class PasswordReset extends Notification
{
    protected $token;

    public function __construct(string $token)
    {
        $this->token = $token;
    }

    public function via($notifiable)
    {
        return ['mail'];
    }

    public function toMail($notifiable)
    {
        $resetUrl = url('/reset-password?token=' . $this->token . '&email=' . urlencode($notifiable->email));

        return (new MailMessage)
            ->subject('密码重置 — 校园招聘平台')
            ->line('您正在申请重置密码。')
            ->line('请点击下方按钮设置新密码，链接有效期 60 分钟。')
            ->action('重置密码', $resetUrl)
            ->line('如果您未申请密码重置，请忽略此邮件。');
    }
}
