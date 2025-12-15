<?php

namespace Modules\Account\Repositories\Eloquent;

use Illuminate\Pagination\LengthAwarePaginator;
use Modules\Account\Repositories\Interfaces\AccountRepositoryInterface;
use Modules\Account\Models\Account;

class AccountRepository implements AccountRepositoryInterface
{
    public function all(): LengthAwarePaginator
    {
        return Account::with(['parent', 'children'])->paginate();
    }

    public function findById(int $id): Account
    {
        return Account::findOrFail($id);
    }

    public function findByUuid(string $uuid): Account
    {
        return Account::where('uuid', $uuid)->firstOrFail();
    }

    public function save(Account $model): Account
    {
        $model->save();
        return $model;
    }

    public function create(array $data): Account
    {
        return Account::create($data);
    }
}
