<?php

namespace App\Notifications;

use App\Models\Offer;
use Illuminate\Notifications\Notification;
use Illuminate\Notifications\Messages\MailMessage;

class OfferSent extends Notification
{
    protected $offer;

    public function __construct(Offer $offer)
    {
        $this->offer = $offer;
    }

    public function via($notifiable)
    {
        return ['mail'];
    }

    public function toMail($notifiable)
    {
        $job = $this->offer->application->job;
        $enterprise = $job->enterprise;

        return (new MailMessage)
            ->subject('录用通知书 — ' . $enterprise->name)
            ->line('恭喜！您已获得「' . $enterprise->name . '」的录用通知。')
            ->line('岗位：' . $this->offer->position)
            ->line('薪资：' . $this->offer->salary)
            ->line('入职日期：' . $this->offer->start_date)
            ->action('查看详情', url('/student/offers'));
    }
}
