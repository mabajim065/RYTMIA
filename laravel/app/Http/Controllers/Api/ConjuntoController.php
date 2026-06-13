<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Resources\ConjuntoResource;
use App\Models\Conjunto;
use App\Services\ConjuntoService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;

class ConjuntoController extends Controller
{
    public function __construct(private readonly ConjuntoService $service) {}

    // listar conjuntos
    public function index(Request $request): AnonymousResourceCollection
    {
        // obtiene conjuntos con filtros
        $conjuntos = $this->service->listar(
            $request->only(['club_id', 'categoria_id', 'entrenador_id', 'search'])
        );
        return ConjuntoResource::collection($conjuntos);
    }

    // listar conjuntos de un club
    public function porClub(int $clubId): AnonymousResourceCollection
    {
        // busca conjuntos por club
        $conjuntos = $this->service->listarPorClub($clubId);
        return ConjuntoResource::collection($conjuntos);
    }

    // crear conjunto
    public function store(Request $request): JsonResponse
    {
        //comprobar datos
        $datos = $request->validate([
            'nombre'       => ['required', 'string', 'max:45'],
            'club_id'      => ['required', 'integer', 'exists:clubs,id'],
            'categoria_id' => ['required', 'integer', 'exists:categorias,id'],
            'horario'      => ['nullable', 'string', 'max:255'],
        ]);

        // crea conjunto
        $conjunto = $this->service->crear($datos);

        return (new ConjuntoResource($conjunto))
            ->response()
            ->setStatusCode(201);
    }

    // ver un conjunto
    public function show(Conjunto $conjunto): ConjuntoResource
    {
        // carga datos relacionados
        $conjunto->loadMissing([
            'club',
            'categoria',
            'gimnastas.user',
            'gimnastas.categoria',
            'entrenadores.user',
        ]);
        return new ConjuntoResource($conjunto);
    }

    // actualizar conjunto
    public function update(Request $request, Conjunto $conjunto): ConjuntoResource
    {
        // valida datos opcionales
        $datos = $request->validate([
            'nombre'       => ['sometimes', 'string', 'max:45'],
            'club_id'      => ['sometimes', 'integer', 'exists:clubs,id'],
            'categoria_id' => ['sometimes', 'integer', 'exists:categorias,id'],
            'horario'      => ['sometimes', 'nullable', 'string', 'max:255'],
        ]);

        // actualiza conjunto
        $conjunto = $this->service->actualizar($conjunto, $datos);
        return new ConjuntoResource($conjunto);
    }

    // eliminar conjunto
    public function destroy(Request $request, Conjunto $conjunto): JsonResponse
    {
        // elimina o fuerza eliminación
        $this->service->eliminar($conjunto, (bool) $request->query('force', false));
        return response()->json(['message' => 'Conjunto eliminado correctamente.']);
    }

    // asignar gimnasta
    public function asignarGimnasta(Request $request, Conjunto $conjunto): JsonResponse
    {
        // valida gimnasta
        $request->validate([
            'gimnasta_id' => ['required', 'integer', 'exists:gimnastas,id'],
        ]);

        // carga categoría del conjunto
        $conjunto->loadMissing('categoria');

        // asigna gimnasta
        $gimnasta = $this->service->asignarGimnasta($conjunto, $request->gimnasta_id);

        // devuelve resultado
        return response()->json([
            'message'  => "Gimnasta asignada correctamente al conjunto «{$conjunto->nombre}».",
            'gimnasta' => [
                'id'              => $gimnasta->id,
                'nombre'          => $gimnasta->user?->nombre,
                'apellidos'       => $gimnasta->user?->apellidos,
                'numero_licencia' => $gimnasta->numero_licencia,
                'conjunto'        => [
                    'id'     => $gimnasta->conjunto?->id,
                    'nombre' => $gimnasta->conjunto?->nombre,
                ],
            ],
        ]);
    }

    // quitar gimnasta
    public function desasignarGimnasta(Conjunto $conjunto, int $gimnastaId): JsonResponse
    {
        // desasigna gimnasta
        $gimnasta = $this->service->desasignarGimnasta($conjunto, $gimnastaId);

        // devuelve resultado
        return response()->json([
            'message'  => "Gimnasta retirada del conjunto «{$conjunto->nombre}».",
            'gimnasta' => [
                'id'        => $gimnasta->id,
                'nombre'    => $gimnasta->user?->nombre,
                'apellidos' => $gimnasta->user?->apellidos,
                'conjunto'  => null,
            ],
        ]);
    }

    // sincronizar gimnastas
    public function sincronizarGimnastas(Request $request, Conjunto $conjunto): JsonResponse
    {
        // valida lista de gimnastas
        $request->validate([
            'gimnasta_ids'   => ['required', 'array'],
            'gimnasta_ids.*' => ['integer', 'exists:gimnastas,id'],
        ]);

        // carga categoría del conjunto
        $conjunto->loadMissing('categoria');

        // reemplaza gimnastas del conjunto
        $gimnastas = $this->service->sincronizarGimnastas($conjunto, $request->gimnasta_ids);

        // devuelve resultado
        return response()->json([
            'message'    => "Asignación sincronizada. Total gimnastas: {$gimnastas->count()}.",
            'total'      => $gimnastas->count(),
            'gimnastas'  => $gimnastas->map(fn ($g) => [
                'id'        => $g->id,
                'nombre'    => $g->user?->nombre,
                'apellidos' => $g->user?->apellidos,
            ]),
        ]);
    }

    // asignar entrenadora
    public function asignarEntrenadora(Request $request, Conjunto $conjunto): JsonResponse
    {
        // valida entrenadora
        $request->validate([
            'entrenador_id' => ['required', 'integer', 'exists:entrenadores,id'],
        ]);

        // asigna entrenadora
        $this->service->asignarEntrenadora($conjunto, $request->entrenador_id);

        // carga entrenadoras
        $conjunto->load('entrenadores.user');

        // devuelve resultado
        return response()->json([
            'message'      => "Entrenadora asignada al conjunto «{$conjunto->nombre}».",
            'entrenadores' => $conjunto->entrenadores->map(fn ($e) => [
                'id'        => $e->id,
                'nombre'    => $e->user?->nombre,
                'apellidos' => $e->user?->apellidos,
            ]),
        ]);
    }

    // quitar entrenadora
    public function desasignarEntrenadora(Conjunto $conjunto, int $entrenadorId): JsonResponse
    {
        // desasigna entrenadora
        $this->service->desasignarEntrenadora($conjunto, $entrenadorId);

        // devuelve mensaje
        return response()->json([
            'message' => 'Entrenadora desvinculada del conjunto.',
        ]);
    }

    // sincronizar entrenadoras
    public function sincronizarEntrenadores(Request $request, Conjunto $conjunto): JsonResponse
    {
        // valida lista de entrenadoras
        $request->validate([
            'entrenador_ids'   => ['required', 'array'],
            'entrenador_ids.*' => ['integer', 'exists:entrenadores,id'],
        ]);

        // reemplaza entrenadoras del conjunto
        $this->service->sincronizarEntrenadores($conjunto, $request->entrenador_ids);

        // carga entrenadoras
        $conjunto->load('entrenadores.user');

        // devuelve resultado
        return response()->json([
            'message'      => 'Entrenadoras del conjunto actualizadas.',
            'entrenadores' => $conjunto->entrenadores->map(fn ($e) => [
                'id'        => $e->id,
                'nombre'    => $e->user?->nombre,
                'apellidos' => $e->user?->apellidos,
            ]),
        ]);
    }
}