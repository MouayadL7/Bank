<?php

namespace Modules\User\Repositories\Interfaces;

use Modules\User\Models\User;

interface UserRepositoryInterface
{
    public function list(array $filters);
    public function findByUuid(string $uuid): User;
    public function save(User $user): User;
}
