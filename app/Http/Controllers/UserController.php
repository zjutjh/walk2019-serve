<?php

namespace App\Http\Controllers;

use App\User;
use App\Apply;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Database\QueryException;

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
     * [√测试通过]
     * 注册报名
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function register(Request $request)
    {
        $all = $request->all();
        //print($all);
        $openid = $request->session()->get('openid');

        if ($openid === null)
            return StandardJsonResponse("你还没有openid");

        $validator = Validator::make($request->all(), $this->userValidator);
        if ($validator->fails())
            return StandardJsonResponse(-1,"字段验证不通过");

        $user = new User();
        $user->openid = $openid;
        $user->fill($all);

        try{
            $user->save();
        } catch (QueryException $exception){
            return StandardJsonResponse(-1, "openid重复");
        }

        return StandardJsonResponse(1,"报名成功");
    }

        return template(1, '登录成功,请完善信息');
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
            return StandardFailJsonResponse();
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
            return StandardFailJsonResponse();

        $user->fill($all);
        $user->save();
        return StandardSuccessJsonResponse();
    }

}
