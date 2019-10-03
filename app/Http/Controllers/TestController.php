<?php

namespace App\Http\Controllers;

use App\User;
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
