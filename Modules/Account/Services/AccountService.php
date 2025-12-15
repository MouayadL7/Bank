<?php

namespace Modules\Account\Services;

use Illuminate\Support\Facades\DB;
use Modules\Account\Actions\DepositAction;
use Modules\Account\Actions\WithdrawAction;
use Modules\Account\DTOs\AccountData;
use Modules\Account\Repositories\Interfaces\AccountRepositoryInterface;
use Modules\Accounts\Http\Resources\AccountResource;

class AccountService
{
    public function __construct(
        private AccountRepositoryInterface $repo,
        private DepositAction $depositAction,
        private WithdrawAction $withdrawAction,
    ) {}

    public function getAll()
    {
        $accounts = $this->repo->all();

        return AccountResource::collection($accounts);
    }

    public function getByUuid(string $uuid): AccountResource
    {
        $account = $this->repo->findByUuid($uuid);

        return new AccountResource($account);
    }

    public function createAccount(AccountData $dto): AccountResource
    {
        $model = $this->repo->create($dto->toArray());

        return new AccountResource($model);
    }

    public function deposit(string $uuid, float $amount, ?int $byUserId = null): AccountResource
    {
        return DB::transaction(function() use ($uuid, $amount, $byUserId) {
            $account = $this->repo->findByUuid($uuid);

            // apply domain rules
            $updatedAccount = $this->depositAction->execute($account, $amount);

            // persist changes
            $this->repo->save($updatedAccount);

            // events are not responsibility of this method
            // event(new AccountBalanceChanged(...));

            return new AccountResource($updatedAccount);
        });
    }

    public function withdraw(string $uuid, float $amount, ?int $byUserId = null): AccountResource
    {
        return DB::transaction(function() use ($uuid, $amount, $byUserId) {
            $account = $this->repo->findByUuid($uuid);

            // apply domain rules
            $updatedAccount = $this->withdrawAction->execute($account, $amount);

            // persist
            $this->repo->save($updatedAccount);

            // event(new AccountBalanceChanged(...));

            return new AccountResource($updatedAccount);
        });
    }
}
