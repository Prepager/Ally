<?php

namespace ZapsterStudios\TeamPay\Controllers;

use App\Team;
use App\User;
use Carbon\Carbon;
use Illuminate\Http\Request;
use ZapsterStudios\TeamPay\Events\Teams\TeamSuspended;
use ZapsterStudios\TeamPay\Events\Users\UserSuspended;

class DashboardController extends Controller
{
    /**
     * Display a listing of the dashboard details.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        //
    }

    /**
     * Display a listing of users.
     *
     * @return \Illuminate\Http\Response
     */
    public function users()
    {
        return response()->json(User::paginate(30));
    }

    /**
     * Display a single user.
     *
     * @return \Illuminate\Http\Response
     */
    public function user($id)
    {
        return response()->json(User::with('teams')->findOrFail($id));
    }

    /**
     * Search in users.
     *
     * @return \Illuminate\Http\Response
     */
    public function searchUsers(Request $request)
    {
        $search = $request->search;

        return response()->json(User::where('id', $search)
            ->orWhere('country', $search)
            ->orWhere('name', 'LIKE', '%'.$search.'%')
            ->orWhere('email', 'LIKE', '%'.$search.'%')
            ->paginate(30)
        );
    }

    /**
     * Suspend user.
     *
     * @return \Illuminate\Http\Response
     */
    public function suspendUser(Request $request, User $user)
    {
        $user->suspended_at = Carbon::now();
        $user->suspended_to = $request->input('suspended_to');
        $user->suspended_reason = $request->input('suspended_reason');

        event(new UserSuspended($user));

        return response()->json(tap($user)->save());
    }

    /**
     * Unsuspend user.
     *
     * @return \Illuminate\Http\Response
     */
    public function unsuspendUser(Request $request, User $user)
    {
        $user->suspended_at = null;
        $user->suspended_to = null;
        $user->suspended_reason = null;

        return response()->json(tap($user)->save());
    }

    /**
     * Impersonate a user.
     *
     * @return \Illuminate\Http\Response
     */
    public function impersonate(User $user)
    {
        //
    }

    /**
     * Stop impersonating a user.
     *
     * @return \Illuminate\Http\Response
     */
    public function stopImpersonation()
    {
        //
    }

    /**
     * Display a listing of teams.
     *
     * @return \Illuminate\Http\Response
     */
    public function teams()
    {
        return response()->json(Team::paginate(30));
    }

    /**
     * Display a single team.
     *
     * @return \Illuminate\Http\Response
     */
    public function team($id)
    {
        return response()->json(Team::findOrFail($id));
    }

    /**
     * Search in teams.
     *
     * @return \Illuminate\Http\Response
     */
    public function searchTeams(Request $request)
    {
        $search = $request->search;

        return response()->json(Team::where('id', $search)
            ->orWhere('user_id', $search)
            ->orWhere('name', 'LIKE', '%'.$search.'%')
            ->orWhere('slug', 'LIKE', '%'.$search.'%')
            ->paginate(30)
        );
    }

    /**
     * Suspend team.
     *
     * @return \Illuminate\Http\Response
     */
    public function suspendTeam(Request $request, Team $team)
    {
        $team->suspended_at = Carbon::now();
        $team->suspended_to = $request->input('suspended_to');
        $team->suspended_reason = $request->input('suspended_reason');

        event(new TeamSuspended($team));

        return response()->json(tap($team)->save());
    }

    /**
     * Unsuspend team.
     *
     * @return \Illuminate\Http\Response
     */
    public function unsuspendTeam(Request $request, Team $team)
    {
        $team->suspended_at = null;
        $team->suspended_to = null;
        $team->suspended_reason = null;

        return response()->json(tap($team)->save());
    }
}
