<?php

namespace App\Crawlers;

use App\Components\DomParser;
use DOMXPath;
use Throwable;
use DOMDocument;
use App\Models\Product;
use Filament\Forms\Form;
use Illuminate\Bus\Batch;
use Illuminate\Support\Str;
use App\Jobs\HourglassProducts;
use App\Notifications\CrawlCompleted;
use App\Settings\HourglassSettings;
use Illuminate\Support\Facades\Bus;
use Illuminate\Support\Facades\Log;
use Spatie\LaravelSettings\Settings;
use Filament\Forms\Components\Select;
use Illuminate\Support\Facades\Notification;

class Hourglass extends BaseCrawler
{
    protected array $productUrls = [];
    protected array $products = [];

    public function crawl(string $url): void
    {
        $links = DomParser::load($url)->dom()->getElementsByTagName('a');

        $catalogUrls = [];

        foreach ($this->settings()->brands as $brand) {
            foreach ($links as $link) {
                $href = $link->getAttribute('href');

                // we only want to crawl the catalog pages
                if (Str::contains($href, 'catalog/' . $brand)) {
                    $catalogUrls[] = $href;
                }
            }
        }

        foreach (collect($catalogUrls)->unique()->all() as $catalogUrl) {
            $this->crawlCatalog($catalogUrl);
        }

        $this->scrapeProducts();

        return;
    }

    public function crawlCatalog(string $url): void
    {
        $parser = DomParser::load($url);

        $links = $parser->dom()->getElementsByTagName('a');

        foreach ($this->settings()->brands as $brand) {
            foreach ($links as $link) {
                $href = $link->getAttribute('href');

                // we only want to crawl the product pages
                if (Str::contains($href, 'product/' . $brand)) {
                    $this->productUrls[] = $href;
                }
            }
        }
    }

    public function scrapeProducts(): void
    {
        $chunks = collect($this->productUrls)->unique()
            ->map(function ($productUrl) {
                return str_replace('http://', 'https://', $productUrl);
            })
            ->chunk(20);

        $jobs = [];
        foreach ($chunks as $productUrls) {
            $jobs[] = new HourglassProducts($productUrls->all());
        }

        if (count($jobs) == 0) {
            return;
        }

        Bus::batch($jobs)->catch(function (Batch $batch, Throwable $e) {
            Log::error($e->getMessage());
        })->finally(function (Batch $batch) {
            Notification::route('mail', [
                $this->settings->notification_email => 'Hourglass',
            ])->notify(new CrawlCompleted());
        })->dispatch();
    }

    public static function settings(): Settings
    {
        return app(HourglassSettings::class);
    }

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Select::make('brands')
                    ->multiple()
                    ->options([
                        'patek-philippe' => 'Patek Philippe',
                        'rolex' => 'Rolex',
                    ])
            ]);
    }
}
