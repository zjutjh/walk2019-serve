<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class RegisterTime extends Model
{
    /**
     * 数据表名
     */
    protected $table = 'register_time';

    /**
     * 可填充字段
     */
    protected $fillable = ['begin','end','capacity'];

    /**
     * 由配置的数据统计报名开始的时间
     */
    public static function beginAt()
    {
        return WalkTime::select('begin')->orderBy('begin')->first();
    }

    /**
     * 由配置的数据统计报名结束的时间
     */
    public static function endAt(){
        return WalkTime::select('end')->orderBy('end','desc')->first();
    }

    /**
     * 计算出各个时间段的报名人数信息
     */
    public static function caculateConfig(){
        $capacityAll = WalkRoute::capacityAll();
        $capacityGiven = 0;
        $flag = false;
        $result = array();

        $times1 = WalkRoute::where('capacity','<>','auto')->get();

        foreach($times1 as $time1){

            $currentCapacity = int($time1->capacity);
            if ($capacityGiven + $currentCapacity >= $capactiyAll) {
                $currentCapacity = $capacityAll - $capacityGiven;
                $flag = true;
            };

            //将计算好的配置添加到数组
            $result[] = [
                'begin' => $time1->begin,
                'end' => $time1->end,
                'capacity' => $currentCapacity
            ];

            if ($flag) {
                break;
            }
        }

        if (!$flag) {
            $times2 = WalkRoute::where('capacity','auto')->get();
            $times2Count = $times2->count();
            $capacityRemain = $capacityAll - $capacityGiven;
            $capacityRemainCaculated = 0;

            foreach($times2 as $i => $time2){
                if($i < $times2Count - 1){
                    $currentCapacity = $capacityRemain / $times2Count;
                    $capacityRemainCaculated += $currentCapacity;
                } else {
                    $currentCapacity = $capacityRemain - $capacityRemainCaculated;
                }

                $result[] = [
                    'begin' => $time2->begin,
                    'end' => $time2->end,
                    'capacity' => $currentCapacity
                ];
            }
        }

        return $result;
    }

    /**
     * 计算当前的报名配置，包括当前[begin,end]，以及剩余可报的人数（当前时间段，以及总共剩余人数）
     * 如果当前不可报名，则返回null
     * @return array|null
     */
    public static function caculateCurrentConfig(){
        $config = RegisterTime::caculateConfig();
        $capacityAll = WalkRoute::capacityAll();
        $capacityToNow = 0;
        $groupCountToNow = Group::countSumbitToNow();
        $flag = false;

        foreach ($config as $i => $time) {
            if (!$flag && now() >= $time -> begin && now() <= $time -> end) {
                $current = $time;
                $flag = true;
            }
            if (now() >= $time -> begin){
                $capacityToNow += $time -> capacity;
            }
        }

        $remainNow = $capacityToNow - $groupCountToNow;
        $remainAll = $capacityToNow - $groupCountToNow;

        return [
            'begin' => $current -> begin,
            'end' => $current -> end,
            'group_count_sumbit' => $groupCountToNow,
            'group_count' => Group::count(),
            'remain' => $remainNow,
            'remain_total' => $remainAll,
            'capacity' => $capacityAll
        ];
    }


}
