<?php

namespace Tests\Unit\Account;

use Mockery;
use Modules\Account\Actions\ChangeParentAccountAction;
use Modules\Account\Models\Account;
use Tests\TestCase;

class ChangeParentAccountActionTest extends TestCase
{
    protected function tearDown(): void
    {
        Mockery::close();
        parent::tearDown();
    }

    public function test_execute_sets_parent_account(): void
    {
        // Arrange
        $account = new Account();
        $account->id = 1;
        $parent = new Account();
        $parent->id = 2;

        $action = new ChangeParentAccountAction();

        // Act
        $action->execute($account, $parent);

        // Assert
        $this->assertEquals($parent->id, $account->parent_account_id);
    }

    public function test_execute_removes_parent_when_null(): void
    {
        // Arrange
        $account = new Account();
        $account->id = 1;
        $account->parent_account_id = 2;
        $account->state = \Modules\Account\Enums\AccountState::ACTIVE;

        $action = new ChangeParentAccountAction();

        // Act
        $action->execute($account, null);

        // Assert
        $this->assertNull($account->parent_account_id);
    }

    public function test_execute_throws_exception_when_account_is_closed(): void
    {
        // Arrange
        $account = new Account();
        $account->id = 1;
        $account->state = \Modules\Account\Enums\AccountState::CLOSED;
        $parent = new Account();
        $parent->id = 2;

        $action = new ChangeParentAccountAction();

        // Assert
        $this->expectException(\DomainException::class);
        $this->expectExceptionMessage('Cannot move a closed account.');

        // Act
        $action->execute($account, $parent);
    }

    public function test_execute_throws_exception_when_account_is_parent_of_itself(): void
    {
        // Arrange
        $account = new Account();
        $account->id = 1;
        $account->state = \Modules\Account\Enums\AccountState::ACTIVE;

        $action = new ChangeParentAccountAction();

        // Assert
        $this->expectException(\DomainException::class);
        $this->expectExceptionMessage('Account cannot be parent of itself.');

        // Act
        $action->execute($account, $account);
    }
}

