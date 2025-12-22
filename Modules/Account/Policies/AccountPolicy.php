<?php

namespace Modules\Account\Policies;

use Modules\User\Models\User;
use Illuminate\Auth\Access\HandlesAuthorization;
use Illuminate\Support\Facades\Gate;
use Modules\Account\Models\Account;

class AccountPolicy
{
    use HandlesAuthorization;

    /**
     * تحقق من صلاحية الإيداع في حساب معين
     *
     * @param User $user
     * @param Account $account
     * @return bool
     */
    public function deposit(User $user, Account $account)
    {
        if (Gate::allows('isCustomer')) {
            return $user->id == $account->customer_id;
        }

        return true;
    }

    /**
     * تحقق من صلاحية السحب من حساب معين
     *
     * @param User $user
     * @param Account $account
     * @return bool
     */
    public function withdraw(User $user, Account $account)
    {
        if (Gate::allows('isCustomer')) {
            return $user->id == $account->customer_id;
        }

        return true;
    }

    /**
     * تحقق من صلاحية التحويل بين الحسابات
     *
     * @param User $user
     * @param string $fromUuid
     * @param string $toUuid
     * @return bool
     */
    public function transfer(User $user, Account $fromAccount)
    {
        if (Gate::allows('isCustomer')) {
            return $user->id == $fromAccount->customer_id;
        }

        return true;
    }
}
