<?php

use App\Models\Brand;

if (! function_exists('currentBrand')) {
    /**
     * Get the brand resolved from the current request URL.
     * Only available inside routes protected by the ResolveBrand middleware.
     */
    function currentBrand(): Brand
    {
        return app('current.brand');
    }
}

if (! function_exists('countryCurrencyExample')) {
    /**
     * Return a currency symbol and revenue example for a given country code.
     * Used in UI placeholders to feel local and relevant to the user.
     *
     * @return array{symbol: string, example: string, revenue_range: string}
     */
    function countryCurrencyExample(string $countryCode): array
    {
        $map = [
            'NG' => ['symbol' => '₦', 'example' => '₦50M–₦500M', 'revenue_range' => '₦50M–₦500M'],
            'GH' => ['symbol' => 'GH₵', 'example' => 'GH₵500K–GH₵5M', 'revenue_range' => 'GH₵500K–GH₵5M'],
            'KE' => ['symbol' => 'KSh', 'example' => 'KSh5M–KSh50M', 'revenue_range' => 'KSh5M–KSh50M'],
            'ZA' => ['symbol' => 'R', 'example' => 'R2M–R20M', 'revenue_range' => 'R2M–R20M'],
            'RW' => ['symbol' => 'RWF', 'example' => 'RWF50M–RWF500M', 'revenue_range' => 'RWF50M–RWF500M'],
            'TZ' => ['symbol' => 'TSh', 'example' => 'TSh50M–TSh500M', 'revenue_range' => 'TSh50M–TSh500M'],
            'UG' => ['symbol' => 'USh', 'example' => 'USh50M–USh500M', 'revenue_range' => 'USh50M–USh500M'],
            'ET' => ['symbol' => 'Br', 'example' => 'Br500K–Br5M', 'revenue_range' => 'Br500K–Br5M'],
            'SN' => ['symbol' => 'FCFA', 'example' => 'FCFA5M–FCFA50M', 'revenue_range' => 'FCFA5M–FCFA50M'],
            'CI' => ['symbol' => 'FCFA', 'example' => 'FCFA5M–FCFA50M', 'revenue_range' => 'FCFA5M–FCFA50M'],
            'CM' => ['symbol' => 'FCFA', 'example' => 'FCFA5M–FCFA50M', 'revenue_range' => 'FCFA5M–FCFA50M'],
        ];

        return $map[strtoupper($countryCode)] ?? ['symbol' => '$', 'example' => '$50K–$500K', 'revenue_range' => '$50K–$500K'];
    }
}
