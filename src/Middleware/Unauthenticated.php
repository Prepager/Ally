<?php

namespace ZapsterStudios\TeamPay\Middleware;

use Closure;

class Unauthenticated
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
        $user = $request->user();

        if ($user) {
            return response()->json('You must be unauthenticated.', 403);
        }

        return $next($request);
    }
}
