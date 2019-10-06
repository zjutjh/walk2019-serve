<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use App\User;

class YxRoute extends Model
{
    protected $fillable = ['name','title','limit_campus','limit_group_count'];

    /**
     * 获取支持的线路
     */
    public static function getSupportRoute($campus){
        $routes = Route::where('limit_campus',$campus)->orWhere('limit_campus','all')->get();
        return $routes;
    }

    public function allowJoin(User $user){
        if($this->limit_campus === 'all' or $this->limit_campus === $user->campus){
            return true;
        } else {
            return false;
        }
    }

    public function isFull(){
        $count = Group::where('is_locked',true)->where('route',$this->name)->count();
        if ($count >= $this-> $limit_group_count) {
            return true;
        } else {
            return false;
        }
    }

    public static function fromName($name){
       return YxRoute::where('name', $name)->first();
    }
}
