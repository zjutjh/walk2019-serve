<?php

namespace App\Http\Controllers;


use App\Group;
use App\Helpers\VerifyCode;
use App\User;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class IdentityCardVerifyController extends Controller
{

    public function verifyInfo(Request $request)
    {
        $all = $request->all();
        $validator = Validator::make($all, [
            'iid' => 'required|alpha_dash',
            'code' => 'required|integer|between:0,3'
        ]);

        if ($validator->fails())
            return StandardJsonResponse(-1, '字段验证失败');

        $iid = $all['iid'];
        $user = User::where('id_card', encrypt_iid($iid))->get()->first();

        if ($user === null)
            return StandardJsonResponse(-1, '该用户不存在');

        $group = Group::find($user->group_id);
        $data = [
            'user' => $user,
            'group' => $group
        ];

        if ($group == null)
            return StandardJsonResponse(-1, '该用户现在还没有队伍', $data);

        if ($group->is_submit == 0)
            return StandardJsonResponse(-1, '该队伍没有报名', $data);

        $code = $all['code'];

        if ($code == VerifyCode::no)
            return StandardJsonResponse(-1, '该选项不可用');

        else if ($code == VerifyCode::start) {

            if ($user->verify_code == VerifyCode::complete || $user->verify_code == VerifyCode::fail)
                return StandardJsonResponse(-1, '该队伍已经结束毅行了', $data);


            $user->verify_code = VerifyCode::start;
            $user->start_at = now();
        } else {
            if ($user->verify_code == VerifyCode::no)
                return StandardJsonResponse(-1, '该队伍还没有出发，无法完成', $data);

            $user->verify_code = $code;
            $user->end_at = now();
        }
        $user->save();


        return StandardJsonResponse(1, '刷卡成功', $data);
    }


}
