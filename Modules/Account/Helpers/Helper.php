<?php

use Modules\Account\Models\Account;

if (! function_exists('generateAccountNumber')) {
    /**
     * Generate unique account number
     * Format: ACC-YYYYMMDD-XXXX
     */
    function generateAccountNumber(): string
    {
        $date = now()->format('Ymd');
        $prefix = 'ACC';

        // Get today's account count
        $count = Account::whereDate('created_at', today())->count();

        // Sequential number (padded to 4 digits)
        $sequence = str_pad($count + 1, 4, '0', STR_PAD_LEFT);

        return "{$prefix}-{$date}-{$sequence}";
    }
}
