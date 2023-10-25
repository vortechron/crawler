<?php

namespace App\Crawlers\Traits;

use App\Settings\CrawlerSettings;
use Illuminate\Support\Facades\Notification;
use Illuminate\Notifications\AnonymousNotifiable;

trait Notifiable
{
    public static function notifier(): AnonymousNotifiable
    {
        return Notification::route('mail', [
            app(CrawlerSettings::class)->notification_email => 'Hourglass',
        ]);
    }
}
