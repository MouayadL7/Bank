<?php

namespace Modules\Customer\Services;

use Illuminate\Http\Resources\Json\AnonymousResourceCollection;
use Modules\Customer\DTOs\SupportTicketDTO;
use Modules\Customer\Http\Resources\SupportTicketResource;
use Modules\Customer\Models\SupportTicket;
use Modules\Customer\Repositories\Eloquent\SupportTicketRepository;

class SupportTicketService
{
    public function __construct(
        protected SupportTicketRepository $repository
    ) {}

    public function getAllTickets(): AnonymousResourceCollection
    {
        $tickets = $this->repository->getAllTickets();
        return SupportTicketResource::collection($tickets);
    }

    public function create(SupportTicketDTO $dto): SupportTicketResource
    {
        $ticket = $this->repository->create($dto->toArray());

        return new SupportTicketResource($ticket);
    }

    public function update(SupportTicket $ticket, SupportTicketDTO $dto): SupportTicketResource
    {
        $ticket = $this->repository->update($ticket, $dto->toArray());

        return new SupportTicketResource($ticket);
    }
}
