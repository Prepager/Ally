<?php

namespace ZapsterStudios\Ally\Controllers\Account;

use Illuminate\Http\Request;
use ZapsterStudios\Ally\Controllers\Controller;
use Illuminate\Notifications\DatabaseNotification;

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
        $this->authorize('view', DatabaseNotification::class);

        if ($method == 'recent') {
            return response()->json($request->user()->notifications()->limit(6)->get());
        }

        return response()->json($request->user()->notifications()->paginate(30));
    }

    /**
     * Display an authenticated users notification.
     *
     * @param  string  $notification
     * @return Response
     */
    public function show($notification)
    {
        $notification = DatabaseNotification::findOrFail($notification);
        $this->authorize('view', $notification);

        return response()->json($notification);
    }

    /**
     * Set the read status of a notification.
     *
     * @param  Request  $request
     * @param  \Illuminate\Notifications\DatabaseNotification  $notification
     * @return Response
     */
    public function update(Request $request, DatabaseNotification $notification)
    {
        $this->authorize('update', $notification);

        if (! $request->read) {
            $notification->update([
                'read_at' => null,
            ]);
        } else {
            $notification->markAsRead();
        }

        return response()->json($notification);
    }

    /**
     * Delete a user notification.
     *
     * @param  Request  $request
     * @param  \Illuminate\Notifications\DatabaseNotification  $notification
     * @return Response
     */
    public function destroy(Request $request, DatabaseNotification $notification)
    {
        $this->authorize('delete', $notification);

        $notification->delete();

        return response()->json('Notification deleated', 200);
    }
}
