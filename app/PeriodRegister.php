<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Carbon;

class PeriodRegister extends Model
{
    protected $fillable = ['begin','end','limit_group_count'];

    public static function remainCount(){
        $periods = PeriodRegister::where('end','<',now);
        $count = 0;
        foreach ($periods as $period) {
            $count += $period->limit_person_count;
        }
        return $count - Group::successCount();
    }

    public static function current(){
        $period = PeriodRegister::where('end','<',now)->orderBy('end','desc')->first();
        return [
            'begin' => $period->begin,
            'end' => $period->end,
            'remain' => $period-> remainCount()
        ];
    }

    public static function totalBegin(){
        $period = PeriodRegister::get()->orderBy('begin')->first();
        return $period->begin;
    }

    public static function totalEnd(){
        $period = PeriodRegister::get()->orderBy('end','desc')->first();
        return $period->end;
    }
}
