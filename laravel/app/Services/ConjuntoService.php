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
    // listar conjuntos
    public function listar(array $filtros): LengthAwarePaginator
    {
        // preparamos consulta
        $query = Conjunto::query()
            ->with(['club', 'categoria', 'entrenadores.user'])
            ->withCount('gimnastas')
            ->orderBy('nombre');

        // filtrar por club
        if (! empty($filtros['club_id'])) {
            $query->where('club_id', $filtros['club_id']);
        }
        // filtrar por categoría
        if (! empty($filtros['categoria_id'])) {
            $query->where('categoria_id', $filtros['categoria_id']);
        }
        // filtrar por entrenadora
        if (! empty($filtros['entrenador_id'])) {
            $query->whereHas('entrenadores', function ($q) use ($filtros) {
                $q->where('entrenadores.id', $filtros['entrenador_id']);
            });
        }
        // buscar por nombre
        if (! empty($filtros['search'])) {
            $s = '%' . $filtros['search'] . '%';
            $query->where('nombre', 'like', $s);
        }
        return $query->paginate(15);
    }

    // listar por club
    public function listarPorClub(int $clubId): Collection
    {
        // nos da los conjuntos del club
        return Conjunto::with(['categoria'])
            ->where('club_id', $clubId)
            ->orderBy('nombre')
            ->get();
    }

    // crear conjunto
    public function crear(array $datos): Conjunto
    {
        // guardar conjunto
        $conjunto = Conjunto::create([
            'nombre'       => $datos['nombre'],
            'club_id'      => $datos['club_id'],
            'categoria_id' => $datos['categoria_id'],
            'horario'      => $datos['horario'] ?? null,
        ]);

        return $conjunto->load(['club', 'categoria', 'gimnastas', 'entrenadores.user']);
    }

    // actualizar conjunto
    public function actualizar(Conjunto $conjunto, array $datos): Conjunto
    {
        // actualizar solo campos enviados
        $conjunto->update(array_filter([
            'nombre'       => $datos['nombre']       ?? null,
            'club_id'      => $datos['club_id']      ?? null,
            'categoria_id' => $datos['categoria_id'] ?? null,
            'horario'      => $datos['horario']      ?? null,
        ], fn ($v) => ! is_null($v)));

        return $conjunto->fresh(['club', 'categoria', 'gimnastas', 'entrenadores.user']);
    }

    // eliminar conjunto
    public function eliminar(Conjunto $conjunto, bool $force = false): void
    {
        // contar gimnastas asignadas
        $totalGimnastas = $conjunto->gimnastas()->count();

        // si tiene gimnastas y no se fuerza, da error
        if ($totalGimnastas > 0 && ! $force) {
            throw ValidationException::withMessages([
                'conjunto' => [
                    "No se puede eliminar: el conjunto tiene {$totalGimnastas} gimnasta(s) asignada(s). "
                    . "Usa ?force=1 para eliminar y desasignar a todas las gimnastas.",
                ],
            ]);
        }

        // borrar de forma segura
        DB::transaction(function () use ($conjunto) {

            // quitar gimnastas del conjunto
            $conjunto->gimnastas()->update(['conjunto_id' => null]);

            // quitar entrenadoras del conjunto
            $conjunto->entrenadores()->detach();

            // borrar conjunto
            $conjunto->delete();
        });
    }

    // asignar gimnasta
    public function asignarGimnasta(Conjunto $conjunto, int $gimnastaId): Gimnasta
    {
        // buscar gimnasta
        $gimnasta = Gimnasta::findOrFail($gimnastaId);

        // si ya está asignada, devolverla
        if ($gimnasta->conjunto_id === $conjunto->id) {
            return $gimnasta->load(['user', 'categoria', 'conjunto']);
        }

        // comprobamos q este en la misma categoria
        if ($gimnasta->categoria_id !== $conjunto->categoria_id) {
            throw ValidationException::withMessages([
                'gimnasta' => [
                    "La gimnasta pertenece a la categoría «{$gimnasta->categoria?->nombre}» "
                    . "pero el conjunto es de categoría «{$conjunto->categoria?->nombre}».",
                ],
            ]);
        }

        // asignar conjunto
        $gimnasta->update(['conjunto_id' => $conjunto->id]);

        return $gimnasta->fresh(['user', 'categoria', 'conjunto.club']);
    }

    // quitar gimnasta
    public function desasignarGimnasta(Conjunto $conjunto, int $gimnastaId): Gimnasta
    {
        // buscar gimnasta
        $gimnasta = Gimnasta::findOrFail($gimnastaId);

        // comprobar que pertenece al conjunto
        if ($gimnasta->conjunto_id !== $conjunto->id) {
            throw ValidationException::withMessages([
                'gimnasta' => ['Esta gimnasta no pertenece al conjunto indicado.'],
            ]);
        }

        // quitar conjunto
        $gimnasta->update(['conjunto_id' => null]);

        return $gimnasta->fresh(['user', 'categoria']);
    }

    // sincronizar gimnastas
    public function sincronizarGimnastas(Conjunto $conjunto, array $gimnastaIds): Collection
    {
        // sincronizar gimnastas en un conjunto 
        return DB::transaction(function () use ($conjunto, $gimnastaIds) {

            // quitar todas las gimnastas actuales
            $conjunto->gimnastas()->update(['conjunto_id' => null]);

            // si no hay ids, devolver vacío
            if (empty($gimnastaIds)) {
                return collect();
            }

            // buscar gimnastas nuevas
            $gimnastas = Gimnasta::whereIn('id', $gimnastaIds)->get();

            // detectar categoría incorrecta
            $invalidas = $gimnastas->filter(fn ($g) => $g->categoria_id !== $conjunto->categoria_id);

            // si hay inválidas, mostrar error
            if ($invalidas->isNotEmpty()) {
                $nombres = $invalidas->map(fn ($g) => $g->user?->nombre ?? "ID {$g->id}")->implode(', ');

                throw ValidationException::withMessages([
                    'gimnastas' => [
                        "Las siguientes gimnastas no pertenecen a la categoría del conjunto: {$nombres}.",
                    ],
                ]);
            }

            // asignar nuevas gimnastas
            Gimnasta::whereIn('id', $gimnastaIds)->update(['conjunto_id' => $conjunto->id]);

            // devolver gimnastas del conjunto
            return $conjunto->gimnastas()->with('user')->get();
        });
    }

    // asignar entrenadora
    public function asignarEntrenadora(Conjunto $conjunto, int $entrenadorId): void
    {
        // añade sin quitar anteriores
        $conjunto->entrenadores()->syncWithoutDetaching([$entrenadorId]);
    }

    // quitar entrenadora
    public function desasignarEntrenadora(Conjunto $conjunto, int $entrenadorId): void
    {
        // quita relación
        $conjunto->entrenadores()->detach($entrenadorId);
    }

    // sincronizar entrenadoras
    public function sincronizarEntrenadores(Conjunto $conjunto, array $entrenadorIds): void
    {
        // reemplaza lista completa
        $conjunto->entrenadores()->sync($entrenadorIds);
    }
}