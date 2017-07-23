<?php

namespace ZapsterStudios\TeamPay\Controllers\Account;

use TeamPay;
use App\Team;
use App\User;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use ZapsterStudios\TeamPay\Events\Teams\TeamCreated;
use ZapsterStudios\TeamPay\Events\Users\UserCreated;
use ZapsterStudios\TeamPay\Events\Users\UserUpdated;
use ZapsterStudios\TeamPay\Notifications\EmailVerification;

class AccountController extends Controller
{
    /**
     * Retrieve the authenticated user.
     *
     * @return Response
     */
    public function show()
    {
        $this->authorize('view', User::class);

        return response()->json(auth()->user());
    }

    /**
     * Register a new user.
     *
     * @param  Request  $request
     * @return Response
     */
    public function store(Request $request)
    {
        $this->validate($request, [
            'name' => 'required|min:2',
            'team' => 'required|min:2|unique:teams,name',
            'email' => 'required|email|unique:users',
            'password' => 'required|min:6|confirmed',
            'country' => 'required|in:'.TeamPay::getCountryKeyString(),
        ]);

        $user = User::create(array_merge([
            'password' => bcrypt($request->password),
            'email_token' => str_random(32),
        ], $request->only('name', 'email', 'country')));

        event(new UserCreated($user));

        $user->notify(new EmailVerification($user, $user->email_token));

        $team = $user->ownedTeams()->create([
            'name' => $request->team,
            'slug' => Team::generateSlug(str_slug($request->team)),
        ]);

        $user->teams()->attach($team);
        $user->team_id = $team->id;
        $user->save();

        event(new TeamCreated($team));

        $user->team = $team;

        return response()->json($user);
    }

    /**
     * Update an existing user.
     *
     * @return Response
     */
    public function update(Request $request)
    {
        $this->authorize('update', User::class);
        $this->validate($request, [
            'name' => 'sometimes|required|min:2',
            'email' => 'sometimes|required|email|unique:users,email,'.$request->user()->email,
            'country' => 'sometimes|required|in:'.TeamPay::getCountryKeyString(),
        ]);

        $user = $request->user();
        $inputs = $request->intersect('name', 'email', 'country');
        if ($request->email && $user->email !== $request->email) {
            $inputs['email_verified'] = 0;
            $inputs['email_token'] = str_random(32);
        }

        $user->update($inputs);

        event(new UserUpdated($user));

        return response()->json($user);
    }

    /**
     * Verify account email.
     *
     * @param  string  $token
     * @return Response
     */
    public function verify($token)
    {
        $user = User::where('email_token', $token)->firstOrFail();

        $user->update([
            'email_verified' => 1,
            'email_token' => null,
        ]);

        return response()->json('Account verified.');
    }
}
