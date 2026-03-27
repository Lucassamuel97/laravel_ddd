<?php

namespace App\Interfaces\Http\Controllers;

use App\Application\User\DTOs\RegisterUserInputDTO;
use App\Application\User\DTOs\RegisterUserOutputDTO;
use App\Application\User\UseCases\RegisterUserUseCase;
use App\Domain\User\Exceptions\DuplicateEmailException;
use App\Domain\User\Repositories\UserRepositoryInterface;
use App\Interfaces\Http\Requests\RegisterUserRequest;
use App\Interfaces\Http\Resources\UserResource;
use Illuminate\Http\JsonResponse;
use Illuminate\Routing\Controller;

class UserController extends Controller
{
    public function __construct(
        private readonly RegisterUserUseCase $registerUserUseCase,
        private readonly UserRepositoryInterface $userRepository,
    ) {}

    public function index(): JsonResponse
    {
        $users = $this->userRepository->getAll();

        $data = array_map(fn($user) => new RegisterUserOutputDTO(
            id: $user->getId(),
            name: $user->getName(),
            email: $user->getEmail()->getValue(),
            role: $user->getRole(),
        ), $users);

        return response()->json([
            'data' => UserResource::collection($data),
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
