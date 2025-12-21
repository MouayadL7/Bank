<?php

namespace Modules\Transaction\Http\Controllers;

use Modules\Core\Http\Controllers\BaseController;
use Modules\Transaction\Facades\TransactionFacade;
use Modules\Transaction\Http\Requests\DepositRequest;
use Modules\Transaction\Http\Requests\TransferRequest;
use Modules\Transaction\Http\Requests\WithdrawRequest;
use Modules\Transaction\Models\Transaction;

class TransactionController extends BaseController
{
    public function __construct(private TransactionFacade $facade) {}

    public function deposit(DepositRequest $request, $uuid)
    {
        $transaction = $this->facade->deposit($uuid, (float)$request->input('amount'));

        return $this->successResponse($transaction);
    }

    public function withdraw(WithdrawRequest $request, $uuid)
    {
        $transaction = $this->facade->withdraw($uuid, (float)$request->input('amount'));

        return $this->successResponse($transaction);
    }

    public function transfer(TransferRequest $request, $fromUuid, $toUuid)
    {
        $transaction = $this->facade->transfer($fromUuid, $toUuid, (float)$request->input('amount'));

        return $this->successResponse($transaction);
    }

    public function approve(int $id)
    {
        $transaction = Transaction::findOrFail($id);
        $approved = $this->facade->approve($transaction);

        if ($approved) {
            return $this->successResponse($transaction, 'Transaction approved successfully.');
        }

        return $this->errorResponse('Transaction could not be approved.');
    }

    public function processScheduled()
    {
        $this->facade->processScheduled();
        return $this->successResponse([], 'Scheduled transactions processed.');
    }
}
