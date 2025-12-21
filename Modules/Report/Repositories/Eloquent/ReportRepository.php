<?php

namespace Modules\Report\Repositories\Eloquent;

use Carbon\Carbon;
use Illuminate\Support\Collection;
use Modules\Account\Models\Account;
use Modules\Report\Models\AuditLog;
use Modules\Report\Repositories\Interfaces\ReportRepositoryInterface;
use Modules\Transaction\Models\Transaction;

class ReportRepository implements ReportRepositoryInterface
{
    public function transactionsBetween(Carbon $from, Carbon $to, ?string $status = null, ?string $type = null): Collection
    {
        $query = Transaction::query()
            ->whereBetween('created_at', [$from, $to])
            ->orderBy('created_at');

        if ($status) {
            $query->where('status', $status);
        }

        if ($type) {
            $query->where('type', $type);
        }

        return $query->get();
    }

    public function accountsFiltered(
        ?string $type = null,
        ?string $state = null,
        ?float $minBalance = null,
        ?float $maxBalance = null,
        ?string $openedFrom = null,
        ?string $openedTo = null,
    ): Collection {
        $query = Account::query();

        if ($type) {
            $query->where('type', $type);
        }

        if ($state) {
            $query->where('state', $state);
        }

        if ($minBalance !== null) {
            $query->where('balance', '>=', $minBalance);
        }

        if ($maxBalance !== null) {
            $query->where('balance', '<=', $maxBalance);
        }

        if ($openedFrom) {
            $query->whereDate('opened_at', '>=', $openedFrom);
        }

        if ($openedTo) {
            $query->whereDate('opened_at', '<=', $openedTo);
        }

        return $query->orderByDesc('created_at')->get();
    }

    public function auditLogs(array $filters, int $limit = 100): Collection
    {
        $query = AuditLog::query()->orderByDesc('created_at');

        if (!empty($filters['event'])) {
            $query->where('event', $filters['event']);
        }

        if (!empty($filters['subject_type'])) {
            $query->where('subject_type', $filters['subject_type']);
        }

        if (!empty($filters['subject_id'])) {
            $query->where('subject_id', $filters['subject_id']);
        }

        if (!empty($filters['from_date'])) {
            $query->whereDate('created_at', '>=', $filters['from_date']);
        }

        if (!empty($filters['to_date'])) {
            $query->whereDate('created_at', '<=', $filters['to_date']);
        }

        $maxLimit = (int) config('report.audit.max_limit', 500);
        $limit = min($limit, $maxLimit);

        return $query->limit($limit)->get();
    }

    public function storeAuditLog(array $payload): AuditLog
    {
        return AuditLog::create($payload);
    }
}

