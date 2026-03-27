<?php

namespace Tests\Unit\Domain\User\ValueObjects;

use App\Domain\User\Exceptions\InvalidEmailException;
use App\Domain\User\ValueObjects\Email;
use PHPUnit\Framework\TestCase;

class EmailTest extends TestCase
{
    public function test_valid_email_is_accepted(): void
    {
        $email = new Email('test@example.com');
        $this->assertEquals('test@example.com', $email->getValue());
    }

    public function test_email_is_normalized_to_lowercase(): void
    {
        $email = new Email('TEST@EXAMPLE.COM');
        $this->assertEquals('test@example.com', $email->getValue());
    }

    public function test_invalid_email_throws_exception(): void
    {
        $this->expectException(InvalidEmailException::class);
        new Email('not-an-email');
    }

    public function test_empty_email_throws_exception(): void
    {
        $this->expectException(InvalidEmailException::class);
        new Email('');
    }

    public function test_two_equal_emails_are_equal(): void
    {
        $email1 = new Email('test@example.com');
        $email2 = new Email('TEST@example.com');
        $this->assertTrue($email1->equals($email2));
    }

    public function test_two_different_emails_are_not_equal(): void
    {
        $email1 = new Email('a@example.com');
        $email2 = new Email('b@example.com');
        $this->assertFalse($email1->equals($email2));
    }

    public function test_to_string_returns_value(): void
    {
        $email = new Email('user@domain.com');
        $this->assertEquals('user@domain.com', (string) $email);
    }
}
