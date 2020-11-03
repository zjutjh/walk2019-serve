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

    /**
     * 追加字段
     * @var array
     */
    protected $appends = ['remain'];

    public $timestamps = false;

    public function getRemainAttribute()
    {
        return $this->capacity - Group::where([['is_submit', true], ['route_id', $this->id], ['is_super', false]])->count();
    }
}
