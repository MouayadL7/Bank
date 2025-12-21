<?php

namespace Modules\Transaction\Policies;

use Modules\User\Models\User;
use Illuminate\Auth\Access\HandlesAuthorization;
use Illuminate\Support\Facades\Gate;

class TransactionPolicy
{
    use HandlesAuthorization;

    /**
     * تحقق من صلاحية الإيداع في حساب معين
     *
     * @param User $user
     * @param string $accountUuid
     * @return bool
     */
    public function deposit(User $user, string $accountUuid)
    {
        if (Gate::allows('isCustomer')) {
            // تحقق من أن المستخدم يملك الحساب بناءً على UUID
            return $user->accounts()->where('uuid', $accountUuid)->exists();
        }

        return true;
    }

    /**
     * تحقق من صلاحية السحب من حساب معين
     *
     * @param User $user
     * @param string $accountUuid
     * @return bool
     */
    public function withdraw(User $user, string $accountUuid)
    {
        if (Gate::allows('isCustomer')) {
            // تحقق من أن المستخدم يملك الحساب بناءً على UUID
            return $user->accounts()->where('uuid', $accountUuid)->exists();
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
    public function transfer(User $user, string $fromUuid, string $toUuid)
    {
        if (Gate::allows('isCustomer')) {
            // تحقق من أن المستخدم يملك الحساب المرسل (fromUUID)
            return $user->accounts()->where('uuid', $fromUuid)->exists();
        }

        return true;
    }
}
