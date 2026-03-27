<?php

namespace App\Domain\User\Entities;

use App\Domain\User\ValueObjects\Email;
use App\Domain\User\ValueObjects\Password;

class User
{
    public function __construct(
        private readonly ?string $id,
        private readonly string $name,
        private readonly Email $email,
        private readonly Password $password,
        private readonly string $role = 'user',
    ) {}

    public function getId(): ?string
    {
        return $this->id;
    }

    public function getName(): string
    {
        return $this->name;
    }

    public function getEmail(): Email
    {
        return $this->email;
    }

    public function getPassword(): Password
    {
        return $this->password;
    }

    public function getRole(): string
    {
        return $this->role;
    }
}
