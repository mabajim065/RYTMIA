<?php

namespace Database\Seeders;

use App\Models\Categoria;
use App\Models\Club;
use App\Models\Conjunto;
use App\Models\Entrenador;
use App\Models\Gimnasta;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class GruposSeeder extends Seeder
{
    public function run(): void
    {
        // ── 1. Club ───────────────────────────────────────────────
        $club = Club::firstOrCreate(
            ['nombre' => 'Club Rítmica Estrella'],
            [
                'direccion' => 'Calle Mayor 10, Valencia',
                'telefono'  => '963000000',
                'email'     => 'info@estrella.test',
            ]
        );

        // ── 2. Categorías / Niveles ───────────────────────────────
        $categorias = [
            ['nombre' => 'Benjamín',  'edad_min' => 6,  'edad_max' => 8],
            ['nombre' => 'Alevín',    'edad_min' => 9,  'edad_max' => 11],
            ['nombre' => 'Infantil',  'edad_min' => 12, 'edad_max' => 14],
            ['nombre' => 'Cadete',    'edad_min' => 15, 'edad_max' => 17],
            ['nombre' => 'Juvenil',   'edad_min' => 18, 'edad_max' => 20],
            ['nombre' => 'Senior',    'edad_min' => 21, 'edad_max' => null],
        ];

        $catObjs = [];
        foreach ($categorias as $cat) {
            $catObjs[$cat['nombre']] = Categoria::firstOrCreate(
                ['nombre' => $cat['nombre']],
                ['edad_min' => $cat['edad_min'], 'edad_max' => $cat['edad_max']]
            );
        }

        // ── 3. Conjuntos / Grupos ─────────────────────────────────
        $conjuntos = [
            ['nombre' => 'Grupo Benjamín A',  'categoria' => 'Benjamín',  'horario' => 'Lunes y Miércoles 17:00-18:30'],
            ['nombre' => 'Grupo Alevín A',    'categoria' => 'Alevín',    'horario' => 'Martes y Jueves 17:00-19:00'],
            ['nombre' => 'Grupo Infantil A',  'categoria' => 'Infantil',  'horario' => 'Lunes, Miércoles y Viernes 17:30-19:30'],
            ['nombre' => 'Grupo Cadete A',    'categoria' => 'Cadete',    'horario' => 'Martes y Jueves 18:00-20:00'],
            ['nombre' => 'Conjunto Nacional', 'categoria' => 'Senior',    'horario' => 'Lunes a Viernes 16:00-20:00'],
        ];

        $conjObjs = [];
        foreach ($conjuntos as $c) {
            $conjObjs[$c['nombre']] = Conjunto::firstOrCreate(
                ['nombre' => $c['nombre'], 'club_id' => $club->id],
                [
                    'categoria_id' => $catObjs[$c['categoria']]->id,
                    'horario'      => $c['horario'],
                ]
            );
        }

        // ── 4. Entrenadoras de ejemplo ────────────────────────────
        $usersEntrenadoras = [
            ['dni' => '10000001A', 'nombre' => 'Laura',  'apellidos' => 'Gómez Ruiz',    'pw' => 'Laura1234',  'titulacion' => 'Nivel III RFEG', 'anios' => 8,  'horas' => 15, 'bio' => 'Especialista en elementos corporales y aparatos. 10 años de carrera competitiva.'],
            ['dni' => '10000002B', 'nombre' => 'Marta',  'apellidos' => 'Ruiz Sánchez',  'pw' => 'Marta1234',  'titulacion' => 'Nivel I RFEG',   'anios' => 3,  'horas' => 10, 'bio' => 'Joven entrenadora con gran pasión por la gimnasia base.'],
            ['dni' => '10000003C', 'nombre' => 'Carmen', 'apellidos' => 'López Navarro', 'pw' => 'Carmen1234', 'titulacion' => 'Nivel II RFEG',  'anios' => 6,  'horas' => 12, 'bio' => 'Experta en coreografía y trabajo en grupo.'],
        ];

        $entrenadoras = [];
        foreach ($usersEntrenadoras as $u) {
            $user = User::firstOrCreate(
                ['dni' => $u['dni']],
                [
                    'nombre'    => $u['nombre'],
                    'apellidos' => $u['apellidos'],
                    'email'     => strtolower($u['nombre']) . '@rytmia.test',
                    'password'  => Hash::make($u['pw']),
                    'rol'       => 'entrenadora',
                    'activo'    => true,
                ]
            );

            $entrenadora = Entrenador::firstOrCreate(
                ['user_id' => $user->id],
                [
                    'club_id'           => $club->id,
                    'titulacion'        => $u['titulacion'],
                    'biografia'         => $u['bio'],
                    'anios_experiencia' => $u['anios'],
                    'horas_semanales'   => $u['horas'],
                    'estado'            => 'activa',
                ]
            );

            $entrenadoras[$u['nombre']] = $entrenadora;
        }

        // ── 5. Asignar entrenadoras a conjuntos ───────────────────
        $conjObjs['Grupo Benjamín A']->entrenadores()->syncWithoutDetaching([$entrenadoras['Marta']->id]);
        $conjObjs['Grupo Alevín A']->entrenadores()->syncWithoutDetaching([$entrenadoras['Laura']->id]);
        $conjObjs['Grupo Infantil A']->entrenadores()->syncWithoutDetaching([$entrenadoras['Laura']->id, $entrenadoras['Carmen']->id]);
        $conjObjs['Grupo Cadete A']->entrenadores()->syncWithoutDetaching([$entrenadoras['Carmen']->id]);
        $conjObjs['Conjunto Nacional']->entrenadores()->syncWithoutDetaching([$entrenadoras['Laura']->id]);

        // ── 6. Gimnastas de ejemplo ───────────────────────────────
        $gimnastasData = [
            // Alevín
            ['dni' => '20000001A', 'nombre' => 'Sofia',    'apellidos' => 'Martín Pérez',   'cat' => 'Alevín',   'conj' => 'Grupo Alevín A',    'nacimiento' => '2013-03-10', 'licencia' => 'LIC-001'],
            ['dni' => '20000002B', 'nombre' => 'Lucía',    'apellidos' => 'Fernández Gil',  'cat' => 'Alevín',   'conj' => 'Grupo Alevín A',    'nacimiento' => '2014-06-22', 'licencia' => 'LIC-002'],
            ['dni' => '20000003C', 'nombre' => 'Valeria',  'apellidos' => 'Sanz Torres',    'cat' => 'Alevín',   'conj' => 'Grupo Alevín A',    'nacimiento' => '2013-11-05', 'licencia' => 'LIC-003'],
            // Infantil
            ['dni' => '20000004D', 'nombre' => 'Andrea',   'apellidos' => 'Moreno Ramos',   'cat' => 'Infantil', 'conj' => 'Grupo Infantil A',  'nacimiento' => '2010-09-14', 'licencia' => 'LIC-004'],
            ['dni' => '20000005E', 'nombre' => 'Paula',    'apellidos' => 'Jiménez Vega',   'cat' => 'Infantil', 'conj' => 'Grupo Infantil A',  'nacimiento' => '2011-02-28', 'licencia' => 'LIC-005'],
            // Benjamín
            ['dni' => '20000006F', 'nombre' => 'Carla',    'apellidos' => 'Romero Díaz',    'cat' => 'Benjamín', 'conj' => 'Grupo Benjamín A',  'nacimiento' => '2016-07-01', 'licencia' => 'LIC-006'],
            // Cadete sin conjunto aún
            ['dni' => '20000007G', 'nombre' => 'Elena',    'apellidos' => 'González Castro','cat' => 'Cadete',   'conj' => null,                'nacimiento' => '2008-04-18', 'licencia' => 'LIC-007'],
        ];

        foreach ($gimnastasData as $g) {
            $user = User::firstOrCreate(
                ['dni' => $g['dni']],
                [
                    'nombre'    => $g['nombre'],
                    'apellidos' => $g['apellidos'],
                    'email'     => strtolower($g['nombre']) . '@rytmia.test',
                    'password'  => Hash::make('Gimnasta1234'),
                    'rol'       => 'gimnasta',
                    'activo'    => true,
                ]
            );

            if (! $user->gimnasta) {
                $conjId = $g['conj'] ? $conjObjs[$g['conj']]->id : null;
                $user->gimnasta()->create([
                    'club_id'          => $club->id,
                    'categoria_id'     => $catObjs[$g['cat']]->id,
                    'conjunto_id'      => $conjId,
                    'numero_licencia'  => $g['licencia'],
                    'fecha_nacimiento' => $g['nacimiento'],
                    'anios_en_club'    => rand(1, 5),
                    'estado'           => 'activa',
                ]);
            }
        }

        // ── Admin por defecto ──────────────────────────────────────
        User::firstOrCreate(
            ['dni' => '00000001A'],
            [
                'nombre'    => 'Admin',
                'apellidos' => 'Principal',
                'email'     => 'admin@rytmia.test',
                'password'  => Hash::make('Admin1234'),
                'rol'       => 'administrador',
                'activo'    => true,
            ]
        );

        $this->command->info('✅ Datos de grupos cargados: 1 club, 6 categorías, 5 conjuntos, 3 entrenadoras, 7 gimnastas.');
    }
}
