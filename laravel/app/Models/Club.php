<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Club extends Model
{
    use HasFactory;


    // CONFIGURACIÓN DE LA TABLA
    // Define los campos de contacto y datos básicos que se pueden asignar masivamente

    protected $fillable = [
        'nombre',
        'direccion',
        'telefono',
        'email',
    ];


    // RELACIONES DE BASE DE DATOS
    // Vínculos con los conjuntos, entrenadoras y gimnastas pertenecientes a este club

    public function conjuntos()
    {
        return $this->hasMany(Conjunto::class);
    }

    public function entrenadores()
    {
        return $this->hasMany(Entrenador::class);
    }

    public function gimnastas()
    {
        return $this->hasMany(Gimnasta::class);
    }
}