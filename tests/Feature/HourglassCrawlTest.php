<?php

namespace Tests\Feature;

use App\Notifications\CrawlCompleted;
use App\Notifications\CrawlNotice;
use Tests\TestCase;
use App\Settings\CrawlerSettings;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Support\Facades\Notification;
use Illuminate\Notifications\AnonymousNotifiable;
use Illuminate\Foundation\Testing\RefreshDatabase;

class HourglassCrawlTest extends TestCase
{
    use RefreshDatabase;

    /**
     * A basic feature test example.
     */
    public function test_crawl(): void
    {
        Notification::fake();

        $this->assertDatabaseCount('products', 0);

        $crawlerSettings = $this->crawlerSettings("catalogs.html");
        $hourglass = new \App\Crawlers\Hourglass($crawlerSettings);

        $hourglass->settings()->brands = ['test-brand'];
        $hourglass->settings()->save();

        $hourglass->handle();

        $this->assertDatabaseCount('products', 1);
        $this->assertDatabaseHas('products', [
            'sku' => '7040/250G-001',
            'name' => 'Grand Complications Ladies\' Minute Repeater',
            'description' => 'test',
            'price' => 0,
            'stock' => 1,
            'meta->movement' => 'Mechanical self-winding movement',
            'meta->case material' => 'White Gold',
        ]);

        Notification::assertSentTo(
            new AnonymousNotifiable(),
            CrawlCompleted::class,
            function ($notification, $channels, $notifiable) use ($crawlerSettings) {
                return array_keys($notifiable->routes['mail'])[0] == $crawlerSettings->notification_email;
            }
        );
    }

    public function test_crawl_no_catalogs(): void
    {
        Notification::fake();

        $crawlerSettings = $this->crawlerSettings("catalogs-empty.html");
        $hourglass = new \App\Crawlers\Hourglass($crawlerSettings);

        $hourglass->settings()->brands = ['test-brand'];
        $hourglass->settings()->save();

        $hourglass->handle();

        $this->assertDatabaseCount('products', 0);

        Notification::assertSentTo(
            new AnonymousNotifiable(),
            CrawlNotice::class,
            function ($notification, $channels, $notifiable) use ($crawlerSettings) {
                return array_keys($notifiable->routes['mail'])[0] == $crawlerSettings->notification_email && $notification->message == 'No catalogs found.';
            }
        );
    }

    public function test_crawl_no_products(): void
    {
        Notification::fake();

        $crawlerSettings = $this->crawlerSettings("catalogs-no-products.html");
        $hourglass = new \App\Crawlers\Hourglass($crawlerSettings);

        $hourglass->settings()->brands = ['test-brand'];
        $hourglass->settings()->save();

        $hourglass->handle();

        $this->assertDatabaseCount('products', 0);

        Notification::assertSentTo(
            new AnonymousNotifiable(),
            CrawlNotice::class,
            function ($notification, $channels, $notifiable) use ($crawlerSettings) {
                return array_keys($notifiable->routes['mail'])[0] == $crawlerSettings->notification_email && $notification->message == 'No products found.';
            }
        );
    }

    protected function crawlerSettings(string $url): CrawlerSettings
    {
        $crawlerSettings = app(CrawlerSettings::class);
        $crawlerSettings->url = base_path("tests/stubs/hourglass/{$url}");
        $crawlerSettings->notification_email = 'admin@test.com';
        $crawlerSettings->save();

        return $crawlerSettings;
    }
}
