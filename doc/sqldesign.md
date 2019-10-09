# 数据库设计

## table users

| 字段名 | 类型 | 其他设置 | 备注 | 校验 |
| --- | --- | --- | --- | --- |
| id | int | increment | | |
| name | string | nullable | 姓名 | 2..32 |
| email | string | nullable | 邮箱 | format:email |
| sex | string | nullable | 性别 | enum['男','女'] |
| logo | string | nullable | 头像 | ..1000;url |
| campus | string | nullable | 校区 |  |
| phone | string | nullable | 电话号码 | format:phonev3 |
| id_card | string | nullable | 身份证号码,md5 | format:id_card |
| openid | string | nullable | 微信openid | |
| qq | string | nullable | qq | format:qq |
| wx_id | string | nullable | 微信号 | format:wx_id |
| identity | string | nullable | 报名身份 | ['学生', '教职工', '校友', '其他'] |
| height | string | nullable | 升高 | 50..300 |
| birthday | string | nullable | 出生年月 | format:datetime |
| sid | string | nullable | 学生学号 | format:number |
| school | string | nullable | 学院 | format:number |
| group_id | integer | nullable | 队伍编号 | |
| state | tinyInteger | default:0 | 队伍状态 | 0 未报名 1已经报名未组队 2 正在申请队伍 3 有队伍（队长）4 有队伍（队员）5 未填写信息 |
| timestamps | 

其中`campus`校区字段写在`config\campus.php`中，因为考虑到这种设置的稳定性，所以并没有考虑做到数据库中

## table groups

| 字段名 | 类型 | 其他设置 | 备注 | 校验 |
| --- | --- | --- | --- | --- |
| id | integer | increment | | |
| name | string | | 队伍名称 | 2..32 |
| logo | string | | 队伍图片 | ..1000;url |
| capacity | tinyInteger | | 队伍人数 | | 
| decription | text | | 队伍简介 | |
| captain_id | integer | | 队长id | |
| route_id | integer | | 毅行路线的id |  |
| walk_time_id | integer | | 出发时间的id | |
| is_submit | boolean | default:false | 是否提交队伍 | |

## table apply

| 字段名 | 类型 | 其他设置 | 备注 | 校验 |
| --- | --- | --- | --- | --- |
| id | integer | increment | | |
| apply_id | integer | | 申请者id | |
| apply_team_id | integer | | 申请队伍id | |

备注：少了管理模块，下面是管理模块的数据库设计

行走的路线的设置

## table walk_path

| 字段名 | 类型 | 其他设置 | 备注 | 校验 |
| --- | --- | --- | --- | --- |
| id | integer | increment | | |
| name | string | | 路线的名称，选项中显示的就是这个名称 | |
| limit_campus | string | | 此条线路限制的校区，all表示没有限制，其他以校区名来进行限制 |
| campus_from | string | | 出发校区，主要用于可选的早起警告功能 | |
| capacity | integer | | 此条线路限制的队伍数，注意，此字段的限制优先于walk_time的相应配置 |

出发时间配置表，主要用于进行分时间段的分流

`caculateConfig`返回数组的格式

```php
[
    $route_id => [
        'name' => $name,
        'limit_campus' => $limit_campus,
        'campus_from' => $campus_from,
        'capacities' => [
            [
                'begin' => $begin,
                'end' => $end,
                'capacity' => $capacity,
                'remain' => $remain
            ]
        ]
    ]
]
```

## table walk_time

| 字段名 | 类型 | 其他设置 | 备注 | 校验 |
| --- | --- | --- | --- | --- | 
| id | integer | increment | | |
| begin | timestamp | datetime | 出发时间 | |
| end | timestamp | datetime | 结束出发时间 | | 
| capacity_array | string | format_type | 每个路线的队伍数限制 |

capacity_array的格式，注意，不标明的默认为0，auto表示使用自动算法分配，其值由capacity来标识。

```
0,100;1,1000;2,800
1,auto;
```

为了应对报名限流，故特意指定此表以扩展功能

## table register_time

| 字段名 | 类型 | 其他设置 | 备注 | 校验 | 
| --- | --- | --- | --- | --- |
| id | integer | increment | | |
| begin | integer | datetiem | 开始报名时间 | | |
| end | integer | datetime | 结束时间 | | |
| capacity | string | | 该时间段限制报名的队伍数(累计算法) | | 

capacity的格式，为数字就表示人数，auto表示自动分配。