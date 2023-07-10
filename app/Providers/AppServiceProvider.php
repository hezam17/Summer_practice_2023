<?php

namespace App\Providers;

use Illuminate\Http\Resources\Json\JsonResource;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\ServiceProvider;
use App\Http\Middleware\RequestsMiddleware;
use Prometheus\CollectorRegistry;
use Prometheus\Storage\InMemory;


class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        $this->app->singleton(CollectorRegistry::class, function ($app) {
            return new CollectorRegistry(new InMemory());
        });
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        DB::connection()->enableQueryLog();

        JsonResource::withoutWrapping();
    }
}
