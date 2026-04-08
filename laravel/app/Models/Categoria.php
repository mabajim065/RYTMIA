<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Categoria extends Model
{
    use HasFactory;

    protected $fillable = [
        'nombre',
        'edad_min',
        'edad_max',
    ];

    protected function casts(): array
    {
        return [
            'edad_min' => 'integer',
            'edad_max' => 'integer',
        ];
    }

    // ── Relaciones ───────────────────────────────────────────────

    public function conjuntos()
    {
        return $this->hasMany(Conjunto::class);
    }

    public function gimnastas()
    {
        return $this->hasMany(Gimnasta::class);
    }

    // ── Helper: rango de edad como string ───────────────────────

    public function rangoEdad(): string
    {
        if ($this->edad_min && $this->edad_max) {
            return "{$this->edad_min}–{$this->edad_max} años";
        }
        if ($this->edad_min) {
            return "≥ {$this->edad_min} años";
        }
        if ($this->edad_max) {
            return "≤ {$this->edad_max} años";
        }
        return 'Sin restricción de edad';
    }
}
