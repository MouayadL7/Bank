<?php

namespace Tests\Unit\Transaction;

use Mockery;
use Modules\Account\Factories\AccountComponentFactory;
use Modules\Account\Models\Account;
use Modules\Transaction\Actions\DepositAction;
use Tests\TestCase;

class DepositActionTest extends TestCase
{
    protected function tearDown(): void
    {
        Mockery::close();
        parent::tearDown();
    }

    public function test_execute_deposits_amount_to_account(): void
    {
        // Arrange
        $account = Mockery::mock(Account::class)->makePartial();
        $account->shouldAllowMockingProtectedMethods();
        $account->id = 1;
        $account->balance = 0;
        $account->shouldReceive('isComposite')
            ->andReturn(false);
        $account->children = collect([]);
        
        // Mock the state instance
        $stateInstance = Mockery::mock(\Modules\Account\Patterns\States\AccountStateInterface::class);
        $stateInstance->shouldReceive('deposit')
            ->once()
            ->with($account, 100.50);
        
        $account->shouldReceive('getStateInstance')
            ->andReturn($stateInstance);
        
        $amount = 100.50;

        $action = new DepositAction();

        // Act
        $result = $action->execute($account, $amount);

        // Assert
        $this->assertEquals($account, $result);
    }
}

