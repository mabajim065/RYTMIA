<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;

class User extends Authenticatable
{
    use HasApiTokens, HasFactory, Notifiable;

    /**
     * Los atributos que se pueden asignar masivamente.
     */
    protected $fillable = [
        'nombre',
        'apellidos',
        'dni',
        'email',
        'password',
        'rol',
        'telefono',
        'activo',
    ];

    /**
     * Atributos ocultos en la serialización.
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    protected function casts(): array
    {
        return [
            'password' => 'hashed',
            'activo'   => 'boolean',
        ];
    }

    // ── Relaciones ──────────────────────────────────────────────

    public function entrenador()
    {
        return $this->hasOne(\App\Models\Entrenador::class);
    }

    public function gimnasta()
    {
        return $this->hasOne(\App\Models\Gimnasta::class);
    }

    // ── Helpers de rol ──────────────────────────────────────────

    public function esAdministrador(): bool
    {
        return $this->rol === 'administrador';
    }

    public function esEntrenadora(): bool
    {
        return $this->rol === 'entrenadora';
    }

    public function esGimnasta(): bool
    {
        return $this->rol === 'gimnasta';
    }
}
