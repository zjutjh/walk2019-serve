<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;


class Wechat extends Notification implements ShouldQueue
{
    use Queueable;

    public function __construct()
    {
        //
    }

    public function via($notifiable)
    {
        return [\App\Channels\wechatChannel::class];
    }

    public function toWechat($notifiable)
    {
        $data = [
            'first' => 'Test First',
            'keyword1' => 'keyword1',
            'keyword2' => 'keyword2',
            'keyword3' => ['keyword3', '#000000'],
            'remark' => ['Test remark', '#fdfdfd'],
        ];

        return [
            'openid' => $this->openid,
            'url' => 'http://walk.zjutjh.com',
            'data' => $data
        ];
    }
}
