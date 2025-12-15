<?php

namespace Modules\Accounts\Domain\States;

use Modules\Account\Models\Account;

interface AccountStateInterface
{
    public function deposit(Account $account, float $amount): void;

    public function withdraw(Account $account, float $amount): void;

    public function transitionTo(Account $account, string $newState): void;

    public function name(): string;
}
