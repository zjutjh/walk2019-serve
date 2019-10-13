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
}
