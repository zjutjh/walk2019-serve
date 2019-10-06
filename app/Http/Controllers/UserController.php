<?php

namespace App\Http\Controllers;

use App\Exports\UsersExport;
use App\Services\UserCenterService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Maatwebsite\Excel\Facades\Excel;
use Illuminate\Support\Facades\Validator;

class UserController extends Controller
{
    /**
     * 获取用户信息.
     */
    public function userInfo() {
        if (!$user = Auth::user()) {
            return template(-1, '登陆过期');
        } elseif (!$token = Auth::login($user)) {
            return template(-1, '生成token失败');
        } else {
            return template(1, '认证成功', ['user' => $user, 'token' => $token]);
        }
    }

    /**
     * 更新个人的信息
     */
    public function updateInfo(Request $request) {
        $validator = Validator::make($request->all(), [
            'name' => 'required|between:2,32',
            'phone' => 'required|size:11',
            'email' => 'email',
            'qq_id' => 'alpha_num',
            'wx_id' => 'alpha_num',
            'height' => 'integer|between:50,300'
        ]);
        
        if($validator->fails()){
            return template(-1, '字段不符合要求');
        }
    
        $user = Auth::user();
        $user->fill($detail);
        if ($detail['type'] == 'create') {
            $user->state = 1;
        }
        $user->save();
        return template(1, '更新信息成功');

    }


    /**
     * 验证学生身份； 改变状态为已经报名
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function verifyStu(Request $request) {
        $uid = $request->get('uid');
        $password = $request->get('password');
        //TODO: 需要对身份做验证
        $identity = $request->get('identity');
        $uCenter = new UserCenterService();

        if  (!$error = $uCenter->checkJhPassport($uid, $password)) {
            $error = $uCenter->getError();
            return template(-1, $error ?  $error: '用户或密码错误');
        }

        $user = Auth::user();
        $user->uid = $uid;
        $user->identity = $identity;
        //TODO: 完善确认是否正确填写信息的逻辑
        if (!$user->id_card) {
            $user->state=5;
        } 

        $user->save();

        return template(1, '登录成功,请完善信息');
    }

    /**
     * 确定身份： 教职工 校友 其他; 改变状态为已经报名
     */
    public function verifyOther(Request $request) {
        $identity = $request->get('identity');

        $user = Auth::user();
        $user->identity = $identity;
        if (!$user->id_card) {
            $user->state=5;
        }
        $user->save();
        return template(1, '登录成功,请完善信息');
    }


    /*
     * 获取报名名单
     */
    public function download() {
        return Excel::download(new UsersExport(), '报名名单.xlsx');

    }

    /**
     * 确认是否关注公众号
     */
    private function identifyGz($openid) {
        $client = new Client();
        $response = $client->request('GET', 'https://api.weixin.qq.com/sns/oauth2/access_token?appid='.env('WECHAT_APPID').'&secret='.env('WECHAT_SECRET').'&code='.$code.'&grant_type=authorization_code', ['verify' => false]);
        $data = json_decode($response->getBody(), true);
        if (isset($data['openid'])) {
            return $data['openid'];
        }
        return null;
    }


}
