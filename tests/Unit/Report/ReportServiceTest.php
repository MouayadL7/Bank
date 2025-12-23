<?php

namespace Tests\Unit\Report;

use Carbon\Carbon;
use Illuminate\Support\Collection;
use Mockery;
use Modules\Report\Repositories\Interfaces\ReportRepositoryInterface;
use Modules\Report\Services\ReportService;
use Modules\Transaction\Models\Transaction;
use Tests\TestCase;

class ReportServiceTest extends TestCase
{

    protected function tearDown(): void
    {
        Mockery::close();
        parent::tearDown();
    }

    public function test_daily_transactions_returns_formatted_report(): void
    {
        // Arrange
        $from = Carbon::now()->subDays(7);
        $to = Carbon::now();
        $status = 'approved';
        $type = 'deposit';

        $transaction = new Transaction();
        $transaction->amount = 100.00;
        $transaction->type = 'deposit';
        $transaction->status = 'approved';

        $transactions = collect([$transaction]);

        $repository = Mockery::mock(ReportRepositoryInterface::class);
        $repository->shouldReceive('transactionsBetween')
            ->once()
            ->with($from, $to, $status, $type)
            ->andReturn($transactions);

        $service = new ReportService($repository);

        // Act
        $result = $service->dailyTransactions($from, $to, $status, $type);

        // Assert
        $this->assertIsArray($result);
        $this->assertArrayHasKey('period', $result);
        $this->assertArrayHasKey('summary', $result);
        $this->assertArrayHasKey('transactions', $result);
        $this->assertEquals(100.00, $result['summary']['total_amount']);
    }

    public function test_account_summary_returns_formatted_report(): void
    {
        // Arrange
        $account = Mockery::mock(\Modules\Account\Models\Account::class)->makePartial();
        $account->shouldAllowMockingProtectedMethods();
        $account->uuid = 'test-uuid';
        // Use actual enum instances instead of mocking (enums are final and can't be mocked)
        $account->type = \Modules\Account\Enums\AccountType::SAVINGS;
        $account->state = \Modules\Account\Enums\AccountState::ACTIVE;
        $account->balance = 1000.00;
        $account->currency = 'USD';
        $account->opened_at = Carbon::now();
        $account->updated_at = Carbon::now();

        $accounts = collect([$account]);

        $repository = Mockery::mock(ReportRepositoryInterface::class);
        $repository->shouldReceive('accountsFiltered')
            ->once()
            ->andReturn($accounts);

        $service = new ReportService($repository);

        // Act
        $result = $service->accountSummary();

        // Assert
        $this->assertIsArray($result);
        $this->assertArrayHasKey('filters', $result);
        $this->assertArrayHasKey('summary', $result);
        $this->assertArrayHasKey('accounts', $result);
        $this->assertEquals(1, $result['summary']['total_accounts']);
    }

    public function test_audit_logs_returns_formatted_logs(): void
    {
        // Arrange
        $filters = ['limit' => 50];
        $logs = collect([]);

        $repository = Mockery::mock(ReportRepositoryInterface::class);
        $repository->shouldReceive('auditLogs')
            ->once()
            ->with($filters, 50)
            ->andReturn($logs);

        $service = new ReportService($repository);

        // Act
        $result = $service->auditLogs($filters);

        // Assert
        $this->assertIsArray($result);
        $this->assertArrayHasKey('filters', $result);
        $this->assertArrayHasKey('logs', $result);
    }
}

