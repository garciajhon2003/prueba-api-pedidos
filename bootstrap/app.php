<?php

use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;
use Illuminate\Http\Request;
use Illuminate\Validation\ValidationException;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

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
    ->withExceptions(function (Exceptions $exceptions) {

        // 1. Errores de Validación (422)
        $exceptions->render(function (ValidationException $e, Request $request) {
            if ($request->expectsJson()) {
                return response()->json([
                    'status' => 'error',
                    'message' => 'Los datos proporcionados no son válidos.',
                    'errors' => $e->errors(),
                ], 422, [], JSON_UNESCAPED_UNICODE);
            }

            return null;
        });

        // 2. Recursos no encontrados (404)
        $exceptions->render(function (NotFoundHttpException $e, Request $request) {
            if ($request->expectsJson()) {
                return response()->json([
                    'status' => 'error',
                    'message' => 'El recurso solicitado no existe.',
                ], 404);
            }

            return null;
        });

        // 3. Error genérico del servidor (500)
        $exceptions->render(function (Throwable $e, Request $request) {
            if (config('app.debug') || app()->environment('testing')) {
                return null;
            }

            if ($request->expectsJson()) {
                return response()->json([
                    'status' => 'error',
                    'message' => 'Ocurrió un error interno en el servidor.',
                ], 500);
            }

            return null;
        });
    })->create();
