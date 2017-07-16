<?php

namespace ZapsterStudios\TeamPay\Middleware;

use Closure;

class DatabaseDebugger
{
    public function handle($request, Closure $next)
    {
        $response = $next($request);
        if (! env('DB_LOGGER') || ! method_exists($response, 'getData')) {
            return $response;
        }

        $data = $response->getData();
        if (is_string($data)) {
            $data = [
                'message' => $data,
            ];
        }

        if (is_array($data)) {
            $data['queries'] = \TeamPay::$queryLog;
        } else {
            $data->queries = \TeamPay::$queryLog;
        }

        return $response->setData($data);
    }
}
