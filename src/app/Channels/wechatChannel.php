<?php

namespace App\Channels;

use Exception;
use GuzzleHttp\Client;
use Illuminate\Notifications\Notification;
use Illuminate\Support\Facades\Log;

class wechatChannel
{
    /**
     * 发送指定的通知.
     *
     * @param mixed $notifiable
     * @param Notification $notification
     * @return void
     */
    public function send($notifiable, Notification $notification)
    {
        $message = $notification->toWechat($notifiable);
        $client = new Client();
        try {
            $res = $client->post(config('api.jh.template'), [
                'json' => $message
            ]);
            Log::info($res);
        } catch (Exception $e) {

        }

    }
}
