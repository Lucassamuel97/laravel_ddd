<?php

namespace App\Infrastructure\Persistence\Repositories;

use App\Domain\Shared\Pagination\DefaultPagination;
use App\Domain\Shared\Pagination\Pagination;
use App\Domain\Shared\Pagination\SearchQuery;
use App\Domain\User\Entities\User;
use App\Domain\User\Repositories\UserRepositoryInterface;
use App\Domain\User\ValueObjects\Email;
use App\Domain\User\ValueObjects\Password;
use App\Infrastructure\Persistence\Eloquent\UserModel;

class EloquentUserRepository implements UserRepositoryInterface
{
    public function create(User $user): User
    {
        $model = UserModel::create([
            'name'     => $user->getName(),
            'email'    => $user->getEmail()->getValue(),
            'password' => $user->getPassword()->getHash(),
            'role'     => $user->getRole(),
        ]);

        return $this->toEntity($model);
    }

    public function update(User $user): User
    {
        $model = UserModel::query()->findOrFail($user->getId());

        $model->update([
            'name' => $user->getName(),
            'email' => $user->getEmail()->getValue(),
            'password' => $user->getPassword()->getHash(),
            'role' => $user->getRole(),
        ]);

        return $this->toEntity($model->refresh());
    }

    public function findById(string $id): ?User
    {
        $model = UserModel::query()->find($id);

        return $model ? $this->toEntity($model) : null;
    }

    public function findByEmail(string $email): ?User
    {
        $model = UserModel::where('email', strtolower($email))->first();

        return $model ? $this->toEntity($model) : null;
    }

    public function getAll(): array
    {
        $models = UserModel::all();

        return $models->map(fn (UserModel $model) => $this->toEntity($model))->toArray();
    }

    public function findAll(SearchQuery $query): Pagination
    {
        $paginator = UserModel::query()
            ->when($query->name !== null && $query->name !== '', function ($builder) use ($query) {
                $builder->where('name', 'like', '%' . $query->name . '%');
            })
            ->when($query->email !== null && $query->email !== '', function ($builder) use ($query) {
                $builder->where('email', 'like', '%' . $query->email . '%');
            })
            ->orderBy('id')
            ->paginate(
                perPage: $query->perPage,
                columns: ['*'],
                pageName: 'page',
                page: $query->page,
            );

        return new DefaultPagination(
            items: $paginator->items() === []
                ? []
                : array_map(fn (UserModel $model) => $this->toEntity($model), $paginator->items()),
            total: $paginator->total(),
            perPage: $paginator->perPage(),
            currentPage: $paginator->currentPage(),
        );
    }

    private function toEntity(UserModel $model): User
    {
        return new User(
            id: (string) $model->id,
            name: $model->name,
            email: new Email($model->email),
            password: Password::fromHash($model->password),
            role: $model->role,
        );
    }
}
