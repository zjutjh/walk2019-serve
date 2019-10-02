<?php

namespace App\Http\Middleware;

use Closure;

class CheckFinish
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @return mixed
     */
    public function handle($request, Closure $next)
    {
        $yxState = YxState::where('id', 0)->first();
        if ($yxState->state === 1) {
            return RJM(-1, '关闭报名');
        }

        return $next($request);
    }
}
