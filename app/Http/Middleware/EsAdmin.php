<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class EsAdmin
{
    /**
     * Solo permite el acceso si el usuario autenticado tiene rol 'admin'.
     * Se usa en rutas que requieren permisos de administrador (Bibliotecario).
     */
    public function handle(Request $request, Closure $next): Response
    {
        if (!$request->user() || $request->user()->rol !== 'admin') {
            return response()->json([
                'status'  => 'error',
                'message' => 'Acceso denegado. Se requieren permisos de administrador.',
                'code'    => 403,
            ], 403);
        }

        return $next($request);
    }
}
