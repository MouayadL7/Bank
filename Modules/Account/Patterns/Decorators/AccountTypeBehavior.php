<?php

namespace Modules\Account\Decorators;

use Modules\Account\Models\Account;

interface AccountTypeBehavior
{
    public function calculateBalance(): float;

    public function deposit(float $amount): void;

    public function withdraw(float $amount): void;

    public function getModel(): Account;
}
