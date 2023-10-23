<?php

namespace App\Filament\Pages;

use Filament\Forms\Components\Component;

class Login extends \Filament\Pages\Auth\Login
{
    protected function getEmailFormComponent(): Component
    {
        return parent::getEmailFormComponent()
            ->default($this->getDemoCredentials("email"));
    }

    protected function getPasswordFormComponent(): Component
    {
        return parent::getPasswordFormComponent()
            ->default($this->getDemoCredentials("password"));
    }

    protected function getDemoCredentials($field)
    {
        return app()->environment('production') ?
            "" : (config("app.demo.$field") ?? "");
    }
}
