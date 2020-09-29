<?php

namespace App\Http\Controllers;

use GuzzleHttp\Client;
use GuzzleHttp\Exception\GuzzleException;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class WXLoginController extends Controller
{
    /**
     * 微信回调
     */
    public function oauth()
    {
        return redirect('https://open.weixin.qq.com/connect/oauth2/authorize?appid='
            . config('api.wx.WECHAT_APPID')
            . '&redirect_uri='
            . urlencode(config('api.jh.oauth'))
            . urlencode(config('api.wx.WECHAT_REDIRECT'))
            . '&response_type=code&scope=snsapi_base&state=STATE#wechat_redirect');
    }

    /**
     *  微信登录
     * @param Request $request
     * @return JsonResponse
     * @throws GuzzleException
     */
    public function wxLogin(Request $request)
    {
        $code = $request->get('code');
        $openid = $this->getWxOpenid($code);

        if (!isset($openid))
            return StandardJsonResponse(0, '请在微信中打开');

        if (!$openid)
            return StandardJsonResponse(0, '请在微信中打开');
        if (!CheckSubscription($openid))
            return StandardFailJsonResponse('请先关注浙江工业大学精弘网络公众号');

        session(['openid' => $openid]);
        return StandardSuccessJsonResponse();
    }

    /** use code to get openid
     * @param $code
     * @return mixed
     * @throws GuzzleException
     */
    public function getWxOpenid($code)
    {
        $response = (new Client())->request('GET',
            'https://api.weixin.qq.com/sns/oauth2/access_token?'
            . 'appid=' . config('api.wx.WECHAT_APPID')
            . '&secret=' . config('api.wx.WECHAT_SECRET')
            . '&code=' . $code
            . '&grant_type=authorization_code', ['verify' => false]);

        $data = json_decode($response->getBody(), true);

        if (isset($data['openid']))
            return $data['openid'];

        return null;
    }
}
