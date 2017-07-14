<?php

namespace ZapsterStudios\TeamPay\Controllers;

use App\User;
use Illuminate\Http\Request;
use Laravel\Passport\Client;
use ZapsterStudios\TeamPay\Events\Users\UserCreated;

class AuthController extends Controller
{
    /**
     * Retrieve authenticated user.
     *
     * @return Response
     */
    public function user()
    {
        return response()->json(auth()->user());
    }

    /**
     * Authenticate user and return token.
     *
     * @param  Request  $request
     * @return Response
     */
    public function login(Request $request)
    {
        if (auth()->attempt(['email' => $request->email, 'password' => $request->password])) {
            return $this->proxy('password', [
                'username' => $request->email,
                'password' => $request->password,
                'scope' => '*',
            ]);
        }

        return response()->json(['message' => 'Incorrect email or password.'], 401);
    }

    /**
     * Refresh authentication token and return it.
     *
     * @param  Request  $request
     * @return Response
     */
    public function refresh(Request $request)
    {
        return $this->proxy('refresh_token', [
            'refresh_token' => $request->refresh_token,
            'scope' => '*',
        ]);
    }

    /**
     * Revoke current authentication token.
     *
     * @return void
     */
    public function logout()
    {
        $token = auth()->user()->token();

        if ($token) {
            $token->revoke();
            $token->delete();
        }
    }

    /**
     * Register and Authenticate user.
     *
     * @param  Request  $request
     * @return Response
     */
    public function register(Request $request)
    {
        $this->validate($request, User::$rules);

        $user = User::create(array_merge($request->except([
            '_method',
            'password',
            'password_confirmation',
        ]), [
            'password' => bcrypt($request->password),
        ]));

        event(new UserCreated($user));

        return response()->json($user);
    }

    /**
     * Proxy OAuth password calls using internal calls.
     *
     * @param  string  $grantType
     * @param  array  $data
     * @return Response
     */
    public function proxy($grantType, $data = [])
    {
        $client = Client::where('password_client', 1)->firstOrFail();

        $request = request()->create('/oauth/token', 'POST', array_merge($data, [
            'grant_type' => $grantType,
            'client_id' => $client->id,
            'client_secret' => $client->secret,
        ]));

        $response = app()->handle($request);

        return response()->json(json_decode($response->getContent()), $response->getStatusCode());
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
