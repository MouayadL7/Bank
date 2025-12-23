<?php

namespace Modules\Recurring\Services;

use Carbon\Carbon;
use Illuminate\Support\Facades\Log;
use Modules\Recurring\Models\Recurring;

class RecurringService
{
    public function runDueRecurrings(): void
    {
        Recurring::query()
            ->where('active', true)
            ->where('next_run_at', '<=', now())
            ->each(function (Recurring $recurring) {
                $this->execute($recurring);
            });
    }

    private function execute(Recurring $recurring): void
    {
        try {
            $accountService = app(AccountService::class);

            match ($recurring->action) {
                'deposit' => $accountService->deposit(
                    $recurring->account_uuid,
                    $recurring->amount
                ),
                'withdraw' => $accountService->withdraw(
                    $recurring->account_uuid,
                    $recurring->amount
                ),
            };

            $recurring->update([
                'next_run_at' => $this->calculateNextRun($recurring),
            ]);

        } catch (\Throwable $e) {
            Log::error('Recurring rule failed', [
                'recurring_uuid' => $recurring->uuid,
                'error' => $e->getMessage(),
            ]);
        }
    }

    private function calculateNextRun(Recurring $recurring): Carbon
    {
        return match ($recurring->interval) {
            'monthly' => Carbon::parse($recurring->next_run_at)->addMonth(),
            'weekly'  => Carbon::parse($recurring->next_run_at)->addWeek(),
            default   => null,
        };
    }
}

