<?php

namespace App\Providers;

use Illuminate\Auth\Events\Registered;
use Illuminate\Auth\Listeners\SendEmailVerificationNotification;
use Illuminate\Foundation\Support\Providers\EventServiceProvider as ServiceProvider;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Event;
use Prometheus\CollectorRegistry;

class EventServiceProvider extends ServiceProvider
{
    /**
     * The event to listener mappings for the application.
     *
     * @var array<class-string, array<int, class-string>>
     */
    protected $listen = [
        Registered::class => [
            SendEmailVerificationNotification::class,
        ],
    ];


    public function boot(): void
    {
        DB::listen(function ($query) {
            $duration = $query->time / 1000;
            $queryType = $this->getQueryType($query->sql);

            $queryCounter = app(CollectorRegistry::class)
                ->getOrRegisterCounter('spa', 'query_count', 'Total number of database queries', ['query_type']);

            $queryCounter->incBy(1, [$queryType]);
        });
    }

    private function getQueryType($sql)
    {
        $sql = strtolower($sql);
        if (str_starts_with($sql, 'select')) {
            return 'select';
        } elseif (str_starts_with($sql, 'insert')) {
            return 'insert';
        } elseif (str_starts_with($sql, 'update')) {
            return 'update';
        } elseif (str_starts_with($sql, 'delete')) {
            return 'delete';
        } else {
            return 'other';
        }
    }







    /**
     * Determine if events and listeners should be automatically discovered.
     */
    public function shouldDiscoverEvents(): bool
    {
        return false;
    }
}
