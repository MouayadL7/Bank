<?php

namespace Modules\User\Http\Controllers;

use Modules\Core\Http\Controllers\BaseController;
use Modules\User\Http\Requests\UserListRequest;
use Modules\User\Http\Requests\UserSearchRequest;
use Modules\User\Services\UserService;
use OpenApi\Attributes as OA;

class UserController extends BaseController
{
    public function __construct(private UserService $userService) {}

    #[OA\Get(
        path: '/users',
        summary: 'List users',
        description: 'Get a paginated list of users with optional filters',
        tags: ['Users'],
        security: [['sanctum' => []]],
        parameters: [
            new OA\Parameter(
                name: 'role',
                in: 'query',
                required: false,
                description: 'Filter by role name',
                schema: new OA\Schema(type: 'string')
            ),
            new OA\Parameter(
                name: 'status',
                in: 'query',
                required: false,
                description: 'Filter by user status',
                schema: new OA\Schema(type: 'string', enum: ['active', 'suspended', 'disabled'])
            ),
            new OA\Parameter(
                name: 'per_page',
                in: 'query',
                required: false,
                description: 'Number of items per page',
                schema: new OA\Schema(type: 'integer', minimum: 1, maximum: 100, default: 15)
            ),
        ],
        responses: [
            new OA\Response(
                response: 200,
                description: 'List of users',
                content: new OA\JsonContent(
                    properties: [
                        new OA\Property(property: 'success', type: 'boolean', example: true),
                        new OA\Property(property: 'data', type: 'object'),
                        new OA\Property(property: 'status_code', type: 'integer', example: 200),
                    ]
                )
            ),
            new OA\Response(response: 401, description: 'Unauthenticated'),
            new OA\Response(response: 403, description: 'Forbidden - Admin access required'),
        ]
    )]
    public function index(UserListRequest $request)
    {
        $users = $this->userService->list($request->validated());

        return $this->paginatedResponse($users);
    }

    #[OA\Get(
        path: '/users/search',
        summary: 'Search users',
        description: 'Search users by query string with optional filters',
        tags: ['Users'],
        security: [['sanctum' => []]],
        parameters: [
            new OA\Parameter(
                name: 'q',
                in: 'query',
                required: true,
                description: 'Search query (2-100 characters)',
                schema: new OA\Schema(type: 'string', minLength: 2, maxLength: 100)
            ),
            new OA\Parameter(
                name: 'role',
                in: 'query',
                required: false,
                description: 'Filter by role name',
                schema: new OA\Schema(type: 'string')
            ),
            new OA\Parameter(
                name: 'status',
                in: 'query',
                required: false,
                description: 'Filter by user status',
                schema: new OA\Schema(type: 'string', enum: ['active', 'suspended', 'disabled'])
            ),
            new OA\Parameter(
                name: 'per_page',
                in: 'query',
                required: false,
                description: 'Number of items per page',
                schema: new OA\Schema(type: 'integer', minimum: 1, maximum: 100, default: 15)
            ),
        ],
        responses: [
            new OA\Response(
                response: 200,
                description: 'Search results',
                content: new OA\JsonContent(
                    properties: [
                        new OA\Property(property: 'success', type: 'boolean', example: true),
                        new OA\Property(property: 'data', type: 'object'),
                        new OA\Property(property: 'status_code', type: 'integer', example: 200),
                    ]
                )
            ),
            new OA\Response(response: 401, description: 'Unauthenticated'),
            new OA\Response(response: 403, description: 'Forbidden - Admin access required'),
            new OA\Response(response: 422, description: 'Validation error'),
        ]
    )]
    public function search(UserSearchRequest $request)
    {
        $users = $this->userService->search($request->validated());

        return $this->paginatedResponse($users);
    }

    #[OA\Get(
        path: '/users/{uuid}',
        summary: 'Get user by UUID',
        description: 'Retrieve a specific user by their UUID',
        tags: ['Users'],
        security: [['sanctum' => []]],
        parameters: [
            new OA\Parameter(
                name: 'uuid',
                in: 'path',
                required: true,
                description: 'User UUID',
                schema: new OA\Schema(type: 'string', format: 'uuid')
            ),
        ],
        responses: [
            new OA\Response(
                response: 200,
                description: 'User details',
                content: new OA\JsonContent(
                    properties: [
                        new OA\Property(property: 'success', type: 'boolean', example: true),
                        new OA\Property(property: 'data', type: 'object'),
                        new OA\Property(property: 'status_code', type: 'integer', example: 200),
                    ]
                )
            ),
            new OA\Response(response: 401, description: 'Unauthenticated'),
            new OA\Response(response: 403, description: 'Forbidden - Admin access required'),
            new OA\Response(response: 404, description: 'User not found'),
        ]
    )]
    public function show(string $uuid)
    {
        $user = $this->userService->getByUuid($uuid);

        return $this->successResponse($user);
    }

    #[OA\Get(
        path: '/users/{uuid}/suspend',
        summary: 'Suspend a user',
        description: 'Suspend a user account',
        tags: ['Users'],
        security: [['sanctum' => []]],
        parameters: [
            new OA\Parameter(
                name: 'uuid',
                in: 'path',
                required: true,
                description: 'User UUID',
                schema: new OA\Schema(type: 'string', format: 'uuid')
            ),
        ],
        responses: [
            new OA\Response(
                response: 200,
                description: 'User suspended successfully',
                content: new OA\JsonContent(
                    properties: [
                        new OA\Property(property: 'success', type: 'boolean', example: true),
                        new OA\Property(property: 'data', type: 'object'),
                        new OA\Property(property: 'status_code', type: 'integer', example: 200),
                    ]
                )
            ),
            new OA\Response(response: 401, description: 'Unauthenticated'),
            new OA\Response(response: 403, description: 'Forbidden - Admin access required'),
            new OA\Response(response: 404, description: 'User not found'),
        ]
    )]
    public function suspend(string $uuid)
    {
        $user = $this->userService->suspend($uuid);

        return $this->successResponse($user);
    }

    #[OA\Get(
        path: '/users/{uuid}/activate',
        summary: 'Activate a user',
        description: 'Activate a suspended or disabled user account',
        tags: ['Users'],
        security: [['sanctum' => []]],
        parameters: [
            new OA\Parameter(
                name: 'uuid',
                in: 'path',
                required: true,
                description: 'User UUID',
                schema: new OA\Schema(type: 'string', format: 'uuid')
            ),
        ],
        responses: [
            new OA\Response(
                response: 200,
                description: 'User activated successfully',
                content: new OA\JsonContent(
                    properties: [
                        new OA\Property(property: 'success', type: 'boolean', example: true),
                        new OA\Property(property: 'data', type: 'object'),
                        new OA\Property(property: 'status_code', type: 'integer', example: 200),
                    ]
                )
            ),
            new OA\Response(response: 401, description: 'Unauthenticated'),
            new OA\Response(response: 403, description: 'Forbidden - Admin access required'),
            new OA\Response(response: 404, description: 'User not found'),
        ]
    )]
    public function activate(string $uuid)
    {
        $user = $this->userService->activate($uuid);

        return $this->successResponse($user);
    }
}
