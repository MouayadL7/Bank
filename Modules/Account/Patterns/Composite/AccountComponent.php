<?php

namespace Modules\Account\Patterns\Composite;

interface AccountComponent
{
    public function getBalance(): float;

    public function deposit(float $amount): void;

    public function withdraw(float $amount): void;
}
