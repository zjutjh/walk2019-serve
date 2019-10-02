<?php

namespace App\Jobs;

use GuzzleHttp\Client;
use Illuminate\Bus\Queueable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;

class SendTemplate implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;


    /**
     * [
     *   'openid' => 'xxxx',
     *   'url' => 'xxxx',
     *   'data' => [
     *    'first' => 'xxx'
     * ]
     *
     * ]
     * @var
     */
    public $config;

    /**
     * Create a new job instance.
     *
     * @return void
     */
    public function __construct($config)
    {
        $this->config = $config;
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
        $client = new Client();
        echo json_encode($this->config);
        $res = $client->post(config('api.jh.template'), [
            'json' => $this->config
        ]);
//        echo $res->getBody();
    }
}
