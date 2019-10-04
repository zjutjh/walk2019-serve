<?php

namespace App\Http\Controllers;

use App\Notifications\Wechat;
use App\User;
use App\WxTemplate;
use Illuminate\Http\Request;

class TestController extends Controller
{

    /**
     * 发送消息通知
     * @param Request $request
     * @return string
     */
    public function sendTmp(Request $request) {
        $ids = $request->get('ids');
        $ids = explode("\n", $ids);
        $users = User::find($ids);
        foreach ($users as $user) {
            $user->notify(new Wechat($ids,WxTemplate::Test));
        }

        return '发送成功';

    }
}
