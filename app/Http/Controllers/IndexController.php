<?php

namespace App\Http\Controllers;


use App\User;
use App\Group;
use App\SignupTime;
use App\WalkRoute;
use Carbon\Traits\Timestamp;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Carbon;

class IndexController extends Controller
{

    /**
     * [√通过测试]
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
        $begin = SignupTime::beginAt();
        $end = SignupTime::endAt();
        $now = now()->toDateTimeString();

        if($begin == null || $end == null){
            return StandardJsonResponse(-1, "服务器还没有配置");
        }

        if ($now < $begin){
          $state = -1;//'not_start';
        } else if (now() <= $end){
          $state = 1;// 'doing';
        } else {
          $state = 0;//'end';
        }

        $indexInfo = [
            'begin' => $begin,
            'end' => $end,
            'now' => $now,
            'state' => $state,
            'apply_count' => User::getUserCount(),
            'current' => SignupTime::caculateCurrentConfig(),
        ];

        return StandardJsonResponse(1,"获取信息成功",$indexInfo);
    }

    /**
     * [√通过测试]
     * 获取报名的人数
     * @return JsonResponse
     */
    public function signupConfig() {
        return StandardSuccessJsonResponse(SignupTime::caculateConfig());
    }

}
