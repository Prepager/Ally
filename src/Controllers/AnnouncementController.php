<?php

namespace ZapsterStudios\Ally\Controllers;

use Illuminate\Http\Request;
use ZapsterStudios\Ally\Models\Announcement;
use ZapsterStudios\Ally\Events\Announcements\AnnouncementCreated;

class AnnouncementController extends Controller
{
    /**
     * Display a listing of the announcements.
     *
     * @param  string  $method
     * @return Response
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
     * @param  Request  $request
     * @return Response
     */
    public function store(Request $request)
    {
        $this->validate($request, [
            'message' => 'required',
            'visit' => 'required',
        ]);

        $announcement = Announcement::create(
            $request->only(['user_id', 'message', 'visit'])
        );

        event(new AnnouncementCreated($announcement));

        return response()->json($announcement);
    }

    /**
     * Display the specified announcement.
     *
     * @param  \ZapsterStudios\Ally\Models\Announcement  $announcement
     * @return Response
     */
    public function show(Announcement $announcement)
    {
        return response()->json($announcement);
    }

    /**
     * Update the specified announcement in storage.
     *
     * @param  Request  $request
     * @param  \ZapsterStudios\Ally\Models\Announcement  $announcement
     * @return Response
     */
    public function update(Request $request, Announcement $announcement)
    {
        return response()->json(tap($announcement)->update(
            $request->only(['user_id', 'message', 'visit'])
        ));
    }

    /**
     * Remove the specified announcement from storage.
     *
     * @param  \ZapsterStudios\Ally\Models\Announcement  $announcement
     * @return Response
     */
    public function destroy(Announcement $announcement)
    {
        $announcement->delete();
    }
}
