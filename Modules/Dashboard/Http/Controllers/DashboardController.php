<?php

namespace Modules\Dashboard\Http\Controllers;

use Modules\Core\Http\Controllers\BaseController;
use Modules\Dashboard\Services\DashboardService;
use OpenApi\Attributes as OA;

class DashboardController extends BaseController
{
    public function __construct(private DashboardService $service) {}

    #[OA\Get(
        path: '/dashboard/statistics',
        summary: 'Get system statistics',
        description: 'Retrieve comprehensive system statistics including users, accounts, and transactions',
        tags: ['Dashboard'],
        security: [['sanctum' => []]],
        responses: [
            new OA\Response(
                response: 200,
                description: 'System statistics',
                content: new OA\JsonContent(
                    properties: [
                        new OA\Property(property: 'success', type: 'boolean', example: true),
                        new OA\Property(property: 'data', type: 'object'),
                        new OA\Property(property: 'status_code', type: 'integer', example: 200),
                    ]
                )
            ),
            new OA\Response(response: 401, description: 'Unauthenticated'),
        ]
    )]
    public function statistics()
    {
        $statistics = $this->service->getStatistics();
        return $this->successResponse($statistics);
    }

    #[OA\Get(
        path: '/dashboard/health',
        summary: 'Get system health status',
        description: 'Check system health including database, cache, and queue status',
        tags: ['Dashboard'],
        security: [['sanctum' => []]],
        responses: [
            new OA\Response(
                response: 200,
                description: 'System health status',
                content: new OA\JsonContent(
                    properties: [
                        new OA\Property(property: 'success', type: 'boolean', example: true),
                        new OA\Property(property: 'data', type: 'object'),
                        new OA\Property(property: 'status_code', type: 'integer', example: 200),
                    ]
                )
            ),
            new OA\Response(response: 401, description: 'Unauthenticated'),
        ]
    )]
    public function health()
    {
        $health = $this->service->getSystemHealth();
        return $this->successResponse($health);
    }

    #[OA\Get(
        path: '/dashboard/performance',
        summary: 'Get performance metrics',
        description: 'Retrieve system performance metrics including response times and transaction volumes',
        tags: ['Dashboard'],
        security: [['sanctum' => []]],
        responses: [
            new OA\Response(
                response: 200,
                description: 'Performance metrics',
                content: new OA\JsonContent(
                    properties: [
                        new OA\Property(property: 'success', type: 'boolean', example: true),
                        new OA\Property(property: 'data', type: 'object'),
                        new OA\Property(property: 'status_code', type: 'integer', example: 200),
                    ]
                )
            ),
            new OA\Response(response: 401, description: 'Unauthenticated'),
        ]
    )]
    public function performance()
    {
        $metrics = $this->service->getPerformanceMetrics();
        return $this->successResponse($metrics);
    }

    #[OA\Get(
        path: '/dashboard/activity',
        summary: 'Get activity monitoring data',
        description: 'Retrieve recent system activity including transactions and account changes',
        tags: ['Dashboard'],
        security: [['sanctum' => []]],
        parameters: [
            new OA\Parameter(
                name: 'limit',
                in: 'query',
                required: false,
                description: 'Number of recent activities to retrieve',
                schema: new OA\Schema(type: 'integer', minimum: 1, maximum: 100, default: 50)
            ),
        ],
        responses: [
            new OA\Response(
                response: 200,
                description: 'Activity monitoring data',
                content: new OA\JsonContent(
                    properties: [
                        new OA\Property(property: 'success', type: 'boolean', example: true),
                        new OA\Property(property: 'data', type: 'object'),
                        new OA\Property(property: 'status_code', type: 'integer', example: 200),
                    ]
                )
            ),
            new OA\Response(response: 401, description: 'Unauthenticated'),
        ]
    )]
    public function activity()
    {
        $limit = (int) request()->get('limit', 50);
        $activity = $this->service->getActivityMonitoring($limit);
        return $this->successResponse($activity);
    }

    #[OA\Get(
        path: '/dashboard/financial',
        summary: 'Get financial overview',
        description: 'Retrieve financial overview including total assets, transaction summaries, and account distribution',
        tags: ['Dashboard'],
        security: [['sanctum' => []]],
        responses: [
            new OA\Response(
                response: 200,
                description: 'Financial overview',
                content: new OA\JsonContent(
                    properties: [
                        new OA\Property(property: 'success', type: 'boolean', example: true),
                        new OA\Property(property: 'data', type: 'object'),
                        new OA\Property(property: 'status_code', type: 'integer', example: 200),
                    ]
                )
            ),
            new OA\Response(response: 401, description: 'Unauthenticated'),
        ]
    )]
    public function financial()
    {
        $overview = $this->service->getFinancialOverview();
        return $this->successResponse($overview);
    }
}

