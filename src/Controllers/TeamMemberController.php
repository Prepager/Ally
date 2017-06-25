<?php

namespace ZapsterStudios\TeamPay\Controllers;

use App\Team;
use ZapsterStudios\TeamPay\Models\TeamMember;

use Illuminate\Http\Request;
use ZapsterStudios\TeamPay\Events\Teams\TeamCreated;
use ZapsterStudios\TeamPay\Events\Teams\TeamDeleated;

class TeamMemberController extends Controller
{
    /**
     * Display a listing of the teams members.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Team $team)
    {
        $this->authorize('view', Team::class);

        return response()->json($team->members()->get());
    }

    /**
     * Store a newly created team member in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request, Team $team)
    {
        // Team invitation
    }

    /**
     * Display the specified team member.
     *
     * @param  \App\Team  $team
     * @return \Illuminate\Http\Response
     */
    public function show(Team $team, TeamMember $member)
    {
        $this->authorize('view', $team);
        abort_if($member->team_id != $team->id, 404);

        return response()->json($member);
    }

    /**
     * Update the specified team member in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Team  $team
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, Team $team, TeamMember $member)
    {
        $this->authorize('update', $team);
        $this->validate($request, TeamMember::$rules);
        abort_if($member->team_id != $team->id, 404);

        return response()->json(tap($member)->update([
            'group' => $request->group
        ]));
    }

    /**
     * Remove the specified member from the team.
     *
     * @param  \App\Team  $team
     * @return \Illuminate\Http\Response
     */
    public function destroy(Team $team, TeamMember $member)
    {
        $this->authorize('update', $team);
        abort_if($member->team_id != $team->id, 404);

        $member->delete();

        // Event...
    }
}
