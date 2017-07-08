<?php

namespace ZapsterStudios\TeamPay\Controllers;

use TeamPay;
use Braintree\ClientToken;
use Illuminate\Http\Request;

class AppController extends Controller
{
    /**
     * Display a listing of the app settings.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        return response()->json([
            'plans' => TeamPay::plans(),
        ]);
    }

    /**
     * Return a new billing provider token.
     *
     * @param  Request  $request
     * @return \Illuminate\Http\Response
     */
    public function token(Request $request)
    {
        $team = $request->user()->team;

        return response()->json([
            'token' => ClientToken::generate([
                'customerId' => ($team ? $team->braintree_id : ''),
            ]),
        ]);
    }
}
