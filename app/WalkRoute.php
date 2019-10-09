<?php

namespace App;

use App\WalkTime;
use Illuminate\Database\Eloquent\Model;

/**
 * @method static orderBy(string $string, string $string1)
 */
class WalkRoute extends Model
{
    /**
     * 数据表名
     */
    protected $table = 'walk_route';

    /**
     * 可填充字段
     * @return int
     */
    protected $fillable = ['name', 'limit_campus', 'campus_from', 'capacity'];

    public function supportCampus($campus){
        if($this->limit_campus === 'all'){
            return true;
        } elseif ($this->limit_campus === $campus){
            return true;
        } else {
            return false;
        }
    }

    public function caculateCapacity(){
        /**
         * 不含auto的时间配置.
         */
        $result1 = array();
        /**
         * 包含auto的时间配置.
         */
        $result2 = array();
        $walkTimes = WalkTime::cursor();
        $capacityThisPath = $this->capacity;
        $capacityCaculate = 0;

        foreach ($walkTimes as $item) {
            $capacity = $item->getCapacityOf($this->id);
            if (!is_null($capacity)) {
                $temp = [
                    'begin' => $item->begin,
                    'end' => $item->end,
                    'capacity' => $capacity,
                    'remain' => $capacity - $item->submitGroupCountOfWalkTime($item->id)
                ];

                if ($capacity === 'auto') {
                    $result2[] = $temp;
                } else {
                    $result1[] = $temp;
                }
            }
        }

        $result1 = array_values(array_sort($result1, function ($key, $value) {
            return $value['end'];
        }));

        foreach ($result1 as $value) {
            $capacity = $value->capacity;
            if ($capacityCaculate + int($value->capacity) > $capacityThisPath) {
                $remain = $capacityThisPath - $capacityCaculate;
                if ($remain > 0) {
                    $value->capacity = $remain;
                    $capacityCaculate = $capacityThisPath;
                } else {
                    $value->capacity = 0;
                }
            }
        }

        $capacityRemain = $capacityThisPath - $capacityCaculate;
        $capacityRemainCaculate = 0;

        $autoCount = $result2->count();
        foreach ($result2 as $index => $value) {
            if ($index < $autoCount - 1) {
                $eachCount = $capacityRemain / $autoCount;
                $value->$capacity = $eachCount;
                $capacityRemainCaculate += $eachCount;
            } else {
                $value->$capacity = $capacityRemain - $capacityRemainCaculate;
            }
        }

        $result = array();
        foreach (array_merge($result1, $result2) as $item) {
            if ($item->capacity > 0) {
                $result[] = $item;
            }
        }

        return $result;
    }

    public function capacityGroupCountOfWalkTime($walk_time_id){
        $walkTime = WalkTime::find($walk_time_id);
        return $walkTime->getCapacityOf($this->id);
    }

    /**
     * 获取已经提交的队伍在一个线路一个时间段的数量
     */
    public function submitGroupCountOfWalkTime($walk_time_id){
        $count = Group::where('is_submit', true)
            ->where('route_id', $this->id)
            ->where('walk_time_id', $walk_time_id)
            ->get()
            ->count();
        return $count;
    }

    /**
     * 所有的容量(队伍数)
     */
    public static function capacityAll()
    {
        try {
            //$capacity = array_sum(WalkRoute::select('capacity')->get());
        } finally {
            $capacity = 0;
        }


    }

    /**
     * 计算各个路线在出发时间段的限制人数和剩余人数等配置项
     * 详情请见\doc\sqldesign
     */
    public static function caculateConfig()
    {
        $walk_paths = WalkRoute::get();
        return map($walk_paths, function ($item) {
            return [
                'name' => $item->name,
                'limit_campus' => $item->limit_campus,
                'campus_from' => $item->campus_from,
                'capacities' => $item->caculateCapacity()
            ];
        });
    }
}