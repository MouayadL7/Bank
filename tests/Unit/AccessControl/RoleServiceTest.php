<?php

namespace Tests\Unit\AccessControl;

use Illuminate\Pagination\LengthAwarePaginator;
use Mockery;
use Modules\AccessControl\DTOs\RoleData;
use Modules\AccessControl\Http\Resources\RoleResource;
use Modules\AccessControl\Models\Role;
use Modules\AccessControl\Repositories\Interfaces\RoleRepositoryInterface;
use Modules\AccessControl\Services\RoleService;
use Tests\TestCase;

class RoleServiceTest extends TestCase
{

    protected function tearDown(): void
    {
        Mockery::close();
        parent::tearDown();
    }

    public function test_get_all_returns_role_resource_collection(): void
    {
        // Arrange
        $collection = collect([]);

        $repo = Mockery::mock(RoleRepositoryInterface::class);
        $repo->shouldReceive('all')
            ->once()
            ->andReturn($collection);

        $service = new RoleService($repo);

        // Act
        $result = $service->getAll();

        // Assert
        $this->assertInstanceOf(\Illuminate\Http\Resources\Json\AnonymousResourceCollection::class, $result);
    }

    public function test_create_role_creates_new_role(): void
    {
        // Arrange
        $role = new Role();
        $role->id = 1;
        $role->name = 'test-role';

        $dto = Mockery::mock(RoleData::class);
        $dto->shouldReceive('toArray')
            ->once()
            ->andReturn(['name' => 'test-role', 'permissions' => []]);

        $repo = Mockery::mock(RoleRepositoryInterface::class);
        $repo->shouldReceive('create')
            ->once()
            ->andReturn($role);

        $service = new RoleService($repo);

        // Act
        $result = $service->createRole($dto);

        // Assert
        $this->assertInstanceOf(RoleResource::class, $result);
    }
}

