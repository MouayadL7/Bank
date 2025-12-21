<?php

namespace Modules\Report\Http\Controllers;

use Carbon\Carbon;
use Modules\Core\Http\Controllers\BaseController;
use Modules\Report\Http\Requests\AccountSummaryRequest;
use Modules\Report\Http\Requests\AuditLogRequest;
use Modules\Report\Http\Requests\TransactionReportRequest;
use Modules\Report\Services\ReportService;

class ReportController extends BaseController
{
    public function __construct(private ReportService $service) {}

    public function dailyTransactions(TransactionReportRequest $request)
    {
        $validated = $request->validated();

        $from = isset($validated['from_date'])
            ? Carbon::parse($validated['from_date'])->startOfDay()
            : Carbon::now()->startOfDay();

        $to = isset($validated['to_date'])
            ? Carbon::parse($validated['to_date'])->endOfDay()
            : Carbon::now()->endOfDay();

        $report = $this->service->dailyTransactions(
            from: $from,
            to: $to,
            status: $validated['status'] ?? null,
            type: $validated['type'] ?? null
        );

        return $this->successResponse($report);
    }

    public function accountSummary(AccountSummaryRequest $request)
    {
        $validated = $request->validated();

        $summary = $this->service->accountSummary(
            type: $validated['type'] ?? null,
            state: $validated['state'] ?? null,
            minBalance: $validated['min_balance'] ?? null,
            maxBalance: $validated['max_balance'] ?? null,
            openedFrom: $validated['opened_from'] ?? null,
            openedTo: $validated['opened_to'] ?? null,
        );

        return $this->successResponse($summary);
    }

    public function auditLogs(AuditLogRequest $request)
    {
        $validated = $request->validated();

        $logs = $this->service->auditLogs($validated);

        return $this->successResponse($logs);
    }
}

