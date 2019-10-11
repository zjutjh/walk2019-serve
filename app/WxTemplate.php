<?php


namespace App;


class  WxTemplate
{
    const  Test = [
        'first' => 'Test First',
        'keyword1' => 'keyword1',
        'keyword2' => 'keyword2',
        'keyword3' => ['keyword3', '#000000'],
        'remark' => ['Test remark', '#fdfdfd'],
    ];
    //创建队伍时
    const  Create = [
        'first' => '你已经创建了一个队伍',
        'keyword1' => '队伍创建',
        'keyword2' => '创建成功',
        'keyword3' => 'ZJUT JH',
        'remark' => '快邀请大家来加入你的队伍把！ 点击查看详情',
    ];

    const  Apply = [
        'first' => '有人申请加入你的队伍',
        'keyword1' => '队友申请',
        'keyword2' => '申请',
        'keyword3' => 'ZJUT JH',
        'remark' => '点击查看详情',
    ];
    //队伍信息更改时
    const Agree = [
        'first' => '你的申请已经批准',
        'keyword1' => '加入队伍',
        'keyword2' => '加入',
        'keyword3' => 'ZJUT JH',
        'remark' => '点击查看详情',
    ];
    const Refuse = [
        'first' => '你的申请已经被拒绝',
        'keyword1' => '加入队伍',
        'keyword2' => '拒绝',
        'keyword3' => 'ZJUT JH',
        'remark' => '点击查看详情',
    ];
    //删除队伍时
    const Delete = [
        'first' => '你申请或者加入的队伍已经解散',
        'keyword1' => '队伍解散',
        'keyword2' => '解散',
        'keyword3' => 'ZJUT JH',
        'remark' => '点击查看详情',
    ];


    //锁定队伍时
    const Submit = [
        'first' => 'Test First',
        'keyword1' => 'keyword1',
        'keyword2' => 'keyword2',
        'keyword3' => ['keyword3', '#000000'],
        'remark' => ['Test remark', '#fdfdfd'],
    ];

    //解除锁定队伍时
    const Unsubmit = [
        'first' => 'Test First',
        'keyword1' => 'keyword1',
        'keyword2' => 'keyword2',
        'keyword3' => ['keyword3', '#000000'],
        'remark' => ['Test remark', '#fdfdfd'],
    ];

}
