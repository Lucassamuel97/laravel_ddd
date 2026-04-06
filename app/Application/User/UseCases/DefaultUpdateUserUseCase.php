<?php

namespace App\Application\User\UseCases;

use App\Application\User\DTOs\UpdateUserInputDTO;
use App\Application\User\DTOs\UpdateUserOutputDTO;
use App\Domain\User\Entities\User;
use App\Domain\User\Exceptions\DuplicateEmailException;
use App\Domain\User\Exceptions\UserNotFoundException;
use App\Domain\User\Repositories\UserRepositoryInterface;
use App\Domain\User\ValueObjects\Email;
use App\Domain\User\ValueObjects\Password;

class DefaultUpdateUserUseCase extends UpdateUserUseCase
{
    public function __construct(
        private readonly UserRepositoryInterface $userRepository,
    ) {}

    public function execute(UpdateUserInputDTO $dto): UpdateUserOutputDTO
    {
        $existingUser = $this->userRepository->findById($dto->id);

        if ($existingUser === null) {
            throw new UserNotFoundException("User with id {$dto->id} was not found.");
        }

        $email = new Email($dto->email);
        $userWithEmail = $this->userRepository->findByEmail($email->getValue());

        if ($userWithEmail !== null && $userWithEmail->getId() !== $dto->id) {
            throw new DuplicateEmailException(
                "The email {$email->getValue()} is already registered."
            );
        }

        $updatedUser = new User(
            id: $dto->id,
            name: $dto->name,
            email: $email,
            password: Password::fromPlainText($dto->password),
            role: $dto->role,
        );

        $saved = $this->userRepository->update($updatedUser);

        return new UpdateUserOutputDTO(
            id: $saved->getId(),
            name: $saved->getName(),
            email: $saved->getEmail()->getValue(),
            role: $saved->getRole(),
        );
    }
}
