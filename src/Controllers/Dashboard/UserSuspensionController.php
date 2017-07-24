<?php

namespace ZapsterStudios\Ally\Controllers\Dashboard;

use App\User;
use Carbon\Carbon;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use ZapsterStudios\Ally\Events\Users\UserSuspended;

class UserSuspensionController extends Controller
{
    /**
     * Suspend user.
     *
     * @param  Request  $request
     * @param  \App\User  $user
     * @return Response
     */
    public function store(Request $request, User $user)
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
     * @param  Request  $request
     * @param  \App\User  $user
     * @return Response
     */
    public function destroy(Request $request, User $user)
    {
        $user->suspended_at = null;
        $user->suspended_to = null;
        $user->suspended_reason = null;

        return response()->json(tap($user)->save());
    }
}
