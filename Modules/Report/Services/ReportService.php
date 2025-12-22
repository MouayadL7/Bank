<?php

namespace Modules\Report\Services;

use Modules\Transaction\Enums\TransactionStatusEnum;
use Modules\Transaction\Enums\TransactionTypeEnum;
use Carbon\Carbon;
use Modules\Account\Enums\AccountState;
use Modules\Account\Enums\AccountType;
use Modules\Report\Http\Resources\AuditLogResource;
use Modules\Report\Repositories\Interfaces\ReportRepositoryInterface;
use Modules\Transaction\Http\Resources\TransactionResource;

class ReportService
{
    public function __construct(private ReportRepositoryInterface $repository) {}

    public function dailyTransactions(Carbon $from, Carbon $to, ?string $status = null, ?string $type = null): array
    {
        $transactions = $this->repository->transactionsBetween($from, $to, $status, $type);

        $totalAmount = $transactions->sum(fn ($tx) => (float) $tx->amount);

        $byType = $transactions->groupBy(fn ($tx) => $tx->type instanceof TransactionTypeEnum ? $tx->type->value : (string) $tx->type)
            ->map(fn ($items) => [
                'count' => $items->count(),
                'amount' => $items->sum(fn ($tx) => (float) $tx->amount),
            ])->toArray();

        $byStatus = $transactions->groupBy(fn ($tx) => $tx->status instanceof TransactionStatusEnum ? $tx->status->value : (string) $tx->status)
            ->map(fn ($items) => [
                'count' => $items->count(),
                'amount' => $items->sum(fn ($tx) => (float) $tx->amount),
            ])->toArray();

        return [
            'period' => [
                'from' => $from->toIsoString(),
                'to' => $to->toIsoString(),
            ],
            'summary' => [
                'count' => $transactions->count(),
                'total_amount' => $totalAmount,
                'by_type' => $byType,
                'by_status' => $byStatus,
            ],
            'transactions' => TransactionResource::collection($transactions),
        ];
    }

    public function accountSummary(
        ?string $type = null,
        ?string $state = null,
        ?float $minBalance = null,
        ?float $maxBalance = null,
        ?string $openedFrom = null,
        ?string $openedTo = null,
    ): array {
        $accounts = $this->repository->accountsFiltered($type, $state, $minBalance, $maxBalance, $openedFrom, $openedTo);

        $byType = $accounts->groupBy(fn ($acc) => $acc->type instanceof AccountType ? $acc->type->value : (string) $acc->type)
            ->map(fn ($items) => $items->count())
            ->toArray();

        $byState = $accounts->groupBy(fn ($acc) => $acc->state instanceof AccountState ? $acc->state->value : (string) $acc->state)
            ->map(fn ($items) => $items->count())
            ->toArray();

        $totalBalance = $accounts->sum(fn ($acc) => (float) $acc->balance);
        $averageBalance = $accounts->count() ? $totalBalance / $accounts->count() : 0.0;

        $accountsSnapshot = $accounts->take(100)->map(function ($account) {
            return [
                'uuid' => $account->uuid,
                'type' => $account->type instanceof AccountType ? $account->type->value : (string) $account->type,
                'state' => $account->state instanceof AccountState ? $account->state->value : (string) $account->state,
                'balance' => (float) $account->balance,
                'currency' => $account->currency,
                'opened_at' => $account->opened_at?->toIsoString(),
                'updated_at' => $account->updated_at?->toIsoString(),
            ];
        })->values();

        return [
            'filters' => array_filter([
                'type' => $type,
                'state' => $state,
                'min_balance' => $minBalance,
                'max_balance' => $maxBalance,
                'opened_from' => $openedFrom,
                'opened_to' => $openedTo,
            ], fn ($value) => $value !== null && $value !== ''),
            'summary' => [
                'total_accounts' => $accounts->count(),
                'by_type' => $byType,
                'by_state' => $byState,
                'total_balance' => $totalBalance,
                'average_balance' => $averageBalance,
            ],
            'accounts' => $accountsSnapshot,
        ];
    }

    public function auditLogs(array $filters): array
    {
        $limit = (int) ($filters['limit'] ?? config('report.audit.default_limit', 100));
        $logs = $this->repository->auditLogs($filters, $limit);

        return [
            'filters' => array_filter($filters, fn ($value) => $value !== null && $value !== ''),
            'logs' => AuditLogResource::collection($logs),
        ];
    }
}

