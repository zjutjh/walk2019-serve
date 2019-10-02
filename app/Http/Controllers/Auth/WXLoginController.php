<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\User;
use App\UserState;
use GuzzleHttp\Client;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

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
     *  通过openid 自动登陆
     * @param Request $request
     * @return
     */
    public function wxLogin(Request $request) {
        $code = $request->get('code');
        $openid = $this->getWxOpenid($code);

        if (!isset($openid)) {
            return RJM(-1, '用户认证失败');
        }

        if (!$openid) {
            return RJM(-1, '用户认证失败');
        }
        session(['openid', $openid]);

        if (!$user = User::where('openid', $openid)->first()) {
            $user = new User();
            $user->openid = $openid;
            $user->save();
            $state = new UserState();
            $user->state()->save($state);
        }


        if (!$token = Auth::login($user)) {
            return RJM(-1, '生成token失败');
        }


        return RJM(1, '认证成功', ['user' => $user, 'token' => $token]);


    }

    public function getUserInfo() {
        if (!$user = Auth::user()) {
            return RJM(-1, '登陆过期');
        }


        if (!$token = Auth::login($user)) {
            return RJM(-1, '生成token失败');
        }


        return RJM(1, '认证成功', ['user' => $user, 'token' => $token]);
    }


    /** use code to get openid
     * @param $code
     * @return mixed
     */
    public function getWxOpenid($code) {
        $client = new Client();
        $response = $client->request('GET', 'https://api.weixin.qq.com/sns/oauth2/access_token?appid='.env('WECHAT_APPID').'&secret='.env('WECHAT_SECRET').'&code='.$code.'&grant_type=authorization_code', ['verify' => false]);
        $data = json_decode($response->getBody(), true);
        if (isset($data['openid'])) {
            return $data['openid'];
        }
        return null;
    }
}
