<?php

namespace ZapsterStudios\Ally\Controllers\Team;

use Ally;
use App\Team;
use App\User;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use ZapsterStudios\Ally\Models\TeamInvitation;
use ZapsterStudios\Ally\Events\Teams\Members\TeamMemberInvited;
use ZapsterStudios\Ally\Notifications\TeamInvitation as TeamInvitationMail;

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
        $this->authorize('create', TeamInvitation::class);

        $this->validate($request, [
            'email' => 'required|email',
            'group' => 'required|'.Ally::inGroup(),
        ]);

        if ($team->maxMemberCountReached()) {
            return response()->json('Max member count reached', 402);
        }

        $invitation = TeamInvitation::forceCreate([
            'team_id' => $team->id,
            'email' => $request->email,
            'group' => $request->group,
        ]);

        event(new TeamMemberInvited($team, $request->email));

        $user = User::where('email', $request->email)->first() ?? new User([
            'email' => $request->email,
        ]);

        $user->notify(new TeamInvitationMail($team, $user, $user->exists));

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
        $this->authorize('view', $invitation);

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
        $this->authorize('update', $invitation);
        $this->validate($request, [
            'group' => 'required|'.Ally::inGroup(),
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
        $this->authorize('delete', $invitation);

        $invitation->delete();

        return response()->json('Invitation deleted.');
    }
}
