<?php

namespace App\Http\Controllers;

use App\Exports\UsersExport;
use App\Services\UserCenterService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Maatwebsite\Excel\Facades\Excel;


class UserController extends Controller
{
    //注册报名
    public function register(Request $request)
    {
        $UserInfo= $request->all();



    }
    //注册报名
    public function updateInfo(Request $request)
    {
        $UserInfo= $request->all();



    }

    /**
     * 创建详细信息
     */
    public function detailInfo(Request $request)
    {
        $detail = $request->all();
        if (
            strlen($detail['name']) > 180 ||
            strlen($detail['qq']) >  180 ||
            strlen($detail['email']) > 180 ||
            strlen($detail['wx_id']) > 180
        ) {
            return RJM(-1, '字段过长');
        }
        if ($detail['type'] == 'create' && $detail['height'] > 300) {
            return RJM(-1, '身高过大');
        }

        $user = Auth::user();
        $user->fill($detail);
        $user->save();
        if ($detail['type'] == 'create') {
            $uState = $user->state()->first();
            $uState->state = 1;
            $uState->save();
        }
        return RJM(1, '更新信息成功');
    }


    /**
     * 验证学生身份； 改变状态为已经报名
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function verifyStu(Request $request)
    {
        $sid = $request->get('sid');
        $passwd = $request->get('passwd');
        $identity = $request->get('identity');
        $uCenter = new UserCenterService();

        if (!$error = $uCenter->checkJhPassport($sid, $passwd)) {
            $error = $uCenter->getError();
            return RJM(-1, $error ?  $error : '用户或密码错误');
        }

        $user = Auth::user();
        $user->sid = $sid;
        $user->identity = $identity;
        if (!$user->id_card) {
            $uState = $user->state()->first();
            $uState->state = 5;
            $uState->save();
        }

        $user->save();

        return RJM(1, '登录成功,请完善信息');
    }

    /**
     * 确定身份： 教职工 校友 其他; 改变状态为已经报名
     */
    public function verifyOther(Request $request)
    {
        $identity = $request->get('identity');

        $user = Auth::user();
        $user->identity = $identity;
        if (!$user->id_card) {
            $uState = $user->state()->first();
            $uState->state = 5;
            $uState->save();
        }
        $user->save();

        return RJM(1, '登录成功,请完善信息');
    }


    /*
     * 获取报名名单
     */
    public function download()
    {
        return Excel::download(new UsersExport(), '报名名单.xlsx');
    }

    /**
     * 确认是否关注公众号
     */
    private function identifyGz($openid)
    {
        $client = new Client();
        $response = $client->request('GET', 'https://api.weixin.qq.com/sns/oauth2/access_token?appid=' . env('WECHAT_APPID') . '&secret=' . env('WECHAT_SECRET') . '&code=' . $code . '&grant_type=authorization_code', ['verify' => false]);
        $data = json_decode($response->getBody(), true);
        if (isset($data['openid'])) {
            return $data['openid'];
        }
        return null;
    }
}
