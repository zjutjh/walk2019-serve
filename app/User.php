<?php

namespace App;

use App\Jobs\SendTemplate;
use App\Mail\Message;
use Illuminate\Notifications\Notifiable;
use App\Helpers\_State;

/**
 * @property mixed openid
 * @property null group_id
 * @method static where(string $string, $captain_id)
 */
class User extends Model
{
    /**
     * The attributes that should be hidden for arrays.
     *
     * @var array
     */
    protected $hidden = [
        'openid', 'sex', 'id_card', 'height', 'birthday', 'uid'
    ];


    protected $fillable = [
        'name', 'id_card', 'email', 'sex', 'qq', 'wx_id', 'height', 'birthday', 'phone', 'campus', 'school', 'sid','logo','identity','state','group_id', 'height'
    ];



    /**
     * Selectors
     */
    public static function fromOpenid($openid){
        return User::where('openid', $openid)->first();
    }

    /**
     * Get the identifier that will be stored in the subject claim of the JWT.
     *
     * @return mixed
     */
    public function getJWTIdentifier()
    {
        return $this->getKey();
    }
    /**
     * Return a key value array, containing any custom claims to be added to the JWT.
     *
     * @return array
     */
    public function getJWTCustomClaims()
    {
        return [];
    }

    /**
     * 获取所在的组
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function group() {
        return $this->belongsTo('App\Group', 'group_id');
    }

    public function applies(){
        return $this->hasMany('App\Apply');
    }

    /**
     * 模板消息通知
     */
    public function notify($data) {
        $config = [
            'openid' => $this->openid,
            'url'    => 'http://walk.zjutjh.com',
            'data'   => $data
        ];
        dispatch(new SendTemplate($config));
    }

    /**
     * user state 访问器
     * @return mixed
     */
    public function getStateAttribute() {
        return $this->state()->first();
    }

    /**
     * 获取报名人数
     * @return mixed
     */
    static public function getUserCount() {
        return UserState::where('state', '>', 0)->count();
    }

    /**
     * 离开队伍
     * @return $this
     */
    public function leaveGroup()
    {
        $group = Group::find($this->group_id);
        $this->group_id = null;
        $this->update(['state' => _State::no_entered]);
        //DONE: 在人数不达标时，强制{解锁}队伍
        if($group->members()->count() < config('info.members_count.least')){
            //notify(_notify::dismiss, $group->id);
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
    public function addGroup($groupId) {
        $this->group_id = $groupId;
        $this->update(['state' => _state::member]);

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

        //var_dump( $access_msg);

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
