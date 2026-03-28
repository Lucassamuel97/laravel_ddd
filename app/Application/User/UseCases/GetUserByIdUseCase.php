<?php

namespace App\Application\User\UseCases;

use App\Application\User\DTOs\GetUserByIdOutputDTO;

abstract class GetUserByIdUseCase
{
    abstract public function execute(string $id): GetUserByIdOutputDTO;
}
