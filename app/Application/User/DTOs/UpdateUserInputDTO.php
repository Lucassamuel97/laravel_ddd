<?php

namespace App\Application\User\DTOs;

class UpdateUserInputDTO
{
    public function __construct(
        public readonly string $id,
        public readonly string $name,
        public readonly string $email,
        public readonly string $password,
        public readonly string $role = 'user',
    ) {}
}
