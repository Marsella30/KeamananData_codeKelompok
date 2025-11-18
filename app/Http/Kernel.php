<?php

namespace App\Http;

use Illuminate\Foundation\Http\Kernel as HttpKernel;

class Kernel extends HttpKernel
{
    /**
     * Global HTTP middleware stack.
     *
     * Middleware ini jalan di semua request (web & api)
     */
    protected $middleware = [
        // Middleware bawaan Laravel:
        \Illuminate\Foundation\Http\Middleware\TrustProxies::class,
        \Illuminate\Http\Middleware\HandleCors::class,
        \App\Http\Middleware\PreventRequestsDuringMaintenance::class,
        \Illuminate\Foundation\Http\Middleware\ValidatePostSize::class,
        \App\Http\Middleware\TrimStrings::class,
        \Illuminate\Foundation\Http\Middleware\ConvertEmptyStringsToNull::class,
    ];

    /**
     * Middleware groups.
     *
     * 'web' dan 'api'
     */
    protected $middlewareGroups = [

        // ============================================================
        // MIDDLEWARE WEB
        // ============================================================
        'web' => [
            \Illuminate\Cookie\Middleware\EncryptCookies::class,
            \Illuminate\Cookie\Middleware\AddQueuedCookiesToResponse::class,
            \Illuminate\Session\Middleware\StartSession::class,
            \Illuminate\Session\Middleware\AuthenticateSession::class,
            \Illuminate\View\Middleware\ShareErrorsFromSession::class,
            \App\Http\Middleware\VerifyCsrfToken::class,
            \Illuminate\Routing\Middleware\SubstituteBindings::class,
        ],

        // ============================================================
        // MIDDLEWARE API
        // ============================================================
        'api' => [
            // Rate limiting default Laravel
            \Illuminate\Routing\Middleware\ThrottleRequests::class . ':api',

            // Log semua request API ke storage/logs/api.log
            \App\Http\Middleware\ApiLogMiddleware::class,

            \Illuminate\Routing\Middleware\SubstituteBindings::class,
            
        ],
    ];

    /**
     * Route middleware.
     * Bisa dipasang manual pada route.
     */
    protected $routeMiddleware = [
        'auth' => \App\Http\Middleware\Authenticate::class,
        'auth.basic' => \Illuminate\Auth\Middleware\AuthenticateWithBasicAuth::class,
        'auth.session' => \Illuminate\Session\Middleware\AuthenticateSession::class,
        'cache.headers' => \Illuminate\Http\Middleware\SetCacheHeaders::class,
        'can' => \Illuminate\Auth\Middleware\Authorize::class,
        'guest' => \App\Http\Middleware\RedirectIfAuthenticated::class,
        'password.confirm' => \Illuminate\Auth\Middleware\RequirePassword::class,
        'signed' => \Illuminate\Routing\Middleware\ValidateSignature::class,
        'throttle' => \Illuminate\Routing\Middleware\ThrottleRequests::class,
        'verified' => \Illuminate\Auth\Middleware\EnsureEmailIsVerified::class,

        // Tambahan untuk JWT protected route
        'jwt.auth' => \App\Http\Middleware\JwtAuthMiddleware::class,
        //rate limiting
        'log.throttle' => \App\Http\Middleware\LogThrottle::class,

    ];
}
