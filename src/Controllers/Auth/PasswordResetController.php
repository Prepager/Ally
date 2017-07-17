<?php

namespace ZapsterStudios\TeamPay\Controllers\Auth;

use App\User;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use ZapsterStudios\TeamPay\Models\PasswordReset;
use ZapsterStudios\TeamPay\Notifications\PasswordReset as PasswordResetMail;

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
