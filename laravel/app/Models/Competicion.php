<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Competicion extends Model
{
    use HasFactory;

    protected $fillable = [
        'nombre',
        'fecha',
        'lugar',
        'tipo',
        'estado'
    ];

    protected function casts(): array
    {
        return [
            'fecha' => 'date',
        ];
    }

    public function categorias()
    {
        return $this->belongsToMany(Categoria::class, 'competicion_categoria');
    }
}
