<?php

namespace App\Domain\Shared\Pagination;

class DefaultPagination implements Pagination
{
    /**
     * @param array<mixed> $items
     */
    public function __construct(
        private readonly array $items,
        private readonly int $total,
        private readonly int $perPage,
        private readonly int $currentPage,
    ) {}

    public function items(): array
    {
        return $this->items;
    }

    public function total(): int
    {
        return $this->total;
    }

    public function perPage(): int
    {
        return $this->perPage;
    }

    public function currentPage(): int
    {
        return $this->currentPage;
    }

    public function lastPage(): int
    {
        if ($this->perPage <= 0) {
            return 1;
        }

        return max(1, (int) ceil($this->total / $this->perPage));
    }

    public function map(callable $mapper): Pagination
    {
        return new self(
            items: array_map($mapper, $this->items),
            total: $this->total,
            perPage: $this->perPage,
            currentPage: $this->currentPage,
        );
    }
}
