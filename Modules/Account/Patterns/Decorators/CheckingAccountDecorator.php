<?php

namespace Modules\Account\Decorators;

use DomainException;

class CheckingAccountDecorator extends AccountTypeDecorator
{
    public function onWithdraw(float $amount): void
    {
        $fee = (float) ($this->getModel()->meta['withdraw_fee'] ?? 0);

        parent::withdraw($amount);

        if ($fee > 0) {
            parent::withdraw($fee);
        }
    }
}
