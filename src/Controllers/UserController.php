<?php

namespace ZapsterStudios\TeamPay\Controllers;

use App\User;
use Illuminate\Http\Request;
use ZapsterStudios\TeamPay\Events\Users\UserCreated;
use ZapsterStudios\TeamPay\Events\Users\UserUpdated;

class UserController extends Controller
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
        $this->validate($request, User::$rules);

        $user = User::create(array_merge([
            'password' => bcrypt($request->password),
            'email_token' => str_random(32),
        ], $request->only('name', 'email', 'country')));

        event(new UserCreated($user));

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
}
