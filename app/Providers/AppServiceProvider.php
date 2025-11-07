<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use App\Models\Organisasi;
use Illuminate\Support\Facades\View;
use Illuminate\Support\Facades\Route;
use Illuminate\Pagination\Paginator;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        View::share('daftarOrganisasi', Organisasi::all());
        Paginator::useBootstrapFive();

        Route::prefix('api')
            ->middleware('api')
            ->group(base_path('routes/api.php'));

        Route::middleware('web')
            ->group(base_path('routes/web.php'));
    }
}
