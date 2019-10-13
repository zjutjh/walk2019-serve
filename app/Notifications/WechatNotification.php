<?php

namespace App\Notifications;

use App\WxTemplate;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Notification;
use Yansongda\LaravelNotificationWechat\WechatChannel;
use Yansongda\LaravelNotificationWechat\WechatMessage;

class WechatNotification extends Notification implements ShouldQueue
{
    use Queueable;

    private $data;

    public function __construct( $data = WxTemplate::Test)
    {
        $this->data = $data;
    }

    public function via($notifiable)
    {
        return [WechatChannel::class];
    }

    public function toWechat($notifiable)
    {
        $accessToken = getAccessToken();

        $this->data['keyword3']=date('Y-m-d H:i:s', time());
        return WechatMessage::create($accessToken)
            ->to($notifiable->openid)
            ->template('0qUpCTpgeYMFbjEKQ4W_D3ZNx5zUzQIfgasgqYX53mg')
            ->url('http://walk.zjutjh.com')
            ->data($this->data);
    }
}
