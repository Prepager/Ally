<?php

namespace ZapsterStudios\TeamPay\Middleware;

use Closure;

class DatabaseDebugger
{
    public function handle($request, Closure $next)
    {
        $response = $next($request);
        if (! config('app.debug') || config('app.env') != 'local') {
            return $response;
        }

        $data = $response->getData();
        if (is_array($data)) {
            $data['queries'] = \TeamPay::$queryLog;
        } else {
            $data->queries = \TeamPay::$queryLog;
        }

        return $response->setData($data);
    }
}
