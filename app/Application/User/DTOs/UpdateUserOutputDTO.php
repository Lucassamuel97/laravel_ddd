<?php

namespace App\Application\User\DTOs;

class UpdateUserOutputDTO
{
    public function __construct(
        public readonly string $id,
        public readonly string $name,
        public readonly string $email,
        public readonly string $role,
    ) {}
}
