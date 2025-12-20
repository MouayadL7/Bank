<?php

namespace Modules\Account\Factories;

use Modules\Account\Models\Account;
use Modules\Account\Patterns\Composite\AccountComponent;
use Modules\Account\Patterns\Composite\CompositeAccount;
use Modules\Account\Patterns\Composite\SingleAccount;

class AccountComponentFactory
{
    public static function make(Account $account): AccountComponent
    {
        if (!$account->isComposite()) {
            return new SingleAccount($account);
        }

        $composite = new CompositeAccount();

        foreach ($account->children as $child) {
            $composite->add(new SingleAccount($child));
        }

        return $composite;
    }
}
