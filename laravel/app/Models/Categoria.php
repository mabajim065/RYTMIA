<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Categoria extends Model
{
    use HasFactory;
// campos que se pueden guardar
    protected $fillable = [
        'nombre',
        'edad_min',
        'edad_max',
    ];

    // tipos de datos
    protected function casts(): array
    {
        return [
            // edad mínima como numero
            'edad_min' => 'integer',

            // edad máxima como numero
            'edad_max' => 'integer',
        ];
    }

    // conjuntos de esta categoría
    public function conjuntos()
    {
        return $this->hasMany(Conjunto::class);
    }

    // gimnastas de esta categoría
    public function gimnastas()
    {
        return $this->hasMany(Gimnasta::class);
    }

    // texto del rango de edad
    public function rangoEdad(): string
    {
        // si tiene edad mínima y máxima
        if ($this->edad_min && $this->edad_max) {
            return "{$this->edad_min}–{$this->edad_max} años";
        }

        // si solo tiene edad minima
        if ($this->edad_min) {
            return "≥ {$this->edad_min} años";
        }

        // si solo tiene edad maxima
        if ($this->edad_max) {
            return "≤ {$this->edad_max} años";
        }

        // si no tiene limite de edad
        return 'Sin restricción de edad';
    }
}