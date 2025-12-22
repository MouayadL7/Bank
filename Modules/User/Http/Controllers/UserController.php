<?php

namespace Modules\User\Http\Controllers;

use Modules\Core\Http\Controllers\BaseController;
use Modules\User\Http\Requests\UserListRequest;
use Modules\User\Http\Requests\UserSearchRequest;
use Modules\User\Services\UserService;

class UserController extends BaseController
{
    public function __construct(private UserService $userService) {}

    public function index(UserListRequest $request)
    {
        $users = $this->userService->list($request->validated());

        return $this->paginatedResponse($users);
    }

    public function search(UserSearchRequest $request)
    {
        $users = $this->userService->search($request->validated());

        return $this->paginatedResponse($users);
    }

    public function show(string $uuid)
    {
        $user = $this->userService->getByUuid($uuid);

        return $this->successResponse($user);
    }

    public function suspend(string $uuid)
    {
        $user = $this->userService->suspend($uuid);

        return $this->successResponse($user);
    }

    public function activate(string $uuid)
    {
        $user = $this->userService->activate($uuid);

        return $this->successResponse($user);
    }
}
