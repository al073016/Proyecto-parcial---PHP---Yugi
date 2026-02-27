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
        // 1. Manejar errores 404 (Recurso no encontrado)
        $exceptions->render(function (\Symfony\Component\HttpKernel\Exception\NotFoundHttpException $e, $request) {
            if ($request->is('api/*')) {
                return response()->json([
                    'status'  => 'error',
                    'message' => 'El recurso (ítem o préstamo) no existe en nuestra base de datos.',
                    'code'    => 404
                ], 404);
            }
        });

        // 2. Manejar errores 405 (Método no permitido)
        $exceptions->render(function (\Symfony\Component\HttpKernel\Exception\MethodNotAllowedHttpException $e, $request) {
            if ($request->is('api/*')) {
                return response()->json([
                    'status'  => 'error',
                    'message' => 'Estás usando un método (POST/GET/PUT) no válido para esta ruta.',
                    'code'    => 405
                ], 405);
            }
        });

        // 3. Manejar errores 500 o cualquier excepción inesperada
        $exceptions->render(function (\Throwable $e, $request) {
            if ($request->is('api/*')) {
                return response()->json([
                    'status'  => 'error',
                    'message' => 'Ocurrió un error inesperado en el servidor.',
                    'debug'   => config('app.debug') ? $e->getMessage() : 'Contacte al administrador',
                    'code'    => 500
                ], 500);
            }
        });
    })->create();


        
        
