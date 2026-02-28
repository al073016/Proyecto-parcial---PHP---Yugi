<?php

use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;

return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        web: __DIR__.'/../routes/web.php',
        api: __DIR__.'/../routes/api.php',
        commands: __DIR__.'/../routes/console.php',
        health: '/up',
    )
    ->withMiddleware(function (Middleware $middleware): void {
        // Registramos el alias 'es_admin' para proteger rutas de administrador
        $middleware->alias([
            'es_admin' => \App\Http\Middleware\EsAdmin::class,
        ]);
    })
    ->withExceptions(function (Exceptions $exceptions): void {

        $exceptions->render(function (\Throwable $e, $request) {
            if (!$request->is('api/*')) {
                return null; // Dejar que Laravel maneje rutas no-API normalmente
            }

            // 401 — Sin token o token inválido
            if ($e instanceof \Illuminate\Auth\AuthenticationException) {
                return response()->json([
                    'status'  => 'error',
                    'message' => 'No autenticado. Debes iniciar sesión y enviar tu token.',
                    'code'    => 401,
                ], 401);
            }

            // Errores HTTP con código propio (403, 404, 405, 422, etc.)
            if ($e instanceof \Symfony\Component\HttpKernel\Exception\HttpException) {
                $code = $e->getStatusCode();

                $mensajes = [
                    403 => 'Acceso denegado. No tienes permisos para esta acción.',
                    404 => 'El recurso solicitado no existe.',
                    405 => 'Método HTTP no permitido para esta ruta.',
                    422 => 'Los datos enviados no son válidos.',
                ];

                return response()->json([
                    'status'  => 'error',
                    'message' => $mensajes[$code] ?? $e->getMessage(),
                    'code'    => $code,
                ], $code);
            }

            // Errores de validación (422)
            if ($e instanceof \Illuminate\Validation\ValidationException) {
                return response()->json([
                    'status'  => 'error',
                    'message' => 'Error de validación.',
                    'errors'  => $e->errors(),
                    'code'    => 422,
                ], 422);
            }

            // Cualquier otro error inesperado (500)
            return response()->json([
                'status'  => 'error',
                'message' => 'Ocurrió un error inesperado en el servidor.',
                'debug'   => config('app.debug') ? $e->getMessage() : 'Contacte al administrador',
                'code'    => 500,
            ], 500);
        });

    })->create();
