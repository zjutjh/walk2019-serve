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

## /index/info

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
