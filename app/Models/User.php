<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;

class User extends Authenticatable
{
    // HasApiTokens habilita Laravel Sanctum para generar tokens de acceso
    use HasApiTokens, HasFactory, Notifiable;

    protected $fillable = [
        'name',
        'email',
        'password',
        'rol',
        'bloqueado',
        'reputacion',
    ];

    protected $hidden = [
        'password',
        'remember_token',
    ];

    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password'          => 'hashed',
            'bloqueado'         => 'boolean',
        ];
    }

    /**
     * Relación: Un usuario puede tener muchos préstamos.
     * Incluye el objeto relacionado para mostrar el nombre real del alumno.
     */
    public function loans()
    {
        return $this->hasMany(Loan::class);
    }

    /**
     * Verifica si el usuario es administrador.
     */
    public function esAdmin(): bool
    {
        return $this->rol === 'admin';
    }
}
