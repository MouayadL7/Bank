<?php

namespace Modules\Account\Decorators;

use Modules\Account\Decorators\AccountTypeBehavior;
use Modules\Account\Models\Account;
use Modules\Account\Patterns\Composite\AccountComponent;

abstract class AccountTypeDecorator implements AccountTypeBehavior
{
    public function __construct(
        protected AccountComponent $inner
    ) {}

    public function calculateBalance(): float
    {
        return $this->inner->getBalance();
    }

    public function deposit(float $amount): void
    {
        $this->inner->deposit($amount);
    }

    public function withdraw(float $amount): void
    {
        $this->inner->withdraw($amount);
    }

    public function getModel(): Account
    {
        return $this->inner->getModel();;
    }
}
