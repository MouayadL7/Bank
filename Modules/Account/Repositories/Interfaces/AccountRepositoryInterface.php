<?php

namespace Modules\Account\Repositories\Interfaces;

use Illuminate\Pagination\LengthAwarePaginator;
use Modules\Account\Models\Account;

interface AccountRepositoryInterface
{
    public function all(): LengthAwarePaginator;
    public function findById(int $id): Account;
    public function findByUuid(string $uuid): Account;
    public function save(Account $model): Account;
    public function load(Account $account): Account;
    public function create(array $data): Account;
}
