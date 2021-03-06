<?php

namespace ZapsterStudios\Ally\Controllers\Dashboard;

use App\Team;
use Carbon\Carbon;
use Illuminate\Http\Request;
use ZapsterStudios\Ally\Controllers\Controller;
use ZapsterStudios\Ally\Events\Teams\TeamSuspended;

class TeamSuspensionController extends Controller
{
    /**
     * Suspend team.
     *
     * @param  Request  $request
     * @param  \App\Team  $team
     * @return Response
     */
    public function store(Request $request, Team $team)
    {
        $team->suspended_at = Carbon::now();
        $team->suspended_to = $request->input('suspended_to');
        $team->suspended_reason = $request->input('suspended_reason');

        event(new TeamSuspended($team));

        return response()->json(tap($team)->save());
    }

    /**
     * Unsuspend team.
     *
     * @param  Request  $request
     * @param  \App\Team  $team
     * @return Response
     */
    public function destroy(Request $request, Team $team)
    {
        $team->suspended_at = null;
        $team->suspended_to = null;
        $team->suspended_reason = null;

        return response()->json(tap($team)->save());
    }
}
