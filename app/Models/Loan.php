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
        'fecha_devolucion_real',
        'estado_fisico_salida',
        'estado_fisico_entrada',
        'monto_multa',
    ];

    protected $casts = [
        'fecha_prestamo'            => 'datetime',
        'fecha_devolucion_esperada' => 'datetime',
        'fecha_devolucion_real'     => 'datetime',
        'monto_multa'               => 'decimal:2',
    ];

    /**
     * Relación: Un préstamo pertenece a un ítem.
     * Permite mostrar el nombre del objeto en lugar de solo el item_id.
     */
    public function item()
    {
        return $this->belongsTo(Item::class);
    }

    /**
     * Relación: Un préstamo pertenece a un usuario.
     * Permite mostrar el nombre real del alumno en el historial.
     */
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Verifica si el préstamo está atrasado.
     */
    public function estaAtrasado(): bool
    {
        return is_null($this->fecha_devolucion_real)
            && now()->isAfter($this->fecha_devolucion_esperada);
    }
}

