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

    // LISTADO DE COMPETICIONES
    // Devuelve las competiciones filtradas según el rol del usuario actual

    public function index(Request $request)
    {
        $user = $request->user();

        // Rol: Administrador (Ve todas las competiciones)
        if ($user->esAdministrador()) {
            return response()->json(Competicion::with(['conjuntos', 'entrenadoras', 'gimnastas'])->get());
        }

        // Rol: Gimnasta (Ve competiciones de su conjunto o asignadas a ella directamente)
        if ($user->esGimnasta()) {
            $gimnasta = $user->gimnasta;
            if (!$gimnasta) {
                return response()->json([]);
            }
            
            $conjuntoId = $gimnasta->conjunto_id;
            
            $competiciones = Competicion::where(function($query) use ($conjuntoId, $gimnasta) {
                if ($conjuntoId) {
                    $query->whereHas('conjuntos', function($q) use ($conjuntoId) {
                        $q->where('conjuntos.id', $conjuntoId);
                    });
                }
                $query->orWhereHas('gimnastas', function($q) use ($gimnasta) {
                    $q->where('gimnastas.id', $gimnasta->id);
                });
            })->with(['conjuntos', 'entrenadoras', 'gimnastas'])->get();
            
            return response()->json($competiciones);
        }

        // Rol: Entrenadora (Ve competiciones de sus conjuntos o asignadas a ella directamente)
        if ($user->esEntrenadora()) {
            $entrenador = $user->entrenador;
            if (!$entrenador) {
                return response()->json([]);
            }
            
            $conjuntoIds = $entrenador->conjuntos()->pluck('conjuntos.id')->unique();
            
            $competiciones = Competicion::where(function($query) use ($conjuntoIds, $entrenador) {
                if ($conjuntoIds->isNotEmpty()) {
                    $query->whereHas('conjuntos', function($q) use ($conjuntoIds) {
                        $q->whereIn('conjuntos.id', $conjuntoIds);
                    });
                }
                $query->orWhereHas('entrenadoras', function($q) use ($entrenador) {
                    $q->where('entrenadores.id', $entrenador->id);
                });
            })->with(['conjuntos', 'entrenadoras', 'gimnastas'])->get();
            
            return response()->json($competiciones);
        }

        return response()->json([]);
    }


    // CREACIÓN DE COMPETICIÓN
    // Valida datos, guarda el registro, sincroniza relaciones y notifica por email

    public function store(Request $request)
    {
        // 1. Validación de datos de entrada
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
        
        // 2. Creación del registro principal en la base de datos
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
        
        // 3. Sincronización de relaciones (Tablas pivote)
        if (!empty($data['conjuntos'])) {
            $competicion->conjuntos()->sync($data['conjuntos']);
        }
        if (!empty($data['entrenadoras'])) {
            $competicion->entrenadoras()->sync($data['entrenadoras']);
        }
        if (!empty($data['gimnastas'])) {
            $competicion->gimnastas()->sync($data['gimnastas']);
        }

        // 4. Sistema de notificaciones por correo electrónico
        try {
            // Recopilación de gimnastas asignadas directamente
            $directGimnastasUsers = User::whereHas('gimnasta', function($q) use ($competicion) {
                $q->whereIn('gimnastas.id', $competicion->gimnastas()->pluck('gimnastas.id'));
            })->whereNotNull('email')->get();

            // Recopilación de entrenadoras asignadas directamente
            $directEntrenadorasUsers = User::whereHas('entrenador', function($q) use ($competicion) {
                $q->whereIn('entrenadores.id', $competicion->entrenadoras()->pluck('entrenadores.id'));
            })->whereNotNull('email')->get();

            // Recopilación de usuarios a través de los conjuntos asignados
            $conjuntoIds = $competicion->conjuntos()->pluck('conjuntos.id');
            $conjuntoGimnastasUsers = collect();
            $conjuntoEntrenadorasUsers = collect();

            if ($conjuntoIds->isNotEmpty()) {
                $conjuntoGimnastasUsers = User::whereHas('gimnasta', function($q) use ($conjuntoIds) {
                    $q->whereIn('conjunto_id', $conjuntoIds);
                })->whereNotNull('email')->get();

                $conjuntoEntrenadorasUsers = User::whereHas('entrenador', function($q) use ($conjuntoIds) {
                    $q->whereHas('conjuntos', function($q2) use ($conjuntoIds) {
                        $q2->whereIn('conjuntos.id', $conjuntoIds);
                    });
                })->whereNotNull('email')->get();
            }

            // Unificación de destinatarios (evitando envíos duplicados al mismo ID)
            $recipients = $directGimnastasUsers
                ->merge($directEntrenadorasUsers)
                ->merge($conjuntoGimnastasUsers)
                ->merge($conjuntoEntrenadorasUsers)
                ->unique('id');

            // Envío en bucle
            foreach ($recipients as $user) {
                Mail::to($user->email)->send(new CompeticionCreadaMail($competicion, $user));
            }
            
        } catch (\Exception $e) {
            // Registro de errores si falla el envío de correos
            Log::error('Error al enviar correos de competición creada a las gimnastas y entrenadoras convocadas: ' . $e->getMessage(), [
                'competicion_id' => $competicion->id,
                'exception' => $e
            ]);
        }
        
        // 5. Respuesta de éxito
        return response()->json($competicion->load(['conjuntos', 'entrenadoras', 'gimnastas']), 201);
    }
}