<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;

class CheckFinish
{
    /**
     * Handle an incoming request.
     *
     * @param Request $request
     * @param Closure $next
     * @return mixed
     */
    public function handle($request, Closure $next)
    {

        $begin = config("api.system.BeginTime");
        $end = config("api.system.EndTime");
        $now = date('Y-m-d-H:i:s', time());

        if ($begin == null || $end == null) {
            return StandardFailJsonResponse("Server Error");
        }

        if ($now < $begin) {
            return StandardFailJsonResponse("报名还未开始");
        } else if ($now <= $end) {
            return $next($request);
        } else {
            return StandardFailJsonResponse("报名已结束");
        }


    }
}
