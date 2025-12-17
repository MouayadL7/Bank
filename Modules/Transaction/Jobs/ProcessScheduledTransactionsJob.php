<?php

namespace App\Modules\Transaction\Jobs;

use App\Modules\Transaction\Services\TransactionService;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;

class ProcessScheduledTransactionsJob implements ShouldQueue
{
    use InteractsWithQueue, Queueable, SerializesModels;

    public function handle(TransactionService $service): void
    {
        $service->processScheduledTransactions();
    }
}
