<?php

namespace Astroselling\WebhookLogger\Listeners;

use Astroselling\WebhookLogger\Events\WebhookFailed;
use Astroselling\WebhookLogger\Events\WebhookProcessed;
use Astroselling\WebhookLogger\Events\WebhookReceived;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Support\Facades\Log;

class WebhookMetricsListener implements ShouldQueue
{
    use InteractsWithQueue;

    public function handle(WebhookReceived|WebhookProcessed|WebhookFailed $event): void
    {
        match (true) {
            $event instanceof WebhookReceived => $this->handleWebhookReceived($event),
            $event instanceof WebhookProcessed => $this->handleWebhookProcessed($event),
            $event instanceof WebhookFailed => $this->handleWebhookFailed($event),
        };
    }

    private function getLogChannel(): string
    {
        $channel = config('webhook-logger.log_channel', 'webhooks');

        return is_string($channel) ? $channel : 'webhooks';
    }

    private function handleWebhookReceived(WebhookReceived $event): void
    {
        Log::channel($this->getLogChannel())->info('Webhook received', [
            'event_type' => 'webhook_received',
            'channel_id' => $event->channelId,
            'channel_type' => $event->channelType,
            'topic' => $event->topic,
            'webhook_id' => $event->webhookId,
            'source' => $event->source,
            'trace_id' => $event->traceId,
            'payload' => $event->payload,
            'timestamp' => now()->toISOString(),
        ]);
    }

    private function handleWebhookProcessed(WebhookProcessed $event): void
    {
        Log::channel($this->getLogChannel())->info('Webhook processed successfully', [
            'event_type' => 'webhook_processed',
            'channel_id' => $event->channelId,
            'channel_type' => $event->channelType,
            'topic' => $event->topic,
            'webhook_id' => $event->webhookId,
            'source' => $event->source,
            'processing_time_ms' => $event->processingTimeMs,
            'trace_id' => $event->traceId,
            'payload' => $event->payload,
            'timestamp' => now()->toISOString(),
        ]);
    }

    private function handleWebhookFailed(WebhookFailed $event): void
    {
        Log::channel($this->getLogChannel())->error('Webhook processing failed', [
            'event_type' => 'webhook_failed',
            'channel_id' => $event->channelId,
            'channel_type' => $event->channelType,
            'topic' => $event->topic,
            'webhook_id' => $event->webhookId,
            'source' => $event->source,
            'error_message' => $event->errorMessage,
            'error_code' => $event->errorCode,
            'processing_time_ms' => $event->processingTimeMs,
            'trace_id' => $event->traceId,
            'payload' => $event->payload,
            'timestamp' => now()->toISOString(),
        ]);
    }
}
