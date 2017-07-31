<?php

namespace ZapsterStudios\Ally\Middleware;

use Closure;
use App\Team;

class Subscribed
{
    /**
     * Handle an incoming request.
     *
     * @param  Request  $request
     * @param  Closure  $next
     * @return mixed
     */
    public function handle($request, Closure $next)
    {
        if (! $request->user()) {
            return response()->json('Unauthenticated.', 401);
        }

        $team = ($request->route('team') ?? $request->user()->team);
        if (is_string($team)) {
            $team = Team::where('slug', $team)->first();
        }

        if (! $team || ! $team->subscribed()) {
            return response()->json('Subscription required.', 403);
        }

        return $next($request);
    }
}
