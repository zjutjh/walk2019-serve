<?php

namespace App\Http\Controllers;

use App\Apply;
use App\User;
use App\Group;
use App\WalkRoute;
use App\SignupTime;
use App\Helpers\_State;
use App\WxTemplate;
use ErrorException;
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
     * [√通过测试]
     * 查询队伍信息
     * @return JsonResponse
     */
    public function getGroupInfo()
    {
        $user = User::current();
        $group = $user->group()->first()->toArray();

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
        $user = User::current();

        if(!$this->validateGroup($all)) {
            return StandardJsonResponse(-1,'表单验证失败');
        }

        if ($user->group_id !== null)
            return StandardJsonResponse(-1, '你已经拥有队伍');

        $all['captain_id'] = $user->id;
        $group = Group::create($all);
        $user->group_id = $group->id;
        $user->state = _State::captain;
        $user->save();

        //notify(_notify::create, $group->id);

        return StandardJsonResponse(1, '创建成功');
    }

    public function validateGroup($all){

        $validator = Validator::make($all, [
            'name' => 'required|between:2,15',
            'capacity' => 'integer|between:'.config('info.members_count.min').','.config('info.members_count.max'),
            'description' => 'max:200',
            'route' => 'required'
        ]);
        if($validator->fails()){
            return false;
        }
        try{
            $all['route_id'] = WalkRoute::getId($all['route']);
            //$all['route_id'] = WalkRoute::getId($all['route']);
            unset($all['route']);
        } catch (ErrorException $ex){
            return false;
        }

        return true;
    }


    /**
     * [√普通]
     * 更新队伍信息
     * @param Request $request
     * @return JsonResponse
     */
    public function updateGroupInfo(Request $request)
    {
        $user = User::current();
        $all = $request->all();
        $group = Group::where('captain_id', $user->id)->first();

        if(!$this->validateGroup($all)) {
            return StandardJsonResponse(-1,'表单验证失败');
        }

        if ($group !== null) {
            // 验证1: 队伍锁定状态验证
            // 验证2: 人数验证
            // 验证3: 校区验证
            $memberCount = $group->members()->count();
            $walkPath = WalkRoute::find('$all->route_id');
            if ($group->is_submit === true){
                return StandardJsonResponse(-1, '队伍已锁定');
            }
            elseif ($memberCount > $group->capacity) {
                return StandardJsonResponse(-1, '队伍人数超过容量');
            }

            $group->fill($all);
            $group->save();

            //notify(_notify::update, $group->id);

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

        //echo $user->id . ' '. $group->id;

        if (!$group){
            return StandardJsonResponse(-1,"你还没有队伍");
        } elseif ($user->id !== $group->captain_id) {
            return StandardJsonResponse(-1, '你没有权限解散队伍');
        }

        /**
         * 先提醒，再删除
         */
        //notify(_notify::dismiss, $group_id);

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

        if($group->captain_id === $user->id){
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
        if ($user->state !== _State::captain)
            return StandardJsonResponse(-1, '你没有权限锁定队伍');

        $group = $user->group()->first();
        //校验: 人数达到需求
        $leastMembersCount = config('info.members_count.least');
        if ($group->members < $leastMembersCount) //判断人数是否达到要求
            return StandardJsonResponse(-1, '只有到达'.$leastMembersCount.'人才可以锁定哦');
        //校验: 当前报名未满
        $signupConfig = SignupTime::caculateCurrentConfig();
        if($signupConfig['remain'] <= 0){
            return StandardJsonResponse(-1, '当前报名队伍已满');
        }
        //校验: 当前报名的线路是否还有余量
        $walkRoute = WalkRoute::find($group->route_id);
        if($walkRoute->remainCount() <= 0){
            return StandardJsonResponse(-1,'此线路已经报满了');
        }

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
        //拒绝与此队伍有关的所有apply
        $applies = Apply::where('apply_team_id', $group->id)->get();

        Apply::removeGroup($group->id);

        //notify(_notify::submit, $group->id);

        return StandardJsonResponse(1, '锁定队伍成功');
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
            if($group->is_submit === false){
                return StandardJsonResponse(-1, '无需此操作');
            }

            $group->is_submit = false;
            $group->save();

            //notify(_notify::unsubmit, $group->id);

            return StandardJsonResponse(1,'解锁队伍成功');
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
        $validator = Validator::make($request->all(),[
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
        } elseif ($group->captain_id === $delete_id) {
            return StandardJsonResponse(-1, '你是队长，不能踢自己');
        } elseif( $deleteUser === null){
            return StandardJsonResponse(-1, '找不到该用户');
        }

        $deleteUser->leaveGroup();

        return StandardJsonResponse(1,'踢人成功');
    }

}
