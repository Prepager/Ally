<?php

namespace ZapsterStudios\Ally\Controllers\Auth;

use App\User;
use Illuminate\Http\Request;
use Laravel\Passport\Client;
use ZapsterStudios\Ally\Controllers\Controller;

class AuthController extends Controller
{
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

        return response()->json('Token revoked.');
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
}
