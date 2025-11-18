<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\RateLimiter;

class LogThrottle
{
    public function handle($request, Closure $next, $attempts, $decay)
    {
        $key = $this->resolveKey($request);

        // Jika sudah melewati batas sebelum request diproses
        if (RateLimiter::tooManyAttempts($key, $attempts)) {
            $retry = RateLimiter::availableIn($key);

            Log::warning("Rate limit triggered", [
                'ip' => $request->ip(),
                'url' => $request->path(),
                'attempts_allowed' => $attempts,
                'retry_after' => $retry
            ]);
        }

        return $next($request);
    }

    private function resolveKey($request)
    {
        return strtolower($request->method() . '|' . $request->ip());
    }
}
