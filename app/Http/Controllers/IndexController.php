<?php

namespace App\Http\Controllers;


use App\User;
use App\Group;
use App\RegisterTime;
use Illuminate\Http\JsonResponse;

class IndexController extends Controller
{

    /**
     * 获取首页信息
     * @return JsonResponse
     */
    public function indexInfo() {
        // $indexInfo = [
        //   'end_time' => config('api.system.EndTime'),
        //   'is_end'=> config('api.system.IsEnd'),
        //   'apply_count' => User::getUserCount(),
        //   'group_count' => Group::getTeamCount()
        // ];
        $begin = RegisterTime::beginAt();
        $end = RegisterTime::endAt();
        if (now() < $begin){
          $state = -1;//'not_start';
        } else if (now() <= $end){
          $state = 1;// 'doing';
        } else {
          $state = 0;//'end';
        }

        $indexInfo = [
          'begin' => $begin,
          'end' => $end,
          'state' => $state,
          'apply_count' => User::getUserCount(),
          'current' => RegisterTime::caculateCurrentConfig(),
        ];

        return StandardJsonResponse(1, 'Success', $indexInfo);
    }

    public function configAll(){
        return StandardJsonResponse(1, 'Success', RegisterTime::caculateConfig());
    }
}
