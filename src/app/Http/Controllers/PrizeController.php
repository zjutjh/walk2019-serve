<?php

namespace App\Http\Controllers;

use App\Group;
use App\PrizePool;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class PrizeController extends Controller
{
    private static function validate_no($all)
    {
        return Validator::make($all, [
            'no' => 'required|alpha_dash'
        ]);
    }


    public function getData(Request $request)
    {
        return StandardJsonResponse(1, "获取成功", PrizePool::getData());
    }

    public function verify(Request $request)
    {
        $all = $request->all();
        $validator = self::validate_no($all);

        if ($validator->fails()) {
            return StandardJsonResponse(-1, '字段验证失败');
        }

        $no = $all['no'];

        $group = Group::where('No', $no)->get()->first();

        if ($group === null) {
            return StandardJsonResponse(-1, '队伍不存在');
        }

        //$group = $groups->first();
        $group_id = $group['id'];

        $result = PrizePool::verify($group_id);

        if ($result === null)
            return StandardJsonResponse(-1, '队伍不存在');
        elseif ($result === 1)
            return StandardJsonResponse(-1, '该队伍还没有抽过奖');
        else {
            $prize = PrizePool::find($group->prize_id);
            if ($result == 2)
                return StandardJsonResponse(1, "该队伍已经领过奖了", $prize);
            else
                return StandardJsonResponse(1, "领奖成功", $prize);

        }
    }

    public function select(Request $request)
    {
        $all = $request->all();
        $validator = Validator::make($all, [
            'no' => 'required|alpha_dash',
            'title' => 'required'
        ]);

        if ($validator->fails()) {
            return StandardJsonResponse(-1, '字段验证失败');
        }
        $no = $all['no'];

        $group = Group::where('No', $no)->get()->first();

        if ($group === null)
            return StandardJsonResponse(-1, '队伍不存在');

        $group_id = $group['id'];
        $title = $all['title'];

        $prize = PrizePool::next($title, $group_id);

        if ($prize === null)
            return StandardJsonResponse(-1, '抽奖失败');
        else if ($prize === 1)
            return StandardJsonResponse(4, '队伍未提交，不能抽奖');
        else if ($prize === 2) {
            $prize_exist = PrizePool::find($group->prize_id);
            return StandardJsonResponse(3, '该队伍抽过奖了', $prize_exist);
        } else if ($prize === 3)
            return StandardJsonResponse(2, '奖池为空，不能再抽奖了');
        else
            return StandardJsonResponse(1, '抽奖成功', $prize);

    }

    public function index(Request $request)
    {
        return view('prize',
            [
                'prize_pool' => PrizePool::getData()
            ]
        );
    }

    public function indexPost(Request $request)
    {
        $result = [];
        $all = $request->all();
        $validator = Validator::make($all, [
            'no' => 'required|alpha_dash'
        ]);

        if ($validator->fails()) {
            return StandardJsonResponse(-1, '字段验证失败');
        }
        $no = $all['no'];

        $group = Group::where('No', $no)->get()->first();

        if ($group === null) {
            $result['msg'] = '队伍不存在';
        } else {
            //$group = $groups->first();
            $group_id = $group['id'];
            $result['no'] = $no;

            $prize = PrizePool::next($group_id);

            if ($prize === null)
                $result['msg'] = '队伍不存在';
            else if ($prize === 1)
                $result['msg'] = '队伍未提交，不能抽奖';
             else if ($prize === 2) {
                $prize_exist = PrizePool::find($group->prize);
                $result['msg'] = '该队伍抽过奖了';
                $result['data'] = $prize_exist;
            } else if ($prize === 3)
                $result['msg'] = '奖池为空，不能再抽奖了';
             else {
                $result['msg'] = '抽奖成功';
                $result['data'] = $prize;
            }
        }

        return view('prize',
            [
                'result' => $result,
                'prize_pool' => PrizePool::getData()
            ]
        );
    }
}
