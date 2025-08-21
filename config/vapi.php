<?php

return [
    /*
    |--------------------------------------------------------------------------
    | Vapi API Configuration
    |--------------------------------------------------------------------------
    |
    | This file contains the configuration for the Vapi API integration.
    |
    */

    'api_key' => env('VAPI_API_KEY', ''),
    'api_secret' => env('VAPI_API_SECRET', ''),
    'webhook_secret' => env('VAPI_WEBHOOK_SECRET', ''),
    'base_url' => env('VAPI_BASE_URL', 'https://api.vapi.ai'),
    'version' => env('VAPI_API_VERSION', 'v1'),
];
