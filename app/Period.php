<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Period extends Model
{
    protected $table = 'period';
    protected $fillable = ['route','begin','end'];
    
}
