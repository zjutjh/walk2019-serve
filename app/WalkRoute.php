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
    protected $fillable = ['name',  'capacity', 'campus', 'type'];

    public $timestamps = false;


}
