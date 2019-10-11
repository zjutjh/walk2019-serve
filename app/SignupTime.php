<?php

namespace App;

use Carbon\Traits\Timestamp;
use Illuminate\Database\Eloquent\Model;
use phpDocumentor\Reflection\DocBlock\Tags\Since;

class SignupTime extends Model
{
    /**
     * 数据表名
     */
    protected $table = 'signup_time';

    /**
     * 可填充字段
     */
    protected $fillable = ['begin','end','capacity'];

    public $timestamps = false;

    /**
     * @return Timestamp|null
     */
    public static function beginAt()
    {
        $first = SignupTime::orderBy('begin','asc')->first();
        if ($first == null) {
            return null;
        }
        return $first->begin;
    }

    /**
     * 由配置的数据统计报名结束的时间
     */
    public static function endAt(){
        $last = SignupTime::orderBy('end','desc')->first();
        if ($last == null){
            return null;
        }
        return $last->end;
    }

    /**
     * 计算出各个时间段的报名人数信息
     */
    public static function caculateConfig(){
        $times = SignupTime::all();
        return $times;
    }

    public static function capacityToNow(){
        $times = SignupTime::where('begin','<=',now())->get();
        //echo $times;
        $capacity = 0;
        foreach($times as $time){
            $capacity += $time->capacity;
        }
        return $capacity;
    }

    public static function capacityAll(){
        $times = SignupTime::all();
        $capacity = 0;
        foreach($times as $time){
            $capacity += $time->capacity;
        }
        return $capacity;
    }

    /**
     * 计算当前的报名配置，包括当前[begin,end]，以及剩余可报的人数（当前时间段，以及总共剩余人数）
     * 如果当前不可报名，则返回null
     * @return array|null
     */
    public static function caculateCurrentConfig(){
        $current = SignupTime::where('begin','<=',now())->where('end','>',now())->first();
        if($current == null){
            return null;
        }

        $capacityAll = SignupTime::capacityAll();
        $capacityToNow = SignupTime::capacityToNow();
        $groupCountToNow = Group::submitedCount();

        $remainNow = $capacityToNow - $groupCountToNow;
        $remainAll = $capacityAll - $groupCountToNow;

        return [
            'begin' => $current -> begin,
            'end' => $current -> end,
            'group_count_sumbit' => $groupCountToNow,
            'group_count' => Group::all()->count(),
            'remain' => $remainNow,
            'remain_total' => $remainAll,
        ];
    }
}
