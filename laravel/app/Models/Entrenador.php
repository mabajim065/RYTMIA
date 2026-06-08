<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Entrenador extends Model
{
    use HasFactory;


    // CONFIGURACIÓN DE LA TABLA
    // Define el nombre (evitando el plural automático 'entrenadors'), los campos y el tipado

    protected $table = 'entrenadores';

    protected $fillable = [
        'user_id',
        'club_id',
        'titulacion',
        'biografia',
        'foto_url',
        'anios_experiencia',
        'horas_semanales',
        'estado',
    ];

    protected function casts(): array
    {
        return [
            'anios_experiencia' => 'integer',
            'horas_semanales'   => 'integer',
        ];
    }


    // RELACIONES DE BASE DE DATOS
    // Vínculos con la cuenta de usuario, el club al que pertenece y sus asignaciones

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function club()
    {
        return $this->belongsTo(Club::class);
    }

    public function conjuntos()
    {
        return $this->belongsToMany(Conjunto::class, 'conjunto_entrenador');
    }

    public function competiciones()
    {
        return $this->belongsToMany(Competicion::class, 'competicion_entrenador');
    }


    // SCOPES DE BÚSQUEDA
    // Filtros rápidos y reutilizables para las consultas de base de datos

    public function scopeActivas($query)
    {
        return $query->where('estado', 'activa');
    }
}