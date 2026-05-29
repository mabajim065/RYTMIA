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
        'username',
        'dni',
        'email',
        'password',
        'password_temporal',
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

    /**
     * Boot del modelo para autogenerar username en creación si no está establecido.
     */
    protected static function booted(): void
    {
        static::creating(function ($user) {
            if (empty($user->username)) {
                $firstApellido = explode(' ', trim($user->apellidos))[0];
                $baseUsername = \Illuminate\Support\Str::slug($user->nombre . '.' . $firstApellido, '.');
                
                $username = $baseUsername;
                $counter = 1;

                while (static::where('username', $username)->exists()) {
                    $username = $baseUsername . $counter;
                    $counter++;
                }

                $user->username = $username;
            }
        });
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
