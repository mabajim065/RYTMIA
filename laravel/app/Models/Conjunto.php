<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Conjunto extends Model
{
    use HasFactory;

    protected $table = 'conjuntos';

    protected $fillable = [
        'nombre',
        'club_id',
        'categoria_id',
        'horario',
    ];

    // ── Relaciones ───────────────────────────────────────────────

    /**
     * Club al que pertenece el conjunto.
     */
    public function club()
    {
        return $this->belongsTo(Club::class);
    }

    /**
     * Categoría (nivel/edad) del conjunto.
     */
    public function categoria()
    {
        return $this->belongsTo(Categoria::class);
    }

    /**
     * Gimnastas asignadas a este conjunto.
     * La FK conjunto_id vive en la tabla gimnastas.
     */
    public function gimnastas()
    {
        return $this->hasMany(Gimnasta::class);
    }

    /**
     * Entrenadoras asignadas a este conjunto (tabla pivote).
     */
    public function entrenadores()
    {
        return $this->belongsToMany(
            Entrenador::class,
            'conjunto_entrenador',
            'conjunto_id',
            'entrenador_id'
        )->withTimestamps();
    }

    // ── Scopes ───────────────────────────────────────────────────

    /**
     * Filtrar por club.
     */
    public function scopePorClub($query, int $clubId)
    {
        return $query->where('club_id', $clubId);
    }

    /**
     * Filtrar por categoría.
     */
    public function scopePorCategoria($query, int $categoriaId)
    {
        return $query->where('categoria_id', $categoriaId);
    }

    // ── Computed ──────────────────────────────────────────────────

    /**
     * Número de gimnastas actualmente en el conjunto.
     */
    public function totalGimnastas(): int
    {
        return $this->gimnastas()->count();
    }
}
