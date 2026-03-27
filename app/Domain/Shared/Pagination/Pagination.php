<?php

namespace App\Domain\Shared\Pagination;

interface Pagination
{
    /**
     * @return array<mixed>
     */
    public function items(): array;

    public function total(): int;

    public function perPage(): int;

    public function currentPage(): int;

    public function lastPage(): int;

    public function map(callable $mapper): Pagination;
}
