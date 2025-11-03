<?php

return [
    'name' => env('APP_NAME', 'Master Seller'),

    'description' => 'Powerful eCommerce system built to help businesses sell products online with ease. It offers seamless product management, secure payments, and efficient order tracking—all in one place. With its user-friendly design and smart features, Master Seller makes online selling faster, smarter, and more successful.',

    'app_logo_bare_path' => 'assets/images/master_seller_bare_primary.png',

    'app_logo_backgroud_path' => 'assets/images/master_seller_background_primary.png',

    'logo_link' => '/shop',

    'admin_logo_link' => '/shop',

    'cache_time' => 3600,

    'env' => env('APP_ENV', 'production'),

    'debug' => (bool) env('APP_DEBUG', false),

    'url' => env('APP_URL', 'http://localhost'),

    'timezone' => 'Asia/Bangkok',

    'locale' => env('APP_LOCALE', 'en'),

    'fallback_locale' => env('APP_FALLBACK_LOCALE', 'en'),

    'faker_locale' => env('APP_FAKER_LOCALE', 'en_US'),

    'cipher' => 'AES-256-CBC',

    'key' => env('APP_KEY'),

    'previous_keys' => [
        ...array_filter(
            explode(',', (string) env('APP_PREVIOUS_KEYS', ''))
        ),
    ],

    'maintenance' => [
        'driver' => env('APP_MAINTENANCE_DRIVER', 'file'),
        'store' => env('APP_MAINTENANCE_STORE', 'database'),
    ],

];
