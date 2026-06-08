<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Mensaje extends Model
{
    use HasFactory;


    // CONFIGURACIÓN DE LA TABLA
    // Define los campos que se pueden guardar masivamente y el casteo de fechas

    protected $fillable = [
        'emisor_id',
        'receptor_id',
        'asunto',
        'contenido',
        'leido_at',
    ];

    protected $casts = [
        'leido_at' => 'datetime',
    ];


    // RELACIONES DE BASE DE DATOS
    // Vínculos con los usuarios involucrados en la comunicación (remitente y destinatario)

    public function emisor()
    {
        return $this->belongsTo(User::class, 'emisor_id');
    }

    public function receptor()
    {
        return $this->belongsTo(User::class, 'receptor_id');
    }
}