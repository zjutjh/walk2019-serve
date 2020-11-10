<?php


namespace App\Helpers;
use Illuminate\Support\Facades\Cache;

class SystemSettings
{
    const EventStartTime = 'api.system.BeginTime';
    const EventEndTime = 'api.system.EndTime';
    const SubmitStartTime ='api.system.SubmitBeginTime';
    const WechatAppID = 'api.wx.WECHAT_APPID';
    const WechatRedirect ='api.wx.WECHAT_REDIRECT';
    const WechatSecret ='api.wx.WECHAT_SECRET';

    public static function getSetting($name)
    {
        $val = Cache::get($name);
        return $val ? $val : config($name);
    }

    public static function setSetting($name)
    {
        Cache::set($name);
    }
}
