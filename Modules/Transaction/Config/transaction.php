<?php

return [
    /*
    |--------------------------------------------------------------------------
    | Transaction Module Configuration
    |--------------------------------------------------------------------------
    |
    | This file contains configuration options specific to the Transaction module.
    |
    */

    'default_status' => 'active',
    'items_per_page' => 15,
    'feature_enabled' => env('TRANSACTION_FEATURE_ENABLED', true),

    // Add more module-specific settings here
];
