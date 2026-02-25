<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Item;
use Illuminate\Http\Request;

class ItemController extends Controller
{
    // Este método devuelve todos los objetos
    public function index()
    {
        // Obtenemos todos los items de la base de datos
        $items = Item::all();
        
        // Los enviamos como respuesta JSON
        return response()->json($items);
    }

    // Este método devuelve un solo objeto por su ID
    public function show($id)
    {
        $item = Item::find($id);

        if (!$item) {
            return response()->json(['message' => 'Objeto no encontrado'], 404);
        }

        return response()->json($item);
    }
}
