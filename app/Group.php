<?php

namespace App;


use App\Notifications\Wechat;
use Exception;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use App\Helpers\State;

/**
 * @property mixed id
 * @property mixed captain_id
 * @method static orderBy(string $string, string $string1)
 */
class Group extends Model
{
    protected $fillable = [
        'name', 'logo', 'capacity', 'description', 'captain_id', 'route_id', 'is_submit', 'No'
    ];

    /**
     * 追加字段
     * @var array
     */
    protected $appends = ['members', 'route', 'captain_name'];

    /**
     *  获取所有组员
     * @return HasMany
     */
    public function members()
    {
        return $this->hasMany('App\User');
    }

    /**
     *  获取所有组员
     * @return HasMany
     */
    public function getRouteAttribute()
    {
        return WalkRoute::where('id', $this->route_id)->first()->name;
    }

    /**
     *  获取所有组员
     * @return HasMany
     */
    public function getCaptainNameAttribute()
    {
        return User::where('id', $this->captain_id)->first()->name;
    }

    /**
     * 获取队员
     */
    public function getMembersAttribute()
    {
        return $this->members()->count();
    }


    /**
     * 获取队伍数量
     * @return mixed
     */
    static public function getGroupCount()
    {
        return Group::count();
    }

    /**
     * 删除队伍
     * @return bool|null
     * @throws Exception
     */
    public function delete()
    {
        $members = $this->members()->get();
        $applies = Apply::with('user')->where('apply_team_id', $this->id)->get();


        foreach ($applies as $apply) {
            $user = $apply->user();
            $user->notify(new Wechat(WxTemplate::Delete));
            $apply->delete();
        }


        foreach ($members as $member) {
            $member->notify(new Wechat(WxTemplate::Delete));
            $member->leaveGroup();
        }
        return parent::delete();
    }

    public static function submitedCount()
    {
        return Group::where('is_submit', true)->get()->count();
    }
}
