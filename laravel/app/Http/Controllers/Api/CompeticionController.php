<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Competicion;
use Illuminate\Http\Request;

class CompeticionController extends Controller
{
    public function index(Request $request)
    {
        $user = $request->user();

        if ($user->esAdministrador()) {
            return response()->json(Competicion::with('conjuntos')->get());
        }

        if ($user->esGimnasta()) {
            $gimnasta = $user->gimnasta;
            if (!$gimnasta || !$gimnasta->conjunto_id) {
                return response()->json([]);
            }
            $conjuntoId = $gimnasta->conjunto_id;
            
            $competiciones = Competicion::whereHas('conjuntos', function($q) use ($conjuntoId) {
                $q->where('conjuntos.id', $conjuntoId);
            })->with('conjuntos')->get();
            return response()->json($competiciones);
        }

        if ($user->esEntrenadora()) {
            $entrenador = $user->entrenador;
            if (!$entrenador) {
                return response()->json([]);
            }
            $conjuntoIds = $entrenador->conjuntos()->pluck('conjuntos.id')->unique();
            
            $competiciones = Competicion::whereHas('conjuntos', function($q) use ($conjuntoIds) {
                $q->whereIn('conjuntos.id', $conjuntoIds);
            })->with('conjuntos')->get();
            return response()->json($competiciones);
        }

        return response()->json([]);
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'nombre' => 'required|string',
            'fecha' => 'required|date',
            'conjunto_id' => 'required|exists:conjuntos,id',
        ]);
        
        $competicion = Competicion::create([
            'nombre' => $data['nombre'],
            'fecha' => $data['fecha'],
            'tipo' => 'promesas',
            'estado' => 'confirmada'
        ]);
        
        $competicion->conjuntos()->sync([$data['conjunto_id']]);
        
        return response()->json($competicion, 201);
    }
}
