<?php

namespace App\Http\Controllers;

use App\Exports\GroupExport;
use App\Group;
use App\Notifications\Wechat;
use App\User;
use App\WalkRoute;
use App\WechatTemplate;
use DateInterval;
use DateTime;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Maatwebsite\Excel\Facades\Excel;

class ConfigController extends Controller
{

    /**
     * 发送消息通知
     * @param Request $request
     * @return string
     */
    public function setConfig(Request $request)
    {

        return '发送成功';
    }

    /**
     * 发送消息通知
     * @param Request $request
     * @return string
     */
    public function delConfig(Request $request)
    {

        return '发送成功';
    }

}
