<?php

namespace App\Domain\User\ValueObjects;

use App\Domain\User\Exceptions\InvalidPasswordException;

class Password
{
    private function __construct(
        private readonly string $hashedValue,
    ) {}

    public static function fromPlainText(string $plainText): self
    {
        if (strlen($plainText) < 6) {
            throw new InvalidPasswordException('Password must be at least 6 characters.');
        }

        return new self(password_hash($plainText, PASSWORD_BCRYPT));
    }

    public static function fromHash(string $hash): self
    {
        return new self($hash);
    }

    public function getHash(): string
    {
        return $this->hashedValue;
    }

    public function verify(string $plainText): bool
    {
        return password_verify($plainText, $this->hashedValue);
    }
}
