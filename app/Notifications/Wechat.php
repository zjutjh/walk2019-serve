<?php

namespace App\Notifications;

use App\WxTemplate;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Notification;


class Wechat extends Notification implements ShouldQueue
{
    use Queueable;
    private $data;

    public function __construct( $data = WxTemplate::Test)
    {
        $this->data = $data;
    }

    public function via($notifiable)
    {
        return [\App\Channels\wechatChannel::class];
    }

    public function toWechat($notifiable)
    {

        $data = $this->data;
        $this->data['keyword3']=date('Y-m-d H:i:s', time());
        return [
            'openid' =>$notifiable->openid,
            'url' => 'http://walk.zjutjh.com',
            'data' => $data
        ];
    }
}
