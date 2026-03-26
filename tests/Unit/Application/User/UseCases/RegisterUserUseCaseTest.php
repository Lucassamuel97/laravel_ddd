<?php

namespace Tests\Unit\Application\User\UseCases;

use App\Application\User\DTOs\RegisterUserInputDTO;
use App\Application\User\UseCases\RegisterUserUseCase;
use App\Domain\User\Entities\User;
use App\Domain\User\Exceptions\DuplicateEmailException;
use App\Domain\User\Repositories\UserRepositoryInterface;
use App\Domain\User\ValueObjects\Email;
use App\Domain\User\ValueObjects\Password;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;

class RegisterUserUseCaseTest extends TestCase
{
    private UserRepositoryInterface&MockObject $repository;
    private RegisterUserUseCase $useCase;

    protected function setUp(): void
    {
        parent::setUp();
        $this->repository = $this->createMock(UserRepositoryInterface::class);
        $this->useCase    = new RegisterUserUseCase($this->repository);
    }

    public function test_register_user_successfully(): void
    {
        $dto = new RegisterUserInputDTO(
            name: 'John Doe',
            email: 'john@example.com',
            password: 'secret123',
            role: 'user',
        );

        $this->repository
            ->expects($this->once())
            ->method('findByEmail')
            ->with('john@example.com')
            ->willReturn(null);

        $this->repository
            ->expects($this->once())
            ->method('create')
            ->willReturnCallback(function (User $user) {
                return new User(
                    id: '42',
                    name: $user->getName(),
                    email: $user->getEmail(),
                    password: $user->getPassword(),
                    role: $user->getRole(),
                );
            });

        $output = $this->useCase->execute($dto);

        $this->assertEquals('42', $output->id);
        $this->assertEquals('John Doe', $output->name);
        $this->assertEquals('john@example.com', $output->email);
        $this->assertEquals('user', $output->role);
    }

    public function test_throws_duplicate_email_exception(): void
    {
        $this->expectException(DuplicateEmailException::class);

        $dto = new RegisterUserInputDTO(
            name: 'John Doe',
            email: 'john@example.com',
            password: 'secret123',
        );

        $existingUser = new User(
            id: '1',
            name: 'Existing',
            email: new Email('john@example.com'),
            password: Password::fromHash('$2y$10$somehash'),
            role: 'user',
        );

        $this->repository
            ->expects($this->once())
            ->method('findByEmail')
            ->willReturn($existingUser);

        $this->repository->expects($this->never())->method('create');

        $this->useCase->execute($dto);
    }

    public function test_register_user_with_admin_role(): void
    {
        $dto = new RegisterUserInputDTO(
            name: 'Admin User',
            email: 'admin@example.com',
            password: 'admin123',
            role: 'admin',
        );

        $this->repository->method('findByEmail')->willReturn(null);
        $this->repository
            ->method('create')
            ->willReturnCallback(function (User $user) {
                return new User(
                    id: '1',
                    name: $user->getName(),
                    email: $user->getEmail(),
                    password: $user->getPassword(),
                    role: $user->getRole(),
                );
            });

        $output = $this->useCase->execute($dto);

        $this->assertEquals('admin', $output->role);
    }
}
