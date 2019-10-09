<?php

namespace App\Http\Controllers;

use App\User;
use App\Apply;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Facades\Validator;
use Auth;

class UserController extends Controller
{

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
        if (Validator::make($all, [
            'name' => 'required|between:2,32',
            'email' => 'required|email',
            'phone' => 'required|digits:11',
            'id_card' => 'required|alpha_dash|size:18',
            'qq' => 'digits_between:5,12',
            'wx_id' => 'alpha_dash|between:2,100',
            'phone' => 'required|digits:11',
            'identity' => 'required',
            'height' => 'integer|between:50-300',
            'sid' => 'required_if:identity,学生|digits_between:10-14',
            'school' => 'required_if:identity,学生'  
        ], function() {
            if (!in_array($all->identity, config('info.identity'))){
                return false;
            } elseif (!checkIid($all->id_card)){
                return false;
            }
            if ($all->identity == '学生'){
                if (!in_array($all->school, mapdic(config('info.school', function($key, $value){
                    return $key;
                })))) {
                    return false;
                } elseif(!in_array($all->campus, config('info.campus'))){
                    return false;
                }
            }
            return true;
        })->fails()){
            return StandardFailJsonResponse();
        }

 
        
        $user = new User();
        $user->openid = $openid;

        $user->fill($all);
        //TODO: what is User::setIdCardAttribute?
        $user->setIdCardAttribute($all->id_card);
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
        
        //TODO: 表单验证

        $user->fill($UserInfo);
        $user->save();
        return StandardSuccessJsonResponse();
    }

    /**
     * 解除绑定
     * @param Request $request
     * @return JsonResponse
     */
    public function dismiss(Request $request){
        $user = User::current();
        $group = $user->group()->first();
        if($group->captain_id === $user->id){
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
