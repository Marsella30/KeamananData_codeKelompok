<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class ApiLogMiddleware
{
    public function handle(Request $request, Closure $next)
    {
        // Log request masuk
        Log::channel('api')->info('API Request', [
            'method' => $request->method(),
            'path'   => $request->path(),
            'ip'     => $request->ip(),
            'params' => $request->all(),
            'time'   => now(),
        ]);

        $response = $next($request);

        // Log response keluar
        Log::channel('api')->info('API Response', [
            'status' => $response->status(),
            'time'   => now()
        ]);

        return $response;
    }
}
