<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Club extends Model
{
    use HasFactory;

    protected $fillable = [
        'nombre',
        'direccion',
        'telefono',
        'email',
    ];

    // ── Relaciones ───────────────────────────────────────────────

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
