<?php

namespace ZapsterStudios\TeamPay\Controllers;

use App\Team;
use App\User;
use Illuminate\Http\Request;
use ZapsterStudios\TeamPay\Events\Teams\TeamCreated;
use ZapsterStudios\TeamPay\Events\Users\UserCreated;
use ZapsterStudios\TeamPay\Events\Users\UserUpdated;

class AccountController extends Controller
{
    /**
     * Retrieve the authenticated user.
     *
     * @return Response
     */
    public function show()
    {
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
            'team' => Team::$rules['name'],
            'email' => 'required|email|unique:users',
            'password' => 'required|confirmed',
            'country' => 'required',
        ]);

        $user = User::create(array_merge([
            'password' => bcrypt($request->password),
            'email_token' => str_random(32),
        ], $request->only('name', 'email', 'country')));

        event(new UserCreated($user));

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
        $user = auth()->user();
        $this->validate($request, [
            'name' => 'sometimes|required|min:2',
            'email' => 'sometimes|required|email|unique:users,email,'.$user->email,
            'country' => 'sometimes|required',
        ]);

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
     * Display an authenticated users notifications.
     *
     * @param  Request  $request
     * @param  string  $method
     * @return Response
     */
    public function notifications(Request $request, $method = 'recent')
    {
        abort_if(! $request->user()->tokenCan('view-notifications'), 403);

        if ($method == 'recent') {
            return response()->json($request->user()->notifications()->limit(6));
        }

        return response()->json($request->user()->notifications()->paginate(30));
    }
}
