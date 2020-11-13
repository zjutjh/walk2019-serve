<?php

namespace App\Http\Controllers\Enroll;

use App\Helpers\UserState;
use App\Http\Controllers\Controller;
use App\Notifications\Wechat;
use App\User;
use App\Apply;
use App\Group;
use App\Helpers\WechatTemplate;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class ApplyController extends Controller
{
    /**
     * 查询申请者列表
     * @param Request $request
     * @return JsonResponse
     */
    public function getApplicantList(Request $request)
    {
        $groupId = User::current()->group_id;
        $pageSize = $request->get('page_size', 15);
        $apply_ids = Apply::where('apply_team_id', $groupId)->get('apply_id');

        $applyUsers = User::whereIn('id', $apply_ids)->paginate($pageSize);
        return StandardSuccessJsonResponse($applyUsers);
    }

    /**
     * 查询申请者数量
     * @return JsonResponse
     */
    public function getApplicantCount()
    {
        $groupId = User::current()->group_id;
        $applyCount = Apply::where('apply_team_id', $groupId)->get();
        return StandardSuccessJsonResponse($applyCount);
    }

    /**
     * 匹配入队
     * @param Request $request
     * @return JsonResponse
     */
    public function doMatching(Request $request)
    {
        $user = User::current();
        if ($user->group_id !== null)
            return StandardFailJsonResponse('你已经拥有队伍，无法匹配');

        $routeId = $request->get('route_id');
        if ($routeId === null)
            return StandardFailJsonResponse('参数错误');

        $groups = Group::where([['allow_matching', true], ['is_submit', false], ['route_id', $routeId]])->inRandomOrder()->get();
        $selectedGroup = null;
        foreach ($groups as $group)
            if ($group->members < $group->capacity) {
                $selectedGroup = $group;
                break;
            }

        if (!!!$selectedGroup)
            return StandardFailJsonResponse('哎呀，没有找到可以匹配的队伍');


        DB::transaction(function () use ($user, $selectedGroup) {
            $user->state = UserState::appling;
            $user->save();
            Apply::create(['apply_team_id' => $selectedGroup->id, 'apply_id' => $user->id]);
        });

        User::where('id', $selectedGroup->captain_id)->first()->notify(new Wechat(WechatTemplate::Apply));
        return StandardSuccessJsonResponse($selectedGroup);
    }

    /**
     * 申请入队
     * @param Request $request
     * @return JsonResponse
     */
    public function doApply(Request $request)
    {
        $groupId = $request->get('group_id');
        $group = Group::where('id', $groupId)->first();
        if (!$group)
            return StandardFailJsonResponse('该队伍已经解散');
        if ($group->members >= $group->capacity)
            return StandardFailJsonResponse('该队伍已经满员');
        if ($group->is_submit)
            return StandardFailJsonResponse('该队伍已经锁定');

        $user = User::current();

        if ($group->captain_id == $user->id)
            return StandardFailJsonResponse('这是你自己的队伍');
        if ($user->group_id !== null)
            return StandardFailJsonResponse('你已经拥有队伍，无法申请');

        DB::transaction(function () use ($user, $group) {
            $user->state = UserState::appling;
            $user->save();
            Apply::create(['apply_team_id' => $group->id, 'apply_id' => $user->id]);
        });

        User::where('id', $group->captain_id)->first()->notify(new Wechat(WechatTemplate::Apply));
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

        if ($user->state !== UserState::appling)
            return StandardFailJsonResponse('你的申请已经处理');

        $apply = Apply::where('apply_id', $user->id)->first();
        if ($apply) {
            $apply->delete();
        }
        $user->state = UserState::no_entered;
        $user->save();
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

        if (!$group || $user->id !== $group->captain_id)
            return StandardFailJsonResponse('你没有权限处理申请');
        if ($group->members >= $group->capacity)
            return StandardFailJsonResponse('队伍已经达到上限');
        $applyUser = User::where('id', $apply_id)->first();
        if ($applyUser->state !== UserState::appling)
            return StandardFailJsonResponse('该申请者已经撤回申请了');
        $apply = Apply::where('apply_id', $apply_id);
        if ($apply === null)
            return StandardFailJsonResponse('申请不存在');

        DB::transaction(function () use ($group, $apply, $applyUser) {
            $applyUser->group_id = $group->id;
            $applyUser->state = UserState::member;
            $applyUser->save();
            $apply->delete();
            $applyUser->notify(new Wechat(WechatTemplate::Agree));
        });

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

        if ($user->id !== $group->captain_id)
            return StandardSuccessJsonResponse('你没有权限处理申请');

        $apply_id = $request->get('apply_id');
        $applyUser = User::where('id', $apply_id)->first();

        if ($applyUser->state !== UserState::appling)
            return StandardSuccessJsonResponse('该申请者已经撤回申请了');

        $apply = Apply::where('apply_id', $apply_id);

        if ($apply === null)
            return StandardFailJsonResponse('申请不存在');


        DB::transaction(function () use ($apply, $applyUser) {
            $apply->delete();
            $applyUser->state = UserState::no_entered;
            $applyUser->save();
            $applyUser->notify(new Wechat(WechatTemplate::Refuse));
        });
        return StandardSuccessJsonResponse();


    }

}
