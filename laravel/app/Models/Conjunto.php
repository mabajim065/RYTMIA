<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Conjunto extends Model
{
    use HasFactory;


    // datos que se pueden guardar
    protected $table = 'conjuntos';
    protected $fillable = [
        'nombre',
        'club_id',
        'categoria_id',
        'horario',
    ];


    // relaciones
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


    // filtros de consulta

    public function scopePorClub($query, int $clubId)
    {
        return $query->where('club_id', $clubId);
    }

    public function scopePorCategoria($query, int $categoriaId)
    {
        return $query->where('categoria_id', $categoriaId);
    }


    // obtener el total de gimnastas en el conjunto

    public function totalGimnastas(): int
    {
        return $this->gimnastas()->count();
    }
}