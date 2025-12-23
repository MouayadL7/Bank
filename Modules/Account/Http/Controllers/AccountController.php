<?php

namespace Modules\Account\Http\Controllers;

use Modules\Account\Http\Requests\ChangeAccountStateRequest;
use Modules\Account\Http\Requests\ChangeParentAccountRequest;
use Modules\Core\Http\Controllers\BaseController;
use Modules\Account\Http\Requests\CreateAccountRequest;
use Modules\Account\Http\Requests\UpdateAccountMetaRequest;
use Modules\Account\Services\AccountService;
use Modules\Account\Models\Account;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Facades\Auth;
use OpenApi\Attributes as OA;

class AccountController extends BaseController
{
    public function __construct(private AccountService $service) {}

    #[OA\Get(
        path: '/accounts',
        summary: 'List all accounts',
        description: 'Retrieve a list of all accounts',
        tags: ['Accounts'],
        responses: [
            new OA\Response(
                response: 200,
                description: 'List of accounts',
                content: new OA\JsonContent(
                    properties: [
                        new OA\Property(property: 'success', type: 'boolean', example: true),
                        new OA\Property(property: 'data', type: 'array', items: new OA\Items(type: 'object')),
                        new OA\Property(property: 'status_code', type: 'integer', example: 200),
                    ]
                )
            ),
        ]
    )]
    public function index()
    {
        // Customers should only see their own accounts
        if (Auth::check() && Gate::allows('isCustomer')) {
            $accounts = $this->service->getMyAccounts();
        } else {
            // Tellers, managers, and admins can see all accounts
            $accounts = $this->service->getAll();
        }

        return $this->successResponse($accounts);
    }

    #[OA\Post(
        path: '/accounts',
        summary: 'Create a new account',
        description: 'Create a new bank account',
        tags: ['Accounts'],
        requestBody: new OA\RequestBody(
            required: true,
            content: new OA\JsonContent(
                required: ['type'],
                properties: [
                    new OA\Property(property: 'customer_id', type: 'integer', nullable: true, example: 1, description: 'Customer user ID'),
                    new OA\Property(property: 'type', type: 'string', enum: ['savings', 'checking', 'loan', 'investment'], example: 'savings', description: 'Account type'),
                    new OA\Property(property: 'balance', type: 'number', format: 'float', nullable: true, minimum: 0, example: 1000.00, description: 'Initial balance'),
                    new OA\Property(property: 'currency', type: 'string', nullable: true, example: 'USD', description: 'Currency code'),
                    new OA\Property(property: 'parent_account_id', type: 'integer', nullable: true, example: 1, description: 'Parent account ID'),
                    new OA\Property(property: 'meta', type: 'object', nullable: true, description: 'Additional metadata'),
                ]
            )
        ),
        responses: [
            new OA\Response(
                response: 200,
                description: 'Account created successfully',
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
    public function store(CreateAccountRequest $request)
    {
        $account = $this->service->createAccount($request->toDTO());

        return $this->successResponse($account);
    }

    #[OA\Get(
        path: '/accounts/{uuid}',
        summary: 'Get account by UUID',
        description: 'Retrieve a specific account by its UUID',
        tags: ['Accounts'],
        parameters: [
            new OA\Parameter(
                name: 'uuid',
                in: 'path',
                required: true,
                description: 'Account UUID',
                schema: new OA\Schema(type: 'string', format: 'uuid')
            ),
        ],
        responses: [
            new OA\Response(
                response: 200,
                description: 'Account details',
                content: new OA\JsonContent(
                    properties: [
                        new OA\Property(property: 'success', type: 'boolean', example: true),
                        new OA\Property(property: 'data', type: 'object'),
                        new OA\Property(property: 'status_code', type: 'integer', example: 200),
                    ]
                )
            ),
            new OA\Response(response: 404, description: 'Account not found'),
        ]
    )]
    public function show(string $uuid)
    {
        // Get the account model first for authorization
        $account = Account::where('uuid', $uuid)->firstOrFail();

        // Authorize: customers can only view their own accounts
        $this->authorize('view', $account);

        // Get the resource after authorization
        $accountResource = $this->service->getByUuid($uuid);

        return $this->successResponse($accountResource);
    }
  
    public function getMyAccounts()
    {
        $accounts = $this->service->getMyAccounts();

        return $this->successResponse($accounts);
    }

  #[OA\Post(
        path: '/accounts/{uuid}/state',
        summary: 'Change account state',
        description: 'Update the state of an account (active, frozen, suspended, closed)',
        tags: ['Accounts'],
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
                required: ['state'],
                properties: [
                    new OA\Property(property: 'state', type: 'string', enum: ['active', 'frozen', 'suspended', 'closed'], example: 'active', description: 'New account state'),
                ]
            )
        ),
        responses: [
            new OA\Response(
                response: 200,
                description: 'Account state updated successfully',
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
    public function changeState(ChangeAccountStateRequest $request, string $uuid)
    {
        $account = $this->service->changeState(
            $uuid,
            $request->validated()['state']
        );

        return $this->successResponse($account);
    }

    #[OA\Post(
        path: '/accounts/{uuid}/close',
        summary: 'Close an account',
        description: 'Close a bank account',
        tags: ['Accounts'],
        parameters: [
            new OA\Parameter(
                name: 'uuid',
                in: 'path',
                required: true,
                description: 'Account UUID',
                schema: new OA\Schema(type: 'string', format: 'uuid')
            ),
        ],
        responses: [
            new OA\Response(
                response: 200,
                description: 'Account closed successfully',
                content: new OA\JsonContent(
                    properties: [
                        new OA\Property(property: 'success', type: 'boolean', example: true),
                        new OA\Property(property: 'data', type: 'object'),
                        new OA\Property(property: 'status_code', type: 'integer', example: 200),
                    ]
                )
            ),
            new OA\Response(response: 404, description: 'Account not found'),
        ]
    )]
    public function close(string $uuid)
    {
        $account = $this->service->close($uuid);

        return $this->successResponse($account);
    }

    #[OA\Patch(
        path: '/accounts/{uuid}/meta',
        summary: 'Update account metadata',
        description: 'Update the metadata of an account',
        tags: ['Accounts'],
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
                required: ['meta'],
                properties: [
                    new OA\Property(property: 'meta', type: 'object', example: ['key' => 'value'], description: 'Account metadata object'),
                ]
            )
        ),
        responses: [
            new OA\Response(
                response: 200,
                description: 'Account metadata updated successfully',
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
    public function updateMeta(UpdateAccountMetaRequest $request, string $uuid)
    {
        $account = $this->service->updateMeta(
            $uuid,
            $request->validated()['meta']
        );

        return $this->successResponse($account);
    }

    #[OA\Patch(
        path: '/accounts/{uuid}/parent',
        summary: 'Change account parent',
        description: 'Update the parent account of an account',
        tags: ['Accounts'],
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
                properties: [
                    new OA\Property(property: 'parent_uuid', type: 'string', format: 'uuid', nullable: true, example: '550e8400-e29b-41d4-a716-446655440000', description: 'Parent account UUID (null to remove parent)'),
                ]
            )
        ),
        responses: [
            new OA\Response(
                response: 200,
                description: 'Account parent updated successfully',
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
    public function changeParent(ChangeParentAccountRequest $request, string $uuid)
    {
        $account = $this->service->changeParent(
            $uuid,
            $request->validated()['parent_uuid'] ?? null
        );

        return $this->successResponse($account);
    }
}
