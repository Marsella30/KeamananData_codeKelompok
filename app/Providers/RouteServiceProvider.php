<?php

namespace App\Providers;

use Illuminate\Foundation\Support\Providers\RouteServiceProvider as ServiceProvider;
use Illuminate\Support\Facades\Route;
use Illuminate\Cache\RateLimiting\Limit;
use Illuminate\Support\Facades\RateLimiter;

class RouteServiceProvider extends ServiceProvider
{
    /**
     * Nilai root path ketika pengguna berhasil login.
     *
     * @var string
     */
    public const HOME = '/home';

    /**
     * Daftarkan model binding, pattern filter, dll.
     */
    // public function boot(): void
    // {
    //     parent::boot();
    // }
    public function boot()
    {
        RateLimiter::for('login', function ($request) {
            return Limit::perMinute(2)->by($request->ip());
        });
    }

    /**
     * Define route‐loading untuk aplikasi.
     */
    public function map(): void
    {
        $this->mapApiRoutes();
        $this->mapWebRoutes();
    }

    /**
     * Define route "web" untuk aplikasi.
     */
    protected function mapWebRoutes(): void
    {
        Route::middleware('web')
             ->group(base_path('routes/web.php'));
    }

    /**
     * Define route "api" untuk aplikasi.
     */
    protected function mapApiRoutes(): void
    {
        Route::prefix('api')
             ->middleware('api')
             ->group(base_path('routes/api.php'));
    }
}
