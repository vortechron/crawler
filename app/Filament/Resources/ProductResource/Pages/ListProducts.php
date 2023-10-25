<?php

namespace App\Filament\Resources\ProductResource\Pages;

use Filament\Actions;
use App\Exports\ProductsExport;
use App\Settings\CrawlerSettings;
use Maatwebsite\Excel\Facades\Excel;
use Filament\Notifications\Notification;
use Filament\Resources\Pages\ListRecords;
use Filament\Notifications\Actions\Action;
use App\Filament\Resources\ProductResource;
use App\Filament\Pages\CrawlerSettings as CrawlerSettingsPage;

class ListProducts extends ListRecords
{
    protected static string $resource = ProductResource::class;

    public static ?string $title = 'Crawled Products';

    protected function getHeaderActions(): array
    {
        return [
            $this->runActionButton(),
            Actions\Action::make('reset')
                ->color('danger')
                ->label('Reset')
                ->icon('heroicon-o-trash')
                ->action(function () {
                    ProductResource::getModel()::truncate();

                    Notification::make()
                        ->title('Reset successfully.')
                        ->success()
                        ->send();
                })
                ->requiresConfirmation(),
            Actions\Action::make('export')
                ->color('info')
                ->label('Export CSV file')
                ->icon('heroicon-s-arrow-right-on-rectangle')
                ->action(function () {
                    return Excel::download(new ProductsExport, 'products.csv');
                })
                ->requiresConfirmation(),
        ];
    }

    protected function runActionButton()
    {
        return Actions\Action::make('run')
            ->label('Run Crawler')
            ->icon('heroicon-o-play')
            ->action(function () {
                $crawlerSettings = app(CrawlerSettings::class);
                $strategyClass = $crawlerSettings->strategy;

                if (empty($strategyClass)) {
                    return Notification::make()
                        ->title('Please select a strategy first.')
                        ->warning()
                        ->actions([
                            Action::make('go to settings')
                                ->button()
                                ->url(CrawlerSettingsPage::getUrl())
                        ])
                        ->send();
                }

                dispatch(app($strategyClass));

                Notification::make()
                    ->title('Run successfully, you will reminded when its done.')
                    ->success()
                    ->send();
            })
            ->requiresConfirmation();
    }
}
