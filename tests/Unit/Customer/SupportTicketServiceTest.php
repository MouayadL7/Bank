<?php

namespace Tests\Unit\Customer;

use Illuminate\Pagination\LengthAwarePaginator;
use Mockery;
use Modules\Customer\DTOs\SupportTicketDTO;
use Modules\Customer\Http\Resources\SupportTicketResource;
use Modules\Customer\Models\SupportTicket;
use Modules\Customer\Repositories\Eloquent\SupportTicketRepository;
use Modules\Customer\Services\SupportTicketService;
use Tests\TestCase;

class SupportTicketServiceTest extends TestCase
{

    protected function tearDown(): void
    {
        Mockery::close();
        parent::tearDown();
    }

    public function test_get_all_tickets_returns_resource_collection(): void
    {
        // Arrange
        $paginator = new LengthAwarePaginator([], 0, 15);

        $repository = Mockery::mock(SupportTicketRepository::class);
        $repository->shouldReceive('getAllTickets')
            ->once()
            ->andReturn($paginator);

        $service = new SupportTicketService($repository);

        // Act
        $result = $service->getAllTickets();

        // Assert
        $this->assertInstanceOf(\Illuminate\Http\Resources\Json\AnonymousResourceCollection::class, $result);
    }

    public function test_create_creates_ticket(): void
    {
        // Arrange
        $ticket = new SupportTicket();
        $ticket->id = 1;

        $dto = Mockery::mock(SupportTicketDTO::class);
        $dto->shouldReceive('toArray')
            ->once()
            ->andReturn(['subject' => 'Test', 'message' => 'Test message']);

        $repository = Mockery::mock(SupportTicketRepository::class);
        $repository->shouldReceive('create')
            ->once()
            ->andReturn($ticket);

        $service = new SupportTicketService($repository);

        // Act
        $result = $service->create($dto);

        // Assert
        $this->assertInstanceOf(SupportTicketResource::class, $result);
    }

    public function test_update_updates_ticket(): void
    {
        // Arrange
        $ticket = new SupportTicket();
        $ticket->id = 1;

        $dto = Mockery::mock(SupportTicketDTO::class);
        $dto->shouldReceive('toArray')
            ->once()
            ->andReturn(['subject' => 'Updated', 'message' => 'Updated message']);

        $repository = Mockery::mock(SupportTicketRepository::class);
        $repository->shouldReceive('update')
            ->once()
            ->with($ticket, Mockery::any())
            ->andReturn($ticket);

        $service = new SupportTicketService($repository);

        // Act
        $result = $service->update($ticket, $dto);

        // Assert
        $this->assertInstanceOf(SupportTicketResource::class, $result);
    }
}

