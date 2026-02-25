<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Item extends Model
{
    use HasFactory;

    // ESTA ES LA LÍNEA QUE FALTA:
    // Aquí autorizamos los campos que el backend puede llenar/actualizar
    protected $fillable = [
        'nombre',
        'categoria',
        'estado',
        'vida_util_max',
        'uso_acumulado'
    ];
}