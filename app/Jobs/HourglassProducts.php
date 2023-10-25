<?php

namespace App\Jobs;

use App\Models\Product;
use Illuminate\Support\Str;
use App\Components\DomParser;
use Illuminate\Bus\Batchable;
use Illuminate\Bus\Queueable;
use Illuminate\Support\Facades\Log;
use Illuminate\Queue\SerializesModels;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Contracts\Queue\ShouldBeUnique;

class HourglassProducts implements ShouldQueue, ShouldBeUnique
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels, Batchable;

    public $timeout = 1800; // 30 minutes

    public function __construct(
        public array $productUrls = []
    ) {
        //
    }

    public function handle(): void
    {
        $products = [];
        foreach ($this->productUrls as $productUrl) {
            try {
                $parser = DomParser::load($productUrl);
                $dom = $parser->dom();

                $descriptionNodes = $parser->query('//div[contains(@class, "spec-short-desc")]');
                $description = null;
                foreach ($descriptionNodes as $descElement) {
                    foreach ($descElement->childNodes as $node) {
                        $description .= $dom->saveHTML($node);
                    }
                }

                $data = [
                    'name' => Str::of($dom->getElementById("watch_name")?->textContent)->trim()->toString(),
                    'sku' => Str::of($parser->query('//*[@data-class="prd-name"]')?->item(0)?->textContent)
                        ->trim()->toString(),
                    'description' => Str::of($description)->trim()->toString(),
                    'price' => 0, // price is loaded thru js, requires headless browser
                    'stock' => 1,
                ];

                $meta = [];
                $specsList = $parser->query('//ul[@class="specs"]/li');

                foreach ($specsList as $spec) {
                    $label = Str::of(trim($parser->query('./div[@class="specs-lbl"]', $spec)->item(0)?->textContent))
                        ->replace(":", "")->lower()->toString();
                    $value = trim($parser->query('./div[@class="specs-val"]/p', $spec)->item(0)?->textContent);

                    $meta[$label] = $value;
                }

                $meta['brand'] = $dom->getElementById("watch_brand")?->textContent;
                $data['meta'] = json_encode($meta, JSON_THROW_ON_ERROR);

                $products[] = $data;
            } catch (\Throwable $th) {
                Log::debug($th->getMessage());
            }
        }

        if (count($products) > 0) Product::insert($products);
    }
}
