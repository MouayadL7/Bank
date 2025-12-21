<?php

namespace Modules\Report\Repositories\Interfaces;

use Carbon\Carbon;
use Illuminate\Support\Collection;
use Modules\Report\Models\AuditLog;

interface ReportRepositoryInterface
{
    public function transactionsBetween(Carbon $from, Carbon $to, ?string $status = null, ?string $type = null): Collection;

    public function accountsFiltered(
        ?string $type = null,
        ?string $state = null,
        ?float $minBalance = null,
        ?float $maxBalance = null,
        ?string $openedFrom = null,
        ?string $openedTo = null,
    ): Collection;

    public function auditLogs(array $filters, int $limit = 100): Collection;

    public function storeAuditLog(array $payload): AuditLog;
}

