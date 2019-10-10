<?php

namespace App\Http\Controllers;

use App\User;
use App\Apply;
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
        'wx_id' => 'alpha_dash|between:2,100',
        'identity' => 'required',
        'height' => 'integer|between:50,300',
        'sid' => 'required_if:identity,学生|digits_between:10,14',
        'school' => 'required_if:identity,学生'
    ];

    /**
     * 注册报名
     * @param Request $request
     * @return JsonResponse
     */
    public function register(Request $request)
    {
        $all = $request->all();
        $openid = $request->session()->get('openid');

        if ($openid === null)
            return StandardFailJsonResponse();

        //TODO: 表单验证

        $validator = Validator::make($request->all(), $this->userValidator);

        if ($validator->fails())
            return StandardFailJsonResponse();

        $user = new User();
        $user->openid = $openid;

        $user->fill($all);

        $user->save();
        return StandardSuccessJsonResponse();
    }


    /**
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
            return StandardFailJsonResponse();
    }

    /**
     * 更新用户信息
     * @param Request $request
     * @return JsonResponse
     */
    public function updateInfo(Request $request)
    {
        $UserInfo = $request->all();
        $user = User::current();

        $validator = Validator::make($request->all(), $this->userValidator);

        if ($validator->fails())
            return StandardFailJsonResponse();


        $user->fill($UserInfo);
        $user->save();
        return StandardSuccessJsonResponse();
    }

    /**
     * 解除绑定
     * @param Request $request
     * @return JsonResponse
     */
    public function dismiss(Request $request)
    {
        $user = User::current();
        $group = $user->group()->first();
        if ($group->captain_id === $user->id) {
            $group->dismiss();
        } else {
            $user->leaveGroup();
        }

        Apply::removeAll($user->id);
        $user->openid = null;
        $user->save();

        return StandardSuccessJsonResponse();
    }

}
