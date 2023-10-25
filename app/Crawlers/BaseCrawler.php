<?php

namespace App\Crawlers;

use Throwable;
use Filament\Forms\Form;
use Illuminate\Bus\Queueable;
use App\Settings\CrawlerSettings;
use App\Notifications\CrawlNotice;
use App\Crawlers\Traits\Notifiable;
use Illuminate\Support\Facades\Log;
use Spatie\LaravelSettings\Settings;
use Illuminate\Queue\SerializesModels;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Support\Facades\Notification;
use Illuminate\Contracts\Queue\ShouldBeUnique;
use Illuminate\Notifications\AnonymousNotifiable;

abstract class BaseCrawler implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels, Notifiable;

    public $timeout = 1800;

    public function __construct(
        protected CrawlerSettings $settings
    ) {
    }

    abstract protected function crawl(string $url): void;

    abstract public static function settings(): Settings;

    public static function form(Form $form): Form
    {
        return $form;
    }

    public function handle(): void
    {
        $this->crawl($this->settings->url);
    }

    public function failed(Throwable $exception): void
    {
        $this->notifier()->notify(new CrawlNotice("Crawl job failed, " . $exception->getMessage()));

        Log::error($exception->getMessage());
    }
}
