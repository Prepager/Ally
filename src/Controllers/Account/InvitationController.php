<?php

namespace ZapsterStudios\TeamPay\Controllers\Account;

use App\Team;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use ZapsterStudios\TeamPay\Models\TeamMember;
use ZapsterStudios\TeamPay\Models\TeamInvitation;

class InvitationController extends Controller
{
    /**
     * Display a listing of the team invitations.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        return response()->json(auth()->user()->invitations()->with('team')->get());
    }

    /**
     * Accept a team invitation.
     *
     * @param  Request  $request
     * @param  TeamInvitation  $invitation
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, TeamInvitation $invitation)
    {
        $team = $invitation->team()->firstOrFail();

        TeamMember::forceCreate([
            'team_id' => $team->id,
            'user_id' => $request->user()->id,
            'group' => $invitation->group,
        ]);

        $invitation->delete();

        return response()->json($team);
    }

    /**
     * Decline a team invitaiton.
     *
     * @param  TeamInvitation  $invitation
     * @return \Illuminate\Http\Response
     */
    public function destroy(TeamInvitation $invitation)
    {
        $invitation->delete();

        return response()->json('Invitation declined');
    }
}
