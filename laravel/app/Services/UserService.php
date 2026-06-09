<?php

namespace App\Services;

use App\Mail\WelcomeBienvenidaMail;
use App\Mail\WelcomeGimnastaMayorMail;
use App\Mail\WelcomeTutorMail;
use App\Models\Entrenador;
use App\Models\Gimnasta;
use App\Models\TutorLegal;
use App\Models\User;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Str;
use Illuminate\Validation\ValidationException;

class UserService
{

    // LISTADO DE USUARIOS
    // Devuelve los usuarios paginados con filtros opcionales de rol, estado y búsqueda

    public function listar(array $filtros): LengthAwarePaginator
    {
        $query = User::query()->orderBy('apellidos');

        if (! empty($filtros['rol'])) {
            $query->where('rol', $filtros['rol']);
        }

        if (isset($filtros['activo']) && $filtros['activo'] !== '') {
            $query->where('activo', filter_var($filtros['activo'], FILTER_VALIDATE_BOOLEAN));
        }

        if (! empty($filtros['search'])) {
            $s = '%' . $filtros['search'] . '%';
            $query->where(function ($q) use ($s) {
                $q->where('nombre', 'like', $s)
                  ->orWhere('apellidos', 'like', $s)
                  ->orWhere('email', 'like', $s)
                  ->orWhere('username', 'like', $s);
            });
        }

        return $query->paginate(20);
    }


    // CREACIÓN DE USUARIO
    // Genera contraseña temporal, crea el usuario, su perfil (entrenador/gimnasta) y envía el email de bienvenida

    public function crear(array $datos): User
    {
        // Generamos la contraseña temporal basada en nombre, apellidos y DNI
        $passwordTemporal = $this->generarPasswordTemporal(
            $datos['nombre'],
            $datos['apellidos'],
            $datos['dni']
        );

        // Generamos el username automáticamente si no se proporcionó
        $username = $datos['username'] ?? $this->generarUsername($datos['nombre'], $datos['apellidos']);

        // Creamos el usuario en la base de datos
        $user = User::create([
            'nombre'            => $datos['nombre'],
            'apellidos'         => $datos['apellidos'],
            'username'          => $username,
            'dni'               => strtoupper($datos['dni']),
            'email'             => $datos['email'],
            'telefono'          => $datos['telefono'] ?? null,
            'password'          => Hash::make($passwordTemporal),
            'password_temporal' => $passwordTemporal,
            'rol'               => $datos['rol'],
            'activo'            => true,
        ]);

        // Adjuntamos la contraseña temporal al objeto para poder usarla en el email
        $user->password_temporal = $passwordTemporal;

        // Creamos el perfil específico según el rol
        if ($datos['rol'] === 'entrenadora') {
            Entrenador::create([
                'user_id'           => $user->id,
                'club_id'           => $datos['club_id'] ?? null,
                'titulacion'        => $datos['titulacion'] ?? null,
                'biografia'         => $datos['biografia'] ?? null,
                'anios_experiencia' => $datos['anios_experiencia'] ?? null,
                'horas_semanales'   => $datos['horas_semanales'] ?? null,
                'estado'            => 'activa',
            ]);
        }

        if ($datos['rol'] === 'gimnasta') {
            $esMenor = isset($datos['fecha_nacimiento'])
                && now()->diffInYears($datos['fecha_nacimiento']) < 18;

            Gimnasta::create([
                'user_id'           => $user->id,
                'club_id'           => $datos['club_id'] ?? null,
                'categoria_id'      => $datos['categoria_id'] ?? null,
                'conjunto_id'       => $datos['conjunto_id'] ?? null,
                'numero_licencia'   => $datos['numero_licencia'] ?? null,
                'fecha_nacimiento'  => $datos['fecha_nacimiento'] ?? null,
                'anios_en_club'     => $datos['anios_en_club'] ?? 0,
                'telefono_contacto' => $datos['telefono_contacto'] ?? null,
                'estado'            => 'activa',
            ]);

            // Si es menor de edad, creamos el tutor legal si se proporcionaron los datos
            if ($esMenor && ! empty($datos['tutor_nombre'])) {
                $tutor = TutorLegal::create([
                    'gimnasta_user_id' => $user->id,
                    'nombre'           => $datos['tutor_nombre'],
                    'apellidos'        => $datos['tutor_apellidos'] ?? '',
                    'dni'              => $datos['tutor_dni'] ?? null,
                    'email'            => $datos['tutor_email'] ?? null,
                    'telefono'         => $datos['tutor_telefono'] ?? null,
                    'relacion'         => $datos['tutor_relacion'] ?? 'padre/madre',
                ]);

                // Enviamos el email al tutor legal con las credenciales de la menor
                if ($tutor->email) {
                    Mail::to($tutor->email)->send(new WelcomeTutorMail($user, $tutor));
                }
            } elseif (! $esMenor && $user->email) {
                // Enviamos el email de bienvenida a la gimnasta mayor de edad
                Mail::to($user->email)->send(new WelcomeGimnastaMayorMail($user));
            }
        }

        // Enviamos el email de bienvenida a entrenadora o administrador
        if (in_array($datos['rol'], ['entrenadora', 'administrador']) && $user->email) {
            Mail::to($user->email)->send(new WelcomeBienvenidaMail($user));
        }

        return $user->load(['entrenador', 'gimnasta']);
    }


    // ACTUALIZACIÓN DE USUARIO
    // Modifica los datos del usuario y su perfil asociado

    public function actualizar(User $user, array $datos): User
    {
        // Actualizamos solo los campos que lleguen en la petición
        $camposUser = array_filter([
            'nombre'    => $datos['nombre']    ?? null,
            'apellidos' => $datos['apellidos'] ?? null,
            'email'     => $datos['email']     ?? null,
            'telefono'  => $datos['telefono']  ?? null,
            'dni'       => isset($datos['dni']) ? strtoupper($datos['dni']) : null,
        ], fn ($v) => ! is_null($v));

        // Si se proporciona una nueva contraseña, la hasheamos
        if (! empty($datos['password'])) {
            $camposUser['password'] = Hash::make($datos['password']);
            $camposUser['password_temporal'] = $datos['password'];
        }

        $user->update($camposUser);

        // Actualizamos el perfil de entrenadora si corresponde
        if ($user->rol === 'entrenadora' && $user->entrenador) {
            $user->entrenador->update(array_filter([
                'titulacion'        => $datos['titulacion']        ?? null,
                'biografia'         => $datos['biografia']         ?? null,
                'anios_experiencia' => $datos['anios_experiencia'] ?? null,
                'horas_semanales'   => $datos['horas_semanales']   ?? null,
                'club_id'           => $datos['club_id']           ?? null,
            ], fn ($v) => ! is_null($v)));
        }

        // Actualizamos el perfil de gimnasta si corresponde
        if ($user->rol === 'gimnasta' && $user->gimnasta) {
            $user->gimnasta->update(array_filter([
                'categoria_id'      => $datos['categoria_id']      ?? null,
                'conjunto_id'       => $datos['conjunto_id']       ?? null,
                'numero_licencia'   => $datos['numero_licencia']   ?? null,
                'fecha_nacimiento'  => $datos['fecha_nacimiento']  ?? null,
                'anios_en_club'     => $datos['anios_en_club']     ?? null,
                'telefono_contacto' => $datos['telefono_contacto'] ?? null,
                'club_id'           => $datos['club_id']           ?? null,
            ], fn ($v) => ! is_null($v)));
        }

        return $user->fresh(['entrenador.club', 'gimnasta.categoria', 'gimnasta.conjunto']);
    }


    // ELIMINACIÓN DE USUARIO
    // Borrado lógico (desactiva) o físico (elimina de la BD) según el parámetro $hard

    public function eliminar(User $user, bool $hard = false): void
    {
        if ($hard) {
            // Borrado físico: elimina el registro de la base de datos
            $user->delete();
        } else {
            // Borrado lógico: simplemente desactiva la cuenta
            $user->update(['activo' => false]);
        }
    }


    // GENERACIÓN DE CONTRASEÑA TEMPORAL
    // Crea una contraseña legible basada en las iniciales del nombre y apellidos + dígitos del DNI

    public function generarPasswordTemporal(string $nombre, string $apellidos, string $dni): string
    {
        // Tomamos la primera letra del nombre y del primer apellido (en minúsculas)
        $iniciales = strtolower(
            substr(trim($nombre), 0, 1) .
            substr(trim(explode(' ', trim($apellidos))[0]), 0, 1)
        );

        // Extraemos los primeros 3 dígitos numéricos del DNI
        preg_match_all('/\d/', $dni, $matches);
        $numeros = implode('', array_slice($matches[0], 0, 3));

        // Combinamos iniciales + números para formar la contraseña (ej: "ap001")
        return $iniciales . str_pad($numeros, 3, '0', STR_PAD_LEFT);
    }


    // GENERACIÓN DE USERNAME
    // Crea un nombre de usuario único basado en nombre.primerApellido

    private function generarUsername(string $nombre, string $apellidos): string
    {
        $primerApellido = explode(' ', trim($apellidos))[0];
        $base = strtolower(
            Str::slug($nombre, '') . '.' . Str::slug($primerApellido, '')
        );

        // Si ya existe ese username, añadimos un número incremental
        $username = $base;
        $i = 1;
        while (User::where('username', $username)->exists()) {
            $username = $base . $i;
            $i++;
        }

        return $username;
    }
}