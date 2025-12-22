<?php

namespace Tests\Unit\Auth;

use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\ValidationException;
use Mockery;
use Modules\Auth\Actions\AuthenticatorAction;
use Modules\User\Models\User;
use Modules\User\Repositories\Interfaces\UserRepositoryInterface;
use Tests\TestCase;

class AuthenticatorActionTest extends TestCase
{

    protected function tearDown(): void
    {
        Mockery::close();
        parent::tearDown();
    }

    public function test_attempt_returns_user_when_credentials_are_valid(): void
    {
        // Arrange
        $email = 'test@example.com';
        $password = 'password123';
        $hashedPassword = Hash::make($password);

        $user = new User();
        $user->email = $email;
        $user->password = $hashedPassword;

        $userRepository = Mockery::mock(UserRepositoryInterface::class);
        $userRepository->shouldReceive('findByEmail')
            ->once()
            ->with($email)
            ->andReturn($user);

        $authenticator = new AuthenticatorAction($userRepository);

        // Act
        $result = $authenticator->attempt($email, $password);

        // Assert
        $this->assertInstanceOf(User::class, $result);
        $this->assertEquals($email, $result->email);
    }

    public function test_attempt_throws_validation_exception_when_password_is_invalid(): void
    {
        // Arrange
        $email = 'test@example.com';
        $correctPassword = 'password123';
        $wrongPassword = 'wrongpassword';
        $hashedPassword = Hash::make($correctPassword);

        $user = new User();
        $user->email = $email;
        $user->password = $hashedPassword;

        $userRepository = Mockery::mock(UserRepositoryInterface::class);
        $userRepository->shouldReceive('findByEmail')
            ->once()
            ->with($email)
            ->andReturn($user);

        $authenticator = new AuthenticatorAction($userRepository);

        // Assert
        $this->expectException(ValidationException::class);
        // The actual message is "The provided credentials are incorrect."
        $this->expectExceptionMessage('The provided credentials are incorrect.');

        // Act
        $authenticator->attempt($email, $wrongPassword);
    }

    public function test_attempt_throws_exception_when_user_not_found(): void
    {
        // Arrange
        $email = 'nonexistent@example.com';
        $password = 'password123';

        $userRepository = Mockery::mock(UserRepositoryInterface::class);
        $userRepository->shouldReceive('findByEmail')
            ->once()
            ->with($email)
            ->andThrow(new \Illuminate\Database\Eloquent\ModelNotFoundException());

        $authenticator = new AuthenticatorAction($userRepository);

        // Assert
        $this->expectException(\Illuminate\Database\Eloquent\ModelNotFoundException::class);

        // Act
        $authenticator->attempt($email, $password);
    }
}

