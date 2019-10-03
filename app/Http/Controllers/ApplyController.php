<?php

namespace App\Http\Controllers;

use App\User;
use App\Apply;
use App\Group;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class ApplyController extends Controller
{
    /**
     * 查询申请者列表
     * @return JsonResponse
     */
    public function getApplyList()
    {
        $groupId = User::current()->yx_group_id;
        $applyModels = Apply::where('apply_team_id', $groupId)->get();
        $userId = [];
        foreach ($applyModels as $applyModel) {
            $userId[] = $applyModel->apply_id;
        }

        $applyUsers = User::find($userId);
        return StandardJsonResponse(1, '请求成功', $applyUsers);
    }


    /**
     * 查询申请者数量
     * @return JsonResponse
     */
    public function getApplyCount()
    {
        $groupId = User::current()->yx_group_id;
        $applyModels = Apply::where('apply_team_id', $groupId)->count();
        return StandardJsonResponse(1, '请求成功', $applyModels);
    }

    /**
     * 申请入队
     * @param Request $request
     * @return JsonResponse
     */
    public function doApply(Request $request)
    {
        $user = User::current();
        $groupId = $request->get('groupId');
        $group = Group::where('id', $groupId)->first();

        if ($group->members >= $group->capacity)
            return StandardJsonResponse(-1, '该队伍已经满员');

        if ($group->is_submit)
            return StandardJsonResponse(-1, '该队伍已经锁定');

        if ($group->captain_id == $user->id)
            return StandardJsonResponse(-1, '这是你自己的队伍');


        Apply::create(['apply_team_id' => $groupId, 'apply_id' => $user->id]);


        // Todo:: notify Captain
        // Todo:: notify

        $user->state()->update(['state' => 2]);
        return StandardJsonResponse(1, '正在申请中');
    }

    /**
     * 撤回申请
     * @return JsonResponse
     */
    public function deleteApply()
    {
        $user = User::current();
        $apply_id = $user->id;
        if (!$apply = Apply::where('apply_id', $apply_id)->first())
            return StandardJsonResponse(-1, '你的申请已经处理');
        $user->state = 1;
        $apply->delete();
        return StandardJsonResponse(1, 'Success');
    }

    /**
     * 同意加入
     * @param Request $request
     * @return JsonResponse
     */
    public function agreeMember(Request $request)
    {
        $Group = User::current()->group()->first();
        if ($Group->members >= $Group->capacity)
            return StandardJsonResponse(-1, '队伍已经达到上限');

        $apply_id = $request->get('apply_id');
        $groupId = User::current()->yx_group_id;
        $user = User::where('id', $apply_id)->first();

        if ($user->state != 2)
            return StandardJsonResponse(-1, '该申请者已经撤回申请了');

        $user->addGroup($groupId);
        Apply::where('apply_id', $apply_id)->delete();

        // Todo : notify
        $group = $user->group()->first();


        return StandardJsonResponse(1, '同意成功');
    }

    /**
     * 拒绝加入
     * @param Request $request
     * @return JsonResponse
     */
    public function refuseMember(Request $request)
    {

        $apply_id = $request->get('apply_id');

        $user = User::where('id', $apply_id)->first();
        if ($user->state != 2) {
            return StandardJsonResponse(-1, '该申请者已经撤回申请了');
        }
        Apply::where('apply_id', $apply_id)->delete();
        $user->state = 1;
        // Todo:: notify
        return StandardJsonResponse(1, '拒绝成功');
    }

}
