<?php

namespace Tests\Unit\Application\User\UseCases;

use App\Application\User\DTOs\GetUserByIdOutputDTO;
use App\Application\User\UseCases\DefaultGetUserByIdUseCase;
use App\Domain\User\Entities\User;
use App\Domain\User\Exceptions\UserNotFoundException;
use App\Domain\User\Repositories\UserRepositoryInterface;
use App\Domain\User\ValueObjects\Email;
use App\Domain\User\ValueObjects\Password;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;

class DefaultGetUserByIdUseCaseTest extends TestCase
{
    private UserRepositoryInterface&MockObject $repository;
    private DefaultGetUserByIdUseCase $useCase;

    protected function setUp(): void
    {
        parent::setUp();

        $this->repository = $this->createMock(UserRepositoryInterface::class);
        $this->useCase = new DefaultGetUserByIdUseCase($this->repository);
    }

    public function test_execute_returns_user_data_when_user_exists(): void
    {
        // Arrange
        $userId = '10';

        $user = new User(
            id: $userId,
            name: 'John Doe',
            email: new Email('john@example.com'),
            password: Password::fromHash('$2y$10$somehash'),
            role: 'admin',
        );

        $this->repository
            ->expects($this->once())
            ->method('findById')
            ->with($userId)
            ->willReturn($user);

        // Act
        $output = $this->useCase->execute($userId);

        // Assert
        $this->assertInstanceOf(GetUserByIdOutputDTO::class, $output);
        $this->assertSame($userId, $output->id);
        $this->assertSame('John Doe', $output->name);
        $this->assertSame('john@example.com', $output->email);
        $this->assertSame('admin', $output->role);
    }

    public function test_execute_throws_exception_when_user_does_not_exist(): void
    {
        // Arrange
        $userId = '999';

        $this->repository
            ->expects($this->once())
            ->method('findById')
            ->with($userId)
            ->willReturn(null);

        $this->expectException(UserNotFoundException::class);
        $this->expectExceptionMessage('User with id 999 was not found.');

        // Act
        $this->useCase->execute($userId);
    }
}
