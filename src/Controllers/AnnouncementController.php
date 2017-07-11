<?php

namespace ZapsterStudios\TeamPay\Controllers;

use Illuminate\Http\Request;
use ZapsterStudios\TeamPay\Models\Announcement;

class AnnouncementController extends Controller
{
    /**
     * Display a listing of the announcements.
     *
     * @return \Illuminate\Http\Response
     */
    public function index($method = 'recent')
    {
        if ($method == 'recent') {
            return response()->json(Announcement::limit(6)->get());
        }

        return response()->json(Announcement::paginate(30));
    }

    /**
     * Store a newly created announcement in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $this->validate($request, Announcement::$rules);

        return response()->json(Announcement::create(
            $request->intersect(['user_id', 'message', 'visit'])
        ));
    }

    /**
     * Display the specified announcement.
     *
     * @param  \App\Team  $team
     * @return \Illuminate\Http\Response
     */
    public function show(Announcement $announcement)
    {
        return response()->json($announcement);
    }

    /**
     * Update the specified announcement in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Team  $team
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, Announcement $announcement)
    {
        return response()->json(tap($announcement)->update(
            $request->intersect(['user_id', 'message', 'visit'])
        ));
    }

    /**
     * Remove the specified announcement from storage.
     *
     * @param  \App\Team  $team
     * @return \Illuminate\Http\Response
     */
    public function destroy(Announcement $announcement)
    {
        $announcement->delete();
    }
}
