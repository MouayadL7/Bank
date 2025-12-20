<?php

namespace Modules\Account\Http\Controllers;

use Modules\Account\Http\Requests\ChangeAccountStateRequest;
use Modules\Account\Http\Requests\ChangeParentAccountRequest;
use Modules\Core\Http\Controllers\BaseController;
use Modules\Account\Http\Requests\CreateAccountRequest;
use Modules\Account\Http\Requests\UpdateAccountMetaRequest;
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

    public function changeState(ChangeAccountStateRequest $request, string $uuid)
    {
        $account = $this->service->changeState(
            $uuid,
            $request->validated()['state']
        );

        return $this->successResponse($account);
    }

    public function close(string $uuid)
    {
        $account = $this->service->close($uuid);

        return $this->successResponse($account);
    }

    public function updateMeta(UpdateAccountMetaRequest $request, string $uuid)
    {
        $account = $this->service->updateMeta(
            $uuid,
            $request->validated()['meta']
        );

        return $this->successResponse($account);
    }

    public function changeParent(ChangeParentAccountRequest $request, string $uuid)
    {
        $account = $this->service->changeParent(
            $uuid,
            $request->validated()['parent_uuid'] ?? null
        );

        return $this->successResponse($account);
    }
}
