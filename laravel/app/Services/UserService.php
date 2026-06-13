<?php

namespace App\Services;

use App\Models\User;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

class UserService
{
    /*Listado paginado con filtros.*/
    public function listar(array $filtros): LengthAwarePaginator
    {
        $query = User::query()
            ->with(['entrenador.club', 'gimnasta.club', 'gimnasta.categoria', 'gimnasta.conjunto'])
            ->orderBy('apellidos');

        if (isset($filtros['rol'])) {
            $query->where('rol', $filtros['rol']);
        }

        if (isset($filtros['activo'])) {
            $query->where('activo', filter_var($filtros['activo'], FILTER_VALIDATE_BOOLEAN));
        }
        /*Si viene una busqueda  busca lo que coincida*/
        if (!empty($filtros['search'])) {
            $s = '%' . $filtros['search'] . '%';
            $query->where(function ($q) use ($s) {
                $q->where('nombre', 'like', $s)
                    ->orWhere('apellidos', 'like', $s)
                    ->orWhere('dni', 'like', $s)
                    ->orWhere('email', 'like', $s);
            });
        }
        // Si se filtra por entrenadora, busca sus gimnastas
        if (!empty($filtros['entrenador_id'])) {
            $query->whereHas('gimnasta.conjunto.entrenadores', function ($q) use ($filtros) {
                $q->where('entrenadores.id', $filtros['entrenador_id']);
            });
        }

        return $query->paginate(15);
    }

    /*Crear usuario y su perfil de rol.*/
    public function crear(array $datos): User
    {
        return DB::transaction(function () use ($datos) {
            /*genero la contraseña temporal */
            $passwordTemporal = $this->generarPasswordTemporal($datos['nombre'], $datos['apellidos'], $datos['dni']);
            $username = empty($datos['username']) ? $this->generarUsername($datos['nombre'], $datos['apellidos']) : $datos['username'];

            // Crea el usuario en la tabla users
            $usuario = User::create([
                'nombre' => $datos['nombre'],
                'apellidos' => $datos['apellidos'],
                'username' => $username,
                'dni' => strtoupper($datos['dni']),
                'email' => $datos['email'] ?? null,
                
                /**hasheo manual de la contraseña temporal para que no se guarde en texto plano en la base de datos*/
                'password' => Hash::make($passwordTemporal),
                'password_temporal' => $passwordTemporal,
                'rol' => $datos['rol'],
                'telefono' => $datos['telefono'] ?? null,
                'activo' => $datos['activo'] ?? true,
            ]);

            //guarda la contraseña temporal 
            $usuario->password_temporal = $passwordTemporal;

            $this->crearPerfil($usuario, $datos);
            
            // Guarda el rol del usuario 
            return $usuario->load(['entrenador.club', 'gimnasta.club', 'gimnasta.categoria']);
        });
    }

    /*Actualiza los datos de un usuario.*/
    public function actualizar(User $usuario, array $datos): User
    {
        // Actualiza los datos del usuario y su perfil según el rol
        return DB::transaction(function () use ($usuario, $datos) {
            $camposUser = array_filter([
                'nombre' => $datos['nombre'] ?? null,
                'apellidos' => $datos['apellidos'] ?? null,
                'email' => $datos['email'] ?? null,
                'telefono' => $datos['telefono'] ?? null,
                'activo' => $datos['activo'] ?? null,
            ], fn($v) => !is_null($v));

            //convierto dni a mayusculas 
            if (isset($datos['dni'])) {
                $camposUser['dni'] = strtoupper($datos['dni']);
            }

            //se da una nueva contraseña temporal
            if (!empty($datos['password'])) {
                $camposUser['password'] = Hash::make($datos['password']);
                $camposUser['password_temporal'] = $datos['password'];
            }

            //se actualizan los datos 
            $usuario->update($camposUser);
            $this->actualizarPerfil($usuario, $datos);
        
            return $usuario->fresh(['entrenador.club', 'gimnasta.club', 'gimnasta.categoria']);
        });
    }

    /* eliminar usuario sea definitivo o desactivandolo*/
    public function eliminar(User $usuario, bool $hard = false): void
    {
        // si hard es true, se elimina el usuario de la base de datos, si es false, se desactiva el usuario
        if ($hard) {
            $usuario->delete();
        } else {
            $usuario->update(['activo' => false]);
        }
    }

    /*crear un nombre de usuario unico , si ya existe pues le añado un numero al final para que sea unico*/
    public function generarUsername(string $nombre, string $apellidos): string
    {
        // Obtengo el primer apellido para generar el username
        $firstApellido = explode(' ', trim($apellidos))[0];
        //creo el user name 
        $baseUsername = Str::slug($nombre . '.' . $firstApellido, '.');

        //compruebo que no exista ya
        $username = $baseUsername;
        $counter = 1;

        //si existe un usuario con el mismo nombre, le añado un numero al final para que sea unico
        while (User::where('username', $username)->exists()) {
            $username = $baseUsername . $counter;
            $counter++;
        }

        return $username;
    }

    /* creo la contraseña basandome en las dos primeras letras del nombre, las dos primeras letras de los apellidos y los tres últimos dígitos del dni. */
    
    public function generarPasswordTemporal(string $nombre, string $apellidos, string $dni): string
    {
        //quitar todos los acentos y cosas del nombre
        $cleanNombre = \Illuminate\Support\Str::ascii(str_replace(' ', '', trim($nombre)));
        //cojo 2 primeras letras del nombre
        $twoNombre = mb_strtolower(mb_substr($cleanNombre, 0, 2));

        //igual pero con el apellido
        $cleanApellidos = \Illuminate\Support\Str::ascii(str_replace(' ', '', trim($apellidos)));
        $twoApellidos = mb_strtolower(mb_substr($cleanApellidos, 0, 2));

        //cojo los tres ultimos digitos del dni sin coger la letra del final
        $onlyDigits = preg_replace('/[^0-9]/', '', $dni);
        $lastThreeDigits = substr($onlyDigits, -3);
        //si tuviera menos de tres digitos pongo 0 delante
        $lastThreeDigits = str_pad($lastThreeDigits, 3, '0', STR_PAD_LEFT);

        return $twoNombre . $twoApellidos . $lastThreeDigits;
    }

    // crear perfil dependiendo del rol

    private function crearPerfil(User $usuario, array $datos): void
    {
        
        match ($usuario->rol) {
            'entrenadora' => $usuario->entrenador()->create([
                'club_id' => $datos['club_id'],
                'titulacion' => $datos['titulacion'] ?? null,
                'anios_experiencia' => $datos['anios_experiencia'] ?? 0,
                'horas_semanales' => $datos['horas_semanales'] ?? 0,
                'estado' => $datos['estado'] ?? 'activa',
            ]),
            'gimnasta' => $usuario->gimnasta()->create([
                'club_id' => $datos['club_id'],
                'conjunto_id' => $datos['conjunto_id'] ?? null,
                'categoria_id' => $datos['categoria_id'],
                'numero_licencia' => $datos['numero_licencia'] ?? null,
                'fecha_nacimiento' => $datos['fecha_nacimiento'] ?? null,
                'anios_en_club' => $datos['anios_en_club'] ?? 0,
                'telefono_contacto' => $datos['telefono_contacto'] ?? null,
                'estado' => $datos['estado'] ?? 'activa',
            ]),
            default => null,
        };
    }

    //actualizo el perfil dependiendo del rol
    private function actualizarPerfil(User $usuario, array $datos): void
    {
        match ($usuario->rol) {
            'entrenadora' => $usuario->entrenador?->update(array_filter([
                'club_id' => $datos['club_id'] ?? null,
                'titulacion' => $datos['titulacion'] ?? null,
                'anios_experiencia' => $datos['anios_experiencia'] ?? null,
                'horas_semanales' => $datos['horas_semanales'] ?? null,
                'estado' => $datos['estado'] ?? null,
            ], fn($v) => !is_null($v))),
            'gimnasta' => $usuario->gimnasta?->update(array_filter([
                'club_id' => $datos['club_id'] ?? null,
                'conjunto_id' => $datos['conjunto_id'] ?? null,
                'categoria_id' => $datos['categoria_id'] ?? null,
                'numero_licencia' => $datos['numero_licencia'] ?? null,
                'fecha_nacimiento' => $datos['fecha_nacimiento'] ?? null,
                'anios_en_club' => $datos['anios_en_club'] ?? null,
                'telefono_contacto' => $datos['telefono_contacto'] ?? null,
                'estado' => $datos['estado'] ?? null,
            ], fn($v) => !is_null($v))),
            default => null,
        };
    }
}
