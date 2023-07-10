<?php

namespace App\Http\Controllers;

use App\Models\Role;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;
use Prometheus\CollectorRegistry;
use Prometheus\Exception\MetricsRegistrationException;
use Prometheus\RenderTextFormat;

class MyMetricsController extends Controller
{
    private CollectorRegistry $registry;

    public function __construct(CollectorRegistry $registry)
    {
        $this->registry = $registry;
    }

    public function myMetrics(Request $request)
    {
        DB::connection()->enableQueryLog();
        $collectorRegistry = app(CollectorRegistry::class);

        // CPU usage metric
        $cpuUsage = sys_getloadavg()[0]; // Retrieves the average system load for the last minute
        $gauge = $collectorRegistry->getOrRegisterGauge(
            'spa',
            'cpu_usage_percentage',
            'CPU usage percentage'
        );
        $gauge->set($cpuUsage);

        //memory usage metric
        $memoryUsage = memory_get_usage(true);
        $gauge = $collectorRegistry->getOrRegisterGauge(
            'spa',
            'memory_usage_bytes',
            'Memory usage in bytes'
        );
        $gauge->set($memoryUsage);

        // Count the number of registered users
        $usersRegistered = User::count();
        $gauge = $collectorRegistry->getOrRegisterGauge(
            'spa',
            'users_registered_total',
            'Total number of registered users'
        );
        $gauge->set($usersRegistered);


        // Count the number of registered roles
        $usersRegistered = Role::count();
        $gauge = $collectorRegistry->getOrRegisterGauge(
            'spa',
            'roles_registered_total',
            'Total number of registered roles'
        );
        $gauge->set($usersRegistered);

        // Retrieve the metrics from the registry
        $renderer = new RenderTextFormat();
        $result = $renderer->render($collectorRegistry->getMetricFamilySamples());
        return response($result, 200)->header('Content-Type', RenderTextFormat::MIME_TYPE);
    }

}
