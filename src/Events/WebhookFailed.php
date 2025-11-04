<?php

namespace Astroselling\WebhookLogger\Events;

use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class WebhookFailed
{
    use Dispatchable, SerializesModels;

    public function __construct(
        public string $channelId,
        public string $channelType,
        public string $topic,
        public ?string $webhookId = null,
        public ?string $source = null,
        public ?string $errorMessage = null,
        public ?int $errorCode = null,
        public ?float $processingTimeMs = null,
        public ?string $traceId = null,
        /** @var array<string, mixed>|null */
        public ?array $payload = null,
    ) {}
}
