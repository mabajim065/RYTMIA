<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Competicion;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Log;
use App\Mail\CompeticionCreadaMail;

class CompeticionController extends Controller
{
    // listar competiciones
    public function index(Request $request)
    {
        // usuario logueado
        $user = $request->user();

        // si es admin, ve todas
        if ($user->esAdministrador()) {
            return response()->json(
                Competicion::with(['conjuntos', 'entrenadoras', 'gimnastas'])->get()
            );
        }

        // si es gimnasta
        if ($user->esGimnasta()) {

            // obtener perfil de gimnasta
            $gimnasta = $user->gimnasta;

            // si no tiene perfil, no ve nada
            if (!$gimnasta) {
                return response()->json([]);
            }

            // conjunto de la gimnasta
            $conjuntoId = $gimnasta->conjunto_id;

            // buscar competiciones de su conjunto o asignadas a ella
            $competiciones = Competicion::where(function($query) use ($conjuntoId, $gimnasta) {

                // competiciones de su conjunto
                if ($conjuntoId) {
                    $query->whereHas('conjuntos', function($q) use ($conjuntoId) {
                        $q->where('conjuntos.id', $conjuntoId);
                    });
                }

                // competiciones asignadas directamente
                $query->orWhereHas('gimnastas', function($q) use ($gimnasta) {
                    $q->where('gimnastas.id', $gimnasta->id);
                });

            })->with(['conjuntos', 'entrenadoras', 'gimnastas'])->get();

            // devolver competiciones
            return response()->json($competiciones);
        }

        // si es entrenadora
        if ($user->esEntrenadora()) {

            // obtener perfil de entrenadora
            $entrenador = $user->entrenador;

            // si no tiene perfil, no ve nada
            if (!$entrenador) {
                return response()->json([]);
            }

            // conjuntos de la entrenadora
            $conjuntoIds = $entrenador->conjuntos()->pluck('conjuntos.id')->unique();

            // buscar competiciones de sus conjuntos o asignadas a ella
            $competiciones = Competicion::where(function($query) use ($conjuntoIds, $entrenador) {

                // competiciones de sus conjuntos
                if ($conjuntoIds->isNotEmpty()) {
                    $query->whereHas('conjuntos', function($q) use ($conjuntoIds) {
                        $q->whereIn('conjuntos.id', $conjuntoIds);
                    });
                }

                // competiciones asignadas directamente
                $query->orWhereHas('entrenadoras', function($q) use ($entrenador) {
                    $q->where('entrenadores.id', $entrenador->id);
                });

            })->with(['conjuntos', 'entrenadoras', 'gimnastas'])->get();

            // devolver competiciones
            return response()->json($competiciones);
        }

        // si no coincide ningún rol
        return response()->json([]);
    }

    // crear competición
    public function store(Request $request)
    {
        // validar datos
        $data = $request->validate([
            'nombre'       => 'required|string',
            'fecha'        => 'required|date',
            'hora'         => 'nullable|date_format:H:i',
            'direccion'    => 'nullable|string',
            'lat'          => 'nullable|numeric',
            'lng'          => 'nullable|numeric',
            'conjuntos'    => 'nullable|array',
            'conjuntos.*'  => 'exists:conjuntos,id',
            'entrenadoras' => 'nullable|array',
            'entrenadoras.*' => 'exists:entrenadores,id',
            'gimnastas'    => 'nullable|array',
            'gimnastas.*'  => 'exists:gimnastas,id',
            'invitados_ids' => 'nullable|array',
            'invitados_ids.*' => 'exists:users,id',
        ]);

        // crear competición
        $competicion = Competicion::create([
            'nombre'    => $data['nombre'],
            'fecha'     => $data['fecha'],
            'hora'      => $data['hora'] ?? null,
            'direccion' => $data['direccion'] ?? null,
            'lat'       => $data['lat'] ?? null,
            'lng'       => $data['lng'] ?? null,
            'tipo'      => 'promesas',
            'estado'    => 'confirmada'
        ]);

        // guardar conjuntos asignados
        if (!empty($data['conjuntos'])) {
            $competicion->conjuntos()->sync($data['conjuntos']);
        }

        // guardar entrenadoras asignadas
        if (!empty($data['entrenadoras'])) {
            $competicion->entrenadoras()->sync($data['entrenadoras']);
        }

        // guardar gimnastas asignadas
        if (!empty($data['gimnastas'])) {
            $competicion->gimnastas()->sync($data['gimnastas']);
        }

        // enviar correos
        try {

            // usuarios de gimnastas directas
            $directGimnastasUsers = User::whereHas('gimnasta', function($q) use ($competicion) {
                $q->whereIn('gimnastas.id', $competicion->gimnastas()->pluck('gimnastas.id'));
            })->whereNotNull('email')->get();

            // usuarios de entrenadoras directas
            $directEntrenadorasUsers = User::whereHas('entrenador', function($q) use ($competicion) {
                $q->whereIn('entrenadores.id', $competicion->entrenadoras()->pluck('entrenadores.id'));
            })->whereNotNull('email')->get();

            // conjuntos asignados
            $conjuntoIds = $competicion->conjuntos()->pluck('conjuntos.id');

            // listas vacías
            $conjuntoGimnastasUsers = collect();
            $conjuntoEntrenadorasUsers = collect();

            // si hay conjuntos
            if ($conjuntoIds->isNotEmpty()) {

                // gimnastas de esos conjuntos
                $conjuntoGimnastasUsers = User::whereHas('gimnasta', function($q) use ($conjuntoIds) {
                    $q->whereIn('conjunto_id', $conjuntoIds);
                })->whereNotNull('email')->get();

                // entrenadoras de esos conjuntos
                $conjuntoEntrenadorasUsers = User::whereHas('entrenador', function($q) use ($conjuntoIds) {
                    $q->whereHas('conjuntos', function($q2) use ($conjuntoIds) {
                        $q2->whereIn('conjuntos.id', $conjuntoIds);
                    });
                })->whereNotNull('email')->get();
            }

            // unir destinatarios sin repetir
            $recipients = $directGimnastasUsers
                ->merge($directEntrenadorasUsers)
                ->merge($conjuntoGimnastasUsers)
                ->merge($conjuntoEntrenadorasUsers)
                ->unique('id');

            // enviar email a cada destinatario
            foreach ($recipients as $user) {
                Mail::to($user->email)->send(new CompeticionCreadaMail($competicion, $user));
            }

        } catch (\Exception $e) {

            // guardar error si falla el correo
            Log::error('error al enviar correos de competición creada: ' . $e->getMessage(), [
                'competicion_id' => $competicion->id,
                'exception' => $e
            ]);
        }

        // devolver competición creada
        return response()->json(
            $competicion->load(['conjuntos', 'entrenadoras', 'gimnastas']),
            201
        );
    }
}