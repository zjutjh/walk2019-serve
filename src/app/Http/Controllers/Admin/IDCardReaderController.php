<?php

namespace App\Http\Controllers\Admin;

use App\Exports\GroupExport;
use App\Exports\UsersExport;
use App\Group;
use App\Http\Controllers\Controller;
use App\IDCardReaderRecode;
use App\Notifications\Wechat;
use App\User;
use App\WalkRoute;
use App\Helpers\WechatTemplate;
use DateInterval;
use DateTime;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Maatwebsite\Excel\Facades\Excel;

class IDCardReaderController extends Controller
{
    public function getUserInfo(Request $request)
    {
        $idCardNumber = $request->get("idcard");
        $user = User::where('id_card', $idCardNumber)->get()->first();
        if ($user === null)
            return StandardJsonResponse(-1, '该用户不存在');

        return StandardSuccessJsonResponse($user);
    }
    public function getIDCardGroupInfo(Request $request)
    {
        $idCardNumber = $request->get("idcard");
        $mode = $request->get("mode");
        $user = User::where('id_card', $idCardNumber)->get()->first();
        if ($user === null)
            return StandardJsonResponse(-1, '该用户不存在');

        $group = Group::find($user->group_id);
        if ($group == null)
            return StandardJsonResponse(-1, '该用户现在还没有队伍', null);

        if ($group->is_submit == 0)
            return StandardJsonResponse(-1, '该队伍没有成功报名', null);

        $members = $group->members()->get();
        $response = array();
        $new_mems = array();
        foreach ($members as $m) {
            $mem = array();
            $mem['name'] = $m->name;
            $mem['state'] = IDCardReaderRecode::where([['idcard', $m->id_card], ['mode', $mode]])->count();
            $mem['logo'] = $m->logo;
            array_push($new_mems, $mem);
        }
        $response['member_list'] = $new_mems;
        $response['name']=$group->name;
        $response['route']=$group->route;
        $response['groupId']=$group->No;

        return StandardSuccessJsonResponse($response);
    }

    public function recodeIDCard(Request $request)
    {
        $idCardNumber = $request->get("idcard");
        $mode = $request->get("mode");
        $user = User::where('id_card', $idCardNumber)->get()->first();
        if ($user === null)
            return StandardJsonResponse(-1, '该用户不存在');

        $group = Group::find($user->group_id);
        if ($group == null)
            return StandardJsonResponse(-1, '该用户现在还没有队伍', null);

        if ($group->is_submit == 0)
            return StandardJsonResponse(-1, '该队伍没有成功报名', null);

        $recode = IDCardReaderRecode::where([['idcard', $idCardNumber], ['mode', $mode]])->first();
        if (!$recode && $request->get("mode") === 0) {
            IDCardReaderRecode::create($request->all());
        } else if ($request->get("mode") !== 0) {
            return StandardJsonResponse(-1, '该成员没有出发', null);
        } else {
            $recode->fill($request->all());
        }

        return StandardSuccessJsonResponse();
    }
}
