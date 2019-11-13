<?php

namespace App\Http\Controllers;

use App\Helpers\Verify_Code;
use App\User;
use App\Group;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

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
            return StandardFailJsonResponse($validator->errors().'字段验证不通过,请检查一下');

        $user->fill($all);
        $user->save();
        return StandardSuccessJsonResponse();
    }

    public function verify(Request $request){
        $all = $request->all();
        $validator = Validator::make($all, [
           'iid' => 'required|alpha_dash|size:18',
           'code' => 'required|integer|between:0,3'
        ]);

        if($validator->fails()){
            return StandardJsonResponse(-1, '字段验证失败');
        }

        $iid = $all['iid'];

        $user = User::where('id_card',encrypt_iid($iid))->get()->first();


        if($user === null) {
            return StandardJsonResponse(-1, '该用户不存在');
        }

        $group = Group::find($user->group_id);
        $data = [
            'user' => $user,
            'group' => $group
        ];

        if($group == null){
            return StandardJsonResponse(-1, '该用户现在还没有队伍', $data);
        }

        $code = $all['code'];

        if($code == Verify_Code::no){
            return StandardJsonResponse(-1, '该选项不可用');
        } else if($code == Verify_Code::start){
            if($user->verify_code == Verify_Code::complete || $user->verify_code == Verify_Code::fail){
                return StandardJsonResponse(-1, '该队伍已经结束毅行了', $data);
            }

            $user->verify_code = Verify_Code::start;
            $user->start_at = now();

        } else {
            if($user->verify_code == Verify_Code::no){
                return StandardJsonResponse(-1, '该队伍还没有出发，无法完成', $data);
            }
            $user->verify_code = $code;
            $user->end_at = now();
        }
        $user->save();


        return StandardJsonResponse(1, '刷卡成功', $data);
    }

}
