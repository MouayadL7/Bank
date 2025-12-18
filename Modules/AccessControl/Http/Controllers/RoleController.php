<?php

namespace Modules\AccessControl\Http\Controllers;

use Modules\Core\Http\Controllers\BaseController;
use Illuminate\Http\Request;
use Modules\AccessControl\Http\Requests\StoreRoleRequest;
use Modules\AccessControl\Services\RoleService;

class RoleController extends BaseController
{
    public function __construct(private RoleService $service) {}

    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $roles = $this->service->getAll();

        return $this->successResponse($roles);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(StoreRoleRequest $request)
    {
        $role = $this->service->createRole($request->toDTO());

        return $this->successResponse($role);
    }
}
