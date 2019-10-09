<?php

namespace App\Http\Controllers;


use App\User;
use App\Group;
use App\SignupTime;
use App\WalkPath;
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
        $begin = SignupTime::beginAt();
        $end = SignupTime::endAt();
        if (now() < $begin){
          $state = 'nostart';
        } else if (now() <= $end){
          $state = 'doing';
        } else {
          $state = 'end';
        }

        $indexInfo = [
          'begin' => $begin,
          'end' => $end,
          'state' => $state,
          'apply_count' => User::getUserCount(),
          'current' => SignupTime::caculateCurrentConfig(),
        ];

        return StandardSuccessJsonResponse($indexInfo);
    }

    public function signupConfig() {
        return StandardSuccessJsonResponse(SignupTime::caculateConfig());
    }

    public function walkpathConfig() {
        return StandardSuccessJsonResponse(WalkPath::caculateConfig());
    }

    public function campusConfig() {
        return StandardSuccessJsonResponse(config('info.campus'));
    }

    public function schoolConfig(){
        return StandardSuccessJsonResponse(config('info.school'));
    }

    public function membersCountConfig(){
        return StandardSuccessJsonResponse(config('info.members_count'));
    }
}
