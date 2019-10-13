<?php

namespace App\Http\Controllers;

use App\Helpers\State;
use App\Notifications\Wechat;
use App\User;
use App\Group;
use App\WalkRoute;
use App\WxTemplate;
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
        return StandardSuccessJsonResponse($groups);
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
        $groups = Group::where('id', $query_string)->orWhere('name', 'like', "%{$query_string}%")->paginate($pageSize);
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

        if ($user->group_id === null)
            return StandardFailJsonResponse('你还没有加入');

        $group = $user->group();
        $group->route = WalkRoute::where('id', $group->route_id)->first()->name;

        return StandardSuccessJsonResponse($group);
    }

    /**
     * [√通过测试]
     * 获取队伍成员信息
     * @return JsonResponse
     */
    public function getGroupMembers()
    {
        $user = User::current();
        if ($user === null)
            return StandardFailJsonResponse('你还没有报名');
        if ($user->group_id === null)
            return StandardFailJsonResponse('你还没有加入');

        $members = $user->group()->members()->get();
        return StandardSuccessJsonResponse($members);
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

        if ($validator->fails())
            return StandardFailJsonResponse('表单验证失败,请检查一下');

        $user = User::current();

        if ($user->group_id)
            return StandardFailJsonResponse('你已经拥有队伍');

        $all['captain_id'] = $user->id;

        $group = Group::create($all);

        $user->group_id = $group->id;
        $user->state = State::captain;
        $user->save();
        return StandardSuccessJsonResponse('创建成功');
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
            return StandardFailJsonResponse('表单验证失败');

        $user = User::current();
        $group = Group::where('captain_id', $user->id)->first();

        if ($group === null)
            return StandardFailJsonResponse('你没有权限修改队伍信息');
        if ($group->is_submit)
            return StandardFailJsonResponse('队伍已经提交，不能修改');

        $memberCount = $group->members()->count();

        if ($memberCount > $group->capacity)
            return StandardFailJsonResponse('队伍人数超过容量');

        $group->fill($all);
        $group->save();

        return StandardSuccessJsonResponse();
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
        $group = $user->group();

        if (!$group)
            return StandardFailJsonResponse("你还没有队伍");
        if ($user->id !== $group->captain_id)
            return StandardFailJsonResponse('你没有权限解散队伍');

        $group->delete();
        return StandardSuccessJsonResponse();
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
        $group = $user->group();

        if ($group->captain_id === $user->id)
            return StandardFailJsonResponse('你是队长，不能离开队伍');
        if ($group->is_submit)
            return StandardFailJsonResponse('队伍已经提交，不能离开');

        $user->leaveGroup();

        return StandardSuccessJsonResponse();
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

        $group = $user->group();
        //校验: 人数达到需求
        $leastMembersCount = env("minGroupPeople");

        if ($group->members < $leastMembersCount) //判断人数是否达到要求
            return StandardFailJsonResponse('只有到达' . $leastMembersCount . '人才可以锁定哦');

        //校验: 当前报名的线路是否还有余量
        $route = WalkRoute::where('id', $group->route_id)->first();
        $submit = Group::where('is_submit', 1)->where('route_id', $group->route_id)->count();

        if ($route->capacity <= $submit)
            return StandardFailJsonResponse('今日人数已经满了');

        $group->is_submit = true;
        $group->save();

        return StandardSuccessJsonResponse();
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
        $group = $user->group();

        if ($user->id !== $group->captain_id)
            return StandardFailJsonResponse('你没有权限解锁队伍');

        if (!$group->is_submit)
            return StandardJsonResponse(-1, '无需此操作');

        $group->is_submit = false;
        $group->save();

        //notify(_notify::unsubmit, $group->id);

        return StandardSuccessJsonResponse();
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

        $deleteUser = User::where('id', $delete_id)->first();

        $group = $user->group();

        if ($group->is_submit)
            return StandardFailJsonResponse('已提交队伍,不能踢人');
        else if ($group->captain_id === $delete_id)
            return StandardFailJsonResponse('你是队长，不能踢自己');
        else if ($deleteUser === null)
            return StandardFailJsonResponse('找不到该用户');
        else if ($group->id !== $deleteUser->group_id)
            return StandardFailJsonResponse('找不到该用户');

        $deleteUser->notify(new Wechat(WxTemplate::Knit));
        $deleteUser->leaveGroup();

        return StandardSuccessJsonResponse();
    }

}
