<?php

// Di project asli helper ini memilih aset per brand; di katalog cukup asset().
if (! function_exists('brand_asset')) {
    function brand_asset(string $path): string
    {
        return asset($path);
    }
}
