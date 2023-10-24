<?php

use Spatie\LaravelSettings\Migrations\SettingsMigration;

return new class extends SettingsMigration
{
    public function up(): void
    {
        $this->migrator->add('crawler.strategy', null);
        $this->migrator->add('crawler.url', null);
        $this->migrator->add('crawler.notification_email', null);
    }
};
