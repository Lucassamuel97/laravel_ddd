<?php

namespace App\Application\User\UseCases;

use App\Application\User\DTOs\GetUserByIdOutputDTO;
use App\Domain\User\Exceptions\UserNotFoundException;
use App\Domain\User\Repositories\UserRepositoryInterface;

class DefaultGetUserByIdUseCase extends GetUserByIdUseCase
{
    public function __construct(
        private readonly UserRepositoryInterface $userRepository,
    ) {}

    public function execute(string $id): GetUserByIdOutputDTO
    {
        $user = $this->userRepository->findById($id);

        if ($user === null) {
            throw new UserNotFoundException("User with id {$id} was not found.");
        }

        return GetUserByIdOutputDTO::fromEntity($user);
    }
}
