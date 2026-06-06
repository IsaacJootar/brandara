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
