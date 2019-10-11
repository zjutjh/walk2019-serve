<?php

namespace App\Http\Controllers;

use App\Exports\GroupExport;
use App\User;
use App\Group;
use App\SignupTime;
use App\WalkRoute;
use Carbon\Traits\Timestamp;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Carbon;

class IndexController extends Controller
{
    public function supportRoute(){
        return template(1, '请求成功', YxRoute::getSupportRoute());
    }


    /**
     * [√通过测试]
     * 获取首页信息
     * @return \Illuminate\Http\JsonResponse
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


    /**
     * 发送消息通知
     */
    public function sendTmp(Request $request) {
        $ids = $request->get('ids');
        $title = $request->get('title');
        $content = $request->get('content');
        $ids = explode("\n", $ids);
        $users = User::find($ids);
        $data = [
            'first' => $title,
            'keyword1' => '消息通知',
            'keyword2' => '很重要哦',
            'keyword3' => date('Y-m-d H:i:s', time()),
            'remark' => $content
        ];
        foreach ($users as $user) {
            $user->notify($data);
        }

        return '发送成功';


    }
}
