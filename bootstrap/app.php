<?php

use App\Domain\User\Exceptions\DuplicateEmailException;
use App\Domain\User\Exceptions\UserNotFoundException;
use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;
use Illuminate\Http\Request;
use Illuminate\Validation\ValidationException;

return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        api: __DIR__.'/../routes/api.php',
        commands: __DIR__.'/../routes/console.php',
    )
    ->withMiddleware(function (Middleware $middleware) {
        //
    })
    ->withExceptions(function (Exceptions $exceptions) {
        $apiError = static function (string $message, int $status, array $errors = []) {
            return response()->json([
                'message' => $message,
                'errors' => $errors,
            ], $status);
        };

        $exceptions->render(function (ValidationException $exception, Request $request) use ($apiError) {
            if (! $request->is('api/*')) {
                return null;
            }

            return $apiError(
                'The given data was invalid.',
                422,
                $exception->errors(),
            );
        });

        $exceptions->render(function (DuplicateEmailException $exception, Request $request) use ($apiError) {
            if (! $request->is('api/*')) {
                return null;
            }

            return $apiError(
                $exception->getMessage(),
                422,
                ['email' => [$exception->getMessage()]],
            );
        });

        $exceptions->render(function (UserNotFoundException $exception, Request $request) use ($apiError) {
            if (! $request->is('api/*')) {
                return null;
            }

            return $apiError(
                $exception->getMessage(),
                404,
                ['id' => [$exception->getMessage()]],
            );
        });
    })->create();
