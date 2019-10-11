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
            $user->save();
        }
    }

    public static function removeOne($user_id)
    {
        $apply = Apply::where('apply_id', $user_id)->first();
        if (!is_null($apply)) {
            $apply->delete();
        }

        $user = User::find($user_id)->first();
        if (!is_null($user)) {

            $user->state = State::no_entered;
            $user->save();
        }
    }

    public static function add($user_id, $group_id)
    {
        $user = User::find($user_id);
        if ($user->state === State::captain || $user->state === State::member || $user->state === State::appling) {
            return 0;
        } else {

            $apply = Apply::create(['apply_team_id' => $group_id, 'apply_id' => $user_id]);
            $user->state = State::appling;
            $apply->save();
            $user->save();
            return $apply;
        }

    }


}
