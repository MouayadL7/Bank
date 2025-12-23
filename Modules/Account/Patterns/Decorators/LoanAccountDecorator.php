<?php

namespace Modules\Account\Decorators;

use DomainException;
use Modules\Account\Events\AccountSettledEvent;

class LoanAccountDecorator extends AccountTypeDecorator
{
    public function deposit(float $amount): void
    {
        $account = $this->getModel();

        $account->getStateInstance()->deposit($account, 0);

        $newDebt = max(0, $account->balance - $amount);
        $account->balance = $newDebt;

        if ($newDebt === 0) {
            event(new AccountSettledEvent($account));
        }
    }

    public function withdraw(float $amount): void
    {
        $account = $this->getModel();

        $account->getStateInstance()->withdraw($account, 0);

        $limit = $account->meta['loan_limit'] ?? null;
        $newDebt = $account->balance + $amount;

        if ($limit && $newDebt > $limit) {
            throw new DomainException("Loan limit exceeded");
        }

        $account->balance = $newDebt;
    }
}

