<?php

namespace ZapsterStudios\TeamPay\Controllers;

use App\Team;
use App\User;
use Illuminate\Http\Request;

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
}
