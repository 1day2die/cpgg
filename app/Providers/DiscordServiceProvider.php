<?php

namespace App\Providers;

use Illuminate\Support\Facades\Config;
use Illuminate\Support\ServiceProvider;
use App\Settings\DiscordSettings;

class DiscordServiceProvider extends ServiceProvider
{
    public function register()
    {
        // No need to register anything
    }

    public function boot()
    {
        // Retrieve Discord settings from the Spatie settings class
        $discordSettings = app(DiscordSettings::class);

        // Inject the settings into the config
        Config::set('services.discord.client_id', $discordSettings->client_id);
        Config::set('services.discord.client_secret', $discordSettings->client_secret);
        Config::set('services.discord.redirect', env('APP_URL', 'http://localhost') . '/auth/callback');

        // optional
        Config::set('services.discord.allow_gif_avatars', (bool)env('DISCORD_AVATAR_GIF', true));
        Config::set('services.discord.avatar_default_extension', env('DISCORD_EXTENSION_DEFAULT', 'jpg'));
    }
}
