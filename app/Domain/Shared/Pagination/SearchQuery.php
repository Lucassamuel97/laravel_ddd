<?php

namespace App\Domain\Shared\Pagination;

class SearchQuery
{
    public readonly int $page;
    public readonly int $perPage;
    public readonly ?string $name;
    public readonly ?string $email;

    public function __construct(
        int $page = 1,
        int $perPage = 15,
        ?string $name = null,
        ?string $email = null,
    ) {
        $this->page = max(1, $page);
        $this->perPage = max(1, $perPage);
        $this->name = $name;
        $this->email = $email;
    }
}
