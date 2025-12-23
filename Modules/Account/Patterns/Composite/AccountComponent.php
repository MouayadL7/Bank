<?php

namespace Modules\Account\Patterns\Composite;

use Modules\Account\Models\Account;

interface AccountComponent
{
    public function getModel();

    public function getBalance(): float;

    public function deposit(float $amount): void;

    public function withdraw(float $amount): void;
}
