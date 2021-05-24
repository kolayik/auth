<?php

return [
    /**
     * database or cache
     */
    'driver' => env('KOLAY_AUTH_DRIVER', 'cache'),
    /**
     * Seconds
     */
    'ttl' => env('KOLAY_AUTH_TTL', 60 * 1440),
    'refreshTtl' => env('KOLAY_AUTH_REFRESH_TTL', 60 * 20160),
    /**
     * Do not change
     */
    'providers' => [
        'cache' => KolayIK\Auth\Providers\Storage\Illuminate::class,
    ],

    'drivers' => [
        'cache' => KolayIK\Auth\Drivers\Cache::class,
        'database' => KolayIK\Auth\Drivers\Database::class,
    ],

    'logger_status' => env('KOLAY_AUTH_DEBUG_STATUS', false),
];
