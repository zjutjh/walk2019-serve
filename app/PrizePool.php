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

    public static function current(string $title){
        return PrizePool::where('title',$title)->get();
    }

    /**
     * 给某个队伍抽一次奖
     * @param int $group_id 队伍编号
     * @return mixed
     */
    public static function next(string $title,int $group_id){
        $group = Group::find($group_id);
        if($group === null){
            return null;
        } else if($group->is_submit === false){
            return 1;
        } else if($group->prize_id !== null){
            return 2;
        }

        $c = PrizePool::current($title);

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
        $prize = PrizePool::find($prize_id);
        $prize->accept_count += 1;
        $prize->save();
        $group->prize_get = true;
        $group->save();

        return 3;
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
        $data = [];
        $result = [];
        $prizePools = PrizePool::all()->toArray();
        foreach($prizePools as $value){
            $data[$value['title']][] = $value;
        }
        foreach($data as $key=>$value){
            $result[] = [
                'title' => $key,
                'data' => $value
            ];
        }

        return $result;
    }
}
