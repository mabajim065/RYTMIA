<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class ConjuntoResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id'      => $this->id,
            'nombre'  => $this->nombre,
            'horario' => $this->horario,

            // Club
            'club' => $this->when(
                $this->relationLoaded('club') && $this->club,
                fn () => [
                    'id'     => $this->club->id,
                    'nombre' => $this->club->nombre,
                ]
            ),

            // Categoría / nivel
            'categoria' => $this->when(
                $this->relationLoaded('categoria') && $this->categoria,
                fn () => [
                    'id'        => $this->categoria->id,
                    'nombre'    => $this->categoria->nombre,
                    'edad_min'  => $this->categoria->edad_min,
                    'edad_max'  => $this->categoria->edad_max,
                    'rango'     => $this->categoria->rangoEdad(),
                ]
            ),

            // Gimnastas del conjunto
            'gimnastas' => $this->when(
                $this->relationLoaded('gimnastas'),
                fn () => $this->gimnastas->map(fn ($g) => [
                    'id'              => $g->id,
                    'user_id'         => $g->user_id,
                    'nombre'          => $g->user?->nombre,
                    'apellidos'       => $g->user?->apellidos,
                    'numero_licencia' => $g->numero_licencia,
                    'fecha_nacimiento'=> $g->fecha_nacimiento?->format('Y-m-d'),
                    'anios_en_club'   => $g->anios_en_club,
                    'estado'          => $g->estado,
                    'telefono_contacto' => $g->telefono_contacto,
                ])
            ),

            // Entrenadoras asignadas
            'entrenadores' => $this->when(
                $this->relationLoaded('entrenadores'),
                fn () => $this->entrenadores->map(fn ($e) => [
                    'id'         => $e->id,
                    'user_id'    => $e->user_id,
                    'nombre'     => $e->user?->nombre,
                    'apellidos'  => $e->user?->apellidos,
                    'titulacion' => $e->titulacion,
                ])
            ),

            // Contador rápido
            'total_gimnastas'   => $this->when(
                $this->relationLoaded('gimnastas'),
                fn () => $this->gimnastas->count()
            ),
            'total_entrenadores' => $this->when(
                $this->relationLoaded('entrenadores'),
                fn () => $this->entrenadores->count()
            ),

            'created_at' => $this->created_at?->toDateTimeString(),
            'updated_at' => $this->updated_at?->toDateTimeString(),
        ];
    }
}
