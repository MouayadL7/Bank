<?php

namespace Modules\Account\Services;

use Illuminate\Support\Facades\DB;
use Modules\Account\Actions\ChangeParentAccountAction;
use Modules\Account\Actions\CloseAccountAction;
use Modules\Account\Actions\DepositAction;
use Modules\Account\Actions\UpdateAccountMetaAction;
use Modules\Account\Actions\WithdrawAction;
use Modules\Account\DTOs\AccountData;
use Modules\Account\Events\AccountBalanceUpdated;
use Modules\Account\Events\AccountClosed;
use Modules\Account\Events\AccountStateChanged;
use Modules\Account\Repositories\Interfaces\AccountRepositoryInterface;
use Modules\Accounts\Http\Resources\AccountResource;

class AccountService
{
    public function __construct(
        private AccountRepositoryInterface $repo,
        private DepositAction $depositAction,
        private WithdrawAction $withdrawAction,
        private CloseAccountAction $closeAccountAction,
        private UpdateAccountMetaAction $updateAccountMetaAction,
        private ChangeParentAccountAction $changeParentAccountAction,
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
            event(new AccountBalanceUpdated(
                $account,
                $amount,
                'deposit'
            ));

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

            event(new AccountBalanceUpdated(
                $account,
                -$amount,
                'withdraw'
            ));

            return new AccountResource($updatedAccount);
        });
    }

    public function changeState(string $uuid, string $newState): AccountResource
    {
        return DB::transaction(function() use ($uuid, $newState) {
            $account = $this->repo->findByUuid($uuid);

            $state = $account->getStateInstance();
            $state->transitionTo($account, $newState);

            $this->repo->save($account);

            event(new AccountStateChanged($account, $newState));

            return new AccountResource($account);
        });
    }

    public function close(string $uuid): AccountResource
    {
        return DB::transaction(function () use ($uuid) {

            $account = $this->repo->findByUuid($uuid);

            $this->closeAccountAction->execute($account);

            $this->repo->save($account);

            event(new AccountClosed($account));

            return new AccountResource($account);
        });
    }

    public function updateMeta(string $uuid, array $meta): AccountResource
    {
        return DB::transaction(function () use ($uuid, $meta) {

            $account = $this->repo->findByUuid($uuid);

            $this->updateAccountMetaAction->execute($account, $meta);

            $this->repo->save($account);

            return new AccountResource($account);
        });
    }

    public function changeParent(string $uuid, ?string $parentUuid): AccountResource
    {
        return DB::transaction(function () use ($uuid, $parentUuid) {

            $account = $this->repo->findByUuid($uuid);

            $parent = $parentUuid
                ? $this->repo->findByUuid($parentUuid)
                : null;

            $this->changeParentAccountAction->execute($account, $parent);

            $this->repo->save($account);

            return new AccountResource($account);
        });
    }
}
