<?php

namespace App\Interfaces\Http\Resources;

use App\Application\User\DTOs\ListUserOutputDTO;
use App\Application\User\DTOs\RegisterUserOutputDTO;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class UserResource extends JsonResource
{
    public function __construct(private readonly RegisterUserOutputDTO|ListUserOutputDTO $dto)
    {
        parent::__construct($dto);
    }

    public function toArray(Request $request): array
    {
        return [
            'id'    => $this->dto->id,
            'name'  => $this->dto->name,
            'email' => $this->dto->email,
            'role'  => $this->dto->role,
        ];
    }
}
