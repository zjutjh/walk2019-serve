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
            'no' => 'required|alpha_dash'
        ]);

        if($validator->fails()){
            return StandardJsonResponse(-1,'字段验证失败');
        }
        $no = $all['no'];

        $group = Group::where('No', $no)->get()->first();

        if($group === null){
            return StandardJsonResponse(-1,'队伍不存在');
        }

        //$group = $groups->first();
        $group_id = $group['id'];

        $prize = PrizePool::next($group_id);

        if($prize === null){
            return StandardJsonResponse(-1,'抽奖失败');
        } else if($prize === 1){
            return StandardJsonResponse(1, '队伍未提交，不能抽奖');
        } else if($prize === 2) {
            $prize_exist = PrizePool::find($group->prize);
            return StandardJsonResponse(1, '该队伍抽过奖了', $prize_exist);
        } else if($prize === 3){
            return StandardJsonResponse(1, '奖池为空，不能再抽奖了');
        } else {
            return StandardJsonResponse(1,'抽奖成功', $prize);
        }

    }

    public function index(Request $request){
        return view('prize',
            [
                'prize_pool'=>PrizePool::getData()
            ]
        );
    }

    public function indexPost(Request $request){
        $result = [];
        $all = $request->all();
        $validator = Validator::make($all, [
            'no' => 'required|alpha_dash'
        ]);

        if($validator->fails()){
            return StandardJsonResponse(-1,'字段验证失败');
        }
        $no = $all['no'];

        $group = Group::where('No', $no)->get()->first();

        if($group === null){
            $result['msg'] = '队伍不存在';
        }

        //$group = $groups->first();
        $group_id = $group['id'];
        $result['no'] = $no;

        $prize = PrizePool::next($group_id);


        if($prize === null){
            $result['msg'] = '队伍不存在';
        } else if ($prize === 1){
            $result['msg'] = '队伍未提交，不能抽奖';
        } else if($prize === 2) {
            $prize_exist = PrizePool::find($group->prize);
            $result['msg'] = '该队伍抽过奖了';
            $result['data'] = $prize_exist;
        } else if($prize === 3){
            $result['msg'] ='奖池为空，不能再抽奖了';
        } else {
            $result['msg'] = '抽奖成功';
            $result['data'] = $prize;
        }

        return view('prize',
            [
                'result'=>$result,
                'prize_pool'=>PrizePool::getData()
            ]
        );
    }
}
