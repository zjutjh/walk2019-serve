<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use App\Helpers\funciton;

class WalkTime extends Model
{
    /**
     * 数据表名
     */
    protected $table = 'walk_time';

    /**
     * 可填充字段
     */
    protected $fillable = ['begin', 'end', 'capacity_array'];

    /**
     * 获取一个walkpath的容量
     */
    public function getCapacityOf($walkpath_id){
        $capacityArray = _getCapacityArray();
        return $capacityArray[$walkpath_id];
    }

    /**
     * 设置一个walkpath的容量
     */
    public function setCapacityOf($walkpath_id, $capacity){
        $capacityArray = _getCapacityArray();
        if (is_null($capacity)) {
            unset($capacityArray[$walkpath_id]);
        } else {
            $capacityArray[$walkpath_id] = $capacity;
        }
    }

    /**
     * 工具函数，处理capacity_array
     */
    private function _getCapacityArray(){
        $capacityArray = explode($this->capacity_array,';');
        $result = array();
        foreach($capacityArray as $item){
            $keyValueArray = explode($item,',');
            $result[] = [
                $keyValueArray[0] => $keyValueArray[1]
            ];
        }
        
        return $result;
    }

    /**
     * 工具函数，处理capacity_array
     */
    private function _setCapacityArray($array){
        $result = join(';',map($array, function($item){
            return join(',',$item);
        }));
    }
}
