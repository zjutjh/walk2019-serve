<?php

namespace App\Http\Controllers;

use App\User;
use App\Apply;
use App\Group;
use App\WalkPath;
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
        $applyCount = Apply::where('apply_team_id', $groupId)->count();
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
            return StandardJsonResponse(-1, '该队伍已经满员');
        elseif ($group->is_submit)
            return StandardJsonResponse(-1, '该队伍已经锁定');
        elseif ($group->captain_id == $user->id)
            return StandardJsonResponse(-1, '这是你自己的队伍');
        else {
            $walkPath = WalkPath::find($group->walk_path_id);
            if(!$walkPath->supportCampus($user->campus)){
                return StandardJsonResponse(-1, '该线路不支持'
                    .config('info.campus.'.$user->campus.'')
                    .'校区的同学参加');
            }
        }


        notify(_notify::apply, $groupId);

        $apply = Apply::addOne($user->id, $groupId);
        if (is_null($apply)) {
            return StandardJsonResponse(-1, '你已经拥有队伍，无法申请');
        } else {
            return StandardJsonResponse(1, '正在申请中');
        }

    }

    /**
     * 撤回申请
     * @return JsonResponse
     */
    public function deleteApply(Request $request)
    {
        $user = User::current();
        $apply_id = $user->id;
        $apply_team_id = $request->get('apply_team_id');
        if (!$apply = Apply::where('apply_id', $apply_id)->first())
            return StandardJsonResponse(-1, '你的申请已经处理');
        
        
        notify(_notify::apply_cancel_user, $user->id);

        Apply::removeOne($apply_id, $apply_team_id);
        
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
        
        if($user->id != $group->captain_id){
            return StandardJsonResponse(-1, '你没有权限处理申请');
        }

        if ($group->members >= $group->capacity)
            return StandardJsonResponse(-1, '队伍已经达到上限');

        $apply_id = $request->get('apply_id');
        $groupId = User::current()->group_id;
        $applyUser = User::find($apply_id);

        if ($applyUser->state == _state::appling){
            return StandardJsonResponse(-1, '该申请者已经撤回申请了');
        }

        Apply::removeAll($apply_id);

        notify(_notify::apply_agree, $apply_id);

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
        if ($applyUser->state != _state::appling) {
            return StandardJsonResponse(-1, '该申请者已经撤回申请了');
        }
        Apply::removeOne($apply_id, $group->id);
        
        notify(_notify::apply_cancel_captain, $apply_id);

        return StandardJsonResponse(1, '拒绝成功');
    }

}
