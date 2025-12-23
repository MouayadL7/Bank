<?php

namespace Tests\Unit\Transaction;

use Mockery;
use Modules\Account\Models\Account;
use Modules\Transaction\Actions\DepositAction;
use Modules\Transaction\Actions\TransferAction;
use Modules\Transaction\Actions\WithdrawAction;
use Tests\TestCase;

class TransferActionTest extends TestCase
{
    protected function tearDown(): void
    {
        Mockery::close();
        parent::tearDown();
    }

    public function test_execute_transfers_amount_between_accounts(): void
    {
        // Arrange
        $fromAccount = new Account();
        $fromAccount->id = 1;
        $toAccount = new Account();
        $toAccount->id = 2;
        $amount = 75.00;

        $withdrawAction = Mockery::mock(WithdrawAction::class);
        $withdrawAction->shouldReceive('execute')
            ->once()
            ->with($fromAccount, $amount)
            ->andReturn($fromAccount);

        $depositAction = Mockery::mock(DepositAction::class);
        $depositAction->shouldReceive('execute')
            ->once()
            ->with($toAccount, $amount)
            ->andReturn($toAccount);

        $action = new TransferAction($withdrawAction, $depositAction);

        // Act
        $action->execute($fromAccount, $toAccount, $amount);

        // Assert - if we reach here without exception, the method executed successfully
        $this->assertTrue(true);
    }
}

