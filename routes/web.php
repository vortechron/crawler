<?php

use App\Models\Product;
use Illuminate\Support\Str;
use App\Notifications\CrawlCompleted;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Notification;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "web" middleware group. Make something great!
|
*/

Route::get('/', function () {
    return view('welcome');
});

Route::get('/tinker', function () {
    $settings = app(\App\Settings\CrawlerSettings::class);
    Notification::route('mail', [
        $settings->notification_email => 'Hourglass',
    ])->notify(new CrawlCompleted());
});
