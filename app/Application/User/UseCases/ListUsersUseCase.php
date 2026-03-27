<?php

namespace App\Application\User\UseCases;

use App\Domain\Shared\Pagination\Pagination;
use App\Domain\Shared\Pagination\SearchQuery;

abstract class ListUsersUseCase
{
    abstract public function execute(SearchQuery $query): Pagination;
}
