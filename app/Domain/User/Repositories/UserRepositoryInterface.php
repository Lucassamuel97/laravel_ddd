<?php

namespace App\Domain\User\Repositories;

use App\Domain\User\Entities\User;

interface UserRepositoryInterface
{
    public function create(User $user): User;

    public function findByEmail(string $email): ?User;

    /**
     * @return array<User>
     */
    public function getAll(): array;
}
