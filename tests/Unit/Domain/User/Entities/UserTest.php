<?php

namespace Tests\Unit\Domain\User\Entities;

use App\Domain\User\Entities\User;
use App\Domain\User\ValueObjects\Email;
use App\Domain\User\ValueObjects\Password;
use PHPUnit\Framework\TestCase;

class UserTest extends TestCase
{
    public function test_can_create_user_with_all_fields(): void
    {
        $email    = new Email('john@example.com');
        $password = Password::fromPlainText('secret123');

        $user = new User(
            id: '1',
            name: 'John Doe',
            email: $email,
            password: $password,
            role: 'admin',
        );

        $this->assertEquals('1', $user->getId());
        $this->assertEquals('John Doe', $user->getName());
        $this->assertSame($email, $user->getEmail());
        $this->assertSame($password, $user->getPassword());
        $this->assertEquals('admin', $user->getRole());
    }

    public function test_user_default_role_is_user(): void
    {
        $user = new User(
            id: null,
            name: 'Jane',
            email: new Email('jane@example.com'),
            password: Password::fromPlainText('password123'),
        );

        $this->assertEquals('user', $user->getRole());
        $this->assertNull($user->getId());
    }
}
