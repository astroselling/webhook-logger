# This is my package webhook-logger

[![Latest Version on Packagist](https://img.shields.io/packagist/v/astroselling/webhook-logger.svg?style=flat-square)](https://packagist.org/packages/astroselling/webhook-logger)
[![GitHub Tests Action Status](https://img.shields.io/github/actions/workflow/status/astroselling/webhook-logger/run-tests.yml?branch=main&label=tests&style=flat-square)](https://github.com/astroselling/webhook-logger/actions?query=workflow%3Arun-tests+branch%3Amain)
[![GitHub Code Style Action Status](https://img.shields.io/github/actions/workflow/status/astroselling/webhook-logger/fix-php-code-style-issues.yml?branch=main&label=code%20style&style=flat-square)](https://github.com/astroselling/webhook-logger/actions?query=workflow%3A"Fix+PHP+code+style+issues"+branch%3Amain)
[![Total Downloads](https://img.shields.io/packagist/dt/astroselling/webhook-logger.svg?style=flat-square)](https://packagist.org/packages/astroselling/webhook-logger)

Un paquete Laravel para loguear webhooks recibidos, procesados y fallidos usando eventos de Laravel. Este paquete permite registrar m?tricas detalladas de webhooks en la base de datos para facilitar el monitoreo y debugging.

## Support us

[<img src="https://github-ads.s3.eu-central-1.amazonaws.com/webhook-logger.jpg?t=1" width="419px" />](https://spatie.be/github-ad-click/webhook-logger)

We invest a lot of resources into creating [best in class open source packages](https://spatie.be/open-source). You can support us by [buying one of our paid products](https://spatie.be/open-source/support-us).

We highly appreciate you sending us a postcard from your hometown, mentioning which of our package(s) you are using. You'll find our address on [our contact page](https://spatie.be/about-us). We publish all received postcards on [our virtual postcard wall](https://spatie.be/open-source/postcards).

## Installation

You can install the package via composer:

```bash
composer require astroselling/webhook-logger
```

You can publish the config file with:

```bash
php artisan vendor:publish --tag="webhook-logger-config"
```

This is the contents of the published config file:

```php
return [
];
```

Optionally, you can publish the views using

```bash
php artisan vendor:publish --tag="webhook-logger-views"
```

## Usage

### Disparar eventos directamente

Puedes disparar eventos directamente usando la clase `WebhookLogger`:

```php
use Astroselling\WebhookLogger\Facades\WebhookLogger;

// Cuando recibes un webhook
WebhookLogger::received(
    channelId: (string) $channel->id,
    channelType: $channel->human_type,
    topic: $topic,
    webhookId: $webhookId,
    source: 'WebhookController',
    traceId: $traceId
);

// Cuando procesas exitosamente un webhook
$processingTime = (microtime(true) - $startTime) * 1000;
WebhookLogger::processed(
    channelId: (string) $channel->id,
    channelType: $channel->human_type,
    topic: $topic,
    webhookId: $webhookId,
    source: 'WebhookJob',
    processingTimeMs: $processingTime,
    traceId: $traceId
);

// Cuando falla el procesamiento de un webhook
$processingTime = (microtime(true) - $startTime) * 1000;
WebhookLogger::failed(
    channelId: (string) $channel->id,
    channelType: $channel->human_type,
    topic: $topic,
    webhookId: $webhookId,
    source: 'WebhookJob',
    errorMessage: $e->getMessage(),
    errorCode: is_numeric($e->getCode()) ? (int) $e->getCode() : null,
    processingTimeMs: $processingTime,
    traceId: $traceId,
    payload: $payload
);
```

### Usar eventos directamente

Tambi?n puedes usar los eventos directamente:

```php
use Astroselling\WebhookLogger\Events\WebhookReceived;
use Astroselling\WebhookLogger\Events\WebhookProcessed;
use Astroselling\WebhookLogger\Events\WebhookFailed;

// Disparar evento recibido
WebhookReceived::dispatch(
    (string) $channel->id,
    $channel->human_type,
    $topic,
    $webhookId,
    'WebhookController',
    $traceId
);

// Disparar evento procesado
WebhookProcessed::dispatch(
    (string) $channel->id,
    $channel->human_type,
    $topic,
    $webhookId,
    'WebhookJob',
    $processingTime,
    $traceId
);

// Disparar evento fallido
WebhookFailed::dispatch(
    (string) $channel->id,
    $channel->human_type,
    $topic,
    $webhookId,
    'WebhookJob',
    $e->getMessage(),
    is_numeric($e->getCode()) ? (int) $e->getCode() : null,
    $processingTime,
    $traceId,
    $payload
);
```

### Ejemplo completo en un Job

```php
<?php

namespace App\Jobs;

use Astroselling\WebhookLogger\Facades\WebhookLogger;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;

class WebhookJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    protected Channel $channel;
    protected array $product;
    protected string $topic;
    protected ?string $traceId;

    public function __construct(Channel $channel, array $product, string $topic, ?string $traceId = null)
    {
        $this->channel = $channel;
        $this->product = $product;
        $this->topic = $topic;
        $this->traceId = $traceId;
        $this->onQueue('webhooks');
    }

    public function handle()
    {
        $startTime = microtime(true);
        
        try {
            // Tu l?gica de procesamiento aqu?
            // (new DispatchQuickUpdateWebhookAction)->execute(...);
            
            $processingTime = (microtime(true) - $startTime) * 1000;
            WebhookLogger::processed(
                channelId: (string) $this->channel->id,
                channelType: $this->channel->human_type,
                topic: $this->topic,
                source: 'WebhookJob',
                processingTimeMs: $processingTime,
                traceId: $this->traceId
            );
        } catch (\Exception $e) {
            $processingTime = (microtime(true) - $startTime) * 1000;
            WebhookLogger::failed(
                channelId: (string) $this->channel->id,
                channelType: $this->channel->human_type,
                topic: $this->topic,
                source: 'WebhookJob',
                errorMessage: $e->getMessage(),
                errorCode: is_numeric($e->getCode()) ? (int) $e->getCode() : null,
                processingTimeMs: $processingTime,
                traceId: $this->traceId,
                payload: $this->product
            );
            
            throw $e;
        }
    }
}
```

### Configuraci?n del canal de logging

Para que los logs se registren correctamente, necesitas configurar el canal de logging. Por defecto el paquete usa el canal `webhooks`, pero puedes cambiarlo a trav?s de la configuraci?n:

1. Publica el archivo de configuraci?n:
```bash
php artisan vendor:publish --tag="webhook-logger-config"
```

2. Configura el canal en tu archivo `.env` (opcional):
```env
WEBHOOK_LOGGER_CHANNEL=webhooks
```

3. Configura el canal en tu archivo `config/logging.php`:

```php
'channels' => [
    // ... otros canales ...
    
    'webhooks' => [
        'driver' => 'single',
        'path' => storage_path('logs/webhooks.log'),
        'level' => env('LOG_LEVEL', 'debug'),
    ],
    
    // O si prefieres usar un canal diario:
    'webhooks' => [
        'driver' => 'daily',
        'path' => storage_path('logs/webhooks.log'),
        'level' => env('LOG_LEVEL', 'debug'),
        'days' => 14,
    ],
    
    // O para usar CloudWatch, Elasticsearch, etc.:
    'webhooks' => [
        'driver' => 'custom',
        'via' => YourCustomLogger::class,
    ],
],
```

Los logs se guardan con formato estructurado JSON, facilitando su procesamiento para dashboards y m?tricas operativas. Todos los eventos incluyen campos estructurados como `event_type`, `channel_id`, `channel_type`, `topic`, `trace_id`, y `timestamp`, lo que permite f?cilmente consultar y analizar los logs para generar dashboards operativos.

## Testing

```bash
composer test
```

## Changelog

Please see [CHANGELOG](CHANGELOG.md) for more information on what has changed recently.

## Contributing

Please see [CONTRIBUTING](CONTRIBUTING.md) for details.

## Security Vulnerabilities

Please review [our security policy](../../security/policy) on how to report security vulnerabilities.

## Credits

- [isajar](https://github.com/astroselling)
- [All Contributors](../../contributors)

## License

The MIT License (MIT). Please see [License File](LICENSE.md) for more information.


- [isajar](https://github.com/astroselling)
- [All Contributors](../../contributors)

## License

The MIT License (MIT). Please see [License File](LICENSE.md) for more information.
