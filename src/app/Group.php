<?php

namespace App;


use App\Notifications\Wechat;
use Exception;
use App\Helpers\WechatTemplate;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

/**
 * @property mixed id
 * @property mixed captain_id
 * @method static orderBy(string $string, string $string1)
 */
class Group extends Model
{
    protected $fillable = [
        'name', 'logo', 'capacity', 'description', 'captain_id', 'route_id',
        'prize_id', 'prize_get','allow_matching'
    ];

    /**
     * 追加字段
     * @var array
     */
    protected $appends = ['members', 'route', 'captain_name'];


    /**
     * 获得当前用户
     * @return Group|null
     */
    public static function current()
    {
        $user = User::current();
        if (!!!$user)
            return null;
        return User::current()->group();
    }

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
     *  获取队长姓名
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
            $user->notify(new Wechat(WechatTemplate::Delete));
            $apply->delete();
        }


        foreach ($members as $member) {
            $member->notify(new Wechat(WechatTemplate::Delete));
            $member->leaveGroup();
        }
        return parent::delete();
    }

}
