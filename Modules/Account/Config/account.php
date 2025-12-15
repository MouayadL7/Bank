<?php

return [
    /*
    |--------------------------------------------------------------------------
    | Account Module Configuration
    |--------------------------------------------------------------------------
    |
    | This file contains configuration options specific to the Account module.
    |
    */

    'default_status' => 'active',
    'items_per_page' => 15,
    'feature_enabled' => env('ACCOUNT_FEATURE_ENABLED', true),

    // Add more module-specific settings here
];
