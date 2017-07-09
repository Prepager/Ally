<?php

namespace ZapsterStudios\TeamPay\Controllers;

use App\Team;
use Illuminate\Http\Request;
use ZapsterStudios\TeamPay\Events\Teams\TeamCreated;
use ZapsterStudios\TeamPay\Events\Teams\TeamDeleated;

class TeamController extends Controller
{
    /**
     * Display a listing of the users teams.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        $this->authorize('view', Team::class);

        return response()->json($request->user()->teams()->withTrashed()->get());
    }

    /**
     * Store a newly created team in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $request->request->add([
            'slug' => Team::generateSlug(str_slug($request->name)),
        ]);

        $this->authorize('create', Team::class);
        $this->validate($request, Team::$rules);

        $team = $request->user()->ownedTeams()->create($request->all());
        $team->teamMembers()->forceCreate([
            'team_id' => $team->id,
            'user_id' => $request->user()->id,
            'group' => 'owner',
        ]);

        event(new TeamCreated($team));

        return response()->json($team);
    }

    /**
     * Display the specified team.
     *
     * @param  \App\Team  $team
     * @return \Illuminate\Http\Response
     */
    public function show(Team $team)
    {
        $this->authorize('view', $team);

        return response()->json($team);
    }

    /**
     * Update the specified team in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Team  $team
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, Team $team)
    {
        $request->request->add([
            'slug' => Team::generateSlug(str_slug($request->name), null, $team->slug),
        ]);

        $this->authorize('update', $team);
        $this->validate($request, Team::$rules);

        return tap($team)->update($this->requestSlug($request));
    }

    /**
     * Remove the specified team from storage.
     *
     * @param  \App\Team  $team
     * @return \Illuminate\Http\Response
     */
    public function destroy(Team $team)
    {
        $this->authorize('delete', $team);

        $team->delete();

        event(new TeamDeleated($team));
    }

    /**
     * Change a users active team.
     *
     * @param  Request  $request
     * @return Response
     */
    public function change(Request $request, Team $team)
    {
        $this->authorize('view', $team);

        $user = $request->user();
        $user->team_id = $team->id;
        $user->save();

        return response()->json($team);
    }

    /**
     * Restore a deleated team.
     *
     * @return Response
     */
    public function restore($team)
    {
        $team = Team::where('slug', $team)->withTrashed()->firstOrFail();
        $this->authorize('update', $team);

        if ($team->trashed()) {
            $team->restore();
        }

        return response()->json($team);
    }
}
