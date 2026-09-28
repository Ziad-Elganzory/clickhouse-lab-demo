<?php

namespace App\Providers;

use App\Analytics\OrderAnalyticsReader;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        $this->app->bind(OrderAnalyticsReader::class, function ($app) {
        $driver = config('analytics.driver');
        $class = config("analytics.readers.{$driver}");
        return $app->make($class);
    });
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        if (! $this->app->environment('testing')) {
            $this->loadMigrationsFrom(database_path('migrations/clickhouse'));
        }
    }
}
