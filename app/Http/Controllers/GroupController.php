<?php

namespace App\Http\Controllers;

use App\Helpers\State;
use App\SubmitTime;
use App\User;
use App\Group;
use App\WalkRoute;
use Exception;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

use Illuminate\Support\Facades\Validator;

class GroupController extends Controller
{

    /**
     * 队伍列表
     * @param Request $request
     * @return JsonResponse
     */
    public function groupLists(Request $request)
    {
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
        $query_string = $request->get('query_string');
        if (!$query_string)
            return $this->groupLists($request);

        $pageSize = $request->get('page_size', 15);
        $groups = Group::where('id', $query_string)->where('name', 'like', "%{$query_string}%")->paginate($pageSize);
        return StandardSuccessJsonResponse($groups);
    }

    /**
     * [√通过测试]
     * 查询队伍信息
     * @return JsonResponse
     */
    public function getGroupInfo()
    {
        $user = User::current();

        if ($user->group_id === null) {
            return StandardFailJsonResponse('你还没有加入');
        }
        $group = $user->group()->first();
        $group['route'] = WalkRoute::find($group['route_id'])->name;
        unset($group['route_id']);

        return StandardJsonResponse(1, 'Success', $group);
    }

    /**
     * [√通过测试]
     * 获取队伍成员信息
     * @return JsonResponse
     */
    public function getGroupMembers()
    {
        $user = User::current();
        if ($user->group_id === null) {
            return StandardFailJsonResponse('你还没有加入');
        }
        $members = $user->group()->first()->members()->get();
        return StandardJsonResponse(1, 'Success', $members);
    }

    /**
     * [√通过测试]
     * 创建队伍
     * @param Request $request
     * @return JsonResponse
     */
    public function createGroup(Request $request)
    {
        $all = $request->all();


        $validator = Validator::make($all, [
            'name' => 'required',
            'capacity' => 'integer|between:4,6',
            'description' => 'required',
            'route_id' => 'required'
        ]);

        if ($validator->fails()) {
            return StandardJsonResponse(-1, '表单验证失败,请检查一下');
        }

        $user = User::current();

        if ($user->group_id)
            return StandardJsonResponse(-1, '你已经拥有队伍');

        $all['captain_id'] = $user->id;

        $group = Group::create($all);

        $user->group_id = $group->id;
        $user->state = State::captain;
        $user->save();
        return StandardJsonResponse(1, '创建成功');
    }


    /**
     * [√普通]
     * 更新队伍信息
     * @param Request $request
     * @return JsonResponse
     */
    public function updateGroupInfo(Request $request)
    {

        $all = $request->all();
        $validator = Validator::make($all, [
            'name' => 'required',
            'capacity' => 'integer|between:4,6',
            'description' => 'required',
            'route_id' => 'required'
        ]);

        if ($validator->fails())
            return StandardJsonResponse(-1, '表单验证失败');

        $user = User::current();
        $group = Group::where('captain_id', $user->id)->first();
        if ($group !== null) {
            // 验证1: 队伍锁定状态验证
            // 验证2: 人数验证
            // 验证3: 校区验证
            $memberCount = $group->members()->count();
            $walkPath = WalkRoute::find($all['route_id']);

            if ($group->is_submit === true)
                return StandardJsonResponse(-1, '队伍已锁定');
            else if ($memberCount > $group->capacity)
                return StandardJsonResponse(-1, '队伍人数超过容量');

            $group->fill($all);
            $group->save();

            return StandardJsonResponse(1, '更新队伍信息成功');
        }

        return StandardJsonResponse(-1, '你没有权限修改队伍信息');
    }


    /**
     * [√基本]
     * 解散队伍
     * @param Request $request
     * @return JsonResponse
     * @throws Exception
     */
    public function breakGroup(Request $request)
    {
        $user = User::current();
        $group = $user->group()->first();

        if (!$group)
            return StandardJsonResponse(-1, "你还没有队伍");
        else if ($user->id !== $group->captain_id)
            return StandardJsonResponse(-1, '你没有权限解散队伍');

        $group->delete();
        return StandardJsonResponse(1, '成功解散队伍');

    }

    /**
     * [√基本]
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

        if ($group->captain_id === $user->id) {
            return StandardJsonResponse(-1, '你是队长，不能离开队伍');
        }

        $user->leaveGroup();

        //notify(_notify::leave, $user->id);

        return StandardJsonResponse(1, '成功离开队伍');
    }


    /**
     * [√无权限 √人数要求 √报名限流 √线路限制]
     * 锁定队伍
     * @param Request $request
     * @return JsonResponse
     */
    public function submitGroup(Request $request)
    {
        $user = User::current();
        if ($user->state != State::captain)
            return StandardFailJsonResponse('你没有权限锁定队伍');

        $group = $user->group()->first();
        //校验: 人数达到需求
        $leastMembersCount = env("minGroupPeople");

        if ($group->members < $leastMembersCount) //判断人数是否达到要求
            return StandardFailJsonResponse('只有到达' . $leastMembersCount . '人才可以锁定哦');


        //校验: 当前报名的线路是否还有余量
        $route = WalkRoute::find($group->route_id)->first();
        $submit = Group::where('is_submit', 1)->where('route_id', $group->route_id)->count();

        if ($route->capacity <= $submit) {
            return StandardFailJsonResponse('今日人数已经满了');
        }

        $group->is_submit = true;
        $group->save();

        return StandardJsonResponse(1, '提交队伍成功');
    }

    /**
     * [√通过验证]
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
            if ($group->is_submit == false) {
                return StandardJsonResponse(-1, '无需此操作');
            }

            $group->is_submit = false;
            $group->save();

            //notify(_notify::unsubmit, $group->id);

            return StandardJsonResponse(1, '取消提交队伍成功');
        }
        return StandardJsonResponse(-1, '你没有权限解锁队伍');
    }


    /**
     * [√通过验证]
     * 踢出队伍
     * @param Request $request
     * @return JsonResponse
     */
    public function deleteMember(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'user_id' => 'required|integer'
        ]);
        $user = User::current();
        $delete_id = $request->get('user_id');
        $deleteUser = User::find($delete_id);

        $group = $user->group()->first();
        $members = $group->members()->get();

        //校验1: 队伍是否锁定
        //校验2: 自己是否时队长
        if ($group->is_submit) {
            return StandardJsonResponse(-1, '已锁定队伍');
        } else if ($group->captain_id == $delete_id) {
            return StandardJsonResponse(-1, '你是队长，不能踢自己');
        } else if ($deleteUser == null) {
            return StandardJsonResponse(-1, '找不到该用户');
        }

        $deleteUser->leaveGroup();

        return StandardJsonResponse(1, '踢人成功');
    }

}
