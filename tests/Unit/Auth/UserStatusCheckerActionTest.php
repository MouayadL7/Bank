<?php

namespace Tests\Unit\Auth;

use Illuminate\Auth\Access\AuthorizationException;
use Modules\Auth\Actions\UserStatusCheckerAction;
use Modules\User\Enums\UserStatus;
use Modules\User\Models\User;
use Tests\TestCase;

class UserStatusCheckerActionTest extends TestCase
{
    public function test_ensure_active_passes_when_user_is_active(): void
    {
        // Arrange
        $user = new User();
        $user->status = UserStatus::ACTIVE->value;

        $statusChecker = new UserStatusCheckerAction();

        // Act & Assert - should not throw exception
        $statusChecker->ensureActive($user);
        $this->assertTrue(true); // If we reach here, no exception was thrown
    }

    public function test_ensure_active_throws_exception_when_user_is_suspended(): void
    {
        // Arrange
        $user = new User();
        $user->status = UserStatus::SUSPENDED->value;

        $statusChecker = new UserStatusCheckerAction();

        // Assert
        $this->expectException(AuthorizationException::class);
        $this->expectExceptionMessage('User is not active');

        // Act
        $statusChecker->ensureActive($user);
    }

    public function test_ensure_active_throws_exception_when_user_is_disabled(): void
    {
        // Arrange
        $user = new User();
        $user->status = UserStatus::DISABLED->value;

        $statusChecker = new UserStatusCheckerAction();

        // Assert
        $this->expectException(AuthorizationException::class);
        $this->expectExceptionMessage('User is not active');

        // Act
        $statusChecker->ensureActive($user);
    }
}

