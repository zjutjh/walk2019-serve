<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;

class CheckFinish
{
    /**
     * Handle an incoming request.
     *
     * @param  Request  $request
     * @param Closure $next
     * @return mixed
     */
    public function handle($request, Closure $next)
    {

       if(config('api.system.IsEnd')===true){
           return StandardJsonResponse(-1,"报名已结束");
       }
        return $next($request);
    }
}
