<?php

namespace App;

use App\User;
use App\Helpers\State;
use Illuminate\Database\Eloquent\Model;

/**
 * Class Apply
 * @package App
 */
class Apply extends Model
{
    protected $fillable = [
        'apply_team_id', 'apply_id'
    ];

    public static function removeAll($user_id)
    {
        $applies = Apply::where('apply_id', $user_id)->get();
        $applies->delete();
        $user = User::find($user_id);
        if ($user->state === State::appling) {
            $user->state = State::no_entered;
        }
    }

    public static function removeOne($user_id, $group_id)
    {
        $flag = false;
        $apply = Apply::where('apply_id', $user_id)->where('apply_team_id', $group_id)->get()->first();
        if (!is_null($apply)) {
            $apply->delete();
            $flag = true;
        }

        $user = User::where('id',$user_id)->get()->first();
        if ($user->count() == 0) {
            if ($user->state === State::appling) {
                $user->state = State::no_entered;
            }
        }
    }


}
