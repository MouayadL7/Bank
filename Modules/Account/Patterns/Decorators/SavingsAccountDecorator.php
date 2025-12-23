<?php

namespace Modules\Account\Decorators;

class SavingsAccountDecorator extends AccountTypeDecorator
{
    public function calculateBalance($account): float
    {
        $rate = $account->meta['interest_rate'] ?? 0;
        return $account->balance * (1 + $rate);
    }
}
