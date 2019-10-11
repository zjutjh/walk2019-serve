<?php

namespace App;

use App\User;
use App\Helpers\_state;
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

    public static function removeAll($user_id){
      $applies = Apply::where('apply_id', $user_id)->get();
      $applies->delete();
      $user = User::find($user_id);
      if($user->state === _state::appling){
        $user->state = _state::no_entered;
      }
    }

    public static function removeOne($user_id, $group_id){
      $flag = false;
      $apply = Apply::where('apply_id', $user_id)->where('apply_team_id', $group_id)->get()->first();
      if (!is_null($apply)) {
        $apply->delete();
        $flag = true;
      }
      $applies = Apply::where('apply_id', $user_id)->get();
      if ($applies -> count() == 0){
        if($user->state === _state::appling){
          $user->state = _state::no_entered;
        }
      }
    }

    public static function addOne($user_id, $group_id){
      $user = User::find($user_id);
      if ($user->state === _state::captain || $user->state === _state::member) {
        return null;
      } else {
        $apply = Apply::create(['apply_team_id' => $groupId, 'apply_id' => $user->id]);
        $user->state = _state::appling;
        $user->save();
        return $apply;
      }
    }
}
