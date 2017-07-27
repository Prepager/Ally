<?php

namespace ZapsterStudios\Ally\Controllers\Account;

use Hash;
use App\User;
use Illuminate\Http\Request;
use ZapsterStudios\Ally\Controllers\Controller;

class PasswordController extends Controller
{
    /**
     * Update an existing password.
     *
     * @return Response
     */
    public function update(Request $request)
    {
        $this->authorize('password', User::class);

        $user = $request->user();
        $this->validate($request, [
            'current' => 'required',
            'password' => 'required|min:6|confirmed',
        ]);

        if (! Hash::check($request->current, $user->password)) {
            return response()->json([
                'current' => [
                    lang('auth.failed'),
                ],
            ], 422);
        }

        return response()->json(tap($user)->update([
            'password' => bcrypt($request->password),
        ]), 200);
    }
}
