<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\User;
use App\UserState;
use GuzzleHttp\Client;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class WxController extends Controller
{
    /**
     * 用于微信回调的API.
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
     * 通过openid自动登陆.
     * POST
     * @param Request $request
     */
    public function autologin(Request $request) {
        $code = $request->get('code');
        $openid = $this->getWxOpenid($code);

        if (!isset($openid) or !$openid) {
            return template(-1, '用户认证失败');
        }

        //将openid存入session
        session(['openid', $openid]);
        if (!$user = User::fromOpenid($openid)) {
            //TODO: 需要重新设计数据库
            $user = new User();
            $user->openid = $openid;
            $user->save();
            $state = new UserState();
            $user->state()->save($state);
        }


        if (!$token = Auth::login($user)) {
            return template(-1, '生成token失败');
        } else {
            return template(1, '认证成功', ['user' => $user, 'token' => $token]);
        }
    }

    /** 
     * TODO: 将其封装到工具类中
     * 工具函数
     * use code to get openid
     * @param $code
     * @return mixed
     */
    private function getWxOpenid($code) {
        $client = new Client();
        $response = $client->request('GET', 'https://api.weixin.qq.com/sns/oauth2/access_token?appid='.env('WECHAT_APPID').'&secret='.env('WECHAT_SECRET').'&code='.$code.'&grant_type=authorization_code', ['verify' => false]);
        $data = json_decode($response->getBody(), true);
        if (isset($data['openid'])) {
            return $data['openid'];
        }
        return null;
    }
}
