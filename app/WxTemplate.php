<?php


namespace App;


class  WxTemplate
{
    const  Test = [
        'first' => '你已经创建了一个队伍',
        'keyword1' => '队伍创建',
        'keyword2' => '创建成功',
        'keyword3' => 'ZJUT JH',
        'remark' => '快邀请大家来加入你的队伍把！ 点击查看详情',
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
    const Knit = [
        'first' => '你已经被队长移出队伍',
        'keyword1' => '移出队伍',
        'keyword2' => '移出',
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
        'first' => '你加入的队伍已经提交',
        'keyword1' => '队伍提交',
        'keyword2' => '队伍提交',
        'keyword3' => 'ZJUT JH',
        'remark' => '请于11月5日,在报名平台查看队伍编号',
    ];

    //解除锁定队伍时
    const Unsubmit = [
        'first' => '你加入的队伍已经取消提交',
        'keyword1' => '队伍取消提交',
        'keyword2' => '队伍取消提交',
        'keyword3' => ['keyword3', '#000000'],
        'remark' => '你的队伍已经取消了提交,将不视为报名参加',
    ];
    const Success = [
        'first' => '你的队伍成功报名精弘毅行',
        'keyword1' => '报名成功',
        'keyword2' => '报名成功',
        'keyword3' => ['keyword3', '#000000'],
        'remark' => '点击查看队伍编号',
    ];
    const Failed = [
        'first' => '你的队伍没有成功报名精弘毅行',
        'keyword1' => '队伍报名失败',
        'keyword2' => '队伍报名失败',
        'keyword3' => ['keyword3', '#000000'],
        'remark' => '你的队伍由于没有提交或者,报名失败,欢迎明年报名精弘毅行',
    ];

}
