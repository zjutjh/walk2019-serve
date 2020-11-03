<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

/**
 * @method static orderBy(string $string, string $string1)
 */
class WalkRoute extends Model
{
    /**
     * 数据表名
     */
    protected $table = 'walk_route';

    /**
     * 可填充字段
     * @return int
     */
    protected $fillable = ['name', 'capacity', 'campus', 'type'];
    protected $appends = ['remain'];
    public $timestamps = false;


    public function getRemainAttribute()
    {
        return $this->capacity - Group::Where([['is_submit', 1], ['is_super', 0]])->count();
    }


}
