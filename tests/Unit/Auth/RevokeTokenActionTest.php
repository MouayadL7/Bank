<?php

namespace Tests\Unit\Auth;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Modules\Auth\Actions\RevokeTokenAction;
use Modules\User\Models\User;
use Tests\TestCase;

class RevokeTokenActionTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
    }

    public function test_revoke_current_deletes_all_user_tokens(): void
    {
        // Arrange
        $role = \Modules\AccessControl\Models\Role::create(['name' => 'customer']);
        $user = User::factory()->create(['role_id' => $role->id]);
        $user->createToken('api');
        $user->createToken('mobile');
        $this->assertEquals(2, $user->tokens()->count());

        $revokeToken = new RevokeTokenAction();

        // Act
        $revokeToken->revokeCurrent($user);

        // Assert
        $this->assertEquals(0, $user->tokens()->count());
    }

    public function test_revoke_current_handles_user_with_no_tokens(): void
    {
        // Arrange
        $role = \Modules\AccessControl\Models\Role::create(['name' => 'customer']);
        $user = User::factory()->create(['role_id' => $role->id]);
        $this->assertEquals(0, $user->tokens()->count());

        $revokeToken = new RevokeTokenAction();

        // Act
        $revokeToken->revokeCurrent($user);

        // Assert
        $this->assertEquals(0, $user->tokens()->count());
    }
}

