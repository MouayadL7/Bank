<?php

namespace Tests\Feature\Auth;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;
use Modules\AccessControl\Models\Role;
use Modules\User\Enums\UserStatus;
use Modules\User\Models\User;
use Tests\TestCase;

class AuthControllerTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
    }

    public function test_login_returns_token_and_user_when_credentials_are_valid(): void
    {
        // Arrange
        $password = 'password123';
        
        // Create a role first
        $role = \Modules\AccessControl\Models\Role::create(['name' => 'customer']);
        
        $user = User::factory()->create([
            'email' => 'test@example.com',
            'password' => Hash::make($password),
            'status' => UserStatus::ACTIVE->value,
            'role_id' => $role->id,
        ]);

        // Act
        $response = $this->postJson('/api/auth/login', [
            'email' => $user->email,
            'password' => $password,
        ]);

        // Assert
        $response->assertStatus(200)
            ->assertJsonStructure([
                'success',
                'data' => [
                    'user',
                    'token',
                ],
                'status_code',
                'timestamp',
            ])
            ->assertJson([
                'success' => true,
            ]);

        $this->assertNotEmpty($response->json('data.token'));
    }

    public function test_login_returns_validation_error_when_email_is_missing(): void
    {
        // Act
        $response = $this->postJson('/api/auth/login', [
            'password' => 'password123',
        ]);

        // Assert
        $response->assertStatus(422)
            ->assertJsonValidationErrors(['email']);
    }

    public function test_login_returns_validation_error_when_password_is_missing(): void
    {
        // Arrange - create a user so email exists
        $role = \Modules\AccessControl\Models\Role::create(['name' => 'customer']);
        User::factory()->create([
            'email' => 'test@example.com',
            'role_id' => $role->id,
        ]);

        // Act
        $response = $this->postJson('/api/auth/login', [
            'email' => 'test@example.com',
        ]);

        // Assert
        $response->assertStatus(422)
            ->assertJsonValidationErrors(['password']);
    }

    public function test_login_returns_validation_error_when_email_is_invalid(): void
    {
        // Act
        $response = $this->postJson('/api/auth/login', [
            'email' => 'invalid-email',
            'password' => 'password123',
        ]);

        // Assert
        $response->assertStatus(422)
            ->assertJsonValidationErrors(['email']);
    }

    public function test_login_returns_validation_error_when_password_is_too_short(): void
    {
        // Arrange - create a user so email exists
        $role = \Modules\AccessControl\Models\Role::create(['name' => 'customer']);
        User::factory()->create([
            'email' => 'test@example.com',
            'role_id' => $role->id,
        ]);

        // Act
        $response = $this->postJson('/api/auth/login', [
            'email' => 'test@example.com',
            'password' => 'short',
        ]);

        // Assert
        $response->assertStatus(422)
            ->assertJsonValidationErrors(['password']);
    }

    public function test_login_returns_validation_error_when_password_is_too_long(): void
    {
        // Arrange - create a user so email exists
        $role = \Modules\AccessControl\Models\Role::create(['name' => 'customer']);
        User::factory()->create([
            'email' => 'test@example.com',
            'role_id' => $role->id,
        ]);

        // Act
        $response = $this->postJson('/api/auth/login', [
            'email' => 'test@example.com',
            'password' => str_repeat('a', 21), // 21 characters
        ]);

        // Assert
        $response->assertStatus(422)
            ->assertJsonValidationErrors(['password']);
    }

    public function test_login_returns_error_when_credentials_are_invalid(): void
    {
        // Arrange
        $role = \Modules\AccessControl\Models\Role::create(['name' => 'customer']);
        
        $user = User::factory()->create([
            'email' => 'test@example.com',
            'password' => Hash::make('correctpassword'),
            'status' => UserStatus::ACTIVE->value,
            'role_id' => $role->id,
        ]);

        // Act
        $response = $this->postJson('/api/auth/login', [
            'email' => $user->email,
            'password' => 'wrongpassword',
        ]);

        // Assert
        $response->assertStatus(422)
            ->assertJsonValidationErrors(['email']);
    }

    public function test_login_returns_error_when_user_is_suspended(): void
    {
        // Arrange
        $password = 'password123';
        $role = \Modules\AccessControl\Models\Role::create(['name' => 'customer']);
        
        $user = User::factory()->create([
            'email' => 'test@example.com',
            'password' => Hash::make($password),
            'status' => UserStatus::SUSPENDED->value,
            'role_id' => $role->id,
        ]);

        // Act
        $response = $this->postJson('/api/auth/login', [
            'email' => $user->email,
            'password' => $password,
        ]);

        // Assert
        $response->assertStatus(403);
    }

    public function test_login_returns_error_when_user_is_disabled(): void
    {
        // Arrange
        $password = 'password123';
        $role = \Modules\AccessControl\Models\Role::create(['name' => 'customer']);
        
        $user = User::factory()->create([
            'email' => 'test@example.com',
            'password' => Hash::make($password),
            'status' => UserStatus::DISABLED->value,
            'role_id' => $role->id,
        ]);

        // Act
        $response = $this->postJson('/api/auth/login', [
            'email' => $user->email,
            'password' => $password,
        ]);

        // Assert
        $response->assertStatus(403);
    }

    public function test_logout_revokes_user_token(): void
    {
        // Arrange
        $role = \Modules\AccessControl\Models\Role::create(['name' => 'customer']);
        
        $user = User::factory()->create([
            'status' => UserStatus::ACTIVE->value,
            'role_id' => $role->id,
        ]);
        
        $token = $user->createToken('api')->plainTextToken;
        $this->assertEquals(1, $user->tokens()->count());

        // Act
        $response = $this->withHeader('Authorization', 'Bearer ' . $token)
            ->postJson('/api/auth/logout');

        // Assert
        $response->assertStatus(200)
            ->assertJson([
                'success' => true,
            ]);

        $this->assertEquals(0, $user->fresh()->tokens()->count());
    }

    public function test_logout_requires_authentication(): void
    {
        // Act
        $response = $this->postJson('/api/auth/logout');

        // Assert
        $response->assertStatus(401);
    }
}

