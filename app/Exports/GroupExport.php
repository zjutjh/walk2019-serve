<?php

namespace App\Exports;

use App\User;
use App\Group;
use App\WalkRoute;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;
use PhpOffice\PhpSpreadsheet\Shared\Date;

class GroupExport implements FromCollection, WithMapping, WithHeadings
{
    /**
     * @return \Illuminate\Support\Collection
     */
    public function collection()
    {
        return Group::all();
    }


    /**
     * @param mixed $row
     *
     * @return array
     */
    public function map($row): array
    {
        $captain = User::find($row->captain_id);
        $members = $row->members()->where('id', '<>', $row->captain_id)->get();
        $names = [];
        foreach ($members as $member) {
            $names [] = ['name' => $member->name, 'id' => $member->id, 'phone' => $member->phone, 'state' => $member->state, 'identity' => $member->identity];
        }

//        $names = array_sort($names, function($value) {
//            return $value['state'];
//        });

        return [
            $row->is_submit === 0 ? '失败队伍' : $row->No,
            $row->id,
            $row->name,
            $row->description,
            $row->route_id,
            WalkRoute::find($row->route_id)->name,
            count($members) + 1,
            $captain->identity,
            $captain->name,
            $captain->id,
            $captain->phone,

            isset($names[0]) ? $names[0]['identity'] : '',
            isset($names[0]) ? $names[0]['name'] : '',
            isset($names[0]) ? $names[0]['id'] : '',
            isset($names[0]) ? $names[0]['phone'] : '',

            isset($names[1]) ? $names[1]['identity'] : '',
            isset($names[1]) ? $names[1]['name'] : '',
            isset($names[1]) ? $names[1]['id'] : '',
            isset($names[1]) ? $names[1]['phone'] : '',

            isset($names[2]) ? $names[2]['identity'] : '',
            isset($names[2]) ? $names[2]['name'] : '',
            isset($names[2]) ? $names[2]['id'] : '',
            isset($names[2]) ? $names[2]['phone'] : '',

            isset($names[3]) ? $names[3]['identity'] : '',
            isset($names[3]) ? $names[3]['name'] : '',
            isset($names[3]) ? $names[3]['id'] : '',
            isset($names[3]) ? $names[3]['phone'] : '',

            isset($names[4]) ? $names[4]['identity'] : '',
            isset($names[4]) ? $names[4]['name'] : '',
            isset($names[4]) ? $names[4]['id'] : '',
            isset($names[4]) ? $names[4]['phone'] : '',

        ];
    }

    /**
     * @return array
     */
    public function headings(): array
    {
        return [
            '队伍参与活动号码',
            '队伍报名系统号码',
            '队伍名称',
            '队伍描述',
            '路线id',
            '队伍路线',
            '队伍人数',
            '队长身份',
            '队长',
            '队长-id',
            '队长手机',
            '队员2身份',
            '队员2',
            '队员2-id',
            '队员2手机',
            '队员3身份',
            '队员3',
            '队员3-id',
            '队员3手机',
            '队员4身份',
            '队员4',
            '队员4-id',
            '队员4手机',
            '队员5身份',
            '队员5',
            '队员5-id',
            '队员5手机',
            '队员6身份',
            '队员6',
            '队员6-id',
            '队员6手机'

        ];
    }
}
