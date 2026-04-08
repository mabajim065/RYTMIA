<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        // ── Administrador ──────────────────────────────────────────
        User::firstOrCreate(
            ['dni' => '00000001A'],
            [
                'nombre'    => 'Admin',
                'apellidos' => 'Principal',
                'email'     => 'admin@rytmia.test',
                'password'  => Hash::make('Admin1234'),
                'rol'       => 'administrador',
                'telefono'  => '600000001',
                'activo'    => true,
            ]
        );

        // ── Entrenadora de ejemplo ─────────────────────────────────
        $entrenadora = User::firstOrCreate(
            ['dni' => '00000002B'],
            [
                'nombre'    => 'Laura',
                'apellidos' => 'Gómez Ruiz',
                'email'     => 'laura@rytmia.test',
                'password'  => Hash::make('Laura1234'),
                'rol'       => 'entrenadora',
                'telefono'  => '600000002',
                'activo'    => true,
            ]
        );

        // Perfil entrenadora (solo si no existe ya)
        if (! $entrenadora->entrenador) {
            $entrenadora->entrenador()->create([
                'club_id'           => 1, // asegúrate de tener al menos 1 club en DB
                'titulacion'        => 'Nivel III RFEG',
                'biografia'         => 'Especialista en aparatos y preparación física. Ha competido a nivel nacional durante 10 años antes de dedicarse a la formación de nuevas gimnastas.',
                'anios_experiencia' => 8,
                'horas_semanales'   => 15,
                'estado'            => 'activa',
            ]);
        }

        // ── Gimnasta de ejemplo ────────────────────────────────────
        $gimnasta = User::firstOrCreate(
            ['dni' => '00000003C'],
            [
                'nombre'    => 'Sara',
                'apellidos' => 'Martínez López',
                'email'     => 'sara@rytmia.test',
                'password'  => Hash::make('Sara1234'),
                'rol'       => 'gimnasta',
                'telefono'  => '600000003',
                'activo'    => true,
            ]
        );

        // Perfil gimnasta (solo si no existe ya y hay categoría)
        if (! $gimnasta->gimnasta && \App\Models\Categoria::exists()) {
            $categoriaId = \App\Models\Categoria::first()->id;
            $gimnasta->gimnasta()->create([
                'club_id'          => 1,
                'categoria_id'     => $categoriaId,
                'numero_licencia'  => 'LIC-001',
                'fecha_nacimiento' => '2010-05-15',
                'anios_en_club'    => 3,
                'estado'           => 'activa',
            ]);
        }
    }
}
