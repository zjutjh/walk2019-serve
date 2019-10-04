<?php

namespace App\Notifications;

use App\WxTemplate;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Notification;


class Wechat extends Notification implements ShouldQueue
{
    use Queueable;

    private $openid;
    private $data;

    public function __construct($openid, $data = WxTemplate::Test)
    {
        $this->openid = $openid;
        $this->data = $data;
    }

    public function via($notifiable)
    {
        return [\App\Channels\wechatChannel::class];
    }

    public function toWechat($notifiable)
    {
        $data = $this->data;

        return [
            'openid' => $this->openid,
            'url' => 'http://walk.zjutjh.com',
            'data' => $data
        ];
    }
}
