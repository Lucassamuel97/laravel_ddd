<?php

namespace App\Domain\User\Repositories;

use App\Domain\Shared\Pagination\Pagination;
use App\Domain\Shared\Pagination\SearchQuery;
use App\Domain\User\Entities\User;

interface UserRepositoryInterface
{
    public function create(User $user): User;

    public function update(User $user): User;

    public function findById(string $id): ?User;

    public function findByEmail(string $email): ?User;

    /**
     * @return array<User>
     */
    public function getAll(): array;

    public function findAll(SearchQuery $query): Pagination;
}
