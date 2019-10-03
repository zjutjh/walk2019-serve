<?php

namespace App\Http\Controllers;


use App\User;
use App\Group;
use Illuminate\Http\JsonResponse;

class IndexController extends Controller
{

    /**
     * 获取首页信息
     * @return JsonResponse
     */
    public function indexInfo() {
        $indexInfo = [
          'end_time' => env('EndTime'),
          'is_end'=> env('IsEnd'),
          'apply_count' => User::getUserCount(),
          'team_count' => Group::getTeamCount()
        ];

        return StandardJsonResponse(1, 'Success', $indexInfo);

    }

}
