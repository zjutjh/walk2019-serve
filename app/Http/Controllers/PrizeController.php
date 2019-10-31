<?php

namespace App\Http\Controllers;

use App\Group;
use App\PrizePool;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class PrizeController extends Controller
{
    public function current(Request $request){
        $all = $request->all();
        $validator = Validator::make($all, [
           'campus'=>'required'
        ]);

        if($validator->fails()){
            return StandardFailJsonResponse();
        }

        $campus = $all['campus'];
        if($campus == '屏峰' || $campus == '朝晖'){
            return StandardJsonResponse('请求成功', PrizePool::current($campus)) ;
        }
    }

    public function select(Request $request){
        $all = $request->all();
        $validator = Validator::make($all, [
            'group_id' => 'integer'
        ]);

        if($validator->fails()){
            return StandardFailJsonResponse();
        }

        $group_id = $all['group_id'];

        $group = Group::find($group_id);

        if($group === null){
            return StandardJsonResponse(-1,'队伍不存在');
        }

        $prize = PrizePool::next($group_id);

        if($prize === null){
            return StandardJsonResponse(-1,'抽奖失败');
        } else if($prize === 1){
            return StandardJsonResponse(1, '队伍未提交，不能抽奖');
        } else if($prize === 2) {
            $prize_exist = PrizePool::find($group->prize);
            return StandardJsonResponse(1, '该队伍抽过奖了', $prize_exist);
        } else {
            return StandardJsonResponse(1,'抽奖成功', $prize);
        }

    }
}
