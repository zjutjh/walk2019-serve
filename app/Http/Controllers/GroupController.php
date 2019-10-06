<?php

namespace App\Http\Controllers;

use App\SuccessTeam;
use App\User;
use App\YxApply;
use App\YxGroup;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;

class GroupController extends Controller
{

    /**
     * 队伍列表
     */
    public function groupLists()
    {
        $groups = Group::orderBy('id', 'desc')->paginate(15);
        return template(1, '获取数据成功', $groups);
    }

    /**
     * 创建队伍
     */
    public function createGroup(Request $request)
    {
        $teamInfo = $request->all();
        $validator = Validator::make($teamInfo, [
            'name' => 'required|max:180',
            'description' => 'required|max:180'
        ]);
        if($validator->fails()){
            return template(-1, '名称或描述太长');
        }

        $user = Auth::user();
        if (!!$user->group_id) {
            return template(-1, '你已经拥有队伍');
        }
        $teamInfo['captain_id'] = $user->id;
        $group = Group::create($teamInfo);
        $user->group_id = $group->id;
        $user->state = 3;
        $user->save();
        $data = [
            'first' => '你已经创建了一个队伍',
            'keyword1' => '队伍创建',
            'keyword2' => '创建成功',
            'keyword3' => date('Y-m-d H:i:s', time()),
            'remark' => '快邀请大家来加入你的队伍把！ 点击查看详情'
        ];
        $user->notify($data);
        return template(1, '创建成功');
    }


    public function updateGroupInfo(Request $request) {
        
        $teamInfo = $request->all();
        $validator = Validator::make($teamInfo, [
            'name' => 'required|max:180',
            'description' => 'required|max:180'
        ]);
        if($validator->fails()){
            return template(-1, '名称或描述太长');
        }

        $user = Auth::user();
        $group = $user->group()->first();

        $route = YxRoute::fromName($teamInfo['route']);
        $members = $group->members();
        foreach ($members as $member) {
            if(!$route->allowJoin($member)){
                return template(-1, '你队伍里有不支持此线路校区的队员');
            }
        }

        $group->fill($teamInfo);
        $group->save();
        return template(1, '更新成功');
    }


    /**
     * 解散队伍
     */
    public function breakGroup()
    {
        $user = Auth::user();
        $group = $user->group()->first();
        if ($user->id !== $group->captain_id) {
            return template(-1, '你没有权限删除队伍');
        }
        $group->delete();
        $data = [
            'first' => '你已经解散了你的队伍',
            'keyword1' => '队伍解散',
            'keyword2' => '解散成功',
            'keyword3' => date('Y-m-d H:i:s', time()),
            'remark' => '如果你还想创建一个队伍，点击详情哦'
        ];
        $user->notify($data);
        return template(1, '删除成功');
    }

    /**
     * 申请入队
     */
    public function doApply(Request $request)
    {
        $user = Auth::user();
        $groupId = $request->get('groupId');
        $group = Group::find($groupId);
        if ($group->members === $group->num) {

            return template(-1, '该队伍已经满员');
        }
        if ($group->is_lock) {
            return template(-1, '该队伍已经锁定');
        }

        $route = YxRoute::fromName($group->route);

        if(!$route->allowJoin($user)){
            return template(1,'你无法参加此条线路的毅行');
        }
        if ($group->captain_id == Auth::user()->id) {
            return template(-1, '这是你自己的队伍');
        }

        YxApply::create(['apply_team_id' => $groupId, 'apply_id' => Auth::user()->id]);
        $group->notifyCaptain();

        $data = [
            'first' => "你正在申请 {$group->name} 的队伍",
            'keyword1' => '队伍申请',
            'keyword2' => '等待同意',
            'keyword3' => date('Y-m-d H:i:s', time()),
            'remark' => '耐心等待队长同意哦'
        ];
        $user->notify($data);
        $user->state = 2;
        $user->save();
        return template(1, '正在申请中');
    }

    /**
     * 撤回申请
     * @return \Illuminate\Http\JsonResponse
     */
    public function deleteApply() {
        $user = Auth::user();
        $apply_id = $user->id;
        if (!$apply = YxApply::where('apply_id', $apply_id)->first()) {
            return template(-1, '你的申请已经处理');
        }
        $user->state = 1;
        $user->save();
        $apply->delete();
        return template(1, '撤回成功');
    }


    /**
     * 获取申请队伍信息
     */
    public function getApplyTeam() {
        $apply_id = Auth::user()->id;
        $yxAplly = YxApply::where('apply_id', $apply_id)->first();
        $group = Group::where('id', $yxAplly->group_id)->first();
        return template(1, '查询成功', $group);

    }

    /**
     * 离开队伍
     */
    public function leaveGroup()
    {
        $user = Auth::user();
        $group = $user->group()->first();
       
        //TODO: 加入后续逻辑

        $user->leaveGroup();
        $data = [
            'first' => "你已经离开了一个队伍",
            'keyword1' => '离开队伍',
            'keyword2' => '离开成功',
            'keyword3' => date('Y-m-d H:i:s', time()),
            'remark' => '如果你还想加入一个队伍，请进入队伍列表寻找哦'
        ];
        $user->notify($data);

        return template(1, '离开队伍');
    }


    /**
     * 锁定队伍
     */
    public function lockGroup()
    {
        $user = Auth::user();
        if ($user->state()->first()->state == 3) {
            $group = $user->group()->first();
            if($group->members()->count() < 4){
                return template(-1, '人数只有达到4人才可以锁定');
            } elseif(PeriodRegister::remainCount() <= 0){
                return template(-1, '当前时间段已经没有名额了');
            } elseif(YxRoute::isFull()){
                return template(-1, '此线路该时间段出发的队伍已满');
            }

            $group->is_lock = true;
            $group->save();

            $data = [
                'first' => "你已经锁定了你的队伍",
                'keyword1' => '队伍锁定',
                'keyword2' => '锁定成功',
                'keyword3' => date('Y-m-d H:i:s', time()),
                'remark' => '如果想解除锁定，点击详情进入队伍列表解锁哦'
            ];
            $user->notify($data);
            return template(1, '已经锁定队伍');
        }


        return template(-1, '你没有权限');

    }


    /**
     * 解锁队伍
     * @return \Illuminate\Http\JsonResponse
     */
    public function unlockGroup()
    {
        $user = Auth::user();
        if ($user->state == 3) {
            $group = $user->group()->first();
            $group->is_lock = false;
            $group->save();
            $data = [
                'first' => "你已经解锁你的队伍",
                'keyword1' => '队伍解锁',
                'keyword2' => '解锁成功',
                'keyword3' => date('Y-m-d H:i:s', time()),
                'remark' => '点击详情，查看队伍信息'
            ];
            $user->notify($data);
            return template(1, '解锁成功');
        }

        return template(-1, '你没有权限');
    }

    /**
     * 同意加入
     */
    public function agreeMember(Request $request)
    {
        $group = Auth::user()->group()->first();
        if ($group->members()->count() === $group->num) {
            return template(-1, '队员数已经达到上限');
        }
        $apply_id = $request->get('apply_id');
        $groupId = Auth::user()->group_id;
        $user = User::where('id', $apply_id)->first();
        if ($user->state != 2) {
            return template(-1, '该申请者已经撤回申请了');
        }
        $user->addGroup($groupId);
        YxApply::where('apply_id', $apply_id)->delete();
        $data = [
            'first' => "你申请的队伍已经同意了你的申请",
            'keyword1' => '队伍申请',
            'keyword2' => '申请成功',
            'keyword3' => date('Y-m-d H:i:s', time()),
            'remark' => '点击详情，查看队伍信息'
        ];
        $user->notify($data);
        $group = $user->group()->first();


        return template(1, '同意成功');

    }
    /**
     * 拒绝加入
     */
    public function refuseMember(Request $request)
    {
        $apply_id = $request->get('apply_id');

        $user = User::where('id', $apply_id)->first();
        if ($user->state != 2) {
            return template(-1, '该申请者已经撤回申请了');
        }
        YxApply::where('apply_id', $apply_id)->delete();
        $uState = $user->state()->first();
        $uState->state = 1;
        $uState->save();
        $data = [
            'first' => "你申请的队伍已经拒绝了你的申请",
            'keyword1' => '队伍申请',
            'keyword2' => '申请失败',
            'keyword3' => date('Y-m-d H:i:s', time()),
            'remark' => '可以进入队伍列表找寻其他你希望加入的队伍哦'
        ];
        $user->notify($data);
        return template(1, '拒绝成功');
    }

    /**
     * 搜索队伍
     */
    public function searchTeam(Request $request)
    {
        $query_string = $request->get('query_string');
        if (!$query_string) {
            return $this->groupLists();
        }
        $groups = Group::where('name', 'like', "%{$query_string}%")->orWhere('id', $query_string)->paginate(100);
        return template(1, '搜索成功', $groups);
    }


    /**
     * 查询申请者列表
     * @return \Illuminate\Http\JsonResponse
     */
    public function getApplyList()
    {
        $groupId = Auth::user()->group_id;
        $applies = YxApply::where('apply_team_id', $groupId)->get();
        $userId = [];
        foreach ($applies as $apply) {
            $userId [] = $apply->apply_id;
        }

        $applyUsers = User::find($userId);
        return template(1, '请求成功', $applyUsers);
    }


    /**
     * 查询申请者数量
     * @return \Illuminate\Http\JsonResponse
     */
    public function getApplyCount()
    {
        $groupId = Auth::user()->group_id;
        $applyModels = YxApply::where('apply_team_id', $groupId)->count();
        return template(1, '请求成功', $applyModels);
    }

    /**
     * 查询队伍信息
     * @return \Illuminate\Http\JsonResponse
     */
    public function getGroupInfo()
    {
        $user = Auth::user();
        $group = $user->group()->first();
        return template(1, '获取成功', $group);
    }

    /**
     * 获取队伍成员信息
     * @return \Illuminate\Http\JsonResponse
     */
    public function getGroupMembers() {
        $user = Auth::user();

        $members = $user->group()->first()->members()->get();
        return template(1, '查询成功', $members);

    }


    /**
     * 踢出队伍
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function deleteMember(Request $request) {
        $delete_id = $request->get('delete_id');
        $user = User::find($delete_id);
        $user->leaveGroup();
        $data = [
            'first' => "你已经被移出了队伍",
            'keyword1' => '移出队伍',
            'keyword2' => '移出成功',
            'keyword3' => date('Y-m-d H:i:s', time()),
            'remark' => '如果你还想加入一个队伍，请进入队伍列表寻找哦'
        ];
        $user->notify($data);
        $cUser = Auth::user();
        $group = $cUser->group()->first();

        //TODO: 添加后续还击
        
        return template(1, '踢出队伍');
    }

    /**
     * 最后结果
     */
    public function result() {
        $user = Auth::user();
        Log::info('user', ['user' =>  $user]);
        $group = $user->group()->first();
        if (!$group) {
            Log::info('没有队伍', ['user' => $user]);
            return template(-1, '对不起你没有队伍, 所以你没有成功报名');
        } elseif (!$group->is_locked) {
            Log::info('失败查询', ['id' => $user->id, 'group' => $group]);
            return template(-1, '你的队伍没有报名成功');
        }
        Log::info('成功队伍查询', ['id' => $success->id]);
        $members = $group->members()->get();
        return template(1, '恭喜你的队伍和你成功报名精弘毅行', $members);

    }


}
