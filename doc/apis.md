# API接口文档

| route | body | comment |
| --- | --- | --- |
| /oauth | | 微信oauth跳转 |
| /wx/login | | 微信登录 |
| /index/info | | 主页信息 |
| /user/info | | 用户信息 |
| /user/register | | 用户注册 |
| /user/update | | 用户更新 |
| /group/list | | 队伍列表 |
| /group/create | | 创建队伍 |
| /group/break| | 解散队伍 |
| /group/submit | | 提交队伍 |
| /group/search | | 搜索队伍 |
| /group/members/list | | 成员名单 |
| /group/members/delete | | 删除成员 |
| /group/info | | 队伍详细信息 |
| /group/update | | 队伍信息更新 |
| /group/unsumbit | | |
| /group/leave | | 离开队伍 |
| /apply/list | | 申请者列表 |
| /apply/agree | | 同意申请 |
| /apply/refuse | | 拒绝申请 |
| /apply/do | | 提交申请 |
| /apply/delete | | 撤回申请 |
| /apply/cout | | 申请的数量 |
| /config/signup | | 

## info/config

### /index/info

获取主页信息

```json
{
    'begin': '毅行报名开始时间',
    'end': '毅行报名结束时间',
    'state': '当前报名状态 enum['nostart','doing','end']',
    'apply_count': '报名的人数',
    'current': {
        'begin': '当前时间段报名开始时间',
        'end': '当前时间段报名结束时间',
        'group_count_sumbit': '已提交的队伍数',
        'group_count': '报名的队伍数',
        'remain': '当前时间段可以提交剩余的队伍数',
        'remain_total': '所有时间段可以提交剩余的队伍数',
        'capacity': '队伍数容量(总数)',
    }
}
```



### /config/signup

获取与报名(signup)有关的配置

```json
{
    [
        {
            'begin': '报名开始的时间',
            'end': '报名结束的时间',
            'capacity': '此阶段报名限制的人数'
        }
    ]
}
```

### /config/walkpath

获取与各条线路有关的配置

```json
{
    [
        {
            'name': '线路的名称',
            'limit_campus': '限制的校区 enum['all',config('campus')]',
            'campus_from': '出发的校区 enum[config('campus'))]',
            'capacities': [
                {
                    'begin': '开始出发的时间',
                    'end': '结束出发的时间',
                    'capacity': '此线路此时间段的容量',
                    'remain': '剩余的队伍数'
                }
            ]
        }
    ]
}
```

## login

### /oauth

微信的openoauth的跳转

### /wx/login

使用code来进行登录

## user

### /user/register

注册报名

| params | typeano | comment |
| --- | --- | --- |
| name | string | 姓名 |
| email | string | 邮箱 |
| campus | string | 校区 |
| id_card | string | 身份证 |
| wx_id | string | optional,微信号 |
| qq | string | optional,qq号 |
| phone | string | 手机号码 |
| identity | string | 身份 |
| height | integer | 身高 |
| sid | string | only:identity='学生',学号 |
| school | string | only:identity='学生',学院 |

### /user/info 

获取用户信息

TODO: 待完善

### /user/update

更新用户信息

TODO: 待完善

### /user/dismiss

解除绑定

我觉得应该添加这个方法，因为他可能不参加了，或者由于某某原因信息输入错误等需要结束session的绑定。

## group

### /group/list

队伍列表

### /group/create

创建队伍

name
logo
capacity
description
captain_id
route_id
walk_time_id

### /group/break

解散队伍

### /group/submit

提交队伍(视为报名)

### /group/search

搜索队伍

### /group/member/list

获取队员名单

### /group/member/delete

踢掉成员

### /group/info

队伍信息

### /group/update

更新队伍信息

推荐支持更改路线和时间段的信息，并能够提示路线和时间段的报名人数。

### /group/unsubmit

反提交队伍(视为暂时放弃报名)

### /group/leave

离开队伍

## apply

### /apply/list 获取申请者列表

### /apply/agree 同意申请

### /apply/refuse 拒绝申请

### /apply/do 提交申请

### /apply/delete 撤回申请

### /apply/count 申请者的数量???