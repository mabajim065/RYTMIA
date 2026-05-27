<?php

namespace App\Services;

use App\Models\User;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

class UserService
{
    /**
     * Listado paginado con filtros.
     */
    public function listar(array $filtros): LengthAwarePaginator
    {
        $query = User::query()
            ->with(['entrenador.club', 'gimnasta.club', 'gimnasta.categoria', 'gimnasta.conjunto', 'gimnasta.tutorLegal'])
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

        if (! empty($filtros['entrenador_id'])) {
            $query->whereHas('gimnasta.conjunto.entrenadores', function ($q) use ($filtros) {
                $q->where('entrenadores.id', $filtros['entrenador_id']);
            });
        }

        return $query->paginate(15);
    }

    /**
     * Crear usuario y su perfil de rol.
     */
    public function crear(array $datos): User
    {
        $usuario = DB::transaction(function () use ($datos) {
            $passwordTemporal = $this->generarPasswordTemporal($datos['nombre'], $datos['apellidos'], $datos['dni']);
            $username = empty($datos['username']) ? $this->generarUsername($datos['nombre'], $datos['apellidos']) : $datos['username'];

            $usuario = User::create([
                'nombre'    => $datos['nombre'],
                'apellidos' => $datos['apellidos'],
                'username'  => $username,
                'dni'       => strtoupper($datos['dni']),
                'email'     => $datos['email'] ?? null,
                'password'  => Hash::make($passwordTemporal),
                'password_temporal' => $passwordTemporal,
                'rol'       => $datos['rol'],
                'telefono'  => $datos['telefono'] ?? null,
                'activo'    => $datos['activo'] ?? true,
            ]);

            // Almacenar contraseña temporal en el objeto retornado en texto plano
            $usuario->password_temporal = $passwordTemporal;

            $this->crearPerfil($usuario, $datos);

            return $usuario;
        });

        // Enviar email después de crear el usuario con éxito
        if ($usuario->rol === 'gimnasta') {
            $gimnasta = $usuario->gimnasta;
            if ($gimnasta && $gimnasta->esMenorDeEdad() && $gimnasta->tutorLegal) {
                try {
                    \Illuminate\Support\Facades\Mail::to($gimnasta->tutorLegal->email)
                        ->send(new \App\Mail\WelcomeTutorMail($usuario, $gimnasta->tutorLegal));
                } catch (\Exception $e) {
                    // Log or handle mail sending failure gracefully
                }
            } else {
                if ($usuario->email) {
                    try {
                        \Illuminate\Support\Facades\Mail::to($usuario->email)
                            ->send(new \App\Mail\WelcomeGimnastaMayorMail($usuario));
                    } catch (\Exception $e) {
                        // Log or handle mail sending failure gracefully
                    }
                }
            }
        }

        return $usuario->load(['entrenador.club', 'gimnasta.club', 'gimnasta.categoria', 'gimnasta.tutorLegal']);
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
                $camposUser['password_temporal'] = $datos['password'];
            }

            $usuario->update($camposUser);
            $this->actualizarPerfil($usuario, $datos);

            return $usuario->fresh(['entrenador.club', 'gimnasta.club', 'gimnasta.categoria', 'gimnasta.tutorLegal']);
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

    /**
     * Generar un username único a partir del nombre y apellidos.
     */
    public function generarUsername(string $nombre, string $apellidos): string
    {
        $firstApellido = explode(' ', trim($apellidos))[0];
        $baseUsername = Str::slug($nombre . '.' . $firstApellido, '.');
        
        $username = $baseUsername;
        $counter = 1;

        while (User::where('username', $username)->exists()) {
            $username = $baseUsername . $counter;
            $counter++;
        }

        return $username;
    }

    /**
     * Generar una contraseña temporal basada en nombre, apellidos y DNI.
     */
    public function generarPasswordTemporal(string $nombre, string $apellidos, string $dni): string
    {
        // Primer nombre (sin acentos, minúsculas, primera palabra si es compuesto)
        $firstNombre = explode(' ', trim($nombre))[0];
        $cleanNombre = \Illuminate\Support\Str::ascii($firstNombre);
        $twoNombre = mb_strtolower(mb_substr($cleanNombre, 0, 2));

        // Primer apellido (sin acentos, minúsculas, primera palabra de apellidos)
        $firstApellido = explode(' ', trim($apellidos))[0];
        $cleanApellido = \Illuminate\Support\Str::ascii($firstApellido);
        $twoApellidos = mb_strtolower(mb_substr($cleanApellido, 0, 2));

        // Últimos 3 números del DNI
        $onlyDigits = preg_replace('/[^0-9]/', '', $dni);
        $lastThreeDigits = substr($onlyDigits, -3);
        $lastThreeDigits = str_pad($lastThreeDigits, 3, '0', STR_PAD_LEFT);

        return $twoNombre . $twoApellidos . $lastThreeDigits;
    }

    // ── Helpers de perfil ────────────────────────────────────────────

    private function crearPerfil(User $usuario, array $datos): void
    {
        if ($usuario->rol === 'entrenadora') {
            $usuario->entrenador()->create([
                'club_id'           => $datos['club_id'],
                'titulacion'        => $datos['titulacion']        ?? null,
                'anios_experiencia' => $datos['anios_experiencia'] ?? 0,
                'horas_semanales'   => $datos['horas_semanales']   ?? 0,
                'estado'            => $datos['estado']            ?? 'activa',
            ]);
        } elseif ($usuario->rol === 'gimnasta') {
            $gimnasta = $usuario->gimnasta()->create([
                'club_id'          => $datos['club_id'],
                'conjunto_id'      => $datos['conjunto_id']      ?? null,
                'categoria_id'     => $datos['categoria_id'],
                'numero_licencia'  => $datos['numero_licencia']  ?? null,
                'fecha_nacimiento' => $datos['fecha_nacimiento'] ?? null,
                'anios_en_club'    => $datos['anios_en_club']    ?? 0,
                'telefono_contacto'=> $datos['telefono_contacto']?? null,
                'estado'           => $datos['estado']           ?? 'activa',
            ]);

            if ($gimnasta->esMenorDeEdad() && !empty($datos['tutor_nombre'])) {
                $gimnasta->tutorLegal()->create([
                    'nombre' => $datos['tutor_nombre'],
                    'apellidos' => $datos['tutor_apellidos'],
                    'email' => $datos['tutor_email'],
                    'relacion' => $datos['tutor_relacion'],
                ]);
            }
        }
    }

    private function actualizarPerfil(User $usuario, array $datos): void
    {
        if ($usuario->rol === 'entrenadora' && $usuario->entrenador) {
            $usuario->entrenador->update(array_filter([
                'club_id'           => $datos['club_id']           ?? null,
                'titulacion'        => $datos['titulacion']        ?? null,
                'anios_experiencia' => $datos['anios_experiencia'] ?? null,
                'horas_semanales'   => $datos['horas_semanales']   ?? null,
                'estado'            => $datos['estado']            ?? null,
            ], fn ($v) => ! is_null($v)));
        } elseif ($usuario->rol === 'gimnasta' && $usuario->gimnasta) {
            $gimnasta = $usuario->gimnasta;
            $gimnasta->update(array_filter([
                'club_id'          => $datos['club_id']          ?? null,
                'conjunto_id'      => $datos['conjunto_id']      ?? null,
                'categoria_id'     => $datos['categoria_id']     ?? null,
                'numero_licencia'  => $datos['numero_licencia']  ?? null,
                'fecha_nacimiento' => $datos['fecha_nacimiento'] ?? null,
                'anios_en_club'    => $datos['anios_en_club']    ?? null,
                'telefono_contacto'=> $datos['telefono_contacto']?? null,
                'estado'           => $datos['estado']           ?? null,
            ], fn ($v) => ! is_null($v)));

            if ($gimnasta->esMenorDeEdad()) {
                if (!empty($datos['tutor_nombre'])) {
                    $gimnasta->tutorLegal()->updateOrCreate(
                        [],
                        [
                            'nombre' => $datos['tutor_nombre'],
                            'apellidos' => $datos['tutor_apellidos'],
                            'email' => $datos['tutor_email'],
                            'relacion' => $datos['tutor_relacion'],
                        ]
                    );
                }
            } else {
                $gimnasta->tutorLegal()?->delete();
            }
        }
    }
}
