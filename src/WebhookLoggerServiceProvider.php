<?php

namespace Astroselling\WebhookLogger;

use Astroselling\WebhookLogger\Events\WebhookFailed;
use Astroselling\WebhookLogger\Events\WebhookProcessed;
use Astroselling\WebhookLogger\Events\WebhookReceived;
use Astroselling\WebhookLogger\Listeners\WebhookMetricsListener;
use Illuminate\Support\Facades\Event;
use Spatie\LaravelPackageTools\Package;
use Spatie\LaravelPackageTools\PackageServiceProvider;

class WebhookLoggerServiceProvider extends PackageServiceProvider
{
    /**
     * @var array<class-string, array<int, class-string>>
     */
    protected array $listen = [
        WebhookReceived::class => [
            WebhookMetricsListener::class,
        ],
        WebhookProcessed::class => [
            WebhookMetricsListener::class,
        ],
        WebhookFailed::class => [
            WebhookMetricsListener::class,
        ],
    ];

    public function configurePackage(Package $package): void
    {
        $package
            ->name('webhook-logger')
            ->hasConfigFile();
    }

    public function packageBooted(): void
    {
        foreach ($this->listen as $event => $listeners) {
            foreach ($listeners as $listener) {
                Event::listen($event, $listener);
            }
        }
    }
}
