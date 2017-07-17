<?php

namespace ZapsterStudios\TeamPay\Controllers;

use TeamPay;
use Braintree\ClientToken;
use Illuminate\Http\Request;
use Illuminate\Routing\Router;
use App\Http\Controllers\Controller;

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
            'groups' => TeamPay::groups(),
        ]);
    }

    /**
     * Display a listing of the app settings.
     *
     * @param  Router  $router
     * @return \Illuminate\Http\Response
     */
    public function routes(Router $router)
    {
        $routes = collect($router->getRoutes())->reject(function ($route) {
            return ! $route->getName();
        })->mapWithKeys(function ($route) {
            return [
                $route->getName() => $route->uri(),
            ];
        })->sortBy(function ($route, $key) {
            return $key;
        })->all();

        return response()->json($routes);
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
