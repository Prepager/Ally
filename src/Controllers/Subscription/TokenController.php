<?php

namespace ZapsterStudios\Ally\Controllers\Subscription;

use App\Team;
use Braintree\ClientToken;
use App\Http\Controllers\Controller;

class TokenController extends Controller
{
    /**
     * Return a new billing provider token.
     *
     * @param  \App\Team  $team
     * @return Response
     */
    public function store(Team $team)
    {
        $this->authorize('billing', $team);

        return response()->json([
            'token' => ClientToken::generate([
                'customerId' => ($team ? $team->braintree_id : ''),
            ]),
        ]);
    }
}
