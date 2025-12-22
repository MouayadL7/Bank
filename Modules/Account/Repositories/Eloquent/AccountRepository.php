<?php

namespace Modules\Account\Repositories\Eloquent;

use Illuminate\Pagination\LengthAwarePaginator;
use Modules\Account\Repositories\Interfaces\AccountRepositoryInterface;
use Modules\Account\Models\Account;

class AccountRepository implements AccountRepositoryInterface
{
    public function all(): LengthAwarePaginator
    {
        return Account::with(['customer', 'parent', 'children'])->paginate();
    }

    public function findById(int $id): Account
    {
        return Account::findOrFail($id);
    }

    public function findByUuid(string $uuid, bool $load = false): Account
    {
        return Account::where('uuid', $uuid)
            ->when($load, fn($q) => $q->with(['customer', 'parent', 'children']))
            ->firstOrFail();
    }

    public function findByCustomerId(int $customerId): LengthAwarePaginator
    {
        return Account::where('customer_id', $customerId)
            ->with(['customer', 'parent', 'children'])
            ->paginate();
    }

    public function save(Account $model): Account
    {
        $model->save();
        return $model;
    }

    public function load(Account $account): Account
    {
        return $account->load(['customer', 'parent', 'children']);
    }

    public function create(array $data): Account
    {
        $account = Account::create($data);

        return $account->load('customer');
    }
}
