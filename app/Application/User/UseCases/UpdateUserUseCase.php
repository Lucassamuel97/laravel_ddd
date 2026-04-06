<?php

namespace App\Application\User\UseCases;

use App\Application\User\DTOs\UpdateUserInputDTO;
use App\Application\User\DTOs\UpdateUserOutputDTO;

abstract class UpdateUserUseCase
{
    abstract public function execute(UpdateUserInputDTO $dto): UpdateUserOutputDTO;
}
