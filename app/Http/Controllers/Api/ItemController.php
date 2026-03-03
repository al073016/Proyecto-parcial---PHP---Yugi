<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Item;
use Illuminate\Http\Request;

class ItemController extends Controller
{
    /**
     * GET /api/items
     * Lista todos los ítems. Opcionalmente filtra por ?estado=disponible
     * Público: cualquiera puede ver el catálogo.
     */
    public function index(Request $request)
    {
        $query = Item::query();

        if ($request->has('estado')) {
            $query->where('estado', $request->estado);
        }

        if ($request->has('categoria')) {
            $query->where('categoria', $request->categoria);
        }

        if ($request->has('categoria')) {
            $query->where('categoria', $request->categoria);
        }

        $items = $query->get();

        return response()->json([
            'status' => 'success',
            'data'   => $items,
            'total'  => $items->count(),
        ], 200);
    }

    /**
     * GET /api/items/{id}
     * Detalle de un ítem con su historial de préstamos.
     * Público.
     */
    public function show($id)
    {
        $item = Item::with(['loans.user:id,name,email'])->find($id);

        if (!$item) {
            return response()->json([
                'status'  => 'error',
                'message' => 'Objeto no encontrado.',
            ], 404);
        }

        return response()->json([
            'status' => 'success',
            'data'   => $item,
        ], 200);
    }

    /**
     * POST /api/items
     * Registra un nuevo objeto en el inventario.
     * Solo Admin.
     */
    public function store(Request $request)
    {
        $request->validate([
            'nombre'       => 'required|string|max:255',
            'categoria'    => 'required|string|max:100',
            'vida_util_max'=> 'required|integer|min:1',
            'estado'       => 'sometimes|in:disponible,prestado,mantenimiento,atrasado',
        ]);

        $item = Item::create([
            'nombre'        => $request->nombre,
            'categoria'     => $request->categoria,
            'vida_util_max' => $request->vida_util_max,
            'uso_acumulado' => 0,
            'estado'        => $request->estado ?? 'disponible',
        ]);

        return response()->json([
            'status'  => 'success',
            'message' => 'Objeto registrado en el inventario.',
            'data'    => $item,
        ], 201);
    }

    /**
     * PUT /api/items/{id}
     * Actualiza datos de un objeto (nombre, categoría, estado, etc.)
     * Solo Admin.
     */
    public function update(Request $request, $id)
    {
        $item = Item::find($id);

        if (!$item) {
            return response()->json([
                'status'  => 'error',
                'message' => 'Objeto no encontrado.',
            ], 404);
        }

        $request->validate([
            'nombre'        => 'sometimes|string|max:255',
            'categoria'     => 'sometimes|string|max:100',
            'estado'        => 'sometimes|in:disponible,prestado,mantenimiento,atrasado',
            'vida_util_max' => 'sometimes|integer|min:1',
            'uso_acumulado' => 'sometimes|integer|min:0',
        ]);

        $item->update($request->only(['nombre', 'categoria', 'estado', 'vida_util_max', 'uso_acumulado']));

        return response()->json([
            'status'  => 'success',
            'message' => 'Objeto actualizado correctamente.',
            'data'    => $item,
        ], 200);
    }

    /**
     * DELETE /api/items/{id}
     * Elimina (retira) un objeto del inventario.
     * Solo Admin.
     */
    public function destroy($id)
    {
        $item = Item::find($id);

        if (!$item) {
            return response()->json([
                'status'  => 'error',
                'message' => 'Objeto no encontrado.',
            ], 404);
        }

        // No se puede eliminar un objeto que está prestado
        if ($item->estado === 'prestado') {
            return response()->json([
                'status'  => 'error',
                'message' => 'No se puede eliminar un objeto que está actualmente prestado.',
            ], 400);
        }

        $item->delete();

        return response()->json([
            'status'  => 'success',
            'message' => 'Objeto eliminado del inventario.',
        ], 200);
    }

    /**
     * POST /api/items/{id}/completar-mantenimiento
     * Marca un objeto como disponible después de mantenimiento.
     * Solo Admin.
     */
    public function completarMantenimiento($id)
    {
        $item = Item::find($id);

        if (!$item) {
            return response()->json([
                'status'  => 'error',
                'message' => 'Objeto no encontrado.',
            ], 404);
        }

        if ($item->estado !== 'mantenimiento') {
            return response()->json([
                'status'  => 'error',
                'message' => 'El objeto no está en mantenimiento.',
            ], 400);
        }

        // Reseteamos el contador de uso y devolvemos al inventario
        $item->update([
            'uso_acumulado' => 0,
            'estado'        => 'disponible',
        ]);

        return response()->json([
            'status'  => 'success',
            'message' => 'Mantenimiento completado. El objeto está disponible nuevamente.',
            'data'    => $item,
        ], 200);
    }
}

