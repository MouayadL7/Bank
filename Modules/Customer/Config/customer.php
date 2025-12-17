<?php

return [
    /*
    |--------------------------------------------------------------------------
    | Customer Module Configuration
    |--------------------------------------------------------------------------
    |
    | This file contains configuration options specific to the Customer module.
    |
    */

    'default_status' => 'active',
    'items_per_page' => 15,
    'feature_enabled' => env('CUSTOMER_FEATURE_ENABLED', true),

    // Add more module-specific settings here
];
