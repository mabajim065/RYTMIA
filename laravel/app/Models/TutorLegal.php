<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class TutorLegal extends Model
{
    use HasFactory;

    protected $table = 'tutores_legales';

    protected $fillable = [
        'gimnasta_id',
        'nombre',
        'apellidos',
        'email',
        'relacion',
    ];

    public function gimnasta()
    {
        return $this->belongsTo(Gimnasta::class);
    }
}
