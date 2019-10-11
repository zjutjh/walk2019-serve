<?php

namespace App;

use App\User;
use App\Helpers\_State;
use Illuminate\Database\Eloquent\Model;

/**
 * Class Apply
 * @package App
 * @method static where(string $string, $id)
 * @method delete()
 */
class Apply extends Model
{
    protected $fillable = [
        'group_id', 'apply_id'
    ];

    public static function removeAll($user_id){
      $applies = Apply::where('apply_id', $user_id)->get();
      foreach ($applies as $apply){
          $apply->delete();
      }
      $user = User::find($user_id);
      if($user->state === _state::appling){
        $user->state = _state::no_entered;
      }
    }

    public static function removeOne($user_id, $group_id){
        $user = User::find($user_id);
        $apply = Apply::where('apply_id', $user_id)->where('apply_team_id', $group_id)->get()->first();
        if ($apply !== null) {
            $apply->delete();
        }
        $applies = Apply::where('apply_id', $user_id)->get();
        if ($applies->count() === 0){
            if($user->state === _State::appling){
                $user->state = _State::no_entered;
                $user->save();
            }
        }
    }

    public static function addOne($user_id, $group_id){
        $user = User::find($user_id);
        if ($user->state === _State::captain || $user->state === _State::member) {
            return 0;
        } else {
            $applis = Apply::where('apply_team_id',$group_id)->where('apply_id',$user_id)->get();
            if($applis !== null){
                return 1;
            } else {
                $apply = Apply::create(['apply_team_id' => $group_id, 'apply_id' => $user_id]);
                $user->state = _State::appling;
                $user->save();
                return $apply;
            }
        }
    }

    public static function removeGroup($group_id) {
        $applies = Apply::where('group_id',$group_id)->get();
        foreach($applies as $apply){
            $apply->delete();
        }
    }
}
