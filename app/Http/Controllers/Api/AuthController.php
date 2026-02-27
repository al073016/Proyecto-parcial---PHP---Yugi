<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\ValidationException;

class AuthController extends Controller
{
    /**
     * POST /api/register
     * Registra un nuevo alumno en el sistema.
     */
    public function register(Request $request)
    {
        $request->validate([
            'name'     => 'required|string|max:255',
            'email'    => 'required|string|email|max:255|unique:users',
            'password' => 'required|string|min:8|confirmed',
        ]);

        $user = User::create([
            'name'     => $request->name,
            'email'    => $request->email,
            'password' => Hash::make($request->password),
            'rol'      => 'alumno', // Por defecto siempre es alumno
        ]);

        // Creamos el token de acceso personal con Sanctum
        $token = $user->createToken('auth_token')->plainTextToken;

        return response()->json([
            'status'       => 'success',
            'message'      => 'Usuario registrado correctamente.',
            'access_token' => $token,
            'token_type'   => 'Bearer',
            'user'         => [
                'id'         => $user->id,
                'name'       => $user->name,
                'email'      => $user->email,
                'rol'        => $user->rol,
                'bloqueado'  => $user->bloqueado,
                'reputacion' => $user->reputacion,
            ],
        ], 201);
    }

    /**
     * POST /api/login
     * Autentica un usuario existente y devuelve su token.
     */
    public function login(Request $request)
    {
        $request->validate([
            'email'    => 'required|email',
            'password' => 'required',
        ]);

        $user = User::where('email', $request->email)->first();

        // Verificamos que el usuario exista y la contraseña sea correcta
        if (!$user || !Hash::check($request->password, $user->password)) {
            throw ValidationException::withMessages([
                'email' => ['Las credenciales proporcionadas son incorrectas.'],
            ]);
        }

        // Revocamos tokens anteriores para mayor seguridad (una sesión activa)
        $user->tokens()->delete();

        $token = $user->createToken('auth_token')->plainTextToken;

        return response()->json([
            'status'       => 'success',
            'message'      => 'Sesión iniciada correctamente.',
            'access_token' => $token,
            'token_type'   => 'Bearer',
            'user'         => [
                'id'         => $user->id,
                'name'       => $user->name,
                'email'      => $user->email,
                'rol'        => $user->rol,
                'bloqueado'  => $user->bloqueado,
                'reputacion' => $user->reputacion,
            ],
        ], 200);
    }

    /**
     * POST /api/logout
     * Revoca el token actual del usuario (cierra sesión).
     * Requiere: auth:sanctum middleware
     */
    public function logout(Request $request)
    {
        // Eliminamos el token con el que llegó la petición
        $request->user()->currentAccessToken()->delete();

        return response()->json([
            'status'  => 'success',
            'message' => 'Sesión cerrada correctamente.',
        ], 200);
    }

    /**
     * GET /api/me
     * Devuelve el perfil del usuario autenticado.
     * Requiere: auth:sanctum middleware
     */
    public function me(Request $request)
    {
        $user = $request->user()->load('loans.item');

        return response()->json([
            'status' => 'success',
            'data'   => [
                'id'              => $user->id,
                'name'            => $user->name,
                'email'           => $user->email,
                'rol'             => $user->rol,
                'bloqueado'       => $user->bloqueado,
                'reputacion'      => $user->reputacion,
                'prestamos_activos' => $user->loans->whereNull('fecha_devolucion_real')->values(),
            ],
        ], 200);
    }
}
