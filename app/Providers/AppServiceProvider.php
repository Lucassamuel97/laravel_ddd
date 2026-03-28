<?php

namespace App\Providers;

use App\Application\User\UseCases\DefaultGetUserByIdUseCase;
use App\Application\User\UseCases\DefaultListUsersUseCase;
use App\Application\User\UseCases\GetUserByIdUseCase;
use App\Application\User\UseCases\ListUsersUseCase;
use App\Domain\User\Repositories\UserRepositoryInterface;
use App\Infrastructure\Persistence\Repositories\EloquentUserRepository;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        $this->app->bind(UserRepositoryInterface::class, EloquentUserRepository::class);
        $this->app->bind(ListUsersUseCase::class, DefaultListUsersUseCase::class);
        $this->app->bind(GetUserByIdUseCase::class, DefaultGetUserByIdUseCase::class);
    }

    public function boot(): void
    {
        //
    }
}
