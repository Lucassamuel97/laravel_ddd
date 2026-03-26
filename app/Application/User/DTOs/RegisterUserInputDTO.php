<?php

namespace App\Application\User\DTOs;

class RegisterUserInputDTO
{
    public function __construct(
        public readonly string $name,
        public readonly string $email,
        public readonly string $password,
        public readonly string $role = 'user',
    ) {}
}
