<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\App;
use App\Models\Order;
use App\Observers\OrderObserver; // ✅ the only import you need

class AppServiceProvider extends ServiceProvider
{
    public function register() {}

    public function boot(): void
    {
        // Optional: avoid using request in console
        if (! $this->app->runningInConsole()) {
            App::setLocale(request()->getPreferredLanguage(['en', 'es', 'fr', 'de', 'ar']) ?? config('app.locale'));
        }

        // Guard so the app doesn’t crash before the file exists
        if (class_exists(OrderObserver::class)) {
            Order::observe(OrderObserver::class); // ✅ use FQCN, not a string
        }
    }
}
