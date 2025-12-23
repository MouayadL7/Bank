<?php

namespace Tests\Unit\Account;

use DomainException;
use Mockery;
use Modules\Account\Actions\CloseAccountAction;
use Modules\Account\Models\Account;
use Tests\TestCase;

class CloseAccountActionTest extends TestCase
{

    protected function tearDown(): void
    {
        Mockery::close();
        parent::tearDown();
    }

    public function test_execute_throws_exception_when_balance_is_not_zero(): void
    {
        // Arrange
        $account = Mockery::mock(Account::class)->makePartial();
        $account->shouldAllowMockingProtectedMethods();
        $account->balance = 100.50;

        $action = new CloseAccountAction();

        // Assert
        $this->expectException(DomainException::class);
        $this->expectExceptionMessage('Account balance must be zero before closure.');

        // Act
        $action->execute($account);
    }

    public function test_execute_throws_exception_when_children_exist(): void
    {
        // Arrange
        $account = Mockery::mock(Account::class)->makePartial();
        $account->shouldAllowMockingProtectedMethods();
        $account->balance = 0.0;

        $children = Mockery::mock(\Illuminate\Database\Eloquent\Relations\HasMany::class);
        $children->shouldReceive('where')
            ->once()
            ->with('state', '!=', 'closed')
            ->andReturnSelf();
        $children->shouldReceive('exists')
            ->once()
            ->andReturn(true);

        $account->shouldReceive('children')
            ->once()
            ->andReturn($children);

        $action = new CloseAccountAction();

        // Assert
        $this->expectException(DomainException::class);
        $this->expectExceptionMessage('All child accounts must be closed first.');

        // Act
        $action->execute($account);
    }

    public function test_execute_closes_account_when_conditions_met(): void
    {
        // Arrange
        $account = Mockery::mock(Account::class)->makePartial();
        $account->shouldAllowMockingProtectedMethods();
        $account->balance = 0.0;

        $children = Mockery::mock(\Illuminate\Database\Eloquent\Relations\HasMany::class);
        $children->shouldReceive('where')
            ->once()
            ->with('state', '!=', 'closed')
            ->andReturnSelf();
        $children->shouldReceive('exists')
            ->once()
            ->andReturn(false);

        $account->shouldReceive('children')
            ->once()
            ->andReturn($children);

        $stateInstance = Mockery::mock(\Modules\Account\Patterns\States\AccountStateInterface::class);
        $stateInstance->shouldReceive('transitionTo')
            ->once()
            ->with($account, 'closed');

        $account->shouldReceive('getStateInstance')
            ->once()
            ->andReturn($stateInstance);

        $action = new CloseAccountAction();

        // Act
        $action->execute($account);

        // Assert - if we reach here without exception, the method executed successfully
        $this->assertTrue(true);
    }
}

