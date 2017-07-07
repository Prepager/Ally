<?php

namespace ZapsterStudios\TeamPay\Controllers;

use TeamPay;
use Braintree\ClientToken;

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
     * @return \Illuminate\Http\Response
     */
    public function token()
    {
        return response()->json([
            'token' => ClientToken::generate([
                'customerId' => $team->braintree_id,
            ]),
        ]);
    }
}
