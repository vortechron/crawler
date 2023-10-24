<?php

namespace App\Filament\Pages;

use Filament\Forms\Form;
use Filament\Pages\Page;
use Filament\Actions\Action;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Notifications\Notification;
use App\Settings\CrawlerSettings as ModelsCrawlerSettings;

class CrawlerSettings extends Page
{
    protected static ?string $navigationIcon = 'heroicon-o-document-text';

    protected static string $view = 'filament.pages.crawler-settings';

    protected static ?string $navigationGroup = 'Crawler';

    protected static ?int $navigationSort = 3;

    public static function getNavigationLabel(): string
    {
        return 'Settings';
    }

    public ?array $data = [];
    public ?array $strategyData = [];

    public function mount(): void
    {
        $crawlerSettings = app(ModelsCrawlerSettings::class);
        $this->getForm('defaultForm')->fill($crawlerSettings->toArray());

        $strat = $crawlerSettings->strategy;
        $this->getForm('strategyForm')->fill(empty($strat) ? [] : $strat::settings()->toArray());
    }

    protected function getForms(): array
    {
        return [
            'defaultForm',
            'strategyForm',
        ];
    }

    public function defaultForm(Form $form): Form
    {
        $strategies = config('crawler.strategies');

        return $form
            ->schema([
                Select::make('strategy')->options(
                    collect($strategies)->mapWithKeys(fn ($strategy) => [$strategy => substr($strategy, strrpos($strategy, '\\') + 1)])
                )->live()->helperText("The strategy to use when crawling. Contact developer to add more strategy like 'shopee'")->required(),
                TextInput::make('url')->autofocus()->required(),
                TextInput::make('notification_email')->required()->email()->helperText('Email to send notification of the crawler result'),
            ])->statePath('data');
    }

    public function strategyForm(Form $form): Form
    {
        $data = $this->data;

        $strat = $data['strategy'] ?? app(ModelsCrawlerSettings::class)->strategy ?? null;

        if (empty($strat)) return $form;

        return $strat::form($form)->statePath('strategyData');
    }

    public function submit(): void
    {
        $settings = app(ModelsCrawlerSettings::class);

        $settings->strategy = $this->data['strategy'];
        $settings->url = $this->data['url'];
        $settings->notification_email = $this->data['notification_email'];

        $settings->save();

        if ($settings->strategy) {
            $strat = $settings->strategy::settings();
            foreach ($this->strategyData as $key => $value) {
                $strat->$key = $value;
            }
            $strat->save();
        }

        Notification::make()
            ->title('Saved successfully')
            ->success()
            ->send();
    }

    protected function getActions(): array
    {
        return [
            Action::make('submit')->label('Save')->submit('form'),
        ];
    }
}
