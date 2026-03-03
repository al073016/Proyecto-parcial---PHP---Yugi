<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Item extends Model
{
    use HasFactory;

    protected $fillable = [
        'nombre',
        'categoria',
        'estado',
        'vida_util_max',
        'uso_acumulado',
    ];

    /**
     * Relación: Un ítem puede tener muchos préstamos.
     */
    public function loans()
    {
        return $this->hasMany(Loan::class);
    }

    /**
     * Calcula el porcentaje de salud del objeto.
     * 100% = nuevo, 0% = necesita mantenimiento.
     */
    public function getSaludAttribute(): int
    {
        if ($this->vida_util_max == 0) return 100;
        $salud = 100 - (($this->uso_acumulado / $this->vida_util_max) * 100);
        return max(0, (int) round($salud));
    }

    protected $appends = ['salud'];
}
