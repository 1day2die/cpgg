<?php

namespace App\Extensions\PaymentGateways\Bitpave;

use Spatie\LaravelSettings\Settings;

class BitpaveSettings extends Settings
{
    public bool $enabled = false;
    public ?string $client_id;
    public ?string $client_secret;
    public ?string $wallet;

    public static function group(): string
    {
        return 'bitpave';
    }




    /**
     * Summary of optionInputData array
     * Only used for the settings page
     * @return array<array<'type'|'label'|'description'|'options', string|bool|float|int|array<string, string>>>
     */
    public static function getOptionInputData()
    {
        return [
            'category_icon' => 'fas fa-dollar-sign',
            'client_id' => [
                'type' => 'string',
                'label' => 'Client ID',
                'description' => 'The Client ID of your Bitpave Account',
            ],
            'client_secret' => [
                'type' => 'string',
                'label' => 'Client Secret',
                'description' => 'The Client Secret of your Bitpave Account',
            ],
            'enabled' => [
                'type' => 'boolean',
                'label' => 'Enabled',
                'description' => 'Enable this payment gateway',
            ],
            'wallet' => [
                'type' => 'string',
                'label' => 'Wallet ID',
                'description' => 'Your Crypto Wallet ID',
            ],

        ];
    }
}
