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
            ['nombre' => 'Escuela',    'edad_min' => 4,  'edad_max' => null],
            ['nombre' => 'Diputación', 'edad_min' => 6,  'edad_max' => null],
            ['nombre' => 'Promesa',    'edad_min' => 6,  'edad_max' => null],
            ['nombre' => 'Precopa',    'edad_min' => 8,  'edad_max' => null],
            ['nombre' => 'Copa',       'edad_min' => 8,  'edad_max' => null],
            ['nombre' => 'Base',       'edad_min' => 10, 'edad_max' => null],
            ['nombre' => 'Absoluto',   'edad_min' => 12, 'edad_max' => null],
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
            ['nombre' => 'Etapa Infantil',       'categoria' => 'Escuela',    'horario' => 'Lunes y Miércoles 17:00-18:00'],
            ['nombre' => 'Escuela',              'categoria' => 'Escuela',    'horario' => 'Martes y Jueves 17:00-18:30'],
            ['nombre' => 'Primaria 1',           'categoria' => 'Escuela',    'horario' => 'Miércoles y Viernes 17:30-19:00'],
            ['nombre' => 'Primaria 2',           'categoria' => 'Escuela',    'horario' => 'Lunes y Miércoles 18:00-19:30'],
            ['nombre' => 'Primaria 3',           'categoria' => 'Escuela',    'horario' => 'Martes y Jueves 18:30-20:00'],
            ['nombre' => 'Diputación',           'categoria' => 'Diputación', 'horario' => 'Viernes 17:00-20:00'],
            ['nombre' => 'Promesas',             'categoria' => 'Promesa',    'horario' => 'Lunes, Miércoles y Viernes 18:00-20:00'],
            ['nombre' => 'Individuales Precopa', 'categoria' => 'Precopa',    'horario' => 'Lunes a Jueves 16:30-19:00'],
            ['nombre' => 'Individuales Copa',    'categoria' => 'Copa',       'horario' => 'Lunes a Viernes 17:00-20:00'],
            ['nombre' => 'Conjuntos Precopa',    'categoria' => 'Precopa',    'horario' => 'Lunes, Miércoles y Viernes 16:00-19:00'],
            ['nombre' => 'Conjuntos Copa',       'categoria' => 'Copa',       'horario' => 'Martes, Jueves y Sábados 16:00-20:00'],
            ['nombre' => 'Grupo Base',           'categoria' => 'Base',       'horario' => 'Lunes a Viernes 16:00-20:00'],
            ['nombre' => 'Grupo Absoluto',       'categoria' => 'Absoluto',   'horario' => 'Lunes a Sábado 15:30-20:30'],
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
            ['dni' => '10000001A', 'nombre' => 'Laura',   'apellidos' => 'Gómez Ruiz',    'pw' => 'Laura1234',  'titulacion' => 'Nivel 1',  'anios' => 3,  'horas' => 10, 'bio' => 'Especialista en iniciación deportiva y escuela.'],
            ['dni' => '10000002B', 'nombre' => 'Marta',   'apellidos' => 'Ruiz Sánchez',  'pw' => 'Marta1234',  'titulacion' => 'Nivel 2',  'anios' => 6,  'horas' => 20, 'bio' => 'Especialista técnica en Precopa y Copa.'],
            ['dni' => '10000003C', 'nombre' => 'Carmen',  'apellidos' => 'López Navarro', 'pw' => 'Carmen1234', 'titulacion' => 'Nivel 3',  'anios' => 10, 'horas' => 30, 'bio' => 'Directora técnica, responsable superior de Base y Absoluto.'],
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
        $conjObjs['Etapa Infantil']->entrenadores()->syncWithoutDetaching([$entrenadoras['Laura']->id]);
        $conjObjs['Escuela']->entrenadores()->syncWithoutDetaching([$entrenadoras['Laura']->id]);
        $conjObjs['Primaria 1']->entrenadores()->syncWithoutDetaching([$entrenadoras['Laura']->id]);
        $conjObjs['Primaria 2']->entrenadores()->syncWithoutDetaching([$entrenadoras['Laura']->id]);
        $conjObjs['Primaria 3']->entrenadores()->syncWithoutDetaching([$entrenadoras['Laura']->id]);
        
        $conjObjs['Diputación']->entrenadores()->syncWithoutDetaching([$entrenadoras['Marta']->id]);
        $conjObjs['Promesas']->entrenadores()->syncWithoutDetaching([$entrenadoras['Marta']->id]);
        $conjObjs['Individuales Precopa']->entrenadores()->syncWithoutDetaching([$entrenadoras['Marta']->id]);
        $conjObjs['Individuales Copa']->entrenadores()->syncWithoutDetaching([$entrenadoras['Marta']->id]);
        $conjObjs['Conjuntos Precopa']->entrenadores()->syncWithoutDetaching([$entrenadoras['Marta']->id]);
        $conjObjs['Conjuntos Copa']->entrenadores()->syncWithoutDetaching([$entrenadoras['Marta']->id]);
        
        $conjObjs['Grupo Base']->entrenadores()->syncWithoutDetaching([$entrenadoras['Carmen']->id]);
        $conjObjs['Grupo Absoluto']->entrenadores()->syncWithoutDetaching([$entrenadoras['Carmen']->id]);

        // ── 6. Gimnastas de ejemplo ───────────────────────────────
        $gimnastasData = [
            ['dni' => '20000001A', 'nombre' => 'Sofia',    'apellidos' => 'Martín',   'cat' => 'Escuela',   'conj' => 'Etapa Infantil', 'nacimiento' => '2018-03-10', 'licencia' => 'LIC-001'],
            ['dni' => '20000002B', 'nombre' => 'Lucía',    'apellidos' => 'Fernández','cat' => 'Escuela',   'conj' => 'Escuela',        'nacimiento' => '2016-06-22', 'licencia' => 'LIC-002'],
            ['dni' => '20000003C', 'nombre' => 'Valeria',  'apellidos' => 'Sanz',     'cat' => 'Escuela',   'conj' => 'Primaria 1',     'nacimiento' => '2015-11-05', 'licencia' => 'LIC-003'],
            ['dni' => '20000004D', 'nombre' => 'Andrea',   'apellidos' => 'Moreno',   'cat' => 'Diputación','conj' => 'Diputación',     'nacimiento' => '2014-09-14', 'licencia' => 'LIC-004'],
            ['dni' => '20000005E', 'nombre' => 'Paula',    'apellidos' => 'Jiménez',  'cat' => 'Promesa',   'conj' => 'Promesas',       'nacimiento' => '2013-02-28', 'licencia' => 'LIC-005'],
            ['dni' => '20000006F', 'nombre' => 'Carla',    'apellidos' => 'Romero',   'cat' => 'Precopa',   'conj' => 'Individuales Precopa','nacimiento' => '2012-07-01', 'licencia' => 'LIC-006'],
            ['dni' => '20000007G', 'nombre' => 'Elena',    'apellidos' => 'González', 'cat' => 'Copa',      'conj' => 'Conjuntos Copa', 'nacimiento' => '2010-04-18', 'licencia' => 'LIC-007'],
            ['dni' => '20000008H', 'nombre' => 'Martina',  'apellidos' => 'Pérez',    'cat' => 'Base',      'conj' => 'Grupo Base',     'nacimiento' => '2009-08-10', 'licencia' => 'LIC-008'],
            ['dni' => '20000009I', 'nombre' => 'Daniela',  'apellidos' => 'López',    'cat' => 'Absoluto',  'conj' => 'Grupo Absoluto', 'nacimiento' => '2007-01-20', 'licencia' => 'LIC-009'],
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

        $this->command->info('✅ Datos de categorías, niveles y grupos reales cargados con éxito.');
    }
}
