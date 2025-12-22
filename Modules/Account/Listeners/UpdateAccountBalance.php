<?php

// app/Modules/Account/Listeners/UpdateAccountBalance.php

namespace Modules\Account\Listeners;

use Modules\Account\Events\AccountBalanceUpdated;
use Modules\Account\Models\Account;
use DomainException;

class UpdateAccountBalance
{
    public function handle(AccountBalanceUpdated $event)
    {
        $account = Account::find($event->account->id);  

        if ($account) {
            if ($event->transactionType == 'deposit') {
                $account->balance += $event->amount;
            }

            elseif ($event->transactionType == 'withdraw') {
                if ($account->balance < $event->amount) {
                    throw new DomainException("Insufficient funds");
                }
                $account->balance -= $event->amount;
            }

            elseif ($event->transactionType == 'transfer') {

                $fromAccount = Account::find($event->fromAccountId);
                $toAccount = Account::find($event->toAccountId);

                if ($fromAccount && $toAccount) {
                    if ($fromAccount->balance < $event->amount) {
                        throw new DomainException("Insufficient funds in the sender's account");
                    }
                    $fromAccount->balance -= $event->amount;

                    $toAccount->balance += $event->amount;

                    $fromAccount->save();
                    $toAccount->save();
                } else {
                    throw new DomainException("One or both accounts not found.");
                }
            }

            $account->save();
        } else {
            throw new DomainException("Account not found.");
        }
    }
}


