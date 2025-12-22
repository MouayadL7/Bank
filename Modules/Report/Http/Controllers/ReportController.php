<?php

namespace Modules\Report\Http\Controllers;

use Carbon\Carbon;
use Modules\Core\Http\Controllers\BaseController;
use Modules\Report\Http\Requests\AccountSummaryRequest;
use Modules\Report\Http\Requests\AuditLogRequest;
use Modules\Report\Http\Requests\TransactionReportRequest;
use Modules\Report\Services\ReportService;
use OpenApi\Attributes as OA;

class ReportController extends BaseController
{
    public function __construct(private ReportService $service) {}

    #[OA\Get(
        path: '/reports/transactions/daily',
        summary: 'Get daily transactions report',
        description: 'Generate a report of daily transactions with optional filters',
        tags: ['Reports'],
        parameters: [
            new OA\Parameter(
                name: 'from_date',
                in: 'query',
                required: false,
                description: 'Start date (YYYY-MM-DD)',
                schema: new OA\Schema(type: 'string', format: 'date')
            ),
            new OA\Parameter(
                name: 'to_date',
                in: 'query',
                required: false,
                description: 'End date (YYYY-MM-DD), must be after or equal to from_date',
                schema: new OA\Schema(type: 'string', format: 'date')
            ),
            new OA\Parameter(
                name: 'status',
                in: 'query',
                required: false,
                description: 'Filter by transaction status',
                schema: new OA\Schema(type: 'string', enum: ['pending', 'approved', 'rejected'])
            ),
            new OA\Parameter(
                name: 'type',
                in: 'query',
                required: false,
                description: 'Filter by transaction type',
                schema: new OA\Schema(type: 'string', enum: ['deposit', 'withdrawal', 'transfer'])
            ),
        ],
        responses: [
            new OA\Response(
                response: 200,
                description: 'Daily transactions report',
                content: new OA\JsonContent(
                    properties: [
                        new OA\Property(property: 'success', type: 'boolean', example: true),
                        new OA\Property(property: 'data', type: 'object'),
                        new OA\Property(property: 'status_code', type: 'integer', example: 200),
                    ]
                )
            ),
            new OA\Response(response: 422, description: 'Validation error'),
        ]
    )]
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

    #[OA\Get(
        path: '/reports/accounts/summary',
        summary: 'Get account summary report',
        description: 'Generate a summary report of accounts with optional filters',
        tags: ['Reports'],
        parameters: [
            new OA\Parameter(
                name: 'type',
                in: 'query',
                required: false,
                description: 'Filter by account type',
                schema: new OA\Schema(type: 'string', enum: ['savings', 'checking', 'loan', 'investment'])
            ),
            new OA\Parameter(
                name: 'state',
                in: 'query',
                required: false,
                description: 'Filter by account state',
                schema: new OA\Schema(type: 'string', enum: ['active', 'frozen', 'suspended', 'closed'])
            ),
            new OA\Parameter(
                name: 'min_balance',
                in: 'query',
                required: false,
                description: 'Minimum account balance',
                schema: new OA\Schema(type: 'number', format: 'float')
            ),
            new OA\Parameter(
                name: 'max_balance',
                in: 'query',
                required: false,
                description: 'Maximum account balance (must be >= min_balance)',
                schema: new OA\Schema(type: 'number', format: 'float')
            ),
            new OA\Parameter(
                name: 'opened_from',
                in: 'query',
                required: false,
                description: 'Accounts opened from date (YYYY-MM-DD)',
                schema: new OA\Schema(type: 'string', format: 'date')
            ),
            new OA\Parameter(
                name: 'opened_to',
                in: 'query',
                required: false,
                description: 'Accounts opened to date (YYYY-MM-DD), must be after or equal to opened_from',
                schema: new OA\Schema(type: 'string', format: 'date')
            ),
        ],
        responses: [
            new OA\Response(
                response: 200,
                description: 'Account summary report',
                content: new OA\JsonContent(
                    properties: [
                        new OA\Property(property: 'success', type: 'boolean', example: true),
                        new OA\Property(property: 'data', type: 'object'),
                        new OA\Property(property: 'status_code', type: 'integer', example: 200),
                    ]
                )
            ),
            new OA\Response(response: 422, description: 'Validation error'),
        ]
    )]
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

    #[OA\Get(
        path: '/reports/audit-logs',
        summary: 'Get audit logs',
        description: 'Retrieve audit logs with optional filters',
        tags: ['Reports'],
        parameters: [
            new OA\Parameter(
                name: 'event',
                in: 'query',
                required: false,
                description: 'Filter by event name',
                schema: new OA\Schema(type: 'string', maxLength: 150)
            ),
            new OA\Parameter(
                name: 'subject_type',
                in: 'query',
                required: false,
                description: 'Filter by subject type',
                schema: new OA\Schema(type: 'string', maxLength: 150)
            ),
            new OA\Parameter(
                name: 'subject_id',
                in: 'query',
                required: false,
                description: 'Filter by subject ID',
                schema: new OA\Schema(type: 'integer')
            ),
            new OA\Parameter(
                name: 'from_date',
                in: 'query',
                required: false,
                description: 'Start date (YYYY-MM-DD)',
                schema: new OA\Schema(type: 'string', format: 'date')
            ),
            new OA\Parameter(
                name: 'to_date',
                in: 'query',
                required: false,
                description: 'End date (YYYY-MM-DD), must be after or equal to from_date',
                schema: new OA\Schema(type: 'string', format: 'date')
            ),
            new OA\Parameter(
                name: 'limit',
                in: 'query',
                required: false,
                description: 'Maximum number of logs to return',
                schema: new OA\Schema(type: 'integer', minimum: 1, maximum: 500)
            ),
        ],
        responses: [
            new OA\Response(
                response: 200,
                description: 'Audit logs',
                content: new OA\JsonContent(
                    properties: [
                        new OA\Property(property: 'success', type: 'boolean', example: true),
                        new OA\Property(property: 'data', type: 'array', items: new OA\Items(type: 'object')),
                        new OA\Property(property: 'status_code', type: 'integer', example: 200),
                    ]
                )
            ),
            new OA\Response(response: 422, description: 'Validation error'),
        ]
    )]
    public function auditLogs(AuditLogRequest $request)
    {
        $validated = $request->validated();

        $logs = $this->service->auditLogs($validated);

        return $this->successResponse($logs);
    }
}

