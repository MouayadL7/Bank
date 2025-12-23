<?php

namespace Tests\Unit\User;

use Illuminate\Pagination\LengthAwarePaginator;
use Mockery;
use Modules\User\DTOs\UserData;
use Modules\User\Enums\UserStatus;
use Modules\User\Http\Resources\UserResource;
use Modules\User\Models\User;
use Modules\User\Repositories\Interfaces\UserRepositoryInterface;
use Modules\User\Services\UserService;
use Tests\TestCase;

class UserServiceTest extends TestCase
{

    protected function tearDown(): void
    {
        Mockery::close();
        parent::tearDown();
    }

    public function test_create_returns_existing_user_if_email_exists(): void
    {
        // Arrange
        $email = 'test@example.com';
        $existingUser = new User();
        $existingUser->id = 1;
        $existingUser->email = $email;

        $dto = Mockery::mock(UserData::class);
        $dto->email = $email;

        $repo = Mockery::mock(UserRepositoryInterface::class);
        $repo->shouldReceive('findByEmail')
            ->once()
            ->with($email)
            ->andReturn($existingUser);

        $service = new UserService($repo);

        // Act
        $result = $service->create($dto);

        // Assert
        $this->assertEquals($existingUser, $result);
    }

    public function test_create_creates_new_user_when_email_not_exists(): void
    {
        // Arrange
        $email = 'new@example.com';
        $newUser = new User();
        $newUser->id = 1;
        $newUser->email = $email;

        $dto = Mockery::mock(UserData::class);
        $dto->email = $email;
        $dto->shouldReceive('toArray')
            ->once()
            ->andReturn(['email' => $email, 'name' => 'Test User']);

        $repo = Mockery::mock(UserRepositoryInterface::class);
        $repo->shouldReceive('findByEmail')
            ->once()
            ->with($email)
            ->andReturn(null);
        $repo->shouldReceive('create')
            ->once()
            ->andReturn($newUser);

        $service = new UserService($repo);

        // Act
        $result = $service->create($dto);

        // Assert
        $this->assertEquals($newUser, $result);
    }

    public function test_list_returns_user_resource_collection(): void
    {
        // Arrange
        $filters = ['role' => 'customer'];
        $paginator = new LengthAwarePaginator([], 0, 15);

        $repo = Mockery::mock(UserRepositoryInterface::class);
        $repo->shouldReceive('list')
            ->once()
            ->with($filters)
            ->andReturn($paginator);

        $service = new UserService($repo);

        // Act
        $result = $service->list($filters);

        // Assert - UserService::list() returns ->resource which is a LengthAwarePaginator
        $this->assertInstanceOf(LengthAwarePaginator::class, $result);
    }

    public function test_search_returns_user_resource_collection(): void
    {
        // Arrange
        $filters = ['q' => 'test'];
        $paginator = new LengthAwarePaginator([], 0, 15);

        $repo = Mockery::mock(UserRepositoryInterface::class);
        $repo->shouldReceive('list')
            ->once()
            ->with($filters)
            ->andReturn($paginator);

        $service = new UserService($repo);

        // Act
        $result = $service->search($filters);

        // Assert - UserService::search() returns ->resource which is a LengthAwarePaginator
        $this->assertInstanceOf(LengthAwarePaginator::class, $result);
    }

    public function test_get_by_uuid_returns_user_resource(): void
    {
        // Arrange
        $uuid = 'test-uuid';
        $user = new User();
        $user->uuid = $uuid;

        $repo = Mockery::mock(UserRepositoryInterface::class);
        $repo->shouldReceive('findByUuid')
            ->once()
            ->with($uuid, true)
            ->andReturn($user);

        $service = new UserService($repo);

        // Act
        $result = $service->getByUuid($uuid);

        // Assert
        $this->assertInstanceOf(UserResource::class, $result);
    }

    public function test_suspend_suspends_user(): void
    {
        // Arrange
        $uuid = 'test-uuid';
        $user = new User();
        $user->uuid = $uuid;
        $user->status = UserStatus::ACTIVE;

        $repo = Mockery::mock(UserRepositoryInterface::class);
        $repo->shouldReceive('findByUuid')
            ->once()
            ->with($uuid)
            ->andReturn($user);
        $repo->shouldReceive('save')
            ->once()
            ->with(Mockery::on(function ($u) {
                return $u->status === UserStatus::SUSPENDED;
            }))
            ->andReturn($user);

        $service = new UserService($repo);

        // Act
        $result = $service->suspend($uuid);

        // Assert
        $this->assertInstanceOf(UserResource::class, $result);
        $this->assertEquals(UserStatus::SUSPENDED, $user->status);
    }

    public function test_activate_activates_user(): void
    {
        // Arrange
        $uuid = 'test-uuid';
        $user = new User();
        $user->uuid = $uuid;
        $user->status = UserStatus::SUSPENDED;

        $repo = Mockery::mock(UserRepositoryInterface::class);
        $repo->shouldReceive('findByUuid')
            ->once()
            ->with($uuid)
            ->andReturn($user);
        $repo->shouldReceive('save')
            ->once()
            ->with(Mockery::on(function ($u) {
                return $u->status === UserStatus::ACTIVE;
            }))
            ->andReturn($user);

        $service = new UserService($repo);

        // Act
        $result = $service->activate($uuid);

        // Assert
        $this->assertInstanceOf(UserResource::class, $result);
        $this->assertEquals(UserStatus::ACTIVE, $user->status);
    }
}

