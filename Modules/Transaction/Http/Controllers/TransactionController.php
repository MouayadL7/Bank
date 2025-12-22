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

    public function getPending()
    {
        $transactions = $this->facade->getPending();

        return $this->successResponse($transactions);
    }

    public function getAccountTransactions(string $uuid)
    {
        $transactions = $this->facade->getAccountTransactions($uuid);

        return $this->successResponse($transactions);
    }

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

    public function approve(string $uuid)
    {
        $transaction = $this->facade->approve($uuid);

        return $this->successResponse($transaction);
    }

    public function reject(string $uuid)
    {
        $transaction = $this->facade->reject($uuid);

        return $this->successResponse($transaction);
    }

    public function processScheduled()
    {
        $this->facade->processScheduled();
        return $this->successResponse([], 'Scheduled transactions processed.');
    }
}
