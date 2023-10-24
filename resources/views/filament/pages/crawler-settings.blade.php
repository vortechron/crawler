<form wire:submit="submit">
    <x-filament-panels::page>
        {{ $this->defaultForm }}
        {{ $this->strategyForm }}
    </x-filament-panels::page>
</form>
