<?php

namespace App\Settings;

use Spatie\LaravelSettings\Settings;

class CrawlerSettings extends Settings
{
    public ?string $strategy;
    public ?string $url;
    public ?string $notification_email;

    public static function group(): string
    {
        return 'crawler';
    }
}
