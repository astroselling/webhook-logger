<?php

namespace Astroselling\WebhookLogger\Facades;

use Illuminate\Support\Facades\Facade;

class WebhookLogger extends Facade
{
    protected static function getFacadeAccessor(): string
    {
        return \Astroselling\WebhookLogger\WebhookLogger::class;
    }
}
