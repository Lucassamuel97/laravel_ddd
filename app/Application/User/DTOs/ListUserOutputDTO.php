<?php

namespace App\Application\User\DTOs;

use App\Domain\User\Entities\User;

class ListUserOutputDTO
{
    public function __construct(
        public readonly string $id,
        public readonly string $name,
        public readonly string $email,
        public readonly string $role,
    ) {}

    public static function fromEntity(User $user): self
    {
        return new self(
            id: (string) $user->getId(),
            name: $user->getName(),
            email: $user->getEmail()->getValue(),
            role: $user->getRole(),
        );
    }
}
