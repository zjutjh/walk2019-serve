<?php

namespace App\Http\Controllers;

use App\Helpers\State;
use App\Notifications\Wechat;
use App\User;
use App\Apply;
use App\Group;
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
        foreach ($applies as $apply)
            $userId[] = $apply->apply_id;

        $pageSize = $request->get('page_size', 15);
        $applyUsers = User::where('id', $userId);
        return StandardSuccessJsonResponse( $applyUsers);
    }

    /**
     * 查询申请者数量
     * @return JsonResponse
     */
    public function getApplyCount()
    {
        $groupId = User::current()->group_id;
        $applyCount = Apply::where('apply_team_id', $groupId)->get();
        return StandardSuccessJsonResponse($applyCount);
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
        $group = Group::where('id', $groupId)->first();
        if (!$group)
            return StandardFailJsonResponse('该队伍已经解散');
        if ($group->members >= $group->capacity)
            return StandardFailJsonResponse('该队伍已经满员');
        if ($group->is_submit)
            return StandardFailJsonResponse('该队伍已经锁定');
        if ($group->captain_id == $user->id)
            return StandardFailJsonResponse('这是你自己的队伍');
        if ($user->group_id !== null)
            return StandardFailJsonResponse('你已经拥有队伍，无法申请');

        Apply::create(['apply_team_id' => $groupId, 'apply_id' => $user->id]);

        $user->state = State::appling;
        $user->save();

        User::where('id', $group->captain_id)->first()->notify(new Wechat(WxTemplate::Apply));
        return StandardSuccessJsonResponse();
    }

    /**
     * 撤回申请
     * @param Request $request
     * @return JsonResponse
     */
    public function deleteApply(Request $request)
    {
        $user = User::current();
        $apply = Apply::where('apply_id', $user->id)->first();
        if ($apply === null)
            return StandardFailJsonResponse('你的申请已经处理');

        $apply->delete();
        return StandardSuccessJsonResponse();
    }

    /**
     * 同意加入
     * @param Request $request
     * @return JsonResponse
     */
    public function agreeMember(Request $request)
    {
        $user = User::current();
        $group = $user->group();
        $apply_id = $request->get('apply_id');

        if (!$group||$user->id !== $group->captain_id)
            return StandardFailJsonResponse('你没有权限处理申请');
        if ($group->members >= $group->capacity)
            return StandardFailJsonResponse('队伍已经达到上限');
        $applyUser = User::where('id', $apply_id)->first();
        if ($applyUser->state !== State::appling)
            return StandardFailJsonResponse('该申请者已经撤回申请了');
        $apply = Apply::where('apply_id', $apply_id);
        if ($apply === null)
            return StandardFailJsonResponse('申请不存在');

        $applyUser->group_id = $group->id;
        $applyUser->state = State::member;
        $applyUser->save();
        $applyUser->notify(new Wechat(WxTemplate::Agree));

        $apply->delete();
        return StandardSuccessJsonResponse();
    }

    /**
     * 拒绝加入
     * @param Request $request
     * @return JsonResponse
     */
    public function refuseMember(Request $request)
    {
        $user = User::current();
        $group = $user->group();

        if ($user->id != $group->captain_id)
            return StandardSuccessJsonResponse('你没有权限处理申请');

        $apply_id = $request->get('apply_id');
        $applyUser = User::where('id', $apply_id)->first();
        if ($applyUser->state != State::appling)
            return StandardSuccessJsonResponse('该申请者已经撤回申请了');

        $apply = Apply::where('apply_id', $apply_id);
        if ($apply === null)
            return StandardFailJsonResponse('申请不存在');

        $apply->delete();

        $applyUser->state = State::no_entered;
        $applyUser->save();
        $applyUser->notify(new Wechat(WxTemplate::Refuse));
        return StandardSuccessJsonResponse();
    }

}
