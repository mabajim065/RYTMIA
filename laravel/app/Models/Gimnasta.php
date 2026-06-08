<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Gimnasta extends Model
{
    use HasFactory;


    // CONFIGURACIÓN DE LA TABLA
    // Define los campos de asignación masiva y el casteo de tipos de datos (como la fecha)

    protected $fillable = [
        'user_id',
        'club_id',
        'categoria_id',
        'conjunto_id',
        'numero_licencia',
        'fecha_nacimiento',
        'anios_en_club',
        'estado',
        'telefono_contacto',
    ];

    protected function casts(): array
    {
        return [
            'fecha_nacimiento' => 'date',
            'anios_en_club'    => 'integer',
        ];
    }


    // RELACIONES DE BASE DE DATOS
    // Vínculos con la cuenta, tutor, club, categoría, conjunto y competiciones asignadas

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function tutorLegal()
    {
        return $this->hasOne(TutorLegal::class);
    }

    public function club()
    {
        return $this->belongsTo(Club::class);
    }

    public function categoria()
    {
        return $this->belongsTo(Categoria::class);
    }

    public function conjunto()
    {
        return $this->belongsTo(Conjunto::class);
    }

    public function competiciones()
    {
        return $this->belongsToMany(Competicion::class, 'competicion_gimnasta');
    }


    // SCOPES DE BÚSQUEDA
    // Filtros rápidos y reutilizables para las consultas de base de datos

    public function scopeActivas($query)
    {
        return $query->where('estado', 'activa');
    }

    public function scopePorCategoria($query, int $categoriaId)
    {
        return $query->where('categoria_id', $categoriaId);
    }


    // MÉTODOS AUXILIARES
    // Lógica adicional para obtener información calculada sobre la gimnasta

    public function esMenorDeEdad(): bool
    {
        if (!$this->fecha_nacimiento) {
            return false;
        }
        
        return $this->fecha_nacimiento->age < 18;
    }
}