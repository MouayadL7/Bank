<?php

namespace Modules\Customer\DTOs;

use Modules\Customer\Enums\SupportTicketStatus;
use DateTimeInterface;

class SupportTicketDTO
{
    public function __construct(
        public int $customer_id,
        public string $title,
        public string $description,
        public SupportTicketStatus $status = SupportTicketStatus::OPEN,
        public ?DateTimeInterface $created_at = null,
    ) {}

    public static function fromArray(array $data): self
    {
        return new self(
            customer_id: $data['customer_id'],
            title:       $data['title'],
            description: $data['description'],
            status:      isset($data['status'])? SupportTicketStatus::from($data['status']): SupportTicketStatus::OPEN,
            created_at:  isset($data['created_at'])? new \DateTime($data['created_at']): null,
        );
    }

    public function toArray(): array
    {
        return [
            'customer_id' => $this->customer_id,
            'title'       => $this->title,
            'description' => $this->description,
            'status'      => $this->status->value,
        ];
    }
}
