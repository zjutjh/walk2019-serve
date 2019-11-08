<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class PrizePool extends Model
{
    /**
     * @var string 数据库表名
     */
    protected $table = 'prize_pool';

    /**
     * @var bool 关闭时间戳选项
     */
    public $timestamps = false;

    protected $fillable = ['count'];

    /**
     * 当前路线奖项的状态
     * @param int $route_id get the prize pool of campus now.
     * @return mixed data.
     */
    public static function current(int $route_id){
        return PrizePool::where('route_id', $route_id)->get();
    }

    /**
     * 给某个队伍抽一次奖
     * @param int $group_id 队伍编号
     * @return mixed
     */
    public static function next(int $group_id){
        $group = Group::find($group_id);
        if($group === null){
            return null;
        } else if($group->is_submit === false){
            return 1;
        } else if($group->prize_id !== null){
            return 2;
        }
        $route = WalkRoute::find($group->route_id);

        $c = PrizePool::current($route->id);

        $prize = PrizePool::select($c);

        if($prize == null){
            return 3;
        }

        $prize->count += 1;
        $prize->save();

        $group->prize_id=$prize['id'];
        $group->save();

        //TODO 发送通知

        return $prize;
    }

    /**
     * 确认领奖
     * @param int $group_id
     * @return mixed
     */
    public static function verify(int $group_id){
        $group = Group::find($group_id);
        if($group === null){
            return null;
        }
        $prize_id = $group->prize_id;
        $prize_get = $group->prize_get;
        if($prize_id === null){
            return 1;
        } else if($prize_get == true ){ //true
            return 2;
        }

        $group->prize_get = true;
        $group->save();

        return PrizePool::find($prize_id);
    }

    /**
     * 选择一个奖项，但不进行操作。
     * @param $c
     * @return mixed
     */
    public static function select($c){
        $result = [];
        $current = 0;
        foreach($c as $value){
            $remain = $value['capacity'] - $value['count'];
            if($remain > 0){
                $result[] = [
                    'id'=>$value['id'],
                    'remain'=>$remain,
                    'min'=>$current,
                    'max'=>$current+$remain
                ];
                $current+=$remain;
            }
        }

        $nextValue = rand(0, $current -1);

        foreach($result as $value){
            if($nextValue >= $value['min'] && $nextValue < $value['max']){
                return PrizePool::find($value['id']);
            }
        }

        return null;
    }


    public static function getData(){
        $mapping = function ($route) {
            $data = PrizePool::current($route['id']);

            return [
                'route'=> $route,
                'data' => $data
            ];
        };

        $route = WalkRoute::all()->toArray();

        return array_map($mapping, $route);
    }
}
