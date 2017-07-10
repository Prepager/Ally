<?php

namespace ZapsterStudios\TeamPay\Middleware;

use Closure;

class Administrator
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
        if (! $request->user()) {
            return response()->json('Unauthenticated.', 401);
        }

        if(! $request->user()->isAdmin()) {
            return response()->json('Insufficient permissions.', 403);
        }

        return $next($request);
    }
}
