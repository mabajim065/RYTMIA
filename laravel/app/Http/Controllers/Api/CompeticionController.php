<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Competicion;
use App\Services\GoogleCalendarService;
use App\Models\User;
use Illuminate\Http\Request;


class CompeticionController extends Controller
{
    public function index(Request $request)
    {
        $user = $request->user();

        if ($user->esAdministrador()) {
            return response()->json(Competicion::with(['conjuntos', 'entrenadoras', 'gimnastas'])->get());
        }

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

    public function store(Request $request)
    {
        $data = $request->validate([
            'nombre'    => 'required|string',
            'fecha'     => 'required|date',
            'direccion' => 'nullable|string',
            'lat'       => 'nullable|numeric',
            'lng'       => 'nullable|numeric',
            'conjuntos' => 'nullable|array',
            'conjuntos.*' => 'exists:conjuntos,id',
            'entrenadoras' => 'nullable|array',
            'entrenadoras.*' => 'exists:entrenadores,id',
            'gimnastas' => 'nullable|array',
            'gimnastas.*' => 'exists:gimnastas,id',
            'invitados_ids' => 'nullable|array', // IDs de gimnastas y entrenadoras específicas a invitar, opcional
            'invitados_ids.*' => 'exists:users,id',
        ]);
        
        $competicion = Competicion::create([
            'nombre'    => $data['nombre'],
            'fecha'     => $data['fecha'],
            'direccion' => $data['direccion'] ?? null,
            'lat'       => $data['lat'] ?? null,
            'lng'       => $data['lng'] ?? null,
            'tipo'      => 'promesas',
            'estado'    => 'confirmada'
        ]);
        
        if (!empty($data['conjuntos'])) {
            $competicion->conjuntos()->sync($data['conjuntos']);
        }
        if (!empty($data['entrenadoras'])) {
            $competicion->entrenadoras()->sync($data['entrenadoras']);
        }
        if (!empty($data['gimnastas'])) {
            $competicion->gimnastas()->sync($data['gimnastas']);
        }

        // Sincronizar con Google Calendar de los usuarios involucrados
        $this->syncWithGoogleCalendar($competicion, $request);
        
        return response()->json($competicion->load(['conjuntos', 'entrenadoras', 'gimnastas']), 201);
    }

    /**
     * Sincroniza la competición con el calendario de Google de todos los usuarios
     * (gimnastas y entrenadoras) asociados a los conjuntos de la competición.
     */
    protected function syncWithGoogleCalendar(Competicion $competicion, Request $request)
    {
        $calendarService = new GoogleCalendarService();
        $admin = auth()->user();
        
        // Si el admin no tiene token de Google, no podemos crear el evento en su calendario
        if (!$admin || !$admin->google_token) {
            return;
        }

        $emailsInvitados = [];

        // Obtener emails de las gimnastas asignadas directamente
        $userGimnastasEmails = User::whereHas('gimnasta', function($q) use ($competicion) {
            $q->whereIn('gimnastas.id', $competicion->gimnastas()->pluck('gimnastas.id'));
        })->whereNotNull('email')->pluck('email')->toArray();

        // Obtener emails de las entrenadoras asignadas directamente
        $userEntrenadorasEmails = User::whereHas('entrenador', function($q) use ($competicion) {
            $q->whereIn('entrenadores.id', $competicion->entrenadoras()->pluck('entrenadores.id'));
        })->whereNotNull('email')->pluck('email')->toArray();
        
        // Mantener compatibilidad si hubiese conjuntos asignados
        $conjuntoIds = $competicion->conjuntos()->pluck('conjuntos.id');
        $conjuntoGimnastasEmails = User::whereHas('gimnasta', function($q) use ($conjuntoIds) {
            $q->whereIn('conjunto_id', $conjuntoIds);
        })->whereNotNull('email')->pluck('email')->toArray();

        // Obtener emails de las entrenadoras asociadas a los conjuntos asignados
        $conjuntoEntrenadorasEmails = User::whereHas('entrenador', function($q) use ($conjuntoIds) {
            $q->whereHas('conjuntos', function($q2) use ($conjuntoIds) {
                $q2->whereIn('conjuntos.id', $conjuntoIds);
            });
        })->whereNotNull('email')->pluck('email')->toArray();

        $emailsInvitados = array_merge($userGimnastasEmails, $userEntrenadorasEmails, $conjuntoGimnastasEmails, $conjuntoEntrenadorasEmails);
        $emailsInvitados = array_unique($emailsInvitados);

        // Crear el evento y añadir asistentes
        if (!empty($emailsInvitados)) {
            $calendarService->createEventWithAttendees($admin, $competicion, $emailsInvitados);
        }
    }
}
