<?php

namespace App\Interfaces\Http\Controllers;

use App\Application\User\UseCases\GetUserByIdUseCase;
use App\Application\User\UseCases\ListUsersUseCase;
use App\Application\User\DTOs\RegisterUserInputDTO;
use App\Application\User\UseCases\RegisterUserUseCase;
use App\Domain\Shared\Pagination\SearchQuery;
use App\Interfaces\Http\Requests\ListUsersRequest;
use App\Interfaces\Http\Requests\RegisterUserRequest;
use App\Interfaces\Http\Resources\UserResource;
use Illuminate\Http\JsonResponse;
use Illuminate\Routing\Controller;

class UserController extends Controller
{
    public function __construct(
        private readonly RegisterUserUseCase $registerUserUseCase,
        private readonly ListUsersUseCase $listUsersUseCase,
        private readonly GetUserByIdUseCase $getUserByIdUseCase,
    ) {}

    public function index(ListUsersRequest $request): JsonResponse
    {
        $validated = $request->validated();

        $pagination = $this->listUsersUseCase->execute(new SearchQuery(
            page: (int) ($validated['page'] ?? 1),
            perPage: (int) ($validated['per_page'] ?? 15),
            name: $validated['name'] ?? null,
            email: $validated['email'] ?? null,
        ));

        return UserResource::collection($pagination->items())
            ->additional([
            'meta' => [
                'total' => $pagination->total(),
                'per_page' => $pagination->perPage(),
                'current_page' => $pagination->currentPage(),
                'last_page' => $pagination->lastPage(),
            ],
            ])
            ->response();
    }

    public function store(RegisterUserRequest $request): JsonResponse
    {
        $validated = $request->validated();

        $dto = new RegisterUserInputDTO(
            name: $validated['name'],
            email: $validated['email'],
            password: $validated['password'],
            role: $validated['role'] ?? 'user',
        );

        $output = $this->registerUserUseCase->execute($dto);

        return (new UserResource($output))->response()->setStatusCode(201);
    }

    public function show(string $id): JsonResponse
    {
        $output = $this->getUserByIdUseCase->execute($id);

        return (new UserResource($output))->response();
    }
}
