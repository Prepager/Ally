<?php

namespace ZapsterStudios\Ally\Controllers\Team;

use App\Team;
use Illuminate\Http\Request;
use ZapsterStudios\Ally\Controllers\Controller;
use ZapsterStudios\Ally\Events\Teams\TeamCreated;
use ZapsterStudios\Ally\Events\Teams\TeamDeleated;
use ZapsterStudios\Ally\Events\Teams\TeamRestored;

class TeamController extends Controller
{
    /**
     * Display a listing of the users teams.
     *
     * @param  Request  $request
     * @return Response
     */
    public function index(Request $request)
    {
        $this->authorize('view', Team::class);

        return response()->json($request->user()->teams()->withTrashed()->get());
    }

    /**
     * Store a newly created team in storage.
     *
     * @param  Request  $request
     * @return Response
     */
    public function store(Request $request)
    {
        $this->authorize('create', Team::class);

        $request->request->add([
            'slug' => Team::generateSlug(str_slug($request->name)),
        ]);

        $this->validate($request, [
            'name' => 'required|min:2|unique:teams,name',
            'slug' => 'required|alpha_dash|unique:teams,slug',
        ]);

        $user = $request->user();
        $team = $user->ownedTeams()->create($request->all());
        $user->teams()->attach($team);

        $user->team_id = $team->id;
        $user->save();

        event(new TeamCreated($team));

        return response()->json($team);
    }

    /**
     * Display the specified team.
     *
     * @param  \App\Team  $team
     * @return Response
     */
    public function show(Team $team)
    {
        $this->authorize('view', $team);

        return response()->json($team);
    }

    /**
     * Update the specified team in storage.
     *
     * @param  Request  $request
     * @param  \App\Team  $team
     * @return Response
     */
    public function update(Request $request, Team $team)
    {
        $this->authorize('update', $team);

        $request->request->add([
            'slug' => Team::generateSlug(str_slug($request->name), null, $team->slug),
        ]);

        $this->validate($request, [
            'name' => 'required|min:2|unique:teams,name',
            'slug' => 'sometimes|required|alpha_dash|unique:teams,slug',
        ]);

        return tap($team)->update($request->all());
    }

    /**
     * Remove the specified team from storage.
     *
     * @param  Request  $request
     * @param  \App\Team  $team
     * @return Response
     */
    public function destroy(Request $request, Team $team)
    {
        $this->authorize('delete', $team);

        $team->performDeletion();

        $user = $request->user();
        if ($user->team_id === $team->id) {
            $user->team_id = $user->firstTeam();
            $user->save();
        }

        event(new TeamDeleated($team));
    }

    /**
     * Change a users active team.
     *
     * @param  Request  $request
     * @param  \App\Team  $team
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
     * @param  \App\Team  $team
     * @return Response
     */
    public function restore($team)
    {
        $team = Team::where('slug', $team)->withTrashed()->firstOrFail();
        $this->authorize('restore', $team);

        if ($team->trashed()) {
            $team->restore();
        }

        event(new TeamRestored($team));

        return response()->json($team);
    }
}
