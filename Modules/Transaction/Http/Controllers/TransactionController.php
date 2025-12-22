<?php

namespace Modules\Transaction\Http\Controllers;

use Modules\Core\Http\Controllers\BaseController;
use Modules\Transaction\Facades\TransactionFacade;
use Modules\Transaction\Http\Requests\DepositRequest;
use Modules\Transaction\Http\Requests\TransferRequest;
use Modules\Transaction\Http\Requests\WithdrawRequest;
use Modules\Transaction\Models\Transaction;
use OpenApi\Attributes as OA;

class TransactionController extends BaseController
{
    public function __construct(private TransactionFacade $facade) {}

    #[OA\Post(
        path: '/{uuid}/transactions/deposit',
        summary: 'Deposit money to account',
        description: 'Deposit money into a bank account',
        tags: ['Transactions'],
        parameters: [
            new OA\Parameter(
                name: 'uuid',
                in: 'path',
                required: true,
                description: 'Account UUID',
                schema: new OA\Schema(type: 'string', format: 'uuid')
            ),
        ],
        requestBody: new OA\RequestBody(
            required: true,
            content: new OA\JsonContent(
                required: ['amount'],
                properties: [
                    new OA\Property(property: 'amount', type: 'number', format: 'float', minimum: 0.01, example: 100.50, description: 'Deposit amount (must be greater than 0.01)'),
                ]
            )
        ),
        responses: [
            new OA\Response(
                response: 200,
                description: 'Deposit successful',
                content: new OA\JsonContent(
                    properties: [
                        new OA\Property(property: 'success', type: 'boolean', example: true),
                        new OA\Property(property: 'data', type: 'object'),
                        new OA\Property(property: 'status_code', type: 'integer', example: 200),
                    ]
                )
            ),
            new OA\Response(response: 422, description: 'Validation error'),
            new OA\Response(response: 404, description: 'Account not found'),
        ]
    )]
    public function getPending()
    {
        $transactions = $this->facade->getPending();

        return $this->successResponse($transactions);
    }

    public function getAccountTransactions(string $uuid)
    {
        $transactions = $this->facade->getAccountTransactions($uuid);

        return $this->successResponse($transactions);
    }

    public function deposit(DepositRequest $request, $uuid)
    {
        $transaction = $this->facade->deposit($uuid, (float)$request->input('amount'));

        return $this->successResponse($transaction);
    }

    #[OA\Post(
        path: '/{uuid}/transactions/withdraw',
        summary: 'Withdraw money from account',
        description: 'Withdraw money from a bank account',
        tags: ['Transactions'],
        parameters: [
            new OA\Parameter(
                name: 'uuid',
                in: 'path',
                required: true,
                description: 'Account UUID',
                schema: new OA\Schema(type: 'string', format: 'uuid')
            ),
        ],
        requestBody: new OA\RequestBody(
            required: true,
            content: new OA\JsonContent(
                required: ['amount'],
                properties: [
                    new OA\Property(property: 'amount', type: 'number', format: 'float', minimum: 0.01, example: 50.25, description: 'Withdrawal amount (must be greater than 0.01)'),
                ]
            )
        ),
        responses: [
            new OA\Response(
                response: 200,
                description: 'Withdrawal successful',
                content: new OA\JsonContent(
                    properties: [
                        new OA\Property(property: 'success', type: 'boolean', example: true),
                        new OA\Property(property: 'data', type: 'object'),
                        new OA\Property(property: 'status_code', type: 'integer', example: 200),
                    ]
                )
            ),
            new OA\Response(response: 422, description: 'Validation error'),
            new OA\Response(response: 404, description: 'Account not found'),
        ]
    )]
    public function withdraw(WithdrawRequest $request, $uuid)
    {
        $transaction = $this->facade->withdraw($uuid, (float)$request->input('amount'));

        return $this->successResponse($transaction);
    }

    #[OA\Post(
        path: '/{fromUuid}/transactions/transfer/{toUuid}',
        summary: 'Transfer money between accounts',
        description: 'Transfer money from one account to another',
        tags: ['Transactions'],
        parameters: [
            new OA\Parameter(
                name: 'fromUuid',
                in: 'path',
                required: true,
                description: 'Source account UUID',
                schema: new OA\Schema(type: 'string', format: 'uuid')
            ),
            new OA\Parameter(
                name: 'toUuid',
                in: 'path',
                required: true,
                description: 'Destination account UUID',
                schema: new OA\Schema(type: 'string', format: 'uuid')
            ),
        ],
        requestBody: new OA\RequestBody(
            required: true,
            content: new OA\JsonContent(
                required: ['amount'],
                properties: [
                    new OA\Property(property: 'amount', type: 'number', format: 'float', minimum: 0.01, example: 75.00, description: 'Transfer amount (must be greater than 0.01)'),
                ]
            )
        ),
        responses: [
            new OA\Response(
                response: 200,
                description: 'Transfer successful',
                content: new OA\JsonContent(
                    properties: [
                        new OA\Property(property: 'success', type: 'boolean', example: true),
                        new OA\Property(property: 'data', type: 'object'),
                        new OA\Property(property: 'status_code', type: 'integer', example: 200),
                    ]
                )
            ),
            new OA\Response(response: 422, description: 'Validation error'),
            new OA\Response(response: 404, description: 'Account not found'),
        ]
    )]
    public function transfer(TransferRequest $request, $fromUuid, $toUuid)
    {
        $transaction = $this->facade->transfer($fromUuid, $toUuid, (float)$request->input('amount'));

        return $this->successResponse($transaction);
    }

    #[OA\Post(
        path: '/transactions/{id}/approve',
        summary: 'Approve a transaction',
        description: 'Approve a pending transaction',
        tags: ['Transactions'],
        parameters: [
            new OA\Parameter(
                name: 'id',
                in: 'path',
                required: true,
                description: 'Transaction ID',
                schema: new OA\Schema(type: 'integer')
            ),
        ],
        responses: [
            new OA\Response(
                response: 200,
                description: 'Transaction approved successfully',
                content: new OA\JsonContent(
                    properties: [
                        new OA\Property(property: 'success', type: 'boolean', example: true),
                        new OA\Property(property: 'data', type: 'object'),
                        new OA\Property(property: 'status_code', type: 'integer', example: 200),
                    ]
                )
            ),
            new OA\Response(response: 404, description: 'Transaction not found'),
        ]
    )]
    public function approve(string $uuid)
    {
        $transaction = $this->facade->approve($uuid);

        return $this->successResponse($transaction);
    }

    public function reject(string $uuid)
    {
        $transaction = $this->facade->reject($uuid);

        return $this->successResponse($transaction);
    }

    #[OA\Post(
        path: '/transactions/process-scheduled',
        summary: 'Process scheduled transactions',
        description: 'Process all scheduled transactions',
        tags: ['Transactions'],
        responses: [
            new OA\Response(
                response: 200,
                description: 'Scheduled transactions processed',
                content: new OA\JsonContent(
                    properties: [
                        new OA\Property(property: 'success', type: 'boolean', example: true),
                        new OA\Property(property: 'message', type: 'string', example: 'Scheduled transactions processed.'),
                        new OA\Property(property: 'status_code', type: 'integer', example: 200),
                    ]
                )
            ),
        ]
    )]
    public function processScheduled()
    {
        $this->facade->processScheduled();
        return $this->successResponse([], 'Scheduled transactions processed.');
    }
}
