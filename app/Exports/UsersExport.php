<?php

namespace App\Exports;

use App\User;
use App\Group;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;

class UsersExport implements FromCollection, WithMapping, WithHeadings
{
    /**
    * @return \Illuminate\Support\Collection
    */
    public function collection()
    {
        $user = User::whereHas('state', function($query) {
            $query->where('state', '>', 0)->where('state', '<>', 5);
        })->get();
        return $user;
    }


    /**
     * @param User $user
     * @return array
     */
    public function map($user): array
    {

        $group = Group::find($user->group_id);
        return [
          $user->id,
          $user->name,
          $user->sex,
          $user->campus,
          !$group ? '未组队' : $group->select_route,
          $user->height,
          $user->birthday,
          $user->identity,
          $user->sid,
          $user->phone,
          $user->wx_id,
          $user->qq,
          $user->group_id,
          $group->is_submit
        ];
    }


    public function headings(): array
    {
        return [
            'id',
            '姓名',
            '性别',
            '校区',
            '路线',
            '身高',
            '生日',
            '身份',
            '学号',
            '电话号码',
            '微信',
            'qq',
            '系统队伍号',
            '正式队伍号',
            '队长id',
            '身份证hash',
            '是否组队成功'
        ];
    }
}
