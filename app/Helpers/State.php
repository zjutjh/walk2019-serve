<?php


namespace App\Helpers;


class State
{
    /**
     * 没有报名[默认项]
     */
    const no_signup = 0;
    /**
     * 以报名，没有加入队伍
     */
    const no_entered = 1;
    /**
     * 正在申请中
     */
    const appling = 2;
    /**
     * 有队伍(队长)
     */
    const captain = 3;
    /**
     * 有队伍(队员)
     */
    const member = 4;
    /**
     * 未填写信息
     */
    const no_fillinfo = 5;
}
