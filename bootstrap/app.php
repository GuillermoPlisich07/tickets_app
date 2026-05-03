<?php

use App\Exceptions\CannotDeleteRootMessageException;
use App\Exceptions\TicketClosedException;
use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;
use Illuminate\Http\JsonResponse;

return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        web: __DIR__.'/../routes/web.php',
        api: __DIR__.'/../routes/api.php',
        commands: __DIR__.'/../routes/console.php',
        health: '/up',
    )
    ->withMiddleware(function (Middleware $middleware): void {
        //
    })
    ->withExceptions(function (Exceptions $exceptions): void {
        // Mapeamos excepciones de dominio a respuestas HTTP aquí,
        // para que los Services no necesiten saber nada de HTTP.
        $exceptions->render(function (TicketClosedException $e): JsonResponse {
            return response()->json(['message' => $e->getMessage()], 422);
        });

        $exceptions->render(function (CannotDeleteRootMessageException $e): JsonResponse {
            return response()->json(['message' => $e->getMessage()], 422);
        });
    })->create();
