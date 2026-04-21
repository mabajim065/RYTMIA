<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Mensaje;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class MensajeController extends Controller
{
    /**
     * GET /api/mensajes
     * Obtener mensajes recibidos por el usuario autenticado.
     */
    public function index(): JsonResponse
    {
        $query = Mensaje::with(['emisor', 'receptor']);

        if (Auth::user()->rol !== 'administrador') {
            $query->where('receptor_id', Auth::id());
        }

        $mensajes = $query->orderBy('created_at', 'desc')->get();

        return response()->json($mensajes);
    }

    /**
     * POST /api/mensajes
     * Enviar un mensaje.
     */
    public function store(Request $request): JsonResponse
    {
        $datos = $request->validate([
            'receptor_id' => ['required', 'integer', 'exists:users,id'],
            'asunto'      => ['nullable', 'string', 'max:255'],
            'contenido'   => ['required', 'string'],
        ]);

        $mensaje = Mensaje::create([
            'emisor_id'   => Auth::id(),
            'receptor_id' => $datos['receptor_id'],
            'asunto'      => $datos['asunto'] ?? 'Sin asunto',
            'contenido'   => $datos['contenido'],
        ]);

        return response()->json($mensaje, 201);
    }

    /**
     * PATCH /api/mensajes/{mensaje}/marcar-leido
     */
    public function marcarLeido(Mensaje $mensaje): JsonResponse
    {
        if ($mensaje->receptor_id !== Auth::id()) {
            return response()->json(['message' => 'No autorizado'], 403);
        }

        $mensaje->update(['leido_at' => now()]);

        return response()->json($mensaje);
    }
}
