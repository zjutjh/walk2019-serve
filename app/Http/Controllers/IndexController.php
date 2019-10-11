<?php

namespace App\Http\Controllers;


use App\User;
use Illuminate\Http\JsonResponse;

class IndexController extends Controller
{

    /**
     * [√通过测试]
     * 获取首页信息
     * @return JsonResponse
     */
    public function indexInfo()
    {

        $begin = config("api.system.BeginTime");
        $end = config("api.system.EndTime");
        $now = now()->toDateTimeString();

        if ($begin == null || $end == null) {
            return StandardJsonResponse(-1, "Server Error");
        }

        if ($now < $begin) {
            $state = -1;//'not_start';
        } else if (now() <= $end) {
            $state = 1;// 'doing';
        } else {
            $state = 0;//'end';
        }

        $indexInfo = [
            'begin' => $begin,
            'end' => $end,
            'now' => $now,
            'state' => $state,
            'apply_count' => User::getUserCount()
        ];

        return StandardSuccessJsonResponse($indexInfo);
    }


}
