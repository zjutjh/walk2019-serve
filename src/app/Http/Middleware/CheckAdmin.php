<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;

class CheckAdmin
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

        if (session('key') === null && env('AdminPass') !== $request->get('pass')) {
            return StandardJsonResponse(-1, "Admin pass wrong");
        }
        return $next($request);
    }
}
