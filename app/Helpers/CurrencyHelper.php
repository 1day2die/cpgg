<?php

namespace App\Helpers;

use NumberFormatter;

class CurrencyHelper
{
    private function convertForDisplay($amount)
    {
        return $amount / 1000;
    }

    public function formatForDisplay($amount, $decimals = 2, $locale = null, $ignoreOverride = false)
    {
        $locale = $locale ?: str_replace('_', '-', app()->getLocale());

        if (!$ignoreOverride) {
            $override = resolve(\App\Settings\GeneralSettings::class)->currency_format_override ?? null;
            if ($override) {
                $locale = $override;
            }
        }

        $display = $this->convertForDisplay($amount);

        if ($locale === 'bg' && $display <= 9999) {
            return number_format($display, $decimals, ',', '');
        }

        if ($locale === 'es' && $display < 10000) {
            return number_format($display, $decimals, ',', '');
        }

        if ($locale === 'pl' && $display <= 9999) {
            return number_format($display, $decimals, ',', '');
        }

        $formatter = new NumberFormatter($locale, NumberFormatter::DECIMAL);
        $formatter->setAttribute(NumberFormatter::MIN_FRACTION_DIGITS, $decimals);
        $formatter->setAttribute(NumberFormatter::MAX_FRACTION_DIGITS, $decimals);
        return $formatter->format($display);
    }

    public function formatForForm($amount, $decimals = 2)
    {
        return number_format($this->convertForDisplay($amount), $decimals, '.', '');
    }

    public function prepareForDatabase($amount)
    {
        return (int)($amount * 1000);
    }

    public function formatToCurrency(int $amount, $currency_code, $locale = null,)
    {
        $locale = $locale ?: str_replace('_', '-', app()->getLocale());

        // overriding users locale with global override
        $override = resolve(\App\Settings\GeneralSettings::class)->currency_format_override ?? null;
        if ($override) {
            $locale = $override;
        }

        $formatter = new NumberFormatter($locale, NumberFormatter::CURRENCY);

        return $formatter->formatCurrency($this->convertForDisplay($amount), $currency_code);
    }
}
