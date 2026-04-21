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

        // ── 6. Generación Masiva de Gimnastas (20 por clase) ──────
        $this->command->info('Generando 20 gimnastas por clase...');
        
        foreach ($conjObjs as $nombreConjunto => $conjunto) {
            for ($i = 1; $i <= 20; $i++) {
                // DNI único basado en ID de conjunto e índice
                $dniNum = 20000000 + ($conjunto->id * 100) + $i;
                $dni = $dniNum . "X";
                
                $user = User::firstOrCreate(
                    ['dni' => $dni],
                    [
                        'nombre'    => "Gimnasta " . $i,
                        'apellidos' => "de " . $nombreConjunto,
                        'email'     => "gimnasta" . $conjunto->id . "_" . $i . "@rytmia.test",
                        'password'  => Hash::make('Gimnasta1234'),
                        'rol'       => 'gimnasta',
                        'activo'    => true,
                        'telefono'  => '600000000' // Teléfono general del usuario
                    ]
                );

                if (! $user->gimnasta) {
                    $user->gimnasta()->create([
                        'club_id'          => $club->id,
                        'categoria_id'     => $conjunto->categoria_id,
                        'conjunto_id'      => $conjunto->id,
                        'numero_licencia'  => "LIC-" . str_pad($conjunto->id, 2, '0', STR_PAD_LEFT) . "-" . str_pad($i, 2, '0', STR_PAD_LEFT),
                        'fecha_nacimiento' => now()->subYears(rand(6, 16))->format('Y-m-d'),
                        'anios_en_club'    => rand(1, 4),
                        'estado'           => 'activa',
                        'telefono_contacto'=> '6' . str_pad(rand(0, 99999999), 8, '0', STR_PAD_LEFT)
                    ]);
                }
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
