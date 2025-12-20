<?php

return [
    /*
    |--------------------------------------------------------------------------
    | Auth Module Configuration
    |--------------------------------------------------------------------------
    |
    | This file contains configuration options specific to the Auth module.
    |
    */

    'default_status' => 'active',
    'items_per_page' => 15,
    'feature_enabled' => env('AUTH_FEATURE_ENABLED', true),

    // Add more module-specific settings here
];
