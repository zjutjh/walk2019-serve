<?php

namespace App\Http\Controllers;

use App\Helpers\State;
use App\Notifications\Wechat;
use App\User;
use App\Apply;
use App\Group;
use App\WalkRoute;
use App\WxTemplate;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class ApplyController extends Controller
{
    /**
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
     * 查询申请者数量
     * @return JsonResponse
     */
    public function getApplyCount()
    {
        $groupId = User::current()->group_id;
        $applyCount = Apply::where('apply_team_id', $groupId)->get();
        return StandardJsonResponse(1, '请求成功', $applyCount);
    }

    /**
     * 申请入队
     * @param Request $request
     * @return JsonResponse
     */
    public function doApply(Request $request)
    {
        $user = User::current();
        $groupId = $request->get('group_id');
        $group = Group::find($groupId)->first();

        if ($group->members >= $group->capacity)
            return StandardFailJsonResponse('该队伍已经满员');
        else if ($group->is_submit)
            return StandardFailJsonResponse('该队伍已经锁定');
        else if ($group->captain_id == $user->id)
            return StandardFailJsonResponse('这是你自己的队伍');

        $apply = Apply::add($user->id, $groupId);


        if (is_null($apply)) {
            return StandardJsonResponse(-1, '你已经拥有队伍，无法申请');
        } else {
            User::find($group->captain_id)->first()->notify(new Wechat(WxTemplate::Apply));
            return StandardJsonResponse(1, '正在申请中');
        }

    }

    /**
     * 撤回申请
     * @param Request $request
     * @return JsonResponse
     */
    public function deleteApply(Request $request)
    {
        $user = User::current();
        $apply_id = $user->id;
        if (!$apply = Apply::where('apply_id', $apply_id)->first())
            return StandardJsonResponse(-1, '你的申请已经处理');

        Apply::removeOne($apply_id);
        return StandardJsonResponse(1, 'Success');
    }

    /**
     * 同意加入
     * @param Request $request
     * @return JsonResponse
     */
    public function agreeMember(Request $request)
    {
        $user = User::current();
        $group = $user->group()->first();

        if ($user->id != $group->captain_id)
            return StandardJsonResponse(-1, '你没有权限处理申请');

        if ($group->members >= $group->capacity)
            return StandardJsonResponse(-1, '队伍已经达到上限');

        $apply_id = $request->get('apply_id');
        $groupId = User::current()->group_id;
        $applyUser = User::find($apply_id);


        if ($applyUser->state !== State::appling) {
            return StandardJsonResponse(-1, '该申请者已经撤回申请了');
        }
        $applyUser->group_id = $groupId;
        $applyUser->state = State::member;

        Apply::removeAll($apply_id);

        User::find($apply_id)->first()->notify(new Wechat(WxTemplate::Agree));

        return StandardJsonResponse(1, '同意成功');
    }

    /**
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

        $applyUser = User::where('id', $apply_id)->first();
        if ($applyUser->state != State::appling) {
            return StandardJsonResponse(-1, '该申请者已经撤回申请了');
        }
        Apply::removeOne($apply_id, $group->id);

        User::find($apply_id)->first()->notify(new Wechat(WxTemplate::Refuse));
        return StandardJsonResponse(1, '拒绝成功');
    }

}
