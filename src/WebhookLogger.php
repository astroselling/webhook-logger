<?php

namespace Astroselling\WebhookLogger;

use Astroselling\WebhookLogger\Events\WebhookFailed;
use Astroselling\WebhookLogger\Events\WebhookProcessed;
use Astroselling\WebhookLogger\Events\WebhookReceived;

class WebhookLogger
{
    /**
     * @param  array<string, mixed>|null  $payload
     */
    public static function received(
        string $channelId,
        string $channelType,
        string $topic,
        ?string $webhookId = null,
        ?string $source = null,
        ?string $traceId = null,
        ?array $payload = null
    ): void {
        WebhookReceived::dispatch(
            $channelId,
            $channelType,
            $topic,
            $webhookId,
            $source,
            $traceId,
            $payload
        );
    }

    /**
     * @param  array<string, mixed>|null  $payload
     */
    public static function processed(
        string $channelId,
        string $channelType,
        string $topic,
        ?string $webhookId = null,
        ?string $source = null,
        ?float $processingTimeMs = null,
        ?string $traceId = null,
        ?array $payload = null
    ): void {
        WebhookProcessed::dispatch(
            $channelId,
            $channelType,
            $topic,
            $webhookId,
            $source,
            $processingTimeMs,
            $traceId,
            $payload
        );
    }

    /**
     * @param  array<string, mixed>|null  $payload
     */
    public static function failed(
        string $channelId,
        string $channelType,
        string $topic,
        ?string $webhookId = null,
        ?string $source = null,
        ?string $errorMessage = null,
        ?int $errorCode = null,
        ?float $processingTimeMs = null,
        ?string $traceId = null,
        ?array $payload = null
    ): void {
        WebhookFailed::dispatch(
            $channelId,
            $channelType,
            $topic,
            $webhookId,
            $source,
            $errorMessage,
            $errorCode,
            $processingTimeMs,
            $traceId,
            $payload
        );
    }
}
