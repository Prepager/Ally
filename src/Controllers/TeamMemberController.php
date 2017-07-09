<?php

namespace ZapsterStudios\TeamPay\Controllers;

use TeamPay;
use App\Team;
use Illuminate\Http\Request;
use ZapsterStudios\TeamPay\Models\TeamMember;
use ZapsterStudios\TeamPay\Events\Teams\Members\TeamMemberKicked;

class TeamMemberController extends Controller
{
    /**
     * Display a listing of the teams members.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Team $team)
    {
        $this->authorize('view', $team);

        return response()->json($team->members()->get());
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

        return response()->json($member); // Include user
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
        $request->request->add([
            'user_id' => $member->id,
        ]);

        $rules = TeamMember::$rules;
        $rules['group'] = $rules['group'].'|in:'.TeamPay::groups()->implode('id', ',');

        $this->authorize('update', $team);
        $this->validate($request, $rules);
        abort_if($member->team_id != $team->id, 404);

        return response()->json(tap($member)->update([
            'group' => $request->group,
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

        event(new TeamMemberKicked($team, $member->user()->first()));
    }
}
