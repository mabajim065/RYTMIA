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
            return response()->json(Competicion::with('categorias')->get());
        }

        if ($user->esGimnasta()) {
            $gimnasta = $user->gimnasta;
            if (!$gimnasta || !$gimnasta->conjunto_id) {
                return response()->json([]);
            }
            $categoriaId = $gimnasta->conjunto->categoria_id;
            
            $competiciones = Competicion::whereHas('categorias', function($q) use ($categoriaId) {
                $q->where('categorias.id', $categoriaId);
            })->with('categorias')->get();
            return response()->json($competiciones);
        }

        if ($user->esEntrenadora()) {
            $entrenador = $user->entrenador;
            if (!$entrenador) {
                return response()->json([]);
            }
            $categoriaIds = $entrenador->conjuntos()->pluck('categoria_id')->unique();
            
            $competiciones = Competicion::whereHas('categorias', function($q) use ($categoriaIds) {
                $q->whereIn('categorias.id', $categoriaIds);
            })->with('categorias')->get();
            return response()->json($competiciones);
        }

        return response()->json([]);
    }
}
