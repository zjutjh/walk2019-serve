<?php
/**
 * Created by PhpStorm.
 * User: 70473
 * Date: 2018/10/9
 * Time: 11:12
 */

return [
    'jh' => [
        'center' => 'http://user.zjut.com/api.php',
        'template' => 'https://server.wejh.imcr.me/api/notification/walk',
        'oauth' => 'https://craim.net/oauth/index.php?url=',
        'accessToken'=>'https://server.wejh.imcr.me/api/wechat/accessToken?passport=2002jhwl',
    ],
    'system' => [
        'BeginTime' => env('BeginTime'),
        'EndTime' => env('EndTime'),
        'ServerUrl'=> env('APP_URL'),
        'IsEnd' => false,
        'minGroupPeople' => env("minGroupPeople"),
        'maxGroupPeople' => env("maxGroupPeople"),
    ],
    'wx' => [
        'WECHAT_REDIRECT' => env('WECHAT_REDIRECT'),
        'WECHAT_APPID' => env('WECHAT_APPID'),
        'WECHAT_SECRET' => env('WECHAT_SECRET')
    ]
];
