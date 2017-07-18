<?php

namespace ZapsterStudios\TeamPay\Controllers\Team;

use TeamPay;
use App\Team;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use ZapsterStudios\TeamPay\Models\TeamInvitation;
use ZapsterStudios\TeamPay\Events\Teams\Members\TeamMemberInvited;

class InvitationController extends Controller
{
    /**
     * Display a listing of the invited team members.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Team $team)
    {
        $this->authorize('view', $team);

        return response()->json($team->invitations()->get());
    }

    /**
     * Store a newly created team member invite in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request, Team $team)
    {
        $this->authorize('update', $team);
        $this->validate($request, [
            'email' => 'required|email',
            'group' => 'required|'.TeamPay::inGroup(),
        ]);

        $invitation = TeamInvitation::forceCreate([
            'team_id' => $team->id,
            'email' => $request->email,
            'group' => $request->group,
        ]);

        event(new TeamMemberInvited($team, $request->email));

        // Email here

        return response()->json($invitation);
    }

    /**
     * Display the specified team member invitation.
     *
     * @param  \App\Team  $team
     * @return \Illuminate\Http\Response
     */
    public function show(Team $team, TeamInvitation $invitation)
    {
        $this->authorize('view', $team);

        return response()->json($invitation);
    }

    /**
     * Update the specified team member invitation in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Team  $team
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, Team $team, TeamInvitation $invitation)
    {
        $this->authorize('update', $team);
        $this->validate($request, [
            'group' => 'required|'.TeamPay::inGroup()
        ]);

        return response()->json(tap($invitation)->update([
            'group' => $request->group,
        ]));
    }

    /**
     * Remove the specified member invitation from the team.
     *
     * @param  \App\Team  $team
     * @return \Illuminate\Http\Response
     */
    public function destroy(Team $team, TeamInvitation $invitation)
    {
        $this->authorize('update', $team);

        $invitation->delete();

        return response()->json('Invitation deleted.');
    }
}
