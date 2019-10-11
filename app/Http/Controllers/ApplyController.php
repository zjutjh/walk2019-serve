<?php

namespace App\Http\Controllers;

use App\Helpers\_State;
use App\User;
use App\Apply;
use App\Group;
use App\WalkRoute;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class ApplyController extends Controller
{
    /**
     * [√通过测试]
     * 查询申请者列表
     * @param Request $request
     * @return JsonResponse
     */
    public function getApplyList(Request $request)
    {
        $groupId = User::current()->group_id;
        $applies = Apply::where('apply_team_id', $groupId)->get();
        $userId = [];
        foreach ($applies as $apply) {
            $userId[] = $apply->apply_id;
        }
        $pageSize = $request->get('page_size', 15);
        $applyUsers = User::find($userId);
        return StandardJsonResponse(1, '请求成功', $applyUsers);
    }


    /**
     * [√通过测试]
     * 查询申请者数量
     * @return JsonResponse
     */
    public function getApplyCount()
    {
        $groupId = User::current()->group_id;
        $applies = Apply::where('apply_team_id', $groupId)->get();
        if($applies !== null){
            $applyCount = $applies->count();
        }
        echo $applyCount;
        return StandardJsonResponse(1, '请求成功', $applyCount);
    }

    /**
     * [√通过测试]
     * 申请入队
     * @param Request $request
     * @return JsonResponse
     */
    public function doApply(Request $request)
    {
        $validator = \Validator::make($request->all(),[
           'group_id' => 'required|integer'
        ]);
        if($validator->fails()) {
            return StandardJsonResponse(-1,'表单验证失败');
        }
        $user = User::current();
        $groupId = $request->get('group_id');
        $group = Group::find($groupId)->first();
        if ($group == null)
            return StandardJsonResponse(-1,'该队伍不存在');
        elseif ($group->is_submit)
            return StandardJsonResponse(-1, '该队伍已经锁定');
        elseif ($group->members >= $group->capacity)
            return StandardJsonResponse(-1, '该队伍已经满员');
        elseif ($group->captain_id === $user->id)
            return StandardJsonResponse(-1, '这是你自己的队伍');

        //notify(_notify::apply, $groupId);

        $apply = Apply::addOne($user->id, $groupId);
        if ($apply === 0)
            return StandardJsonResponse(-1, '你已经拥有队伍，无法申请');
        elseif ($apply === 1)
            return StandardJsonResponse(-1,'不能重复申请');
        else
            return StandardJsonResponse(1, '正在申请中');
    }

    /**
     * [√]通过测试
     * 撤回申请
     * @return JsonResponse
     */
    public function deleteApply(Request $request)
    {
        $user = User::current();
        $apply_id = $user->id;
        $apply_team_id = $request->get('apply_team_id');

        $apply = Apply::where('apply_id', $apply_id)->where('apply_team_id', $apply_team_id)->first();
        if ($apply === null)
            return StandardJsonResponse(-1, '你的申请已经处理');

        //notify(_notify::apply_cancel_user, $user->id);

        Apply::removeOne($apply_id, $apply_team_id);

        return StandardJsonResponse(1, '撤回成功');
    }

    /**
     * [√通过测试]
     * 同意加入
     * @param Request $request
     * @return JsonResponse
     */
    public function agreeMember(Request $request)
    {
        $user = User::current();
        $group = $user->group()->first();

        if($user->id != $group->captain_id){
            return StandardJsonResponse(-1, '你没有权限处理申请');
        }
        if ($group->members >= $group->capacity)
            return StandardJsonResponse(-1, '队伍已经达到上限');

        $apply_id = $request->get('apply_id');
        $applyUser = User::find($apply_id);

        if($applyUser->state !== _State::appling){
            return StandardJsonResponse(-1,'该申请者已经撤回申请了');
        }

        $applyUser->group_id = $group->id;
        $applyUser->state = _State::member;
        $applyUser->save();

        Apply::removeAll($apply_id);


        //notify(_notify::apply_agree, $apply_id);

        return StandardJsonResponse(1, '同意成功');
    }

    /**
     * [√通过测试]
     * 拒绝加入
     * @param Request $request
     * @return JsonResponse
     */
    public function refuseMember(Request $request)
    {
        $user = User::current();
        $group = $user->group()->first();

        if ($user->id != $group->captain_id) {
            return StandardJsonResponse(-1, '你没有权限处理申请');
        }

        $apply_id = $request->get('apply_id');
        $applyUser = User::find($apply_id);

        if ($applyUser->state !== _State::appling) {
            return StandardJsonResponse(-1, '该申请者已经撤回申请了');
        }
        Apply::removeOne($apply_id, $group->id);

        //notify(_notify::apply_cancel_captain, $apply_id);

        return StandardJsonResponse(1, '拒绝成功');
    }

}
