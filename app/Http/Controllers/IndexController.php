<?php

namespace App\Http\Controllers;

use App\Exports\GroupExport;
use App\User;
use App\YxGroup;
use App\YxState;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Maatwebsite\Excel\Facades\Excel;


class IndexController extends Controller
{

    /**
     * 获取首页信息
     * @return \Illuminate\Http\JsonResponse
     */
    public function indexInfo() {
        $yxState = YxState::where('id', 0)->first();
        $indexInfo = [
          'finish_time' => $yxState->finish_time,
          'apply_count' => User::getUserCount(),
          'team_count' => YxGroup::getTeamCount()
        ];

        return RJM(1, '请求成功', $indexInfo);

    }


    /**
     * 报名是否关闭
     * @return \Illuminate\Http\JsonResponse
     */
    public function verifyApplyEnd() {
        $yxState = YxState::where('id', 0)->first();
        if ($yxState->state === 1) {
            return RJM(1, '关闭报名');
        }

        return RJM(-1, '报名正在进行');
    }


    /**
     * 首页信息统计
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function count() {
        $apply_count = User::getUserCount();
        $team_count = YxGroup::getTeamCount();
        $upToTeam = YxGroup::whereNotNull('up_to_standard')->where('select_route', '<>', '朝晖京杭大运河毅行')->count();
        $chToTeam = YxGroup::whereNotNull('up_to_standard')->where('select_route', '朝晖京杭大运河毅行')->count();

//        $res  = '报名人数: ' . $apply_count . '<br>';
//        $res .= '队伍总数: ' . $team_count . '<br>';
//        $res .= '达到要求队伍数: '. $upToTeam . '<br>';
//        return $res;
        return view('count', ['apply_count' => $apply_count, 'team_count' => $team_count, 'upToTeam' => $upToTeam, 'ch' => $chToTeam]);
    }


    /**
     * 给琪琪测试的api
     */
    public function toQq() {
        $apply_count = User::getUserCount();
        $team_count = YxGroup::getTeamCount();
        $upToTeam = YxGroup::whereNotNull('up_to_standard')->where('select_route', '<>', '朝晖京杭大运河毅行')->count();
        $chToTeam = YxGroup::whereNotNull('up_to_standard')->where('select_route', '朝晖京杭大运河毅行')->count();
        $data = array(
            'apply_count' => $apply_count,
            'team_count' => $team_count,
            'pf' => $upToTeam,
            'ch' => $chToTeam
        );

        return RJM(1, '获取成功', $data);
    }

    /**
     * 获取队伍名单
     */
    public function teamDownload() {
        return Excel::download(new GroupExport(), '队伍名单.xlsx');

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
