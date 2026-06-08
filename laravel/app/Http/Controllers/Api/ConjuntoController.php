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


    // GESTIÓN BÁSICA DE CONJUNTOS (CRUD)
    // Listado, creación, visualización, actualización y borrado

    // GET /api/conjuntos (Filtros: club_id, categoria_id, search)
    public function index(Request $request): AnonymousResourceCollection
    {
        $conjuntos = $this->service->listar(
            $request->only(['club_id', 'categoria_id', 'entrenador_id', 'search'])
        );

        return ConjuntoResource::collection($conjuntos);
    }

    // GET /api/conjuntos/por-club/{club} (Sin paginación, ideal para selects)
    public function porClub(int $clubId): AnonymousResourceCollection
    {
        $conjuntos = $this->service->listarPorClub($clubId);

        return ConjuntoResource::collection($conjuntos);
    }

    // POST /api/conjuntos
    public function store(Request $request): JsonResponse
    {
        $datos = $request->validate([
            'nombre'       => ['required', 'string', 'max:45'],
            'club_id'      => ['required', 'integer', 'exists:clubs,id'],
            'categoria_id' => ['required', 'integer', 'exists:categorias,id'],
            'horario'      => ['nullable', 'string', 'max:255'],
        ]);

        $conjunto = $this->service->crear($datos);

        return (new ConjuntoResource($conjunto))
            ->response()
            ->setStatusCode(201);
    }

    // GET /api/conjuntos/{conjunto}
    public function show(Conjunto $conjunto): ConjuntoResource
    {
        $conjunto->loadMissing([
            'club',
            'categoria',
            'gimnastas.user',
            'gimnastas.categoria',
            'entrenadores.user',
        ]);

        return new ConjuntoResource($conjunto);
    }

    // PUT/PATCH /api/conjuntos/{conjunto}
    public function update(Request $request, Conjunto $conjunto): ConjuntoResource
    {
        $datos = $request->validate([
            'nombre'       => ['sometimes', 'string', 'max:45'],
            'club_id'      => ['sometimes', 'integer', 'exists:clubs,id'],
            'categoria_id' => ['sometimes', 'integer', 'exists:categorias,id'],
            'horario'      => ['sometimes', 'nullable', 'string', 'max:255'],
        ]);

        $conjunto = $this->service->actualizar($conjunto, $datos);

        return new ConjuntoResource($conjunto);
    }

    // DELETE /api/conjuntos/{conjunto} (Usa ?force=1 para eliminar aunque tenga gimnastas)
    public function destroy(Request $request, Conjunto $conjunto): JsonResponse
    {
        $this->service->eliminar($conjunto, (bool) $request->query('force', false));

        return response()->json(['message' => 'Conjunto eliminado correctamente.']);
    }


    // VINCULACIÓN DE GIMNASTAS
    // Añadir, quitar o sincronizar masivamente a las gimnastas del conjunto

    // POST /api/conjuntos/{conjunto}/gimnastas
    public function asignarGimnasta(Request $request, Conjunto $conjunto): JsonResponse
    {
        $request->validate([
            'gimnasta_id' => ['required', 'integer', 'exists:gimnastas,id'],
        ]);

        $conjunto->loadMissing('categoria');
        $gimnasta = $this->service->asignarGimnasta($conjunto, $request->gimnasta_id);

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

    // DELETE /api/conjuntos/{conjunto}/gimnastas/{gimnasta}
    public function desasignarGimnasta(Conjunto $conjunto, int $gimnastaId): JsonResponse
    {
        $gimnasta = $this->service->desasignarGimnasta($conjunto, $gimnastaId);

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

    // PUT /api/conjuntos/{conjunto}/gimnastas/sync
    public function sincronizarGimnastas(Request $request, Conjunto $conjunto): JsonResponse
    {
        $request->validate([
            'gimnasta_ids'   => ['required', 'array'],
            'gimnasta_ids.*' => ['integer', 'exists:gimnastas,id'],
        ]);

        $conjunto->loadMissing('categoria');
        $gimnastas = $this->service->sincronizarGimnastas($conjunto, $request->gimnasta_ids);

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


    // VINCULACIÓN DE ENTRENADORAS
    // Añadir, quitar o sincronizar masivamente a las responsables del conjunto

    // POST /api/conjuntos/{conjunto}/entrenadores
    public function asignarEntrenadora(Request $request, Conjunto $conjunto): JsonResponse
    {
        $request->validate([
            'entrenador_id' => ['required', 'integer', 'exists:entrenadores,id'],
        ]);

        $this->service->asignarEntrenadora($conjunto, $request->entrenador_id);

        $conjunto->load('entrenadores.user');

        return response()->json([
            'message'      => "Entrenadora asignada al conjunto «{$conjunto->nombre}».",
            'entrenadores' => $conjunto->entrenadores->map(fn ($e) => [
                'id'        => $e->id,
                'nombre'    => $e->user?->nombre,
                'apellidos' => $e->user?->apellidos,
            ]),
        ]);
    }

    // DELETE /api/conjuntos/{conjunto}/entrenadores/{entrenador}
    public function desasignarEntrenadora(Conjunto $conjunto, int $entrenadorId): JsonResponse
    {
        $this->service->desasignarEntrenadora($conjunto, $entrenadorId);

        return response()->json([
            'message' => 'Entrenadora desvinculada del conjunto.',
        ]);
    }

    // PUT /api/conjuntos/{conjunto}/entrenadores/sync
    public function sincronizarEntrenadores(Request $request, Conjunto $conjunto): JsonResponse
    {
        $request->validate([
            'entrenador_ids'   => ['required', 'array'],
            'entrenador_ids.*' => ['integer', 'exists:entrenadores,id'],
        ]);

        $this->service->sincronizarEntrenadores($conjunto, $request->entrenador_ids);
        $conjunto->load('entrenadores.user');

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