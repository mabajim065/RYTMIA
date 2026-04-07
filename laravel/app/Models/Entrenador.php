<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Entrenador extends Model
{
    use HasFactory;

    // La tabla se llama 'entrenadores', no 'entrenadors'
    protected $table = 'entrenadores';

    protected $fillable = [
        'user_id',
        'club_id',
        'titulacion',
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

    // ── Relaciones ──────────────────────────────────────────────

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function club()
    {
        return $this->belongsTo(Club::class);
    }

    // ── Scopes ──────────────────────────────────────────────────

    public function scopeActivas($query)
    {
        return $query->where('estado', 'activa');
    }
}