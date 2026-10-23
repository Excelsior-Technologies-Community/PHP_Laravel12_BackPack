<?php

return [
    // when dev mode is enabled basset will replace the changed assets by either checking if the url changed
    // or comparing the file hash with the one in the asset map
    'dev_mode' => env('BASSET_DEV_MODE', env('APP_ENV') === 'local'),

    // verify ssl certificate while fetching assets
    'verify_ssl_certificate' => env('BASSET_VERIFY_SSL_CERTIFICATE', true),

    // disk and path where to store bassets
    'disk' => env('BASSET_DISK', 'basset'),

    // the path where assets will be stored inside the `public` folder
    // if you change this, you should also add it to your .gitignore file
    'path' => env('BASSET_PATH', 'basset'),

    // use cache map file (.basset).
    'cache_map' => env('BASSET_CACHE_MAP', true),

    // view paths that may use @basset
    // used to internalize assets in advance with artisan basset:internalize
    'view_paths' => [
        resource_path('views'),
    ],

    'asset_overrides' => null,

    // content security policy nonce
    'nonce' => null,

    // use relative path - disable when running under subfolder in Apache/XAMPP
    'relative_paths' => env('BASSET_RELATIVE_PATHS', false),
];
