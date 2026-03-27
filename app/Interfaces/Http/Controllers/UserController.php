<?php

namespace App\Interfaces\Http\Controllers;

use App\Application\User\UseCases\ListUsersUseCase;
use App\Application\User\DTOs\RegisterUserInputDTO;
use App\Application\User\UseCases\RegisterUserUseCase;
use App\Domain\Shared\Pagination\SearchQuery;
use App\Domain\User\Exceptions\DuplicateEmailException;
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
    ) {}

    public function index(ListUsersRequest $request): JsonResponse
    {
        $pagination = $this->listUsersUseCase->execute(new SearchQuery(
            page: (int) $request->input('page', 1),
            perPage: (int) $request->input('per_page', 15),
            name: $request->input('name'),
            email: $request->input('email'),
        ));

        $data = array_map(
            fn ($item) => (new UserResource($item))->toArray($request),
            $pagination->items(),
        );

        return response()->json([
            'data' => $data,
            'meta' => [
                'total' => $pagination->total(),
                'per_page' => $pagination->perPage(),
                'current_page' => $pagination->currentPage(),
                'last_page' => $pagination->lastPage(),
            ],
        ]);
    }

    public function store(RegisterUserRequest $request): JsonResponse
    {
        try {
            $dto = new RegisterUserInputDTO(
                name: $request->input('name'),
                email: $request->input('email'),
                password: $request->input('password'),
                role: $request->input('role', 'user'),
            );

            $output = $this->registerUserUseCase->execute($dto);

            return (new UserResource($output))->response()->setStatusCode(201);
        } catch (DuplicateEmailException $e) {
            return response()->json(['message' => $e->getMessage()], 422);
        }
    }
}
