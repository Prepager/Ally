<?php

namespace ZapsterStudios\TeamPay\Controllers\Dashboard;

use App\User;
use App\Http\Controllers\Controller;

class ImpersonationController extends Controller
{
    /**
     * Create a new impersonation token.
     *
     * @param  User  $user
     * @return Response
     */
    public function store(User $user)
    {
        $token = $user->createToken(config('app.name').' Administration', ['*']);

        return response()->json($token);
    }

    /**
     * Delete an impersonation token.
     *
     * @param  User  $user
     * @return Response
     */
    public function destroy()
    {
        $token = auth()->user()->token();

        if ($token) {
            $token->revoke();
            $token->delete();
        }

        return response()->json('Token revoked.');
    }
}
