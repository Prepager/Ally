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
        //
    }

    /**
     * Display a single user.
     *
     * @return \Illuminate\Http\Response
     */
    public function user(User $user)
    {
        //
    }

    /**
     * Search in users.
     *
     * @return \Illuminate\Http\Response
     */
    public function searchUsers(Request $request)
    {
        //
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
        //
    }

    /**
     * Display a single team.
     *
     * @return \Illuminate\Http\Response
     */
    public function team(Team $team)
    {
        //
    }

    /**
     * Search in teams.
     *
     * @return \Illuminate\Http\Response
     */
    public function searchTeams(Request $request)
    {
        //
    }
}
