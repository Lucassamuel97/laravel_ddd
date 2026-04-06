<?php

namespace App\Providers;

use App\Application\User\UseCases\DefaultGetUserByIdUseCase;
use App\Application\User\UseCases\DefaultListUsersUseCase;
use App\Application\User\UseCases\DefaultUpdateUserUseCase;
use App\Application\User\UseCases\GetUserByIdUseCase;
use App\Application\User\UseCases\ListUsersUseCase;
use App\Application\User\UseCases\UpdateUserUseCase;
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
        $this->app->bind(UpdateUserUseCase::class, DefaultUpdateUserUseCase::class);
    }

    public function boot(): void
    {
        //
    }
}
