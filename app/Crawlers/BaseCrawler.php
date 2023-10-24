<?php

namespace App\Crawlers;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldBeUnique;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use App\Settings\CrawlerSettings;
use Spatie\LaravelSettings\Settings;
use Filament\Forms\Form;

abstract class BaseCrawler implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public $timeout = 1800;
    protected CrawlerSettings $settings;

    abstract public function crawl(string $url): void;

    abstract public static function settings(): Settings;

    public static function form(Form $form): Form
    {
        return $form;
    }

    public function handle(): void
    {
        $this->settings = app(CrawlerSettings::class);

        $this->crawl($this->settings->url);
    }
}
