<?php

namespace App\Console\Commands;

use App\SuccessTeam;
use App\YxGroup;
use Illuminate\Console\Command;

class CreateTeam extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'create:success';

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
        $groups = YxGroup::whereNotNull('up_to_standard')->where('select_route', '<>', '朝晖京杭大运河毅行')->oldest('up_to_standard')->orderBy('select_route')->get();
        foreach ($groups as $group) {
            $success = new SuccessTeam();
            $success->yx_group_id = $group->id;
            $success->save();
            echo "{$success->id}:{$group->id}:{$group->name}\r\n";

            if (SuccessTeam::count() > 1200) {
                break;
            }
        }
    }
}
