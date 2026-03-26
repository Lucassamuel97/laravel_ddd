<?php

namespace App\Interfaces\Http\Controllers;

use App\Application\User\DTOs\RegisterUserInputDTO;
use App\Application\User\UseCases\RegisterUserUseCase;
use App\Domain\User\Exceptions\DuplicateEmailException;
use App\Interfaces\Http\Requests\RegisterUserRequest;
use App\Interfaces\Http\Resources\UserResource;
use Illuminate\Http\JsonResponse;
use Illuminate\Routing\Controller;

class UserController extends Controller
{
    public function __construct(
        private readonly RegisterUserUseCase $registerUserUseCase,
    ) {}

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
