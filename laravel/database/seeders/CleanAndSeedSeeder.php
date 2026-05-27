<?php

namespace Database\Seeders;

use App\Models\Categoria;
use App\Models\Club;
use App\Models\Conjunto;
use App\Models\Entrenador;
use App\Models\Gimnasta;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

class CleanAndSeedSeeder extends Seeder
{
    public function run(): void
    {
        // Desactivar restricciones de clave foránea
        DB::statement('SET FOREIGN_KEY_CHECKS=0;');

        // 1. Limpiar tablas (excepto administradores en users)
        $admins = User::where('rol', 'administrador')->get();

        DB::table('competicion_gimnasta')->truncate();
        DB::table('competicion_conjunto')->truncate();
        DB::table('competicion_entrenador')->truncate();
        DB::table('competicion_categoria')->truncate();
        DB::table('conjunto_entrenador')->truncate();
        DB::table('mensajes')->truncate();
        DB::table('gimnastas')->truncate();
        DB::table('conjuntos')->truncate();
        DB::table('entrenadores')->truncate();
        DB::table('categorias')->truncate();
        DB::table('competicions')->truncate();
        DB::table('users')->truncate();

        // Volver a guardar los administradores o crear uno por defecto si no hay
        if ($admins->isEmpty()) {
            $pwAdmin = (new \App\Services\UserService())->generarPasswordTemporal('Admin', 'Principal', '00000001A');
            User::create([
                'nombre' => 'Admin',
                'apellidos' => 'Principal',
                'username' => 'admin',
                'dni' => '00000001A',
                'email' => 'admin@rytmia.test',
                'password' => Hash::make($pwAdmin),
                'password_temporal' => $pwAdmin,
                'rol' => 'administrador',
                'activo' => true,
            ]);
        } else {
            foreach ($admins as $admin) {
                User::create([
                    'id' => $admin->id,
                    'nombre' => $admin->nombre,
                    'apellidos' => $admin->apellidos,
                    'username' => $admin->username ?? 'admin',
                    'dni' => $admin->dni,
                    'email' => $admin->email,
                    'password' => $admin->password,
                    'password_temporal' => $admin->password_temporal,
                    'rol' => $admin->rol,
                    'telefono' => $admin->telefono,
                    'activo' => $admin->activo,
                    'google_id' => $admin->google_id,
                    'google_token' => $admin->google_token,
                    'google_refresh_token' => $admin->google_refresh_token,
                ]);
            }
        }

        // Reactivar restricciones de clave foránea
        DB::statement('SET FOREIGN_KEY_CHECKS=1;');

        // 2. Crear Club por defecto
        $club = Club::create([
            'nombre' => 'Club Rítmica Estrella',
            'direccion' => 'Calle Mayor 10, Valencia',
            'telefono'  => '963000000',
            'email'     => 'info@estrella.test',
        ]);

        // 3. Crear Categorías (Niveles)
        $niveles = [
            'Diputación' => ['edad_min' => 6,  'edad_max' => null],
            'Promesa'    => ['edad_min' => 6,  'edad_max' => null],
            'Precopa'    => ['edad_min' => 8,  'edad_max' => null],
            'Copa'       => ['edad_min' => 8,  'edad_max' => null],
            'Base'       => ['edad_min' => 10, 'edad_max' => null],
            'Absoluto'   => ['edad_min' => 12, 'edad_max' => null],
        ];

        $catObjs = [];
        foreach ($niveles as $nombre => $rango) {
            $catObjs[$nombre] = Categoria::create([
                'nombre' => $nombre,
                'edad_min' => $rango['edad_min'],
                'edad_max' => $rango['edad_max']
            ]);
        }

        // 4. Crear Entrenadoras
        $userService = new \App\Services\UserService();

        $entrenadorasData = [
            ['nombre' => 'María',   'apellidos' => 'García López',     'dni' => '10000001A', 'user' => 'maria',   'titulacion' => 'Nivel 1'],
            ['nombre' => 'Andrea',  'apellidos' => 'Martínez Sánchez', 'dni' => '10000002B', 'user' => 'andrea',  'titulacion' => 'Nivel 2'],
            ['nombre' => 'Natalia', 'apellidos' => 'Fernández Gómez',  'dni' => '10000003C', 'user' => 'natalia', 'titulacion' => 'Nivel 3'],
        ];

        $entrenadoras = [];
        foreach ($entrenadorasData as $e) {
            $pw = $userService->generarPasswordTemporal($e['nombre'], $e['apellidos'], $e['dni']);

            $user = User::create([
                'nombre' => $e['nombre'],
                'apellidos' => $e['apellidos'],
                'username' => $e['user'],
                'dni' => $e['dni'],
                'email' => $e['user'] . '@rytmia.test',
                'password' => Hash::make($pw),
                'password_temporal' => $pw,
                'rol' => 'entrenadora',
                'activo' => true,
                'telefono' => '600111222'
            ]);

            $entrenadora = Entrenador::create([
                'user_id' => $user->id,
                'club_id' => $club->id,
                'titulacion' => $e['titulacion'],
                'biografia' => 'Entrenadora especialista del Club Rítmica Estrella.',
                'anios_experiencia' => rand(2, 12),
                'horas_semanales' => 20,
                'estado' => 'activa'
            ]);

            $entrenadoras[] = $entrenadora;
        }

        // Listas de nombres y apellidos españoles para generar gimnastas
        $nombresChicas = [
            'Sofía', 'Lucía', 'Martina', 'Valeria', 'Daniela', 'Sofía', 'Carla', 'Sara', 'Noa', 'Carmen',
            'Claudia', 'Ana', 'Elena', 'Irene', 'Laura', 'Alicia', 'Beatriz', 'Clara', 'Silvia', 'Rocío',
            'Nuria', 'Ángela', 'Cristina', 'Patricia', 'Sandra', 'Miriam', 'Eva', 'Alba', 'Paula', 'Mónica',
            'Lorena', 'Raquel', 'Yolanda', 'Sonia', 'Olga', 'Estela', 'Marina', 'Celia', 'Diana', 'Gloria',
            'Inés', 'Jimena', 'Lidia', 'Adriana', 'Nerea', 'Mireia', 'Ainhoa', 'Ariadna', 'Emma', 'Lara'
        ];

        $apellidosList = [
            'García', 'Rodríguez', 'González', 'Fernández', 'López', 'Martínez', 'Sánchez', 'Pérez', 'Gómez', 'Martín',
            'Jiménez', 'Ruiz', 'Hernández', 'Díaz', 'Moreno', 'Muñoz', 'Álvarez', 'Romero', 'Alonso', 'Gutiérrez',
            'Navarro', 'Torres', 'Domínguez', 'Gil', 'Vázquez', 'Serrano', 'Ramos', 'Blanco', 'Sanz', 'Castro',
            'Rubio', 'Medina', 'Castillo', 'Cortés', 'Garrido', 'Guerrero', 'Lozano', 'Cano', 'Prieto', 'Méndez'
        ];

        $dniCounter = 20000000;
        $licenciaCounter = 1000;

        // Función auxiliar para crear gimnastas en un conjunto
        $crearGimnastasParaConjunto = function (Conjunto $conjunto, $cantidad = 5) use (&$dniCounter, &$licenciaCounter, $nombresChicas, $apellidosList, $club, $userService) {
            for ($i = 0; $i < $cantidad; $i++) {
                $nombre = $nombresChicas[array_rand($nombresChicas)];
                $apellido1 = $apellidosList[array_rand($apellidosList)];
                $apellido2 = $apellidosList[array_rand($apellidosList)];
                $apellidos = "$apellido1 $apellido2";
                
                $dniCounter++;
                $dni = $dniCounter . 'X';
                
                $licenciaCounter++;
                $numeroLicencia = 'LIC-' . $licenciaCounter;

                $pw = $userService->generarPasswordTemporal($nombre, $apellidos, $dni);

                $user = User::create([
                    'nombre' => $nombre,
                    'apellidos' => $apellidos,
                    'dni' => $dni,
                    'email' => strtolower(Str::slug($nombre . '.' . $apellido1)) . $dniCounter . '@rytmia.test',
                    'password' => Hash::make($pw),
                    'password_temporal' => $pw,
                    'rol' => 'gimnasta',
                    'activo' => true,
                    'telefono' => '600' . rand(100000, 999999)
                ]);

                Gimnasta::create([
                    'user_id' => $user->id,
                    'club_id' => $club->id,
                    'categoria_id' => $conjunto->categoria_id,
                    'conjunto_id' => $conjunto->id,
                    'numero_licencia' => $numeroLicencia,
                    'fecha_nacimiento' => now()->subYears(rand(6, 15))->format('Y-m-d'),
                    'anios_en_club' => rand(1, 5),
                    'estado' => 'activa',
                    'telefono_contacto' => '600' . rand(100000, 999999)
                ]);
            }
        };

        // 5. Definición de conjuntos por nivel
        $conjuntosEstructura = [
            'Diputación' => [
                'Conjunto Prebenjamín Dipu',
                'Benjamin Dipu',
                'Infantil 1 Dipu',
                'Infantil 2 Dipu',
            ],
            'Promesa' => [
                'Prebenjamín Promesa',
                'Benjamin Promesa',
                'Alevín 1 Promesa',
                'Alevín 2 Promesa',
                'Alevín 3 Promesa',
                'Infantil 1 Promesa',
                'Infantil 2 Promesa',
                'Cadete 1 Promesa',
                'Cadete 2 Promesa',
            ],
            'Precopa' => [
                'Infantil 1 Precopa',
                'Infantil 2 Precopa',
                'Junior Precopa',
            ],
            'Copa' => [
                'Junior Copa',
                'Juvenil Copa',
            ],
            'Base' => [
                'Juvenil Base',
            ],
        ];

        // Crear Conjuntos y sus Gimnastas
        $conjuntoIndex = 0;
        foreach ($conjuntosEstructura as $nivel => $nombresConjuntos) {
            $catId = $catObjs[$nivel]->id;
            foreach ($nombresConjuntos as $nombreConj) {
                // Crear conjunto
                $conjunto = Conjunto::create([
                    'nombre' => $nombreConj,
                    'club_id' => $club->id,
                    'categoria_id' => $catId,
                    'horario' => 'Lunes, Miércoles y Viernes 17:00-19:00',
                ]);

                // Asignar entrenadoras en orden circular (round robin)
                $entrenadoraAsignada = $entrenadoras[$conjuntoIndex % count($entrenadoras)];
                $conjunto->entrenadores()->attach($entrenadoraAsignada->id);
                $conjuntoIndex++;

                // Crear 5 gimnastas para este conjunto
                $crearGimnastasParaConjunto($conjunto, 5);
            }
        }

        // 6. Gimnastas Absoluto (individuales: 1 chico y 1 chica)
        $nivelAbsolutoId = $catObjs['Absoluto']->id;

        // Chico
        $nombreChico = 'Hugo';
        $apellidoChico = 'García Fernández';
        $dniCounter++;
        $dniChico = $dniCounter . 'M';
        $licenciaCounter++;
        
        $pwChico = $userService->generarPasswordTemporal($nombreChico, $apellidoChico, $dniChico);

        $userChico = User::create([
            'nombre' => $nombreChico,
            'apellidos' => $apellidoChico,
            'dni' => $dniChico,
            'email' => 'hugo.garcia' . $dniCounter . '@rytmia.test',
            'password' => Hash::make($pwChico),
            'password_temporal' => $pwChico,
            'rol' => 'gimnasta',
            'activo' => true,
            'telefono' => '600987654'
        ]);

        Gimnasta::create([
            'user_id' => $userChico->id,
            'club_id' => $club->id,
            'categoria_id' => $nivelAbsolutoId,
            'conjunto_id' => null, // Individual
            'numero_licencia' => 'LIC-' . $licenciaCounter,
            'fecha_nacimiento' => now()->subYears(14)->format('Y-m-d'),
            'anios_en_club' => 4,
            'estado' => 'activa',
            'telefono_contacto' => '600123456'
        ]);

        // Chica
        $nombreChica = 'Lucía';
        $apellidoChica = 'Sánchez Navarro';
        $dniCounter++;
        $dniChica = $dniCounter . 'F';
        $licenciaCounter++;

        $pwChica = $userService->generarPasswordTemporal($nombreChica, $apellidoChica, $dniChica);

        $userChica = User::create([
            'nombre' => $nombreChica,
            'apellidos' => $apellidoChica,
            'dni' => $dniChica,
            'email' => 'lucia.sanchez' . $dniCounter . '@rytmia.test',
            'password' => Hash::make($pwChica),
            'password_temporal' => $pwChica,
            'rol' => 'gimnasta',
            'activo' => true,
            'telefono' => '600123987'
        ]);

        Gimnasta::create([
            'user_id' => $userChica->id,
            'club_id' => $club->id,
            'categoria_id' => $nivelAbsolutoId,
            'conjunto_id' => null, // Individual
            'numero_licencia' => 'LIC-' . $licenciaCounter,
            'fecha_nacimiento' => now()->subYears(13)->format('Y-m-d'),
            'anios_en_club' => 3,
            'estado' => 'activa',
            'telefono_contacto' => '600789456'
        ]);
    }
}
