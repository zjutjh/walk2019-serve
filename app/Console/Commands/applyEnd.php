<?php

namespace App\Console\Commands;

use App\YxState;
use Illuminate\Console\Command;

class applyEnd extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'apply:end';

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
        $yxState = YxState::where('id', 0)->first();
        $yxState->state = 1;
        $yxState->save();
        echo '关闭';
    }
}
