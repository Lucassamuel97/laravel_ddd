<?php

namespace App\Domain\User\ValueObjects;

use App\Domain\User\Exceptions\InvalidEmailException;

class Email
{
    private readonly string $value;

    public function __construct(string $value)
    {
        if (!filter_var($value, FILTER_VALIDATE_EMAIL)) {
            throw new InvalidEmailException("Invalid email: {$value}");
        }

        $this->value = strtolower(trim($value));
    }

    public function getValue(): string
    {
        return $this->value;
    }

    public function equals(Email $other): bool
    {
        return $this->value === $other->value;
    }

    public function __toString(): string
    {
        return $this->value;
    }
}
