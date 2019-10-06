<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class YxApply extends Model
{
    protected $fillable = [
      'apply_team_id', 'apply_id'
    ];

    /**
     * 获取所在的组
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function group() {
        return $this->belongsTo('App\Group', 'group_id');
    }

    public function user(){
        return $this->belongsTo('App\User', 'apply_id');
    }
}
