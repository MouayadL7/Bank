<?php

namespace Tests\Unit\Auth;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Modules\Auth\Actions\TokenIssuerAction;
use Modules\User\Models\User;
use Tests\TestCase;

class TokenIssuerActionTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
    }

    public function test_issue_creates_new_token_for_user(): void
    {
        // Arrange
        $role = \Modules\AccessControl\Models\Role::create(['name' => 'customer']);
        $user = User::factory()->create(['role_id' => $role->id]);
        $tokenIssuer = new TokenIssuerAction();

        // Act
        $token = $tokenIssuer->issue($user);

        // Assert
        $this->assertIsString($token);
        $this->assertNotEmpty($token);
        $this->assertTrue($user->tokens()->count() === 1);
    }

    public function test_issue_deletes_existing_tokens_before_creating_new_one(): void
    {
        // Arrange
        $role = \Modules\AccessControl\Models\Role::create(['name' => 'customer']);
        $user = User::factory()->create(['role_id' => $role->id]);
        
        // Create existing tokens
        $user->createToken('api');
        $user->createToken('api');
        $this->assertEquals(2, $user->tokens()->count());

        $tokenIssuer = new TokenIssuerAction();

        // Act
        $token = $tokenIssuer->issue($user);

        // Assert
        $this->assertIsString($token);
        $this->assertNotEmpty($token);
        $this->assertEquals(1, $user->tokens()->count());
    }

    public function test_issue_returns_plain_text_token(): void
    {
        // Arrange
        $role = \Modules\AccessControl\Models\Role::create(['name' => 'customer']);
        $user = User::factory()->create(['role_id' => $role->id]);
        $tokenIssuer = new TokenIssuerAction();

        // Act
        $token = $tokenIssuer->issue($user);

        // Assert
        $this->assertIsString($token);
        // Sanctum tokens typically have format: {id}|{hash}
        $this->assertStringContainsString('|', $token);
    }
}

