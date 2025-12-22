<?php

namespace Modules\User\Repositories\Interfaces;

use Modules\User\Models\User;

interface UserRepositoryInterface
{
    public function create(array $data): User;
    public function list(array $filters);
    public function findByUuid(string $uuid): User;
    public function findByEmail(string $email): ?User;
    public function save(User $user): User;
}
