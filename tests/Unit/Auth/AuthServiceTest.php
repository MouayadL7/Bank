<?php

namespace Tests\Unit\Auth;

use Illuminate\Auth\Access\AuthorizationException;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Validation\ValidationException;
use Mockery;
use Modules\Auth\Actions\AuthenticatorAction;
use Modules\Auth\Actions\RevokeTokenAction;
use Modules\Auth\Actions\TokenIssuerAction;
use Modules\Auth\Actions\UserStatusCheckerAction;
use Modules\Auth\Services\AuthService;
use Modules\User\Http\Resources\UserResource;
use Modules\User\Models\User;
use Modules\User\Repositories\Interfaces\UserRepositoryInterface;
use Tests\TestCase;

class AuthServiceTest extends TestCase
{
    use RefreshDatabase;

    protected function tearDown(): void
    {
        Mockery::close();
        parent::tearDown();
    }

    public function test_login_returns_user_and_token_when_credentials_are_valid(): void
    {
        // Arrange
        $email = 'test@example.com';
        $password = 'password123';
        
        $user = new User();
        $user->id = 1;
        $user->email = $email;
        $user->status = 'active';

        $token = '1|test-token-123';

        $authenticator = Mockery::mock(AuthenticatorAction::class);
        $authenticator->shouldReceive('attempt')
            ->once()
            ->with($email, $password)
            ->andReturn($user);

        $statusChecker = Mockery::mock(UserStatusCheckerAction::class);
        $statusChecker->shouldReceive('ensureActive')
            ->once()
            ->with($user);

        $tokenIssuer = Mockery::mock(TokenIssuerAction::class);
        $tokenIssuer->shouldReceive('issue')
            ->once()
            ->with($user)
            ->andReturn($token);

        $revokeToken = Mockery::mock(RevokeTokenAction::class);

        $authService = new AuthService(
            $authenticator,
            $statusChecker,
            $tokenIssuer,
            $revokeToken
        );

        // Act
        $result = $authService->login($email, $password);

        // Assert
        $this->assertIsArray($result);
        $this->assertArrayHasKey('user', $result);
        $this->assertArrayHasKey('token', $result);
        $this->assertInstanceOf(UserResource::class, $result['user']);
        $this->assertEquals($token, $result['token']);
    }

    public function test_login_throws_exception_when_credentials_are_invalid(): void
    {
        // Arrange
        $email = 'test@example.com';
        $password = 'wrongpassword';

        $authenticator = Mockery::mock(AuthenticatorAction::class);
        $authenticator->shouldReceive('attempt')
            ->once()
            ->with($email, $password)
            ->andThrow(ValidationException::withMessages(['password' => 'Invalid credentials.']));

        $statusChecker = Mockery::mock(UserStatusCheckerAction::class);
        $tokenIssuer = Mockery::mock(TokenIssuerAction::class);
        $revokeToken = Mockery::mock(RevokeTokenAction::class);

        $authService = new AuthService(
            $authenticator,
            $statusChecker,
            $tokenIssuer,
            $revokeToken
        );

        // Assert
        $this->expectException(ValidationException::class);

        // Act
        $authService->login($email, $password);
    }

    public function test_login_throws_exception_when_user_is_not_active(): void
    {
        // Arrange
        $email = 'test@example.com';
        $password = 'password123';
        
        $user = new User();
        $user->id = 1;
        $user->email = $email;
        $user->status = 'suspended';

        $authenticator = Mockery::mock(AuthenticatorAction::class);
        $authenticator->shouldReceive('attempt')
            ->once()
            ->with($email, $password)
            ->andReturn($user);

        $statusChecker = Mockery::mock(UserStatusCheckerAction::class);
        $statusChecker->shouldReceive('ensureActive')
            ->once()
            ->with($user)
            ->andThrow(new AuthorizationException('User is not active'));

        $tokenIssuer = Mockery::mock(TokenIssuerAction::class);
        $revokeToken = Mockery::mock(RevokeTokenAction::class);

        $authService = new AuthService(
            $authenticator,
            $statusChecker,
            $tokenIssuer,
            $revokeToken
        );

        // Assert
        $this->expectException(AuthorizationException::class);
        $this->expectExceptionMessage('User is not active');

        // Act
        $authService->login($email, $password);
    }

    public function test_logout_revokes_user_tokens(): void
    {
        // Arrange
        $user = new User();
        $user->id = 1;

        $authenticator = Mockery::mock(AuthenticatorAction::class);
        $statusChecker = Mockery::mock(UserStatusCheckerAction::class);
        $tokenIssuer = Mockery::mock(TokenIssuerAction::class);

        $revokeToken = Mockery::mock(RevokeTokenAction::class);
        $revokeToken->shouldReceive('revokeCurrent')
            ->once()
            ->with($user);

        $authService = new AuthService(
            $authenticator,
            $statusChecker,
            $tokenIssuer,
            $revokeToken
        );

        // Act
        $authService->logout($user);

        // Assert - if we reach here without exception, the method executed successfully
        $this->assertTrue(true);
    }
}

