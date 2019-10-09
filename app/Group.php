<?php

namespace App;


use Exception;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use App\WalkPath;
use App\Helpers\_state;

/**
 * @property mixed id
 * @property mixed captain_id
 * @method static orderBy(string $string, string $string1)
 * @method static where(string $string, string $select_route)
 */
class Group extends Model
{
    protected $fillable = [
        'name', 'capacity', 'description', 'route_id', 'captain_id','logo'
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
     * 判断该组的成员是否支持某条线路
     */
    public function supportWalkPath($walkPath){
        $members = $this->members();
        foreach($members as $member){
            if(!$walkPath -> supportCampus($member->campus)){
                return false;
            }
        }
        return true;
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
    public function dismiss()
    {
        $members = $this->members()->get();
        $applies = Apply::where('apply_team_id', $this->id)->get();
        foreach ($applies as $apply) {
            $user = User::find($apply->apply_id);
            $apply->delete();

            notify(_notify::apply_cancel_group, $apply->apply_id, $this->id);
        }
        foreach ($members as $member) {
            $member->leaveGroup();
        
            if($member->id !== $this->captain_id){
                notify(_notify::throwed_group, $member);
            }
        }
        return parent::delete(); // TODO: Change the autogenerated stub
    }

    public static function countSumbitToNow()
    {
        return Group::where('is_submit',true)->get()->count();
    }
}
