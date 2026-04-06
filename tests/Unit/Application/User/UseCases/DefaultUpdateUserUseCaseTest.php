<?php

namespace Tests\Unit\Application\User\UseCases;

use App\Application\User\DTOs\UpdateUserInputDTO;
use App\Application\User\DTOs\UpdateUserOutputDTO;
use App\Application\User\UseCases\DefaultUpdateUserUseCase;
use App\Domain\User\Entities\User;
use App\Domain\User\Exceptions\DuplicateEmailException;
use App\Domain\User\Exceptions\UserNotFoundException;
use App\Domain\User\Repositories\UserRepositoryInterface;
use App\Domain\User\ValueObjects\Email;
use App\Domain\User\ValueObjects\Password;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;

class DefaultUpdateUserUseCaseTest extends TestCase
{
    private UserRepositoryInterface&MockObject $repository;
    private DefaultUpdateUserUseCase $useCase;

    protected function setUp(): void
    {
        parent::setUp();

        $this->repository = $this->createMock(UserRepositoryInterface::class);
        $this->useCase = new DefaultUpdateUserUseCase($this->repository);
    }

    public function test_execute_updates_user_successfully(): void
    {
        // Arrange
        $dto = new UpdateUserInputDTO(
            id: '10',
            name: 'John Updated',
            email: 'john.updated@example.com',
            password: 'new-secret-123',
            role: 'admin',
        );

        $currentUser = new User(
            id: '10',
            name: 'John Doe',
            email: new Email('john@example.com'),
            password: Password::fromHash('$2y$10$existinghash'),
            role: 'user',
        );

        $this->repository
            ->expects($this->once())
            ->method('findById')
            ->with('10')
            ->willReturn($currentUser);

        $this->repository
            ->expects($this->once())
            ->method('findByEmail')
            ->with('john.updated@example.com')
            ->willReturn(null);

        $this->repository
            ->expects($this->once())
            ->method('update')
            ->willReturnCallback(function (User $user) {
                return new User(
                    id: $user->getId(),
                    name: $user->getName(),
                    email: $user->getEmail(),
                    password: $user->getPassword(),
                    role: $user->getRole(),
                );
            });

        // Act
        $output = $this->useCase->execute($dto);

        // Assert
        $this->assertInstanceOf(UpdateUserOutputDTO::class, $output);
        $this->assertSame('10', $output->id);
        $this->assertSame('John Updated', $output->name);
        $this->assertSame('john.updated@example.com', $output->email);
        $this->assertSame('admin', $output->role);
    }

    public function test_execute_throws_exception_when_user_does_not_exist(): void
    {
        // Arrange
        $dto = new UpdateUserInputDTO(
            id: '999',
            name: 'Missing User',
            email: 'missing@example.com',
            password: 'secret123',
            role: 'user',
        );

        $this->repository
            ->expects($this->once())
            ->method('findById')
            ->with('999')
            ->willReturn(null);

        $this->repository->expects($this->never())->method('findByEmail');
        $this->repository->expects($this->never())->method('update');

        $this->expectException(UserNotFoundException::class);
        $this->expectExceptionMessage('User with id 999 was not found.');

        // Act
        $this->useCase->execute($dto);
    }

    public function test_execute_throws_exception_when_email_is_already_registered_by_another_user(): void
    {
        // Arrange
        $dto = new UpdateUserInputDTO(
            id: '10',
            name: 'John Updated',
            email: 'admin@example.com',
            password: 'secret123',
            role: 'user',
        );

        $currentUser = new User(
            id: '10',
            name: 'John Doe',
            email: new Email('john@example.com'),
            password: Password::fromHash('$2y$10$existinghash'),
            role: 'user',
        );

        $otherUser = new User(
            id: '20',
            name: 'Admin',
            email: new Email('admin@example.com'),
            password: Password::fromHash('$2y$10$otherhash'),
            role: 'admin',
        );

        $this->repository
            ->expects($this->once())
            ->method('findById')
            ->with('10')
            ->willReturn($currentUser);

        $this->repository
            ->expects($this->once())
            ->method('findByEmail')
            ->with('admin@example.com')
            ->willReturn($otherUser);

        $this->repository->expects($this->never())->method('update');

        $this->expectException(DuplicateEmailException::class);
        $this->expectExceptionMessage('The email admin@example.com is already registered.');

        // Act
        $this->useCase->execute($dto);
    }
}
