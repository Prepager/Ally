<?php

namespace ZapsterStudios\TeamPay\Controllers\Team;

use TeamPay;
use App\User;
use App\Team;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use ZapsterStudios\TeamPay\Models\TeamMember;
use ZapsterStudios\TeamPay\Events\Teams\Members\TeamMemberKicked;

class MemberController extends Controller
{
    /**
     * Display a listing of the teams members.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Team $team)
    {
        $this->authorize('view', $team);
        $this->authorize('view', TeamMember::class);

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
        $this->authorize('view', $member);

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
        $this->authorize('update', $member);
        $this->validate($request, [
            'group' => 'required|'.TeamPay::inGroup(),
        ]);

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
        $this->authorize('delete', $member);

        $member->delete();

        event(new TeamMemberKicked($team, $member->user()->first()));
    }
}
