<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;

class CheckWeChat
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
        $openid = session('openid');
        if($openid=== null){
            session()->put('openid', 'testopenid');
           //return StandardFailJsonResponse("你还没有openid");
        }
        return $next($request);
    }
}
