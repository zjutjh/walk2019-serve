<?php

namespace App\Http\Controllers\Enroll;

use App\Http\Controllers\Controller;
use App\User;
use App\Group;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Validator;

class UserController extends Controller
{

    private $userValidator = [
        'name' => 'required|between:2,32',
        'email' => 'required|email',
        'phone' => 'required|digits:11',
        'id_card' => 'required|alpha_dash|between:8,20',
        'qq' => 'digits_between:4,12',
        'identity' => 'required',
        'height' => 'integer|between:50,300',
        'sid' => 'exclude_if:identity,学生|required|alpha_dash|between:10,14',
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

        $user = new User();
        $user->openid = $openid;

        $user->fill($all);
        $user->save();

        return StandardSuccessJsonResponse('注册成功');
    }


    /**
     * [√测试通过]
     * 获得当前用户信息
     * @param Request $request
     * @return JsonResponse
     */
    public function getUserInfo(Request $request)
    {
        $user = User::current();
        if ($user) return StandardSuccessJsonResponse($user);
        return StandardFailJsonResponse('登录失败');
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
        $validator = Validator::make($all, $this->userValidator);

        if ($validator->fails())
            return StandardFailJsonResponse($validator->errors() . '字段验证不通过,请检查一下');

        $user = User::current();
        $user->fill($all);
        $user->save();
        return StandardSuccessJsonResponse("修改成功");
    }


}
