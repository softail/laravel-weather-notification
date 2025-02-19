<?php

return [

    /*
    |--------------------------------------------------------------------------
    | Weather Services
    |--------------------------------------------------------------------------
    |
    | This file is for storing the credentials for weather API services such
    | as Open Meteo and Weather API.
    |
    */

    'open_meteo' => [
        'url' => env('OPEN_METEO_API_URL'),
    ],

    'weather_api' => [
        'url' => env('WEATHER_API_API_URL'),
        'key' => env('WEATHER_API_API_KEY'),
    ],
];
