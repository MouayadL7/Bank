<?php

namespace Tests\Unit\Account;

use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Event;
use Mockery;
use Modules\Account\Actions\ChangeParentAccountAction;
use Modules\Account\Actions\CloseAccountAction;
use Modules\Account\Actions\UpdateAccountMetaAction;
use Modules\Account\DTOs\AccountData;
use Modules\Account\Events\AccountClosed;
use Modules\Account\Events\AccountStateChanged;
use Modules\Account\Http\Resources\AccountResource;
use Modules\Account\Models\Account;
use Modules\Account\Repositories\Interfaces\AccountRepositoryInterface;
use Modules\Account\Services\AccountService;
use Modules\User\Models\User;
use Modules\User\Services\UserService;
use Tests\TestCase;

class AccountServiceTest extends TestCase
{

    protected function tearDown(): void
    {
        Mockery::close();
        parent::tearDown();
    }

    public function test_get_all_returns_account_resource_collection(): void
    {
        // Arrange
        $paginator = new LengthAwarePaginator([], 0, 15);
        
        $repo = Mockery::mock(AccountRepositoryInterface::class);
        $repo->shouldReceive('all')
            ->once()
            ->andReturn($paginator);

        $service = new AccountService(
            $repo,
            Mockery::mock(CloseAccountAction::class),
            Mockery::mock(UpdateAccountMetaAction::class),
            Mockery::mock(ChangeParentAccountAction::class),
            Mockery::mock(UserService::class)
        );

        // Act
        $result = $service->getAll();

        // Assert
        $this->assertInstanceOf(\Illuminate\Http\Resources\Json\AnonymousResourceCollection::class, $result);
    }

    public function test_get_by_uuid_returns_account_resource(): void
    {
        // Arrange
        $uuid = 'test-uuid';
        $account = new Account();
        $account->uuid = $uuid;

        $repo = Mockery::mock(AccountRepositoryInterface::class);
        $repo->shouldReceive('findByUuid')
            ->once()
            ->with($uuid, true)
            ->andReturn($account);

        $service = new AccountService(
            $repo,
            Mockery::mock(CloseAccountAction::class),
            Mockery::mock(UpdateAccountMetaAction::class),
            Mockery::mock(ChangeParentAccountAction::class),
            Mockery::mock(UserService::class)
        );

        // Act
        $result = $service->getByUuid($uuid);

        // Assert
        $this->assertInstanceOf(AccountResource::class, $result);
    }

    public function test_get_my_accounts_returns_customer_accounts(): void
    {
        // Arrange
        $userId = 1;
        $paginator = new LengthAwarePaginator([], 0, 15);

        Auth::shouldReceive('id')
            ->once()
            ->andReturn($userId);

        $repo = Mockery::mock(AccountRepositoryInterface::class);
        $repo->shouldReceive('findByCustomerId')
            ->once()
            ->with($userId)
            ->andReturn($paginator);

        $service = new AccountService(
            $repo,
            Mockery::mock(CloseAccountAction::class),
            Mockery::mock(UpdateAccountMetaAction::class),
            Mockery::mock(ChangeParentAccountAction::class),
            Mockery::mock(UserService::class)
        );

        // Act
        $result = $service->getMyAccounts();

        // Assert
        $this->assertInstanceOf(\Illuminate\Http\Resources\Json\AnonymousResourceCollection::class, $result);
    }

    public function test_create_account_creates_user_and_account(): void
    {
        // Arrange
        $user = new User();
        $user->id = 1;
        $user->email = 'test@example.com';

        $account = new Account();
        $account->id = 1;
        $account->customer_id = 1;

        $userData = Mockery::mock(\Modules\User\DTOs\UserData::class);
        
        $dto = Mockery::mock(AccountData::class);
        $dto->userData = $userData;
        $dto->shouldReceive('toArray')
            ->andReturn(['name' => 'Test Account', 'type' => 'savings']);

        $userService = Mockery::mock(UserService::class);
        $userService->shouldReceive('create')
            ->once()
            ->andReturn($user);

        $repo = Mockery::mock(AccountRepositoryInterface::class);
        $repo->shouldReceive('create')
            ->once()
            ->andReturn($account);

        DB::shouldReceive('transaction')
            ->once()
            ->andReturnUsing(function ($callback) {
                return $callback();
            });

        $service = new AccountService(
            $repo,
            Mockery::mock(CloseAccountAction::class),
            Mockery::mock(UpdateAccountMetaAction::class),
            Mockery::mock(ChangeParentAccountAction::class),
            $userService
        );

        // Act
        $result = $service->createAccount($dto);

        // Assert
        $this->assertInstanceOf(AccountResource::class, $result);
    }

    public function test_change_state_transitions_account_state(): void
    {
        // Arrange
        $uuid = 'test-uuid';
        $newState = 'suspended';
        $account = Mockery::mock(Account::class)->makePartial();
        $account->shouldAllowMockingProtectedMethods();
        $account->uuid = $uuid;

        $stateInstance = Mockery::mock(\Modules\Account\Patterns\States\AccountStateInterface::class);
        $stateInstance->shouldReceive('transitionTo')
            ->once()
            ->with($account, $newState);

        $account->shouldReceive('getStateInstance')
            ->andReturn($stateInstance);

        $repo = Mockery::mock(AccountRepositoryInterface::class);
        $repo->shouldReceive('findByUuid')
            ->once()
            ->with($uuid)
            ->andReturn($account);
        $repo->shouldReceive('save')
            ->once()
            ->with($account)
            ->andReturn($account);
        $repo->shouldReceive('load')
            ->once()
            ->with($account)
            ->andReturn($account);

        Event::fake();

        DB::shouldReceive('transaction')
            ->once()
            ->andReturnUsing(function ($callback) {
                return $callback();
            });

        $service = new AccountService(
            $repo,
            Mockery::mock(CloseAccountAction::class),
            Mockery::mock(UpdateAccountMetaAction::class),
            Mockery::mock(ChangeParentAccountAction::class),
            Mockery::mock(UserService::class)
        );

        // Act
        $result = $service->changeState($uuid, $newState);

        // Assert
        $this->assertInstanceOf(AccountResource::class, $result);
        Event::assertDispatched(AccountStateChanged::class);
    }

    public function test_close_closes_account(): void
    {
        // Arrange
        $uuid = 'test-uuid';
        $account = new Account();
        $account->uuid = $uuid;

        $closeAction = Mockery::mock(CloseAccountAction::class);
        $closeAction->shouldReceive('execute')
            ->once()
            ->with($account);

        $repo = Mockery::mock(AccountRepositoryInterface::class);
        $repo->shouldReceive('findByUuid')
            ->once()
            ->with($uuid)
            ->andReturn($account);
        $repo->shouldReceive('save')
            ->once()
            ->with($account)
            ->andReturn($account);
        $repo->shouldReceive('load')
            ->once()
            ->with($account)
            ->andReturn($account);

        Event::fake();

        DB::shouldReceive('transaction')
            ->once()
            ->andReturnUsing(function ($callback) {
                return $callback();
            });

        $service = new AccountService(
            $repo,
            $closeAction,
            Mockery::mock(UpdateAccountMetaAction::class),
            Mockery::mock(ChangeParentAccountAction::class),
            Mockery::mock(UserService::class)
        );

        // Act
        $result = $service->close($uuid);

        // Assert
        $this->assertInstanceOf(AccountResource::class, $result);
        Event::assertDispatched(AccountClosed::class);
    }

    public function test_update_meta_updates_account_metadata(): void
    {
        // Arrange
        $uuid = 'test-uuid';
        $meta = ['key' => 'value'];
        $account = new Account();
        $account->uuid = $uuid;

        $updateAction = Mockery::mock(UpdateAccountMetaAction::class);
        $updateAction->shouldReceive('execute')
            ->once()
            ->with($account, $meta);

        $repo = Mockery::mock(AccountRepositoryInterface::class);
        $repo->shouldReceive('findByUuid')
            ->once()
            ->with($uuid)
            ->andReturn($account);
        $repo->shouldReceive('save')
            ->once()
            ->with($account)
            ->andReturn($account);
        $repo->shouldReceive('load')
            ->once()
            ->with($account)
            ->andReturn($account);

        DB::shouldReceive('transaction')
            ->once()
            ->andReturnUsing(function ($callback) {
                return $callback();
            });

        $service = new AccountService(
            $repo,
            Mockery::mock(CloseAccountAction::class),
            $updateAction,
            Mockery::mock(ChangeParentAccountAction::class),
            Mockery::mock(UserService::class)
        );

        // Act
        $result = $service->updateMeta($uuid, $meta);

        // Assert
        $this->assertInstanceOf(AccountResource::class, $result);
    }

    public function test_change_parent_changes_account_parent(): void
    {
        // Arrange
        $uuid = 'test-uuid';
        $parentUuid = 'parent-uuid';
        $account = new Account();
        $account->uuid = $uuid;
        $parent = new Account();
        $parent->uuid = $parentUuid;

        $changeAction = Mockery::mock(ChangeParentAccountAction::class);
        $changeAction->shouldReceive('execute')
            ->once()
            ->with($account, $parent);

        $repo = Mockery::mock(AccountRepositoryInterface::class);
        $repo->shouldReceive('findByUuid')
            ->twice()
            ->andReturn($account, $parent);
        $repo->shouldReceive('save')
            ->once()
            ->with($account)
            ->andReturn($account);
        $repo->shouldReceive('load')
            ->once()
            ->with($account)
            ->andReturn($account);

        DB::shouldReceive('transaction')
            ->once()
            ->andReturnUsing(function ($callback) {
                return $callback();
            });

        $service = new AccountService(
            $repo,
            Mockery::mock(CloseAccountAction::class),
            Mockery::mock(UpdateAccountMetaAction::class),
            $changeAction,
            Mockery::mock(UserService::class)
        );

        // Act
        $result = $service->changeParent($uuid, $parentUuid);

        // Assert
        $this->assertInstanceOf(AccountResource::class, $result);
    }
}

