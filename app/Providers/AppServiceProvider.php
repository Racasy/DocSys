<?php

namespace App\Providers;

use Illuminate\Support\Facades\Vite;
use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\Event; // Import the Event facade
use App\Listeners\LogSuccessfulLogin;

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
        Vite::prefetch(concurrency: 3);

        Event::listen(
            \Illuminate\Auth\Events\Login::class,  // The event to listen for
            [LogSuccessfulLogin::class, 'handle']   // The listener and its handle method
        );
    }
}
