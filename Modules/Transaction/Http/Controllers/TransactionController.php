<?php

namespace App\Modules\Transaction\Controllers;

use App\Modules\Transaction\Services\TransactionService;
use Modules\Core\Http\Controllers\BaseController;
use Modules\Transaction\Http\Requests\StoreTransactionRequest;
use Modules\Transaction\Http\Requests\UpdateTransactionRequest;
use Modules\Transaction\Models\Transaction;

class TransactionController extends BaseController
{
    public function __construct(
        private TransactionService $service
    ) {}

    public function store(StoreTransactionRequest $request)
    {
        $account = $this->service->create($request->toDTO());

        return $this->successResponse($account);
    }

    public function update(UpdateTransactionRequest $request, int $id)
    {
        $transaction = Transaction::findOrFail($id);
        $updated = $this->service->update($transaction, $request->toDTO());

        return $this->successResponse($updated);
    }

    public function approve(int $id)
    {
        $transaction = Transaction::findOrFail($id);
        $approved = $this->service->approveTransaction($transaction);

        if ($approved) {
            return $this->successResponse($transaction, 'Transaction approved successfully.');
        }

        return $this->errorResponse('Transaction could not be approved.');
    }

}
