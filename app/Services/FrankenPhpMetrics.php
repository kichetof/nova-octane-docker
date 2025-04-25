<?php

namespace App\Services;

use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Str;
use Laravel\Octane\FrankenPhp\ServerProcessInspector;
use ReflectionMethod;

class FrankenPhpMetrics
{
    protected Collection $metrics;

    public function getRequestsTotal(): int
    {
        return $this->readMetricValue('frankenphp_worker_request_count');
    }

    public function getWorkersRestarts(): int
    {
        return $this->readMetricValue('frankenphp_worker_restarts');
    }

    protected function metricsUrl(): string
    {
        $adminConfigUrl = new ReflectionMethod(ServerProcessInspector::class, 'adminUrl');
        $adminConfigUrl->setAccessible(true);

        return $adminConfigUrl->invoke(app(ServerProcessInspector::class)).'/metrics';
    }

    protected function getMetrics(): void
    {
        $body = rescue(fn () => Http::acceptJson()->get($this->metricsUrl())->body(), '', false);

        $this->metrics = Str::of($body)->trim()->explode("\n")
            ->filter(fn (string $value) => ! str_starts_with($value, '#'));
    }

    protected function readMetricValue($metric): int
    {
        if (! isset($this->metrics)) {
            $this->getMetrics();
        }

        return (int) Str::afterLast(
            subject: (string) $this->metrics
                ->filter(fn (string $value) => str_starts_with($value, $metric))
                ->first(),
            search: ' '
        );
    }
}
