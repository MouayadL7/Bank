<?php

namespace App\Modules\Transaction\Controllers;

use App\Modules\Transaction\Services\TransactionService;
use Modules\Core\Http\Controllers\BaseController;
use Modules\Transaction\Http\Requests\StoreTransactionRequest;
use Modules\Transaction\Http\Requests\UpdateTransactionRequestRequest;

class TransactionController extends BaseController
{
    public function __construct(
        private TransactionService $service
    ) {}

    public function store(StoreTransactionRequest $request)
    {
        $account = $this->service->createTransaction($request->toDTO());

        return $this->successResponse($account);
    }

    public function update(UpdateTransactionRequestRequest $request, int $id)
    {
        $transaction = Transaction::findOrFail($id);
        $updated = $this->service->updateTransaction($transaction, $request->toDTO());

        return $this->successResponse($updated);
    }
}
