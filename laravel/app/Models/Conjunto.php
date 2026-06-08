<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Conjunto extends Model
{
    use HasFactory;


    // CONFIGURACIÓN DE LA TABLA
    // Define el nombre exacto de la tabla y los campos de asignación masiva

    protected $table = 'conjuntos';

    protected $fillable = [
        'nombre',
        'club_id',
        'categoria_id',
        'horario',
    ];


    // RELACIONES DE BASE DE DATOS
    // Vínculos con el club, la categoría, sus gimnastas y las entrenadoras asignadas

    public function club()
    {
        return $this->belongsTo(Club::class);
    }

    public function categoria()
    {
        return $this->belongsTo(Categoria::class);
    }

    public function gimnastas()
    {
        return $this->hasMany(Gimnasta::class);
    }

    public function entrenadores()
    {
        return $this->belongsToMany(
            Entrenador::class,
            'conjunto_entrenador',
            'conjunto_id',
            'entrenador_id'
        )->withTimestamps();
    }


    // SCOPES DE BÚSQUEDA
    // Filtros reutilizables para facilitar las consultas a la base de datos

    public function scopePorClub($query, int $clubId)
    {
        return $query->where('club_id', $clubId);
    }

    public function scopePorCategoria($query, int $categoriaId)
    {
        return $query->where('categoria_id', $categoriaId);
    }


    // MÉTODOS CALCULADOS
    // Funciones auxiliares para obtener datos al vuelo sobre este conjunto

    public function totalGimnastas(): int
    {
        return $this->gimnastas()->count();
    }
}