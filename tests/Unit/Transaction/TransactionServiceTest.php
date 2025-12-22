<?php

namespace Tests\Unit\Transaction;

use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Event;
use Mockery;
use Modules\Account\Models\Account;
use Modules\Account\Repositories\Interfaces\AccountRepositoryInterface;
use Modules\Transaction\Actions\DepositAction;
use Modules\Transaction\Actions\TransferAction;
use Modules\Transaction\Actions\WithdrawAction;
use Modules\Transaction\Enums\TransactionTypeEnum;
use Modules\Transaction\Events\AccountBalanceUpdated;
use Modules\Transaction\Handlers\AutoApprovalHandler;
use Modules\Transaction\Models\Transaction;
use Modules\Transaction\Repositories\Interfaces\TransactionRepositoryInterface;
use Modules\Transaction\Services\TransactionService;
use Tests\TestCase;

class TransactionServiceTest extends TestCase
{

    protected function tearDown(): void
    {
        Mockery::close();
        parent::tearDown();
    }

    public function test_deposit_creates_and_processes_transaction(): void
    {
        // Arrange
        $uuid = 'test-uuid';
        $amount = 100.50;
        $account = new Account();
        $account->id = 1;
        $account->uuid = $uuid;

        $transaction = Mockery::mock(Transaction::class)->makePartial();
        $transaction->shouldAllowMockingProtectedMethods();
        $transaction->id = 1;
        $transaction->type = TransactionTypeEnum::DEPOSIT->value;
        $transaction->shouldReceive('isApproved')
            ->andReturn(true);

        $accountRepo = Mockery::mock(AccountRepositoryInterface::class);
        $accountRepo->shouldReceive('findByUuid')
            ->once()
            ->with($uuid)
            ->andReturn($account);
        $accountRepo->shouldReceive('save')
            ->once()
            ->andReturn($account);

        $transactionRepo = Mockery::mock(TransactionRepositoryInterface::class);
        $transactionRepo->shouldReceive('create')
            ->once()
            ->andReturn($transaction);

        $depositAction = Mockery::mock(DepositAction::class);
        $depositAction->shouldReceive('execute')
            ->once()
            ->with($account, $amount)
            ->andReturn($account);

        $autoApproveHandler = Mockery::mock(AutoApprovalHandler::class);
        $autoApproveHandler->shouldReceive('handle')
            ->once()
            ->with($transaction);

        Event::fake();

        DB::shouldReceive('transaction')
            ->once()
            ->andReturnUsing(function ($callback) {
                return $callback();
            });

        $user = Mockery::mock(\Modules\User\Models\User::class)->makePartial();
        $user->shouldAllowMockingProtectedMethods();
        $user->shouldReceive('getAttribute')
            ->with('role_id')
            ->andReturn(1);
        $user->role_id = 1;
        $this->actingAs($user);

        $service = new TransactionService(
            $transactionRepo,
            $accountRepo,
            $depositAction,
            Mockery::mock(WithdrawAction::class),
            Mockery::mock(TransferAction::class),
            $autoApproveHandler
        );

        // Act
        $result = $service->deposit($uuid, $amount);

        // Assert
        $this->assertInstanceOf(\Modules\Transaction\Http\Resources\TransactionResource::class, $result);
        Event::assertDispatched(AccountBalanceUpdated::class);
    }

    public function test_withdraw_creates_and_processes_transaction(): void
    {
        // Arrange
        $uuid = 'test-uuid';
        $amount = 50.25;
        $account = new Account();
        $account->id = 1;
        $account->uuid = $uuid;

        $transaction = Mockery::mock(Transaction::class)->makePartial();
        $transaction->shouldAllowMockingProtectedMethods();
        $transaction->id = 1;
        $transaction->type = TransactionTypeEnum::WITHDRAWAL->value;
        $transaction->shouldReceive('isApproved')
            ->andReturn(true);

        $accountRepo = Mockery::mock(AccountRepositoryInterface::class);
        $accountRepo->shouldReceive('findByUuid')
            ->once()
            ->with($uuid)
            ->andReturn($account);
        $accountRepo->shouldReceive('save')
            ->once()
            ->andReturn($account);

        $transactionRepo = Mockery::mock(TransactionRepositoryInterface::class);
        $transactionRepo->shouldReceive('create')
            ->once()
            ->andReturn($transaction);

        $withdrawAction = Mockery::mock(WithdrawAction::class);
        $withdrawAction->shouldReceive('execute')
            ->once()
            ->with($account, $amount)
            ->andReturn($account);

        $autoApproveHandler = Mockery::mock(AutoApprovalHandler::class);
        $autoApproveHandler->shouldReceive('handle')
            ->once()
            ->with($transaction);

        Event::fake();

        DB::shouldReceive('transaction')
            ->once()
            ->andReturnUsing(function ($callback) {
                return $callback();
            });

        $user = Mockery::mock(\Modules\User\Models\User::class)->makePartial();
        $user->shouldAllowMockingProtectedMethods();
        $user->shouldReceive('getAttribute')
            ->with('role_id')
            ->andReturn(1);
        $user->role_id = 1;
        $this->actingAs($user);

        $service = new TransactionService(
            $transactionRepo,
            $accountRepo,
            Mockery::mock(DepositAction::class),
            $withdrawAction,
            Mockery::mock(TransferAction::class),
            $autoApproveHandler
        );

        // Act
        $result = $service->withdraw($uuid, $amount);

        // Assert
        $this->assertInstanceOf(\Modules\Transaction\Http\Resources\TransactionResource::class, $result);
        Event::assertDispatched(AccountBalanceUpdated::class);
    }

    public function test_transfer_creates_and_processes_transaction(): void
    {
        // Arrange
        $fromUuid = 'from-uuid';
        $toUuid = 'to-uuid';
        $amount = 75.00;
        $fromAccount = new Account();
        $fromAccount->id = 1;
        $fromAccount->uuid = $fromUuid;
        $toAccount = new Account();
        $toAccount->id = 2;
        $toAccount->uuid = $toUuid;

        $transaction = Mockery::mock(Transaction::class)->makePartial();
        $transaction->shouldAllowMockingProtectedMethods();
        $transaction->id = 1;
        $transaction->type = TransactionTypeEnum::TRANSFER->value;
        $transaction->shouldReceive('isApproved')
            ->andReturn(true);

        $accountRepo = Mockery::mock(AccountRepositoryInterface::class);
        $accountRepo->shouldReceive('findByUuid')
            ->twice()
            ->andReturn($fromAccount, $toAccount);
        $accountRepo->shouldReceive('save')
            ->twice()
            ->andReturn($fromAccount, $toAccount);

        $transactionRepo = Mockery::mock(TransactionRepositoryInterface::class);
        $transactionRepo->shouldReceive('create')
            ->once()
            ->andReturn($transaction);

        $transferAction = Mockery::mock(TransferAction::class);
        $transferAction->shouldReceive('execute')
            ->once()
            ->with($fromAccount, $toAccount, $amount);

        $autoApproveHandler = Mockery::mock(AutoApprovalHandler::class);
        $autoApproveHandler->shouldReceive('handle')
            ->once()
            ->with($transaction);

        Event::fake();

        DB::shouldReceive('transaction')
            ->once()
            ->andReturnUsing(function ($callback) {
                return $callback();
            });

        $user = Mockery::mock(\Modules\User\Models\User::class)->makePartial();
        $user->shouldAllowMockingProtectedMethods();
        $user->shouldReceive('getAttribute')
            ->with('role_id')
            ->andReturn(1);
        $user->role_id = 1;
        $this->actingAs($user);

        $service = new TransactionService(
            $transactionRepo,
            $accountRepo,
            Mockery::mock(DepositAction::class),
            Mockery::mock(WithdrawAction::class),
            $transferAction,
            $autoApproveHandler
        );

        // Act
        $result = $service->transfare($fromUuid, $toUuid, $amount);

        // Assert
        $this->assertInstanceOf(\Modules\Transaction\Http\Resources\TransactionResource::class, $result);
        Event::assertDispatched(AccountBalanceUpdated::class);
    }

    public function test_approve_transaction_approves_and_processes(): void
    {
        // Arrange
        $uuid = 'transaction-uuid';
        $userId = 1;
        $amount = 100.00;

        $fromAccount = new Account();
        $fromAccount->id = 1;
        $toAccount = new Account();
        $toAccount->id = 2;

        $transaction = Mockery::mock(Transaction::class)->makePartial();
        $transaction->shouldAllowMockingProtectedMethods();
        $transaction->shouldReceive('isApproved')
            ->once()
            ->andReturn(false);
        $transaction->fromAccount = $fromAccount;
        $transaction->toAccount = $toAccount;
        $transaction->amount = $amount;
        $transaction->type = TransactionTypeEnum::DEPOSIT->value;
        $transaction->shouldReceive('approve')
            ->once()
            ->with($userId);

        $transactionRepo = Mockery::mock(TransactionRepositoryInterface::class);
        $transactionRepo->shouldReceive('findByUuid')
            ->once()
            ->with($uuid)
            ->andReturn($transaction);

        $accountRepo = Mockery::mock(AccountRepositoryInterface::class);
        $accountRepo->shouldReceive('save')
            ->once()
            ->andReturn($fromAccount);

        $depositAction = Mockery::mock(DepositAction::class);
        $depositAction->shouldReceive('execute')
            ->once()
            ->with($fromAccount, $amount)
            ->andReturn($fromAccount);

        Event::fake();

        Auth::shouldReceive('id')
            ->once()
            ->andReturn($userId);

        DB::shouldReceive('transaction')
            ->once()
            ->andReturnUsing(function ($callback) {
                return $callback();
            });

        $service = new TransactionService(
            $transactionRepo,
            $accountRepo,
            $depositAction,
            Mockery::mock(WithdrawAction::class),
            Mockery::mock(TransferAction::class),
            Mockery::mock(AutoApprovalHandler::class)
        );

        // Act
        $result = $service->approveTransaction($uuid);

        // Assert
        $this->assertInstanceOf(\Modules\Transaction\Http\Resources\TransactionResource::class, $result);
        Event::assertDispatched(AccountBalanceUpdated::class);
    }

    public function test_reject_transaction_rejects_transaction(): void
    {
        // Arrange
        $uuid = 'transaction-uuid';
        $userId = 1;

        $transaction = Mockery::mock(Transaction::class);
        $transaction->shouldReceive('isRejected')
            ->once()
            ->andReturn(false);
        $transaction->shouldReceive('reject')
            ->once()
            ->with($userId);

        $transactionRepo = Mockery::mock(TransactionRepositoryInterface::class);
        $transactionRepo->shouldReceive('findByUuid')
            ->once()
            ->with($uuid)
            ->andReturn($transaction);

        Auth::shouldReceive('id')
            ->once()
            ->andReturn($userId);

        DB::shouldReceive('transaction')
            ->once()
            ->andReturnUsing(function ($callback) {
                return $callback();
            });

        $service = new TransactionService(
            $transactionRepo,
            Mockery::mock(AccountRepositoryInterface::class),
            Mockery::mock(DepositAction::class),
            Mockery::mock(WithdrawAction::class),
            Mockery::mock(TransferAction::class),
            Mockery::mock(AutoApprovalHandler::class)
        );

        // Act
        $result = $service->rejectTransaction($uuid);

        // Assert
        $this->assertInstanceOf(\Modules\Transaction\Http\Resources\TransactionResource::class, $result);
    }

    public function test_approve_transaction_throws_exception_when_already_approved(): void
    {
        // Arrange
        $uuid = 'transaction-uuid';

        $transaction = Mockery::mock(Transaction::class);
        $transaction->shouldReceive('isApproved')
            ->once()
            ->andReturn(true);

        $transactionRepo = Mockery::mock(TransactionRepositoryInterface::class);
        $transactionRepo->shouldReceive('findByUuid')
            ->once()
            ->with($uuid)
            ->andReturn($transaction);

        DB::shouldReceive('transaction')
            ->once()
            ->andReturnUsing(function ($callback) {
                return $callback();
            });

        $service = new TransactionService(
            $transactionRepo,
            Mockery::mock(AccountRepositoryInterface::class),
            Mockery::mock(DepositAction::class),
            Mockery::mock(WithdrawAction::class),
            Mockery::mock(TransferAction::class),
            Mockery::mock(AutoApprovalHandler::class)
        );

        // Assert
        $this->expectException(\Exception::class);
        $this->expectExceptionMessage('Transaction has already been approved.');

        // Act
        $service->approveTransaction($uuid);
    }

    public function test_reject_transaction_throws_exception_when_already_rejected(): void
    {
        // Arrange
        $uuid = 'transaction-uuid';

        $transaction = Mockery::mock(Transaction::class);
        $transaction->shouldReceive('isRejected')
            ->once()
            ->andReturn(true);

        $transactionRepo = Mockery::mock(TransactionRepositoryInterface::class);
        $transactionRepo->shouldReceive('findByUuid')
            ->once()
            ->with($uuid)
            ->andReturn($transaction);

        DB::shouldReceive('transaction')
            ->once()
            ->andReturnUsing(function ($callback) {
                return $callback();
            });

        $service = new TransactionService(
            $transactionRepo,
            Mockery::mock(AccountRepositoryInterface::class),
            Mockery::mock(DepositAction::class),
            Mockery::mock(WithdrawAction::class),
            Mockery::mock(TransferAction::class),
            Mockery::mock(AutoApprovalHandler::class)
        );

        // Assert
        $this->expectException(\Exception::class);
        $this->expectExceptionMessage('Transaction has already been rejected.');

        // Act
        $service->rejectTransaction($uuid);
    }
}

