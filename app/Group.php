<?php

namespace App;


use Exception;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use App\Helpers\State;

/**
 * @property mixed id
 * @property mixed captain_id
 * @method static orderBy(string $string, string $string1)
 * @method static where(string $string, string $select_route)
 */
class Group extends Model
{
    protected $fillable = [
        'name', 'logo', 'capacity', 'description',  'captain_id', 'route_id', 'is_submit','No'
    ];

    /**
     * 追加字段
     * @var array
     */
    protected $appends = ['members'];

    /**
     *  获取所有组员
     * @return HasMany
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

    /**
     * 删除队伍
     * @return bool|null
     * @throws Exception
     */
    public function delete()
    {
        $members = $this->members()->get();
        $applies = Apply::where('apply_team_id', $this->id)->get();


        foreach ($applies as $apply) {
            $user = User::find($apply->apply_id);

            $user->notify(new Wechat(WxTemplate::Delete));

            $apply->delete();
        }


        foreach ($members as $member) {
            $member->leaveGroup();
            $member->notify(new Wechat(WxTemplate::Delete));
        }
        return parent::delete();
    }

    public static function submitedCount()
    {
        return Group::where('is_submit', true)->get()->count();
    }
}
