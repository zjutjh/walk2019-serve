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
    protected $fillable = ['name',  'capacity'];

    public $timestamps = false;

    public static function getId($name){
        $route =  WalkRoute::where('name',$name)->first();
        return $route->id;
    }

    public static function capacityAll(){
        $capacities = WalkRoute::select('capacity')->get();
        $capacity = 0;
        foreach ($capacities as $item){
            $capacity += $item->capacity;
        }
        return $capacity;
    }

    public function remainCount(){
        $groupCount = Group::where('route_id', $this->id)->get()->count();
        return $this->capacity - $this->id;
    }
}
