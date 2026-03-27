<?php

namespace App\Infrastructure\Persistence\Repositories;

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

    public function findByEmail(string $email): ?User
    {
        $model = UserModel::where('email', strtolower($email))->first();

        return $model ? $this->toEntity($model) : null;
    }

    /**
     * @return array<User>
     */
    public function getAll(): array
    {
        $models = UserModel::all();

        return $models->map(fn (UserModel $model) => $this->toEntity($model))->toArray();
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
