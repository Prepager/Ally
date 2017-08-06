<?php

namespace ZapsterStudios\Ally\Controllers;

use Ally;
use Illuminate\Routing\Router;

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
            $methods = collect($route->methods());

            return [
                $route->getName() => [
                    'method' => $methods->first(),
                    'url' => $route->uri()
                ],
            ];
        })->sortBy(function ($route, $key) {
            return $key;
        })->all();

        return response()->json($routes);
    }
}
