<?php

namespace App\Helpers;

/**
 * 简要的常量映射
 */

class _state
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

class _notify
{
    /**
     * 创建队伍 -> 通知队长
     */
    const create = 0;
    /**
     * 更新队伍信息 -> 通知所有人
     */
    const update = 1;
    /**
     * 解散队伍 -> 通知所有人
     * 主动解散 | 有人退出队伍，人数不够 |
     */
    const dismiss = 2;
    /**
     * 离开队伍 -> 通知组长
     */
    const leave = 3;
    /**
     * 锁定队伍 -> 通知所有人
     */
    const submit = 4;
    /**
     * 解锁队伍 -> 通知所有人
     */
    const unsubmit = 5;
    /**
     * 被踢出队伍 -> 通知被踢的人
     */
    const throwed = 6;
    /**
     * 加入队伍 -> 通知所有人
     * [可以不通知]
     */
    const add = 7;
    /**
     * 因为删除队伍导致申请的取消 -> 通知取消的人
     */
    const apply_cancel_group = 8;
    /**
     * 因为删除队伍导致离队 -> 通知离队的人
     */
    const throwed_group = 9;
    /**
     * 有人提交了申请 -> 通知了组长
     */
    const apply = 10;
    /**
     * 队长驳回申请 -> 通知取消的人
     */
    const apply_cancel_captain = 11;
    /**
     * 队长同意申请 -> 通知加入的人
     */
    const apply_agree = 12;
    /**
     * 成员撤回了申请 -> 通知队长
     */
    const apply_cancel_user = 13;
}
