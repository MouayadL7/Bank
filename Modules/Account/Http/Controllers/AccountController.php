<?php

namespace Modules\Account\Http\Controllers;

use Modules\Core\Http\Controllers\BaseController;
use Modules\Account\Http\Requests\CreateAccountRequest;
use Modules\Account\Http\Requests\DepositRequest;
use Modules\Account\Http\Requests\WithdrawRequest;
use Modules\Account\Services\AccountService;

class AccountController extends BaseController
{
    public function __construct(private AccountService $service) {}

    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $accounts = $this->service->getAll();

        return $this->successResponse($accounts);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(CreateAccountRequest $request)
    {
        $account = $this->service->createAccount($request->toDTO());

        return $this->successResponse($account);
    }

    public function show(string $uuid)
    {
        $account = $this->service->getByUuid($uuid);
        
        return $this->successResponse($account);
    }

    public function deposit(DepositRequest $request, $uuid)
    {
        $account = $this->service->deposit($uuid, (float)$request->input('amount'));

        return $this->successResponse($account);
    }

    public function withdraw(WithdrawRequest $request, $uuid)
    {
        $account = $this->service->withdraw($uuid, (float)$request->input('amount'));

        return $this->successResponse($account);
    }
}
