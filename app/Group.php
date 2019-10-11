<?php

namespace App;


use Exception;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use App\WalkRoute;

class Group extends Model
{
    protected $fillable = [
        'name', 'logo', 'capacity', 'description',  'captain_id', 'route_id', 'is_submit'
    ];

    /**
     * 追加字段
     * @var array
     */
    protected $appends = ['members'];

    /**
     *  获取所有组员
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function members()
    {
        return $this->hasMany('App\User');
    }

    /**
     * 获取队员
     */
    public function getMembersAttribute()
    {
        return $this->members()->count();
    }

    /**
     * 判断该组的成员是否支持某条线路
     * @param $walkRoute
     * @return bool
     */
    public function supportWalkPath($walkRoute)
    {
        $members = $this->members();
        foreach ($members as $member)
            if (!$walkRoute->supportCampus($member->campus))
                return false;
        return true;
    }

    /**
     * 获取队伍数量
     * @return mixed
     */
    static public function getGroupCount()
    {
        return Group::count();
    }

    public static function successCount(){
        return Group::where('is_locked',true)->count();
    }

    /**
     * 删除队伍
     * @return bool|null
     * @throws \Exception
     */
    public function delete()
    {
        $members = $this->members()->get();
        $applies = Apply::where('apply_team_id', $this->id)->get();


        foreach ($applies as $apply) {
            $user = User::find($apply->apply_id);

            //$applies->notify();
            //notify(_notify::apply_cancel_group, $apply->apply_id, $this->id);

            $apply->delete();
            $user.save();
        }


        foreach ($members as $member) {
            $member->leaveGroup();

            if ($member->id !== $this->captain_id) {

                //notify
            }
        }
        return parent::delete();
    }

    public static function submitedCount()
    {
        return Group::where('is_submit', true)->get()->count();
    }
}
