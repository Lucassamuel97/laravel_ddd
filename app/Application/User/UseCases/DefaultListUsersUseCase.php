<?php

namespace App\Application\User\UseCases;

use App\Application\User\DTOs\ListUserOutputDTO;
use App\Domain\Shared\Pagination\Pagination;
use App\Domain\Shared\Pagination\SearchQuery;
use App\Domain\User\Entities\User;
use App\Domain\User\Repositories\UserRepositoryInterface;

class DefaultListUsersUseCase extends ListUsersUseCase
{
    public function __construct(
        private readonly UserRepositoryInterface $userRepository,
    ) {}

    public function execute(SearchQuery $query): Pagination
    {
        return $this->userRepository
            ->findAll($query)
            ->map(fn (User $user) => ListUserOutputDTO::fromEntity($user));
    }
}
