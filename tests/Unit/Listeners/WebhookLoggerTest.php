<?php

use Astroselling\WebhookLogger\Events\WebhookFailed;
use Astroselling\WebhookLogger\Events\WebhookProcessed;
use Astroselling\WebhookLogger\Events\WebhookReceived;
use Astroselling\WebhookLogger\WebhookLogger;
use Illuminate\Support\Facades\Event;

beforeEach(function () {
    Event::fake();
});

it('dispatches WebhookReceived event when received method is called', function () {
    $channelId = 'channel-123';
    $channelType = 'shopify';
    $topic = 'orders/create';
    $webhookId = 'webhook-456';
    $source = 'api';
    $traceId = 'trace-789';
    $payload = ['order_id' => 12345, 'status' => 'pending'];

    WebhookLogger::received($channelId, $channelType, $topic, $webhookId, $source, $traceId, $payload);

    Event::assertDispatched(WebhookReceived::class, function ($event) use ($channelId, $channelType, $topic, $webhookId, $source, $traceId, $payload) {
        return $event->channelId === $channelId
            && $event->channelType === $channelType
            && $event->topic === $topic
            && $event->webhookId === $webhookId
            && $event->source === $source
            && $event->traceId === $traceId
            && $event->payload === $payload;
    });
});

it('dispatches WebhookReceived event with null optional parameters', function () {
    $channelId = 'channel-123';
    $channelType = 'shopify';
    $topic = 'orders/create';

    WebhookLogger::received($channelId, $channelType, $topic);

    Event::assertDispatched(WebhookReceived::class, function ($event) use ($channelId, $channelType, $topic) {
        return $event->channelId === $channelId
            && $event->channelType === $channelType
            && $event->topic === $topic
            && $event->webhookId === null
            && $event->source === null
            && $event->traceId === null
            && $event->payload === null;
    });
});

it('dispatches WebhookProcessed event when processed method is called', function () {
    $channelId = 'channel-123';
    $channelType = 'shopify';
    $topic = 'orders/create';
    $webhookId = 'webhook-456';
    $source = 'api';
    $processingTimeMs = 150.5;
    $traceId = 'trace-789';
    $payload = ['order_id' => 12345, 'status' => 'pending'];

    WebhookLogger::processed($channelId, $channelType, $topic, $webhookId, $source, $processingTimeMs, $traceId, $payload);

    Event::assertDispatched(WebhookProcessed::class, function ($event) use ($channelId, $channelType, $topic, $webhookId, $source, $processingTimeMs, $traceId, $payload) {
        return $event->channelId === $channelId
            && $event->channelType === $channelType
            && $event->topic === $topic
            && $event->webhookId === $webhookId
            && $event->source === $source
            && $event->processingTimeMs === $processingTimeMs
            && $event->traceId === $traceId
            && $event->payload === $payload;
    });
});

it('dispatches WebhookProcessed event with null optional parameters', function () {
    $channelId = 'channel-123';
    $channelType = 'shopify';
    $topic = 'orders/create';

    WebhookLogger::processed($channelId, $channelType, $topic);

    Event::assertDispatched(WebhookProcessed::class, function ($event) use ($channelId, $channelType, $topic) {
        return $event->channelId === $channelId
            && $event->channelType === $channelType
            && $event->topic === $topic
            && $event->webhookId === null
            && $event->source === null
            && $event->processingTimeMs === null
            && $event->traceId === null
            && $event->payload === null;
    });
});

it('dispatches WebhookFailed event when failed method is called', function () {
    $channelId = 'channel-123';
    $channelType = 'shopify';
    $topic = 'orders/create';
    $webhookId = 'webhook-456';
    $source = 'api';
    $errorMessage = 'Processing failed';
    $errorCode = 500;
    $processingTimeMs = 250.75;
    $traceId = 'trace-789';
    $payload = ['order_id' => 12345, 'status' => 'pending'];

    WebhookLogger::failed(
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

    Event::assertDispatched(WebhookFailed::class, function ($event) use ($channelId, $channelType, $topic, $webhookId, $source, $errorMessage, $errorCode, $processingTimeMs, $traceId, $payload) {
        return $event->channelId === $channelId
            && $event->channelType === $channelType
            && $event->topic === $topic
            && $event->webhookId === $webhookId
            && $event->source === $source
            && $event->errorMessage === $errorMessage
            && $event->errorCode === $errorCode
            && $event->processingTimeMs === $processingTimeMs
            && $event->traceId === $traceId
            && $event->payload === $payload;
    });
});

it('dispatches WebhookFailed event with null optional parameters', function () {
    $channelId = 'channel-123';
    $channelType = 'shopify';
    $topic = 'orders/create';

    WebhookLogger::failed($channelId, $channelType, $topic);

    Event::assertDispatched(WebhookFailed::class, function ($event) use ($channelId, $channelType, $topic) {
        return $event->channelId === $channelId
            && $event->channelType === $channelType
            && $event->topic === $topic
            && $event->webhookId === null
            && $event->source === null
            && $event->errorMessage === null
            && $event->errorCode === null
            && $event->processingTimeMs === null
            && $event->traceId === null
            && $event->payload === null;
    });
});
