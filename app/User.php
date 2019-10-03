<?php

namespace App;

use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Notifications\Notifiable;
use Illuminate\Foundation\Auth\User as Authenticatable;

/**
 * @property mixed openid
 * @property null yx_group_id
 * @method static where(string $string, $captain_id)
 */
class User extends Authenticatable
{

    use Notifiable;
    /**
     * The attributes that should be hidden for arrays.
     * @var array
     */
    protected $hidden = [
        'openid', 'sex', 'id_card', 'height', 'birthday', 'sid'
    ];

    protected $appends = [
        'state'
    ];

    protected $fillable = [
        'name', 'id_card', 'email', 'sex', 'qq', 'wx_id', 'height', 'birthday', 'phone', 'campus', 'school', 'sid'
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
        return $this->belongsTo('App\Group', 'yx_group_id');
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
        $this->yx_group_id = null;
        $this->state()->update(['state' => 1]);
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

    public function getStateAttribute(){}
    /**
     * 加入队伍
     * @param $groupId
     * @return bool
     */
    public function addGroup($groupId)
    {
        $this->yx_group_id = $groupId;
        $this->state()->update(['state' => 4]);
        return parent::save();
    }

    /**
     * 确认是否关注公众号
     * @return bool
     */
    public function identifyGz()
    {
        $openid = $this->openid;
        $access_token = "https://api.weixin.qq.com/cgi-bin/token?grant_type=client_credential&appid=" . env('WECHAT_APPID') . "&secret=" . env('WECHAT_SECRET');
        $access_msg = json_decode(file_get_contents($access_token));
        var_dump( $access_msg);
        $token = $access_msg->access_token;
        $subscribe_msg = "https://api.weixin.qq.com/cgi-bin/user/info?access_token=$token&openid=$openid";
        $subscribe = json_decode(file_get_contents($subscribe_msg));
        $isSubscribed = $subscribe->subscribe;
        //
        if ($isSubscribed === 1) {
            return true;
        } else {
            return false;
        }
    }


}
