<?php

namespace Modules\Account\Services;

use Illuminate\Support\Facades\DB;
use Modules\Account\Actions\ChangeParentAccountAction;
use Modules\Account\Actions\CloseAccountAction;
use Modules\Account\Actions\UpdateAccountMetaAction;
use Modules\Account\DTOs\AccountData;
use Modules\Account\Events\AccountClosed;
use Modules\Account\Events\AccountStateChanged;
use Modules\Account\Repositories\Interfaces\AccountRepositoryInterface;
use Modules\Account\Http\Resources\AccountResource;
use Modules\User\Services\UserService;

class AccountService
{
    public function __construct(
        private AccountRepositoryInterface $repo,
        private CloseAccountAction $closeAccountAction,
        private UpdateAccountMetaAction $updateAccountMetaAction,
        private ChangeParentAccountAction $changeParentAccountAction,
        private UserService $userService,
    ) {}

    public function getAll()
    {
        $accounts = $this->repo->all();

        return AccountResource::collection($accounts);
    }

    public function getByUuid(string $uuid): AccountResource
    {
        $account = $this->repo->findByUuid($uuid, true);

        return new AccountResource($account);
    }

    public function createAccount(AccountData $dto): AccountResource
    {
        return DB::transaction(function () use ($dto) {
            $customer = $this->userService->create($dto->userData);

            $accountData = $dto->toArray();
            $accountData['customer_id'] = $customer->id;
            $account = $this->repo->create($accountData);

            return new AccountResource($account);
        });
    }

    public function changeState(string $uuid, string $newState): AccountResource
    {
        return DB::transaction(function() use ($uuid, $newState) {
            $account = $this->repo->findByUuid($uuid);

            $state = $account->getStateInstance();
            $state->transitionTo($account, $newState);

            $this->repo->save($account);
            $this->repo->load($account);

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
            $this->repo->load($account);

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
            $this->repo->load($account);

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
            $this->repo->load($account);

            return new AccountResource($account);
        });
    }
}
