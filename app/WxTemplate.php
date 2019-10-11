<?php


namespace App;


class  WxTemplate
{
    const  Test =  [
       'first' => 'Test First',
       'keyword1' => 'keyword1',
       'keyword2' => 'keyword2',
       'keyword3' => ['keyword3', '#000000'],
       'remark' => ['Test remark', '#fdfdfd'],
   ];
   //创建队伍时
    const  Create =  [
        'first' => 'Test First',
        'keyword1' => 'keyword1',
        'keyword2' => 'keyword2',
        'keyword3' => ['keyword3', '#000000'],
        'remark' => ['Test remark', '#fdfdfd'],
    ];
    
    const  Apply =  [
        'first' => 'Test First',
        'keyword1' => 'keyword1',
        'keyword2' => 'keyword2',
        'keyword3' => ['keyword3', '#000000'],
        'remark' => ['Test remark', '#fdfdfd'],
    ];
    //队伍信息更改时
    const Update =  [
        'first' => 'Test First',
        'keyword1' => 'keyword1',
        'keyword2' => 'keyword2',
        'keyword3' => ['keyword3', '#000000'],
        'remark' => ['Test remark', '#fdfdfd'],
    ];
    //删除队伍时
    const Delete =  [
        'first' => 'Test First',
        'keyword1' => 'keyword1',
        'keyword2' => 'keyword2',
        'keyword3' => ['keyword3', '#000000'],
        'remark' => ['Test remark', '#fdfdfd'],
    ];

    //离开队伍时
    const Leave =  [
        'first' => 'Test First',
        'keyword1' => 'keyword1',
        'keyword2' => 'keyword2',
        'keyword3' => ['keyword3', '#000000'],
        'remark' => ['Test remark', '#fdfdfd'],
    ];

    //以一种惊悚的方式离开队伍(队伍没了)
    const ShockLeave =  [
        'first' => 'Test First',
        'keyword1' => 'keyword1',
        'keyword2' => 'keyword2',
        'keyword3' => ['keyword3', '#000000'],
        'remark' => ['Test remark', '#fdfdfd'],
    ];

    //锁定队伍时
    const Submit  =  [
        'first' => 'Test First',
        'keyword1' => 'keyword1',
        'keyword2' => 'keyword2',
        'keyword3' => ['keyword3', '#000000'],
        'remark' => ['Test remark', '#fdfdfd'],
    ];

    //解除锁定队伍时
    const Unsubmit  =  [
        'first' => 'Test First',
        'keyword1' => 'keyword1',
        'keyword2' => 'keyword2',
        'keyword3' => ['keyword3', '#000000'],
        'remark' => ['Test remark', '#fdfdfd'],
    ];

    const CancelApply  =  [
        'first' => 'Test First',
        'keyword1' => 'keyword1',
        'keyword2' => 'keyword2',
        'keyword3' => ['keyword3', '#000000'],
        'remark' => ['Test remark', '#fdfdfd'],
    ];
}
