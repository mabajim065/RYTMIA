<?php

namespace App\Services;

use App\Models\User;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;

class UserService
{
    /**
     * Listado paginado con filtros.
     */
    public function listar(array $filtros): LengthAwarePaginator
    {
        $query = User::query()
            ->with(['entrenador.club', 'gimnasta.club'])
            ->orderBy('apellidos');

        if (isset($filtros['rol'])) {
            $query->where('rol', $filtros['rol']);
        }

        if (isset($filtros['activo'])) {
            $query->where('activo', filter_var($filtros['activo'], FILTER_VALIDATE_BOOLEAN));
        }

        if (! empty($filtros['search'])) {
            $s = '%' . $filtros['search'] . '%';
            $query->where(function ($q) use ($s) {
                $q->where('nombre',    'like', $s)
                  ->orWhere('apellidos', 'like', $s)
                  ->orWhere('dni',       'like', $s)
                  ->orWhere('email',     'like', $s);
            });
        }

        return $query->paginate(15);
    }

    /**
     * Crear usuario y su perfil de rol.
     */
    public function crear(array $datos): User
    {
        return DB::transaction(function () use ($datos) {
            $usuario = User::create([
                'nombre'    => $datos['nombre'],
                'apellidos' => $datos['apellidos'],
                'dni'       => strtoupper($datos['dni']),
                'email'     => $datos['email'] ?? null,
                'password'  => Hash::make($datos['password']),
                'rol'       => $datos['rol'],
                'telefono'  => $datos['telefono'] ?? null,
                'activo'    => $datos['activo'] ?? true,
            ]);

            $this->crearPerfil($usuario, $datos);

            return $usuario->load(['entrenador.club', 'gimnasta.club', 'gimnasta.categoria']);
        });
    }

    /**
     * Actualizar usuario y perfil.
     */
    public function actualizar(User $usuario, array $datos): User
    {
        return DB::transaction(function () use ($usuario, $datos) {
            $camposUser = array_filter([
                'nombre'    => $datos['nombre']    ?? null,
                'apellidos' => $datos['apellidos'] ?? null,
                'email'     => $datos['email']     ?? null,
                'telefono'  => $datos['telefono']  ?? null,
                'activo'    => $datos['activo']    ?? null,
            ], fn ($v) => ! is_null($v));

            if (isset($datos['dni'])) {
                $camposUser['dni'] = strtoupper($datos['dni']);
            }

            if (! empty($datos['password'])) {
                $camposUser['password'] = Hash::make($datos['password']);
            }

            $usuario->update($camposUser);
            $this->actualizarPerfil($usuario, $datos);

            return $usuario->fresh(['entrenador.club', 'gimnasta.club', 'gimnasta.categoria']);
        });
    }

    /**
     * Borrado lógico o físico.
     */
    public function eliminar(User $usuario, bool $hard = false): void
    {
        if ($hard) {
            $usuario->delete();
        } else {
            $usuario->update(['activo' => false]);
        }
    }

    // ── Helpers de perfil ────────────────────────────────────────────

    private function crearPerfil(User $usuario, array $datos): void
    {
        match ($usuario->rol) {
            'entrenadora' => $usuario->entrenador()->create([
                'club_id'           => $datos['club_id'],
                'titulacion'        => $datos['titulacion']        ?? null,
                'anios_experiencia' => $datos['anios_experiencia'] ?? 0,
                'horas_semanales'   => $datos['horas_semanales']   ?? 0,
                'estado'            => $datos['estado']            ?? 'activa',
            ]),
            'gimnasta' => $usuario->gimnasta()->create([
                'club_id'          => $datos['club_id'],
                'conjunto_id'      => $datos['conjunto_id']      ?? null,
                'categoria_id'     => $datos['categoria_id'],
                'numero_licencia'  => $datos['numero_licencia']  ?? null,
                'fecha_nacimiento' => $datos['fecha_nacimiento'] ?? null,
                'anios_en_club'    => $datos['anios_en_club']    ?? 0,
                'estado'           => $datos['estado']           ?? 'activa',
            ]),
            default => null,
        };
    }

    private function actualizarPerfil(User $usuario, array $datos): void
    {
        match ($usuario->rol) {
            'entrenadora' => $usuario->entrenador?->update(array_filter([
                'club_id'           => $datos['club_id']           ?? null,
                'titulacion'        => $datos['titulacion']        ?? null,
                'anios_experiencia' => $datos['anios_experiencia'] ?? null,
                'horas_semanales'   => $datos['horas_semanales']   ?? null,
                'estado'            => $datos['estado']            ?? null,
            ], fn ($v) => ! is_null($v))),
            'gimnasta' => $usuario->gimnasta?->update(array_filter([
                'club_id'          => $datos['club_id']          ?? null,
                'conjunto_id'      => $datos['conjunto_id']      ?? null,
                'categoria_id'     => $datos['categoria_id']     ?? null,
                'numero_licencia'  => $datos['numero_licencia']  ?? null,
                'fecha_nacimiento' => $datos['fecha_nacimiento'] ?? null,
                'anios_en_club'    => $datos['anios_en_club']    ?? null,
                'estado'           => $datos['estado']           ?? null,
            ], fn ($v) => ! is_null($v))),
            default => null,
        };
    }
}
