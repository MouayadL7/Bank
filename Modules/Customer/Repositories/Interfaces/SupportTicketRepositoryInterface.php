<?php

namespace Modules\Customer\Repositories\Interfaces;

use Modules\Customer\Models\SupportTicket;

interface SupportTicketRepositoryInterface
{
    public function create(array $data): SupportTicket;

    public function update(SupportTicket $SupportTicket, array $data): SupportTicket;
}
