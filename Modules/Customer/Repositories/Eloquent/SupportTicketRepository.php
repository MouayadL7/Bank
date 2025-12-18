<?php

namespace Modules\Customer\Repositories\Eloquent;

use Modules\Customer\Repositories\Interfaces\SupportTicketRepositoryInterface;
use Modules\Customer\Models\SupportTicket;

class SupportTicketRepository implements SupportTicketRepositoryInterface
{
    protected $model;

    public function getAllTickets()
    {
        return SupportTicket::select('id', 'customer_id', 'title', 'description', 'status', 'created_at', 'updated_at')
            ->with(['customer']) 
            ->get();
    }

    public function create(array $data): SupportTicket
    {
        return SupportTicket::create($data);
    }

    public function update(SupportTicket $ticket, array $data): SupportTicket
    {
        $ticket->update($data);
        return $ticket;
    }
}
