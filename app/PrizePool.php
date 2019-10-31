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
     * @param String $campus get the prize pool of campus now.
     * @return mixed data.
     */
    public static function current(String $campus){
        return PrizePool::where('campus', $campus)->get();
    }

    /**
     * @param int $group_id 队伍编号
     * @return mixed
     */
    public static function next(int $group_id){
        $group = Group::find($group_id);
        if($group === null){
            return null;
        } else if($group->is_submit === false){
            return 1;
        } else if($group->prize !== null){
            return 2;
        }
        $route = WalkRoute::find($group->route_id);

        $c = PrizePool::current($route->campus);

        $prize = PrizePool::select($c);

        $prize->count += 1;
        $prize->save();

        $group->prize=$prize['id'];
        $group->save();

        //TODO 发送通知

        return $prize;
    }

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

}
