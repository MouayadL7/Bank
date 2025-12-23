<?php

namespace Modules\Account\Decorators;

class CheckingAccountDecorator extends AccountTypeDecorator
{
    public function onWithdraw($account, float $amount): void
    {
        $fee = $account->meta['withdraw_fee'] ?? 0;
        $account->balance -= $fee;
    }
}
