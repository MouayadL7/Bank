<?php

namespace Modules\Account\Decorators;

class InvestmentAccountDecorator extends AccountTypeDecorator
{
    public function calculateBalance($account): float
    {
        $roi = $account->meta['roi'] ?? 0;
        return $account->balance * (1 + $roi);
    }
}
