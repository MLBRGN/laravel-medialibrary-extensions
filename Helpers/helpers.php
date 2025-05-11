<?php

if (! function_exists('mle_package_asset')) {
    function mle_package_asset($name): string
    {
        return route(config('media-library-extensions.route-prefix').'-package.assets', ['name' => $name]);
    }
}
