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
    public function oauth() {
        return redirect('https://open.weixin.qq.com/connect/oauth2/authorize?appid='
            .env('WECHAT_APPID')
            .'&redirect_uri='
            .urlencode(config('api.jh.oauth'))
            .urlencode(env('WECHAT_REDIRECT'))
            .'&response_type=code&scope=snsapi_base&state=STATE#wechat_redirect');
    }

    /**
     *  微信登录
     * @param Request $request
     * @return JsonResponse
     * @throws GuzzleException
     */
    public function wxLogin(Request $request) {
        $code = $request->get('code');
        $openid = $this->getWxOpenid($code);

        if (!isset($openid))
            return StandardJsonResponse(-1, 'Failed');

        if (!$openid)
            return StandardJsonResponse(-1, 'Failed');

        session(['openid' => $openid]);
        $openid=$request->session()->get('openid');
        return StandardJsonResponse(1, 'Success',$openid);
    }

    /** use code to get openid
     * @param $code
     * @return mixed
     * @throws GuzzleException
     */
    public function getWxOpenid($code) {
        $response = (new Client())->request('GET', 
            'https://api.weixin.qq.com/sns/oauth2/access_token?'
            .'appid='.env('WECHAT_APPID')
            .'&secret='.env('WECHAT_SECRET')
            .'&code='.$code
            .'&grant_type=authorization_code', ['verify' => false]);
        $data = json_decode($response->getBody(), true);
        if (isset($data['openid'])) {
            return $data['openid'];
        }
        return null;
    }
}
