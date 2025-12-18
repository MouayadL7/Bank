<?php

namespace Modules\Customer\Http\Controllers;

use Modules\Core\Http\Controllers\BaseController;
use Modules\Customer\Http\Requests\StoreSupportTicketRequest;
use Modules\Customer\Http\Requests\UpdateSupportTicketRequest;
use Modules\Customer\Services\SupportTicketService;
use Modules\Customer\Models\SupportTicket;

class SupportTicketController extends BaseController
{
    protected SupportTicketService $service;

    public function __construct(SupportTicketService $service)
    {
        $this->service = $service;
    }

    public function store(StoreSupportTicketRequest $request)
    {
        $ticketResource = $this->service->create($request->toDTO());
        return $this->successResponse($ticketResource);
    }

    public function update(UpdateSupportTicketRequest $request, int $id)
    {
        $ticket = SupportTicket::findOrFail($id);
        $ticketResource = $this->service->update($ticket, $request->toDTO());
        return $this->successResponse($ticketResource);
    }
}
