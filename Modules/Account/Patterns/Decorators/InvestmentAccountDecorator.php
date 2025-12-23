<?php

namespace Modules\Account\Decorators;

use Carbon\Carbon;
use DomainException;

class InvestmentAccountDecorator extends AccountTypeDecorator
{
    public function onWithdraw($account, float $amount): void
    {
        $lockUntil = $account->meta['lock_until'] ?? null;

        if ($lockUntil && Carbon::now()->lt(Carbon::parse($lockUntil))) {
            throw new DomainException('Funds are locked until ' . $lockUntil);
        }

        parent::withdraw($amount);

        $penaltyRate = (float) ($account->meta['early_withdraw_penalty'] ?? 0);

        if ($penaltyRate > 0) {
            $penalty = $amount * $penaltyRate;

            if ($account->balance < ($amount + $penalty)) {
                throw new DomainException('Insufficient balance for withdrawal + penalty');
            }

            parent::withdraw($penalty);
        }
    }
}
