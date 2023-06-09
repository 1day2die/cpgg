<?php

use Spatie\LaravelSettings\Migrations\SettingsMigration;

return new class extends SettingsMigration
{
    public function up(): void
    {
        $this->migrator->add('bitpave.client_id', null);
        $this->migrator->add('bitpave.client_secret', null);
        $this->migrator->add('bitpave.wallet', null);
        $this->migrator->add('bitpave.enabled', false);
    }

    public function down(): void
    {
        $this->migrator->delete('bitpave.client_id');
        $this->migrator->delete('bitpave.client_secret');
        $this->migrator->delete('bitpave.wallet');
        $this->migrator->delete('bitpave.enabled');
    }
};
