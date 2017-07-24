<?php

namespace ZapsterStudios\Ally\Controllers;

use Ally;
use Braintree\ClientToken;
use Illuminate\Http\Request;
use Illuminate\Routing\Router;
use App\Http\Controllers\Controller;

class AppController extends Controller
{
    /**
     * Display a listing of the app settings.
     *
     * @return Response
     */
    public function index()
    {
        return response()->json([
            'plans' => Ally::plans(),
            'groups' => Ally::groups(),
        ]);
    }

    /**
     * Display a listing of the app settings.
     *
     * @param  \Illuminate\Routing\Router  $router
     * @return Response
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
     * @return Response
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
