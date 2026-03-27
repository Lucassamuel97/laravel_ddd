<?php

namespace Tests\Unit\Application\User\UseCases;

use App\Application\User\DTOs\ListUserOutputDTO;
use App\Application\User\UseCases\DefaultListUsersUseCase;
use App\Domain\Shared\Pagination\DefaultPagination;
use App\Domain\Shared\Pagination\SearchQuery;
use App\Domain\User\Entities\User;
use App\Domain\User\Repositories\UserRepositoryInterface;
use App\Domain\User\ValueObjects\Email;
use App\Domain\User\ValueObjects\Password;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;

class DefaultListUsersUseCaseTest extends TestCase
{
    private UserRepositoryInterface&MockObject $repository;
    private DefaultListUsersUseCase $useCase;

    protected function setUp(): void
    {
        parent::setUp();

        $this->repository = $this->createMock(UserRepositoryInterface::class);
        $this->useCase = new DefaultListUsersUseCase($this->repository);
    }

    public function test_execute_returns_paginated_list_of_users(): void
    {
        // Arrange
        $query = new SearchQuery(page: 2, perPage: 10, name: 'john');

        $users = [
            new User(
                id: '1',
                name: 'John Doe',
                email: new Email('john@example.com'),
                password: Password::fromHash('$2y$10$somehash'),
                role: 'user',
            ),
        ];

        $this->repository
            ->expects($this->once())
            ->method('findAll')
            ->with($query)
            ->willReturn(new DefaultPagination(
                items: $users,
                total: 11,
                perPage: 10,
                currentPage: 2,
            ));

        // Act
        $result = $this->useCase->execute($query);

        // Assert
        $this->assertSame(11, $result->total());
        $this->assertSame(10, $result->perPage());
        $this->assertSame(2, $result->currentPage());
        $this->assertSame(2, $result->lastPage());
        $this->assertCount(1, $result->items());
        $this->assertInstanceOf(ListUserOutputDTO::class, $result->items()[0]);
        $this->assertSame('john@example.com', $result->items()[0]->email);
    }
}
