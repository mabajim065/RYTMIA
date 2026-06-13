<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Mensaje;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class MensajeController extends Controller
{
    // listar mensajes
    public function index(): JsonResponse
    {
        // consulta con emisor y receptor
        $query = Mensaje::with(['emisor', 'receptor']);
        // si no es admin, solo ve sus recibidos
        if (Auth::user()->rol !== 'administrador') {
            $query->where('receptor_id', Auth::id());
        }
        // ordenar del más nuevo al más antiguo
        $mensajes = $query->orderBy('created_at', 'desc')->get();
        // devolver mensajes
        return response()->json($mensajes);
    }
    // enviar mensaje
    public function store(Request $request): JsonResponse
    {
        // validar datos
        $datos = $request->validate([
            'receptor_id' => ['required', 'integer', 'exists:users,id'],
            'asunto'      => ['nullable', 'string', 'max:255'],
            'contenido'   => ['required', 'string'],
        ]);

        // crear mensaje
        $mensaje = Mensaje::create([
            'emisor_id'   => Auth::id(),
            'receptor_id' => $datos['receptor_id'],
            'asunto'      => $datos['asunto'] ?? 'Sin asunto',
            'contenido'   => $datos['contenido'],
        ]);

        // devolver mensaje creado
        return response()->json($mensaje, 201);
    }

    // marcar como leído
    public function marcarLeido(Mensaje $mensaje): JsonResponse
    {
        // solo el receptor puede marcarlo
        if ($mensaje->receptor_id !== Auth::id()) {
            return response()->json(['message' => 'No autorizado'], 403);
        }
        // guardar fecha de lectura
        $mensaje->update(['leido_at' => now()]);
        // devolver mensaje actualizado
        return response()->json($mensaje);
    }
}