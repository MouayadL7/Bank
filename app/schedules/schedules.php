<?php

use App\Modules\Transaction\Jobs\ProcessScheduledTransactionsJob;
use Illuminate\Support\Facades\Log;

return function (\Illuminate\Console\Scheduling\Schedule $schedule) {
    $schedule->job(new ProcessScheduledTransactionsJob())
            ->everyMinute()
            ->onSuccess(function () {
                Log::info('Scheduled transactions processed successfully.');
            })
            ->onFailure(function () {
                Log::error('Failed to process scheduled transactions.');
            });
};
