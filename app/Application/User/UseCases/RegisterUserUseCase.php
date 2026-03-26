<?php

namespace App\Application\User\UseCases;

use App\Application\User\DTOs\RegisterUserInputDTO;
use App\Application\User\DTOs\RegisterUserOutputDTO;
use App\Domain\User\Entities\User;
use App\Domain\User\Exceptions\DuplicateEmailException;
use App\Domain\User\Repositories\UserRepositoryInterface;
use App\Domain\User\ValueObjects\Email;
use App\Domain\User\ValueObjects\Password;

class RegisterUserUseCase
{
    public function __construct(
        private readonly UserRepositoryInterface $userRepository,
    ) {}

    public function execute(RegisterUserInputDTO $dto): RegisterUserOutputDTO
    {
        $email = new Email($dto->email);

        $existing = $this->userRepository->findByEmail($email->getValue());

        if ($existing !== null) {
            throw new DuplicateEmailException(
                "The email {$email->getValue()} is already registered."
            );
        }

        $password = Password::fromPlainText($dto->password);

        $user = new User(
            id: null,
            name: $dto->name,
            email: $email,
            password: $password,
            role: $dto->role,
        );

        $created = $this->userRepository->create($user);

        return new RegisterUserOutputDTO(
            id: $created->getId(),
            name: $created->getName(),
            email: $created->getEmail()->getValue(),
            role: $created->getRole(),
        );
    }
}
