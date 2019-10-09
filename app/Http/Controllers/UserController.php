<?php

namespace App\Http\Controllers;

use App\User;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class UserController extends Controller
{

    /**
     * 注册报名
     * @param Request $request
     * @return JsonResponse
     */
    public function register(Request $request)
    {
        $UserInfo = $request->all();
        $openid = $request->session()->get('openid');

        $user = new User();
        $user->openid = $openid;

        $user->fill($UserInfo);
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
        $user->fill($UserInfo);
        $user->save();
        return StandardSuccessJsonResponse();
    }


}
