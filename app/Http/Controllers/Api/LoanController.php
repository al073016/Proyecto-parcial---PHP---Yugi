<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Item;
use App\Models\Loan;
use App\Models\User;
use Illuminate\Http\Request;

class LoanController extends Controller
{
    /**
     * GET /api/loans
     * Historial completo de préstamos con datos del alumno y del objeto.
     * Admin: ve todos. Alumno: ve solo los suyos.
     */
    public function index(Request $request)
    {
        $user = $request->user();

        if ($user->esAdmin()) {
            // El admin ve todo el historial con relaciones cargadas
            $loans = Loan::with([
                'user:id,name,email,rol,reputacion',
                'item:id,nombre,categoria,estado',
            ])->latest()->get();
        } else {
            // El alumno solo ve sus propios préstamos
            $loans = Loan::with(['item:id,nombre,categoria,estado'])
                ->where('user_id', $user->id)
                ->latest()
                ->get();
        }

        return response()->json([
            'status'  => 'success',
            'message' => 'Historial de préstamos obtenido.',
            'data'    => $loans,
            'total'   => $loans->count(),
            'code'    => 200,
        ], 200);
    }

    /**
     * POST /api/loans
     * Registra un nuevo préstamo.
     * Requiere: usuario autenticado (alumno o admin).
     * Validaciones: objeto disponible, alumno no bloqueado, sin préstamos activos del mismo objeto.
     */
    public function store(Request $request)
    {
        $request->validate([
            'item_id'                   => 'required|exists:items,id',
            'fecha_devolucion_esperada' => 'required|date|after:today',
            'estado_fisico_salida'      => 'sometimes|in:bueno,regular,malo',
        ]);

        $currentUser = $request->user();
        $item = Item::find($request->item_id);

        // --- MOTOR DE DISPONIBILIDAD ---

        // 1. Verificar si el objeto está disponible
        if ($item->estado !== 'disponible') {
            return response()->json([
                'status'  => 'error',
                'message' => "El objeto '{$item->nombre}' no está disponible. Estado actual: {$item->estado}.",
                'code'    => 400,
            ], 400);
        }

        // 2. Verificar si el alumno está bloqueado (tiene un préstamo atrasado)
        if ($currentUser->bloqueado) {
            return response()->json([
                'status'  => 'error',
                'message' => 'Tu cuenta está bloqueada porque tienes un préstamo atrasado. Devuelve el objeto pendiente para poder solicitar nuevos préstamos.',
                'code'    => 403,
            ], 403);
        }

        // --- LÓGICA DE BLOQUEO PREVENTIVO ---
        // 3. Verificar si el alumno ya tiene un objeto "atrasado" sin devolver
        $prestamosAtrasados = Loan::where('user_id', $currentUser->id)
            ->whereNull('fecha_devolucion_real')
            ->where('fecha_devolucion_esperada', '<', now())
            ->count();

        if ($prestamosAtrasados > 0) {
            // Bloqueamos al usuario automáticamente si tiene atrasos
            $currentUser->update(['bloqueado' => true]);

            return response()->json([
                'status'  => 'error',
                'message' => 'Tienes préstamos atrasados sin devolver. Tu cuenta ha sido bloqueada. Contacta al administrador.',
                'code'    => 403,
            ], 403);
        }

        // --- CREAR PRÉSTAMO ---
        $loan = Loan::create([
            'item_id'                   => $item->id,
            'user_id'                   => $currentUser->id,
            'fecha_prestamo'            => now(),
            'fecha_devolucion_esperada' => $request->fecha_devolucion_esperada,
            'estado_fisico_salida'      => $request->estado_fisico_salida ?? 'bueno',
        ]);

        // Actualizamos el estado del objeto
        $item->update(['estado' => 'prestado']);

        // Cargamos las relaciones para la respuesta
        $loan->load('user:id,name,email', 'item:id,nombre,categoria');

        return response()->json([
            'status'  => 'success',
            'message' => 'Préstamo registrado exitosamente.',
            'data'    => [
                'prestamo_id'             => $loan->id,
                'objeto'                  => $loan->item->nombre,
                'alumno'                  => $loan->user->name,
                'fecha_prestamo'          => $loan->fecha_prestamo->format('d-m-Y H:i'),
                'devolucion_esperada'     => $loan->fecha_devolucion_esperada->format('d-m-Y'),
                'estado_fisico_salida'    => $loan->estado_fisico_salida,
            ],
            'code' => 201,
        ], 201);
    }

    /**
     * PUT /api/loans/{id}
     * Registra la devolución de un objeto (check-in).
     * Incluye: cálculo de desgaste, detección de daños, cálculo de multa.
     * Admin: puede devolver cualquier préstamo. Alumno: solo los suyos.
     */
    public function update(Request $request, $id)
    {
        $loan = Loan::with('item', 'user')->find($id);
        $currentUser = $request->user();

        if (!$loan || $loan->fecha_devolucion_real !== null) {
            return response()->json([
                'status'  => 'error',
                'message' => 'Este préstamo no existe o ya fue devuelto.',
            ], 404);
        }

        // Alumno solo puede devolver sus propios préstamos
        if (!$currentUser->esAdmin() && $loan->user_id !== $currentUser->id) {
            return response()->json([
                'status'  => 'error',
                'message' => 'No tienes permiso para devolver este préstamo.',
            ], 403);
        }

        $request->validate([
            'estado_fisico_entrada' => 'required|in:bueno,regular,malo',
        ]);

        // --- REGISTRO DE DEVOLUCIÓN ---
        $loan->fecha_devolucion_real   = now();
        $loan->estado_fisico_entrada   = $request->estado_fisico_entrada;

        // --- CALCULADOR DE MULTA ---
        $multa = 0;

        // Multa por retraso: $5 por día de retraso
        if (now()->isAfter($loan->fecha_devolucion_esperada)) {
            $diasRetraso = $loan->fecha_devolucion_esperada->diffInDays(now());
            $multa += $diasRetraso * 5;
        }

        // Multa por daño al objeto
        if ($request->estado_fisico_entrada === 'malo') {
            $multa += 50; // Daño mayor
        } elseif ($request->estado_fisico_entrada === 'regular') {
            $multa += 15; // Daño menor
        }

        $loan->monto_multa = $multa;
        $loan->save();

        // --- CALCULADOR DE DESGASTE ---
        $item = $loan->item;
        $horasDeUso = $loan->fecha_prestamo->diffInHours($loan->fecha_devolucion_real);
        if ($horasDeUso == 0) $horasDeUso = 1;

        $item->uso_acumulado += $horasDeUso;

        // Verificar si el objeto necesita mantenimiento
        if ($item->uso_acumulado >= $item->vida_util_max) {
            $item->estado = 'mantenimiento';
            $mensajeEstado = "⚠️ El objeto ha superado su vida útil y ha sido enviado a MANTENIMIENTO.";
        } else {
            $item->estado = 'disponible';
            $mensajeEstado = "El objeto está nuevamente disponible.";
        }
        $item->save();

        // --- DESBLOQUEAR USUARIO (si no tiene más atrasos) ---
        $atrasosPendientes = Loan::where('user_id', $loan->user_id)
            ->whereNull('fecha_devolucion_real')
            ->where('fecha_devolucion_esperada', '<', now())
            ->count();

        if ($atrasosPendientes === 0) {
            $loan->user->update(['bloqueado' => false]);
        }

        return response()->json([
            'status'  => 'success',
            'message' => 'Devolución registrada correctamente. ' . $mensajeEstado,
            'data'    => [
                'item'                  => $item->nombre,
                'alumno'                => $loan->user->name,
                'horas_de_uso'          => $horasDeUso,
                'uso_total_acumulado'   => $item->uso_acumulado,
                'vida_util_max'         => $item->vida_util_max,
                'salud_restante'        => $item->salud . '%',
                'estado_fisico_entrada' => $loan->estado_fisico_entrada,
                'multa_aplicada'        => '$' . number_format($loan->monto_multa, 2),
                'nuevo_estado_objeto'   => $item->estado,
            ],
        ], 200);
    }

    /**
     * GET /api/loans/dashboard
     * Resumen de riesgo para el panel del administrador.
     * Solo Admin.
     */
    public function dashboard()
    {
        $enPrestamo   = Item::where('estado', 'prestado')->count();
        $atrasados    = Loan::whereNull('fecha_devolucion_real')
            ->where('fecha_devolucion_esperada', '<', now())
            ->count();
        $mantenimiento = Item::where('estado', 'mantenimiento')->count();
        $disponibles   = Item::where('estado', 'disponible')->count();

        $prestamosAtrasados = Loan::with(['user:id,name,email', 'item:id,nombre'])
            ->whereNull('fecha_devolucion_real')
            ->where('fecha_devolucion_esperada', '<', now())
            ->get();

        return response()->json([
            'status' => 'success',
            'data'   => [
                'resumen' => [
                    'disponibles'   => $disponibles,
                    'en_prestamo'   => $enPrestamo,
                    'atrasados'     => $atrasados,
                    'mantenimiento' => $mantenimiento,
                ],
                'prestamos_atrasados' => $prestamosAtrasados,
            ],
        ], 200);
    }
}
