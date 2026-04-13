<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Gimnasta extends Model
{
    use HasFactory;

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

    // ── Relaciones ──────────────────────────────────────────────

    public function user()
    {
        return $this->belongsTo(User::class);
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

    // ── Scopes ──────────────────────────────────────────────────

    public function scopeActivas($query)
    {
        return $query->where('estado', 'activa');
    }

    public function scopePorCategoria($query, int $categoriaId)
    {
        return $query->where('categoria_id', $categoriaId);
    }
}
