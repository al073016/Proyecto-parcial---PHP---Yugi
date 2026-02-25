<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Loan extends Model
{
    protected $fillable = [
    'item_id', 
    'user_id',
    'fecha_prestamo',
    'fecha_devolucion_esperada',
    'fecha_devolucion_real'
];

protected $casts = [
    'fecha_prestamo' => 'datetime',
    'fecha_devolucion_esperada' => 'datetime',
    'fecha_devolucion_real' => 'datetime',
];



}
