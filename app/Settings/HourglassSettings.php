<?php

namespace App\Settings;

use Spatie\LaravelSettings\Settings;

class HourglassSettings extends Settings
{
    public array $brands = [];

    public static function group(): string
    {
        return 'hourglass';
    }
}
