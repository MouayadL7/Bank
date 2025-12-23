<?php

namespace Modules\Account\Services;

use Modules\Recurring\Models\Recurring;
use Illuminate\Support\Str;
use Carbon\Carbon;
use Modules\Account\Models\Account;

class LoanRecurringSetupService
{
    public function createMonthlyPayment(Account $loanAccount): void
    {
        $meta = $loanAccount->meta;

        $monthlyAmount = $this->calculateMonthlyPayment(
            $loanAccount->balance,
            $meta['interest_rate'],
            $meta['duration_months']
        );

        Recurring::create([
            'uuid' => Str::uuid(),
            'action' => 'deposit', // deposit = repay
            'amount' => $monthlyAmount,
            'account_uuid' => $loanAccount->uuid,
            'interval' => 'monthly',
            'next_run_at' => Carbon::now()->addMonth(),
            'meta' => [
                'type' => 'loan_repayment',
            ],
        ]);
    }

    private function calculateMonthlyPayment(
        float $principal,
        float $annualRate,
        int $months
    ): float {
        if ($annualRate == 0) {
            return round($principal / $months, 2);
        }

        $monthlyRate = $annualRate / 12;

        return round(
            ($principal * $monthlyRate) /
            (1 - pow(1 + $monthlyRate, -$months)),
            2
        );
    }
}
