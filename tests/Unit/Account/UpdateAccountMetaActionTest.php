<?php

namespace Tests\Unit\Account;

use Mockery;
use Modules\Account\Actions\UpdateAccountMetaAction;
use Modules\Account\Models\Account;
use Tests\TestCase;

class UpdateAccountMetaActionTest extends TestCase
{
    protected function tearDown(): void
    {
        Mockery::close();
        parent::tearDown();
    }

    public function test_execute_updates_account_metadata(): void
    {
        // Arrange
        $account = new Account();
        $account->meta = ['old_key' => 'old_value'];
        $newMeta = ['new_key' => 'new_value', 'old_key' => 'updated_value'];

        $action = new UpdateAccountMetaAction();

        // Act
        $action->execute($account, $newMeta);

        // Assert
        $this->assertEquals($newMeta, $account->meta);
    }

    public function test_execute_merges_metadata_when_appropriate(): void
    {
        // Arrange
        $account = new Account();
        $account->meta = ['existing_key' => 'existing_value'];
        $newMeta = ['new_key' => 'new_value'];

        $action = new UpdateAccountMetaAction();

        // Act
        $action->execute($account, $newMeta);

        // Assert
        $this->assertArrayHasKey('new_key', $account->meta);
        $this->assertEquals('new_value', $account->meta['new_key']);
    }

    public function test_execute_throws_exception_when_account_is_closed(): void
    {
        // Arrange
        $account = Mockery::mock(Account::class)->makePartial();
        $account->shouldAllowMockingProtectedMethods();
        $account->state = 'closed';
        $newMeta = ['key' => 'value'];

        $action = new UpdateAccountMetaAction();

        // Assert
        $this->expectException(\DomainException::class);
        $this->expectExceptionMessage('Cannot modify a closed account.');

        // Act
        $action->execute($account, $newMeta);
    }
}

