<?php

namespace ZapsterStudios\TeamPay\Middleware;

use Closure;
use App\Team;

class Suspended
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
        $team = ($user ? ($request->route('team') ?? $user->team) : null);
        if (is_string($team)) {
            $team = Team::where('slug', $team)->first();
        }

        if ($user && $user->suspended() && ! $request->is('dashboard/*')) {
            return response()->json($user, 403);
        }

        if ($team && $team->suspended() && ! $request->is('dashboard/*')) {
            return response()->json($team, 403);
        }

        return $next($request);
    }
}
