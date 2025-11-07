<?php

namespace App\Providers;

use Illuminate\Foundation\Support\Providers\EventServiceProvider as ServiceProvider;
use Illuminate\Support\Facades\Event;

class EventServiceProvider extends ServiceProvider
{
    /**
     * Peta event‐to‐listener aplikasi.
     *
     * @var array<string, array<int, string>>
     */
    protected $listen = [
        //
    ];

    /**
     * Bootstrap event listener apa pun.
     */
    public function boot(): void
    {
        parent::boot();

        //
    }
}
