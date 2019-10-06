<?php

namespace App\Console\Commands;

use App\YxGroup;
use Illuminate\Console\Command;

class SendFail extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'send:fail';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Command description';

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle()
    {
        $groups = YxGroup::all();
        $data = [
            'first' => "模拟报名失败",
            'keyword1' => '报名失败',
            'keyword2' => '消息通知',
            'keyword3' => date('Y-m-d H:i:s', time()),
            'remark'   => '收到请忽略'
        ];
        foreach ($groups as $group) {
            if (!$group->up_to_standard) {
                echo "{$group->id}:{$group->name}\n";
                $members = $group->members()->get();
                foreach ($members as $member) {
                    echo "{$member->id}:{$member->name}\n";
                    $member->notify($data);
                }
            }


        }
    }
}
