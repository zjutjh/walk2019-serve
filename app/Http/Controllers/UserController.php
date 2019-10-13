<?php

namespace App\Http\Controllers;

use App\User;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class UserController extends Controller
{

    private $userValidator = [
        'name' => 'required|between:2,32',
        'email' => 'required|email',
        'phone' => 'required|digits:11',
        'id_card' => 'required|alpha_dash|size:18',
        'qq' => 'digits_between:5,12',
        'identity' => 'required',
        'height' => 'integer|between:50,300',
        'sid' => 'required_if:identity,学生|digits_between:10,14',
        'school' => 'required_if:identity,学生'
    ];

    /**
     * [√测试通过]
     * 注册报名
     * @param Request $request
     * @return JsonResponse
     */
    public function register(Request $request)
    {
        $all = $request->all();

        $validator = Validator::make($request->all(), $this->userValidator);
        if ($validator->fails())
            return StandardFailJsonResponse('字段验证不通过,请检查一下');

        $openid = $request->session()->get('openid');
        if ($openid === null)
            return StandardFailJsonResponse('微信登录失败');

        if (!identifyGz($openid))
            return StandardFailJsonResponse('请先关注浙江工业大学精弘网络公众号');
        $user = new User();
        $user->openid = $openid;
        $user->fill($all);
        $user->save();

        return StandardJsonResponse(1, '报名成功');
    }


    /**
     * [√测试通过]
     * 获得当前用户信息
     * @param Request $request
     * @return JsonResponse
     */
    public function getMyInfo(Request $request)
    {
        $user = User::current();
        if ($user)
            return StandardSuccessJsonResponse($user);
        else
            return StandardFailJsonResponse('登录超时');
    }

    /**
     * [√测试通过]
     * 更新用户信息
     * @param Request $request
     * @return JsonResponse
     */
    public function updateInfo(Request $request)
    {
        $all = $request->all();

        $user = User::current();

        $validator = Validator::make($request->all(), $this->userValidator);

        if ($validator->fails())
            return StandardFailJsonResponse('字段验证不通过,请检查一下');

        $user->fill($all);
        $user->save();
        return StandardSuccessJsonResponse();
    }

}
