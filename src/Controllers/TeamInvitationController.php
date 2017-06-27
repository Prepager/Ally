<?php

namespace ZapsterStudios\TeamPay\Controllers;

use App\Team;
use Illuminate\Http\Request;
use ZapsterStudios\TeamPay\Models\TeamMember;

class TeamInvitationController extends Controller
{
    /**
     * Display a listing of the teams members.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Team $team)
    {
        //
    }

    /**
     * Store a newly created team member in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request, Team $team)
    {
        //
    }

    /**
     * Display the specified team member.
     *
     * @param  \App\Team  $team
     * @return \Illuminate\Http\Response
     */
    public function show(Team $team, TeamMember $member)
    {
        //
    }

    /**
     * Update the specified team member in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Team  $team
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, Team $team, TeamMember $member)
    {
        //
    }

    /**
     * Remove the specified member from the team.
     *
     * @param  \App\Team  $team
     * @return \Illuminate\Http\Response
     */
    public function destroy(Team $team, TeamMember $member)
    {
        //
    }
}
