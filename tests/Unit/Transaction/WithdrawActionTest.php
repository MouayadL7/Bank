<?php

namespace Tests\Unit\Transaction;

use Mockery;
use Modules\Account\Factories\AccountComponentFactory;
use Modules\Account\Models\Account;
use Modules\Transaction\Actions\WithdrawAction;
use Tests\TestCase;

class WithdrawActionTest extends TestCase
{
    protected function tearDown(): void
    {
        Mockery::close();
        parent::tearDown();
    }

    public function test_execute_withdraws_amount_from_account(): void
    {
        // Arrange
        $account = Mockery::mock(Account::class)->makePartial();
        $account->shouldAllowMockingProtectedMethods();
        $account->id = 1;
        $account->balance = 100.00;
        $account->shouldReceive('isComposite')
            ->andReturn(false);
        $account->children = collect([]);
        
        // Mock the state instance
        $stateInstance = Mockery::mock(\Modules\Account\Patterns\States\AccountStateInterface::class);
        $stateInstance->shouldReceive('withdraw')
            ->once()
            ->with($account, 50.25);
        
        $account->shouldReceive('getStateInstance')
            ->andReturn($stateInstance);
        
        $amount = 50.25;

        $action = new WithdrawAction();

        // Act
        $result = $action->execute($account, $amount);

        // Assert
        $this->assertEquals($account, $result);
    }
}

