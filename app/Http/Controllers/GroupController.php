<?php

namespace App\Http\Controllers;

use App\User;
use App\Group;
use App\WalkPath;
use App\SignupTime;
use App\Helpers\_state;
use App\WxTemplate;
use Exception;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

use App\Helpers\_notify;

class GroupController extends Controller
{

    /**
     * 队伍列表
     * @param Request $request
     * @return JsonResponse
     */
    public function groupLists(Request $request)
    {
        //TODO: 表单验证

        $pageSize = $request->get('page_size', 15);
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
        //TODO: 表单验证

        $query_string = $request->get('query_string');
        if (!$query_string)
            return $this->groupLists($request);

        $pageSize = $request->get('page_size', 15);
        $groups = Group::orWhere('id', $query_string)->where('name', 'like', "%{$query_string}%")->paginate($pageSize);
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
        $all = $request->all();
        $user = User::current();

        //TODO: 表单验证
        //我们暂时假设提交的数据都是正确的

        if (!!$user->group_id)
            return StandardJsonResponse(-1, '你已经拥有队伍');

        $all['captain_id'] = $user->id;
        $group = Group::create($all);
        $user->group_id = $group->id;
        $user->state = _state::captain;
        $user->save();

        notify(_notify::create, $group->id);

        return StandardJsonResponse(1, '创建成功');
    }


    public function updateGroupInfo(Request $request)
    {
        $user = User::current();
        $all = $request->all();
        $group = Group::where('captain_id', $user->id)->first();

        // TODO: 表单验证

        if ($group !== null) {
            // 验证1: 队伍锁定状态验证
            // 验证2: 人数验证
            // 验证3: 校区验证
            $memberCount = $group->members()->count();
            $walkPath = WalkPath::find('$all->walk_path_id');
            if ($group->is_submit === true){
                //TODO: 给{锁定}取一个好听的名字
                return StandardJsonResponse(-1, '队伍已锁定');
            }
            elseif ($memberCount > $all->capacity) {
                return StandardJsonResponse(-1, '队伍人数超过容量');
            } elseif (!$group->supportWalkPath($walkPath))
            {
                // 2019年可以不管这条语句
                $campusOK = $walkPath->campus;
                return StandardJsonResponse(-1, '此条线路仅供'.config('info.campus')[$campusOK].'校区参加');
            }

            $group->fill($all);
            $group->save();

            notify(_notify::update, $group->id);

            return StandardJsonResponse(1, 'Success');
        }
        return StandardJsonResponse(-1, '你没有权限修改队伍信息');
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
        $group_id = $group->id;
        if ($user->id !== $group->captain_id) {
            return StandardJsonResponse(-1, '你没有权限解散队伍');
        }

        /**
         * 先提醒，再删除
         */
        notify(_notify::dismiss, $group_id);

        $group->dismiss();

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
        //if ($group->is_sumbit)
        //校验，自己是否是队长

        if($group->captain_id === $user->id){
            return StandardJsonResponse(-1, '你是队长，不能离开队伍');
        }

        $user->leaveGroup();

        notify(_notify::leave, $user->id);

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
        if ($user->state != _state::captain)
            return StandardJsonResponse(-1, '你没有权限锁定队伍');

        $group = $user->group()->first();
        //校验: 人数达到需求
        $leastMembersCount = config('info.members_count.least');
        if ($group->members < $leastMembersCount) //判断人数是否达到要求
            return StandardJsonResponse(-1, '只有到达'.$leastMembersCount.'人才可以锁定哦');
        //校验: 当前报名未满
        $signupConfig = SignupTime::caculateCurrentConfig();
        if($signupConfig-> remain <= 0){
            return StandardJsonResponse(-1, '当前报名队伍已满');
        }
        //校验: 当前报名的线路是否还有余量
        $walkPath = WalkPath::find($group->route_id);
        $remainCountOfPathAndTime =
            $walkPath->capacityGroupCountOfWalkTime($this->walk_time_id)
             - $walkPath->submitGroupCountOfWalkTime($this->walk_time_id);

        // 不再使用不稳定，需要跑数据库的设置了
        // $max_team_num = 0;
        // if ($group->select_route === "屏峰小和山全程毅行")
        //     $max_team_num = config('PF_Full_Max');
        // else if ($group->select_route === "屏峰小和山半程毅行")
        //     $max_team_num = config('PF_Half_Max');
        // else if ($group->select_route === "朝晖京杭大运河毅行")
        //     $max_team_num = config('ZH_Full_Max');

        // if (Group::where('select_route', $group->select_route)->where('is_submit', true)->count() >= $max_team_num)
        //     return StandardJsonResponse(-1, '今日队伍已满');

        $group->is_submit = true;
        $group->save();

        notify(_notify::submit, $group->id);

        return StandardJsonResponse(1, '已经锁定队伍');
    }

    /**
     * 解锁队伍
     * @param Request $request
     * @return JsonResponse
     */
    public function unSubmitGroup(Request $request)
    {
        $user = User::current();
        $group = $user->group()->first();
        if ($user->id === $group->captain_id) {
            $group = $user->group()->first();
            $group->is_lock = false;
            $group->save();

            notify(_notify::unsubmit, $group->id);

            return StandardSuccessJsonResponse();
        }
        return StandardJsonResponse(-1, '你没有权限解锁队伍');
    }


    /**
     * 踢出队伍
     * @param Request $request
     * @return JsonResponse
     */
    public function deleteMember(Request $request)
    {
        $user = User::current();
        $delete_id = $request->get('user_id');
        $group = $user->group()->first();
        $members = $group->members()->get();

        //校验1: 队伍是否锁定
        //校验2: 自己是否时队长
        if ($group->is_submit) {
            return StandardJsonResponse(-1, '已锁定队伍');
        } elseif ($group->captain_id === $delete_id) {
            return StandardJsonResponse(-1, '你是队长，不能踢自己');
        }

        $deleteUser = User::find($delete_id);
        notify(_const::throwed, [$delete_id, $group_id]);
        $deleteUser->leaveGroup();

        return StandardSuccessJsonResponse();
    }

}
