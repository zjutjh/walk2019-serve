<?php

namespace App\Http\Controllers;

use App\User;
use App\Group;
use Exception;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class GroupController extends Controller
{

    /**
     * 队伍列表
     * @param Request $request
     * @return JsonResponse
     */
    public function groupLists(Request $request)
    {
        $groupListInfo = $request->all();
        $pageSize = $groupListInfo['page_size']!==null ? $groupListInfo['page_size'] : 15;
        $groups = Group::orderBy('id', 'desc')->paginate($pageSize);
        return StandardJsonResponse(1, 'Success', $groups);
    }

    /**
     * 搜索队伍
     * @param Request $request
     * @return JsonResponse
     */
    public function searchTeam(Request $request)
    {
        $query_string = $request->get('query_string');
        if (!$query_string)
            return $this->groupLists();

        $pageSize = $request->get('page_size')!==null ? $request->get('page_size') : 15;
        $groups = Group::where('name', 'like', "%{$query_string}%")->orWhere('id', $query_string)->paginate($pageSize);
        return StandardJsonResponse(1, '搜索成功', $groups);
    }

    /**
     * 查询队伍信息
     * @return JsonResponse
     */
    public function getGroupInfo()
    {
        $user = User::current();
        $group = $user->group()->first();
        return StandardJsonResponse(1, 'Success', $group);
    }

    /**
     * 获取队伍成员信息
     * @return JsonResponse
     */
    public function getGroupMembers()
    {
        $user = User::current();
        $members = $user->group()->first()->members()->get();
        return StandardJsonResponse(1, 'Success', $members);
    }

    /**
     * 创建队伍
     * @param Request $request
     * @return JsonResponse
     */
    public function createGroup(Request $request)
    {
        $groupInfo = $request->all();
        $user = User::current();

        // Todo:: 验证
        if (!!$user->group_id)
            return StandardJsonResponse(-1, '你已经拥有队伍');

        $groupInfo['captain_id'] = $user->id;
        $group = Group::create($groupInfo);
        $user->group_id = $group->id;
        $user->state = 3;
        $user->save();
        // Todo:: notify

        return StandardJsonResponse(1, '创建成功');
    }


    public function updateGroupInfo(Request $request)
    {
        $user = User::current();
        $groupInfo = $request->all();
        // Todo:: vat
        $Group = Group::where('captain_id', $user->id)->first();
        if ($Group !== null) {
            $Group->fill($groupInfo);
            $Group->save();
            return StandardJsonResponse(1, 'Success');
        }
        return StandardFailJsonResponse();
    }


    /**
     * 解散队伍
     * @param Request $request
     * @return JsonResponse
     * @throws Exception
     */
    public function breakGroup(Request $request)
    {
        $user = User::current();
        $group = $user->group()->first();
        if ($user->id !== $group->captain_id) {
            return StandardJsonResponse(-1, '你没有权限删除队伍');
        }
        $group->delete();
        // TODO: notify
        return StandardJsonResponse(1, 'Success');
    }

    /**
     * 离开队伍
     * @param Request $request
     * @return JsonResponse
     */
    public function leaveGroup(Request $request)
    {
        $user = User::current();
        $group = $user->group()->first();
        if ($group->is_sumbit)
            return StandardFailJsonResponse();
        $user->leaveGroup();
        // TODO: notify
        return StandardJsonResponse(1, 'Success');
    }


    /**
     * 锁定队伍
     * @param Request $request
     * @return JsonResponse
     */
    public function submitGroup(Request $request)
    {
        $user = User::current();
        if ($user->state != 3)
            return StandardJsonResponse(-1, '你没有权限');

        $group = $user->group()->first();
        if ($group->members < 4) //判断人数是否达到要求
            return StandardJsonResponse(-1, "只有到达4人才可以锁定哦");

        $max_team_num = 0;
        if ($group->select_route === "屏峰小和山全程毅行")
            $max_team_num = config('PF_Full_Max');
        else if ($group->select_route === "屏峰小和山半程毅行")
            $max_team_num = config('PF_Half_Max');
        else if ($group->select_route === "朝晖京杭大运河毅行")
            $max_team_num = config('ZH_Full_Max');

        if (Group::where('select_route', $group->select_route)->where('is_submit', true)->count() >= $max_team_num)
            return StandardJsonResponse(-1, '今日队伍已满');

        $group->is_submit = true;
        $group->save();

        // Todo:: notify

        return StandardJsonResponse(1, '已经锁定队伍');
    }

    /**
     * 解锁队伍
     * @param Request $request
     * @return JsonResponse
     */
    public
    function unSubmitGroup(Request $request)
    {
        $user = User::current();
        $group = $user->group()->first();
        if ($user->id === $group->captain_id) {
            $group = $user->group()->first();
            $group->is_lock = false;
            $group->save();
            // Todo:: notify
            return StandardSuccessJsonResponse();
        }
        return StandardFailJsonResponse();
    }


    /**
     * 踢出队伍
     * @param Request $request
     * @return JsonResponse
     */
    public
    function deleteMember(Request $request)
    {
        $user = User::current();
        $delete_id = $request->get('user_id');
        $members = $user->group()->first()->members()->get();

        // Todo:: notify
        return StandardSuccessJsonResponse();
    }

}
