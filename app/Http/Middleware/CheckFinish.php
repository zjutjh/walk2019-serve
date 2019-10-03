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

       if( env('IsEnd')==1){
           return ;
       }
        return $next($request);
    }
}
