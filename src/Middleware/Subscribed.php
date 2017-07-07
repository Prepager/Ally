<?php

namespace ZapsterStudios\TeamPay\Middleware;

use Closure;

class Subscribed
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

        $team = ($request->route('team') ?? $request->user()->team);
        if (! $team || ! $team->subscribed()) {
            return response()->json('Subscription required.', 403);
        }

        return $next($request);
    }
}
