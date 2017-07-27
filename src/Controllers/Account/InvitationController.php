<?php

namespace ZapsterStudios\Ally\Controllers\Account;

use App\Team;
use Illuminate\Http\Request;
use ZapsterStudios\Ally\Models\TeamMember;
use ZapsterStudios\Ally\Models\TeamInvitation;
use ZapsterStudios\Ally\Controllers\Controller;

class InvitationController extends Controller
{
    /**
     * Display a listing of the team invitations.
     *
     * @return Response
     */
    public function index()
    {
        $this->authorize('userView', TeamInvitation::class);

        return response()->json(auth()->user()->invitations()->with('team')->get());
    }

    /**
     * Accept a team invitation.
     *
     * @param  Request  $request
     * @param  \ZapsterStudios\Ally\Models\TeamInvitation  $invitation
     * @return Response
     */
    public function update(Request $request, TeamInvitation $invitation)
    {
        $this->authorize('accept', $invitation);

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
     * @param  \ZapsterStudios\Ally\Models\TeamInvitation  $invitation
     * @return Response
     */
    public function destroy(TeamInvitation $invitation)
    {
        $this->authorize('decline', $invitation);

        $invitation->delete();

        return response()->json('Invitation declined');
    }
}
