<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Mensaje extends Model
{
    use HasFactory;


   // datos que se pueden guardar

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


   // relaciones

    public function emisor()
    {
        return $this->belongsTo(User::class, 'emisor_id');
    }

    public function receptor()
    {
        return $this->belongsTo(User::class, 'receptor_id');
    }
}