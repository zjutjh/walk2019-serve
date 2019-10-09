<?php

namespace App;

use Exception;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

/**
 * @property mixed id
 * @property mixed captain_id
 * @method static orderBy(string $string, string $string1)
 * @method static where(string $string, string $select_route)
 */
class Group extends Model
{
    protected $fillable = [
        'name', 'capacity', 'description', 'route', 'captain_id','logo'
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
    public function members() {
        return $this->hasMany('App\User');
    }
    /**
     * 获取队员
     */
    public function getMembersAttribute() {
        return $this->members()->count();
    }
    /**
     * 获取队伍数量
     * @return mixed
     */
    static public function getTeamCount() {
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
            $user = User::where('id', $apply->apply_id)->first();
            // Todo:: notify
            $user->state = 1;
            $apply->delete();
        }
        foreach ($members as $member) {
            $member->group_id = null;
            $member->state =1;
            // Todo:: notify
            $member->save();
        }
        return parent::delete(); // TODO: Change the autogenerated stub
    }

    public static function countSumbitToNow()
    {
        return Group::where('is_submit',true)->get()->count();
    }
}
