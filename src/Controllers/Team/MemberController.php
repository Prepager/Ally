<?php

namespace ZapsterStudios\Ally\Controllers\Team;

use Ally;
use App\Team;
use App\User;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use ZapsterStudios\Ally\Models\TeamMember;
use ZapsterStudios\Ally\Events\Teams\Members\TeamMemberKicked;

class MemberController extends Controller
{
    /**
     * Display a listing of the teams members.
     *
     * @param  \App\Team  $team
     * @return Response
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
     * @param  \ZapsterStudios\Ally\Models\TeamMember  $member
     * @return Response
     */
    public function show(Team $team, TeamMember $member)
    {
        $this->authorize('view', $member);

        return response()->json($member); // Include user
    }

    /**
     * Update the specified team member in storage.
     *
     * @param  Request  $request
     * @param  \App\Team  $team
     * @param  \ZapsterStudios\Ally\Models\TeamMember  $member
     * @return Response
     */
    public function update(Request $request, Team $team, TeamMember $member)
    {
        $this->authorize('update', $member);
        $this->validate($request, [
            'group' => 'required|'.Ally::inGroup(),
        ]);

        return response()->json(tap($member)->update([
            'group' => $request->group,
        ]));
    }

    /**
     * Remove the specified member from the team.
     *
     * @param  \App\Team  $team
     * @param  \ZapsterStudios\Ally\Models\TeamMember  $member
     * @return Response
     */
    public function destroy(Team $team, TeamMember $member)
    {
        $this->authorize('delete', $member);

        $member->delete();

        event(new TeamMemberKicked($team, $member->user()->first()));
    }
}
