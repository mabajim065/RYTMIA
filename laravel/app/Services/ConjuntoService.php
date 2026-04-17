<?php

namespace App\Services;

use App\Models\Conjunto;
use App\Models\Gimnasta;
use App\Models\Entrenador;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;

class ConjuntoService
{
    // ── CRUD Conjuntos ───────────────────────────────────────────

    /**
     * Listado paginado con filtros opcionales: club_id, categoria_id, search
     */
    public function listar(array $filtros): LengthAwarePaginator
    {
        $query = Conjunto::query()
            ->with(['club', 'categoria', 'entrenadores.user'])
            ->withCount('gimnastas')
            ->orderBy('nombre');

        if (! empty($filtros['club_id'])) {
            $query->where('club_id', $filtros['club_id']);
        }

        if (! empty($filtros['categoria_id'])) {
            $query->where('categoria_id', $filtros['categoria_id']);
        }

        if (! empty($filtros['entrenador_id'])) {
            $query->whereHas('entrenadores', function ($q) use ($filtros) {
                $q->where('entrenadores.id', $filtros['entrenador_id']);
            });
        }

        if (! empty($filtros['search'])) {
            $s = '%' . $filtros['search'] . '%';
            $query->where('nombre', 'like', $s);
        }

        return $query->paginate(15);
    }

    /**
     * Devuelve todos los conjuntos de un club sin paginar (para selects).
     */
    public function listarPorClub(int $clubId): Collection
    {
        return Conjunto::with(['categoria'])
            ->where('club_id', $clubId)
            ->orderBy('nombre')
            ->get();
    }

    /**
     * Crear nuevo conjunto.
     */
    public function crear(array $datos): Conjunto
    {
        $conjunto = Conjunto::create([
            'nombre'       => $datos['nombre'],
            'club_id'      => $datos['club_id'],
            'categoria_id' => $datos['categoria_id'],
            'horario'      => $datos['horario'] ?? null,
        ]);

        return $conjunto->load(['club', 'categoria', 'gimnastas', 'entrenadores.user']);
    }

    /**
     * Actualizar conjunto.
     */
    public function actualizar(Conjunto $conjunto, array $datos): Conjunto
    {
        $conjunto->update(array_filter([
            'nombre'       => $datos['nombre']       ?? null,
            'club_id'      => $datos['club_id']      ?? null,
            'categoria_id' => $datos['categoria_id'] ?? null,
            'horario'      => $datos['horario']      ?? null,
        ], fn ($v) => ! is_null($v)));

        return $conjunto->fresh(['club', 'categoria', 'gimnastas', 'entrenadores.user']);
    }

    /**
     * Eliminar conjunto (solo si no tiene gimnastas activas).
     */
    public function eliminar(Conjunto $conjunto, bool $force = false): void
    {
        $totalGimnastas = $conjunto->gimnastas()->count();

        if ($totalGimnastas > 0 && ! $force) {
            throw ValidationException::withMessages([
                'conjunto' => [
                    "No se puede eliminar: el conjunto tiene {$totalGimnastas} gimnasta(s) asignada(s). "
                    . "Usa ?force=1 para eliminar y desasignar a todas las gimnastas.",
                ],
            ]);
        }

        DB::transaction(function () use ($conjunto) {
            // Desasignar gimnastas (poner conjunto_id a null)
            $conjunto->gimnastas()->update(['conjunto_id' => null]);
            // Quitar entrenadoras de la pivote
            $conjunto->entrenadores()->detach();
            // Borrar
            $conjunto->delete();
        });
    }

    // ── Asignación de Gimnastas ──────────────────────────────────

    /**
     * Asignar una gimnasta a un conjunto.
     * Valida:
     *  - La gimnasta no tenga ya ese mismo conjunto
     *  - La categoría de la gimnasta coincida con la del conjunto
     */
    public function asignarGimnasta(Conjunto $conjunto, int $gimnastaId): Gimnasta
    {
        $gimnasta = Gimnasta::findOrFail($gimnastaId);

        // Ya está en este conjunto → no hacer nada
        if ($gimnasta->conjunto_id === $conjunto->id) {
            return $gimnasta->load(['user', 'categoria', 'conjunto']);
        }

        // Validar que la categoría coincide
        if ($gimnasta->categoria_id !== $conjunto->categoria_id) {
            throw ValidationException::withMessages([
                'gimnasta' => [
                    "La gimnasta pertenece a la categoría «{$gimnasta->categoria?->nombre}» "
                    . "pero el conjunto es de categoría «{$conjunto->categoria?->nombre}».",
                ],
            ]);
        }

        $gimnasta->update(['conjunto_id' => $conjunto->id]);

        return $gimnasta->fresh(['user', 'categoria', 'conjunto.club']);
    }

    /**
     * Desasignar (quitar) una gimnasta de su conjunto actual.
     */
    public function desasignarGimnasta(Conjunto $conjunto, int $gimnastaId): Gimnasta
    {
        $gimnasta = Gimnasta::findOrFail($gimnastaId);

        if ($gimnasta->conjunto_id !== $conjunto->id) {
            throw ValidationException::withMessages([
                'gimnasta' => ['Esta gimnasta no pertenece al conjunto indicado.'],
            ]);
        }

        $gimnasta->update(['conjunto_id' => null]);

        return $gimnasta->fresh(['user', 'categoria']);
    }

    /**
     * Reemplazar todas las gimnastas de un conjunto de una sola vez.
     * Útil para el modo "guardar selección completa".
     */
    public function sincronizarGimnastas(Conjunto $conjunto, array $gimnastaIds): Collection
    {
        return DB::transaction(function () use ($conjunto, $gimnastaIds) {
            // Primero: desasignar las actuales
            $conjunto->gimnastas()->update(['conjunto_id' => null]);

            if (empty($gimnastaIds)) {
                return collect();
            }

            // Validar categorías
            $gimnastas = Gimnasta::whereIn('id', $gimnastaIds)->get();
            $invalidas = $gimnastas->filter(
                fn ($g) => $g->categoria_id !== $conjunto->categoria_id
            );

            if ($invalidas->isNotEmpty()) {
                $nombres = $invalidas->map(fn ($g) => $g->user?->nombre ?? "ID {$g->id}")->implode(', ');
                throw ValidationException::withMessages([
                    'gimnastas' => [
                        "Las siguientes gimnastas no pertenecen a la categoría del conjunto: {$nombres}.",
                    ],
                ]);
            }

            // Asignar todas al conjunto
            Gimnasta::whereIn('id', $gimnastaIds)->update(['conjunto_id' => $conjunto->id]);

            return $conjunto->gimnastas()->with('user')->get();
        });
    }

    // ── Asignación de Entrenadoras ───────────────────────────────

    /**
     * Asignar entrenadora al conjunto (pivote conjunto_entrenador).
     */
    public function asignarEntrenadora(Conjunto $conjunto, int $entrenadorId): void
    {
        $entrenador = Entrenador::findOrFail($entrenadorId);

        // sync sin detach = no borrar las ya existentes
        $conjunto->entrenadores()->syncWithoutDetaching([$entrenadorId]);
    }

    /**
     * Quitar entrenadora del conjunto.
     */
    public function desasignarEntrenadora(Conjunto $conjunto, int $entrenadorId): void
    {
        $conjunto->entrenadores()->detach($entrenadorId);
    }

    /**
     * Reemplazar todas las entrenadoras de un conjunto.
     */
    public function sincronizarEntrenadores(Conjunto $conjunto, array $entrenadorIds): void
    {
        $conjunto->entrenadores()->sync($entrenadorIds);
    }
}
