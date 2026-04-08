<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class UserResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id'        => $this->id,
            'nombre'    => $this->nombre,
            'apellidos' => $this->apellidos,
            'dni'       => $this->dni,
            'email'     => $this->email,
            'rol'       => $this->rol,
            'telefono'  => $this->telefono,
            'activo'    => $this->activo,

            // Perfil entrenadora (solo si existe y está cargado)
            'entrenador' => $this->when(
                $this->relationLoaded('entrenador') && $this->entrenador,
                fn () => [
                    'id'                => $this->entrenador->id,
                    'titulacion'        => $this->entrenador->titulacion,
                    'biografia'         => $this->entrenador->biografia,
                    'foto_url'          => $this->entrenador->foto_url,
                    'anios_experiencia' => $this->entrenador->anios_experiencia,
                    'horas_semanales'   => $this->entrenador->horas_semanales,
                    'estado'            => $this->entrenador->estado,
                    'club'              => $this->when(
                        $this->entrenador->relationLoaded('club') && $this->entrenador->club,
                        fn () => [
                            'id'     => $this->entrenador->club->id,
                            'nombre' => $this->entrenador->club->nombre,
                        ]
                    ),
                ]
            ),

            // Perfil gimnasta (solo si existe y está cargado)
            'gimnasta' => $this->when(
                $this->relationLoaded('gimnasta') && $this->gimnasta,
                fn () => [
                    'id'               => $this->gimnasta->id,
                    'numero_licencia'  => $this->gimnasta->numero_licencia,
                    'fecha_nacimiento' => $this->gimnasta->fecha_nacimiento,
                    'anios_en_club'    => $this->gimnasta->anios_en_club,
                    'estado'           => $this->gimnasta->estado,
                    'club'             => $this->when(
                        $this->gimnasta->relationLoaded('club') && $this->gimnasta->club,
                        fn () => [
                            'id'     => $this->gimnasta->club->id,
                            'nombre' => $this->gimnasta->club->nombre,
                        ]
                    ),
                    'categoria'        => $this->when(
                        $this->gimnasta->relationLoaded('categoria') && $this->gimnasta->categoria,
                        fn () => [
                            'id'     => $this->gimnasta->categoria->id,
                            'nombre' => $this->gimnasta->categoria->nombre,
                        ]
                    ),
                    'conjunto'         => $this->when(
                        $this->gimnasta->relationLoaded('conjunto') && $this->gimnasta->conjunto,
                        fn () => [
                            'id'     => $this->gimnasta->conjunto->id,
                            'nombre' => $this->gimnasta->conjunto->nombre,
                        ]
                    ),
                ]
            ),

            'created_at' => $this->created_at?->toDateTimeString(),
            'updated_at' => $this->updated_at?->toDateTimeString(),
        ];
    }
}
