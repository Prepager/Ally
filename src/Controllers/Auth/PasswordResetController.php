<?php

namespace ZapsterStudios\Ally\Controllers\Auth;

use App\User;
use Illuminate\Http\Request;
use ZapsterStudios\Ally\Models\PasswordReset;
use ZapsterStudios\Ally\Controllers\Controller;
use ZapsterStudios\Ally\Notifications\PasswordReset as PasswordResetMail;

class PasswordResetController extends Controller
{
    /**
     * Create a new password reset.
     *
     * @param  Request  $request
     * @return Response
     */
    public function store(Request $request)
    {
        $this->validate($request, [
            'email' => 'required|email',
        ]);

        $user = User::where('email', $request->email)->firstOrFail();

        $reset = PasswordReset::forceCreate([
            'email' => $request->email,
            'token' => str_random(16),
        ]);

        $user->notify(new PasswordResetMail($user, $reset->token));

        return response()->json('Password reset email sent.');
    }

    /**
     * Update an existing password requested to be reset.
     *
     * @param  Request  $request
     * @param  \ZapsterStudios\Ally\Models\PasswordReset  $reset
     * @return Response
     */
    public function update(Request $request, PasswordReset $reset)
    {
        $this->validate($request, [
            'password' => 'required|min:6|confirmed',
        ]);

        $user = User::where('email', $reset->email)->firstOrFail();
        $user->update([
            'password' => bcrypt($reset->password),
        ]);

        return response()->json('Password reset.');
    }
}
