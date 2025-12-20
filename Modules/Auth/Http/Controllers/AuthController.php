<?php

namespace Modules\Auth\Http\Controllers;

use Illuminate\Http\Request;
use Modules\Auth\Http\Requests\LoginRequest;
use Modules\Auth\Services\AuthService;
use Modules\Core\Http\Controllers\BaseController;

class AuthController extends BaseController
{
    public function __construct(
        private AuthService $authService
    ) {}

    public function login(LoginRequest $request)
    {
        $result = $this->authService->login(
            $request->email,
            $request->password
        );

        return $this->successResponse($result);
    }

    public function logout(Request $request)
    {
        $this->authService->logout(
            $request->user()
        );

        return $this->successResponse([]);
    }
}
