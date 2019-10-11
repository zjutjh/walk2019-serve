<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Notifications\Notifiable;
use App\Helpers\State;

/**
 * @property mixed openid
 * @property null yx_group_id
 * @method static where(string $string, $captain_id)
 */
class User extends Model
{

    use Notifiable;
    /**
     * The attributes that should be hidden for arrays.
     * @var array
     */
    protected $hidden = [
        'openid', 'sex', 'id_card',  'birthday', 'sid'
    ];


    protected $fillable = [
        'name', 'id_card', 'email', 'sex', 'qq', 'wx_id', 'height', 'birthday', 'phone', 'campus', 'school', 'sid','logo','identity','state','group_id', 'height'
    ];



    /**
     * 获得当前用户
     * @return User|null
     */
    public static function current(){
        $openid = session('openid');
        if ($openid === null) { return null; }
        $user = User::where("openid",$openid)->first();
        return $user;
    }

    /**
     * 获取所在的组
     * @return BelongsTo
     */
    public function group()
    {
        return $this->belongsTo('App\Group', 'group_id');
    }


    /**
     * 获取报名人数
     * @return mixed
     */
    static public function getUserCount()
    {
        return User::where('state', '>', 0)->count();
    }

    /**
     * 离开队伍
     * @return bool
     */
    public function leaveGroup()
    {
        $group = Group::find($this->group_id);
        $this->group_id = null;
        $this->update(['state' => State::no_entered]);
        //DONE: 在人数不达标时，强制{解锁}队伍
        if($group->members()->count() < config('info.members_count.least')){

            $group->is_submit=false;
        }
        return parent::save();
    }


    /**
     * 设置身份证信息
     * @param $value
     */
    public function setIdCardAttribute($value)
    {
        $this->attributes['sex'] = iidGetSex($value);
        $this->attributes['birthday'] = iidGetBirthday($value);
        $this->attributes['id_card'] = md5(strtoupper($value));
    }

    /**
     * 加入队伍
     * @param $groupId
     * @return bool
     */
    public function addGroup($groupId)
    {
        $this->group_id = $groupId;
        $this->update(['state' => State::member]);

        return parent::save();
    }




}
