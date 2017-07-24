<?php

namespace ZapsterStudios\Ally\Controllers\Account;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Notifications\Notification;

class NotificationController extends Controller
{
    /**
     * Display an authenticated users notifications.
     *
     * @param  Request  $request
     * @param  string  $method
     * @return Response
     */
    public function index(Request $request, $method = 'recent')
    {
        $this->authorize('view', Notification::class);

        if ($method == 'recent') {
            return response()->json($request->user()->notifications()->limit(6));
        }

        return response()->json($request->user()->notifications()->paginate(30));
    }

    /**
     * Set the read status of a notification.
     *
     * @param  Request  $request
     * @param  Notification  $notification
     * @return Response
     */
    public function update(Request $request, Notification $notification)
    {
        $this->authorize('update', $notification);

        //
    }

    /**
     * Delete a user notification.
     *
     * @param  Request  $request
     * @param  Notification  $notification
     * @return Response
     */
    public function destroy(Request $request, Notification $notification)
    {
        $this->authorize('delete', $notification);

        //
    }
}
