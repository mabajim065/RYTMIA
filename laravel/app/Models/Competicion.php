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
        'hora',
        'direccion',
        'lat',
        'lng',
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

    public function conjuntos()
    {
        return $this->belongsToMany(Conjunto::class, 'competicion_conjunto');
    }

    public function entrenadoras()
    {
        return $this->belongsToMany(Entrenador::class, 'competicion_entrenador');
    }

    public function gimnastas()
    {
        return $this->belongsToMany(Gimnasta::class, 'competicion_gimnasta');
    }
}
