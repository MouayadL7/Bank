<?php

return [
    /*
    |--------------------------------------------------------------------------
    | AccessControl Module Configuration
    |--------------------------------------------------------------------------
    |
    | This file contains configuration options specific to the AccessControl module.
    |
    */

    'default_status' => 'active',
    'items_per_page' => 15,
    'feature_enabled' => env('ACCESS_CONTROL_FEATURE_ENABLED', true),

    // Add more module-specific settings here
];
