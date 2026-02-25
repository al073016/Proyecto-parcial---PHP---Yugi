<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Item;
use App\Models\Loan; // Importamos el nuevo modelo
use Illuminate\Http\Request;

class LoanController extends Controller
{
    public function store(Request $request)
    {
        // 1. Validar la entrada
        $request->validate([
            'item_id' => 'required|exists:items,id',
        ]);

        $item = Item::find($request->item_id);

        // 2. Verificar disponibilidad
        if ($item->estado !== 'disponible') {
            return response()->json(['message' => 'El objeto no está disponible para préstamo'], 400);
        }

        // 3. ACTUALIZACIÓN: Crear el registro en la tabla 'loans'
        // Usamos el user_id 1 (el Test User que creamos en el seeder)
        $loan = Loan::create([
            'item_id' => $item->id,
            'user_id' => 1, 
            'fecha_prestamo' => now(),
            'fecha_devolucion_esperada' => now()->addDays(7), // Préstamo por una semana
        ]);

        // 4. Cambiar el estado del objeto a 'prestado'
        $item->update(['estado' => 'prestado']);

        return response()->json([
            'message' => 'Préstamo registrado exitosamente en la bitácora',
            'detalles' => [
                'objeto' => $item->nombre,
                'prestado_a' => 'Test User',
                'devolucion' => $loan->fecha_devolucion_esperada->format('d-m-Y')
            ]
        ]);
    }


    public function update(Request $request, $id)
{
    $loan = Loan::find($id);

    if (!$loan || $loan->fecha_devolucion_real !== null) {
        return response()->json(['message' => 'Este préstamo no existe o ya fue devuelto'], 404);
    }

    $loan->update([
        'fecha_devolucion_real' => now()
    ]);

    $item = Item::find($loan->item_id);
    $item->uso_acumulado += 10; 
    $item->estado = 'disponible';
    $item->save();

    return response()->json([
        'message' => 'Objeto devuelto exitosamente',
        'item' => $item->nombre,
        'uso_total' => $item->uso_acumulado . ' horas'
        ]);
    }
}