<?php

namespace Modules\AccessControl\Http\Controllers;

use Modules\Core\Http\Controllers\BaseController;
use Illuminate\Http\Request;
use Modules\AccessControl\Http\Requests\StoreRoleRequest;
use Modules\AccessControl\Services\RoleService;
use OpenApi\Attributes as OA;

class RoleController extends BaseController
{
    public function __construct(private RoleService $service) {}

    #[OA\Get(
        path: '/roles',
        summary: 'List all roles',
        description: 'Retrieve a list of all available roles',
        tags: ['Roles'],
        security: [['sanctum' => []]],
        responses: [
            new OA\Response(
                response: 200,
                description: 'List of roles',
                content: new OA\JsonContent(
                    properties: [
                        new OA\Property(property: 'success', type: 'boolean', example: true),
                        new OA\Property(property: 'data', type: 'array', items: new OA\Items(type: 'object')),
                        new OA\Property(property: 'status_code', type: 'integer', example: 200),
                    ]
                )
            ),
            new OA\Response(response: 401, description: 'Unauthenticated'),
            new OA\Response(response: 403, description: 'Forbidden - Admin access required'),
        ]
    )]
    public function index()
    {
        $roles = $this->service->getAll();

        return $this->successResponse($roles);
    }

    #[OA\Post(
        path: '/roles',
        summary: 'Create a new role',
        description: 'Create a new role in the system',
        tags: ['Roles'],
        security: [['sanctum' => []]],
        requestBody: new OA\RequestBody(
            required: true,
            content: new OA\JsonContent(
                required: ['name'],
                properties: [
                    new OA\Property(property: 'name', type: 'string', example: 'manager', description: 'Role name (must be unique)'),
                ]
            )
        ),
        responses: [
            new OA\Response(
                response: 200,
                description: 'Role created successfully',
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
            new OA\Response(response: 422, description: 'Validation error - Role name already exists'),
        ]
    )]
    public function store(StoreRoleRequest $request)
    {
        $role = $this->service->createRole($request->toDTO());

        return $this->successResponse($role);
    }
}
