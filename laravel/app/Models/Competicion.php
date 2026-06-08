<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Competicion extends Model
{
    use HasFactory;


    // CONFIGURACIÓN DE LA TABLA
    // Define los datos de ubicación, fecha y estado que se pueden asignar masivamente

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


    // RELACIONES DE BASE DE DATOS (TABLAS PIVOTE)
    // Vínculos de muchos a muchos con los participantes y categorías del evento

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