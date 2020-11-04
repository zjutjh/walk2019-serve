<?php

namespace App\Http\Controllers\Admin;

use App\Exports\GroupExport;
use App\Exports\UsersExport;
use App\Group;
use App\Http\Controllers\Controller;
use App\Notifications\Wechat;
use App\User;
use App\WalkRoute;
use App\Helpers\WechatTemplate;
use DateInterval;
use DateTime;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Maatwebsite\Excel\Facades\Excel;

class AdminController extends Controller
{
    /**
     * 管理员登录
     * @param Request $request
     * @return string
     */
    public function login(Request $request)
    {
        if(env('AdminPass')!==$request->get('pass'))
            return StandardFailJsonResponse("Admin pass wrong");

        session(['is_admin' => true]);

        return  StandardSuccessJsonResponse();
    }

    /**
     * 管理员登出
     * @param Request $request
     * @return string
     */
    public function logout(Request $request)
    {
        $request->session()->forget('is_admin');
        return  StandardSuccessJsonResponse();
    }
    /**
     * 发送消息通知
     * @param Request $request
     * @return string
     */
    public function getTotalGroupStaticInfo(Request $request)
    {

    }

    /**
     * 发送消息通知
     * @param Request $request
     * @return string
     */
    public function sendTestTmp(Request $request)
    {
        $ids = $request->get('ids');
        $ids = explode("\n", $ids);
        $users = User::find($ids);
        foreach ($users as $user) {
            $d = WechatTemplate::Test;
            $d['keyword2'] = '你的队伍的出发时间是' . $user->created_at;
            $user->notify(new Wechat($d));
        }
        return '发送成功';
    }

    public function genWalkGroupId(Request $request)
    {

        $routes = WalkRoute::orderBy('id', 'asc')->get();
        foreach ($routes as $route) {
            $groups = Group::where('is_submit', 1)->where('route_id', $route->id)->inRandomOrder()->get();
            $i = 1;
            foreach ($groups as $group) {
                $group->No = $route->type . sprintf("%03d", $i);
                $i++;
                $group->save();
            }
        }

    }

    public function genWalkGroupTime(Request $request)
    {
        $groups = Group::where('is_submit', 1)->get();
        foreach ($groups as $group) {
            $group->start_time = $this->GetStartTime($group);
            $group->save();
        }

    }

    public function sendResult(Request $request)
    {
        $groups = Group::all();

        foreach ($groups as $group) {

            if ($group->is_submit) {
                $mem = $group->members()->get();
                foreach ($mem as $m) {
                    try {
                        $d = WechatTemplate::Success;
                        $d['keyword1'] = '你的队伍编号是' . $group->No;
                        $d['keyword2'] = '你的队伍的出发时间是' . $group->start_time;
                        $m->notify(new Wechat($d));
                    } catch (Exception $exception) {
                        Log::info($group->id);
                    }
                }
            } else {
                $mem = $group->members()->get();
                foreach ($mem as $m) {
                    try {
                        $m->notify(new Wechat(WechatTemplate::Failed));
                    } catch (Exception $exception) {
                        Log::info($mem);
                    }

                }
            }
        }
    }

    public function GetStartTime($group)
    {
        if ($group->route_id === 1) {
            $date = new DateTime('2019-11-16 6:40:00');
            for ($i = 0; $i < (intval($group->No) / 90) && $i < 5; $i = $i + 1)
                $date = $date->add((new DateInterval('PT20M')));

            return $date;
        }
        if ($group->route_id === 2) {
            $date = new DateTime('2019-11-16 8:00:00');
            for ($i = 0; $i < ((intval($group->No) - 1000) / 90) && $i < 5; $i = $i + 1)
                $date = $date->add((new DateInterval('PT20M')));

            return $date;
        }

        if ($group->route_id === 3) {
            $date = new DateTime('2019-11-16 7:30:00');
            $No = (intval($group->No) - 2000);

            if ($No <= 75)
                $date = $date->add((new DateInterval('PT30M')));
            else if ($No <= 2 * 75)
                $date = $date->add((new DateInterval('PT1H')));
            else if ($No <= 2 * 75 + 50)
                $date = $date->add((new DateInterval('PT1H30M')));
            else if ($No <= 2 * 75 + 2 * 50)
                $date = $date->add((new DateInterval('PT2H')));
            else if ($No <= 2 * 75 + 3 * 50)
                $date = $date->add((new DateInterval('PT2H30M')));
            else
                $date = $date->add((new DateInterval('PT3H')));

            return $date;
        }

    }

    public function DownloadGroupList(Request $request)
    {
        return Excel::download(new GroupExport(), '队伍名单.xlsx');
    }
    public function DownloadUserList(Request $request)
    {
        return Excel::download(new UsersExport(), '用户名单.xlsx');
    }
}
