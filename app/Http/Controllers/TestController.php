<?php

namespace App\Http\Controllers;

use App\Exports\GroupExport;
use App\Group;
use App\Notifications\Wechat;
use App\User;
use App\WalkRoute;
use App\WxTemplate;
use Illuminate\Http\Request;
use Maatwebsite\Excel\Facades\Excel;

class TestController extends Controller
{

    /**
     * 发送消息通知
     * @param Request $request
     * @return string
     */
    public function sendTmp(Request $request)
    {
        $ids = $request->get('ids');
        $ids = explode("\n", $ids);
        $users = User::find($ids);
        foreach ($users as $user) {
            $user->notify(new Wechat(WxTemplate::Test));
        }
        return '发送成功';
    }

    public function GenYXGroupId(Request $request)
    {

        $routes = WalkRoute::orderBy('id', 'asc')->get();
        foreach ($routes as $route) {
            $groups = Group::where('is_submit', 1)->where('route_id', $route->id)->orderBy('id', 'asc')->get();

            $i = 1;
            foreach ($groups as $group) {
                $group->No = $route->type . $i;
                $i = $i + 1;
                $group->save();
            }
        }

    }

    public function Download(Request $request)
    {
        return Excel::download(new GroupExport(), '队伍名单.xlsx');
    }

    public function SetCap(Request $request)
    {
        return StandardSuccessJsonResponse();
    }
}
