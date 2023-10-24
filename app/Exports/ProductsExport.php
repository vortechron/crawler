<?php

namespace App\Exports;

use App\Models\Product;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\FromCollection;

class ProductsExport implements FromCollection, WithMapping, WithHeadings
{
    public $counter = 0;

    /**
     * @return \Illuminate\Support\Collection
     */
    public function collection()
    {
        return Product::all();
    }

    public function headings(): array
    {
        return [
            '#',
            'Name',
            'Price',
            'Stock',
            'Description',
            'Meta',
        ];
    }

    public function map($product): array
    {
        $metaInline = '';
        foreach ($product->meta as $key => $value) {
            $metaInline .= $key . ': ' . $value . PHP_EOL;
        }

        return [
            ++$this->counter,
            $product->name,
            $product->price,
            $product->stock,
            strip_tags($product->description),
            $metaInline,
        ];
    }
}
