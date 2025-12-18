<?php

return [
    /*
    |--------------------------------------------------------------------------
    | Notification Module Configuration
    |--------------------------------------------------------------------------
    |
    | This file contains configuration options specific to the Notification module.
    |
    */

    'default_status' => 'active',
    'items_per_page' => 15,
    'feature_enabled' => env('NOTIFICATION_FEATURE_ENABLED', true),

    // Add more module-specific settings here
];
