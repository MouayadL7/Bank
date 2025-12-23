<?php

return [
    /*
    |--------------------------------------------------------------------------
    | Recurring Module Configuration
    |--------------------------------------------------------------------------
    |
    | This file contains configuration options specific to the Recurring module.
    |
    */

    'default_status' => 'active',
    'items_per_page' => 15,
    'feature_enabled' => env('RECURRING_FEATURE_ENABLED', true),

    // Add more module-specific settings here
];
