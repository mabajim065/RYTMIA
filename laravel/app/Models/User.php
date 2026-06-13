<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;

class User extends Authenticatable
{
    use HasApiTokens, HasFactory, Notifiable;

    /*campos que se pueden guardar en la base de datos.*/
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

    /**campos ocultos en la serialización*/
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /** cifrado de contraseña*/
    protected function casts(): array
    {
        return [
            'password' => 'hashed',
            'activo' => 'boolean',
        ];
    }

    /**Esto se ejecuta justo antes de crear un usuario  esto sirve para crear un username automático */
    protected static function booted(): void
    {
        static::creating(function ($user) {
            // Si el usuario no tiene username, se genera uno
            if (empty($user->username)) {
                // Cogemos el primer apellido
                $firstApellido = explode(' ', trim($user->apellidos))[0];
                //el nombre de usuario nombre.apellido1
                $baseUsername = \Illuminate\Support\Str::slug($user->nombre . '.' . $firstApellido, '.');

                $username = $baseUsername;
                $counter = 1;

                // Si el username ya existe, se añade un número al final
                while (static::where('username', $username)->exists()) {
                    $username = $baseUsername . $counter;
                    $counter++;
                }

                $user->username = $username;
            }
        });
    }

    //Relaciones

    public function entrenador()
    {
        return $this->hasOne(\App\Models\Entrenador::class);
    }

    public function gimnasta()
    {
        return $this->hasOne(\App\Models\Gimnasta::class);
    }

    // comprobar el rol de el usuario

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
