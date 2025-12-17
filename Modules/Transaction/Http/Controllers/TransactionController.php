<?php

namespace App\Modules\Transaction\Controllers;

use Modules\Core\Http\Controllers\BaseController;
use Modules\Transaction\Facades\TransactionFacade;
use Modules\Transaction\Http\Requests\StoreTransactionRequest;
use Modules\Transaction\Http\Requests\UpdateTransactionRequest;
use Modules\Transaction\Models\Transaction;

class TransactionController extends BaseController
{
    public function __construct(private TransactionFacade $facade) {}

    // إنشاء معاملة
    public function store(StoreTransactionRequest $request)
    {
        $transaction = $this->facade->createTransaction($request->toDTO());
        return $this->successResponse($transaction);
    }

    // تحديث معاملة
    public function update(UpdateTransactionRequest $request, int $id)
    {
        $transaction = Transaction::findOrFail($id);
        $updated = $this->facade->updateTransaction($transaction, $request->toDTO());

        return $this->successResponse($updated);
    }

    // الموافقة على المعاملة
    public function approve(int $id)
    {
        $transaction = Transaction::findOrFail($id);
        $approved = $this->facade->approve($transaction);

        if ($approved) {
            return $this->successResponse($transaction, 'Transaction approved successfully.');
        }

        return $this->errorResponse('Transaction could not be approved.');
    }

    // تنفيذ المعاملات المجدولة
    public function processScheduled()
    {
        $this->facade->processScheduled();
        return $this->successResponse([], 'Scheduled transactions processed.');
    }
}
