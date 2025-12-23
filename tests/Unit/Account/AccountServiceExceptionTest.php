<?php

namespace Tests\Unit\Account;

use DomainException;
use Illuminate\Support\Facades\DB;
use Mockery;
use Modules\Account\Actions\CloseAccountAction;
use Modules\Account\Actions\UpdateAccountMetaAction;
use Modules\Account\Actions\ChangeParentAccountAction;
use Modules\Account\Models\Account;
use Modules\Account\Repositories\Interfaces\AccountRepositoryInterface;
use Modules\Account\Services\AccountService;
use Modules\User\Services\UserService;
use Tests\TestCase;

class AccountServiceExceptionTest extends TestCase
{

    protected function tearDown(): void
    {
        Mockery::close();
        parent::tearDown();
    }

    public function test_close_throws_exception_when_close_action_fails(): void
    {
        // Arrange
        $uuid = 'test-uuid';
        $account = new Account();
        $account->uuid = $uuid;

        $closeAction = Mockery::mock(CloseAccountAction::class);
        $closeAction->shouldReceive('execute')
            ->once()
            ->with($account)
            ->andThrow(new DomainException('Account balance must be zero before closure.'));

        $repo = Mockery::mock(AccountRepositoryInterface::class);
        $repo->shouldReceive('findByUuid')
            ->once()
            ->with($uuid)
            ->andReturn($account);

        DB::shouldReceive('transaction')
            ->once()
            ->andReturnUsing(function ($callback) {
                try {
                    return $callback();
                } catch (\Exception $e) {
                    throw $e;
                }
            });

        $service = new AccountService(
            $repo,
            $closeAction,
            Mockery::mock(UpdateAccountMetaAction::class),
            Mockery::mock(ChangeParentAccountAction::class),
            Mockery::mock(UserService::class)
        );

        // Assert
        $this->expectException(DomainException::class);
        $this->expectExceptionMessage('Account balance must be zero before closure.');

        // Act
        $service->close($uuid);
    }

    public function test_update_meta_throws_exception_when_account_is_closed(): void
    {
        // Arrange
        $uuid = 'test-uuid';
        $meta = ['key' => 'value'];
        $account = new Account();
        $account->uuid = $uuid;
        $account->state = 'closed';

        $updateAction = Mockery::mock(UpdateAccountMetaAction::class);
        $updateAction->shouldReceive('execute')
            ->once()
            ->with($account, $meta)
            ->andThrow(new DomainException('Cannot modify a closed account.'));

        $repo = Mockery::mock(AccountRepositoryInterface::class);
        $repo->shouldReceive('findByUuid')
            ->once()
            ->with($uuid)
            ->andReturn($account);

        DB::shouldReceive('transaction')
            ->once()
            ->andReturnUsing(function ($callback) {
                try {
                    return $callback();
                } catch (\Exception $e) {
                    throw $e;
                }
            });

        $service = new AccountService(
            $repo,
            Mockery::mock(CloseAccountAction::class),
            $updateAction,
            Mockery::mock(ChangeParentAccountAction::class),
            Mockery::mock(UserService::class)
        );

        // Assert
        $this->expectException(DomainException::class);
        $this->expectExceptionMessage('Cannot modify a closed account.');

        // Act
        $service->updateMeta($uuid, $meta);
    }

    public function test_change_parent_throws_exception_when_account_is_closed(): void
    {
        // Arrange
        $uuid = 'test-uuid';
        $parentUuid = 'parent-uuid';
        $account = new Account();
        $account->uuid = $uuid;
        $account->state = \Modules\Account\Enums\AccountState::CLOSED;
        $parent = new Account();
        $parent->uuid = $parentUuid;

        $changeAction = Mockery::mock(ChangeParentAccountAction::class);
        $changeAction->shouldReceive('execute')
            ->once()
            ->with($account, $parent)
            ->andThrow(new DomainException('Cannot move a closed account.'));

        $repo = Mockery::mock(AccountRepositoryInterface::class);
        $repo->shouldReceive('findByUuid')
            ->twice()
            ->andReturn($account, $parent);

        DB::shouldReceive('transaction')
            ->once()
            ->andReturnUsing(function ($callback) {
                try {
                    return $callback();
                } catch (\Exception $e) {
                    throw $e;
                }
            });

        $service = new AccountService(
            $repo,
            Mockery::mock(CloseAccountAction::class),
            Mockery::mock(UpdateAccountMetaAction::class),
            $changeAction,
            Mockery::mock(UserService::class)
        );

        // Assert
        $this->expectException(DomainException::class);
        $this->expectExceptionMessage('Cannot move a closed account.');

        // Act
        $service->changeParent($uuid, $parentUuid);
    }
}

